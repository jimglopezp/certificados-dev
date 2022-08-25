<?php

class Plantilla_DocJuridica extends TCPDF {

    public $Regional;
    public $Direccion;
    public $Telefono;
    public $Tipo;
    public $Nombre;

    public function Header() {
        switch ($this->Tipo) {
            case 1:
                $margen = base_url() . 'img/margenes.png';
                $this->SetAutoPageBreak(false, 0);
               // $this->Image($margen, 0, 0, 210, 300, '', '', '', false, 5, '', false, false, 0);
                $this->SetAutoPageBreak(true, 40);
                $Cabeza = '
                  <table width="100%" border="0">
                    <tr>
                <td width="28%" rowspan="3"><div align="center"><img src="' . base_url('img/Logotipo_SENA.png') . '" width="100" height="100" /></div></td>
                      <td width="72%" height="27"></td>
                    </tr>
                    <tr>
                      <td height="27"><H3>RESOLUCIÓN NO. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; DE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</H3></td>
                    </tr>
                    <tr>
                      <td width="72%" rowspan="2"><H3 align="center">' . $this->Nombre . '</H3></td>
                    </tr>
                    <tr>
                      <td height="21"><div align="center">' . $this->Regional . '</div></td>
                    </tr>
                  </table>';
                break;
            case 2:
                $margen = base_url() . 'img/margenes.png';
                $this->SetAutoPageBreak(false, 0);
            //    $this->Image($margen, 0, 0, 210, 300, '', '', '', false, 5, '', false, false, 0);
                $this->SetAutoPageBreak(true, 40);
                $Cabeza = '
                  <table width="100%" border="0">
                    <tr>
                      <td width="28%" rowspan="3"><div align="center"><img src="' . base_url('img/Logotipo_SENA.png') . '" width="100" height="100" /></div></td>
                      <td width="72%" height="27"></td>
                    </tr>
                    <tr>
                      <td height="27"></td>
                    </tr>
                    <tr>
                      <td width="72%" rowspan="2"><H3 align="center">' . $this->Nombre . '</H3></td>
                    </tr>
                    <tr>
                      <td height="21"><div align="center">' . $this->Regional . '</div></td>
                    </tr>
                  </table>';
                break;
            default:
                $this->SetAutoPageBreak(true, 40);
                $Cabeza = '';
                break;
        }
        $Cabeza="";
        $this->SetY(15);
        $this->SetFont('helvetica', 'N', 8);
        $this->Cell(0, -15, $this->writeHTML($Cabeza, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    public function Footer() {
        switch ($this->Tipo) {
            case 1:
            case 2:
                $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
                $this->Line(20, 282.5, 190, 282.5, $style);
                break;
            default:
                $style = array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
                $this->Line(20, 264.8, 190, 264.8, $style);
                break;
        }
        $Pie = '
        <div align="center">
            <br><b>SENA, Más Trabajo</b><br>
            <b>Ministerio de Trabajo</b><br>
            <b>SERVICIO NACIONAL DE APRENDIZAJE</b><br>
            <b>' . $this->Regional . ' - ' . $this->Direccion . '</b><br>
            <b>Tel: ' . $this->Telefono . ' - www.sena.edu.co - Línea gratuita nacional: 01 8000 9 10 270<b/><br>
                <b>' . $this->getAliasNumPage() . '<b/><br>
        </div>';
        $this->SetY(-28);
        $this->SetFont('helvetica', 'N', 8);
        $this->Cell(0, 0, $this->writeHTML($Pie, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Cell(-15, 0, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
