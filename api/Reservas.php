<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'registrar': registrar($datab); break;
	case 'verificarHorario': verificarHorario($datab); break;
	case 'tomarHabitacion': tomarHabitacion($datab); break;
	case 'filtrarReservaciones': filtrarReservaciones($datab); break;
}

function registrar($db){
	$reserva = json_decode($_POST['reserva'], true);
	$cliente = $reserva['cliente'];
	$sqlCliente = $db->prepare("SELECT * FROM `clientes` where dni = ? and activo = 1;");
	$sqlCliente->execute([ $cliente['dni'] ]);
	$_POST['cliente'] = $cliente;

	if( $cliente['dni'] == '00000000' ){
		$idCliente = 1;
	}else{ //no es el cliente básico
		if($sqlCliente->rowCount() == 0){
			$campo = $cliente; //copiado de Clientes->crear();
			$sql = $db->prepare("INSERT INTO `clientes`(
				`dni`, `nombres`, `apellidos`, `direccion`, `celular`,
				`correo`, `idNacionalidad`, `procedencia`, `observaciones`, `fechaNacimiento`) VALUES
				(?, ?, ?, ?, ?,
				?, ?, ?, ?, ?)");
			if($sql->execute([
				$campo['dni'], $campo['nombres'], $campo['apellidos'], $campo['direccion'], $campo['celular'],
				$campo['correo'], $campo['idNacionalidad'], $campo['procedencia'], $campo['observaciones'], $campo['fechaNacimiento']
				])){
				$idCliente = $db->lastInsertId();
			}else{
				echo 'error';
			}
		}else{
			$sql = $db->prepare("UPDATE `clientes` SET 
			`nombres`=?,`apellidos`=?,`direccion`=?,`celular`=?,
			`correo`=?,`idNacionalidad`=?,`procedencia`=?,`observaciones`=?, fechaNacimiento = ? WHERE `dni`=?;");
			if($sql->execute([
				$cliente['nombres'], $cliente['apellidos'], $cliente['direccion'], $cliente['celular'], 
				$cliente['correo'], $cliente['idNacionalidad'], $cliente['procedencia'], $cliente['observaciones'], 
				$cliente['fechaNacimiento'], $cliente['dni']
			])){
				$idCliente = $cliente['id'];
			}
		}
	}
	
	//Revisamos por ultima vez si la habitacion esta reservada en la fecha elegida
	$sqlComprobar = $db->prepare("SELECT * from registro where idHabitacion =? and ? between entrada and salida and activo = 1 and estado in (2,4);");
	$sqlComprobar ->execute([
		$reserva['idHabitacion'], $reserva['inicio'].' '.$reserva['horaInicio']
	]);
	if( $sqlComprobar->rowCount() == 0){ // El horario esta libre

		$sqlReserva = $db->prepare("INSERT INTO `reservas`(
			`idCliente`, `fechaInicio`, `fechaFin`, `idHabitacion`, `precio`, 
			`adelanto`, `pagar`, `tipoAtencion`, `tipoReserva`, `estado`) VALUES
				(?, ?, ?, ?, ?,
				?, ?, ?, ?, ?);");
		$sqlReserva->execute([
			$idCliente, $reserva['inicio'].' '.$reserva['horaInicio'], $reserva['fin'].' '.$reserva['horaFin'], $reserva['idHabitacion'], $reserva['parcial'],
			$reserva['adelanto'], $reserva['pagar'], $reserva['tipoAtencion'], $reserva['tipoReserva'], $reserva['estado'],
		]);
		$idReserva = $db->lastInsertId();
		//echo $sqlReserva->debugDumpParams();

		if(floatval($reserva['adelanto'])>0){

			$sqlCaja = $db->query("SELECT * from caja where enUso=1;");
			$sqlCaja->execute();
			$rowCaja = $sqlCaja->fetch(PDO::FETCH_ASSOC);
			$idCaja = $rowCaja['id'];

			$descripcion = "Adelanto de alquiler: Habitación N° {$reserva['numero']} {$reserva['tipoCuarto']}";

			$sqlDetalle = $db->prepare("INSERT INTO `cajaDetalle`(
				`idCaja`, `descripcion`, `cantidad`, `monto`, `idReserva`, `idUsuario`, `fecha`) VALUES (
				?, trim(?), 1, ?, ?, ?, CONVERT_TZ(NOW(), @@session.time_zone, 'America/Chicago')
			)");
			$sqlDetalle->execute([
				$idCaja, $descripcion, $reserva['adelanto'], $idReserva, $reserva['idUsuario']
			]);
		}

		if($reserva['estado']=='2'){
			$sqlHabitacion = $db->prepare("UPDATE habitaciones set estado = ? where id = ?;");
			$sqlHabitacion->execute([ $reserva['estado'], $reserva['idHabitacion'] ]);
	
			$sqlRegistro = $db->prepare("INSERT INTO registro (
				`idReserva`, `idCliente`, `entrada`, `salida`, `estado`,`idHabitacion`, `tipoAtencion`, `tipoReserva`) values
				(?, ?, ?, ?, ?, ?, ?, ?);");
			$sqlRegistro -> execute([
				$idReserva, $idCliente, $reserva['inicio'] .' '.$reserva['horaInicio'], $reserva['fin'] .' '.$reserva['horaFin'], $reserva['estado'], $reserva['idHabitacion'], $reserva['tipoAtencion'], $reserva['tipoReserva']
			]);
		}

		echo json_encode( array('cliente' => $idCliente, 'reserva' => $idReserva, 'duplicado' => false ));
	}else
		echo json_encode( array('cliente' => $idCliente, 'duplicado' => true ));
}

function verificarHorario($db){
	$reserva = json_decode($_POST['reserva'], true);

	$sqlComprobar = $db->prepare("SELECT * from registro where idHabitacion =? and ? between entrada and salida and activo = 1 and estado in (2, 4) order by id desc limit 1;");
	$sqlComprobar ->execute([
		$reserva['idHabitacion'], $reserva['inicio'].' '.$reserva['horaInicio']
	]);
	if( $sqlComprobar->rowCount() == 0){
		echo json_encode( array('reserva' => 'libre' ));
	}else{
		$rowComprobar = $sqlComprobar->fetch(PDO::FETCH_ASSOC);
		$sqlCliente = $db->prepare("SELECT * from clientes where id = ?;");
		$sqlCliente->execute([ $rowComprobar['idCliente'] ]);
		$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);
		echo json_encode( array('reserva' => 'ocupado', 'cliente' => $cliente, 'ocupado'=> $rowComprobar ));
	}
}

function tomarHabitacion($db){
	$sql = $db->prepare("UPDATE reservas set estado = 2, tipoReserva = 1 where id = ?;");
	$sql->execute([ $_POST['idReserva'] ]);

	$sqlRegistro = $db->prepare("INSERT INTO registro (
		`idReserva`, `idCliente`, `entrada`, `salida`, `estado`,
		`idHabitacion`, `tipoAtencion`, `tipoReserva`) values
		(?, ?, ?, ?, 2,
		?, 2, 2);");
	$sqlRegistro -> execute([
		$_POST['idReserva'], $_POST['idCliente'], $_POST['entrada'], $_POST['salida'],
		$_POST['idHabitacion']
	]);

	$sqlHabitacion = $db->prepare("UPDATE habitaciones set estado = 2 where id = ?;");
	$sqlHabitacion->execute([ $_POST['idHabitacion'] ]);
	echo json_encode( array('reserva' => 'ocupado'));
}

function filtrarReservaciones($db){
	$filas = [];
	$sql = $db->prepare("SELECT r.*, ta.atencion, e.estado as queEstado, h.numero, c.nombres, c.apellidos FROM `reservas` r
	inner join tipoAtencion ta on ta.id = r.tipoAtencion
	inner join estadoHabitacion e on e.id = r.estado
	inner join habitaciones h on h.id = r.idHabitacion
	inner join clientes c on c.id = r.idCliente
	where tipoReserva = 2 and date_format(r.fechaInicio, '%Y-%m-%d') between ? and ? and r.activo = 1;");
	$sql -> execute([
		$_POST['inicio'], $_POST['fin']
	]);
	while($row = $sql->fetch(PDO::FETCH_ASSOC)){
		$filas [] = $row;
	}
	echo json_encode( array('reservaciones' => $filas));
}