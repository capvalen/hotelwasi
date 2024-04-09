<template>
	<div>
		<p class="fs-2"><i class="bi bi-person-workspace"></i> Registro de nuevo cliente</p>
		<p class="mb-0">Rellene los datos necesarios:</p>
		<p class="text-body-secondary"><small>Los <span class="text-danger">*</span> son campos obligatorios</small></p>

		<div class="row">
			<div class="col-12 col-md-6">
				<div class="card">
					<div class="card-body">
						<label >D.N.I.</label>
						<input type="text" class="form-control" autocomplete="off">
						<label >Apellidos</label>
						<input type="text" class="form-control" autocomplete="off">
						<label >Nombres</label>
						<input type="text" class="form-control" autocomplete="off">
						<label >Dirección</label>
						<input type="text" class="form-control" autocomplete="off">
						<label >Celular</label>
						<input type="text" class="form-control" autocomplete="off">
						<label >¿Nacionalidad Peruana?</label>
						<select class="form-select" id="sltNacionalidad">
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
						<label for="">Procedencia</label>
						<input type="text" class="form-control" autocomplete="off">
						<label for="">Dato extra</label>
						<input type="text" class="form-control" autocomplete="off">
						<button class="btn btn-outline-primary mt-3"><i class="bi bi-asterisk"></i> Crear cliente</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
label{
	margin-top:1rem;
}
</style>