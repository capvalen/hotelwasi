<template>
	<p class="fs-2"> <i class="bi bi-person-workspace"></i> Detalle de cliente</p>
	
	<div class="row justify-content-center">
		<div class="col-md-6">
			<p>Puede editar los campos:</p>

			<label for="">Apellidos <span class="text-danger">*</span></label>
			<input type="text" class="form-control" v-model="cliente.apellidos">
			<label for="">Nombres <span class="text-danger">*</span></label>
			<input type="text" class="form-control" v-model="cliente.nombres">
			<label for="">Dirección</label>
			<input type="text" class="form-control" v-model="cliente.direccion">
			<label for="">Celular</label>
			<input type="text" class="form-control" v-model="cliente.celular">
			<label for="">Correo</label>
			<input type="text" class="form-control" v-model="cliente.correo">
			<label for="">Nacionalidad <span class="text-danger">*</span></label>
			<select class="form-select" v-model="cliente.idNacionalidad">
				<option value="1">Peruano</option>
				<option value="2">Extranjero</option>
			</select>
			<label for="">Procedencia <span class="text-danger">*</span></label>
			<select class="form-select" id="sltDepartamentos" v-model="cliente.procedencia">
				<option v-for="departamento in departamentos" :value="departamento.id">{{ departamento.departamento }}</option>
			</select>

			<button class="btn btn-outline-primary my-4" @click="actualizar()"><i class="bi bi-asterisk"></i> Actualizar cliente</button>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			cliente:[], departamentos:[]
		}
	},
	mounted() {
		this.cargarDatos()
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'buscarPorID');
			datos.append('idCliente', this.$route.params.idCliente);
			let serv = await fetch(this.servidor+'Clientes.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.cliente = temp.cliente

			datos.set('pedir', 'listar')
			let servDepartamentos = await fetch(this.servidor+'Departamentos.php',{
				method:'POST', body: datos
			})
			const tempDepartamentos = await servDepartamentos.json()
			this.departamentos = tempDepartamentos.departamentos
		},
		actualizar(){
			if(!this.cliente.apellidos, !this.cliente.nombres ) alertify.error('Todos los campos deben ser rellenados')
			else{
				let datos = new FormData()
				datos.append('pedir', 'actualizar')
				datos.append('cliente', JSON.stringify(this.cliente))
				this.axios.post(this.servidor + 'Clientes.php', datos)
				.then(res =>{
					if (res.data.id){
						alertify.message('Guardado exitoso', 10);
						this.$router.push({ name: 'clientes'});  //, params:{dniPadre: dni }
					}
				})
			}
		}

	},
}
</script>
<style scoped>
label{margin-top:1rem}
</style>