<template>
	<h1>Reservaciones</h1>
	<div class="row mb-2">
		<div class="col">
			<router-link to="/habitaciones" class="btn btn-outline-primary border-0"><i class="bi bi-arrow-bar-left"></i> Ver habitaciones</router-link>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-lg-2 mb-2">
			<div class="card">
				<div class="card-body text-center">
					<div class=""><img src="@/assets/bed.png" style="width: 64px; margin: 0 auto;" ></div>
					<p class="mb-0"><small>Habitación {{ habitacion.detalle }}</small></p>
					<p class="fs-1 mb-0">{{habitacion.numero}}</p>
					<p class="mb-0"><small>{{habitacion.tipoCuarto}}</small></p>
				</div>
			</div>
			<div class="mt-2">
				<p><strong>Tabla de Precios:</strong></p>
				<ul class="list-group">
					<li class="list-group-item">Público<br>
						<span class="ms-2">S/ {{ parseFloat(habitacion.precioPublico).toFixed(2) }}</span>
					</li>
					<li class="list-group-item">Rebaja<br>
						<span class="ms-2">S/ {{ parseFloat(habitacion.precioRebaja).toFixed(2) }}</span>
					</li>
					<li class="list-group-item">Especial<br>
						<span class="ms-2">S/ {{ parseFloat(habitacion.precioEspecial).toFixed(2) }}</span>
					</li>
				</ul>
			</div>
		</div>
		
			<div class="col-12 col-lg-10 col-lg-8">
				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title ">Detalle de la reserva</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">Fecha de inicio</label>
								<input type="date" class="form-control" v-model="reserva.inicio" @change="verificarHorario()" :min="hoy">
							</div>
							<div class="col">
								<label for="">Hora de inicio</label>
								<input type="time" class="form-control" v-model="reserva.horaInicio" @change="verificarHorario()">
							</div>
							<div class="col">
								<label for="">Fecha de salida</label>
								<input type="date" class="form-control" v-model="reserva.fin" @change="verificarHorario()">
							</div>
							<div class="col">
								<label for="">Hora de salida</label>
								<input type="time" class="form-control" v-model="reserva.horaFin" @change="verificarHorario()">
							</div>
							<div class="col">
								<label for="">Precio acordaro</label>
								<select class="form-select" id="" @change="cambiarPrecio()" v-model="reserva.precioAcordado">
									<option value="1">Público</option>
									<option value="2">Rebaja</option>
									<option value="3">Especial</option>
								</select>
							</div>
							<div class="col">
								<label for=""><strong>A pagar:</strong></label>
								<p>S/ {{parseFloat(reserva.parcial).toFixed(2)}}</p>
							</div>
							<div class="col">
								<label for="">Adelanto (S/)</label>
								<input type="number" class="form-control" min="0" step="1" v-model="reserva.adelanto" v-change="calcularAdelanto()">
							</div>
							<div class="col">
								<label for=""><strong>A pagar:</strong></label>
								<p>S/ {{parseFloat(reserva.pagar).toFixed(2)}}</p>
							</div>
						</div>
					</div>
				</div>

				<div class="card mb-2 " :class="{'border-danger': reservado.id}" v-if="reservado.id">
					<div class="card-body">
						<h5 class="card-title text-danger">Reservaciones ya realizadas</h5>
						<p class="mb-0">
							Cliente: <span>{{ reservado.cliente.apellidos }} {{ reservado.cliente.nombres }}</span>
						</p>
						<p class="mb-0">Fecha de entrada: {{ horaLatam(reservado.entrada) }}</p>
						<p class="mb-0">Fecha de salida: {{ horaLatam(reservado.salida) }}</p>
					</div>
				</div>

				<div class="card mb-2">
					<div class="card-body">
						<h5 class="card-title">Detalle del cliente</h5>
						<div class="row row-cols-md-2">
							<div class="col">
								<label for="">DNI del Cliente  <router-link class="badge text-bg-primary puntero" to="/clientes/nuevo" target="_blank"><i class="bi bi-plus-lg"></i></router-link></label>
								<div class="input-group ">
									<input type="text" class="form-control" v-model="reserva.cliente.dni" @blur="buscarDNI()" @keypress.enter = "buscarDNI()">
									<button class="btn btn-outline-secondary" type="button" @click="buscarDNI()"><i class="bi bi-search"></i></button>
								</div>
							</div>
							<div class="col">
								<label for="">Nacionalidad</label>
								<select class="form-select" @change="cambiarProdecencia()" v-model="reserva.cliente.idNacionalidad">
									<option value="1">Peruano</option>
									<option value="2">Extranjero</option>
								</select>
							</div>
							<div class="col">
								<label for="">Apellidos</label>
								<input type="text" class="form-control text-capitalize" v-model="reserva.cliente.apellidos">
							</div>
							<div class="col">
								<label for="">Nombres</label>
								<input type="text" class="form-control text-capitalize" v-model="reserva.cliente.nombres">
							</div>
							<div class="col">
								<label for="">Procedencia</label>
								<select class="form-select" v-model="reserva.cliente.procedencia" :disabled="!activarDepartamento">
									<option v-for="departamento in departamentos" :value="departamento.id">{{ departamento.departamento }}</option>
								</select>
							</div>
							<div class="col">
								<label for="">Celular</label>
								<input type="text" class="form-control" v-model="reserva.cliente.celular">
							</div>
						</div>
					</div>

				</div>
				<div class="row my-3">
					<div class="d-grid col-6 mx-auto" v-if="!reservado.id && !fechaAnterior">
						<button class="btn btn-outline-primary btn-lg" @click="reservar()"><i class="bi bi-asterisk"></i> Registrar</button>
					</div>
					<div v-else-if="reservado">
						<p class="text-danger text-center"><strong><i class="bi bi-exclamation-triangle"></i> El horario se encuentra ocupado, seleccione otra fecha</strong></p>
					</div>
					<div v-else-if="fechaAnterior">
						<p class="text-danger text-center"><strong><i class="bi bi-exclamation-triangle"></i> No se acepta horarios pasados</strong></p>
					</div>
				</div>
			</div>
		
	</div>
</template>
<script>
import moment from 'moment'
export default{
	data(){ return {
		habitacion:[], departamentos:[], activarDepartamento:true, reservado:{cliente:[]}, fechaAnterior:false, hoy: moment().format('YYYY-MM-DD'),
		reserva:{
			inicio: moment().format('YYYY-MM-DD'), horaInicio: moment().format('HH:mm'),
			fin: moment().format('YYYY-MM-DD'), horaFin: moment().add(3, 'hours').format('HH:mm'), tipoAtencion:7, tipoReserva:1, idHabitacion: this.$route.params.idHabitacion,
			adelanto:0, pagar:0, parcial:0, precioAcordado:1,
			cliente:{
				id:1, dni: '00000000', nombres: 'Cliente simple', apellidos:'-', idNacionalidad:1, procedencia:1, direccion:'', celular:'', correo:'', observaciones:''
			}
		}
	}},
	mounted(){
		this.cargarDatos()
		this.verificarHorario()
	},
	methods: {
		async cargarDatos(){
			let datos = new FormData()
			datos.append('pedir', 'detalleHabitacion');
			datos.append('idHabitacion', this.$route.params.idHabitacion);
			let serv = await fetch(this.servidor+'Habitaciones.php',{
				method:'POST', body: datos
			})
			const temp = await serv.json()
			this.habitacion = temp.habitacion
			this.reserva.parcial = this.habitacion.precioPublico

			datos.set('pedir', 'listar')
			
			let servDepartamentos = await fetch(this.servidor+'Departamentos.php',{
				method:'POST', body: datos
			})
			const tempDepartamentos = await servDepartamentos.json()
			this.departamentos = tempDepartamentos.departamentos
		},
		calcularAdelanto(){
			if(this.reserva.adelanto<0) this.reserva.adelanto=0
			this.cambiarPrecio()
		},
		cambiarPrecio(){
			let parcial =0
			switch (this.reserva.precioAcordado) {
				case '1': case 1: parcial = this.habitacion.precioPublico; break;
				case '2': case 2: parcial = this.habitacion.precioRebaja; break;
				case '3': case 3: parcial = this.habitacion.precioEspecial; break;
				default: break;
			}
			this.reserva.parcial = parcial;
			if(parcial <= 0){
				this.reserva.pagar=0;
				this.reserva.tipoAtencion=7 //pago pendiente
			}
			else{
				this.reserva.pagar = parcial - this.reserva.adelanto
				this.reserva.tipoAtencion=7 //pago parcial
				if( this.reserva.pagar == 0) this.reserva.tipoAtencion=6 //pagado
			}
		},
		cambiarProdecencia(){
			if(this.reserva.cliente.idNacionalidad==1) this.activarDepartamento=true
			else{
				this.reserva.cliente.procedencia = 1
				this.activarDepartamento=false
			}
		},
		horaLatam(hora){
			if(hora) return moment(hora, 'YYYY-MM-DD HH:mm').format('h:mm a [del] DD/MM/YYYY ')
		},
		async reservar(){
			let datos = new FormData()
			datos.append('pedir', 'registrar');
			datos.append('reserva', JSON.stringify(this.reserva));
			let serv = await fetch(this.servidor+'Reservas.php',{
				method:'POST', body: datos
			})
			const respuesta = await serv.json()
			if(!respuesta.duplicado){
				alertify.message(`Habitación ${this.habitacion.numero} tomada con éxito`)
				this.$router.push('/habitaciones')
			}else
				alertify.error('La habitación ya está reservada en ese horario, inténtelo de nuevo')
		},
		async buscarDNI(){
			let datos = new FormData()
			datos.append('pedir', 'buscarDNI');
			datos.append('dni', this.reserva.cliente.dni);
			let serv = await fetch(this.servidor+'Clientes.php',{
				method:'POST', body: datos
			})
			let temp = await serv.json()
			
			if(temp.cliente) this.reserva.cliente = temp.cliente
			else{
				alertify.error('DNI nuevo')
				this.reserva.cliente.id = ''
				//this.reserva.cliente.dni = '00000000'
				this.reserva.cliente.nombres = ''
				this.reserva.cliente.apellidos = ''
				this.reserva.cliente.idNacionalidad = 1
				this.reserva.cliente.procedencia = 1
				this.reserva.cliente.celular = ''
			}
		},
		async verificarHorario(){
			if( this.reserva.inicio > this.reserva.fin) this.reserva.fin = this.reserva.inicio
			//if( this.reserva.horaInicio > this.reserva.horaFin) this.reserva.horaFin = moment(this.reserva.inicio, 'HH:mm').add(3, 'hours').format('HH:mm')
			
			const ahora = moment()
			const inicio = moment(this.reserva.inicio + ' '+this.reserva.horaInicio)

			const diferencia = inicio.diff(ahora, 'minutes')
			this.reservado = []
			
			if(diferencia <0 ){
				this.fechaAnterior = true
				alertify.error('No se aceptan fechas anteriores')
			}else{
				this.fechaAnterior=false
				if(!this.reserva.inicio || !this.reserva.horaInicio || !this.reserva.fin || !this.reserva.horaFin )
					alertify('Rellene todos los campos de hora y fecha')
				else{
					let datos = new FormData()
					datos.append('pedir', 'verificarHorario');
					datos.append('reserva', JSON.stringify(this.reserva));
					let serv = await fetch(this.servidor+'Reservas.php',{
						method:'POST', body: datos
					})
					const respuesta = await serv.json()
					if( respuesta.reserva == 'libre'){
						alertify.message('Horario libre', 3)
						this.reservado  = []
					}
					else{
						this.reservado = respuesta.ocupado
						this.reservado.cliente = respuesta.cliente
						alertify.error('El horario está ocupado')
					}
				}
			}
		}
	},
}
</script>

<style scoped>
.card-title{
	font-size: 0.8rem;
	font-weight: bold;
}
label{
	margin-top:1rem;
}
</style>