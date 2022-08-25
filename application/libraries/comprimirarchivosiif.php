<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');

class Comprimirarchivosiif  {
		
		function creararchivo($rutaOrigen, $archivos, $nombreDestino ,$descargar=TRUE){ 

				if ($this->ion_auth->logged_in()){

					$this->load->library('zip');
 
					
					$pos=0;
					$neg=0;

										

							foreach ($archivos as $row) {

							 $extension = $this->obtenerExtensionFichero($row);

								 if ($extension=='txt' or $extension=='TXT') {

								 	//$files = fopen($filet, "r") or exit("Unable to open file!");
									//fclose($filet);

								 	$pos+=1;
								 	//$this->zip->read_dir($rutaFinal);
								 	$this->zip->read_file($rutaOrigen.'/'.$row); 
																	 	
								 }else{

								 	$neg+=1;
								 } 

							}
						 
						    //donde no se mezcle los zip con los demas archivos
						    $this->zip->archive('zips/'.$nombreDestino.'');

								 $this->data['positivos']=$pos;
								 $this->data['negativos']=$neg;

								 if ($descargar) {
								 	$this->zip->download($nombreDestino);
								 }
								



				
			}
		}


		
		function obtenerExtensionFichero($str) 
		{
			        return end(explode(".", $str));
			
		}	


}


/* End of file Comprimirarchivosiif.php */
/* Location: ./system/application/libraries/Comprimirarchivosiif.php */