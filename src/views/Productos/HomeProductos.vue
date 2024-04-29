<template>
	<div>
		<p class="fs-2"><i class="bi bi-person-workspace"></i> Clientes</p>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col">
						<label for=""><i class="bi bi-funnel"></i> Filtrar clientes</label>
						<input type="text" class="form-control" placeholder="Nombre, DNI, Celular" @keyup.enter="buscar()" v-model="texto">
					</div>
					<div class="col-3 d-flex align-items-end">
						<router-link class="btn btn-outline-primary" to="/clientes/nuevo"><i class="bi bi-asterisk"></i> Nuevo cliente</router-link>
					</div>
				</div>
			</div>
		</div>

		<p class="my-3">Últimos clientes registrados:</p>

		<table class="table table-hover">
			<thead>
				<tr>
					<th>N°</th>
					<th>DNI</th>
					<th>Apellidos y Nombres</th>
					<th>Celular</th>
					<th>Peruano</th>
					<th>Procedencia</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(cliente, index) in clientes">
					<td>{{ index+1 }}</td>
					<td>{{ cliente.dni }}</td>
					<td class="text-capitalize">{{ cliente.apellidos }} {{ cliente.nombres }}</td>
					<td>{{ cliente.celular }}</td>
					<td>{{ cliente.idNacionalidad == '1' ? 'Si': 'No' }}</td>
					<td>{{ cliente.idNacionalidad == '2' ? '': cliente.departamento }}</td>
					<td>
						<button class="btn btn-sm btn-outline-danger" @click="eliminar(cliente.id, index)"><i class="bi bi-trash3"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</template>
<script>
export default{
	data(){ return{
		clientes:[], texto:''
	}},
	mounted(){
		this.cargarDatos();
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar');
			let serv = await fetch(this.servidor+'Clientes.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.clientes = temp.clientes
		},
		async eliminar(id, index){
			if(confirm(`¿Desea eliminar a ${this.clientes[index].apellidos} ${this.clientes[index].nombres}?`)){
				let datos = new FormData()
				datos.append('pedir', 'eliminar');
				datos.append('idCliente', id);
				fetch(this.servidor+'Clientes.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.json() )
				.then(data => this.cargarDatos() )
			}
		},
		buscar(){
			if(this.texto == '') this.cargarDatos()
			else{
				let datos = new FormData()
				datos.append('pedir', 'buscar');
				datos.append('texto', this.texto);
				fetch(this.servidor+'Clientes.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.json() )
				.then(data => this.clientes = data.clientes )
			}
		}
	},
}
</script>
