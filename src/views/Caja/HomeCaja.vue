<template>
	<div>
		<p class="fs-2"><i class="bi bi-key"></i> Caja</p>

		<div class="row justify-content-center" v-if="hayCaja">
			<div class="col-md-6">
				<div class="card">
					<div class="card-body text-center">
						<h5 class="card-title">Control de caja</h5>
						<p class="card-text">Monto actual en caja</p>
						<p class="fs-2 text-center mb-0">S/ {{ parseFloat(sumaTotal).toFixed(2) }}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="row" v-if="hayCaja && cabecera.enUso==1">
			<div class="col-12 d-grid gap-2 d-md-flex justify-content-between">
				<button class="btn btn-outline-danger  mt-2 " data-bs-toggle="modal" data-bs-target="#modalEgreso" v-if="verBoton" @click="caja.monto=0"><i class="bi bi-cart-dash"></i> Registrar egreso</button>

				<button class="btn btn-outline-success mt-2 " v-if="verBoton" @click="cargarDatos()"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
				<button class="btn btn-outline-primary mt-2 " data-bs-toggle="modal" data-bs-target="#modalCaja" v-if="verBoton"><i class="bi bi-piggy-bank"></i> Cerrar caja</button>
			</div>
		</div>
		<div class="row" v-if="cabecera.enUso==0">
			<div class="col-12">
				<button class="btn btn-outline-secondary" @click="irCaja2()"><i class="bi bi-piggy-bank"></i> Ver caja actual</button>
			</div>
		</div>
		<div class="row" v-if="!verBoton">
			<div class="col-12">
				<button class="btn btn-outline-secondary" @click="irCaja2()"><i class="bi bi-piggy-bank"></i> Abrir nueva caja</button>
			</div>
		</div>

		<div class="row justify-content-center" v-if="!hayCaja">
			<div class="col-md-6">
				<div class="card">
					<div class="card-body text-center">
						<h5 class="card-title">Apertura de caja</h5>
						<p class="card-text">Puede ingresar un valor</p>
						<input type="text" class="form-control fs-3 text-center" v-model="caja.monto">
						<div class="d-grid mt-3">
							<button class="btn btn-outline-primary btn-lg" @click="abrirCaja()"><i class="bi bi-piggy-bank"></i> Abrir caja</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		<table class="table table-hover container-fluid mt-3" v-if="hayCaja">
			<thead>
				<tr>
					<th>N°</th>
					<th>Hora</th>
					<th>Detalle</th>
					<th>Tipo</th>
					<th>Monto</th>
					<th>Moneda</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>{{ horaLatam(cabecera.apertura) }}</td>
					<td>Apertura de caja</td>
					<td></td>
					<td class="text-primary">+{{parseFloat(cabecera.inicial ?? 0 ).toFixed(2)}}</td>
					<td></td>
					<td></td>
				</tr>
				<tr v-for="(detalle, index) in detalles">
					<td>{{ index+2 }}</td>
					<td>{{ horaLatam(detalle.fecha) }}</td>
					<td class="text-capitalize">{{ detalle.descripcion }}</td>
					<td>
						<span v-if="detalle.tipo==1">Ingreso</span>
						<span v-else>Salida</span>
					</td>
					<td>
						<span v-if="detalle.tipo==1" class="text-primary">+{{parseFloat(detalle.monto).toFixed(2)}}</span>
						<span v-else class="text-danger">-{{parseFloat(detalle.monto).toFixed(2)}}</span>
					</td>
					<td>{{ queMoneda(detalle.idMoneda-1) }}</td>
					<td>
						<button class="btn btn-outline-primary" title="Ticket" @click="irAticket(detalle.id)"><i class="bi bi-printer"></i></button>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" class="text-end pe-3">Total</td>
					<td class="fw-bold">S/ {{ parseFloat(sumaTotal).toFixed(2) }}</td>
				</tr>
				<tr v-if="cabecera.enUso==0">
					<td colspan="4" class="text-end pe-3">Se cerró con: </td>
					<td class="fw-bold">S/ {{ parseFloat(cabecera.final).toFixed(2) }}</td>
				</tr>

			</tfoot>
		</table>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalCaja" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Monto de caja</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label for="">Ingrese el monto de cierre (S/)</label>
					<input type="number" min="0" class="form-control text-center" v-model="caja.monto" >
					<div class="d-grid">
						<button class="btn btn-outline-primary mt-3" @click="cerrar()" data-bs-dismiss="modal"><i class="bi bi-piggy-bank"></i> Guardar cierre</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalEgreso" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Salida de dinero</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label for="">Ingrese el monto de salida (S/)</label>
					<input type="number" min="0" class="form-control text-center" v-model="caja.monto" >
					<label class="mt-3">Motivo del egreso</label>
					<input type="text" min="0" class="form-control text-center" v-model="caja.detalle" autocomplete="off">
					<div class="d-grid">
						<button class="btn btn-outline-danger mt-3" @click="crearEgreso()" data-bs-dismiss="modal"><i class="bi bi-piggy-bank"></i> Registrar egreso</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import moment from 'moment'
export default {
	data(){return {
		caja:{monto:0, detalle:'', id:-1}, cabecera:[], detalles:[], verBoton:true, hayCaja:false,
		monedas:['Efectivo', 'Tarjeta', 'Yape', 'Plin']
	}},
	mounted(){
		console.log('cambio');
		if(this.$route.name == 'detalleCaja'){
			this.caja.id = this.$route.params.idCaja
		}else this.caja.id=-1
		this.cargarDatos()
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalleCaja'); //de la caja activa
			datos.append('idCaja', this.caja.id); //de la caja activa
			let serv = await fetch(this.servidor+'Caja.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			if(temp.caja=='noCaja'){
				this.verBoton=false
				this.hayCaja=false
			}else{
				this.verBoton=true
				this.hayCaja=true
			}
			this.cabecera = temp.cabecera
			this.detalles = temp.detalles
		},
		async cerrar(){
			let datos = new FormData()
			datos.append('pedir', 'cerrar'); //de la caja activa
			datos.append('monto', this.caja.monto);
			datos.append('cierre', moment().format('YYYY-MM-DD HH:mm'));
			let serv = await fetch(this.servidor+'Caja.php',{
				method:'POST', body: datos
			})
			const resp =await serv.text()
			if(resp=='ok'){
				this.verBoton = false
				alertify.message('Caja cerrada')
			}else{
				this.verBoton=true
				alertify.error('Hubo un error al cerrar')
			}
		},
		async abrirCaja(){
			let datos = new FormData()
			datos.append('pedir', 'abrir'); //de la caja activa
			datos.append('idUsuario', localStorage.getItem('idUsuario'));
			datos.append('monto', this.caja.monto);
			datos.append('apertura', moment().format('YYYY-MM-DD HH:mm'));
			let serv = await fetch(this.servidor+'Caja.php',{
				method:'POST', body: datos
			})
			const resp =await serv.json()
			if( resp.caja ==  'siCaja'){
				this.cabecera = resp.cabecera
				this.detalles = resp.detalles
				this.verBoton=true
				this.hayCaja=true
			}else{
				this.verBoton=false
				this.hayCaja=false
			}
		},
		horaLatam(hora){
			if(hora) return moment(hora, 'YYYY-MM-DD HH:mm').format('h:mm a')
		},
		async crearEgreso(){
			if(this.caja.monto<0) alertify.error('No se puede ingresar montos en 0')
			else{
				let datos = new FormData()
				datos.append('pedir', 'salida'); //de la caja activa
				datos.append('idUsuario', localStorage.getItem('idUsuario'));
				datos.append('monto', this.caja.monto);
				datos.append('detalle', this.caja.detalle);
				let serv = await fetch(this.servidor+'Caja.php',{
					method:'POST', body: datos
				})
				const resp = await serv.json()
				if(resp.mensaje == 'ok'){
					alertify.message('Registro correcto')
					this.cargarDatos()
				}else alertify.error('Hubo un error')
			}
		},
		queMoneda(idMoneda){
			return this.monedas[idMoneda]
		},
		irAticket(id){
			window.open("https://intranet.tambowasi.com/api/printComprobante.php?idVenta="+id, "_blank");
		},
		irCaja2(){
			this.caja.id
			this.cargarDatos()
			this.$router.push('/caja')
		}
	},
	computed: {
		sumaTotal(){
			let suma = 0;
			suma += parseFloat(this.cabecera.inicial??0)
			this.detalles.forEach(det => {
				if(det.tipo==1) suma += parseFloat(det.monto)
				else suma -= parseFloat(det.monto)
			});
			suma = parseFloat(suma).toFixed(2)
			this.caja.monto = suma
			return suma
		},
	},
}
</script>