<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'crear': crear($datab); break;
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
?>