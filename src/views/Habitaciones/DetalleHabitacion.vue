<template>
	<h1>Detalle de la Reservación</h1>
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
				
				<CestaProductos :productos="cesta" @buscarProducto="buscarProducto" @actualizarPrecioProductos="actualizarPrecioProductos"></CestaProductos>
			</div>

			<div class="row my-3">
				<div class="col-6 d-grid mx-auto">
					<button class="btn btn-outline-primary btn-lg" @click="cobrar()"><i class="bi bi-asterisk"></i> Cobrar servicio</button>
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
	}},
	components:{ModalBuscarProducto, CestaProductos},
	mounted(){
		this.cargarDatos()
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalleOcupado');
			datos.append('idHabitacion', this.$route.params.idHabitacion);
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
			let that = this
			alertify.confirm('Cobrar servicio','¿Desea finalizar el alquiler de habitación?',
				function(){ 
					that.pagar()
				},
				function(){ console.log('no') }
			);
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

		}
	},
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