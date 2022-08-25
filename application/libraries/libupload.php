<?php if (!defined('BASEPATH')) exit('No permitir el acceso directo al script'); 
/**
*  Libreria para manejo de archivos
*  agregada por Leonardo Molina
*  Modificada por Yuri Ramirez 

*/
ini_set( 'memory_limit', '300M' );
ini_set('upload_max_filesize', '1000M');  
ini_set('post_max_size', '1000M');  
ini_set('max_input_time', 3600);  
ini_set('max_execution_time', 3600);


class Libupload  {

	function __construct(){
		$this->CI =& get_instance();
	}

	public function doUpload($i,$files, $dir, $kind, $size = NULL, $width = NULL, $height = NULL,$name = NULL){
		unset($config);
		if($name   != NULL){ $config['file_name']  = $name; }
		if($size   != NULL){ $config['max_size']   = $size; }
		if($width  != NULL){ $config['max_width']  = $width;}
		if($height != NULL){ $config['max_height'] = $height;}
		$config['upload_path']   = './uploads/'.$dir.'/';
		$config['allowed_types'] = $kind;
		$config['overwrite']     = FALSE;
		$config['remove_spaces'] = TRUE;

		$this->CI->load->library('upload',$config);

    	if(!empty($files['name'])){
	        if(!$upload = $this->CI->upload->do_upload('archivo'.$i)){    
                    $error = array('error' =>$this->CI->upload->display_errors());
                    return $error;
                } 
                else{
                    $data =array('data' =>$this->CI->upload->data());
                    return $data;
                }
            }
	}


	public function doThumb($img,$dir, $width, $height)
	{
		unset($config);
		$config['image_library']  = 'GD2';
		$config['source_image']   = './'.$dir.'/'.$img;
		$config['new_image']      = './'.$dir.'/thumb/'.$img;
		$config['create_thumb']   = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width']          = $width;
		$config['height']         = $height;

		if(!empty($config['source_image']))
		{
                    $this->CI->load->library('image_lib', $config);
                    $this->CI->image_lib->initialize($config);

                    if (!$this->CI->image_lib->resize()){
                        $error = array('error'=>$this->CI->image_lib->display_errors());
                        return $error;
                    }
                    else{
                        return TRUE;
                    }
                    $this->CI->image_lib->clear();
		}

	}
	public function  delete($dir,$file,$image = NULL)
	{
		$return = array();
		$archivo1  = './uploads/'.$dir.'/'.$file;
		if($image == TRUE) { $archivo2  = './'.$dir.'/thumb/'.$file; }
		if (file_exists($archivo1)) {
		    if(unlink($archivo1)){
		    	$return[0] = TRUE; 
		    } else {
		    	$return[0] = FALSE; 
		    }
		}
		if (file_exists($archivo2)) {
		    if(unlink($archivo2)){
		    	$return[1] = TRUE; 
		    } else {
		    	$return[2] = FALSE; 
		    }
		}
	}
}