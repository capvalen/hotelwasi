<template>
	<div>
		<h1></h1>
		<p class="fs-2"><i class="bi bi-key"></i> Habitaciones</p>

		<div class=" mb-2">
			<p class="mb-0"><i class="bi bi-funnel"></i> Filtros</p>
			<div class="btn-group" role="group" aria-label="Basic outlined example">
				<button type="button" class="btn btn-outline-primary active btnFiltro" @click="filtrar(-1, $event)">Todos</button>
				<button type="button" class="btn btn-outline-primary btnFiltro" @click="filtrar(verFiltro=tipo.id, $event)" v-for="tipo in tipos">{{tipo.habitacion}}</button>
			</div>
			<button class="btn btn-outline-warning ms-3" data-bs-toggle="modal" data-bs-target="#modalNuevo"><i class="bi bi-asterisk"></i> Nueva habitación</button>
			<button class="btn btn-outline-secondary ms-3" @click="actualizar()"><i class="bi bi-arrow-clockwise"></i> Actualizar</button>
		</div>

		<div class=""  v-for="piso in habitacionesPorPiso">
			<div class="row">
				<div class="col">
					<p class="fw-bold mb-0">{{ piso[0].nivel }}° Nivel</p>
				</div>
			</div>
			<div class="row row-cols-2 row-cols-md-4 row-cols-lg-6">
				<div class="col my-2 text-decoration-none" v-for="(habitacion, index) in piso" v-show="habitacion.tipo==verFiltro || verFiltro==-1">
				
					<div class="card card-body" @click="modalQueHacer(index, piso[0].nivel)" >
						<p class="fw-bold mb-0"><small>Habitación {{habitacion.tipoCuarto}}</small></p>
						<p class="text-body-secondary fw-light lh-1"><small><small>{{habitacion.detalle}}</small></small></p>
						<img src="@/assets/bed.png" style="width: 64px; margin: 0 auto;">
						<p class="mb-0 text-center"> {{habitacion.numero}}</p>
						<div class="row ">
							<div class="col-12 col-md-8">
								<p class="fw-bold mb-0" :class="
								{
									'text-success' : habitacion.estado==1 && !habitacion.tieneReserva,
									'text-danger' : habitacion.estado==2,
									'text-primary' : habitacion.estado==3,
									'text-warning' : habitacion.estado==1 && habitacion.tieneReserva,
								}"
								>S/ {{parseFloat(habitacion.precioPublico).toFixed(2)}} </p>
							</div>
							<div class="col-12 col-md text-center">
								<p class="fw-bold mb-0">
									<img v-if="habitacion.estado == 1 && !habitacion.tieneReserva" src="@/assets/free.png" style="width: 32px;">
									<img v-if="habitacion.estado == 2" src="@/assets/bussy.png" style="width: 32px;">
									<img v-if="habitacion.estado == 3" src="@/assets/cleanning.png" style="width: 32px;">
									<img v-if="habitacion.estado == 1 && habitacion.tieneReserva" src="@/assets/reserved.png" style="width: 32px;">
								</p>
							</div>
						</div>
						<div class=" text-end" id="pDescripcion">
							<p v-if="habitacion.estado == 1 && !habitacion.tieneReserva" class="mb-0 text-success">Libre</p>
							<p v-if="habitacion.estado == 2" class="mb-0 text-danger">Ocupado</p>
							<p v-if="habitacion.estado == 3" class="mb-0 text-primary">En limpieza</p>
							<p v-if="habitacion.estado == 1 && habitacion.tieneReserva" class="mb-0 text-warning">Reservación</p>
						</div>
						<p class="tieneReserva text-purple mb-0" v-if="habitacion.tieneReserva"><i class="bi bi-exclamation-diamond-fill"></i> Tiene reserva {{ habitacion.proxima }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<modal-nuevo :tipos ="tipos" @recargarHabitaciones="actualizar()"></modal-nuevo>

	<!-- Modal -->
	<div class="modal fade" id="modalQueHacer" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Seleccione una opción</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row row-cols-3">
						<div class="col d-grid my-1" v-if="[2, 4].includes(seleccionado.estado)">
							<button class="btn btn-outline-light" @click="irA('detalleHabitacion')">
								<img src="@/assets/bed.png" style="width: 32px;">
								<p class="mb-0 text-secondary">Detalle de alquiler</p>
							</button>
						</div>
						<div class="col d-grid  my-1" v-if="seleccionado.estado==1" @click="irA('inmediato')">
							<button class="btn btn-outline-light" >
								<img src="@/assets/bussy.png" style="width: 32px;">
								<p class="mb-0 text-danger">Uso inmediato</p>
							</button>
						</div>
						<div class="col d-grid my-1" v-if="seleccionado.estado==3" @click="liberarLimpieza()" data-bs-dismiss="modal">
							<button class="btn btn-outline-light">
								<img src="@/assets/cleanning.png" style="width: 32px;">
								<p class="mb-0 text-primary">Finalizar limpieza</p>
							</button>
						</div>
						<div class="col d-grid my-1" v-if="seleccionado.tieneReserva==true">
							<button class="btn btn-outline-light" @click="irA('reservado')">
								<img src="@/assets/shake.png" style="width: 32px;">
								<p class="mb-0 text-secondary">Ver reservación</p>
							</button>
						</div>
						<div class="col d-grid my-1">
							<button class="btn btn-outline-light" @click="irA('reservar')">
								<img src="@/assets/reserved.png" style="width: 32px;">
								<p class="mb-0 text-warning">Reservar</p>
							</button>
						</div>
						<div class="col my-1">
							<button class="btn btn-outline-light" @click="irA('editar')">
								<img src="@/assets/edit.png" style="width: 32px;">
								<p class="mb-0 text-purple">Editar habitación</p>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</template>

<script>
import moment from 'moment'
import modalNuevo from './ModalNuevo'
export default{
	data(){ return {
		habitaciones:[], pisos:[], habitacionesPorPiso:{}, verFiltro:-1,
		tipos:[], seleccionado:[]
	}},
	components:{modalNuevo},
	mounted() {
		this.cargarDatos()
		this.actualizar()
		setInterval( ()=>{
			this.actualizar()
		}, 90000) //busca cada minuto y medio		
	},
	methods: {
		actualizar(){
			this.habitacionesPorPiso={};
			let datos = new FormData()
			datos.append('pedir', 'solicitar');
			fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			.then(serv => serv.json() )
			.then(temp => {
				this.habitaciones = temp.habitaciones
				this.reservaciones = temp.reservaciones
				//separa las habitaciones por piso
				for (const habitacion of this.habitaciones) {
					const piso = habitacion.nivel;
					if (!this.habitacionesPorPiso[piso] && piso !=0) {
						this.habitacionesPorPiso[piso] = [];
					}
					let indice = this.reservaciones.findIndex(x=> x.idHabitacion == habitacion.id )
					if(indice>-1){//encontró una reserva
						habitacion.tieneReserva = true
						habitacion.idReservado = this.reservaciones[indice].id
						moment.locale('es')
						habitacion.proxima = moment(this.reservaciones[indice].fechaInicio).fromNow()
					}else{
						habitacion.tieneReserva=false
					}
					this.habitacionesPorPiso[piso].push(habitacion);
				}
			})
		},
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'basicos');
			let serv = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.tipos = temp.tipos
		},
		filtrar(tipo, event){
			const botones = document.querySelectorAll('.btnFiltro')
			botones.forEach(boton => {
				boton.classList.remove('active');
			});
			event.target.classList.add('active')
			this.verFiltro = tipo;
		},
		modalQueHacer(index, nivel){
			this.seleccionado = this.habitacionesPorPiso[nivel][index]
			const modalQueHacer = new bootstrap.Modal(document.getElementById('modalQueHacer'))
			modalQueHacer.show();
		},
		irA(tipo){
			let close = document.querySelector('#modalQueHacer .btn-close')
			close.click()
			
			switch (tipo) {
				case 'inmediato': this.$router.push({ name: 'registrarHabitacion', params:{idHabitacion: this.seleccionado.id }}); break;
				case 'detalleHabitacion': this.$router.push({ name: 'detalleHabitacion', params:{idHabitacion: this.seleccionado.id }}); break;
				case 'reservar': this.$router.push({ name: 'reservarHabitacion', params:{idHabitacion: this.seleccionado.id }}); break;
				case 'editar': this.$router.push({ name: 'editarHabitacion', params:{idHabitacion: this.seleccionado.id }}); break;
				case 'reservado': this.$router.push({ name: 'detalleReserva', params:{idReserva: this.seleccionado.idReservado }}); break;
				default: break;
			}
		},
		liberarLimpieza(){
			let datos = new FormData()
			datos.append('pedir', 'liberarLimpieza');
			datos.append('idHabitacion', this.seleccionado.id);
			fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			}).then(serv => serv.text() )
			.then(resp => {
				if(resp == 'ok'){
					alertify.message('Actualizado con éxito')
				}else{
					alertify.error('Hubo un error actualizando')
				}
				this.actualizar()
			})
		}
	},
}
</script>

<style scoped>
	.card{
		color:black
	}
	.card:hover{
		font-weight: bold;
		box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
		cursor:pointer;
		color:black
	}
	#pDescripcion{
		font-size:0.7rem;
	}
	.text-purple{
		color: #4400aa;
	}
	.tieneReserva{
		font-size:0.85rem;
	}
</style>