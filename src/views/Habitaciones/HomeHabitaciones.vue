<template>
	<div>
		<h1>Habitaciones</h1>

		<div class=" mb-2">
			<p class="mb-0"><i class="bi bi-funnel"></i> Filtros</p>
			<div class="btn-group" role="group" aria-label="Basic outlined example">
				<button type="button" class="btn btn-outline-primary active btnFiltro" @click="filtrar(-1, $event)">Todos</button>
				<button type="button" class="btn btn-outline-primary btnFiltro" @click="filtrar(verFiltro=tipo.id, $event)" v-for="tipo in tipos">{{tipo.habitacion}}</button>
			</div>
			<button class="btn btn-outline-warning ms-3" data-bs-toggle="modal" data-bs-target="#modalNuevo"><i class="bi bi-asterisk"></i> Nueva habitación</button>
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
									'text-success' : habitacion.estado==1,
									'text-danger' : habitacion.estado==2,
									'text-primary' : habitacion.estado==3,
									'text-warning' : habitacion.estado==4,
								}"
								>S/ {{parseFloat(habitacion.precioPublico).toFixed(2)}} </p>
							</div>
							<div class="col-12 col-md text-center">
								<p class="fw-bold mb-0">
									<img v-if="habitacion.estado == 1" src="@/assets/free.png" style="width: 32px;">
									<img v-if="habitacion.estado == 2" src="@/assets/bussy.png" style="width: 32px;">
									<img v-if="habitacion.estado == 3" src="@/assets/cleanning.png" style="width: 32px;">
									<img v-if="habitacion.estado == 4" src="@/assets/reserved.png" style="width: 32px;">
								</p>
							</div>
						</div>
						<div class=" text-end" id="pDescripcion">
							<p v-if="habitacion.estado == 1" class="mb-0 text-success">Libre</p>
							<p v-if="habitacion.estado == 2" class="mb-0 text-danger">Ocupado</p>
							<p v-if="habitacion.estado == 3" class="mb-0 text-primary">En limpieza</p>
							<p v-if="habitacion.estado == 4" class="mb-0 text-warning">Reservado</p>
						</div>
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
						<div class="col d-grid my-1" v-if="[2, 4].includes(selecccionado.estado)">
							<button class="btn btn-outline-light" >
								<img src="@/assets/bed.png" style="width: 32px;">
								<p class="mb-0 text-secondary">Detalle de alquiler</p>
							</button>
						</div>
						<div class="col d-grid  my-1">
							<button class="btn btn-outline-light" v-if="selecccionado.estado==1" @click="irA('inmediato')">
								<img src="@/assets/bussy.png" style="width: 32px;">
								<p class="mb-0 text-danger">Uso inmediato</p>
							</button>
							<button class="btn btn-outline-light" v-if="selecccionado.estado==2 || selecccionado.estado==3">
								<img src="@/assets/free.png" style="width: 32px;">
								<p class="mb-0 text-success">Liberar</p>
							</button>
						</div>
						<div class="col d-grid my-1">
							<button class="btn btn-outline-light">
								<img src="@/assets/cleanning.png" style="width: 32px;">
								<p class="mb-0 text-primary">Iniciar limpieza</p>
							</button>
						</div>
						<div class="col d-grid my-1">
							<button class="btn btn-outline-light">
								<img src="@/assets/reserved.png" style="width: 32px;">
								<p class="mb-0 text-warning">Reservar</p>
							</button>
						</div>
						<div class="col my-1">
							<button class="btn btn-outline-light">
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
import modalNuevo from './ModalNuevo'
export default{
	data(){ return {
		habitaciones:[], pisos:[], habitacionesPorPiso:{}, verFiltro:-1,
		tipos:[], selecccionado:[]
	}},
	components:{modalNuevo},
	mounted() {
		this.cargarDatos()
		this.actualizar()
	},
	methods: {
		async actualizar(){
			let datos = new FormData()
			datos.append('pedir', 'solicitar');
			let serv = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.habitaciones = temp.habitaciones
			//separa las habitaciones por piso
			for (const habitacion of this.habitaciones) {
				const piso = habitacion.nivel;
				if (!this.habitacionesPorPiso[piso] && piso !=0) {
					this.habitacionesPorPiso[piso] = [];
				}
				this.habitacionesPorPiso[piso].push(habitacion);
			}
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
			this.selecccionado = this.habitacionesPorPiso[nivel][index]
			const modalQueHacer = new bootstrap.Modal(document.getElementById('modalQueHacer'))
			modalQueHacer.show();
		},
		irA(tipo){
			let close = document.querySelector('#modalQueHacer .btn-close')
			close.click()
			
			switch (tipo) {
				case 'inmediato': this.$router.push({ name: 'registrarHabitacion', params:{idHabitacion: this.selecccionado.id }});
					break;
				default:
					break;
			}
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
</style>