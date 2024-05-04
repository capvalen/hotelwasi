<template>
	<p class="fs-2"><i class="bi bi-person-workspace"></i> Registro de nuevo cliente</p>

	<div class="row justify-content-center">
		<div class="col-md-6">
			<p class="mb-0">Rellene los datos necesarios:</p>
			<p class="text-body-secondary"><small>Los <span class="text-danger">*</span> son campos obligatorios</small></p>
			
			<div class="card">
				<div class="card-body">
					<label >D.N.I. <span class="text-danger">*</span></label>
					<input type="text" class="form-control" autocomplete="off" v-model="cliente.dni">
					<label >Apellidos <span class="text-danger">*</span></label>
					<input type="text" class="form-control text-capitalize" autocomplete="off" v-model="cliente.apellidos">
					<label >Nombres <span class="text-danger">*</span></label>
					<input type="text" class="form-control text-capitalize" autocomplete="off" v-model="cliente.nombres">
					<label >Dirección</label>
					<input type="text" class="form-control" autocomplete="off" v-model="cliente.direccion">
					<label >Celular</label>	
					<input type="text" class="form-control" autocomplete="off" v-model="cliente.celular">
					<label >Fecha de nacimiento</label>	
					<input type="date" class="form-control" v-model="cliente.fechaNacimiento">
					<label >¿Nacionalidad Peruana?</label>
					<select class="form-select" id="sltNacionalidad" v-model="cliente.idNacionalidad">
						<option value="1">Si</option>
						<option value="0">No</option>
					</select>
					<label for="">Procedencia</label>
					<select class="form-select" id="sltDepartamentos" v-model="cliente.procedencia">
						<option v-for="departamento in departamentos" :value="departamento.id">{{ departamento.departamento }}</option>
					</select>
					<label for="">Dato extra</label>
					<input type="text" class="form-control" autocomplete="off" v-model="cliente.observaciones">
					<button class="btn btn-outline-primary mt-3" @click="guardar()"><i class="bi bi-asterisk"></i> Crear cliente</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	data(){ return {
		cliente:{
			dni:'', apellidos:'', nombres:'', direccion:'', celular:'', idNacionalidad:1, procedencia:1, observaciones:'', fechaNacimiento:null
		}, departamentos:[]
	}},
	mounted() {
		this.cargarDatos();
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar');
			let servDepartamentos = await fetch(this.servidor+'Departamentos.php',{
				method:'POST', body: datos
			})
			const tempDepartamentos = await servDepartamentos.json()
			this.departamentos = tempDepartamentos.departamentos
		},
		guardar(){
			if( this.cliente.dni =='' || this.cliente.dni.length<8 )
				alert('Debe ingresar un DNI obligatorio')
			else if( this.cliente.nombres =='' || this.cliente.apellidos == '' )
				alert('Falta rellenar datos de nommbres y/o apellidos')
			else{
				let datos = new FormData()
				datos.append('pedir', 'crear')
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
label{
	margin-top:1rem;
}
</style>