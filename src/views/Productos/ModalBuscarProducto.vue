<template>
	<div class="modal fade" id="modalProductos" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Selección de productos</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<table class="table table-hover">
						<thead>
							<th>N°</th>
							<th>Producto</th>
							<th>Precio</th>
							<th>@</th>
						</thead>
						<tbody>
							<tr v-for="(producto, index) in productos" class="puntero" @click="devolverProducto(index)" data-bs-dismiss="modal">
								<td>{{ index+1 }}</td>
								<td>{{ producto.producto }}</td>
								<td>{{ producto.precio }}</td>
								<td>
									<button class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-in-down-right"></i></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default{
	props:['productos', 'idReservacion'],
	methods:{
		devolverProducto(index){
			let datos = new FormData()
			datos.append('pedir', 'agregarCesta');
			datos.append('idReservacion', this.idReservacion);
			datos.append('idProducto', this.productos[index].id);
			datos.append('precio', this.productos[index].precio);
			fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			}).then(serv => serv.text() )
			.then(resp => {
				if(resp == 'ok'){
					alertify.message('Agregado correctamente')
					this.$emit('devolverProducto', index )
				}else
					alertify.error('Hubo un error al agregar el producto')
			})
		}
	}
}
</script>