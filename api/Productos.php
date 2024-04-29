<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'buscarProducto': buscarProducto($datab); break;
	case 'agregarCesta': agregarCesta($datab); break;
	case 'sumarProducto': sumarProducto($datab); break;
	case 'eliminar': eliminar($datab); break;
}

function buscarProducto($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM productos where producto like ?;");
	if($sql->execute([ $_POST['texto'].'%' ])){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('productos' => $filas, 'mensaje' => 'ok'));
	}
}

function agregarCesta($db){
	$sql =$db->prepare("INSERT INTO `productosReserva`(`idReservacion`, `idProducto`, `cantidad`, `precio`) VALUES ( ?, ?, 1, ? )");
	if($sql->execute([ $_POST['idReservacion'], $_POST['idProducto'], $_POST['precio'] ])){
		$sqlActualizar=$db->prepare("UPDATE reservas set productos = productos + ? where id = ?; ");
		$sqlActualizar->execute([ $_POST['precio'], $_POST['idReservacion'] ]);
		echo 'ok';
	}else{
		echo 'error';
	}
}

function sumarProducto($db){
	$sql = $db->prepare("UPDATE productosReserva SET cantidad = ? where id = ?;");
	if($sql->execute([ $_POST['cantidad'], $_POST['id'] ])){
		$sqlActualizar = $db->prepare("UPDATE reservas set productos = ? where id = ?; ");
		$sqlActualizar->execute([ $_POST['total'], $_POST['idReservacion'] ]);
		echo 'ok';
	}else echo 'error';
}

function eliminar($db){
	$total = $_POST['total'] - $_POST['cantidad']*$_POST['precio'];
	$sql = $db->prepare("UPDATE productosReserva SET activo = 0 where id = ?;");
	if($sql->execute([ $_POST['id'] ])){
		$sqlActualizar = $db->prepare("UPDATE reservas set productos = ? where id = ?; ");
		$sqlActualizar->execute([ $total, $_POST['idReservacion'] ]);
		echo 'ok';
	}else echo 'error';
}
?>