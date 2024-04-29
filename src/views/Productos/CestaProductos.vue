<template>
	<div class="card mb-2">
		<div class="card-body">
			<h5 class="card-title">Productos adicionales</h5>
			<label for=""> <i class="bi bi-funnel"></i> <small>Filtro</small></label>
			<div class="input-group mb-3">
				<input type="text" class="form-control" v-model="texto" autocomplete="off" @keypress.enter="buscarProducto()">
				<button class="btn btn-outline-secondary" type="button" @click="buscarProducto()"><i class="bi bi-search"></i></button>
			</div>
			<label class="mt-0 mb-3"><small>Productos agregados</small></label>
			<table class="table table-hover" v-if="productos.length>0">
				<thead>
					<th>N°</th>
					<th>Producto</th>
					<th>Precio</th>
					<th>Cantidad</th>
					<th>SubTotal</th>
					<th>@</th>
				</thead>
				<tbody>
					<tr v-for="(producto, index) in productos">
						<td>{{ index+1 }}</td>
						<td>{{producto.producto}}</td>
						<td>S/ {{parseFloat(producto.precio).toFixed(2)}}</td>
						<td>
							<div class="input-group mb-3">
								<input type="number" class="form-control text-center" v-model="producto.cantidad" @change="botones.push(index)" @keypress.enter="actualizarCantidad(index)" min="1">
								<button class="btn btn-outline-primary" v-show="botones.includes(index)" type="button" @click="actualizarCantidad(index)"><i class="bi bi-check2"></i></button>
							</div>
						</td>
						<td>S/ {{parseFloat(producto.precio * producto.cantidad).toFixed(2)}}</td>
						<td>
							<button class="btn btn-sm btn-outline-danger" @click="eliminarProducto(index)"><i class="bi bi-eraser-fill"></i></button>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" class="text-end">Total</td>
						<td class="fw-bold">S/ {{parseFloat(sumaProductos).toFixed(2)}}</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
			<p class="ms-3" v-else><i class="bi bi-chat-right-dots"></i> Lista vacía. No hay productos agregados</p>
		</div>
	</div>
</template>
<script>
export default{
	name: 'CestaProductos',
	props:['productos'],
	data(){ return {
		texto:'', botones:[]
	}},
	methods: {
		buscarProducto(){
			this.$emit('buscarProducto', this.texto)
		},
		actualizarCantidad(index){
			if(this.productos[index].cantidad<=0){
				this.productos[index].cantidad=1
				alertify.error('La cantidad no puede ser menor de 1')
			}

			let datos = new FormData()
			datos.append('pedir', 'sumarProducto');
			datos.append('id', this.productos[index].id);
			datos.append('idReservacion', this.productos[index].idReservacion);
			datos.append('cantidad', this.productos[index].cantidad);
			datos.append('total', this.sumaProductos);
			fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			})
			.then( serv => serv.text() )
			.then( resp => {
				if( resp == 'ok'){
					let indice = this.botones.findIndex(num => num == index)
					this.botones.splice(indice, 1);
					alertify.message('Actualizado con éxito')
					this.$emit('actualizarPrecioProductos', this.sumaProductos)
				}
				else
					alertify.error('Hubo un error al actualizar')
			})
		},
		eliminarProducto(index){
			const nTotal = this.sumaProductos - this.productos[index].cantidad * this.productos[index].precio
 
			let datos = new FormData()
			datos.append('pedir', 'eliminar');
			datos.append('id', this.productos[index].id);
			datos.append('idReservacion', this.productos[index].idReservacion);
			datos.append('cantidad', this.productos[index].cantidad);
			datos.append('precio', this.productos[index].precio);
			datos.append('total', this.sumaProductos);


			fetch(this.servidor+'Productos.php',{
				method:'POST', body: datos
			})
			.then( serv => serv.text() )
			.then( resp => {
				if(resp  == 'ok'){
					this.productos.splice(index,1)
					alertify.message('Actualizado con éxito')
					this.$emit('actualizarPrecioProductos', nTotal)
				}else
					alertify.error('Hubo un error al actualizar')
			})
		}
	},
	computed: {
		sumaProductos(){
			let suma = 0;
			this.productos.forEach(pro=>{
				suma += pro.cantidad * pro.precio
			})
			return suma
		}
	},
}
</script>