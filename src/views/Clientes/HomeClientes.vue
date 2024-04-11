<template>
	<div>
		<p class="fs-2"><i class="bi bi-person-workspace"></i> Clientes</p>

		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col">
						<label for=""><i class="bi bi-funnel"></i> Filtrar cliente</label>
						<input type="text" class="form-control" placeholder="Nombre, DNI, Celular">
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
					<th>Apellidos y Nombres</th>
					<th>Celular</th>
					<th>Peruano</th>
					<th>Procedencia</th>
				</tr>
			</thead>
		</table>
		
	</div>
</template>
