<?php
require('fpdf/fpdf.php');
require('fpdf/font/arial.php'); // Reemplaza "arial-narrow.php" con el nombre real del archivo generado
require('fpdf/font/arial_narrow.php'); // Reemplaza "arial-narrow.php" con el nombre real del archivo generado


$pdf = new FPDF('P','mm',array(90,150));
$pdf->AddPage();
$pdf->SetMargins(2, 0, 5);
$pdf->AddFont('ArialNarrow','','arial_narrow.php'); // Nombre lógico de la fuente
$pdf->AddFont('Arial','','arial.php'); // Nombre lógico de la fuente 


//Cabecera
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(0,6.5, utf8_decode("Hostal Tambo Wasi"), 0, 'C'); $pdf->Ln();


$pdf->Output();