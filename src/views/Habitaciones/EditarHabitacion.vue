<template>
	<p class="fs-2"><i class="bi bi-key"></i> Editar habitación</p>

	<div class="row justify-content-center">
		<div class="col-md-6">
			<p class="mb-0">Puede editar los siguientes campos</p>
			<p class="text-body-secondary"><small>Los <span class="text-danger">*</span> son campos obligatorios</small></p>
			
			<div class="card">
				<div class="card-body">
					<label for="">Tipo de habitación</label>
					<select class="form-select" id="sltTipos" v-model="habitacion.tipo">
						<option v-for="tipo in tipos" :value="tipo.id">{{ tipo.habitacion }}</option>
					</select>
					<label >Número <span class="text-danger">*</span></label>
					<input type="text" class="form-control" autocomplete="off" v-model="habitacion.numero">
					<label >Detalle. <small>Ejm: Cama + TV</small></label>
					<input type="text" class="form-control" autocomplete="off" v-model="habitacion.detalle">
					<label >Precio al público <span class="text-danger">*</span></label>
					<input type="number" class="form-control" autocomplete="off" v-model="habitacion.precioPublico">
					<label >Precio de Rebaja</label>
					<input type="number" class="form-control" autocomplete="off" v-model="habitacion.precioRebaja">
					<label >Precio de Especial</label>
					<input type="number" class="form-control" autocomplete="off" v-model="habitacion.precioEspecial">
					<label >Nivel o N° de Piso que se encuentra <span class="text-danger">*</span></label>
					<input type="number" class="form-control" autocomplete="off" v-model="habitacion.nivel">
					
					<button class="btn btn-outline-primary mt-3" @click="actualizar()"><i class="bi bi-asterisk"></i> Actualizar datos</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	data(){ return {
		tipos:[], habitacion:[]
	}},
	mounted() {
		this.cargarDatos();
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalleHabitacion');
			datos.append('idHabitacion', this.$route.params.idHabitacion);
			let serv = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.habitacion = temp.habitacion

			datos.set('pedir', 'basicos');
			let servTipo = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			const tempTipo = await servTipo.json()
			this.tipos = tempTipo.tipos
		},
		actualizar(){
			let datos = new FormData()
			datos.append('pedir', 'actualizarHabitacion');
			datos.append('habitacion', JSON.stringify(this.habitacion));
			fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			}).then(serv=> serv.text() )
			.then(resp => {
				if(resp=='ok'){
					this.$router.push('/habitaciones')
					alertify.message('Actualizado correctamente')
				}else{
					alertify.error('Hubo un error al actualizar')
				}
			})
		}
	},
}
</script>
<style scoped>
label{
	margin-top:1rem;
}
</style>