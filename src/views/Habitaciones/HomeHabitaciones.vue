<template>
	<div>
		<h1>Habitaciones</h1>

		<div class=" mb-2">
			<p class="mb-0"><i class="bi bi-funnel"></i> Filtros</p>
			<div class="btn-group" role="group" aria-label="Basic outlined example">
				<button type="button" class="btn btn-outline-primary active">Todos</button>
				<button type="button" class="btn btn-outline-primary">Simple</button>
				<button type="button" class="btn btn-outline-primary">Matrimonial</button>
				<button type="button" class="btn btn-outline-primary">Doble</button>
				<button type="button" class="btn btn-outline-primary">Triple</button>
			</div>
		</div>

		<div class="row row-cols-2 row-cols-md-5 row-cols-lg-6">
			<router-link to="/habitacion/detalle/" class="col my-2 text-decoration-none" v-for="habitacion in habitaciones" >
			
				<div class="card card-body" >
					<p class="fw-bold mb-0"><small>Simple</small></p>
					<p class="text-body-secondary fw-light lh-1"><small><small>Cama + TV + Ducha</small></small></p>

					<img src="@/assets/bed.png" style="width: 64px; margin: 0 auto;">
					<p class="mb-0 text-center"> {{habitacion}}</p>
					<div class="row ">
						<div class="col-12 col-md-8">
							<p class="fw-bold mb-0 text-success">S/ 50.00 </p>
						</div>
						<div class="col-12 col-md text-center">
							<p class="fw-bold mb-0"> <img src="@/assets/free.png" style="width: 32px;"> </p>
						</div>
					</div>
				</div>
			
			</router-link>
		</div>
	</div>
</template>

<script>
export default{
	data(){ return {
		habitaciones:['101','102','103','201','202','203', '301','302']
	}}
}
</script>

<style scoped>
.card{
	color:black
}
.card:hover{
	font-weight: bold;
	box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
	cursor:pointer;
	color:black
}
</style>