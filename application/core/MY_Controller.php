<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Community Auth - MY_Controller
 *
 * Community Auth is an open source authentication application for CodeIgniter 2.1.3
 *
 * @package     Community Auth
 * @author      Robert B Gottier
 * @copyright   Copyright (c) 2011 - 2013, Robert B Gottier. (http://brianswebdesign.com/)
 * @license     BSD - http://http://www.opensource.org/licenses/BSD-3-Clause
 * @link        http://community-auth.com
 */
class MY_Controller extends CI_Controller {

    public $template_file = 'templates/main2';
    public $orig_name_load = '';

    /**
     * Class constructor
     */
    public function __construct() {
        // creación dinámica del menú
        parent::__construct();
        header('Pragma: no-cache');
        $this->load->helper('array');
        $this->load->library('Nu_soap');
        $session_id = $this->session->all_userdata();

        if ($this->ion_auth->logged_in()) {
            //  print_r( $session_id);
            $id = $session_id['user_id'];

            $data = array(
                'LASTSESSIONID' => $this->session->userdata('session_id')
            );
            $sesion = $data['LASTSESSIONID'];
            $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);
//$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl",false);
            $parametros = array('id' => $id, 'sesion' => $sesion);
            $respuesta = $cliente->call('Certificados_services..ListarSession', $parametros);
            //  print_r( $respuesta);die;
            if ($respuesta['Respuesta'] != $this->session->userdata('token')) {

                $this->ion_auth->logout();
                $this->session->set_flashdata('message', '
						<div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">&times;</button>
								 <h4>Atención!</h4>
								 Usted fue desconectado porque alguien inició sesión en otro equipo con el mismo usuario
						</div>');
            }
        }
    }

    function menssage_error($text) {
        $class = "error";
        $this->session->set_flashdata('message', '<div class="alert alert-'
                . $class . '"><button type="button" class="close" data-dismiss="alert">&times;</button>'
                . $text . '</div>');
    }

    function menssage_ok($text) {
        $class = "success";
        $this->session->set_flashdata('message', '<div class="alert alert-'
                . $class . '"><button type="button" class="close" data-dismiss="alert">&times;</button>'
                . $text . '</div>');
    }

}

/* End of file MY_Controller.php */
/* Location: /application/libraries/MY_Controller.php */