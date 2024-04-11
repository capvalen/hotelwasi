<template>
	<h1>Reservaciones</h1>
	<div class="row">
		<div class="col-md-2">
			<div class="card">
				<div class="card-body text-center">
					<div class=""><img src="@/assets/bed.png" style="width: 64px; margin: 0 auto;" ></div>
					<p class="mb-0"><small>Habitación</small></p>
					<p class="fs-1 mb-0">105</p>
					<p class="mb-0"><small>Simple</small></p>
				</div>
			</div>
		</div>
		
			<div class="col-md-10 col-lg-8">
				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title">Días de reserva</h5>
						<p class="mb-0 text-center">¿Cuántos días desea hospedarse en la habitación?</p>

						<div class="row justify-content-center">
							<div class="col-6">
								<div class="input-group ">
									<input type="number" class="form-control text-center fs-5" min="1" step="1" value="1">
									<button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="bi bi-caret-right"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title ">Detalle de la reserva</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">Fecha de inicio</label>
								<input type="date" class="form-control" value="2024-06-02">
							</div>
							<div class="col">
								<label for="">Hora de inicio</label>
								<input type="time" class="form-control" value="19:50">
							</div>
							<div class="col">
								<label for="">Fecha de salida</label>
								<input type="date" class="form-control" value="2024-06-02">
							</div>
							<div class="col">
								<label for="">Hora de salida</label>
								<input type="time" class="form-control" value="23:50">
							</div>
							<div class="col">
								<label for="">Precio a acordar</label>
								<select class="form-select" id="">
									<option value="1">Normal</option>
									<option value="2">Descuento</option>
									<option value="3">Especial</option>
								</select>
							</div>
							<div class="col">
								<label for="">Adelanto (S/)</label>
								<input type="number" class="form-control" min="0" step="1" value="0">
							</div>
						</div>
					</div>
				</div>

				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title">Detalle del cliente</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">DNI del Cliente  <router-link class="badge text-bg-primary puntero" to="/clientes/nuevo" target="_blank"><i class="bi bi-plus-lg"></i></router-link></label>
								<div class="input-group ">
									<input type="text" class="form-control" >
									<button class="btn btn-outline-secondary" type="button" ><i class="bi bi-search"></i></button>
								</div>
							</div>
							<div class="col">
								<label for="">Apellidos</label>
								<input type="text" class="form-control">
							</div>
							<div class="col">
								<label for="">Nombres</label>
								<input type="text" class="form-control">
							</div>
							<div class="col">
								<label for="">Procedencia</label>
								<input type="text" class="form-control">
							</div>
						</div>
					</div>
				</div>
			</div>
		
	</div>
</template>

<style scoped>
.card-title{
	font-size: 0.8rem;
	font-weight: bold;
}
label{
	margin-top:1rem;
}
</style>