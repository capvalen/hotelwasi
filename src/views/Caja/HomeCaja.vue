<template>
	<div>
		<h1>Caja</h1>
		<div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Control de caja</h5>
            <p class="card-text">Monto actual en caja</p>
						<p class="fs-2 text-center">S/ 45.00</p>
          </div>
        </div>
      </div>
    </div>
		<a href="#!" class="text-decoration-none my-3"><i class="bi bi-chevron-double-down"></i> Ver detalle de movimientos</a>
		<table class="table table-hover container">
			<thead>
				<tr>
					<th>N°</th>
					<th>Movimiento</th>
					<th>Tipo</th>
					<th>Monto</th>
					<th>Obs.</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Apertura de caja</td>
					<td>Ingreso</td>
					<td class="text-primary">+10.00</td>
					<td></td>
				</tr>
				<tr>
					<td>2</td>
					<td>Alquiler de habitacion 105</td>
					<td>Ingreso</td>
					<td class="text-primary">+50.00</td>
					<td></td>
				</tr>
				<tr>
					<td>3</td>
					<td>Articulos de limpieza</td>
					<td>Egreso</td>
					<td class="text-danger">-5.00</td>
					<td></td>
				</tr>
			</tbody>
		</table>
	</div>
</template>