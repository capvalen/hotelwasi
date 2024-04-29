<template>
	<div>
		<p class="fs-2"><i class="bi bi-bag-check"></i> Productos</p>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col">
						<label for=""><i class="bi bi-funnel"></i> Filtrar productos</label>
						<input type="text" class="form-control" placeholder="" @keyup.enter="buscar()" v-model="texto">
					</div>
					<div class="col-3 d-flex align-items-end">
						<button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNuevo"><i class="bi bi-asterisk"></i> Nuevo producto</button>
					</div>
				</div>
			</div>
		</div>

		<p class="my-3">Productos registrados:</p>

		<table class="table table-hover">
			<thead>
				<tr>
					<th>N°</th>
					<th>Producto</th>
					<th>Precio</th>
					<th>Stock</th>
					<th>@</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(producto, index) in productos">
					<td>{{ index+1 }}</td>
					<td class="text-capitalize">{{ producto.producto }}</td>
					<td>{{ parseFloat(producto.precio).toFixed(2) }}</td>
					<td>{{ producto.stock}}</td>
					<td>
						<button class="btn btn-sm btn-outline-success me-1" title="Ver movimientos" @click="verMovmientos(producto.id)"><i class="bi bi-list-nested"></i></button>
						<button class="btn btn-sm btn-outline-primary me-1" title="Modificar stock" 						@click="nuevo.id = producto.id; nuevo.nombre=producto.producto" data-bs-toggle="modal" data-bs-target="#modalStock" ><i class="bi bi-plus-slash-minus"></i></button>
						<button class="btn btn-sm btn-outline-danger me-1" @click="eliminar(producto.id, index)"><i class="bi bi-trash3"></i></button>
					</td>
				</tr>
			</tbody>
		</table>
		
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Nuevo producto</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label for="">Nombre del producto</label>
					<input type="text" class="form-control mb-3 " autocomplete="off" v-model="nuevo.nombre">
					<label for="">Precio (S/)</label>
					<input type="number" class="form-control mb-3" min="0" v-model="nuevo.precio">
					<label for="">Stock inicial</label>
					<input type="number" class="form-control" min="0" v-model="nuevo.stock">
				</div>
				<div class="modal-footer border-0">
					<button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" @click="crear()"><i class="bi bi-asterisk"></i> Crear producto</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="modalStock" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h1 class="modal-title fs-5" id="exampleModalLabel">Modificar stock</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<label for="">Nombre del producto</label>
					<p class="mb-3 text-capitalize fw-bold">{{ nuevo.nombre }}</p>
					<label for="">Cantidad</label>
					<input type="number" class="form-control" min="0" v-model="nuevo.stock">
					<label for="">Movimiento</label>
					<select class="form-select" v-model="nuevo.movimiento">
						<option value="1">Ingreso</option>
						<option value="2">Salida</option>
					</select>
					<label for="">Detalle extra</label>
					<input type="text" class="form-control" v-model="nuevo.observacion">
				</div>
				<div class="modal-footer border-0">
					<button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal" @click="modificarStock()"><i class="bi bi-asterisk"></i> Crear producto</button>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
export default{
	data(){ return{
		productos:[], texto:'', nuevo:{nombre:'', precio:0, stock:0, movimiento:1, observacion:'', id:-1}
	}},
	mounted(){
		this.cargarDatos();
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'listar');
			let serv = await fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.productos = temp.productos
		},
		async eliminar(id, index){
			if(confirm(`¿Desea eliminar ${this.productos[index].producto}?`)){
				let datos = new FormData()
				datos.append('pedir', 'borrar');
				datos.append('idProducto', id);
				fetch(this.servidor+'Productos.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.text() )
				.then(data => this.cargarDatos() )
			}
		},
		buscar(){
			if(this.texto == '') this.cargarDatos()
			else{
				let datos = new FormData()
				datos.append('pedir', 'buscarProducto');
				datos.append('texto', this.texto);
				fetch(this.servidor+'Productos.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.json() )
				.then(data => this.productos = data.productos )
			}
		},
		crear(){
			if(this.nuevo.nombre == '' || this.nuevo.precio<0 || this.nuevo.stock<0) alertify.error('Los datos no fueron rellenados correctamente')
			else{
				let datos = new FormData()
				datos.append('pedir', 'crear');
				datos.append('nuevo', JSON.stringify(this.nuevo));
				fetch(this.servidor+'Productos.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.json() )
				.then(data => this.cargarDatos() )
			}
		},
		modificarStock(){
			if( this.nuevo.stock<0 ) alertify.error('El stock no puede ser negativo')
			else{
				let datos = new FormData()
				datos.append('pedir', 'modificarStock');
				datos.append('nuevo', JSON.stringify(this.nuevo));
				fetch(this.servidor+'Productos.php',{
					method:'POST', body: datos
				})
				.then(resp => resp.text() )
				.then(data => this.cargarDatos() )
			}
		},
		verMovmientos(id){
			const ahora = new Date();
			this.$router.push({name: 'movimientosStock', params:{idProducto:id, anio: ahora.getFullYear() }})
		}
	},
}
</script>
