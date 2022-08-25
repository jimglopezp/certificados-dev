<?php

if (!defined('BASEPATH'))
  exit('No se permite el acceso directo a las p&aacute;ginas de este sitio.');

/**
 * ecollect_service (class MY_Controller) :)
 *
 * Ecollect Web Service
 *
 * Permite gestionar toda las transacciones de comunicación con ECOLLECT.
 *
 * @author Felipe Camacho [camachogfelipe]
 * @author http://www.cogroupsas.com
 *
 * @package Ecollect_service
 */
class Nu_soap {

  function __construct() {
    require_once(APPPATH . "/libraries/nusoap/nusoap" . EXT);
		//require_once(APPPATH . "/libraries/php-wsdl/class.phpwsdl" . EXT);
  }
}
