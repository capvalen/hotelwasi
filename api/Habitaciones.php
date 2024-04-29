<?php
include 'conectkarl.php';

switch($_POST['pedir']){
	case 'basicos': basicos($datab); break;
	case 'crear': crear($datab); break;
	case 'solicitar': solicitar($datab); break;
	case 'detalleHabitacion': detalleHabitacion($datab); break;
	case 'detalleOcupado': detalleOcupado($datab); break;
	case 'detalleReserva': detalleReserva($datab); break;
	case 'cobrarHabitacion': cobrarHabitacion($datab); break;
	case 'liberarLimpieza': liberarLimpieza($datab); break;
	case 'actualizarHabitacion': actualizarHabitacion($datab); break;
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
	$filas = []; $reservaciones=[];
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

		//Futuras reservaciones
		$sqlReservas=$db->prepare("SELECT * FROM `reservas` where fechaInicio between CONVERT_TZ(NOW(), @@session.time_zone, 'America/Chicago') - INTERVAL 2 hour and CONVERT_TZ(NOW(), @@session.time_zone, 'America/Chicago') + INTERVAL 5 hour and tipoReserva =2 and tipoAtencion = 2 and activo = 1;");
		$sqlReservas->execute();
		while($rowReservas  = $sqlReservas->fetch(PDO::FETCH_ASSOC))
			$reservaciones[] = $rowReservas;
		echo json_encode( array('habitaciones' => $filas, 'pisos' => $pisos, 'reservaciones'=>$reservaciones, 'mensaje' => 'ok'));
	}
}

function detalleHabitacion($db){
	$filas = [];
	$sql = $db->prepare("SELECT h.*, th.habitacion as tipoCuarto, e.estado as estadoCuarto  FROM `habitaciones` h
	inner join estadoHabitacion e on e.id = h.estado
	inner join tipoHabitacion th on th.id = h.tipo
	where h.activo = 1 and h.id  = ?");
	if($sql->execute([ $_POST['idHabitacion'] ])){
		$filas = $sql->fetch(PDO::FETCH_ASSOC);

		echo json_encode( array('habitacion' => $filas, 'mensaje' => 'ok'));
	}
}

function detalleOcupado($db){
	$fila = []; $cliente=[]; $productos=[];
	$sql = $db->prepare("SELECT r.*, h.numero, h.detalle, th.habitacion as tipoCuarto FROM `reservas` r 
	inner join habitaciones h on h.id = r.idHabitacion
	inner join tipoHabitacion th on th.id = h.tipo
	where idHabitacion = ? and r.estado = 2 limit 1;");
	if($sql->execute([ $_POST['idHabitacion']])){
		$fila = $sql->fetch(PDO::FETCH_ASSOC);
		$sqlCliente =  $db->prepare("SELECT c.*, d.departamento from clientes c
		inner join departamentos d on d.id = c.procedencia where c.id = ?;");
		$sqlCliente->execute([$fila['idCliente']]);
		$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);
		$sqlProductos = $db->prepare("SELECT pr.*, p.producto from productosReserva pr
		inner join productos p on p.id = pr.idProducto
		where idReservacion = ? and pr.activo = 1;");
		$sqlProductos->execute([ $fila['id'] ]);
		while($rowProductos = $sqlProductos->fetch(PDO::FETCH_ASSOC))
			$productos[] = $rowProductos;
	}
	echo json_encode( array('habitacion' => $fila, 'cliente' => $cliente, 'productos'=>$productos, 'mensaje' => 'ok'));
}

function detalleReserva($db){
	$fila = []; $cliente=[]; $productos=[];
	$sql = $db->prepare("SELECT r.*, h.numero, h.detalle, th.habitacion as tipoCuarto FROM `reservas` r 
	inner join habitaciones h on h.id = r.idHabitacion
	inner join tipoHabitacion th on th.id = h.tipo
	where r.id = ?;");
	if($sql->execute([ $_POST['idReserva']])){
		$fila = $sql->fetch(PDO::FETCH_ASSOC);
		$sqlCliente =  $db->prepare("SELECT c.*, d.departamento from clientes c
		inner join departamentos d on d.id = c.procedencia where c.id = ?;");
		$sqlCliente->execute([$fila['idCliente']]);
		$cliente = $sqlCliente->fetch(PDO::FETCH_ASSOC);
		$sqlProductos = $db->prepare("SELECT pr.*, p.producto from productosReserva pr
		inner join productos p on p.id = pr.idProducto
		where idReservacion = ? and pr.activo = 1;");
		$sqlProductos->execute([ $fila['id'] ]);
		while($rowProductos = $sqlProductos->fetch(PDO::FETCH_ASSOC))
			$productos[] = $rowProductos;
	}
	echo json_encode( array('habitacion' => $fila, 'cliente' => $cliente, 'productos'=>$productos, 'mensaje' => 'ok'));
}


function cobrarHabitacion($db){
	$sqlCaja = $db->query("SELECT * from caja where enUso=1;");
	if($sqlCaja->execute()){
		if($sqlCaja->rowCount() == 0)
			echo json_encode( array('hayCaja' => 'noCaja' ));
		else{
			$rowCaja = $sqlCaja->fetch(PDO::FETCH_ASSOC);
			$idCaja = $rowCaja['id'];

			$descripcion = "Alquiler de la habitación N° {$_POST['numero']} {$_POST['tipoCuarto']}";
			if($_POST['cantidadProductos']>0) $descripcion .= " con {$_POST['cantidadProductos']} productos";

			$sqlDetalle = $db->prepare("INSERT INTO `cajaDetalle`(
				`idCaja`, `descripcion`, `cantidad`, `monto`, `idReserva`, `idUsuario`) VALUES (
				?, trim(?), 1, ?, ?, ?
			)");
			$sqlDetalle->execute([
				$idCaja, $descripcion, $_POST['total'], $_POST['idReserva'], $_POST['idUsuario']
			]);

			$sqlRegistro=$db->prepare("UPDATE registro SET tipoAtencion = 6, estado = 5 WHERE idReserva = ?;");
			$sqlRegistro ->execute([ $_POST['idReserva'] ]);
			
			$sqlReservas = $db->prepare("UPDATE reservas SET estado = 3, tipoAtencion=6 WHERE id=?;");
			$sqlReservas ->execute([ $_POST['idReserva'] ]);

			$sqlHabitacion = $db->prepare("UPDATE habitaciones SET estado = 3 WHERE id = ?;");
			$sqlHabitacion -> execute([ $_POST['idHabitacion'] ]);

			echo json_encode( array('hayCaja' => 'siCaja', 'mensaje' => 'ok' ));

		}
	}
}

function liberarLimpieza($db){
	$sql = $db->prepare("UPDATE habitaciones SET estado = 1 where id=?; ");
	if($sql->execute([ $_POST['idHabitacion'] ])){
		echo 'ok';
	}else echo 'error';
}

function actualizarHabitacion($db){
	$habitacion = json_decode($_POST['habitacion'], true);
	$sql = $db->prepare("UPDATE habitaciones SET 
	`tipo`=?,`numero`=?,`detalle`=?,`precioPublico`=?,`precioRebaja`=?,
	`precioEspecial`=?,`nivel`=? where id=?;");
	if($sql->execute([
		$habitacion['tipo'], $habitacion['numero'], $habitacion['detalle'], $habitacion['precioPublico'], $habitacion['precioRebaja'],
		$habitacion['precioEspecial'], $habitacion['nivel'], $habitacion['id']
		])){
		echo 'ok';
	}else echo 'error';
}