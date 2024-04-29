<template>
	<div>
		<p class="mb-0 fs-2"><i class="bi bi-lamp-fill"></i> Reservaciones</p>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col">
						<label for=""><i class="bi bi-funnel"></i> Filtrar reservas</label>
						<div class="row">
							<div class="col">
								<input type="date" class="form-control" v-model="filtro.inicio">
							</div>
							<div class="col">
								<input type="date" class="form-control" v-model="filtro.fin">
							</div>
						</div>
					</div>
					<div class="col-3 d-flex align-items-end">
						<button class="btn btn-outline-primary" to="/clientes/nuevo"><i class="bi bi-search"></i> Buscar</button>
					</div>
				</div>
			</div>
		</div>

		<table class="table table-hover mt-3">
			<thead>
				<th>N°</th>
				<th>Cliente</th>
				<th>Fecha ingreso</th>
				<th>Fecha salida</th>
				<th>Habitación</th>
				<th>Precio</th>
				<th>Estado</th>
			</thead>
			<tbody>
				<tr v-for="(reserva, index) in reservaciones">
					<td>{{ index+1 }}</td>
					<td class="text-capitalize">{{ reserva.apellidos }} {{ reserva.nombres }}</td>
					<td>{{ fechaLatam(reserva.fechaFin) }}</td>
					<td>{{ fechaLatam(reserva.fechaInicio) }}</td>
					<td>{{ reserva.numero }}</td>
					<td>S/ {{ parseFloat(reserva.precio).toFixed(2) }}</td>
					<td>{{ reserva.atencion }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
import moment from 'moment'
import Reservaciones from './Reservaciones.vue'
export default {
	data() {
		return {
			texto:'', filtro:{inicio:moment().format('YYYY-MM-DD'), fin:moment().format('YYYY-MM-DD')}, reservaciones : []
		}
	},
	mounted(){
		this.filtrarReservaciones()
	},
	methods: {
		async filtrarReservaciones(){
			let datos = new FormData()
			datos.append('pedir', 'filtrarReservaciones');
			datos.append('inicio', this.filtro.inicio );
			datos.append('fin', this.filtro.fin );
			let serv = await fetch(this.servidor+'Reservas.php',{
				method:'POST', body: datos
			})
			const respuesta = await serv.json()
			this.reservaciones = respuesta.reservaciones
		},
		fechaLatam(fecha){
			if(fecha) return moment(fecha, 'YYYY-MM-DD HH:mm').format('DD/MM/YYYY h:mm a') 
		},
	},
}
</script>