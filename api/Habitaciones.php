<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'basicos': basicos($datab); break;
	case 'crear': crear($datab); break;
	case 'solicitar': solicitar($datab); break;
	
}

function basicos($db){
	$tipos= [];
	$sqlTipos = $db-> prepare("SELECT * FROM `tipoHabitacion` where activo = 1;");
	if($sqlTipos->execute())
		while($rowTipos = $sqlTipos-> fetch(PDO::FETCH_ASSOC))
			$tipos[] = $rowTipos;
	echo json_encode( array( 'tipos' => $tipos ));
}

function crear($db){
	$campo = json_decode($_POST['habitacion'], true);
	//var_dump($campo['tipo']); die();
	$sql = $db->prepare("INSERT INTO `habitaciones`(
		`tipo`, `numero`, `detalle`, `precioPublico`, `precioRebaja`, 
		`precioEspecial`, `nivel`) VALUES (
		?, ?, ?, ?, ?,
		?, ?);");
		if($sql->execute([
			$campo['tipo'], $campo['numero'], $campo['detalle'], $campo['precioPublico'], $campo['precioRebaja'],
			$campo['precioEspecial'], $campo['nivel']
		])){
			$idNuevo = $db->lastInsertId();
			
			echo json_encode( array('id' => $idNuevo, 'mensaje' => 'ok'));
		}else{
			echo 'error';
		}
}

function solicitar($db){
	$piso=0; $pisos=[];
	$filas = [];
	$sql = $db->prepare("SELECT h.*, th.habitacion as tipoCuarto, e.estado as estadoCuarto  FROM `habitaciones` h
	inner join estadoHabitacion e on e.id = h.estado
	inner join tipoHabitacion th on th.id = h.tipo
	where h.activo = 1 order by nivel, numero asc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC)){
			$filas[] = $rows;
			if($piso<>$rows['nivel']){
				$pisos[] = $rows['nivel'];
				$piso =$rows['nivel'];
			}
		}
		echo json_encode( array('habitaciones' => $filas, 'pisos' => $pisos, 'mensaje' => 'ok'));
	}
}
