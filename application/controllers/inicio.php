<?php
error_reporting(0);
class Inicio extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        

        date_default_timezone_set('America/Bogota');
    }
    function index() {


        if ($this->ion_auth->logged_in()) {
            //redirect them to the login page
          redirect('auth/consulta', 'refresh');
          } else{
        
            redirect(base_url() . 'index.php/auth/login');
          }
        
    }

}

