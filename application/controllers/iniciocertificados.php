<?php

error_reporting(0);

class Iniciocertificados extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->data['javascripts'] = array(
            'js/jquery.dataTables.min.js',
            'js/jquery.dataTables.defaults.js',
            'js/tinymce/tinymce.min.js',
            'js/jquery.validationEngine-es.js',
            'js/jquery.validationEngine.js',
            'js/validateForm.js',
        );
//Cargamos las hojas de estilos nesesariass
        $this->data['style_sheets'] = array(
            'css/jquery.dataTables_themeroller.css' => 'screen',
            'css/validationEngine.jquery.css' => 'screen'
        );
        $this->load->library('Nu_soap');
        $this->template_file = 'templates/main';

        date_default_timezone_set('America/Bogota');

        $this->load->library('form_validation');
        $this->load->helper('url');

        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->lang->load('auth');
        $this->load->helper('language');
        $this->template_file = 'templates/main';

        $this->data['user'] = $this->session->all_userdata();

        define("COD_USUARIO", $this->data['user']['user_id']);
        define("COD_REGIONAL", $this->data['user']['regional']);
    }

    function index() {
        
    }

    function tipoCertificados() {
        if ($this->ion_auth->logged_in()) {

            $this->data['nit'] = COD_USUARIO;
            //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
            $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo
            $parametros = array('numero' => "1");
            $respuesta = $cliente->call('Certificados_services..ListaCertificadosFic', $parametros);

            $tipo = $this->input->post('tipoCertificado');
            // print_r($tipo);die;
            switch ($tipo) {
                case "APORTESPARAFISCALES":
                    $this->data['input'] = "1";
                    $this->data['vista'] = 'certificados1';
                    //  $this->activo2('certificados1');
                    $this->data['pdf'] = "";
                    $this->data['titulo'] = "Certificado Aportes Parafiscales";
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                    break;
                case "RECIPROCAS":
                    //  $post = $this->input->post();
                    $this->data['titulo'] = "Certificados Recí­procas";
                    $this->data['vista'] = 'certificados17';
                    //    $this->activo2('certificados17');
                    $this->data['pdf'] = "";
                    $this->data['input'] = "17";
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                    break;
                case "TRIBUTARIO-RECAUDO":
                    $this->data['titulo'] = "Certificado Tributario y Recaudo de Pagos";
                    $this->data['vista'] = 'certificados2';
                    //     $this->activo2('certificados2');
                    $this->data['pdf'] = "";
                    $this->data['input'] = "2";
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                    break;
                case "FIC":
                    $this->data['select'] = $respuesta['Respuesta'];
                    $this->template->load($this->template_file, 'certificados/generador_certificados', $this->data);
                    break;
                case "PAGOS DE LIQUIDACION":
                    $this->data['select'] = $respuesta['Respuesta'];
                    $this->template->load($this->template_file, 'certificados/pagos_liquidacion', $this->data);
                    break;
            }
        } else {
            redirect('auth/login', 'refresh');
        }



        //print_r($this->input->post('tipoCertificado'));die;
    }

    function imprimir() {

        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo

        $id = COD_USUARIO;
        $regional = COD_REGIONAL;
        $tipo = $this->input->post('vista');
        switch ($tipo) {

            case "certificados1":
                $parametros = array('nit' => $id, 'regional' => $regional);
                $respuesta = $cliente->call('Certificados_services..CetificadosAportes', $parametros);

                if ($respuesta['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['input'] = "1";
                    $this->data['vista'] = 'certificados1';
                    $this->data['titulo'] = "Certificado Aportes Parafiscales";
                    $this->data['pdf'] = $respuesta['pdf'];

                    $this->data['nombre'] = $respuesta['nombre'];
                }
                $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                break;

            case "certificados17":
                $parametros = array('numero' => $id, 'ano' => $this->input->post('ano'));
                $resultado = $cliente->call('Certificados_services..CetificadosReciprocas', $parametros);
                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados Recí­procas";
                    $this->data['vista'] = 'certificados17';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->data['input'] = "17";
                }
                $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);

                break;
            case "certificados2":
                $parametros = array('numero' => $id, 'ano' => $this->input->post('ano'), 'regional' => $regional);
                $resultado = $cliente->call('Certificados_services..CertificadoTributarioRecaudo', $parametros);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificado Tributario y Recaudo de Pagos";
                    $this->data['vista'] = 'certificados2';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->data['input'] = "2";
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados7":
                $parametros = array('nit' => $id, 'obra' => $this->input->post('obra'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados7';
                    $this->data['input'] = "7";
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados8":
                $parametros = array('nit' => $this->input->post('transac'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados8';
                    $this->data['input'] = "8";
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados9":
                $parametros = array('nit' => $id, 'obra' => $this->input->post('transacion'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados9';
                    $this->data['input'] = "9";
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados12":
                $parametros = array('nit' => $this->input->post('Ntickei'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados12';
                    $this->data['input'] = "12";
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados13":
                $parametros = array('nit' => $this->input->post('Nticempresakei'), 'obra' => $this->input->post('num_proceso'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados13';
                    $this->data['input'] = "13";
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados15":

                $parametros = array('nit' => $this->input->post('num_proceso'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados15';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "15";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados21":

                $parametros = array('nit' => COD_USUARIO, 'regioanl' => $regional);
                $otro = array($this->input->post('vista') => $parametros);

                //print_r($otro);die;
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados21';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "21";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados22":
                $parametros = array('nit_empresa_referencia' => COD_USUARIO, 'codigos_pago' => $this->input->post('codigos_pago'));
                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);
                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados22';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "22";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }
                break;
            case "certificados23":

                $parametros = array('nit' => COD_USUARIO, 'regioanl' => $regional, 'ano' => $this->input->post('ano'));

                $otro = array($this->input->post('vista') => $parametros);

                //print_r($otro);die;
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados23';
                    $this->data['nombre'] = $resultado['nombre'];

                    $this->data['input'] = "23";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;

            case "certificados16":
                $parametros = array('nit' => $this->input->post('empresa'), 'obra' => $this->input->post('periodo'), 'regioanl' => $regional);

                $otro = array($this->input->post('vista') => $parametros);
                $parametros1 = array("fic" => $otro);
                $resultado = $cliente->call('Certificados_services..certificadosFic', $parametros1);

                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados16';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "16";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }

                break;
            case "certificados100":
                $parametros = array('nit' => COD_USUARIO, 'listado' => $this->input->post('codigos_liquidaciones'), 'tipo' => $this->input->post('vista'));
                $resultado = $cliente->call('Certificados_services..CertificadosPagosLiquidacion', $parametros);
                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados100';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "100";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }
                break;
            case "certificados101":
                $parametros = array('nit' => COD_USUARIO, 'listado' => $this->input->post('codigos_liquidaciones'), 'tipo' => $this->input->post('vista'));
                $resultado = $cliente->call('Certificados_services..CertificadosPagosLiquidacion', $parametros);
                if ($resultado['Respuesta'] == '') {
                    $this->data['title'] = "No es posible Realizar la consulta ";
                    $this->session->set_flashdata('message', $this->data['title']);
                    redirect('auth/consulta', 'refresh');
                } else {
                    $this->data['titulo'] = "Certificados FIC";
                    $this->data['vista'] = 'certificados100';
                    $this->data['nombre'] = $resultado['nombre'];
                    $this->data['input'] = "100";
                    $this->data['pdf'] = $resultado['pdf'];
                    $this->template->load($this->template_file, 'certificados/certificadosAportes', $this->data);
                }
                break;
        }
    }

    function certificados7() {


        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados7';
        $this->data['pdf'] = "";
        $this->data['input'] = "7";
        $this->load->view('certificados/certificadosAportes', $this->data);
        //        $this->template->load($this->template_file, 'reporteador/certificados', $this->data);
    }

    function certificados8() {


        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados8';
        $this->data['pdf'] = "";
        $this->data['input'] = "8";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados9() {


        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados9';
        $this->data['pdf'] = "";
        $this->data['input'] = "9";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados12() {


        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados12';
        $this->data['pdf'] = "";
        $this->data['input'] = "12";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados13() {

        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados13';
        $this->data['pdf'] = "";
        $this->data['input'] = "13";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados15() {

        $this->data['nit'] = COD_USUARIO;
        $post = $this->input->post();
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados15';
        $this->data['pdf'] = "";
        $this->data['input'] = "15";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados16() {

        $this->data['nit'] = COD_USUARIO;
        $post = $this->input->post();
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados16';
        $this->data['pdf'] = "";
        $this->data['input'] = "16";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function mirar_certificados() {
        $this->template->load($this->template_file, 'certificados/mirar_certificados');
    }

    function buscar_certificado() {
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo

        $parametros = array('numero' => $this->input->post('numero'), 'codigo' => $this->input->post('codigo'));

        $resultado = $cliente->call('Certificados_services..Buscarcertificado', $parametros);
        echo $resultado['Respuesta'];
    }

    function registrar() {
        $myVar = $this->session->flashdata('item2');
        if ($myVar != '') {
            $this->data['mensaje'] = $myVar;
        } else {
            $this->data['mensaje'] = "";
        }
        $this->data['TipoId'] = "";
        $this->data['idAportante'] = "";
        $this->data['nombres'] = "";
        $this->data['apellidos'] = "";
        $this->data['correo'] = "";
        $this->data['TipoIdLegal'] = "";
        $this->data['idLegal'] = "";
        $this->data['registro'] = "";

        $this->template->load($this->template_file, 'inicio/registro', $this->data);
    }

    function verificacion() {
        // print_r($this->input->post());
        $valor = date('Y/m/d H:i:s');
        $fechaA = $this->input->post('fefhas');
        //print_r($this->input->post('fefhas'));die;
        $fechaB = $valor;
        $diff = gmdate("i:s", strtotime($fechaB) - strtotime($fechaA));
        //print_r( $diff);die;
        if ($diff > '02:00') {

            //$this->data['message'] = 'Sesion Bloqueada Recuperar  la Contraseña';

            $this->data['message'] = "SU CODIGO EXPIRO";
            $this->template->load($this->template_file, 'auth/login', $this->data);
        } else {
//print_r( $diff  );die;

            $datos = $this->input->post();
            if ($datos != '') {


                @$contador = $this->input->post('contante');
                if (@$contador != '') {


                    $parametros = array('id1' => $this->input->post('idAportante'), 'correo' => $this->input->post('correo'), 'intentos' => $contador);
                    //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl", false);//produccion
                    $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo
                    $respuesta = $cliente->call('Certificadosempresa_services..verificar', $parametros);
                    // print_r($respuesta);die; 
                    $data = $respuesta['Codigo'];
                    echo json_encode($data);
                } else if ($this->input->post('codigo') == 3) {

                    redirect('auth/login', 'refresh');
                } else {


                    $this->data['idAportante'] = $this->input->post('Aportante');
                    $this->data['correo'] = $this->input->post('correos');
                    $this->template->load($this->template_file, 'inicio/verificacion', $this->data);
                }
            } else {
                redirect('auth/login', 'refresh');
            }
        }
    }

    function registrarCodigo() {

        $myVar = $this->session->flashdata('item');
        $this->data['registro'] = "1";
        $this->data['idAportante'] = $myVar['idAportante'];
        $this->data['TipoId'] = $myVar['TipoId'];
        $this->data['nombres'] = $myVar['nombres'];
        $this->data['apellidos'] = $myVar['apellidos'];
        $this->data['correo'] = $myVar['correo'];
        $this->data['TipoIdLegal'] = $myVar['TipoIdLegal'];
        $this->data['idLegal'] = $myVar['idLegal'];
        $this->data['Fechas'] = $myVar['Fechas'];
        $this->data['mensaje'] = "";
        $this->data['validacion'] = $myVar['validacion'];
        if ($myVar == '') {
            $this->data['mensaje'] = " SESION INACTIVA.. INGRESE NUEVAMENTE EL REGISTRO";
            //echo $this->email->print_debugger();die;
            $this->load->library('session');
            $this->session->set_flashdata('item2', $this->data['mensaje']);

            //  $this->template->load($this->template_file, 'inicio/registro', $this->data);

            redirect("/iniciocertificados/registrar");
        }



        $this->template->load($this->template_file, 'inicio/registro', $this->data);
    }

    function registrarServices() {

        $this->data['idAportante'] = $this->input->post('idAportante');
        $cont = 3;
        @$codigo = $this->input->post('codigo');
        @$validacion = $this->input->post('validacion');
        if ($codigo != '') {


            if (@$validacion == @$codigo) {


                $this->template->load($this->template_file, 'inicio/verificacion', $this->data);
            }
        } else {


            $this->data['validacion'] = "";
            $this->data['registro'] = "1";
            $this->data['TipoId'] = $this->input->post('TipoId');
            $this->data['nombres'] = $this->input->post('nombres');
            $this->data['apellidos'] = $this->input->post('apellidos');
            $this->data['correo'] = $this->input->post('correo');
            $this->data['TipoIdLegal'] = $this->input->post('TipoIdLegal');
            $this->data['idLegal'] = $this->input->post('idLegal');
            $this->data['mensaje'] = "";
            $this->data['ver'] = $this->input->post('ver');
            //  print_r($this->input->post());die;  //$this->data['validación']!=$this->input->post('idVerificacion')   
            $var = $this->input->post('idVerificacion');

            $parametros = array('user' => $this->input->post('nombres'), 'apellidos' => $this->input->post('apellidos'), 'correo' => $this->input->post('correo'), 'tipo1' => $this->input->post('TipoId'), 'tipo2' => $this->input->post('TipoIdLegal'), 'id1' => $this->input->post('idAportante'), 'id2' => $this->input->post('idLegal'));
            //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl", false);//produccion
            $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo

            $respuesta = $cliente->call('Certificadosempresa_services..RegistroSirec', $parametros);

            $codigoVerificcion = "";
            $codigoVerificcion = $this->input->post('idVerificacion');

            if ($respuesta['Respuesta'] == 4) {
                $this->data['mensaje'] = " YA SE ENCUENTRA REGISTRADO INGRESE A RECUPERAR CLAVE";
                $this->template->load($this->template_file, 'inicio/registro', $this->data);
                return;
            }
            if ($respuesta['Respuesta'] == 3) {
                $this->data['mensaje'] = "Señor Usuario debe acercarse a actulizar los datos ";
                $this->template->load($this->template_file, 'inicio/registro', $this->data);
            } else {

                if ($respuesta['Respuesta'] == 1) {
                    $this->data['validacion'] = $respuesta['Codigo'];
                    $this->data['Fechas'] = date('Y/m/d H:i:s');

                    $headers = '<p>El Servicio Nacional de Aprendizaje SENA le informa que usted ha solicitado registrarse Certificados de Aportes y FIC.</p><br><br>';
                    $headers .= '<p>Su Código de Acceso Seguro es: ' . $respuesta['Codigo'] . '</p><br><br>';
                    $headers .= '<p> Este código expira en 15 minutos </p><br><br>';
                    $headers .= '<p>En caso de presentar inconvenientes en la generación de Certificados de Aportes y FIC, escribir al correo: certiaportes@sena.edu.co</p><br><br>';
// Additional headers
// Additional headers
                    $headers .= ' <p>**********************NO RESPONDER - Mensaje Generado Automáticamente**********************</p><br><br>';
                    $headers .= 'Este correo es únicamente informativo y es de uso exclusivo del destinatario(a), puede contener información privilegiada y/o confidencial. Si no es usted el destinatario(a) deberá borrarlo inmediatamente. Queda notificado que el mal uso, divulgación no autorizada, alteración y/o  modificación malintencionada sobre este mensaje y sus anexos quedan estrictamente prohibidos y pueden ser legalmente sancionados.  El SENA  no asume ninguna responsabilidad por estas circunstancias.' . "\r\n";
                    //$htmlContent= 'Su Código de Acceso Seguro es: '. $respuesta['Codigo'];
                    $config = array(
                        'protocol' => 'smtp',
                        'smtp_host' => 'relay.sena.edu.co',
                        'smtp_port' => 25,
                        'smtp_user' => 'certiaportes@sena.edu.co',
                        'smtp_pass' => 'Audiencia+2022*',
                        'mailtype' => 'html',
                        'charset' => 'utf-8',
                        'newline' => "\r\n"
                    );

                    $this->email->initialize($config);
                    $this->email->to($this->input->post('correo'), 'Certificados En Lí­nea');
                    $this->email->from('certiaportes@sena.edu.co', 'Certificados En Lí­nea');
                    $this->email->subject('SENA -Código de Acceso Seguro');
                    $this->email->message($headers);
                    $this->email->send();

                    //echo $this->email->print_debugger();die;
                    $this->load->library('session');
                    $this->session->set_flashdata('item', $this->data);

                    //  $this->template->load($this->template_file, 'inicio/registro', $this->data);

                    redirect("/iniciocertificados/registrarCodigo");
                    // return  $this->registrarCodigo($this->data,$this->data['validacion']);
                }


                if ($respuesta['Respuesta'] == 6) {
                    $this->data['validacion'] = $respuesta['Codigo'];

                    //echo $this->email->print_debugger();die;

                    $this->template->load($this->template_file, 'inicio/registro', $this->data);
                }
            }
        }
    }

    function modificarClave() {

        //print_r($this->input->post());

        $parametros = array('user' => $this->input->post('identity'), 'pass' => $this->input->post('password'), 'nit' => $this->input->post('nit'));
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo

        $respuesta = $cliente->call('Certificadosempresa_services..RegistroUpdateLogin', $parametros);
        if ($respuesta['Respuesta'] == 1) {
            $this->load->library('session');
            $this->session->set_flashdata('item', $this->input->post());
            redirect('auth/login', 'refresh');
        } else {
            redirect('auth/login', 'refresh');
        }
    }

    function recuperarClave() {
        if ($this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('auth/consulta', 'refresh');
        } else {


            $myVar = $this->session->flashdata('item2');
            if ($myVar != '') {
                $this->data['mensaje'] = $myVar;
            } else {
                $this->data['mensaje'] = "";
            }
            $this->data['idAportante'] = '';
            $this->data['correo'] = '';
            $this->data['registro'] = "";

            $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
        }
    }

    function VerificarCodigo() {



        $myVar = $this->session->flashdata('item');
        $dates = date('d/m/Y H:i:s');

        $this->data['mensaje'] = "";
        $this->data['registro'] = "1";
        $this->data['validacion'] = "";

        $this->data['idAportante'] = $myVar['idAportante'];
        $this->data['correo'] = $myVar['correo'];
        $this->data['validacion'] = $myVar['validacion'];
        $this->data['Fechas'] = $myVar['Fechas'];

        if ($myVar == '') {
            $this->data['mensaje'] = " SESION INACTIVA.. INTENTE RECUPERAR SU CLAVE NUEVAMENTE";
            //echo $this->email->print_debugger();die;
            $this->load->library('session');
            $this->session->set_flashdata('item2', $this->data['mensaje']);

            //  $this->template->load($this->template_file, 'inicio/registro', $this->data);

            redirect("/iniciocertificados/recuperarClave");
        }

        $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
    }

    function verificarClave() {
        $dates = date('d/m/Y H:i:s');

        // print_r($fechaejec );
        //  die;
        $this->data['mensaje'] = "";
        $this->data['registro'] = "1";
        $this->data['validacion'] = "";

        $this->data['idAportante'] = $this->input->post('idAportante');
        $this->data['correo'] = $this->input->post('correo');

        $var = $this->input->post('idVerificacion');
        $intentos = '';

        $parametros = array('id1' => $this->input->post('idAportante'), 'correo' => $this->input->post('correo'), 'intentos' => $intentos);
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo

        $respuesta = $cliente->call('Certificadosempresa_services..verificar', $parametros);


        if ($respuesta['Respuesta'] == 3) {
            $this->data['mensaje'] = " EL CORREO INGRESADO NO SE ENCUENTRA REGISTRADO ";
            $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
        }
        if ($respuesta['Respuesta'] == 5) {
            $this->data['mensaje'] = " EL NúMERO DE IDENTIFICACIÓN NO SE ENCUENTRA REGISTRADO ";
            $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
        }
        if ($respuesta['Respuesta'] == 7) {
            $this->data['mensaje'] = "EL CORREO INGRESADO NO SE ENCUENTRA REGISTRADO";
            $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
        } else {
            $this->data['validacion'] = $respuesta['Codigo'];
            $this->data['Fechas'] = date('Y/m/d H:i:s');
            // $date = date('d/m/Y H:i:s');


            if ($respuesta['Respuesta'] == 1) {

                $headers = '<p>El Servicio Nacional de Aprendizaje SENA le informa que usted ha solicitado registrarse Certificados de Aportes y FIC.</p><br><br>';
                $headers .= '<p>Su Código de Recuperación de Contraseña es: ' . $respuesta['Codigo'] . '</p><br><br>';
                $headers .= '<p> Este código expira en 15 minutos </p><br><br>';
                $headers .= '<p>En caso de presentar inconvenientes en la generación de Certificados de Aportes y FIC, escribir al correo: certiaportes@sena.edu.co</p><br><br>';
                // Additional headers
                $headers .= ' <p>**********************NO RESPONDER - Mensaje Generado Automáticamente**********************</p><br><br>';
                $headers .= 'Este correo es únicamente informativo y es de uso exclusivo del destinatario(a), puede contener información privilegiada y/o confidencial. Si no es usted el destinatario(a) deberá borrarlo inmediatamente. Queda notificado que el mal uso, divulgación no autorizada, alteración y/o  modificación malintencionada sobre este mensaje y sus anexos quedan estrictamente prohibidos y pueden ser legalmente sancionados.  El SENA  no asume ninguna responsabilidad por estas circunstancias.' . "\r\n";

                $htmlContent = 'Su Código de Recuperación de Contraseña es: ' . $respuesta['Codigo'];
                $config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => 'relay.sena.edu.co',
                    'smtp_port' => 25,
                    'smtp_user' => 'certiaportes@sena.edu.co',
                    'smtp_pass' => 'Audiencia+2022*',
                    'mailtype' => 'html',
                    'charset' => 'utf-8',
                    'newline' => "\r\n"
                );

                $this->email->initialize($config);
                $this->email->to($this->input->post('correo'), 'Certificados En Lí­nea');
                $this->email->from('certiaportes@sena.edu.co', 'Certificados En Lí­nea');
                $this->email->subject('SENA -Código de  Recuperación de Contraseña');
                $this->email->message($headers);
                $this->email->send();

                $this->load->library('session');
                $this->session->set_flashdata('item', $this->data);

                redirect("/iniciocertificados/VerificarCodigo");

                // $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
            }
            if ($respuesta['Respuesta'] == 6) {
                $this->data['mensaje'] = "A OCURRIDO UN ERROR";
            }
            // if ($respuesta['Codigo'] == $codigoVerificcion) {
            //  $this->template->load($this->template_file, 'inicio/verificacion', $this->data);
            //   } //else {
            //$this->data['mensaje'] = "CODIGO ERRONEO";
            //$this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
            // }
        }



        // $this->template->load($this->template_file, 'inicio/recuperarClave', $this->data);
    }

    function certificados21() {

        $this->data['nit'] = COD_USUARIO;
        $post = $this->input->post();
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados21';
        $this->data['pdf'] = "";
        $this->data['input'] = "21";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

    function certificados100() {
        ini_set('memory_limit', '-1');
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo
        $parametros = array('nit' => COD_USUARIO);
        $respuesta = $cliente->call('Certificados_services..LiquidacionesPagas', $parametros);
        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificado por liquidaciones pagas";
        $this->data['vista'] = 'certificados100';
        $this->data['pdf'] = "";
        $this->data['input'] = "100";
        $this->data['liquidacion_nit'] = $respuesta['Respuesta'];
        $this->load->view('pagos_liquidacion/listado_liquidaciones', $this->data);
    }

    function certificados101() {
        ini_set('memory_limit', '-1');
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo
        $parametros = array('nit' => COD_USUARIO);
        $respuesta = $cliente->call('Certificados_services..PagosLiquidaciones', $parametros);
        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados101';
        $this->data['pdf'] = "";
        $this->data['input'] = "101";
        $this->data['liquidacion_pagos'] = $respuesta['Respuesta'];
        $this->load->view('pagos_liquidacion/listado_pagos', $this->data);
    }

    function certificados22() {
        ini_set('memory_limit', '-1');
        //$cliente = new nusoap_client("http://172.29.19.108/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//produccion
        $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl", false);//desarrollo
        $parametros = array('nit' => COD_USUARIO);
        $respuesta = $cliente->call('Certificados_services..PagosFIC', $parametros);
        $this->data['nit'] = COD_USUARIO;
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados22';
        $this->data['pdf'] = "";
        $this->data['input'] = "22";
        $this->data['pagos_nit'] = $respuesta['Respuesta'];
        $this->load->view('fic/listado_FIC', $this->data);
    }

    function certificados23() {
        $this->data['nit'] = COD_USUARIO;
        $post = $this->input->post();
        $this->data['titulo'] = "Certificados FIC";
        $this->data['vista'] = 'certificados23';
        $this->data['pdf'] = "";
        $this->data['input'] = "23";
        $this->load->view('certificados/certificadosAportes', $this->data);
    }

}

/* End of file categorias.php */
/* Location: ./system/application/controllers/categorias.php */