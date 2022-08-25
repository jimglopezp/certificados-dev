<?php

class Plantilla_OficioRemisorio extends TCPDF {

    public $Regional;

    public function Header() {
        $this->SetAutoPageBreak(false, 0);
        // Print of the logo

        $margen = base_url() . 'img/margenes.png';

        $this->Image($margen, 0, 0, 210, 300, '', '', '', false, 5, '', false, false, 0);
        $this->SetAutoPageBreak(true, 40);
        $Cabeza = '
            <table width="100%" border="0">
            <tr>
              <td><div align="center"><img src="' . base_url('img/Logotipo_SENA.png') . '" width="100" height="100" /></div></td>
            </tr>
            <tr>
              <td><div align="center">Regional ' . $this->Regional . '</div></td>
            </tr>
          </table>';
        $this->SetY(15);
        $this->SetFont('helvetica', 'N', 8);
        $this->Cell(0, -15, $this->writeHTML($Cabeza, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function Footer() {
        $Pie = '
    <div align="center">
        <br><b>SENA, Más Trabajo</b><br>
        <b>Ministerio de Trabajo</b><br>
        <b>SERVICIO NACIONAL DE APRENDIZAJE</b><br>
        <b>Regional ' . $this->Regional . ' - Jurisdicción Coactiva</b><br>
        <b>www.sena.edu.co - Línea gratuita nacional: 01 8000 9 10 270<b/><br>
    </div>';
        $this->SetY(-30);
        $this->SetFont('helvetica', 'N', 8);
        $this->Cell(0, -15, $this->writeHTML($Pie, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
