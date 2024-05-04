<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'detalleCaja': detalleCaja($datab); break;
	case 'abrir': abrir($datab); break;
	case 'cerrar': cerrar($datab); break;
	case 'salida': salida($datab); break;
}


function detalleCaja($db){
	$filas = []; $detalles = []; $filtro = '';
	if($_POST['idCaja']<>-1) $filtro = " and id = ".$_POST['idCaja']; //busca caj con id
	else $filtro =  " and enUso =1 "; //busca la caja abierta
	$sql = $db->query("SELECT * FROM caja where activo = 1 {$filtro} limit 1;");
	if($sql->execute()){
		if($sql->rowCount() == 0){
			echo json_encode( array('caja' => 'noCaja', 'cabecera' => $filas, 'detalles' => $detalles ));
		}else{
			$filas = $sql->fetch(PDO::FETCH_ASSOC);
			$sqlDetalles = $db->prepare("SELECT * from cajaDetalle where idCaja = ? and activo = 1;");
			$sqlDetalles->execute([ $filas['id'] ]);
			while($rowDetalles = $sqlDetalles->fetch(PDO::FETCH_ASSOC))
				$detalles[] = $rowDetalles;

			echo json_encode( array('caja'=>'siCaja', 'cabecera' => $filas, 'detalles' => $detalles));
		}
	}
}

function abrir($db){
	$sql = $db->prepare("INSERT INTO `caja`(`apertura`, `inicial`, `idUsuario`) VALUES (?, ?, ?)");
	$sql->execute([ $_POST['apertura'], $_POST['monto'], $_POST['idUsuario']]);
	$_POST['idCaja'] = $db->lastInsertId();
	detalleCaja($db);
}

function cerrar($db){
	$sqlCaja = $db->query("SELECT * FROM caja where enUso =1 and activo = 1;");
	if($sqlCaja->execute()){
		if($sqlCaja->rowCount() == 1){
			$fila = $sqlCaja->fetch(PDO::FETCH_ASSOC);
			$sql = $db->prepare("UPDATE caja SET cierre = ?, final = ?, enUso = 0 where id = ?;");
			if($sql->execute([ $_POST['cierre'], $_POST['monto'], $fila['id'] ])){
				echo 'ok';
			}else echo 'error';
		}else echo 'ok';
	}else echo 'error';
}

function salida($db){
	$sqlCaja = $db->query("SELECT * from caja where enUso=1;");
	if($sqlCaja->execute()){
		if($sqlCaja->rowCount() == 0)
			echo json_encode( array('hayCaja' => 'noCaja' ));
		else{
			$rowCaja = $sqlCaja->fetch(PDO::FETCH_ASSOC);
			$idCaja = $rowCaja['id'];

			$sqlDetalle = $db->prepare("INSERT INTO `cajaDetalle`(
				`idCaja`, `descripcion`, `cantidad`, `monto`, `idReserva`, `idUsuario`, `tipo`) VALUES (
				?, trim(?), 1, ?, ?, ?, detalleCaja2
			)");
			$sqlDetalle->execute([
				$idCaja, $_POST['detalle'], $_POST['monto'], -1, $_POST['idUsuario']
			]);
			echo json_encode( array('hayCaja' => 'siCaja', 'mensaje' => 'ok' ));
		}
	}
}