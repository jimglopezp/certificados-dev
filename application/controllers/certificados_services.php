<?php

/**
 * Archivo para la administración de certificados 
 *
 
 * */
if (!defined('BASEPATH'))
    exit('No se permite el acceso directo a las p&aacute;ginas de este sitio.');

class Certificados_services extends CI_Controller {

    private $nusoap_server;
    private $sesion;

    function __construct() {
        parent::__construct();
        $this->load->library("nu_soap");
        $this->ion = new Ion_auth_model();
        $this->sesion = new CI_Session();
        $this->load->library('tcpdf/tcpdf.php');
        $this->load->model('Reporteador_model');
      //  $this->load->file(APPPATH . "controllers/cartera_service.php", true);
   //   parent::__construct();
      $this->load->library('../controllers/reporteador');
      //  $this->load->file(APPPATH . "controllers/Reporteador.php");
        // Instanciamos la clase servidor de nusoap
        $this->nusoap_server = new nusoap_server();

        // Creamos el End Point, es decir, el lugar donde la petición cliente va a buscar la estructura del WSDL
        // aunque hay que recordar que nusoap genera dinámicamente dicha estructura XML 
        $end_point = base_url() . 'index.php/certificados_services/index/wsdl?wsdl';
        $ns = $end_point;
        // Indicamos cÃ³mo se debe formar el WSDL
        $this->nusoap_server->configureWSDL('Certificados_servicesWSDL', $ns, $end_point);
        $this->nusoap_server->wsdl->schemaTargetNamespace = $ns;

       
        // Indicamos cómo se debe formar el WSDL

    ///////////////////////////////////////////INICIO Alvaro Lasso
        $this->nusoap_server->register(
            'Certificados_services..CetificadosAportes'
            , array('nit' => 'xsd:string')
            , array('return' => 'xsd:Array')
            , 'http://certificados_servicesWSDL'
            , 'http://certificados_servicesWSDL#CetificadosAportes'
            , 'rpc'
            , 'encoded'
            , "Metodo que permite sacar el certificado Aportes Parafiscales del sistema de informacion SIREC."
    );

    $this->nusoap_server->register(
        'Certificados_services..CetificadosReciprocas'
        , array('nit' => 'xsd:string', 'ano' => 'xsd:string')
        , array('return' => 'xsd:Array')
        , 'http://certificados_servicesWSDL'
        , 'http://certificados_servicesWSDL#CetificadosReciprocas'
        , 'rpc'
        , 'encoded'
        , "Metodo que permite sacar el certificado  Reciprocas del sistema de informacion SIREC."
    );

    $this->nusoap_server->register(
        'Certificados_services..CetificadosPila'
        , array('nit' => 'xsd:string')
        , array('return' => 'xsd:Array')
        , 'http://certificados_servicesWSDL'
        , 'http://certificados_servicesWSDL#CetificadosPila'
        , 'rpc'
        , 'encoded'
        , "Metodo que permite sacar el certificado  Reciprocas del sistema de informacion SIREC."
    );

    $this->nusoap_server->register(
        'Certificados_services..CertificadoTributarioRecaudo'
        , array('nit' => 'xsd:string', 'ano' => 'xsd:string')
        , array('return' => 'xsd:Array')
        , 'http://certificados_servicesWSDL'
        , 'http://certificados_servicesWSDL#CertificadoTributarioRecaudo'
        , 'rpc'
        , 'encoded'
        , "Metodo que permite sacar el certificado  Reciprocas del sistema de informacion SIREC."
    );

// Parametros de entrada
$this->nusoap_server->wsdl->addComplexType(  'datos_FIC', 
                                'complexType',  
                                'struct', 
                                'all', 
                                '',
                                array('Pagos_Ordinarios_Nit_Licencia_N_Contrato'    =>  array('name'  => 'EstadoFic1', 'type' => 'tns:ArrayOfEstadoFic1', 'accion' => '7'),
                                      'Pagos_Ordinarios_Nro_Transaccion'            =>  array('name'  => 'EstadoFic2', 'type' => 'tns:ArrayOfEstadoFic2', 'accion' => '8'),
                                      'Pagos_Ordinarios_Nit_Periodo'                =>  array('name'  => 'EstadoFic3', 'type' => 'tns:ArrayOfEstadoFic3'),
                                      'Pagos_Ordinarios_Obra'                       =>  array('name'  => 'EstadoFic4', 'type' => 'tns:ArrayOfEstadoFic4'),
                                      'Pagos_Ordinarios_Nro_Referencia-Dispersion'  =>  array('name'  => 'EstadoFic5', 'type' => 'tns:ArrayOfEstadoFic5'),
                                      'Pagos_Ordinarios_NIT_Periodo'                =>  array('name'  => 'EstadoFic3', 'type' => 'tns:ArrayOfEstadoFic3'),
                                      'Liquidaciones_Resoluciones_NIT'              =>  array('name'  => 'EstadoFic7', 'type' => 'tns:ArrayOfEstadoFic7'),
                                      'Liquidaciones_Resoluciones_Nro_Liquidacion'  =>  array('name'  => 'EstadoFic8', 'type' => 'tns:ArrayOfEstadoFic8')
                                )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic1', 'complexType', 'struct', 'sequence', '', array(
    'nit' => array('name' => 'nit', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1'), 
    'licencia_contrato' => array('name' => 'licencia_contrato', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
    )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic2', 'complexType', 'struct', 'sequence', '', array(
    'nroTransaccion' => array('name' => 'nroTransaccion', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
    )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic3', 'complexType', 'struct', 'sequence', '', array(
        'nit' => array('name' => 'nit', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1'),
        'periodo' => array('name' => 'periodo', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
        )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic4', 'complexType', 'struct', 'sequence', '', array(
        'nit' => array('name' => 'nit', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1'),
        'nObra' => array('name' => 'nObra', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
        )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic5', 'complexType', 'struct', 'sequence', '', array(
        'nTicket' => array('name' => 'nTicket', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
       )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic7', 'complexType', 'struct', 'sequence', '', array(
        'nit' => array('name' => 'nit', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1'),
        'resolucion_liquidacion' => array('name' => 'resolucion_liquidacion', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
       )
);

$this->nusoap_server->wsdl->addComplexType(
    'ArrayOfEstadoFic8', 'complexType', 'struct', 'sequence', '', array(
        'resolucion_liquidacion' => array('name' => 'resolucion_liquidacion', 'type' => 'xsd:string', 'maxOccurs' => '1', 'minOccurs' => '1')
       )
);

 
$this->nusoap_server->register(   'Certificados_services..certificadosFic', // nombre del metodo o funcion
                    array('datos_FIC' => 'tns:datos_FIC'), // parametros de entrada
                    array('return' => 'xsd:Array'), 
                    'http://certificados_servicesWSDL',
                    'http://certificados_servicesWSDL#certificadosFic',
                    'rpc', // style
                    'encoded', // use
                    'La siguiente funcion recibe un arreglo multidimensional de personas y calcula las Edades respectivas segun la fecha de nacimiento indicada' // documentation,
                     //$encodingStyle
);


///////////////////////////////////////////FIN Alvaro Lasso CertificadoTributarioRecaudo
           

    }

    function index() {
        ob_clean();
        $_SERVER['QUERY_string'] = '';

        if ($this->uri->segment(3) == 'wsdl') {
            $_SERVER['QUERY_string'] = 'wsdl';
        } // endif

        $this->nusoap_server->service(trim(file_get_contents('php://input')));
    }

        public  function CetificadosAportes($empresa) {
            if(empty($empresa) )
                {
                    return       array("Respuesta"=>'' );
                }
            $obligatorios = array(
                'nit' => $empresa
            );
            $array = array( 
                "vista"    => "certificados1",
                "name_reporte"  => "Certificado Aportes Parafiscales",
                "empresa"  => $empresa,
                "accion"  => "1",
                "service"  => "1",
            );
            $RutaCertificado = "";
            $reportead = new Reporteador();


            $RutaCertificado=$reportead->imprimir_certificacion($array);

            $RutaCertificado = $RutaCertificado;

            //json_encode(array("RESOLUCIONES" => 'No existe Informacion Para las fechas ingesadas'));
    
            
            return $RutaCertificado;
            
        }
        

        public static function CetificadosReciprocas($empresa, $ano) {//////////////falta
          //  return       array("ll"=>$empresa,"l2"=>$ano );
                if(empty($empresa) || empty($ano))
                {
                    return       array("Respuesta"=>'' );
                }
            $obligatorios1 = array(
                'nit' => $empresa,
                'ano' => $ano,
            );

            $obligatorios = array(
                "vista"    => "certificados17",
                "name_reporte"  => "Certificados Recíprocas",
                'empresa' => $empresa,
                'ano' => $ano,
                "accion"  => "17",
                "service"  => "1",
            );

            $RutaCertificado = "";
            $reportead = new Reporteador();

            $RutaCertificado=$reportead->imprimir_certificacion($obligatorios);   

            return $RutaCertificado;
        }

        public static function CetificadosPila($empresa) {
        //    return       array("ll"=>$empresa );
        if(empty($empresa) )
                {
                    return       array("Respuesta"=>'' );
                }
            $obligatorios1 = array(
                'nit' => $empresa,                
            
            );
            ///  $posta = array('nit2' => $nit_empresa[0], 'estadocierre1' => $res->NOMBREMOTIVO, 'fechaperiodo1' => $res->PERIODO);
            $obligatoriosPila = array(      
                "vista"    => "certificadosPila",          
                "name_reporte"  => "Certificados Pila",
                'nit2' => $empresa,
                'estadocierre1' => "",
                'fechaperiodo1' => "",
                "service" => "1",
            );

            $RutaCertificado = "";
            $reportead = new Reporteador();

            $RutaCertificado=$reportead->pdfCierreEmpresa($obligatoriosPila);   

            return $RutaCertificado;
        }        
        

        public static function CertificadoTributarioRecaudo($nit, $ano) {
            if(empty($nit) || empty($ano))
                {
                    return       array("Respuesta"=>'' );
                }
            $obligatorios = array(
                'nit' => $nit,
                'ano' => $ano
            );
            $obligatorio = array(
                "vista"    => "certificados2",
                "name_reporte"  => "Certificado Tributario Recaudo",
                "empresa"  => $nit,
                'ano' => $ano,
                "accion"  => "2",
                "service"  => "1",
            );
            $RutaCertificado = "";
            $reportead = new Reporteador();

            $RutaCertificado=$reportead->imprimir_certificacion($obligatorio);    

            return $RutaCertificado;
        }



        public static function certificadosFic($empresa) {

                //---------------- CERTIFICADO NUMERO 7 --------------------  ['Pagos_Ordinarios_Nit_Licencia_N_Contrato']
              //  return       array("ll"=>$empresa );

        $i = key($empresa);

        switch ($i) {
            case "Pagos_Ordinarios_NIT_Periodo":
                if(empty($empresa['Pagos_Ordinarios_NIT_Periodo']['nit'])||empty($empresa['Pagos_Ordinarios_NIT_Periodo']['obra']) )
                {
                    return       array("Respuesta"=>'' );
                }
                $obligatoriosPagos = array(      
                    "vista"    => "certificados16",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'empresa' => $empresa['Pagos_Ordinarios_NIT_Periodo']['nit'],
                    "obra" => $empresa['Pagos_Ordinarios_NIT_Periodo']['obra'],
                    "accion" => "16",
                    "service" => "1",
                );
                //return       array("ll"=>key($empresa) ); 
                break;
            case "Pagos_Ordinarios_Nit_Licencia_N_Contrato":
                if(empty($empresa['Pagos_Ordinarios_Nit_Licencia_N_Contrato']['nit'])||empty($empresa['Pagos_Ordinarios_Nit_Licencia_N_Contrato']['obra']) )
                {
                    return       array("Respuesta"=>'' );
                }
                $obligatoriosPagos = array(      
                    "vista"    => "certificados7",          
                    "name_reporte"  => "CertificadoFicLicencia",
                    'empresa' => $empresa['Pagos_Ordinarios_Nit_Licencia_N_Contrato']['nit'],
                    "obra" =>  $empresa['Pagos_Ordinarios_Nit_Licencia_N_Contrato']['obra'],
                    "accion" => "7",
                    "service" => "1",
                );
                break;
            case "Pagos_Ordinarios_Nro_Transaccion":
                if(empty($empresa['Pagos_Ordinarios_Nro_Transaccion']['nit']) )
                {
                    return       array("Respuesta"=>'' );
                }
                $obligatoriosPagos = array(      
                    "vista"    => "certificados8",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'transac' => $empresa['Pagos_Ordinarios_Nro_Transaccion']['nit'],
                    "obra" => "Contrato90",
                    "accion" => "8",
                    "service" => "1",
                );
                break;
            case "Pagos_Ordinarios_Nit_Periodo":
                if(empty($empresa['Pagos_Ordinarios_Nit_Periodo']['nit'])||empty($empresa['Pagos_Ordinarios_Nit_Periodo']['obra']) )
                {
                    return       array("Respuesta"=>'' );
                }
                $obligatoriosPagos = array(      
                    "vista"    => "certificados9",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'empresa' => $empresa['Pagos_Ordinarios_Nit_Periodo']['nit'],
                    "transacion" => $empresa['Pagos_Ordinarios_Nit_Periodo']['obra'],
                    "accion" => "9",
                    "service" => "1",
                );
            break;
            case "Pagos_Ordinarios_Nro_Referencia-Dispersion"://///////////////////////
           //     return       array("ll"=>$empresa );
           
                
     //   return       array("ll"=>$empresa['Pagos_Ordinarios_Nro_Referencia-Dispersion']['nit'] );
         $obligatoriosPagos = array(      
                    "vista"    => "certificados12",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'nTicket' => $empresa['Pagos_Ordinarios_Nro_Referencia-Dispersion']['nit'],//$empresa['Pagos_Ordinarios_Nro_Referencia-Dispersion']['nit'],
                    "obra" => "Contrato90",
                    "accion" => "12",
                    "service" => "1",
                );
            break;
            case "Pagos_Ordinarios_NIT_Periodo":
                $obligatoriosPagos = array(      
                    "vista"    => "certificados16",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'empresa' => $empresa['Pagos_Ordinarios_NIT_Periodo']['nit'],
                    "obra" => "Contrato90",
                    "accion" => "16",
                    "service" => "1",
                );
            break;
            case "Liquidaciones_Resoluciones_NIT":
                $obligatoriosPagos = array(      
                    "vista"    => "certificados13",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'empresa' => $empresa['Liquidaciones_Resoluciones_NIT']['nit'],
                    "num_proceso" => $empresa['Liquidaciones_Resoluciones_NIT']['obra'],
                    "accion" => "13",
                    "service" => "1",
                );
            break;
            case "Liquidaciones_Resoluciones_Nro_Liquidacion":
                $obligatoriosPagos = array(      
                    "vista"    => "certificados15",          
                    "name_reporte"  => "CertificadoFicPagosOrdinarosNIT",
                    'num_proceso' => $empresa['Liquidaciones_Resoluciones_Nro_Liquidacion']['nit'],
                    "obra" => "Contrato90",
                    "accion" => "15",
                    "service" => "1",
                );
            break;
            

        }
      
            
       /*     $obligatorios1 = array(  
                'nit' => $empresa                
            
            );*/
            
            
//return $empresa['Pagos_Ordinarios_NIT_Periodo']['nit'];
            
            $RutaCertificado = "";
            $reportead = new Reporteador();

            $RutaCertificado=$reportead->imprimir_certificacion($obligatoriosPagos);   
           // return array("hola"=>$empresa);
            return $RutaCertificado;          

          
        }  

}

?>

