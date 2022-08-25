<?php

//ruta prototiposena: application/helpers/traza_fecha_helper.php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Funci?n trazar.
 * Registra la gesti?n realizada en el sistema.
 * Actualiza la gesti?n actual para la fiscalizaci?n cuando es relevante.
 *
 * @param Integer $tipogestion C?d. de tipo de gesti?n.
 * @param Integer $tiporespuesta C?d de tipo de respuesta.
 * @param Integer $codfiscalizacion C?d de fiscalizaci?n.
 * @param String $nit NIT de la empresa
 * @param Char $cambiarGestionActual "S": actualiza la tabla de gesti?n actual s?lo se debe poner si se est? registrando una gesti?n relevante.
 * @param String $usuariosAdicionales: Cadena de ID de usuario separada por comas para enviarle recordatorio "1,8012,23"
 * @param String $comentarios: Alg?n comentario al respecto
 * @return Array contiene el C?d de gesti?n cobro generado.
 *
 * $res = trazar(34, 49, 63, 801612878,"S","trazaDDD");
 */
function trazar($tipogestion, $tiporespuesta, $codfiscalizacion, $nit, $cambiarGestionActual, $usuariosAdicionales = '', $comentarios = "Sin Comentarios", $sistema = FALSE) {  //  echo "tipogestion: ".$tipogestion.", tiporespuesta".$tiporespuesta.", codfiscalizacion".$codfiscalizacion.", nit".$nit.", cambiarGestionActual".$cambiarGestionActual.", codgestionAnterior:".$codgestionAnterior." , comentarios:".$comentarios;
    $CI = get_instance();
    if ($CI->ion_auth->logged_in() || $sistema === TRUE) {
        $CI->load->model('flujo_model');
        $CI->load->model('codegen_model');
        ///_ " VERIFICAR SI LA TRAZA COINCIDE CON LA ÚLTIMA REGISTRADA PARA LA FISCALIZACIÓN PARA NO PONERLA. ";
        //(No se va a relizar esta verificación por que genera inconvenientes) $fila = $CI->flujo_model->trazaDuplicada($codfiscalizacion);if($fila['NIT_EMPRESA'] == $nit && $fila['COD_TIPO_RESPUESTA'] == $tiporespuesta && $fila['COMENTARIOS'] == $comentarios && $fila['COD_TIPOGESTION'] == $tipogestion && $fila['COD_USUARIO'] == $CI->ion_auth->user()->row()->IDUSUARIO ){        $idgestioncobro = $fila;     }else
        if (1 == 1) {
            $datagestiongenedoc = array("COMENTARIOS" => $comentarios, "COD_TIPO_RESPUESTA" => "$tiporespuesta", "COD_TIPOGESTION" => "$tipogestion",
                "NIT_EMPRESA" => $nit, "COD_FISCALIZACION_EMPRESA" => $codfiscalizacion, "COD_USUARIO" => $CI->ion_auth->user()->row()->IDUSUARIO);
            $fechaGestion = array('FECHA_CONTACTO' => date("d/m/Y H:i"));
            ///_ " AGREGAR LA TRAZA A LA TABLA. ";
            $data = $CI->flujo_model->addTraza("GESTIONCOBRO", $datagestiongenedoc, $fechaGestion);
            ///_ " OBTENER EL ID DE LA TRAZA INGRESADA. ";
            //$idgestioncobro = $CI->flujo_model->selectId('GESTIONCOBRO', $datagestiongenedoc, $fechaGestion);
            $idgestioncobro['COD_GESTION_COBRO'] = $CI->codegen_model->getLastInserted('GESTIONCOBRO', 'COD_GESTION_COBRO');
            $tf = new Traza_fecha_helper();
            ///_ " CAMBIAR LA GESTIÓN ACTUAL EN LA TABLA FISCALIZACIÓN SI ES RELEVANTE. ";
            if ($cambiarGestionActual == "S" || $cambiarGestionActual == "s" || $cambiarGestionActual == "cambiarGestionActual") {
                if (!empty($idgestioncobro['COD_GESTION_COBRO']) && !empty($codfiscalizacion) && !empty($tipogestion)) {
                    $gestactual = $CI->flujo_model->updateGestionActual('FISCALIZACION', $idgestioncobro['COD_GESTION_COBRO'], $codfiscalizacion, "$tipogestion");
                } else {
                    echo " faltan datos para actualizar la fiscalización";
                }
            }

            ///_ " ELIMINAR EL DISPARADOR DE RECORDATORIO PARA LA GESTIÓN ACTUAL. ";
            $CI->flujo_model->noDispararRecordatorios($codfiscalizacion, $tipogestion);
            ///_ " BUSCAR LA SIGUIENTE GESTIÓN SEGÚN EL FLUJO. ";
            $tipogestion = $CI->flujo_model->siguienteGestion($tipogestion, $tiporespuesta);
            if (!empty($tipogestion)) {
                ///_ " PONER RECORDATORIO PARA LA SIGUIENTE GESTIÓN. ";
                $resRecordar = $tf->poneRecordar($idgestioncobro['COD_GESTION_COBRO'], $tipogestion['GESTIONDESTINO'], $tipogestion['RTADESTINO'], '', $usuariosAdicionales, $codfiscalizacion);
            }
        }
        return $idgestioncobro;
    } else {
        redirect(base_url() . 'index.php/auth/login');
    }
}

/**
  Funci?n enviarcorreosena.
 * Enviar un correo desde la cuenta del SENA a un destino especificado
 *
 * Requiere invocar el helper: $this->load->helper('traza_fecha');
 * Se instancia directamente: enviarcorreosena('juanito@sena.com','Hola estimado amigo');
 * En php.ini debe estar habilitado el openssl para cuentas SSL (como Gmail).
 *
 * @param String $correousuario: Direcci?n de de correo electr?nico del destino.
 * @param String $mensaje: Mensaje a enviar en el correo electr?nico.
 * @param string $asunto: (opcional). Asunto del correo a enviar
 * @param String $copia: (opcional). CC a qui?n se env?a el mensaje.
 * @param String $adjunto (opcional). Ruta del archivo a enviar.
 *        Array  $adjunto (opcional). Arreglo de las rutas de los archivos a enviar.
 * @param String $copiaoculta: (opcional). CCO a qui?n se env?a el mensaje.
 * @param String $html: (opcional). S: Enviar el correo con la cabecera y estilo HTML del Aplicativo.
 *                                  N: Enviar texto plano.
 * @return boolean $enviado: Informa si fue o no enviado el correo
 */
function enviarcorreosena($correousuario, $mensaje, $asunto = "", $copia = "", $adjunto = "", $copiaoculta = "", $html = "S") {//echo " ".$correousuario.",".$mensaje.",".$asunto."=,".$copia." ".$adjunto." ".$copiaoculta." ";
    $CI = get_instance();
    if ($CI->ion_auth->logged_in()) {
        $CI->load->model('correo_model');
        $fila = $CI->correo_model->traeParametrosCorreo();    //echo " <br>SS:".$fila['SERVIDOR_SMTP'];         //echo " <br>PS:".$fila['PUERTO_SMTP'];   //echo " <br>CE:".$fila['CORREO_ELECTRONICO'];    //echo " <br>PW:".$fila['PASSWORD'];
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = $fila['SERVIDOR_SMTP']; //'ssl://smtp.googlemail.com';
        $config['smtp_port'] = $fila['PUERTO_SMTP']; //'465';
        $config['smtp_user'] = $fila['CORREO_ELECTRONICO']; //'correo saliente: carterasena@gmail.com';
        $config['smtp_pass'] = $fila['PASSWORD']; //'7demarzo';
        $config['starttls'] = TRUE;
        $config['newline'] = "\r\n";
        $config['wordwrap'] = TRUE;
        if ($html == "N") {
            $config['mailtype'] = 'text';
        } else {
            $config['mailtype'] = 'html';
        }//$config['charset'] = 'iso-8859-1';
        $CI->email->initialize($config);
        $CI->load->library('email');
        $CI->email->clear(true);
        $CI->email->from($fila['CORREO_ELECTRONICO'], 'A.R.Ca. Aplicativo de Recaudo y CArtera - SENA');
        $CI->email->to($correousuario);
        if (!empty($copia)) {
            $CI->email->cc($copia);
        }
        if (!empty($copiaoculta)) {
            $CI->email->bcc($copiaoculta);
        }
        if (empty($asunto)) {
            $asunto = 'SENA.';
        }
        $asunto = substr($asunto, 0, 50); //Codeigniter permite un límite de carateres en el asunto.
        $CI->email->subject($asunto);
        $fechas = "";
        $base = base_url();

        if ($html != "N") {
            $mensaje = '<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40"><head><meta http-equiv=Content-Type content="text/html; charset=iso-8859-1"><meta name=Generator content="Microsoft Word 14 (filtered medium)"><!--[if !mso]><style>v\:* {behavior:url(#default#VML);}
    o\:* {behavior:url(#default#VML);}
    w\:* {behavior:url(#default#VML);}.shape {behavior:url(#default#VML);}</style><![endif]--><style><!--@font-face{font-family:Helvetica;panose-1:2 11 6 4 2 2 2 2 2 4;}@font-face{font-family:Helvetica;panose-1:2 11 6 4 2 2 2 2 2 4;}@font-face{font-family:Calibri;panose-1:2 15 5 2 2 2 4 3 2 4;}@font-face{font-family:Tahoma;panose-1:2 11 6 4 3 5 4 4 2 4;}p.MsoNormal, li.MsoNormal, div.MsoNormal{margin:0cm;margin-bottom:.0001pt;font-size:11.0pt;font-family:"Calibri","sans-serif";mso-fareast-language:EN-US;}a:link, span.MsoHyperlink{mso-style-priority:99;color:blue;text-decoration:underline;}a:visited, span.MsoHyperlinkFollowed{mso-style-priority:99;color:purple;text-decoration:underline;}p.MsoAcetate, li.MsoAcetate, div.MsoAcetate{mso-style-priority:99;mso-style-link:"Texto de globo Car";margin:0cm;margin-bottom:.0001pt;font-size:8.0pt;font-family:"Tahoma","sans-serif";mso-fareast-language:EN-US;}span.TextodegloboCar{mso-style-name:"Texto de globo Car";mso-style-priority:99;mso-style-link:"Texto de globo";font-family:"Tahoma","sans-serif";}span.EstiloCorreo19{mso-style-type:personal-compose;font-family:"Calibri","sans-serif";color:windowtext;}.MsoChpDefault{mso-style-type:export-only;font-size:10.0pt;}@page WordSection1{size:612.0pt 792.0pt;margin:70.85pt 3.0cm 70.85pt 3.0cm;}div.WordSection1{page:WordSection1;}--></style><!--[if gte mso 9]><xml><o:shapedefaults v:ext="edit" spidmax="1026" /></xml><![endif]--><!--[if gte mso 9]><xml><o:shapelayout v:ext="edit"><o:idmap v:ext="edit" data="1" /></o:shapelayout></xml><![endif]--></head><body lang=ES-CO link=blue vlink=purple><div class=WordSection1><table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0 style="background:#FC7323;border-collapse:collapse"><tr style="height:73.4pt" ><td width=107 valign=top style="width:80.0pt;border:none;border-left:solid windowtext 1.0pt;padding:0cm 5.4pt 0cm 5.4pt;height:73.4pt"><p class=MsoNormal><span style=\'font-family:"Helvetica","sans-serif";color:white;mso-fareast-language:ES-CO\'><img width=88 height=89 id="_x0030__x0020_Imagen" src="' . $base . 'img/ff.png"  alt="SERVICIO NACIONAL DE APRENDIZAJE"></span><span style=\'font-family:"Helvetica","sans-serif";color:#EEEEEE\'><o:p></o:p></span></p></td><td width=624 valign=top style=\'width:467.75pt;padding:0cm 5.4pt 0cm 5.4pt;height:73.4pt;vertical-align:middle;\'><p class=MsoNormal><span style=\'font-size:22.0pt;font-family:"Helvetica","sans-serif";color:white\'>SISTEMA DE INFORMACI&Oacute;N DE RECAUDO,<br>CARTERA Y COBRO<o:p></o:p></span></p></td></tr><tr style=\'height:33.55pt\'><td width=730 colspan=2 valign=top style=\'width:547.75pt;border:none;border-left:solid windowtext 1.0pt;background:#F2F2F2;padding:0cm 5.4pt 0cm 5.4pt;height:33.55pt\'><p class=MsoNormal><span style=\'font-size:12.0pt;font-family:Helvetica,Sans-serif,Tahoma,Arial\'>
' . $mensaje . '<span style=\'color:white\'><o:p></o:p></span></span></p></td></tr><tr style=\'height:13.45pt\'><td width=730 colspan=2 valign=top style=\'width:547.75pt;border:none;border-left:solid windowtext 1.0pt;background:#D9D9D9;padding:0cm 5.4pt 0cm 5.4pt;height:13.45pt\'><p class=MsoNormal align=center style=\'text-align:center\'>SERVICIO NACIONAL DE APRENDIZAJE &#8226; SENA. &#8226;<span style=\'font-size:28.0pt;color:white\'><o:p></o:p></span></p></td></tr></table><p class=MsoNormal><o:p>&nbsp;</o:p></p></div></body></html>';
        }

        $CI->email->message($mensaje);
        if (is_string($adjunto)) {
            if (!empty($adjunto)) {//echo " ".++$i.$adjunto;
                $CI->email->attach($adjunto);
            }
        } else if (is_array($adjunto)) {
            if (!empty($adjunto)) {//echo " ".++$i.$adjunto;
                foreach ($adjunto as $val) {
                    $CI->email->attach($val); //$CI->email->attach($adjunto);
                }
            }
        }
        $enviado = $CI->email->send(); //echo $CI->email->print_debugger();
        return $enviado;
    } else {
        redirect(base_url() . 'index.php/auth/login');
    }
}

class Traza_fecha_helper extends MY_Controller {

    private $objFecha;
    private $arrMeses = array("", "Ene", "Feb", "Mar", "Abr", "Mayo", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");

    function __construct() {
        parent::__construct();
        $this->load->model("calendariofestivo_model");
        $this->data['javascripts'] = array('js/editarCal.js');
    }

    function datePicker($aaaa = 0) {
        if ($this->ion_auth->logged_in()) {
            $this->data['aaaa'] = $aaaa;
            $this->data['arrfestivos'] = json_encode($this->arrfestivos($aaaa));
            $this->load->view("calendario_festivo/datepicker", $this->data);
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo arrfestivos
     * Genera un arreglo con los d?as no h?biles
     * @author Felipe R. Puerto :: Thomas MTI
     * @since 3 II 2014
     * @param $aaaa: A?o del que se consulta el calendario.
     */

    function arrfestivos($aaaa = 0) {
        if ($this->ion_auth->logged_in()) {
            $arrFestivos = array();
            if ($aaaa == 0) {
                $aaaa = date("Y");
            }
            $festivosanuales = $this->traerfestivoarreglo($aaaa);
            $objMes = new DateTime(($aaaa - 1) . "-1-1");
            for ($i = 0; ($objMes->format("Y") < ($aaaa + 2)); $i++) {
                $this->ponerFecha($objMes->format("Y"), $objMes->format("m"), $objMes->format("d"));
                $esFestivo = $this->esFestivoArreglo($festivosanuales);
                if ($esFestivo === true || $esFestivo === 1) {
                    $arrFestivos[] = $this->objFecha->format("Y-n-j");
                }
                $objMes->modify("+1 day");
            }
            return $arrFestivos;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo scriptFestivos
     * Genera un c?digo javascript para el datepicker con los d?as no h?biles
     * @author Felipe R. Puerto :: Thomas MTI
     * @since 3 II 2014
     * @param $aaaa: A?o del que se consulta el calendario.
     */

    function scriptfestivos($aaaa = 0) {
        if ($this->ion_auth->logged_in()) {
            $arrfestivos = json_encode($this->arrfestivos($aaaa));
            $jscript = "
/* (basado en http://davidwalsh.name/jquery-datepicker-disable-days)
Funci?n bloquearFestivos:
Informa los d?as festivos de Colombia a datepicker para que no se puedan seleccionar.
Entre los par?metros de
jQuery('#campo_de_fecha').datepicker({ });
agregue:
beforeShowDay: bloquearFestivos
*/
function bloquearFestivos(date){
var diaInhabil = eval($arrfestivos)
var d = date.getDate(), m = date.getMonth(), y = date.getFullYear();
for (i = 0; i < diaInhabil.length; i++) {
if($.inArray( y+ '-' + (m+1) + '-' + d ,diaInhabil) != -1 || new Date() > date) {
return [false];
}
}
return [true];
}";
            return $jscript;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /**
     * Function poneBloqueo
     * Verifica si la gesti?n indicada tiene un bloqueo asignado y lo lanza.
     * @author Felipe R. Puerto :: THOMAS MTI (2014)
     *
     * @param Integer $casoFiscalizacion: C?d ?nico para recordar el bloqueo del caso por BD.
     * @param Integer $gestion: C?digo de Gesti?n para revisar si tiene bloqueo
     * @param Object $cc: Objeto de fechas y calendario para obtener los festivos y h?biles
     * @param type $tipo: 'pos' para bloqueos.
     * @param type $opc: En desuso. Se usaba para la tabla RECORDATORIO
     * @param type $mostrar: SI: muestra una ventana de aviso, redireccionable, NO: solo env?a un arreglo json
     * @param type $si: URL a la que debe ir en modo mostrar SI al dar clic en desbloquear
     * @param type $no: URL a la que debe ir en modo mostrar SI al dar clic cuando se ha vencido el tiempo
     * @param type $parametros: Se enviar?n a la URL de reenv?o. Formato nombre:valor;nombre2:valor2
     * @param type $BD: si est? en BD almacenar? y consultar? en la BD si est? bloqueado y sus fechas.
     * @return Array $bloqueo: Arreglo que contiene fechas, texto, c?d...
     */
    function poneBloqueo($casoFiscalizacion = '', $codjudicial = '', $codcoactivo = '', $coddevolucion = '', $codrecepcion = '', $codnomisional = '', $gestion, $cc, $tipo = 'pos', $opc = '', $mostrar = "NO", $si = '', $no = '', $parametros = '', $BD) {
        if ($this->ion_auth->logged_in()) {
            $this->data['user'] = $this->ion_auth->user()->row();
            $CI = get_instance();
            $CI->load->model('flujo_model');
            $dato = $CI->flujo_model->buscaRecordatorio($gestion, $tipo, $opc);

            $matriz = $dato; //$matriz = $dato->result_array(); S?lo se va a trabajar con una fila.
//            print_r($matriz);
//            die;
            $bloqueo['bandera'] = $ff = 0; //Cantidad de resultados (de bloqueos en la BD)
            $bloqueo['si'] = $si;
            $bloqueo['no'] = $no;
            $bloqueo['mostrar'] = $mostrar;
            if (count($dato) > 0) { //Hay bloqueos que deben asignarse a esta actividad
                $fecha = $cc->traerFecha();
//
//                echo "<pre>**";
//            print_r($matriz);
//            echo "</pre>**";
                foreach ($matriz as $recordatorio) {
                    $ff++;
                    $codrecordatorio = $recordatorio['CODRECORDATORIO'];
                    $fechaVencimiento = $this->sumarDiasHabiles($fecha, $recordatorio['TIEMPO_NUM'], $recordatorio['TIEMPO_MEDIDA']);
                    $bloqueo['id'] = 0;

////////
                    if ($BD == "BD" || $BD == "DB") { //Utilizar la base de datos como apoyo para almacenar y traer los bloqueos y sus fechas
                        $datosDisparador = $CI->flujo_model->poneDisparaFecha($casoFiscalizacion, $codjudicial, $codcoactivo, $coddevolucion, $codrecepcion, $codnomisional, $codrecordatorio, $fecha, $fechaVencimiento);
                        $id = $datosDisparador['id'];
                        $fecha = $datosDisparador['fechaCreado'];
                        $fechaVencimiento = $datosDisparador['fechaVencimiento'];
                        $bloqueo['id'] = $id;
                    }

                    $bloqueo['casofiscalizacion'] = $casoFiscalizacion;
                    $bloqueo['gestion'] = $gestion;
                    $bloqueo['recordatorio'] = $codrecordatorio;

                    $informacion = $CI->flujo_model->informacion_bloqueo($gestion, $codrecordatorio);

                    $reemplazo = array();
                    $reemplazo['APELLIDOS'] = $this->data['user']->APELLIDOS;
                    $reemplazo['NOMBRES'] = $this->data['user']->NOMBRES;
                    $reemplazo['NOMBREPROCESO'] = '';
                    $reemplazo['NOMBRERESPUESTA'] = '';
                    $reemplazo['FECHACREADO'] = $this->sumarDiasHabiles($fecha, 0, 'd');
                    $reemplazo['FECHAVENCIMIENTO'] = $this->sumarDiasHabiles($fechaVencimiento, 0, 'd');
                    $reemplazo['URLGESTION'] = '';
                    $reemplazo['CODGESTIONCOBRO'] = $gestion;
                    $reemplazo['TIEMPONUM'] = $recordatorio['TIEMPO_NUM'];
                    if ($recordatorio['TIEMPO_MEDIDA'] == 'd') {
                        $medida = 'Dias';
                    } else if ($recordatorio['TIEMPO_MEDIDA'] == 'm') {
                        $medida = 'Meses';
                    } else if ($recordatorio['TIEMPO_MEDIDA'] == 'a') {
                        $medida = 'Años';
                    } else if ($recordatorio['TIEMPO_MEDIDA'] == 'dc') {
                        $medida = 'Dias Calendario';
                    } else if ($recordatorio['TIEMPO_MEDIDA'] == 's') {
                        $medida = 'Semanas';
                    }
                    $reemplazo['TIEMPOMEDIDA'] = $medida;
                    $reemplazo['CODFISCALIZACION'] = $casoFiscalizacion;
                    foreach ($informacion as $col) {
                        $reemplazo['TIPOGESTION'] = $col['TIPOGESTION'];
                    }
                    $texto = $recordatorio['TEXTO'];
                    foreach ($reemplazo as $llave => $valor) {
                        $texto = str_replace('%' . $llave . '%', $valor, $texto);
                    }
                    $bloqueo['texto'] = $texto;
                    $bloqueo['comienza'] = $this->sumarDiasHabiles($fecha, 0, 'd');
                    $bloqueo['vence'] = $this->sumarDiasHabiles($fechaVencimiento, 0, 'd');
                    $bloqueo['parametros'] = $parametros;

////////

                    $fechaV = explode("-", $fechaVencimiento);
                    $fechaC = explode("-", $fecha);
                    $fecComienzo = mktime(0, 0, 0, $fechaC[1], $fechaC[2], $fechaC[0]);
                    $fecVence = mktime(0, 0, -1, $fechaV[1], $fechaV[2] + 1, $fechaV[0]); //Se suma 1 para que se venza a las 00:00 del d?a siguiente y -1 segundo
                    $bloqueo['vencido'] = 0;
                    if ($fecComienzo <= time() && time() < $fecVence) {
                        $bloqueo['vencido'] = 1; //Est? en tiempo de bloqueo
                    } else if (time() > $fecVence) {
                        $bloqueo['vencido'] = 2; //se pas? el tiempo de bloqueo
                    }
                }
            }
            $bloqueo['bandera'] = $ff;
            if ($ff == 0) {
                $bloqueo['adv'] = "NO_HAY_BLOQUEOS_REGISTRADOS_PARA_ESTA_GESTION ";
            }
            return $bloqueo; //return 0;
            //echo $bloqueo;die;
            //print_r($bloqueo);die;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /**
     * Function poneRecordar
     * R?plica modificada de poneBloqueo
     * Verifica si la gesti?n indicada tiene un recordatorio asignado y lo lanza.
     * @author Felipe R. Puerto :: THOMAS MTI (2014)
     *
     * @param Integer $codgestioncobro: C?d ?nico de la tabla gestion cobro
     * @param Integer $tipogestion: C?d ?nico de la tabla gestion cobro
     * @param Integer $casoFiscalizacion: C?d ?nico para recordar el bloqueo del caso por BD.
     * @param Integer $gestion: C?digo de Gesti?n para revisar si tiene bloqueo
     * @param Object $cc: Objeto de fechas y calendario para obtener los festivos y h?biles
     * @param type $respuesta: En desuso. Se usaba para la tabla RECORDATORIO
     * @return Array $bloqueo: Arreglo que contiene fechas, texto, c?d...
     */
    function poneRecordar($codgestioncobro, $tipogestion, $respuesta = '', $fecha = '', $usuariosAdicionales = '', $codfiscalizacion = '') {
        if ($this->ion_auth->logged_in()) {
            $CI = get_instance();
            $CI->load->model('flujo_model');
            $tipo = 'pre';
            ///_ " BUSCAR SI LA GESTIÓN ACTUAL TIENE RECORDATORIOS ASIGNADOS. ";
            $dato = $CI->flujo_model->buscaRecordatorio($tipogestion, $tipo, $respuesta);
            $bloqueo['bandera'] = $ff = 0;
            if (count($dato) > 0) {
                foreach ($dato as $recordatorio) {
                    $ff++;
                    $codrecordatorio = $recordatorio['CODRECORDATORIO'];  //$fecha = $cc->traerFecha();
                    if (empty($fecha)) {
                        $fecha = date("Y-m-d");
                    }
                    $fechaVencimiento = $this->sumarDiasHabiles($fecha, $recordatorio['TIEMPO_NUM'], $recordatorio['TIEMPO_MEDIDA']);
                    $bloqueo['id'] = 0;
                    ///_ " VERIFICAR SI EL RECORDATORIO YA ESTÁ REGISTRADO PARA DISPARAR. ";
                    $matriz = $CI->flujo_model->yaHayPoneRecordatorio($codrecordatorio, $codgestioncobro);
                    if (count($matriz) > 0) {
                        $datosDisparador['id'] = $matriz[0]['CODDISPARARECORDATORIO'];
                        $datosDisparador['fechaCreado'] = $matriz[0]['FECHA_CREACION'];
                        $datosDisparador['fechaVencimiento'] = $matriz[0]['FECHA_VENCIMIENTO'];
                    }
                    if (count($matriz) == 0) {
                        ///_ " REGISTRAR EL DISPARADOR DEL RECORDATORIO. ";
                        $CI->flujo_model->poneDisparaRecordatorio($codgestioncobro, $codrecordatorio, $fecha, $fechaVencimiento);
                        $datosDisparador['id'] = $CI->flujo_model->traeDisparaRecordatorio($codgestioncobro, $codrecordatorio, $fecha, $fechaVencimiento);
                        $arrPar['CODGESTIONCOBRO'] = $arrPar['COD_GESTION_COBRO'] = $codgestioncobro;
                        $arrPar['FECHACREADO'] = $arrPar['FECHA_CREADO'] = $datosDisparador['fechaCreado'] = $fecha;
                        $arrPar['FECHAVENCIMIENTO'] = $arrPar['FECHA_VENCIMIENTO'] = $datosDisparador['fechaVencimiento'] = $fechaVencimiento;
                        if (!empty($codfiscalizacion)) {
                            $arrPar['CODFISCALIZACION'] = $codfiscalizacion;
                        } else if (!empty($arrPar)) {
                            $arrPar['CODFISCALIZACION'] = $CI->flujo_model->traeFiscalizacion($arrPar);
                        }
                        $regional = $CI->flujo_model->traeRegional($arrPar['CODFISCALIZACION']);
                        $abogadoAsignado = $CI->flujo_model->traeAbogado($arrPar['CODFISCALIZACION']);
                        if (!empty($abogadoAsignado)) {
                            $usuariosAdicionales .= "," . $abogadoAsignado; //Agrega el abogado asignado a la lista de usuarios que recibirán el correo
                        }
                        ///_ " CONSULTAR LOS USUARIOS A LOS QUE SE LES REPORTARÁ EL RECORDATORIO. ";
                        $dato = $CI->flujo_model->traeRecUsuarios($datosDisparador['id'], $usuariosAdicionales, $regional);
                        if ($dato->num_rows() > 0) {
                            foreach ($dato->result_array() as $fila) {
                                $arrPar['TIEMPONUM'] = $arrPar['TIEMPO_NUM'] = $recordatorio['TIEMPO_NUM'];
                                $arrPar['TIEMPOMEDIDA'] = $arrPar['TIEMPO_MEDIDA'] = $recordatorio['TIEMPO_MEDIDA'];
                                $arrPar['TIPOGESTION'] = $arrPar['TIPO_GESTION'] = $fila['TIPOGESTION'];
                                $arrPar['NOMBRES'] = $fila['NOMBRES'];
                                $arrPar['APELLIDOS'] = $fila['APELLIDOS'];
                                $arrPar['NOMBRERESPUESTA'] = $arrPar['NOMBRE_RESPUESTA'] = $fila['NOMBRE_GESTION'];
                                $arrPar['NOMBREPROCESO'] = $fila['NOMBREPROCESO'];
                                $arrPar['URLGESTION'] = $fila['URLGESTION'];
                                $texto = $fila['TEXTO'];
                                foreach ($arrPar as $llave => $valor) {
                                    $texto = str_replace('%' . $llave . '%', $valor, $texto);
                                }
                                $texto = str_replace("'", "''", $texto);
                                ///_ " PONER DISPARADOR DE RECORDATORIO. ";
                                $CI->flujo_model->poneDisparaRecUsuarios($fila, $texto);
                                if ($fila['RECORDATORIO_CORREO'] == 'c' || $fila['RECORDATORIO_CORREO'] == 'C') {
                                    enviarcorreosena($fila['EMAIL'], $texto, "Actividad pendiente por realizar. SENA.");
                                }
                            }
                        }
                    }

                    $id = $datosDisparador['id'];
                    $fecha = $datosDisparador['fechaCreado'];
                    $fechaVencimiento = $datosDisparador['fechaVencimiento'];
                    $bloqueo['id'] = $id;
                    $bloqueo['codgestioncobro'] = $codgestioncobro;
                    $bloqueo['tipogestion'] = $tipogestion;
                    $bloqueo['recordatorio'] = $codrecordatorio;
                    $bloqueo['texto'] = $recordatorio['TEXTO'];
                    $bloqueo['comienza'] = $this->sumarDiasHabiles($fecha, 0, 'd');
                    $bloqueo['vence'] = $this->sumarDiasHabiles($fechaVencimiento, 0, 'd');
                    //$bloqueo['parametros'] = $parametros;
                }
            }
            $bloqueo['bandera'] = $ff;  //print_r($bloqueo);
            return $bloqueo; //return 0;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /**
     * Function poneRecordarJudicial
     * R?plica modificada de poneRecordarJudicial
     * Verifica si la gesti?n indicada tiene un recordatorio asignado y lo lanza.
     * @since 22 IV 2014
     * @author Felipe R. Puerto :: THOMAS MTI (2014)
     *
     * @return Array $bloqueo: Arreglo que contiene fechas, texto, c?d...
     */
    function poneRecordarJudicial($codgestioncobro, $tipogestion, $respuesta = '', $fecha = '', $usuariosAdicionales = '', $codtitulo = '') {
        if ($this->ion_auth->logged_in()) {
            $CI = get_instance();
            $CI->load->model('flujo_model');
            $tipo = 'pre';
            ///_ " BUSCAR SI LA GESTIÓN ACTUAL TIENE RECORDATORIOS ASIGNADOS. ";
            $dato = $CI->flujo_model->buscaRecordatorio($tipogestion, $tipo, $respuesta);
            $bloqueo['bandera'] = $ff = 0;
            if (count($dato) > 0) {
                foreach ($dato as $recordatorio) {
                    $ff++;
                    $codrecordatorio = $recordatorio['CODRECORDATORIO'];  //$fecha = $cc->traerFecha();
                    if (empty($fecha)) {
                        $fecha = date("Y-m-d");
                    }
                    $fechaVencimiento = $this->sumarDiasHabiles($fecha, $recordatorio['TIEMPO_NUM'], $recordatorio['TIEMPO_MEDIDA']);
                    $bloqueo['id'] = 0;
                    ///_ " VERIFICAR SI EL RECORDATORIO YA ESTÁ REGISTRADO PARA DISPARAR. ";
                    $matriz = $CI->flujo_model->yaHayPoneRecordatorio($codrecordatorio, $codgestioncobro);
                    if (count($matriz) > 0) {
                        $datosDisparador['id'] = $matriz[0]['CODDISPARARECORDATORIO'];
                        $datosDisparador['fechaCreado'] = $matriz[0]['FECHA_CREACION'];
                        $datosDisparador['fechaVencimiento'] = $matriz[0]['FECHA_VENCIMIENTO'];
                    }
                    if (count($matriz) == 0) {
                        //echo " REGISTRAR EL DISPARADOR DEL RECORDATORIO. ";
                        $CI->flujo_model->poneDisparaRecordatorio($codgestioncobro, $codrecordatorio, $fecha, $fechaVencimiento);
                        $datosDisparador['id'] = $CI->flujo_model->traeDisparaRecordatorio($codgestioncobro, $codrecordatorio, $fecha, $fechaVencimiento);
                        $arrPar['CODGESTIONCOBRO'] = $arrPar['COD_GESTION_COBRO'] = $codgestioncobro;
                        $arrPar['FECHACREADO'] = $arrPar['FECHA_CREADO'] = $datosDisparador['fechaCreado'] = $fecha;
                        $arrPar['FECHAVENCIMIENTO'] = $arrPar['FECHA_VENCIMIENTO'] = $datosDisparador['fechaVencimiento'] = $fechaVencimiento;
                        $arrPar['CODFISCALIZACION'] = $codtitulo;
                        $abogadoAsignado = $CI->flujo_model->traeAbogadoJudicial($arrPar['CODFISCALIZACION']);
                        if (!empty($abogadoAsignado)) {
                            $usuariosAdicionales .= "," . $abogadoAsignado;
                        }
                        ///_ " CONSULTAR LOS USUARIOS A LOS QUE SE LES REPORTARÁ EL RECORDATORIO. ";
                        $dato = $CI->flujo_model->traeRecUsuarios($datosDisparador['id'], $usuariosAdicionales);
                        if ($dato->num_rows() > 0) {
                            foreach ($dato->result_array() as $fila) {
                                $arrPar['TIEMPONUM'] = $arrPar['TIEMPO_NUM'] = $recordatorio['TIEMPO_NUM'];
                                $arrPar['TIEMPOMEDIDA'] = $arrPar['TIEMPO_MEDIDA'] = $recordatorio['TIEMPO_MEDIDA'];
                                $arrPar['TIPOGESTION'] = $arrPar['TIPO_GESTION'] = $fila['TIPOGESTION'];
                                $arrPar['NOMBRES'] = $fila['NOMBRES'];
                                $arrPar['APELLIDOS'] = $fila['APELLIDOS'];
                                $arrPar['NOMBRERESPUESTA'] = $arrPar['NOMBRE_RESPUESTA'] = $fila['NOMBRE_GESTION'];
                                $arrPar['NOMBREPROCESO'] = $fila['NOMBREPROCESO'];
                                $arrPar['URLGESTION'] = $fila['URLGESTION'];
                                $texto = $fila['TEXTO'];
                                foreach ($arrPar as $llave => $valor) {
                                    $texto = str_replace('%' . $llave . '%', $valor, $texto);
                                }
                                $texto = str_replace("'", "''", $texto);
                                ///_ " PONER DISPARADOR DE RECORDATORIO. ";
                                $CI->flujo_model->poneDisparaRecUsuarios($fila, $texto);
                                if ($fila['RECORDATORIO_CORREO'] == 'c' || $fila['RECORDATORIO_CORREO'] == 'C') {
                                    enviarcorreosena($fila['EMAIL'], $texto, "[" . $arrPar['TIPOGESTION'] . "] SENA. Pendientes");
                                }
                            }
                        }
                    }
                    $id = $datosDisparador['id'];
                    $fecha = $datosDisparador['fechaCreado'];
                    $fechaVencimiento = $datosDisparador['fechaVencimiento'];
                    $bloqueo['id'] = $id;
                    $bloqueo['codgestioncobro'] = $codgestioncobro;
                    $bloqueo['tipogestion'] = $tipogestion;
                    $bloqueo['recordatorio'] = $codrecordatorio;
                    $bloqueo['texto'] = $recordatorio['TEXTO'];
                    $bloqueo['comienza'] = $this->sumarDiasHabiles($fecha, 0, 'd');
                    $bloqueo['vence'] = $this->sumarDiasHabiles($fechaVencimiento, 0, 'd');
                    //$bloqueo['parametros'] = $parametros;
                }
            }
            $bloqueo['bandera'] = $ff;  //print_r($bloqueo);
            return $bloqueo; //return 0;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    function index2() {
        if ($this->ion_auth->logged_in()) {
            $this->data['calendario'] = $this;
            $ahora = new DateTime();
            $aaaa = $this->input->post('anno');
            if (empty($aaaa)) {
                $aaaa = intval($ahora->format("Y"));
            } else {
                $aaaa = $this->input->post('anno');
            }
            $mm = $this->input->post('mes');
            if (!empty($mm)) {
                $mm = $this->input->post('mes');
            } else {
                $mm = 0;
            }
            $this->data['aaaa'] = $aaaa;
            $this->data['mm'] = $mm;
            $this->data['mostrarmes'] = $this->desplegarCalendario($aaaa, $mm);
            $this->data['admin'] = 1;
            $this->template->set("title", "Calendario festivo");
            $this->load->view("calendario_festivo/calendario_festivo_home", $this->data);
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

//PARA CODEIGNITER
    function desplegarCalendario($aaaa, $mm) {
        if ($this->ion_auth->logged_in()) {
            $festivosanuales = $this->traerfestivoarreglo($aaaa);
            $mos = "";
            if ($mm == 0) {
                for ($i = 1; $i <= 12; $i++) {
                    if ($i == 7) {
                        $mos .= "</td></tr><tr><td>";
                    } //$mos.= $this->mostrarMes($aaaa,$i);
                    $mos .= $this->mostrarMesArr($festivosanuales, $aaaa, $i);
                }
            } else {  //echo $ahora->format("Y-m-d H:i:s");//$mos.= $this->mostrarMes($aaaa,$mm);
                $mos .= $this->mostrarMesArr($festivosanuales, $aaaa, $mm);
            }
            return $mos;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

//PARA CODEIGNITER
    function forzar() {
        if ($this->ion_auth->logged_in()) {
            $v = $this->input->post('v');
            $o = $this->input->post('o');
            $f = explode("-", $v);

            $num_rows = $this->calendariofestivo_model->festivo3fechas($f[0], $f[1], $f[2]);

            if ($num_rows > 0) {
                $num = 1; //Existe, entonces no hay que insertar sino actualizarlo
            } else {
                $num = 0;
            }
            switch ($o) {
                case "opc1":
                    $this->calendariofestivo_model->ponerfestivo($num, $f[0], $f[1], $f[2], 1);
                    break;
                case "opc2":
                    $this->calendariofestivo_model->ponerfestivo($num, $f[0], $f[1], $f[2], 2);
                    break;
                case "opc3":
                    $this->calendariofestivo_model->borrarfestivo($num, $f[0], $f[1], $f[2]);
                    break;
            }
            echo $o;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /* M?todo ponerFecha: establece la fecha con la que se va a trabajar con la instancia del objeto
      @author F. Ricardo Puerto :: Thomas MTI
      @param $aaaa: a?o de 4 d?gitos
      @param $mm n?mero del mes
      @param $dd d?a de la fecha */

    function ponerFecha($aaaa = NULL, $mm = NULL, $dd = NULL) {
        if ($this->ion_auth->logged_in()) {
            if (empty($aaaa))
                $aaaa = date("Y");
            if (empty($mm))
                $mm = date("m");
            if (empty($dd))
                $dd = date("d");
            $this->objFecha = new DateTime($aaaa . "-" . $mm . "-" . $dd);
        }else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    function traerFecha() {
        if ($this->ion_auth->logged_in()) {
            $fechatexto = $this->objFecha->format("Y-m-d");
            return $fechatexto;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /* M?todo diaFestivo: verifica si un d?a dado es festivo en Colombia
      @author F. Ricardo Puerto :: Thomas MTI
      @since 30 I 2014
      @return boolean Retorna true: s? es festivo false: si es d?a laboral
     */

    function diaFestivo($aaaa = '', $mm = '', $dd = '') {
        if ($this->ion_auth->logged_in()) {
            if (!empty($aaaa) && !empty($mm) && !empty($dd)) {
                $this->ponerFecha($aaaa, $mm, $dd);
            }
            if ($this->esFestivo() === 1 || $this->esFestivo() === true) {
                return true;
            } else {
                return false;
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /* M?todo esFestivo: verifica si un d?a dado es festivo en Colombia
      @author F. Ricardo Puerto :: Thomas MTI
      @return Retorna 1: s? es festivo forzado por BD,
     * 2: s? no es festivo forzado por BD,
     * true: si es normalmente es festivo
     */

    function esFestivo() {
        if ($this->ion_auth->logged_in()) {
            $festivoBD = $this->festivobd();
            if ($festivoBD == 1) {
                return 1;
            } else if ($festivoBD == 2) {
                return 2;
            }
            $festivo = 0;

            if ($this->objFecha->format('N') == 7) {
                return true;
            }//domingo
//S?BADO SENA
            if ($this->objFecha->format('N') == 6) {
                return true;
            }//s?bado
//festivos fijos sin importar el d?a de la semana:
            else if ($this->objFecha->format('m') == 1 && $this->objFecha->format('d') == 1) {
                return true;
            } else if ($this->objFecha->format('m') == 5 && $this->objFecha->format('d') == 1) {
                return true;
            } else if ($this->objFecha->format('m') == 7 && $this->objFecha->format('d') == 20) {
                return true;
            } else if ($this->objFecha->format('m') == 8 && $this->objFecha->format('d') == 7) {
                return true;
            } else if ($this->objFecha->format('m') == 12 && $this->objFecha->format('d') == 8) {
                return true;
            } else if ($this->objFecha->format('m') == 12 && $this->objFecha->format('d') == 25) {
                return true;
            }
//Lunes festivos
            else if ($this->objFecha->format('N') == 1) {
                if ($this->emiliani($this->objFecha->format('Y'), 1, 6)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 3, 19)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 6, 29)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 8, 15)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 10, 12)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 11, 1)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 11, 11)) {
                    return true;
                }
//lunes festivos dependientes de semana santa
                if ((intval($this->objFecha->format('m')) == 5 || intval($this->objFecha->format('m')) == 6 || intval($this->objFecha->format('m')) == 7)) {
                    $festivo = $this->butcher($this->objFecha->format('Y'));

                    $festivo->modify("+43 day"); //asc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }

                    $festivo->modify("+21 day"); //cc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }

                    $festivo->modify("+7 day"); //sc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }
                }
            } else if (($this->objFecha->format('N') == 4 || $this->objFecha->format('N') == 5) && (intval($this->objFecha->format('m')) == 3 || intval($this->objFecha->format('m')) == 4)) {
//Jueves y Viernes Santo
                $festivo = $this->butcher($this->objFecha->format('Y'));
                $festivo->modify("-3 day"); //domingo de pascua -3 = jueves santo
                $intervalo = $this->objFecha->diff($festivo);
                if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && (intval($intervalo->format('%r%d')) == -1 || intval($intervalo->format('%r%d')) == 0)) {
                    return true;
                }
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /* M?todo esFestivoArreglo:
     * R?plica de esFestivo
     * verifica si un d?a dado es festivo en Colombia, consulta la BD una sola vez
      @author F. Ricardo Puerto :: Thomas MTI
      @param $aaaa: a?o de 4 d?gitos
      @param $mm n?mero del mes
      @param $dd d?a de la fecha
      @param $H Horas
      @param $i minutos
      @param $s segundos */

    function esFestivoArreglo($arreglodefestivos) {
        if ($this->ion_auth->logged_in()) {
            $festivoArr = $this->festivocoincide($arreglodefestivos);
            if ($festivoArr == 1) {
                return 1;
            } else if ($festivoArr == 2) {
                return 2;
            }
            $festivo = 0;

            if ($this->objFecha->format('N') == 7) {
                return true;
            }//domingo
//S?BADO SENA
            if ($this->objFecha->format('N') == 6) {
                return true;
            }//s?bado
//festivos fijos sin importar el d?a de la semana:
            else if ($this->objFecha->format('m') == 1 && $this->objFecha->format('d') == 1) {
                return true;
            } else if ($this->objFecha->format('m') == 5 && $this->objFecha->format('d') == 1) {
                return true;
            } else if ($this->objFecha->format('m') == 7 && $this->objFecha->format('d') == 20) {
                return true;
            } else if ($this->objFecha->format('m') == 8 && $this->objFecha->format('d') == 7) {
                return true;
            } else if ($this->objFecha->format('m') == 12 && $this->objFecha->format('d') == 8) {
                return true;
            } else if ($this->objFecha->format('m') == 12 && $this->objFecha->format('d') == 25) {
                return true;
            }
//Lunes festivos
            else if ($this->objFecha->format('N') == 1) {
                if ($this->emiliani($this->objFecha->format('Y'), 1, 6)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 3, 19)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 6, 29)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 8, 15)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 10, 12)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 11, 1)) {
                    return true;
                }
                if ($this->emiliani($this->objFecha->format('Y'), 11, 11)) {
                    return true;
                }
//lunes festivos dependientes de semana santa
                if ((intval($this->objFecha->format('m')) == 5 || intval($this->objFecha->format('m')) == 6 || intval($this->objFecha->format('m')) == 7)) {
                    $festivo = $this->butcher($this->objFecha->format('Y'));

                    $festivo->modify("+43 day"); //asc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }

                    $festivo->modify("+21 day"); //cc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }

                    $festivo->modify("+7 day"); //sc
                    $intervalo = $this->objFecha->diff($festivo);
                    if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && intval($intervalo->format('%r%d')) == 0) {
                        return true;
                    }
                }
            } else if (($this->objFecha->format('N') == 4 || $this->objFecha->format('N') == 5) && (intval($this->objFecha->format('m')) == 3 || intval($this->objFecha->format('m')) == 4)) {
//Jueves y Viernes Santo
                $festivo = $this->butcher($this->objFecha->format('Y'));
                $festivo->modify("-3 day"); //domingo de pascua -3 = jueves santo
                $intervalo = $this->objFecha->diff($festivo);
                if (intval($intervalo->format('%Y')) == 0 && intval($intervalo->format('%m')) == 0 && (intval($intervalo->format('%r%d')) == -1 || intval($intervalo->format('%r%d')) == 0)) {
                    return true;
                }
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /* seis de enero 6, marzo 19, junio 29, agosto 15, octubre 12, noviembre 1, noviembre 11 */
    /*
     * M?todo emiliani
     * Consulta los d?as del a?o que se trasladan a lunes festivo por la ley Emiliani
     * @author Felipe R. Puerto
     * @param $aaaa: A?o de la fecha original que se debe pasar a festivo.
     * @param $mm: Mes de la fecha original que se debe pasar a festivo
     * @param $dd: D?a de la fecha d?a original que se debe pasar a festivo
     * @return boolean: Verdadero si el d?a instancia es lunes festivo 'emiliani'
     * @since 2013-11-15 */

    function emiliani($aaaa, $mm, $dd) {
        if ($this->ion_auth->logged_in()) {
            $fec = $aaaa . "-" . $mm . "-" . $dd;
            $festivo = new DateTime($fec);
            $intervalo = $this->objFecha->diff($festivo);
            if (intval($intervalo->format('%r%d')) >= -6 && intval($intervalo->format('%r%d')) <= 0 && $intervalo->format('%m') == 0 && $intervalo->format('%y') == 0) {
                return true;
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo mostrarMes
     * Muestra el calendario del mes indicado o los meses del a?o
     * @author Felipe R. Puerto
     * @param $aaaa: A?o del que se consulta el calendario.
     * @param $mm: Mes del que se consulta el calendario.
     * @since 2013-11-15 */

    function mostrarMes($aaaa, $mm = 0) {
        if ($this->ion_auth->logged_in()) {
            $objMes = new DateTime($aaaa . "-" . $mm . "-1");
            $tm = intval($objMes->format("m"));
            $tY = intval($objMes->format("Y"));
            $diasInicioSemana = ($objMes->format("N")) % 7;
            $objMes->modify("-" . $diasInicioSemana . " day");
            $claseDia = "diaHabil";
            $tablaMes = "";
            $tablaMes .= "<TABLE align=left border=1><TR><TD>";
            $tablaMes .= " (" . $this->arrMeses[$tm] . " " . $tY . ") ";
            $tablaMes .= "</TD></TR><TR><TD>";
            $tablaMes .= "<table class=calendario>";
            $tablaMes .= "<tr class=cabeza><td></td><td>D</td><td>L</td><td>M</td><td>M</td><td>J</td><td>V</td><td>S</td></tr>";
            for ($i = 0; (($objMes->format("m") <= $mm && $objMes->format("Y") == $aaaa) || ($objMes->format("Y") < $aaaa)); $i++) {
                if (($objMes->format("N")) == 7) {
                    $objMes->modify("+4 day");
                    $tablaMes .= "<tr><td class=semana>" . $objMes->format("W") . "</td>";
                    $objMes->modify("-4 day");
                }
                if (intval($objMes->format("m")) < $mm || ($objMes->format("m") == 12 && $mm == 1)) {
                    $claseDia = "diaGris"; //dias finales del mes anterior
                } else {
                    $this->ponerFecha($objMes->format("Y"), $objMes->format("m"), $objMes->format("d"));
                    $esFestivo = $this->esFestivo();
                    if ($esFestivo === true) {
                        $claseDia = "diaFestivo";
                    } elseif ($esFestivo === 1) {
                        $claseDia = "diaFestivo style='font-size:75%;text-decoration:overline;font-style:italic' ";
                    } elseif ($esFestivo === 2) {
                        $claseDia = "diaHabil style='font-size:75%;text-decoration:overline;font-style:italic' ";
                    } else {
                        $claseDia = "diaHabil";
                    }
                }
                $tablaMes .= "<td class=" . $claseDia . " content='" . $objMes->format("Y-m-d") . "'>" . $objMes->format("d") . "</td>";
                if ($objMes->format("N") == 6) {
                    $tablaMes .= "</tr>";
                }
                $objMes->modify("+1 day");
            }
            $tablaMes .= "</table>";
            $tablaMes .= "</TD></TR></TABLE>";
            return $tablaMes;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo mostrarMesArr
     * R?plica de mostrarMes, que consulta solo una vez la BD
     * Muestra el calendario del mes indicado o los meses del a?o
     * @author Felipe R. Puerto
     * @param $festivosanuales Arreglo de los festivos del mes seg?n BD
     * @param $aaaa: A?o del que se consulta el calendario.
     * @param $mm: Mes del que se consulta el calendario.
     * @since 2013-11-15 */

    function mostrarMesArr($festivosanuales, $aaaa, $mm = 0) {
        if ($this->ion_auth->logged_in()) {
            $objMes = new DateTime($aaaa . "-" . $mm . "-1");
            $tm = intval($objMes->format("m"));
            $tY = intval($objMes->format("Y"));
            $diasInicioSemana = ($objMes->format("N")) % 7;
            $objMes->modify("-" . $diasInicioSemana . " day");
            $claseDia = "diaHabil";
            $tablaMes = "";
            $tablaMes .= "<TABLE align=left border=1><TR><TD>";
            $tablaMes .= " (" . $this->arrMeses[$tm] . " " . $tY . ") ";
            $tablaMes .= "</TD></TR><TR><TD>";
            $tablaMes .= "<table class=calendario>";
            $tablaMes .= "<tr class=cabeza><td></td><td>D</td><td>L</td><td>M</td><td>M</td><td>J</td><td>V</td><td>S</td></tr>";
            for ($i = 0; (($objMes->format("m") <= $mm && $objMes->format("Y") == $aaaa) || ($objMes->format("Y") < $aaaa)); $i++) {
                if (($objMes->format("N")) == 7) {
                    $objMes->modify("+4 day");
                    $tablaMes .= "<tr><td class=semana>" . $objMes->format("W") . "</td>";
                    $objMes->modify("-4 day");
                }
                if (intval($objMes->format("m")) < $mm || ($objMes->format("m") == 12 && $mm == 1)) {
                    $claseDia = "diaGris"; //dias finales del mes anterior
                } else {
                    $this->ponerFecha($objMes->format("Y"), $objMes->format("m"), $objMes->format("d"));
                    $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    if ($esFestivo === true) {
                        $claseDia = "diaFestivo";
                    } elseif ($esFestivo === 1) {
                        $claseDia = "diaFestivo style='font-size:75%;text-decoration:overline;font-style:italic' ";
                    } elseif ($esFestivo === 2) {
                        $claseDia = "diaHabil style='font-size:75%;text-decoration:overline;font-style:italic' ";
                    } else {
                        $claseDia = "diaHabil";
                    }
                }
                $tablaMes .= "<td class=" . $claseDia . " content='" . $objMes->format("Y-m-d") . "'>" . $objMes->format("d") . "</td>";
                if ($objMes->format("N") == 6) {
                    $tablaMes .= "</tr>";
                }
                $objMes->modify("+1 day");
            }
            $tablaMes .= "</table>";
            $tablaMes .= "</TD></TR></TABLE>";
            return $tablaMes;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo butcher
     * F?rmula desarrollada por algoritmo de Butcher para obtener el domingo de resurecci?n a partir de esto obtener jueves, viernes santo y otros 3 festivos
     * @author Felipe R. Puerto
     * @param $anno: A?o del que se averiguan estas fechas.
     * @return boolean: Verdadero si la fecha de la instancia coincide con el jueves o viernes santo
     * @since 2013-11-15 */

    function butcher($anno) {
        if ($this->ion_auth->logged_in()) {
            $a = $anno % 19;
            $b = ($anno - ($anno % 100)) / 100;
            $c = $anno % 100;
            $d = ($b - ($b % 4)) / 4;
            $e = $b % 4;
            $f = (($b + 8) - (($b + 8) % 25)) / 25;
            $g = (($b - $f + 1) - (($b - $f + 1) % 3)) / 3;
            $h = ((19 * $a) + $b - $d - $g + 15) % 30;
            $i = ($c - ($c % 4)) / 4;
            $k = $c % 4;
            $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
            $m = (($a + (11 * $h) + (22 * $l)) - (($a + (11 * $h) + (22 * $l)) % 451) ) / 451;
            $n = $h + $l - (7 * $m) + 114;
            $mmes = ($n - ($n % 31)) / 31;
            $ddia = 1 + ($n % 31);
            $pascua = new DateTime($anno . "-" . $mmes . "-" . $ddia);
            return $pascua;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo festivobd
     * Consulta los d?as festivos o h?biles predefinidos por el usuario por BD
     * @author Felipe R. Puerto
     * @param $aaaa: A?o a consultar.
     * @param $mm: Mes a consultar.
     * @param $dd: D?a a consultar.
     * @return Integer: 1 si se defini? como festivo 2 si se defini? como h?bil
     * @since 2013-11-15 */

    function festivobd() {
        if ($this->ion_auth->logged_in()) {
            $matriz = $this->calendariofestivo_model->festivo($this->objFecha);
            foreach ($matriz as $row) {
                return $row['ESTADO'];
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo traerfestivoarreglo
     * R?plica de festivobd para consultar todo el a?o en vez de d?a por d?a
     * Consulta los d?as festivos o h?biles predefinidos por el usuario por BD
     * @author Felipe R. Puerto
     * @param $aaaa: A?o a consultar.
     * @param $mm: Mes a consultar.
     * @param $dd: D?a a consultar.
     * @return Integer: 1 si se defini? como festivo 2 si se defini? como h?bil
     * @since 2013-11-15
     */

    function traerfestivoarreglo($aaaa) {
        if ($this->ion_auth->logged_in()) {
            $matriz = $this->calendariofestivo_model->festivosanuales($aaaa);
            return $matriz;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /*
     * M?todo festivocoincide
     * Verifica si el objFecha coincide con alguna fecha del arreglo.
     * @author Felipe R. Puerto
     * @param array $arrFestivos Arreglo de festivos Arreglo de d?as festivos del a?o establecidos por el usuario
     * @return Integer: 1 si se defini? como festivo 2 si se defini? como h?bil
     * @since 2013-11-15 */

    function festivocoincide($arrFestivos) {
        if ($this->ion_auth->logged_in()) {
            foreach ($arrFestivos as $row) {
                if ($row['DIA'] == $this->objFecha->format('d') && $row['MES'] == $this->objFecha->format('m') && $row['ANNO'] == $this->objFecha->format('Y')) {
                    return $row['ESTADO'];
                }
            }
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

    /**
     * M?todo sumarDiasHabilesORIGINAL (no se está usando)
     * Encuentra el d?a h?bil n posterior a la fecha dada
     * @author Felipe R. Puerto :: Thomas MTI
     * @since 2013-11-15
     *
     * @param String $fecha Cadena de fecha en formato Y-m-d
     * @param Integer $sumarCuantos N?mero de d?as h?biles a sumar.
     * @param String $sma Letra que indica si suman d?as (h?biles) o semanas, meses y a?os h?biles.
     * @return String Cadena con la fecha del d?a h?bil requerido en formato AAAA-MM-DD.
     */
    function sumarDiasHabilesORIGINAL($fecha, $sumarCuantos, $sma = "d") {
        $f = explode("-", $fecha);
        $d = 0;
        $objDiaH = new DateTime($f[0] . "-" . $f[1] . "-" . $f[2]);
        switch ($sma) {
            case "d":
            case "dias":
                if ($sumarCuantos == 0) {
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    while ($this->diaFestivo()) {
                        $objDiaH->modify("+1 day");
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    }
                }
                for ($i = 1; $d < $sumarCuantos; $i++) {
                    $objDiaH->modify("+1 day");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    if (!$this->diaFestivo()) {
                        $d++;
                    }
                }
                break;

            case "s":
            case "semanas":
                $objDiaH->modify("+$sumarCuantos weeks");
                $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                while ($this->diaFestivo()) {
                    $objDiaH->modify("+1 day");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                }
                break;

            case "m":
            case "month":
                $objDiaH->modify("+$sumarCuantos month");
                $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                while ($this->diaFestivo()) {
                    $objDiaH->modify("+1 day");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                }
                break;

            case "a":
            case "a?o":
                $objDiaH->modify("+$sumarCuantos year");
                $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                while ($this->diaFestivo()) {
                    $objDiaH->modify("+1 day");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                }
                break;
        }
        $cadenaFecha = $objDiaH->format("Y") . "-" . $objDiaH->format("m") . "-" . $objDiaH->format("d");
        return $cadenaFecha;
    }

    /**
     * M?todo sumarDiasHabiles
     * R?plica de sumarDiasHabilesORIGINAL
     * Encuentra el d?a h?bil n posterior a la fecha dada
     * (Este m?todo llama la BD s?lo una vez)
     * @author Felipe R. Puerto :: Thomas MTI
     * @since 2014-03-03
     *
     * @param String $fecha Cadena de fecha en formato Y-m-d
     * @param Integer $sumarCuantos N?mero de d?as h?biles a sumar.
     * @param String $sma Letra que indica si suman d?as (h?biles) o semanas, meses y a?os h?biles.
     * @return String Cadena con la fecha del d?a h?bil requerido en formato AAAA-MM-DD.
     */
    function sumarDiasHabiles($fecha, $sumarCuantos, $sma = "d") {//echo " FEC:".$fecha;
        if ($this->ion_auth->logged_in()) {
            $f = explode("-", $fecha);
            $d = 0;
            $objDiaH = new DateTime($f[0] . "-" . $f[1] . "-" . $f[2]);

            $festivosanuales = $this->traerfestivoarreglo($f[0]);

            switch ($sma) {
                case "d":
                case "dias":
                    if ($sumarCuantos == 0) {
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                        $esFestivo = $this->esFestivoArreglo($festivosanuales);
                        while ($esFestivo === true || $esFestivo === 1) {
                            $objDiaH->modify("+1 day");
                            $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                            $esFestivo = $this->esFestivoArreglo($festivosanuales);
                        }
                    }

                    for ($i = 1; $d < $sumarCuantos; $i++) {
                        $objDiaH->modify("+1 day");
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                        $esFestivo = $this->esFestivoArreglo($festivosanuales);
                        if (!($esFestivo === true || $esFestivo === 1)) {
                            $d++;
                        }
                    }
                    break;

                case "dc":
                case "diascalendario":
                    $objDiaH->modify("+$sumarCuantos day");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    break;

                case "s":
                case "semanas":
                    $objDiaH->modify("+$sumarCuantos weeks");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    while ($esFestivo === true || $esFestivo === 1) {
                        $objDiaH->modify("+1 day");
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                        $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    }
                    break;

                case "m":
                case "month":
                    $objDiaH->modify("+$sumarCuantos month");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    while ($esFestivo === true || $esFestivo === 1) {
                        $objDiaH->modify("+1 day");
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                        $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    }
                    break;

                case "a":
                case "a?o":
                    $objDiaH->modify("+$sumarCuantos year");
                    $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                    $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    while ($esFestivo === true || $esFestivo === 1) {
                        $objDiaH->modify("+1 day");
                        $this->ponerFecha($objDiaH->format("Y"), $objDiaH->format("m"), $objDiaH->format("d"));
                        $esFestivo = $this->esFestivoArreglo($festivosanuales);
                    }
                    break;
            }
            $cadenaFecha = $objDiaH->format("Y") . "-" . $objDiaH->format("m") . "-" . $objDiaH->format("d");
            return $cadenaFecha;
        } else {
            redirect(base_url() . 'index.php/auth/login');
        }
    }

}

/**
 * Funci?n trazarProcesoJuridico.
 * Registra la traza del proceso jur?dico en el sistema.
 *
 * @param Integer $tipogestion C?d. de tipo de gesti?n.
 * @param Integer $tiporespuesta C?d de tipo de respuesta.
 * @param Integer $codtitulo C?d de titulo.
 * @param String $comentarios: Alg?n comentario al respecto
 * @return Integer: C?d de traza del proceso judicial.
 */
function trazarProcesoJuridico($tipogestion, $tiporespuesta, $codtitulo, $codjuridico, $codcarteranomisional, $coddevolucion, $codrecepcion, $comentarios = "Sin Comentarios", $usuariosAdicionales = '', $sistema = FALSE) {

     
            
    $CI = get_instance();
    if ($CI->ion_auth->logged_in() || $sistema === TRUE) {
        $CI->load->model('flujo_model'); //$CI->load->library('ion_auth');//echo " idu:".$CI->ion_auth->user()->row()->EMAIL;
        $usu = $CI->ion_auth->user()->row()->IDUSUARIO;
        ////$fila = $CI->flujo_model->trazaDuplicada($codtitulo); PENDIENTE REALIZAR PARA PROCESOS JUDICIALES
        $datagestiongenedoc = array("COMENTARIOS" => $comentarios, "COD_TIPO_RESPUESTA" => "$tiporespuesta", "COD_TIPOGESTION" => "$tipogestion", "COD_RECEPCIONTITULO" => $codrecepcion,"COD_TITULO" => $codtitulo, "COD_USUARIO" => $usu, "COD_JURIDICO" => $codjuridico, "COD_CARTERANOMISIONAL" => $codcarteranomisional, "COD_DEVOLUCION" => $coddevolucion, "COD_RECEPCIONTITULO" => $codrecepcion);
        
     

        $fechaTraza = array('FECHA' => date("d/m/Y H:i"));
     $prueva=   $CI->flujo_model->addTraza("TRAZAPROCJUDICIAL", $datagestiongenedoc, $fechaTraza);
// echo"<pre>7";   print_r($prueva);echo"</pre>";die(); 
        $idtrazaprocjudicial = $CI->flujo_model->selectIdProcesosJudiciales('TRAZAPROCJUDICIAL', $datagestiongenedoc, $fechaTraza);
      // print_r($idtrazaprocjudicial);die();

     

        $tipogestion = $CI->flujo_model->siguienteGestion($tipogestion, $tiporespuesta);

        if (!empty($tipogestion['GESTIONDESTINO']) || !empty($tipogestion['RTADESTINO'])) {
            ///_ " PONER RECORDATORIO PARA LA SIGUIENTE GESTIÓN. ";
            $tf = new Traza_fecha_helper();
            $resRecordar = $tf->poneRecordarJudicial($idtrazaprocjudicial['COD_TRAZAPROCJUDICIAL'], $tipogestion['GESTIONDESTINO'], $tipogestion['RTADESTINO'], '', $usuariosAdicionales, $codtitulo, $codjuridico, $codcarteranomisional, $coddevolucion, $codrecepcion);
           //  print_r($resRecordar);die();
        }
        
        return @$idtrazaprocjudicial[0]['COD_TRAZAPROCJUDICIAL'];
    } else {
        redirect(base_url() . 'index.php/auth/login');
    }
}
