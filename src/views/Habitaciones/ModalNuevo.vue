<template>	
	<!-- Modal -->
	<div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Crear nueva habitación</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label for="">Tipo</label>
					<select class="form-select" id="" v-model="habitacion.tipo">
						<option v-for="tipo in tipos" :value="tipo.id">{{ tipo.habitacion }}</option>
					</select>
					<label for="">Número de habitación</label>
					<input type="number" class="form-control" v-model="habitacion.numero">

					<label for="detalle">Detalle <small>Ejm: Cama + TV</small></label>
					<input type="text" id="detalle" name="detalle" class="form-control" v-model="habitacion.detalle">

					<label for="precioPublico">Precio Público</label>
					<input type="number" id="precioPublico" name="precioPublico" class="form-control" v-model="habitacion.precioPublico">

					<label for="precioRebaja">Precio Rebaja</label>
					<input type="number" id="precioRebaja" name="precioRebaja" class="form-control" v-model="habitacion.precioRebaja">

					<label for="especial">Precio Especial</label>
					<input type="number" id="precioEspecial" name="precioEspecial" class="form-control" v-model="habitacion.precioEspecial">

					<label for="nivel">N° de Piso o Nivel</label>
					<select class="form-select" id="sleNivel" v-model="habitacion.nivel">
						<option v-for="nivel in niveles" :value="nivel">{{ nivel }}°</option>
					</select>

					
					<button class="btn btn-outline-primary mt-3" @click="crear()" ><i class="bi bi-asterisk"></i> Crear habitación</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	props:['tipos'],
	data(){ return{
		habitacion:{
			tipo:1, numero:100, detalle:'', precioPublico	:0, precioRebaja:0, precioEspecial:0, precioEspecial:0, nivel:1
		}, niveles:[1,2,3,4,5,6,7,8,9,10]
	}},
	methods: {
		crear(){
			if(this.habitacion.numero == '' || this.habitacion.numero<0)
				alertify.error('Debe ingresar un número de habitación', 10);
			else if(this.habitacion.precioPublico==0 || this.habitacion.precioPublico<0)
				alertify.error('El precio a público debe ser mayor a 0', 10);
			else if(this.habitacion.precioRebaja<0 || this.habitacion.precioEspecial<0)
				alertify.error('Ningun precio puede estar debajo de 0', 10);
			else{
				let datos = new FormData()
				datos.append('pedir', 'crear')
				datos.append('habitacion', JSON.stringify(this.habitacion))
				this.axios.post(this.servidor + 'Habitaciones.php', datos)
				.then(res =>{
					if (res.data.id){
						alertify.message('Creado exitoso', 10);
						let close = document.querySelector('#modalNuevo .btn-close')
						close.click()
						this.$emit('recargarHabitaciones')
					}
				})
			}

		}
	},
}
</script>
<style scoped>
label{
	margin-top:1rem;
}</style>