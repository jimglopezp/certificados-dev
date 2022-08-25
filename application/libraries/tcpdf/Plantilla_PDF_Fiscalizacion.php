<?php

class Plantilla_PDF_Fiscalizacion extends TCPDF {

 public $regional;
 
    public function Header() {
	
	  $this->SetAutoPageBreak(false, 0);
            // Print of the logo
            
			$logo=base_url() . 'img/sena.jpg';
			//echo($logo);
			//$this->Image($logo, 96, 6, 20, 20, '', '', '', false, 5, '', false, false, 0);
            //$this->Image($logo, $this->GetX(), $this->getHeaderMargin(), $headerdata['logo_width']);
			$this->SetAutoPageBreak(true, 40);
    }

		public function Footer() {

			$Pie = '
		<div align="center">
			<br><b>Ministerio de Trabajo</b><br>
			<b>SERVICIO NACIONAL DE APRENDIZAJE</b><br>
			<b>'.$this->regional['NOMBRE_REGIONAL'].' - (Direccion de Promocion y Relaciones Corporativas)</b><br>
			<b>'.$this->regional['DIRECCION_REGIONAL'].' - PBX '.$this->regional['TELEFONO_REGIONAL'].' </b><br>
			<b>www.sena.edu.co - Linea Gratuita Nacional: 01 8000 9 10 270</b><br>
		</div>';
	//REGIONAL.NOMBRE_REGIONAL,REGIONAL.DIRECCION_REGIONAL, REGIONAL.TELEFONO_REGIONAL
			$this->SetY(-25);
			$this->SetFont('helvetica', 'N', 8);
			$this->Cell(0, 5, $this->writeHTML($Pie, true, false, true, false, ''), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}

}
