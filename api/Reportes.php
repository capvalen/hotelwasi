<?php
include 'conectkarl.php';


switch($_POST['idReporte']){
	case '1': pernoctacionesXCliente($datab); break;
	case '2': pernoctacionesXFecha($datab); break;
	case '3': cajasXFecha($datab); break;
}

function pernoctacionesXCliente($db){
	$noches = []; $cliente=[];
	$sqlCliente = $db->prepare("SELECT c.*, d.departamento FROM `clientes` c
	inner join departamentos d on d.id = c.procedencia
	where dni=? and activo = 1 limit 1;");
	$sqlCliente->execute([$_POST['texto']]);
	if($sqlCliente->rowCount()>0){
		$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);
		$idCliente = 	$cliente['id'];
	}else $idCliente = -1;
	
	$sqlNoches = $db->prepare("SELECT *, GREATEST(datediff(entrada, salida),1) as noches FROM `registro` where idCliente = ? and estado = 5 and activo = 1 order by entrada desc;");
	$sqlNoches->execute([$idCliente]);
	while($rowNoches = $sqlNoches->fetch(PDO::FETCH_ASSOC)){
		$noches [] = $rowNoches;
	}
	
	echo json_encode( array('cliente' => $cliente, 'noches' => $noches ));
}

function pernoctacionesXFecha($db){
	$noches = [];
	$fecha=json_decode($_POST['fechas'], true);
	$sqlNoches = $db->prepare("SELECT r.*, c.apellidos, c.nombres, GREATEST(datediff(entrada, salida),1) as noches, c.idNacionalidad, d.departamento
	FROM `registro` r 
	inner join clientes c on c.id = r.idCliente
	inner join departamentos d on d.id = c.procedencia
	where ( DATE_FORMAT(entrada,'%Y-%m-%d') between ? and ? or
	DATE_FORMAT(salida,'%Y-%m-%d') between ? and ?) and estado = 5 and r.activo = 1 order by entrada desc;");
	$sqlNoches->execute([ $fecha['inicio'], $fecha['fin'], $fecha['inicio'], $fecha['fin'] ]);
	while($rowNoches = $sqlNoches->fetch(PDO::FETCH_ASSOC)){
		$noches [] = $rowNoches;
	}
	
	echo json_encode( array('noches' => $noches ));
}

function cajasXFecha($db){
	$filas = [];
	$fecha=json_decode($_POST['fechas'], true);
	$sql = $db->prepare("SELECT c.*, u.nombres, u.apellido FROM `caja` c
	inner join usuario u on u.id = c.idUsuario
	and c.activo = 1 and
	DATE_FORMAT(apertura,'%Y-%m-%d') between ? and ?;");
	$sql->execute([ $fecha['inicio'], $fecha['fin'] ]);
	while($row = $sql->fetch(PDO::FETCH_ASSOC)){
		$filas [] = $row;
	}
	
	echo json_encode( array('filas' => $filas ));
}