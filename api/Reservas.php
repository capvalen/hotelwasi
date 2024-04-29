<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'registrar': registrar($datab); break;
	case 'verificarHorario': verificarHorario($datab); break;
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
				`correo`, `idNacionalidad`, `procedencia`, `observaciones`) VALUES
				(?, ?, ?, ?, ?,
				?, ?, ?, ?)");
			if($sql->execute([
				$campo['dni'], $campo['nombres'], $campo['apellidos'], $campo['direccion'], $campo['celular'],
				$campo['correo'], $campo['idNacionalidad'], $campo['procedencia'], $campo['observaciones']
				])){
				$idCliente = $db->lastInsertId();
			}else{
				echo 'error';
			}
		}else{
			$sql = $db->prepare("UPDATE `clientes` SET 
			`nombres`=?,`apellidos`=?,`direccion`=?,`celular`=?,
			`correo`=?,`idNacionalidad`=?,`procedencia`=?,`observaciones`=? WHERE `dni`=?;");
			if($sql->execute([
				$cliente['nombres'], $cliente['apellidos'], $cliente['direccion'], $cliente['celular'], 
				$cliente['correo'], $cliente['idNacionalidad'], $cliente['procedencia'], $cliente['observaciones'], $cliente['dni']
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
			`adelanto`, `pagar`, `tipoAtencion`, `tipoReserva`) VALUES
				(?, ?, ?, ?, ?,
				?, ?, ?, ?);");
		$sqlReserva->execute([
			$idCliente, $reserva['inicio'].' '.$reserva['horaInicio'], $reserva['fin'].' '.$reserva['horaFin'], $reserva['idHabitacion'], $reserva['parcial'],
			$reserva['adelanto'], $reserva['pagar'], $reserva['tipoAtencion'], $reserva['tipoReserva'],
		]);
		$idReserva = $db->lastInsertId();
		//echo $sqlReserva->debugDumpParams();

		$sqlHabitacion = $db->prepare("UPDATE habitaciones set estado = 2 where id = ?;");
		$sqlHabitacion->execute([ $reserva['idHabitacion'] ]);

		$sqlRegistro = $db->prepare("INSERT INTO registro (
			`idReserva`, `idCliente`, `entrada`, `salida`, `estado`,`idHabitacion`, `tipoAtencion`, `tipoReserva`) values
			(?, ?, ?, ?, 2, ?, ?, ?);");
		$sqlRegistro -> execute([
			$idReserva, $idCliente, $reserva['inicio'] .' '.$reserva['horaInicio'], $reserva['fin'] .' '.$reserva['horaFin'], $reserva['idHabitacion'], $reserva['tipoAtencion'], $reserva['tipoReserva']
		]);
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