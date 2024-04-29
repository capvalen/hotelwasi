<template>
	<div class="container-fluid">
		<div class="row">
			<section class="col-12 col-lg-2 p-4">
				<div class="row">
					<div class="col col-lg-12 d-flex justify-content-center">
						<img src="./assets/logo.jpg" class="img-fluid px-md-4 mb-3">
					</div>
					<div class="col d-block d-lg-none d-xl-none d-xxl-none">
						<button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#contraido" aria-expanded="false" aria-controls="contraido"><i class="bi bi-list"></i></button>
					</div>
				</div>
				<p class="text-center text-body-secondary"><small><small>Versión 1.0</small></small></p>
				<ul class="list-group list-group-flush collapse dont-collapse-sm " id="contraido">
					<router-link to="/" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='/'}"  @click="activarBoton('/')"><i class="bi bi-house"></i> Inicio</router-link>
					<router-link to="/caja" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='caja'}"  @click="activarBoton('caja')"><i class="bi bi-piggy-bank"></i> Caja</router-link>
					<router-link to="/reservas" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='reservas'}"  @click="activarBoton('reservas')"><i class="bi bi-lamp-fill"></i> Reservas</router-link>
					<router-link to="/habitaciones" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='habitaciones'}"  @click="activarBoton('habitaciones')"><i class="bi bi-key-fill"></i> Habitaciones</router-link>
					<router-link to="/clientes" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='clientes'}"  @click="activarBoton('clientes')"><i class="bi bi-person-workspace"></i> Clientes</router-link>
					<router-link to="/productos" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='productos'}"  @click="activarBoton('productos')"><i class="bi bi-bag-check"></i> Productos</router-link>
					<router-link to="/reportes" class="list-group-item list-group-item-action" :class="{'active': botonActivo=='reportes'}"  @click="activarBoton('reportes')"><i class="bi bi-luggage"></i> Reportes</router-link>
					<li class="list-group-item list-group-item-action"><i class="bi bi-chat-dots"></i> Soporte</li>
				</ul>
			</section>
			<section class="col mt-4">
				<router-view/>
			</section>
		</div>
	</div>


</template>

<script>
export default{
	data() {
    return {
      botonActivo: null
    };
  },
	mounted(){
		localStorage.setItem('idUsuario', 1)
	},
  methods: {
    activarBoton(boton) {
      this.botonActivo = boton;
    }
  }

}
</script>

<style>
.ajs-message {
	background-color: #00ad2d!important;
	color: aliceblue!important;
}
.ajs-error{
	background-color: #f51e01!important;
	color: aliceblue!important;
}
.puntero{ cursor:pointer}
@media (min-width: 988px) {
  .collapse.dont-collapse-sm {
    display: block;
    height: auto !important;
    visibility: visible;
  }
}
.text-success{
	color:#15AC53!important;
}
</style>