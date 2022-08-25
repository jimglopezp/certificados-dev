<?php

class Plantilla_PDF_Remisibilidad extends TCPDF {

    public function Footer() {
        $Pie = '
    <div align="center">
        <br><b>Ministerio de Trabajo</b><br>
        <b>SERVICIO NACIONAL DE APRENDIZAJE</b><br>
        <b>Direccion General - (Direccion de Promocion y Relaciones Corporativas)</b><br>
        <b>Calle 57 No. 8-69 - PBX (57 1) 5461500</b><br>
        <b>www.sena.edu.co - Linea Gratuita Nacional: 01 8000 9 10 270</b><br>
    </div>';
        $this->SetY(-25);
        $this->SetFont('helvetica', 'N', 8);
        $this->Cell(0, 5, $this->writeHTML($Pie, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
