<template>
	<h1 v-if="$route.name == 'detalleReserva'">Detalle de la Reservación</h1>
	<h1 v-else>Reservación inmediata</h1>
	<div class="row mb-2">
		<div class="col">
			<router-link to="/habitaciones" class="btn btn-outline-primary border-0"><i class="bi bi-arrow-bar-left"></i> Ver habitaciones</router-link>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-lg-2 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class=""><img src="@/assets/bed.png" style="width: 64px; margin: 0 auto;" ></div>
					<p class="mb-0"><small>Habitación {{ habitacion.tipoCuarto }}</small></p>
					<p class="fs-1 mb-0">{{habitacion.numero}}</p>
					<p class="mb-0"><small>{{habitacion.detalle}}</small></p>
				</div>
			</div>
			
		</div>
		
			<div class="col-12 col-lg-10 col-lg-8">
				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title ">Detalle de la reserva</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">Fecha de entrada</label>
								<p class="mb-0">{{fechaLatam(habitacion.fechaInicio)}}</p>
							</div>
							<div class="col">
								<label for="">Hora de entrada</label>
								<p class="mb-0">{{horaLatam(habitacion.fechaInicio)}}</p>
							</div>
							<div class="col">
								<label for="">Fecha de salida</label>
								<p class="mb-0">{{fechaLatam(habitacion.fechaFin)}}</p>
							</div>
							<div class="col">
								<label for="">Hora de salida</label>
								<p class="mb-0">{{horaLatam(habitacion.fechaFin)}}</p>
							</div>
							
						</div>
					</div>
				</div>

				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title">Detalle del cliente</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">DNI</label>
								<p class="mb-0">{{cliente.dni}}</p>
							</div>
							<div class="col">
								<label for="">Apellidos y Nombres</label>
								<p class="mb-0">{{cliente.apellidos}} {{ cliente.nombres }}</p>
							</div>
							<div class="col">
								<label for="">Fecha de nacimiento</label>
								<p class="mb-0">{{fechaLatam(cliente.fechaNacimiento) ?? '-'}}</p>
							</div>
							<div class="col">
								<label for="">Procedencia</label>
								<p class="mb-0" v-if="cliente.idNacionalidad==1">Peruano - {{ cliente.departamento }}</p>
								<p class="mb-0" v-else>Extranjero</p>
							</div>
							<div class="col">
								<label for="">Celular</label>
								<p class="mb-0">{{cliente.celular ?? '-'}}</p>
							</div>
						</div>
					</div>
				</div>
				
				<CestaProductos v-if="$route.name == 'detalleHabitacion'" :productos="cesta" @buscarProducto="buscarProducto" @actualizarPrecioProductos="actualizarPrecioProductos"></CestaProductos>

				<div class="card my-2">
					<div class="card-body">
						<h5 class="card-title ">Cobro del servicio</h5>
						<div class="row row-cols-12 row-cols-lg-4">
							<div class="col">
								<label for="">Precio de la habitación</label>
								<p class="mb-0">S/ {{parseFloat(habitacion.precio).toFixed(2)}}</p>
							</div>
							<div class="col">
								<label for="">Productos</label>
								<p class="mb-0">S/ {{parseFloat(habitacion.productos).toFixed(2)}}</p>
							</div>
							<div class="col">
								<label for="">Adelanto</label>
								<p class="mb-0">S/ {{parseFloat(habitacion.adelanto).toFixed(2)}}</p>
							</div>
							<div class="col">
								<label for="">A cuenta:</label>
								<p class="mb-0 fs-3">S/ {{parseFloat(habitacion.pagar + habitacion.productos).toFixed(2)}}</p>
							</div>
						</div>
						<h5 class="card-title ">Métodos de pago</h5>
						<div class="row row-cols-12 row-cols-lg-4">
							<div class="col">
								<label class="mt-0" for="">Efectivo</label>
								<input type="number" class="form-control" min="0" @focus="handleFocus" v-model="pago.efectivo">
							</div>
							<div class="col">
								<label class="mt-0" for="">Tarjeta</label>
								<input type="number" class="form-control" min="0" @focus="handleFocus" v-model="pago.tarjeta">
							</div>
							<div class="col">
								<label class="mt-0" for="">Yape</label>
								<input type="number" class="form-control" min="0" @focus="handleFocus" v-model="pago.yape">
							</div>
							<div class="col">
								<label class="mt-0" for="">Plin</label>
								<input type="number" class="form-control" min="0" @focus="handleFocus" v-model="pago.plin">
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row my-3">
				<div class="col-6 d-grid mx-auto">
					<button v-if="$route.name == 'detalleHabitacion'" class="btn btn-outline-primary btn-lg" @click="cobrar()"><i class="bi bi-asterisk"></i> Cobrar servicio</button>
					<button v-else class="btn btn-outline-success btn-lg" @click="habilitar()"><i class="bi bi-check2-square"></i> Habilitar servicio</button>
				</div>
			</div>
	</div>
	<ModalBuscarProducto :idReservacion="reserva.id" :productos="productos" @devolverProducto="recibirProducto"></ModalBuscarProducto>
</template>
<script>
import ModalBuscarProducto from '@/views/Productos/ModalBuscarProducto.vue'
import CestaProductos from '@/views/Productos/CestaProductos.vue'
import moment from 'moment'
export default{
	data(){ return {
		reserva:{id:-1, idRegistro:-1},
		habitacion:[], departamentos:[], activarDepartamento:true, texto:'', productos:[], cesta:[],
		cliente:{
			id:1, dni: '00000000', nombres: 'Cliente simple', apellidos:'', idNacionalidad:1, procedencia:1, direccion:'', celular:'', correo:'', observaciones:''
		},
		pago:{efectivo:0, tarjeta:0, yape:0, plin:0}
	}},
	components:{ModalBuscarProducto, CestaProductos},
	mounted(){
		this.cargarDatos()
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			if(this.$route.name == 'detalleReserva'){
				datos.append('pedir', 'detalleReserva');
				datos.append('idReserva', this.$route.params.idReserva);
			}
			else{
				datos.append('pedir', 'detalleOcupado');
				datos.append('idHabitacion', this.$route.params.idHabitacion);
			}
			let serv = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			}) 
			
			const temp = await serv.json()
			this.habitacion = temp.habitacion
			this.reserva.id = this.habitacion.id
			this.cliente = temp.cliente
			this.cesta = temp.productos

			datos.set('pedir', 'listar')
			
			let servDepartamentos = await fetch(this.servidor+'Departamentos.php',{
				method:'POST', body: datos
			})
			const tempDepartamentos = await servDepartamentos.json()
			this.departamentos = tempDepartamentos.departamentos
		},
		fechaLatam(fecha){
			if(fecha) return moment(fecha, 'YYYY-MM-DD HH:mm').format('DD/MM/YYYY') 
		},
		horaLatam(hora){
			if(hora) return moment(hora, 'YYYY-MM-DD HH:mm').format('h:mm a')
		},
		async buscarProducto(texto){
			let datos = new FormData()
			datos.append('pedir', 'buscarProducto');
			datos.append('texto', texto);
			let serv = await fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.productos = temp.productos
			const modalProductos = new bootstrap.Modal(document.getElementById('modalProductos'))
			modalProductos.show();
		},
		recibirProducto(index){
			this.cargarDatos();
		},
		actualizarPrecioProductos(total){
			this.habitacion.productos = total
		},
		cobrar(){

			this.pago.efectivo = this.pago.efectivo<0? 0: this.pago.efectivo
			this.pago.tarjeta = this.pago.tarjeta<0? 0: this.pago.tarjeta
			this.pago.yape = this.pago.yape<0? 0: this.pago.yape
			this.pago.plin = this.pago.plin<0? 0: this.pago.plin

			if(this.debeCliente){
				alertify.error('El monto ingresado no cubre la deuda.')
			}else{
				let that = this
				alertify.confirm('Cobrar servicio','¿Desea finalizar el alquiler de habitación?',
					function(){ 
						that.pagar()
					},
					function(){ /* console.log('no') */ }
				).set('labels', {ok:'Sí, finalizar', cancel:'No'}); ;
			}
		},
		pagar(){
			let pendiente = this.habitacion.precio + this.habitacion.productos
			let datos = new FormData()
			datos.append('pedir', 'cobrarHabitacion');
			datos.append('idHabitacion', this.habitacion.idHabitacion);
			datos.append('idReserva', this.reserva.id);
			datos.append('idUsuario', localStorage.getItem('idUsuario'));
			datos.append('precioHabitacion',this.habitacion.precio );
			datos.append('precioProductos',this.habitacion.productos );
			datos.append('total', pendiente);
			datos.append('cantidadProductos',this.cesta.length );
			datos.append('numero',this.habitacion.numero );
			datos.append('tipoCuarto',this.habitacion.tipoCuarto );
			datos.append('pagos', JSON.stringify(this.pago) );
			fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			}).then(serv=> serv.json())
			.then(resp =>{
				if(resp.hayCaja=='siCaja'){
					this.$router.push({name: 'habitaciones'})
					alertify.message('Se guardó el pago.')
				}else{
					alertify.error('Hubo un error')
				}
			})
		},
		habilitar(){
			let that = this
			alertify.confirm('Habilitar servicio','¿Desea habilitar el alquiler de la habitación?',
				function(){ 
					that.tomar()
				},
				function(){ console.log('no') }
			);
		},
		tomar(){
			let pendiente = this.habitacion.precio + this.habitacion.productos
			let datos = new FormData()
			datos.append('pedir', 'tomarHabitacion');
			datos.append('idReserva', this.reserva.id);
			datos.append('idHabitacion', this.habitacion.idHabitacion);
			datos.append('idCliente', this.habitacion.idCliente);
			datos.append('entrada', this.habitacion.fechaInicio);
			datos.append('salida', this.habitacion.fechaFin);
			fetch(this.servidor+'Reservas.php',{
				method:'POST', body: datos
			}).then(serv=> serv.json())
			.then(resp =>{
				if(resp.reserva=='ocupado'){
					this.$router.push({name: 'detalleHabitacion', params:{idHabitacion: this.habitacion.idHabitacion}})
					alertify.message('Se registró la reserva.')
				}else{
					alertify.error('Hubo un error')
				}
			})
		},
		handleFocus(event){
			event.target.select()
		}
	},
	computed:{
		debeCliente(){
			let debe = parseFloat(this.habitacion.pagar + this.habitacion.productos) //55
			let paga = 0 //54
			
			paga += this.pago.efectivo <0 ? 0: this.pago.efectivo
			paga += this.pago.tarjeta <0 ? 0: this.pago.tarjeta
			paga += this.pago.yape <0 ? 0: this.pago.yape
			paga += this.pago.plin <0 ? 0: this.pago.plin
			return (debe > paga)? true: false
		}
	}
}
</script>

<style scoped>
	.card-title{
		font-size: 0.8rem;
		font-weight: bold;
	}
	label{
		margin-top:1rem;
	}
</style>