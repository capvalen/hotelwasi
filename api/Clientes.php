<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'crear': crear($datab); break;
	case 'listar': listar($datab); break;
	case 'actualizar': actualizar($datab); break;
	case 'buscar': buscar($datab); break;
	case 'buscarDNI': buscarDNI($datab); break;
	case 'buscarPorID': buscarPorID($datab); break;
	case 'eliminar': eliminar($datab); break;
}


function crear($db){
	$campo = json_decode($_POST['cliente'], true);
	$sql = $db->prepare("INSERT INTO `clientes`(
		`dni`, `nombres`, `apellidos`, `direccion`, `celular`,
		`correo`, `idNacionalidad`, `procedencia`, `observaciones`) VALUES
		(?, ?, ?, ?, ?,
		?, ?, ?, ?)");
	if($sql->execute([
		$campo['dni'], $campo['nombres'], $campo['apellidos'], $campo['direccion'], $campo['celular'],
		$campo['correo'], $campo['idNacionalidad'], $campo['procedencia'], $campo['observaciones']
		])){
		$idNuevo = $db->lastInsertId();
		
		echo json_encode( array('id' => $idNuevo, 'mensaje' => 'ok'));
	}else{
		echo 'error';
	}
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT c.*, d.departamento FROM `clientes` c
	inner join departamentos d on d.id =  c.procedencia 
	where c.id <> 1 and c.activo = 1
	order by id desc limit 50;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('clientes' => $filas, 'mensaje' => 'ok'));
	}
}

function actualizar($db){
	$cliente = json_decode($_POST['cliente'], true);
	$sql = $db->prepare("UPDATE `clientes` SET 
	`dni`=?,`nombres`=?,`apellidos`=?,`direccion`=?,`celular`=?,
	`correo`=?,`idNacionalidad`=?,`procedencia`=?,`observaciones`=? WHERE `id`=?;");
	if($sql->execute([
		$cliente['dni'], $cliente['nombres'], $cliente['apellidos'], $cliente['direccion'], $cliente['celular'], 
		$cliente['correo'], $cliente['idNacionalidad'], $cliente['procedencia'], $cliente['observaciones'], $cliente['id']
	])){
		echo json_encode( array('mensaje' => 'ok'));
	}
}

function buscar($db){
	$filas = [];
	$sql = $db->prepare("SELECT c.*, d.departamento FROM `clientes` c
	inner join departamentos d on d.id =  c.procedencia 
	where c.id <> 1 and c.activo = 1 and ( c.apellidos like ? or c.nombres like ? or dni = ? or celular = ? )
	order by id desc limit 50;");
	if($sql->execute([
		$_POST['texto'].'%', $_POST['texto'].'%', $_POST['texto'], $_POST['texto']
	])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('clientes' => $filas, 'mensaje' => 'ok'));
	}
}

function buscarDNI($db){
	$filas = [];
	$sql = $db->prepare("SELECT c.*, d.departamento FROM `clientes` c
	inner join departamentos d on d.id =  c.procedencia 
	where c.activo = 1 and dni = ? limit 1;");
	if($sql->execute([
		$_POST['dni']
	])){
		$filas = $sql->fetch(PDO::FETCH_ASSOC);
		echo json_encode( array('cliente' => $filas, 'mensaje' => 'ok'));
	}
}

function buscarPorID($db){
	$filas = [];
	$sql = $db->prepare("SELECT c.*, d.departamento FROM `clientes` c
	inner join departamentos d on d.id =  c.procedencia 
	where c.id = ?;");
	if($sql->execute([
		$_POST['idCliente']
	])){
		$filas = $sql->fetch(PDO::FETCH_ASSOC);
		echo json_encode( array('cliente' => $filas, 'mensaje' => 'ok'));
	}
}

function eliminar($db){
	$sql = $db->prepare("UPDATE `clientes` SET `activo` = '0' WHERE `id` = ?;");
	if($sql->execute([ $_POST['idCliente'] ])){
		echo json_encode( array('mensaje' => 'eliminado ok'));
	}
}
?>