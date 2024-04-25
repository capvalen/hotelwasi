<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'listar': listar($datab); break;
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM departamentos;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('departamentos' => $filas, 'mensaje' => 'ok'));
	}
}
?>