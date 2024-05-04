<template>
	<div>
		<p class="fs-2"><i class="bi bi-luggage"></i> Reportes</p>
		<div class="card card-body">
			<div class="row">
				<div class="col-4">
					<label for="">Reportes disponibles:</label>
					<select class="form-select" @change="elegirReporte()" v-model="queReporte">
						<option value="3">Cajas por turnos</option>
						<option value="1">Pernoctación por cliente</option>
						<option value="2">Pernoctación por fechas</option>
					</select>
				</div>
				<div class="col-6" v-if="verExtraTexto">
					<label for="">Filtro</label>
					<input type="text" class="form-control" v-model="texto">
				</div>
				<div class="col-3" v-if="verExtraFechas">
					<label for="">Fecha inicial</label>
					<input type="date" class="form-control" v-model="fechas.inicio">
				</div>
				<div class="col-3" v-if="verExtraFechas">
					<label for="">Fecha final</label>
					<input type="date" class="form-control" v-model="fechas.fin">
				</div>
				<div class="col d-flex align-items-end">
					<button class="btn btn-outline-primary" @click="pedirReporte()"><i class="bi bi-file-earmark"></i> Generar</button>
				</div>
			</div>
		</div>

		<section class="my-2" id="respuestas" v-if="verRespuesta">
			<div v-if="queReporte==1">
				<div class="card">
					<div class="card-body">
						<p class="mb-0">Cliente: <strong class="text-capitalize">{{ cabecera.apellidos }} {{ cabecera.nombres }}</strong></p>
						<p class="mb-0">Edad: {{ fechaLatam(cabecera.fechaNacimiento) }} ({{ calcularEdad(cabecera.fechaNacimiento) }} años)</p>
						<p class="mb-0">Celular: {{ cabecera.celular ? cabecera.celular: '-' }}</p>
						<p class="mb-0">Nacionalidad: {{ cabecera.idNacionalidad =='1' ? 'Peruana': 'Extranjera' }}</p>
						<p class="mb-0">Procedencia: {{ cabecera.departamento }}</p>
					</div>
				</div>

				<div class="card my-2">
					<div class="card-body">
						<h5>Noches pernoctadas</h5>

						<table class="table table-hover">
							<thead>
								<tr>
									<th>N°</th>
									<th>Fecha de inicio</th>
									<th>Fecha de fin</th>
									<th># Noches</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(noche, index) in respuesta">
									<td>{{index+1}}</td>
									<td>{{fechaLargaLatam(noche.entrada)}}</td>
									<td>{{fechaLargaLatam(noche.salida)}}</td>
									<td>{{noche.noches}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div v-if="queReporte==2">
				<div class="card">
					<div class="card-body">
						<h5>Noches pernoctadas entre fechas</h5>

						<table class="table table-hover">
							<thead>
								<tr>
									<th>N°</th>
									<th>Cliente</th>
									<th>Nacionalidad</th>
									<th>Procedencia</th>
									<th>Fecha de inicio</th>
									<th>Fecha de fin</th>
									<th># Noches</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(noche, index) in respuesta">
									<td>{{index+1}}</td>
									<td class="text-capitalize">{{noche.apellidos}} {{ noche.nombres }}</td>
									<td>{{noche.idNacionalidad=='1' ? 'Peruana': 'Extranjera'}}</td>
									<td>{{noche.departamento}}</td>
									<td>{{fechaLargaLatam(noche.entrada)}}</td>
									<td>{{fechaLargaLatam(noche.salida)}}</td>
									<td>{{noche.noches}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div v-if="queReporte==3">
				<div class="card">
					<div class="card-body">
						<h5>Cajas entre fechas</h5>

						<table class="table table-hover">
							<thead>
								<tr>
									<th>N°</th>
									<th>Usuario</th>
									<th>Apertura</th>
									<th>Cierre</th>
									<th>Monto apertura</th>
									<th>Monto de cierre</th>
									<th>Ver</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(caja, index) in respuesta">
									<td>{{index+1}}</td>
									<td class="text-capitalize">{{caja.apellido}} {{ caja.nombres }}</td>
									<td>{{fechaLargaLatam(caja.apertura)}}</td>
									<td>{{fechaLargaLatam(caja.cierre)}}</td>
									<td>{{caja.inicial}}</td>
									<td>{{caja.final}}</td>
									<td>
										<router-link class="btn btn-outline-success" :to="{name:'detalleCaja', params:{idCaja:caja.id}}" >Ver</router-link>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
		
	</div>
</template>
<script>
import moment from 'moment'
export default {
	data() {
		return {
			verExtraTexto:false, verExtraTexto:true, texto:'', queReporte:'3', respuesta:[], cabecera:[], verRespuesta : false, fechas:{inicio:moment().format('YYYY-MM-DD'), fin:moment().format('YYYY-MM-DD')}
		}
	},
	mounted(){
		this.elegirReporte()
	},
	methods: {
		elegirReporte(){
			this.verRespuesta= false
			switch (this.queReporte) {
				case '1': 
					this.verExtraTexto=true; this.verExtraFechas=!this.verExtraTexto;
				break;
				case '2':
				case '3':
					this.verExtraTexto=false; this.verExtraFechas=!this.verExtraTexto;
				break;
				default:break;
			}
		},
		pedirReporte(){
			let datos = new FormData()
			datos.append('idReporte', this.queReporte)
			datos.append('texto', this.texto)
			datos.append('fechas', JSON.stringify(this.fechas))
			fetch(this.servidor+'Reportes.php',{
				method:'POST', body:datos
			})
			.then(serv => serv.json() )
			.then(resp => {
				this.verRespuesta=true
				switch(this.queReporte){
					case '1': 
						this.cabecera = resp.cliente;
						this.respuesta = resp.noches;
					break;
					case '2': this.respuesta = resp.noches; break;
					case '3': this.respuesta = resp.filas; break;

				}
			})
		},
		fechaLatam(fecha){
			if(fecha) return moment(fecha, 'YYYY-MM-DD HH:mm').format('DD/MM/YYYY') 
		},
		fechaLargaLatam(fecha){
			if(fecha) return moment(fecha, 'YYYY-MM-DD HH:mm').format('DD/MM/YYYY h:mm a') 
		},
		calcularEdad(fechaNacimiento) {
			if(!fechaNacimiento) return 0
			const hoy = moment(); // Obtiene la fecha y hora actual
			const nacimiento = moment(fechaNacimiento, "YYYY-MM-DD"); // Convierte la fecha de nacimiento a un objeto Moment

			const anos = hoy.diff(nacimiento, 'years');
			let edad = anos;

			// Ajusta la edad si el cumpleaños aún no ha ocurrido en el año actual
			if (hoy.isBefore(nacimiento.add(anos, 'years'))) {
				edad--;
			}

			return edad;
		}
	},
}
</script>