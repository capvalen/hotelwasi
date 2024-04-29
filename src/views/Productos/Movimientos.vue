<template>
	<p class="fs-2"><i class="bi bi-list-nested"></i> Movimientos de productos</p>
	<p>Producto: {{ producto.producto }}</p>
	<p>Precio: {{ parseFloat(producto.precio).toFixed(2) }}</p>
	<p>Stock actual: {{ producto.stock }}</p>

	<p class="fw-bold">Movimientos del año {{$route.params.anio}}:</p>
	<table class="table table-hover">
		<thead>
			<th>N°</th>
			<th>Fecha</th>
			<th>Movimiento</th>
			<th>Cantidad</th>
		</thead>
		<tbody>
			<tr v-for="(producto, index) in kardex">
				<td>{{ index+1 }}</td>
				<td>{{ producto.fecha }}</td>
				<td>
					<span v-if="producto.idMovimiento == 1">Ingreso de stock</span>
					<span v-if="producto.idMovimiento == 2">Salida de stock</span>
					<span v-if="producto.idMovimiento == 3">Venta</span>
					<span v-if="producto.idMovimiento == 4">Devolución</span>
				</td>
				<td>{{ producto.cantidad }}</td>
			</tr>
		</tbody>
	</table>
</template>
<script>
export default {
	mounted(){
		this.cargarDatos()
	},
	data() {
		return {
			producto:[], kardex:[]
		}
	},
	methods: {
		cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'verMovimientos');
			datos.append('idProducto', this.$route.params.idProducto);
			datos.append('año', this.$route.params.anio);
			fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			})
			.then(resp => resp.json() )
			.then(data =>{
				this.producto = data.producto
				this.kardex = data.kardex
			})
		}
	},
}
</script>