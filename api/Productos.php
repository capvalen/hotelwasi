<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'buscarProducto': buscarProducto($datab); break;
	case 'agregarCesta': agregarCesta($datab); break;
	case 'sumarProducto': sumarProducto($datab); break;
	case 'listar': listar($datab); break;
	case 'crear': crear($datab); break;
	case 'eliminar': eliminar($datab); break;
	case 'borrar': borrar($datab); break;
	case 'modificarStock': modificarStock($datab); break;
	case 'verMovimientos': verMovimientos($datab); break;
}

function buscarProducto($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM productos where producto like ? and activo = 1;");
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

		$sqlProducto = $db->prepare("UPDATE productos set stock = stock - 1 where ?;");
		$sqlProducto ->execute([ $_POST['idProducto'] ]);

		$sqlKardex = $db->prepare("INSERT INTO kardex (idProducto, cantidad, idMovimiento) values( ?,1,3 )");
		$sqlKardex ->execute([ $_POST['idProducto'] ]);

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

		if($_POST['antes']<$_POST['cantidad']){ //vendiendo 5<9
			$diferencia = $_POST['cantidad'] - $_POST['antes'];
			$sqlProducto = $db->prepare("UPDATE productos set stock = stock - ? where id = ?;");
			$sqlProducto ->execute([ $diferencia , $_POST['idProducto'] ]);
		}else{ //devolucion 9>5
			$diferencia = $_POST['antes'] - $_POST['cantidad']; 
			$sqlProducto = $db->prepare("UPDATE productos set stock = stock + ? where id = ?;");
			$sqlProducto ->execute([ $diferencia , $_POST['idProducto'] ]);

			$sqlKardex = $db->prepare("INSERT INTO kardex (idProducto, cantidad, idMovimiento) values( ?,?,3 )");
			$sqlKardex ->execute([ $_POST['idProducto'], $diferencia ]);
		}
		echo 'ok';
	}else echo 'error';
}

function eliminar($db){
	$total = $_POST['total'] - $_POST['cantidad']*$_POST['precio'];
	$sql = $db->prepare("UPDATE productosReserva SET activo = 0 where id = ?;");
	if($sql->execute([ $_POST['id'] ])){
		$sqlActualizar = $db->prepare("UPDATE reservas set productos = ? where id = ?; ");
		$sqlActualizar->execute([ $total, $_POST['idReservacion'] ]);

		$sqlProducto = $db->prepare("UPDATE productos set stock = stock + ? where id= ?;");
		$sqlProducto ->execute([ $_POST['cantidad'], $_POST['idProducto'] ]);

		$sqlKardex = $db->prepare("INSERT INTO kardex (idProducto, cantidad, idMovimiento) values( ?,1,4 )");
		$sqlKardex ->execute([ $_POST['idProducto'], $_POST['cantidad'] ]);
		echo 'ok';
	}else echo 'error';
}

function listar($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM productos where activo = 1 order by producto asc;");
	if($sql->execute()){
		while($rows = $sql->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rows;
		echo json_encode( array('productos' => $filas, 'mensaje' => 'ok'));
	}
}

function crear($db){
	$producto = json_decode($_POST['nuevo'], true);
	$sql = $db->prepare("INSERT INTO productos (producto, precio, stock) values(?, ?, ?);");
	if($sql->execute([ $producto['nombre'], $producto['precio'], $producto['stock'] ])){
		$idProducto = $db->lastInsertId();

		if( $producto['stock']<>0 ){
			$sqlKardex = $db->prepare("INSERT INTO kardex (idProducto, cantidad, idMovimiento) values( ?,?,? )");
			$sqlKardex ->execute([ $idProducto, $producto['stock'], $producto['movimiento'] ]);
		}

		echo json_encode( array( 'mensaje' => 'ok'));
	}
}

function borrar($db){
	$sqlActualizar = $db->prepare("UPDATE productos set activo = 0 where id = ?; ");
	if($sqlActualizar->execute([ $_POST['idProducto'] ]))
		echo 'ok';
}

function modificarStock($db){
	$producto =  json_decode($_POST['nuevo'], true);
	if($producto['movimiento']==1){
		$sqlActualizar = $db->prepare("UPDATE productos set stock = stock+? where id = ?; ");
		$sqlActualizar->execute([ $producto['stock'], $producto['id'] ]);
	}else{
		$sqlActualizar = $db->prepare("UPDATE productos set stock = stock-? where id = ?; ");
		$sqlActualizar->execute([ $producto['stock'], $producto['id'] ]);
	}
	$sqlKardex = $db->prepare("INSERT INTO kardex (idProducto, cantidad, idMovimiento, observacion) values( ?,?,?,? )");
	$sqlKardex ->execute([ $producto['id'], $producto['stock'], $producto['movimiento'], $producto['observacion']  ]);
	
	echo 'ok';
}

function verMovimientos($db){
	$filas = [];
	$sql = $db->prepare("SELECT * FROM productos where id = ?;");
	if($sql->execute([$_POST['idProducto']])){
		$rows = $sql->fetch(PDO::FETCH_ASSOC);

		$sqlMovimientos = $db->prepare("SELECT * from kardex where idProducto = ? and year(fecha) = ? and activo = 1;");
		$sqlMovimientos -> execute([$_POST['idProducto'], $_POST['año']]);
		while($rowMovimientos = $sqlMovimientos->fetch(PDO::FETCH_ASSOC))
			$filas[] = $rowMovimientos;
		echo json_encode( array('producto' => $rows, 'kardex' => $filas, 'mensaje' => 'ok'));
	}
}
?>