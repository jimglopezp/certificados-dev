<?php

class Mcinvestigacion_model extends MY_Model {

    var $cod_municipio;
    var $array_num;

    function __construct() {
        parent::__construct();
    }

    function set_cod_municipio($cod_municipio) {
        $this->cod_municipio = $cod_municipio;
    }

    function set_array_num($array_num) {
        $this->array_num = $array_num;
    }

    function permiso() {
        $this->db->select("USUARIOS.IDUSUARIO as IDUSUARIO, APELLIDOS, NOMBRES,GRUPOS.IDGRUPO");
        $this->db->join("USUARIOS_GRUPOS", "USUARIOS.IDUSUARIO=USUARIOS_GRUPOS.IDUSUARIO");
        $this->db->join("GRUPOS", "USUARIOS_GRUPOS.IDGRUPO=GRUPOS.IDGRUPO");
        $this->db->or_where("(GRUPOS.IDGRUPO", ABOGADO);
        $this->db->or_where("GRUPOS.IDGRUPO", SECRETARIO);
        $this->db->or_where("GRUPOS.IDGRUPO", COORDINADOR . ")", FALSE);
        $this->db->where("USUARIOS.IDUSUARIO", ID_USER);
        $dato = $this->db->get("USUARIOS");
        return $dato->result_array;
    }

    function COBROPERSUASIVO($id = NULL, $id_doc = NULL, $id_COD = NULL) {
//        echo $id;
        $this->db->select("VW.REPRESENTANTE AS REPRESENTANTE_LEGAL, RESPUESTAGESTION.NOMBRE_GESTION AS RESPUESTA,VW.CONCEPTO AS NOMBRE_CONCEPTO,VW.DIRECCION,MUNICIPIO.CODMUNICIPIO COD_MUNICIPIO,MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR AS PROCESO,MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION AS COD_RESPUESTA,"
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE_EMPRESA,'
                . 'PC.IDENTIFICACION AS CODEMPRESA, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL, VW.COD_REGIONAL AS COD_REGIONAL,MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION,'
                . "MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO COD_FISCALIZACION,MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR,RESPUESTAGESTION.NOMBRE_GESTION AS TIPOGESTION,"
                . "REST.NOMBRE_GESTION AS TIPOGESTION2,MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION_BIENES,MUNICIPIO.NOMBREMUNICIPIO", FALSE);
//        $this->db->from('MC_MEDIDASCAUTELARES MC');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO');
        $this->db->join('RESPUESTAGESTION', 'RESPUESTAGESTION.COD_RESPUESTA=MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION');
        $this->db->join('RESPUESTAGESTION REST', 'REST.COD_RESPUESTA=MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION_BIENES', 'LEFT');
        $this->db->join('VW_PROCESOS_COACTIVOS_0001 VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $this->db->join('REGIONAL', 'REGIONAL.COD_REGIONAL=VW.COD_REGIONAL');
        $this->db->join('MUNICIPIO', 'MUNICIPIO.CODMUNICIPIO=REGIONAL.COD_CIUDAD AND MUNICIPIO.COD_DEPARTAMENTO=REGIONAL.COD_DEPARTAMENTO', FALSE);
        /**/
        //  $where = 'VW.COD_RESPUESTA = CP.COD_TIPO_RESPUESTA AND CP.COD_TIPO_RESPUESTA NOT IN (204,196)';
        // $where = 'VW.COD_RESPUESTA = MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION';
        //$where='';
        //$this->db->where($where);

        if (!empty($id_doc)) {
            $this->db->select('MC_OFICIOS_GENERADOS.RUTA_DOCUMENTO_GEN');
            $this->db->join("MC_OFICIOS_GENERADOS", "MC_OFICIOS_GENERADOS.COD_MEDIDACAUTELAR=MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR"
                    . " and MC_OFICIOS_GENERADOS.ESTADO=0 AND MC_OFICIOS_GENERADOS.TIPO_DOCUMENTO='" . $id_doc . "'", "LEFT");
        }
        if (!empty($id))
            $this->db->where("MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR", $id);
        else if (!empty($id_COD))
            $this->db->where("MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO", $id_COD);
        else {
            $num = $this->array_num;
            $this->db->where_in("(MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION", $num, FALSE);
            $datos = "";
            for ($i = 0; $i < count($num); $i++) {
                $datos.=$num[$i] . ",";
            }
            $this->db->or_where("MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION_BIENES in (", substr($datos, 0, -1) . "))", FALSE);
        }
        $this->db->where("MC_MEDIDASCAUTELARES.BLOQUEO", "0");
//        $this->db->where("EMPRESA.COD_REGIONAL", COD_REGIONAL);
        $dato = $this->db->get("MC_MEDIDASCAUTELARES");
        // echo $data=$this->db->last_query(); //die();
        return $dato->result_array;
    }

    function COBROPERSUASIVO_PRELACION($id = NULL) {

        $this->db->select("MC_MEDIDASCAUTELARES.OBSERVACIONES_REVISION,MC_MEDIDASCAUTELARES.RUTA_DOCUMENTO_MC,MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR,MC_MEDIDASCAUTELARES.COD_RESPUESTAGESTION,MC_MEDIDASCAUTELARES.COD_FISCALIZACION,"
                . "RESPUESTAGESTION.NOMBRE_GESTION as TIPOGESTION,"
                . "MC_MEDIDASPRELACION.COD_CONCURRENCIA,"
                . "MC_MEDIDASPRELACION.COD_MEDIDAPRELACION,"
                . "MC_MEDIDASPRELACION.COD_TIPOGESTION as TIPO,"
                . "MC_MEDIDASPRELACION.FECHA"
                . "");
        $this->db->join("MC_MEDIDASPRELACION", "MC_MEDIDASPRELACION.COD_MEDIDACAUTELAR=MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR");
        $this->db->join("RESPUESTAGESTION", "RESPUESTAGESTION.COD_RESPUESTA=MC_MEDIDASPRELACION.COD_TIPOGESTION");
        $num = $this->array_num;
        $this->db->where_in("COD_RESPUESTAGESTION", $num, FALSE);
        if (!empty($id))
            $this->db->where("MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR", $id);
        $this->db->where("MC_MEDIDASCAUTELARES.BLOQUEO", "0");
        $this->db->where("MC_MEDIDASPRELACION.ACTIVO", "0");
        $dato = $this->db->get("MC_MEDIDASCAUTELARES");
        return $dato->result_array;
    }

    function municipio() { //funcion para optener el municipio de la empresa
        if (!empty($this->cod_municipio)) :
            $this->db->select("NOMBREMUNICIPIO");
            $this->db->where("CODMUNICIPIO", $this->cod_municipio);
            $dato = $this->db->get("MUNICIPIO");
            if (!empty($dato->result_array[0])):
                return $dato->result_array[0];
            endif;
        endif;
    }

    function municipio2($id) { //funcion para optener el municipio de la empresa
        if (!empty($this->cod_municipio)) :
            $this->db->select("NOMBREMUNICIPIO");
            $this->db->where("CODMUNICIPIO", $id);
            $dato = $this->db->get("MUNICIPIO");
            if (!empty($dato->result_array[0])):
                return $dato->result_array[0];
            endif;
        endif;
    }

    function secretario() {
        $this->db->select("USUARIOS.IDUSUARIO as IDUSUARIO, APELLIDOS, NOMBRES");
        $this->db->join("REGIONAL", "REGIONAL.CEDULA_SECRETARIO=USUARIOS.IDUSUARIO");
        $this->db->where("USUARIOS.COD_REGIONAL", COD_REGIONAL);
//        $this->db->where("USUARIOS.IDUSUARIO", $user);
        $dato = $this->db->get("USUARIOS");
        return $dato->result_array;
    }
    //////////////////////////////////////////////////YURI CDS POPYAN /////////////////////////////////////////////////
    function getTitulosmedidas($cod_coactivo,$id_mandamiento)
  {

    $this -> db -> select("VW.SALDO_DEUDA, VW.SALDO_CAPITAL, VW.SALDO_INTERES,VW.COD_EXPEDIENTE_JURIDICA, VW.CONCEPTO");
    $this -> db -> from('PROCESOS_COACTIVOS PC');
    $this -> db -> join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO AND VW.COD_RESPUESTA=PC.COD_RESPUESTA');
    $this -> db -> join('MC_MEDIDASCAUTELARES MC', 'PC.COD_PROCESO_COACTIVO=MC.COD_PROCESO_COACTIVO');
    $this -> db -> where('PC.COD_PROCESO_COACTIVO', $cod_coactivo);
    $this -> db -> where('MC.COD_MEDIDACAUTELAR', $id_mandamiento);
    $query = $this -> db -> get();
    if ($query -> num_rows() > 0)
    {
      $query = $query -> result_array();
      return $query;
    }
  }
 

  function getEmpresas2($nit)
  {
    $this -> db -> select("NOMBRE_EMPRESA, CODEMPRESA, REPRESENTANTE_LEGAL,TELEFONO_FIJO,ACTIVO,DIRECCION");
    $this -> db -> where("CODEMPRESA", $nit);
    $dato = $this -> db -> get("EMPRESA");
    //echo $this->db->last_query();
    return $dato -> result_array;
  }
  //////////////////////////////////////////////////////////fin////////////////////////////////////

    function guardar_Mc_medidas_cautelarias($post) {
      //  echo "<pre>"; print_r($post); echo "</pre>";// die();
        $this->db->set("COD_PROCESO_COACTIVO", $post['consulta'][0]['COD_FISCALIZACION']);

        switch ($post['post']['cod_siguiente']) {
            case ENVIAR_MODIFICACIONES:
                $no_info = INICIO_SECRETARIO;
                break;
                case SUBIR_FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA:
                $no_info = FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA;
                break;
                case COMINUICACION_PRENDATARIO:
                $no_info = COMINUICACION_PRENDATARIO_SECRETARIO;
                break;
                 case COMINUICACION_PRENDATARIO_VEHICULO:
                $no_info = COMINUICACION_PRENDATARIO_SECRETARIO_VEHICULO;
                break;
                 case SUBIR_COMUNICACION_EXCEDENTE:
                $no_info = COMUNICACION_EXCEDENTE_APROBADO;
                break;
                 case  COMUNICACION_EXCEDENTE_APROBADO:
                $no_info = COMUNICACION_EJECUTOR;
                break;
                case  COMUNICACION_EJECUTOR:
                $no_info = COMUNICACION_EJECUTOR_CORDINADOR;
                break;
                 case  COMUNICACION_EJECUTOR_CORDINADOR:
                $no_info = SUBIR_COMUNICACION_EJECUTOR;
                break;
                 case  SUBIR_COMUNICACION_EJECUTOR:
                $no_info = COMUNICACION_EJECUTOR_APROBADO;
                break;
                 

                

               /* case OFICIO_DE_BANCO_AGRARIO_RECHAZADO:
                $no_info = FRACCIONAMIENTO_ARCHIVO_APROBADO;
                break;*/

                 case FRACCIONAMIENTO_ARCHIVO_APROBADO:
                $no_info = FRACCIONAMIENTO_A_BANCO_AGRARIO;
                break;
                 case FRACCIONAMIENTO_A_BANCO_AGRARIO:
                $no_info = OFICIO_DE_BANCO_AGRARIO_COORDINARO;
                break;
                case COMUNICACION_EXCEDENTE_CORDINADOR:
                $no_info = SUBIR_COMUNICACION_EXCEDENTE;
                break;
                
                case OFICIO_DE_BANCO_AGRARIO_COORDINARO:
                $no_info = SUBIR_FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA;
                break;
                 case SUBIR_FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA:
                $no_info = FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA;
                break;
                case FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA:
                $no_info = CITACION_GENERADA_AL_DEUDOR_DEVOLUCION;
                break;
                case CITACION_DE_BANCO_AGRARIO_APROBADA2:
                $no_info = COMUNICACION_EXCEDENTE;
                break;
                case COMUNICACION_EXCEDENTE:
                $no_info = COMUNICACION_EXCEDENTE_CORDINADOR;
                break;


 case CONVERSION_DE_TITULOS:
                $no_info = CONVERSION_DE_TITULOS_JUDICIALES_GENERADO;
                break;
                 case CONVERSION_DE_TITULOS_JUDICIALES_GENERADO:
                $no_info = CONVERSION_DE_TITULOS_JUDICIALES_PRE_APROBADO;
                break;
                 case CONVERSION_DE_TITULOS_JUDICIALES_PRE_APROBADO:
                $no_info = CONVERSION_DE_TITULOS_JUDICIALES_SUBIR;
                break;
                 case CONVERSION_DE_TITULOS_JUDICIALES_SUBIR:
                $no_info = CONVERSION_DE_TITULOS_JUDICIALES_APROBADOR;
                break;


                
                 case CONVERSION_DE_TITULOS_JUDICIALES_APROBADO:
                $no_info = CONVERSION_DE_TITULOS_OFICIO_BANCO_AGRARIO;
                break;
                 case  CONVERSION_DE_TITULOS_OFICIO_BANCO_AGRARIO:
                $no_info = OFICIO_DE_BANCO_AGRARIO_COORDINARO2;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_SECRETARIO;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES2:
                $no_info = OFICIO_SECRETARIO2;
                break;
            case AUTO_ENVIAR_MODIFICACIONES:
                $no_info = AUTO_INICIO_SECRETARIO;
                break;
            case FRACCIONAMIENTO_ENVIAR_MODIFICACIONES:
                $no_info = FRACCIONAMIENTO_INICIO_SECRETARIO;
                break;
            case OFICIO_BIENES_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_BIENES_INICIO_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente'];
        }
        $post['post']['cod_siguiente2'] = $no_info;
        if ($post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_SECRETARIO ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_COORDINADOR ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_DEVOLUCION ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_ENVIAR_MODIFICACIONES
        )
            $this->db->set("COD_RESPUESTAGESTION_BIENES", $no_info);
        else
            $this->db->set("COD_RESPUESTAGESTION", $no_info);

        $this->db->set("FECHA_MEDIDAS", FECHA, false);
        $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio Orden de Investigacion y Envargo de Dinero");
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("GENARADO_POR", ID_USER);
        $this->db->set("REVISADO_POR", $post['post']['secretario']);
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
echo $this->db->last_query();//die();
        $this->db->select("COD_MEDIDACAUTELAR");
        $this->db->where("COD_PROCESO_COACTIVO", $post['consulta'][0]['COD_FISCALIZACION']);
        $dato = $this->db->get("MC_MEDIDASCAUTELARES");

        $post['post']['id_mc'] = $dato->result_array[0]['COD_MEDIDACAUTELAR'];

        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");
        }

        $this->oficios_generados($post);
    }
    ////////////yuri//////////////////////////////////////////////

    function guardar_Mc($post) {
  // echo"<pre>bbb";   print_r($post);echo"</pre>";die(); 
    
        $this->db->set("COD_PROCESO_COACTIVO", $post['consulta'][0]['COD_FISCALIZACION']);
       
//print_r($post['post']['cod_siguiente']);die(); echo"</pre>";
        switch ($post['post']['cod_siguiente']) {
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA2:
            
                $no_info = OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA:
            
                $no_info = OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;



                

                case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INI;
                break;
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INICIO2;
                break; 
            case ENVIAR_MODIFICACIONES:
                $no_info = INICIO_SECRETARIO;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_SECRETARIO;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES2:
                $no_info = OFICIO_SECRETARIO2;
                break;
            case AUTO_ENVIAR_MODIFICACIONES:
                $no_info = AUTO_INICIO_SECRETARIO;
                break;
            case FRACCIONAMIENTO_ENVIAR_MODIFICACIONES:
                $no_info = FRACCIONAMIENTO_INICIO_SECRETARIO;
                break;
            case OFICIO_BIENES_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_BIENES_INICIO_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente'];
        }
        $post['post']['cod_siguiente2'] = $no_info;
        
        if ($post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_SECRETARIO ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_COORDINADOR ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_DEVOLUCION ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_ENVIAR_MODIFICACIONES ||
                $post['post']['cod_siguiente2'] == OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO
                 ||
                $post['post']['cod_siguiente2'] == OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2
                ||
                $post['post']['cod_siguiente2'] == OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO5
                 ||
                $post['post']['cod_siguiente2'] == OFICIO_PRELACION_Y_CREDITOS_INI 
        )
            $this->db->set("COD_RESPUESTAGESTION_BIENES", $no_info);
        else
            $this->db->set("COD_RESPUESTAGESTION", $no_info);
//echo "<pre> ggffg"; print_r( $post['post']['cod_siguiente2']); echo "</pre>"; die();
        $this->db->set("FECHA_MEDIDAS", FECHA, false);
        if( $post['post']['cod_siguiente2'] == OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO5
                 ||
                $post['post']['cod_siguiente2'] == OFICIO_PRELACION_Y_CREDITOS_INI ) {

        $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio dirigido a  entidad que haya embargado primero (vehiculo)");

        }
        else{
               $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio dirigido a  entidad que haya embargado primero");
        }
      
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("GENARADO_POR", ID_USER);
        $this->db->set("REVISADO_POR", $post['post']['secretario']);
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
//echo $this->db->last_query();die();
        $this->db->select("COD_MEDIDACAUTELAR");
        $this->db->where("COD_PROCESO_COACTIVO", $post['consulta'][0]['COD_FISCALIZACION']);
        $dato = $this->db->get("MC_MEDIDASCAUTELARES");

        $post['post']['id_mc'] = $dato->result_array[0]['COD_MEDIDACAUTELAR'];

        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");echo $this->db->last_query();die();
        }

        $this->oficios_generados($post);
    }
//////////////////////////////////////////////////////////

    function guardar_Mc_medidas_Prelacion($post) {
//        echo $post['post']['cod_siguiente_prelacion']."**";
        switch ($post['post']['cod_siguiente_prelacion']) {
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO:
                $no_info = OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2:
                $no_info = OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;

            
  
                case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INI;
                break;
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INICIO2;
                break; 
            case MUEBLES_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_SECRETARIO;
                break;
            case MUEBLES_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_COM_SECUESTRO_SECRETARIO;
                break;
            case MUEBLES_DES_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_DES_SECUESTRO_SECRETARIO;
                break;
            case MUEBLES_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_RESPUESTA_SECRETARIO;
                break;
            case MUEBLES_COMISORIO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_COMISORIO_SECRETARIO;
                break;
            case MUEBLES_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_FECHA_SECRETARIO;
                break;
            case MUEBLES_DILIGENCIA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_DILIGENCIA_SECRETARIO;
                break;
            case MUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_ORDEN_SECRETARIO;
                break;
            case INMUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_ORDEN_SECRETARIO;
                break;
            case VEHICULO_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_ORDEN_SECRETARIO;
                break;
            case MUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_PROYECTAR_AUTO_SECRETARIO;
                break;
            case MUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_PROYECTAR_RESPUESTA_SECRETARIO;
                break;
            case INMUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_PROYECTAR_AUTO_SECRETARIO;
                break;
            case VEHICULO_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_PROYECTAR_AUTO_SECRETARIO;
                break;
            case INMUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_PROYECTAR_RESPUESTA_SECRETARIO;
                break;
            case INMUEBLES_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_SECRETARIO;
                break;
            case INMUEBLES_EMBARGO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_EMBARGO_SECRETARIO;
                break;
            case INMUEBLES_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_FECHA_SECRETARIO;
                break;
            case INMUEBLES_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_AUTO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_AUTO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_COMISION_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_COMISION_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_DOCUMENTO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_DOCUMENTO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_COMISORIO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_COMISORIO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_DESP_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_DESP_COM_SECUESTRO_SECRETARIO;
                break;
            case VEHICULO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_SECRETARIO;
                break;
            case VEHICULO_EMBARGO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_EMBARGO_SECRETARIO;
                break;
            case VEHICULO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_SECUESTRO_SECRETARIO;
                break;
            case VEHICULO_COMISION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_COMISION_SECRETARIO;
                break;
            case VEHICULO_DESPACHO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_DESPACHO_SECRETARIO;
                break;
            case VEHICULO_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_FECHA_SECRETARIO;
                break;
            case VEHICULO_DILIGENCIA_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_DILIGENCIA_SECRETARIO;
                break;
            case VEHICULO_EMBARGO_OFICIO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_EMBARGO_OFICIO_INICIO;
                break;
            case VEHICULO_OPOSICION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_OPOSICION_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente_prelacion'];
        }


        $this->db->set("FECHA_MEDIDAS", FECHA, false);
        $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio Orden de Investigacion y Envargo de Dinero");
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
        $this->db->set("COD_TIPOGESTION", $no_info);
        $this->db->set("FECHA", FECHA, false);
        if ($post['bloqueo'] == 0)
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        else {
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->where("COD_CONCURRENCIA", $post['bloqueo']);
        }
        $this->db->update("MC_MEDIDASPRELACION");
        //echo $this->db->last_query();die();
//        echo $post['post']['cod_siguiente'] = $post['post']['cod_siguiente_prelacion'];
        $post['post']['cod_siguiente'] = $no_info;
//        echo "entro";
//        echo $no_info;

        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");
        }
        $this->oficios_generados($post);
    }



//////////////////////////////////////////////YURI CDS_POPAYAN////////////////////////////
 function guardar_Mc_Prelacion2($post) {
  //  print_r($post);die();
      //echo $post['post']['bloqueo']."**";die();

        switch ($post['post']['cod_siguiente_prelacion']) {
           
         
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO:
        // echo OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO."********";die();
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA2:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;


                case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INI;
                break;
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INICIO2;
                break; 
                 case MUEBLES_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_SECRETARIO;
                break;

            case MUEBLES_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_COM_SECUESTRO_SECRETARIO;
                break;
            case MUEBLES_DES_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_DES_SECUESTRO_SECRETARIO;
                break;
            case MUEBLES_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_RESPUESTA_SECRETARIO;
                break;
            case MUEBLES_COMISORIO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_COMISORIO_SECRETARIO;
                break;
            case MUEBLES_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_FECHA_SECRETARIO;
                break;
            case MUEBLES_DILIGENCIA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_DILIGENCIA_SECRETARIO;
                break;
            case MUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_ORDEN_SECRETARIO;
                break;
            case INMUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_ORDEN_SECRETARIO;
                break;
            case VEHICULO_ORDEN_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_ORDEN_SECRETARIO;
                break;
            case MUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_PROYECTAR_AUTO_SECRETARIO;
                break;
            case MUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = MUEBLES_PROYECTAR_RESPUESTA_SECRETARIO;
                break;
            case INMUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_PROYECTAR_AUTO_SECRETARIO;
                break;
            case VEHICULO_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_PROYECTAR_AUTO_SECRETARIO;
                break;
            case INMUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_PROYECTAR_RESPUESTA_SECRETARIO;
                break;
            case INMUEBLES_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_SECRETARIO;
                break;
            case INMUEBLES_EMBARGO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_EMBARGO_SECRETARIO;
                break;
            case INMUEBLES_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_FECHA_SECRETARIO;
                break;
            case INMUEBLES_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_AUTO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_AUTO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_COMISION_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_COMISION_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_DOCUMENTO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_DOCUMENTO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_COMISORIO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_COMISORIO_SECUESTRO_SECRETARIO;
                break;
            case INMUEBLES_DESP_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = INMUEBLES_DESP_COM_SECUESTRO_SECRETARIO;
                break;
            case VEHICULO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_SECRETARIO;
                break;
            case VEHICULO_EMBARGO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_EMBARGO_SECRETARIO;
                break;
            case VEHICULO_SECUESTRO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_SECUESTRO_SECRETARIO;
                break;
            case VEHICULO_COMISION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_COMISION_SECRETARIO;
                break;
            case VEHICULO_DESPACHO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_DESPACHO_SECRETARIO;
                break;
            case VEHICULO_FECHA_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_FECHA_SECRETARIO;
                break;
            case VEHICULO_DILIGENCIA_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_DILIGENCIA_SECRETARIO;
                break;
            case VEHICULO_EMBARGO_OFICIO_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_EMBARGO_OFICIO_INICIO;
                break;
            case VEHICULO_OPOSICION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_OPOSICION_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente_prelacion'];
        }

if($post['post']['cod_siguiente_prelacion']==2068){
    //echo "aqi";die();
  $this->db->set("NOMBRE_DOCUMENTO_MC", "Solicitud a la autoridad que haya embargado primero el embargo de remanentes(vehiculos)");
}
else if($post['post']['cod_siguiente_prelacion']==1628){ 
    //echo "aca";;die();
    $this->db->set("NOMBRE_DOCUMENTO_MC", "Solicitud a la autoridad que haya embargado primero el embargo de remanentes");
}
        $this->db->set("FECHA_MEDIDAS", FECHA, false);
       
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
       // echo $this->db->last_query();
        $this->db->set("COD_TIPOGESTION", $no_info);
        $this->db->set("FECHA", FECHA, false);
        if ($post['bloqueo'] == 0)
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        else {
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->where("COD_CONCURRENCIA", $post['bloqueo']);
        }
        $this->db->update("MC_MEDIDASPRELACION");
       // echo $this->db->last_query();//die();
//        echo $post['post']['cod_siguiente'] = $post['post']['cod_siguiente_prelacion'];
        $post['post']['cod_siguiente'] = $no_info;
//        echo "entro";
//        echo $no_info;

        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");
        }
        $this->oficios_generados($post);
    }


function guardar_Mc_Prelacion3($post) {
    //print_r($post);die();
      //echo $post['post']['bloqueo']."**";die();

        switch ($post['post']['cod_siguiente_prelacion']) {
           
         
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO:
        // echo OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO."********";die();
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                 

            case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2:
        // echo OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO."********";die();
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA2:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;
                 
          


                case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INI;
                break;
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INICIO2;
                break; 
          
            case VEHICULO_OPOSICION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_OPOSICION_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente_prelacion'];
        }
      
     $this->db->set("FECHA_MEDIDAS", FECHA, false);
        $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio de Entidad Que Embarga Primero Informando  Prelacion y Preferencia Creditos");
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
       // echo $this->db->last_query();
        $this->db->set("COD_TIPOGESTION", $no_info);
        $this->db->set("FECHA", FECHA, false);
        if ($post['bloqueo'] == 0)
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        else {
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->where("COD_CONCURRENCIA", $post['bloqueo']);
        }
        $this->db->update("MC_MEDIDASPRELACION");
       // echo $this->db->last_query();//die();
//        echo $post['post']['cod_siguiente'] = $post['post']['cod_siguiente_prelacion'];
        $post['post']['cod_siguiente'] = $no_info;
//        echo "entro";
//        echo $no_info;
        
        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");
        }
        $this->oficios_generados($post);
    }
function guardar_Mc_Prelacion4($post) {
    //print_r($post);die();
      //echo $post['post']['bloqueo']."**";die();

        switch ($post['post']['cod_siguiente_prelacion']) {
           
         
            case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO:
        // echo OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO."********";die();
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO;
                break;

                 case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2:
        // echo OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO."********";die();
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;
                case OFICIO_DE_ENTIDAD_QUE_EMBARGA2:
       //
                $no_info =OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2;
                break;


                case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INI;
                break;
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
            
                $no_info = OFICIO_PRELACION_Y_CREDITOS_INICIO2;
                break; 

                case BLOQUEAR_EMBARGO:
       //
                $no_info = BLOQUEAR_EMBARGOS_SECRETARIO2;
                break; 

                 case BLOQUEAR_EMBARGOS_SECRETARIO2:
       //
                $no_info = BLOQUEAR_EMBAR;
                break; 
                case BLOQUEAR_EMBAR:
       //
                $no_info = PENDIENTE_CAMBIO;
                break; 

                case BLOQUEAR_EMBARGOS2:
       //
                $no_info = BLOQUEAR_EMBARGOS_SECRETARIO;
                break; 

                 case BLOQUEAR_EMBARGOS_SECRETARIO:
       //
                $no_info = BLOQUEAR_EMBARGOS;
                break; 
          case BLOQUEAR_EMBARGOS:
       //
                $no_info = PENDIENTE_CAMBIO;
                break;


                  case BLOQUEAR_EMBARGO:
       //
                $no_info = BLOQUEAR_EMBAR;
                break; 
          case BLOQUEAR_EMBAR:
       //
                $no_info = PENDIENTE_CAMBIO;
                break;
          
            case VEHICULO_OPOSICION_ENVIAR_MODIFICACIONES:
                $no_info = VEHICULO_OPOSICION_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente_prelacion'];
        }

        $this->db->set("FECHA_MEDIDAS", FECHA, false);
        $this->db->set("NOMBRE_DOCUMENTO_MC", "Oficio informando que se registro la solicitud de embargo de remanentes");
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        $this->db->set("FECHA_REVISION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
       // echo $this->db->last_query();
        $this->db->set("COD_TIPOGESTION", $no_info);
        $this->db->set("FECHA", FECHA, false);
        if ($post['bloqueo'] == 0)
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        else {
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->where("COD_CONCURRENCIA", $post['bloqueo']);
        }
        $this->db->update("MC_MEDIDASPRELACION");
        //echo $this->db->last_query();//die();
//        echo $post['post']['cod_siguiente'] = $post['post']['cod_siguiente_prelacion'];
        $post['post']['cod_siguiente'] = $no_info;
//        echo "entro";
//        echo $no_info;

        if (!empty($post['post']['obser'])) {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("FECHA_MODIFICACION", FECHA, false);
            $this->db->set("COMENTARIOS", $post['post']['obser']);
            $this->db->set("GENERADO_POR", ID_USER);
            $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->insert("MC_TRAZABILIDAD");
        }
        $this->oficios_generados($post);
    }


/////////////////////////////////////////////////////////////////////////////////////

function select_traza1($cod_coactivo) {


 $datos = $this->db->query("
   SELECT
   COD_TIPO_RESPUESTA
FROM
    TRAZAPROCJUDICIAL
  
  WHERE COD_JURIDICO =" . $cod_coactivo . "
        AND (COD_TIPO_RESPUESTA  IN (1539,1451)
        OR
        COD_TIPO_RESPUESTA = '1444')
         ");
         //echo $this->db->last_query();die();
        return $datos->result_array();
    }
  
        
    
     
    function documento($post) {
        $this->db->select("RUTA_DOCUMENTO_GEN AS RUTA_DOCUMENTO_MC,FECHA_RADICADO");
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->where("ESTADO", 0);
        $this->db->order_by("FECHA_CREACION", "DESC");
        $dato = $this->db->get("MC_OFICIOS_GENERADOS");
       // echo $this->db->last_query();die();
        return $dato->result_array;
    }

    function update_Mc_medidas_cautelarias($post) {
        $this->db->set("APROBADO_POR", ID_USER);
        $this->db->set("RUTA_DOCUMENTO_MC", $post['post']['nombre'] . ".txt");
        switch ($post['post']['cod_siguiente']) {
            case ENVIAR_MODIFICACIONES:
                $no_info = INICIO_SECRETARIO;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_SECRETARIO;
                break;
            case OFICIO_ENVIAR_MODIFICACIONES2:
                $no_info = OFICIO_SECRETARIO2;
                break;
            case AUTO_ENVIAR_MODIFICACIONES:
                $no_info = AUTO_INICIO_SECRETARIO;
                break;
            case FRACCIONAMIENTO_ENVIAR_MODIFICACIONES:
                $no_info = FRACCIONAMIENTO_INICIO_SECRETARIO;
                break;
            case OFICIO_BIENES_ENVIAR_MODIFICACIONES:
                $no_info = OFICIO_BIENES_INICIO_SECRETARIO;
                break;
            default :
                $no_info = $post['post']['cod_siguiente'];
        }
        $post['post']['cod_siguiente2'] = $no_info;
        if ($post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_SECRETARIO ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_INICIO_COORDINADOR ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_DEVOLUCION ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_ENVIAR_MODIFICACIONES ||
                $post['post']['cod_siguiente2'] == OFICIO_BIENES_SUBIR_ARCHIVO
        )
            $this->db->set("COD_RESPUESTAGESTION_BIENES", $no_info);
        else
            $this->db->set("COD_RESPUESTAGESTION", $no_info);

        $this->db->set("FECHA_APROBACION", FECHA, false);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
        $this->oficios_generados($post);
    }

    function oficios_generados($post) {
      /*  echo "<pre>ll";
     print_r($post);
       echo "</pre>"; die();*/
        $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
        $this->db->set("FECHA_CREACION", FECHA, false);
        $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);//;echo $this->db->last_query();die();
        // echo $post['post']['cod_siguiente'];die();
        switch ($post['post']['cod_siguiente']) {
            case 1564:
            case 3063:
            case 3064:
            case 3065:
            case 3066:
            case OFICIO_PRELACION_Y_CREDITOS_INICIO:
              case OFICIO_PRELACION_Y_CREDITOS_INICIO2:
              case OFICIO_PRELACION_Y_CREDITOS_INICIO5:
              case OFICIO_PRELACION_Y_CREDITOS_INI:
                  case CONVERSION_DE_TITULOS:
                   case CONVERSION_DE_TITULOS_JUDICIALES_PRE_APROBADO:
                    case CONVERSION_DE_TITULOS_JUDICIALES_SUBIR:
                  case  CONVERSION_DE_TITULOS_JUDICIALES_APROBADO:
                  case CONVERSION_DE_TITULOS_JUDICIALES_GENERADO:
            case CONVERSION_DE_TITULOS_OFICIO_BANCO_AGRARIO:
            case SUBIR_FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA:
          case PENDIENTE_CAMBIO:
           case COMINUICACION_PRENDATARIO_SECRETARIO:
            case COMINUICACION_PRENDATARIO_SECRETARIO_VEHICULO:
            case SUBIR_COMINUICACION_PRENDATARIO:
           case SUBIR_COMINUICACION_PRENDATARIO_VEHICULO:
                case FRACCIONAMIENTO_A_BANCO_AGRARIO_APROBADA:
                 case BLOQUEAR_EMBARGOS_SECRETARIO:
                   case BLOQUEAR_EMBARGOS_SECRETARIO2:
                
            case FRACCIONAMIENTO_A_BANCO_AGRARIO:
             case  FRACCIONAMIENTO_ARCHIVO_APROBADO:
            case OFICIO_DE_BANCO_AGRARIO_RECHAZADO:
            case OFICIO_DE_BANCO_AGRARIO_RECHAZADO2:
            case CITACION_DE_BANCO_AGRARIO_APROBADA:
            case CITACION_DE_BANCO_AGRARIO_APROBADA2:
            case   COMUNICACION_EXCEDENTE:
           case COMUNICACION_EXCEDENTE_CORDINADOR:
            case OFICIO_DE_BANCO_AGRARIO_COORDINARO:
             case OFICIO_DE_BANCO_AGRARIO_COORDINARO2:
            case SUBIR_COMUNICACION_EXCEDENTE:
           case  COMUNICACION_EXCEDENTE_APROBADO:
           case COMUNICACION_EJECUTOR:
          case  COMUNICACION_EJECUTOR_CORDINADOR:
            case  SUBIR_COMUNICACION_EJECUTOR:
                case COMUNICACION_EJECUTOR_APROBADO:
         case BLOQUEAR_EMBARGOS:
          case BLOQUEAR_EMBARGO:

           case BLOQUEAR_EMBARGO:
          case BLOQUEAR_EMBAR:
         
           

             case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO:

             case OFICIO_DE_ENTIDAD_QUE_EMBARGA_SECRETARIO2:
            case OFICIO_BIENES_INICIO_SECRETARIO:
            case CITACION_GENERADA_AL_DEUDOR_APROBADA:
            case CITACION_GENERADA_AL_DEUDOR_RECHAZADA:
            case SEGUNDA_CITACION_GENERADA_AL_DEUDOR_APROBADA:
            case SEGUNDA_CITACION_GENERADA_AL_DEUDOR_RECHAZADA:
            case SEGUNDA_CITACION_SUBIDA_AL_EXPEDIENTE:
            case CITACION_SUBIDA_AL_EXPEDIENTE:
            case SEGUNDA_CITACION_GENERADA_AL_DEUDOR_DEVOLUCION:
            // case SEGUNDA_CITACION_GENERADA_AL_DEUDOR_DEVOLUCION:

            case OFICIO_DEVOLUCION_TITULOS_GENERADO:
            case OFICIO_DEVOLUCION_TITULOS_PRE_APROBADO:
            case OFICIO_DEVOLUCION_TITULOS_APROBADO:
            case OFICIO_DEVOLUCION_TITULOS_RECHAZADO:
            case OFICIO_DEVOLUCION_TITULOS_SUBIDO_AL_EXPEDIENTE:
            case ACTA_ENVIO_TITULOS_TESORO_NACIONAL_APROBADA:
            case ACTA_ENVIO_TITULOS_TESORO_NACIONAL_PRE_APROBADA:
            case ACTA_ENVIO_TITULOS_TESORO_NACIONAL_GENERADA:
            case ACTA_ENVIO_TITULOS_TESORO_NACIONAL_RECHAZADA:
            case ACTA_ENVIO_TITULOS_TESORO_NACIONAL_SUBIDA:
            case INICIO_SECRETARIO:
            case OFICIO_SECRETARIO:
            case OFICIO_SECRETARIO2:
            // Auto Medidas Cautelares
            case AUTO_INICIO_SECRETARIO:
            // Fraccionamiento de titulo
            case FRACCIONAMIENTO_INICIO_SECRETARIO:
            // Generar oficio bienes
            case OFICIO_BIENES_INICIO_SECRETARIO:
            // bienes y servicio 1 auto
            case MUEBLES_SECRETARIO:
            case MUEBLES_COM_SECUESTRO_SECRETARIO:
            case MUEBLES_DES_SECUESTRO_SECRETARIO:
            case MUEBLES_RESPUESTA_SECRETARIO:
            case MUEBLES_COMISORIO_SECRETARIO:
            case MUEBLES_FECHA_SECRETARIO:
            case MUEBLES_PROYECTAR_AUTO_SECRETARIO:
            case MUEBLES_PROYECTAR_RESPUESTA_SECRETARIO:
            case INMUEBLES_PROYECTAR_AUTO_SECRETARIO:
            case VEHICULO_PROYECTAR_AUTO_SECRETARIO:
            case INMUEBLES_PROYECTAR_RESPUESTA_SECRETARIO:
            case MUEBLES_DILIGENCIA_SECRETARIO:
            case MUEBLES_ORDEN_SECRETARIO:
            case INMUEBLES_ORDEN_SECRETARIO:
            case VEHICULO_ORDEN_SECRETARIO:
            case INMUEBLES_SECRETARIO:
            case INMUEBLES_EMBARGO_SECRETARIO:
            case INMUEBLES_FECHA_SECRETARIO:
            case INMUEBLES_FECHA_SECRETARIO2:
            case INMUEBLES_SECUESTRO_SECRETARIO:
            case INMUEBLES_AUTO_SECUESTRO_SECRETARIO:
            case INMUEBLES_COMISION_SECUESTRO_SECRETARIO:
            case INMUEBLES_DESP_COM_SECUESTRO_SECRETARIO:
            case INMUEBLES_DOCUMENTO_SECUESTRO_SECRETARIO:
            case INMUEBLES_COMISORIO_SECUESTRO_SECRETARIO:
            case VEHICULO_SECRETARIO:
            case VEHICULO_EMBARGO_SECRETARIO:
            case VEHICULO_SECUESTRO_SECRETARIO:
            case VEHICULO_DESPACHO_SECRETARIO:
            case VEHICULO_COMISION_SECRETARIO:
            case VEHICULO_INCORPORANDO_SECRETARIO:
            case VEHICULO_FECHA_SECRETARIO:
            case VEHICULO_DILIGENCIA_SECRETARIO:
            case VEHICULO_OPOSICION_SECRETARIO:
            case VEHICULO_EMBARGO_OFICIO_SECRETARIO:
            //_______________________________________________________
            case ENVIAR_MODIFICACIONES:
            case INICIO_COORDINADOR:
            case DEVOLUCION:
            case SUBIR_ARCHIVO:

            case OFICIO_ENVIAR_MODIFICACIONES:
            case OFICIO_COORDINADOR:
            case OFICIO_DEVOLUCION:
            case OFICIO_SUBIR_ARCHIVO:

            case OFICIO_ENVIAR_MODIFICACIONES2:
            case OFICIO_COORDINADOR2:
            case OFICIO_DEVOLUCION2:
            case OFICIO_SUBIR_ARCHIVO2:

            case AUTO_ENVIAR_MODIFICACIONES:
            case AUTO_INICIO_COORDINADOR:
            case AUTO_DEVOLUCION:
            case AUTO_SUBIR_ARCHIVO:

            case FRACCIONAMIENTO_ENVIAR_MODIFICACIONES:
            case FRACCIONAMIENTO_INICIO_COORDINADOR:
            case FRACCIONAMIENTO_DEVOLUCION:
            case FRACCIONAMIENTO_SUBIR_ARCHIVO:

            case OFICIO_BIENES_ENVIAR_MODIFICACIONES:
            case OFICIO_BIENES_INICIO_COORDINADOR:
            case OFICIO_BIENES_DEVOLUCION:
            case OFICIO_BIENES_SUBIR_ARCHIVO:

            case MUEBLES_ENVIAR_MODIFICACIONES:
            case MUEBLES_COORDINARO:
            case MUEBLES_DEVOLUCION:
            case MUEBLES_SUBIR_ARCHIVO:

            case MUEBLES_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
            case MUEBLES_COM_SECUESTRO_COORDINARO:
            case MUEBLES_COM_SECUESTRO_DEVOLUCION:
            case MUEBLES_COM_SECUESTRO_SUBIR_ARCHIVO:

            case MUEBLES_DES_SECUESTRO_ENVIAR_MODIFICACIONES:
            case MUEBLES_DES_SECUESTRO_COORDINARO:
            case MUEBLES_DES_SECUESTRO_DEVOLUCION:
            case MUEBLES_DES_SECUESTRO_SUBIR_ARCHIVO:

            case MUEBLES_RESPUESTA_ENVIAR_MODIFICACIONES:
            case MUEBLES_RESPUESTA_COORDINARO:
            case MUEBLES_RESPUESTA_DEVOLUCION:
            case MUEBLES_RESPUESTA_SUBIR_ARCHIVO:

            case MUEBLES_COMISORIO_ENVIAR_MODIFICACIONES:
            case MUEBLES_COMISORIO_COORDINARO:
            case MUEBLES_COMISORIO_DEVOLUCION:
            case MUEBLES_COMISORIO_SUBIR_ARCHIVO:

            case MUEBLES_FECHA_ENVIAR_MODIFICACIONES:
            case MUEBLES_FECHA_COORDINARO:
            case MUEBLES_FECHA_DEVOLUCION:
            case MUEBLES_FECHA_SUBIR_ARCHIVO:

            case MUEBLES_DILIGENCIA_ENVIAR_MODIFICACIONES:
            case MUEBLES_DILIGENCIA_COORDINARO:
            case MUEBLES_DILIGENCIA_DEVOLUCION:
            case MUEBLES_DILIGENCIA_SUBIR_ARCHIVO:

            case MUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
            case MUEBLES_ORDEN_COORDINARO:
            case MUEBLES_ORDEN_DEVOLUCION:
            case MUEBLES_ORDEN_SUBIR_ARCHIVO:

            case INMUEBLES_ORDEN_ENVIAR_MODIFICACIONES:
            case INMUEBLES_ORDEN_COORDINARO:
            case INMUEBLES_ORDEN_DEVOLUCION:
            case INMUEBLES_ORDEN_SUBIR_ARCHIVO:

            case VEHICULO_ORDEN_ENVIAR_MODIFICACIONES:
            case VEHICULO_ORDEN_COORDINARO:
            case VEHICULO_ORDEN_DEVOLUCION:
            case VEHICULO_ORDEN_SUBIR_ARCHIVO:

            case MUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
            case MUEBLES_PROYECTAR_AUTO_COORDINARO:
            case MUEBLES_PROYECTAR_AUTO_DEVOLUCION:
            case MUEBLES_PROYECTAR_AUTO_SUBIR_ARCHIVO:

            case MUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
            case MUEBLES_PROYECTAR_RESPUESTA_COORDINARO:
            case MUEBLES_PROYECTAR_RESPUESTA_DEVOLUCION:
            case MUEBLES_PROYECTAR_RESPUESTA_SUBIR_ARCHIVO:

            case INMUEBLES_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_PROYECTAR_AUTO_COORDINARO:
            case INMUEBLES_PROYECTAR_AUTO_DEVOLUCION:
            case INMUEBLES_PROYECTAR_AUTO_SUBIR_ARCHIVO:

            case VEHICULO_PROYECTAR_AUTO_ENVIAR_MODIFICACIONES:
            case VEHICULO_PROYECTAR_AUTO_COORDINARO:
            case VEHICULO_PROYECTAR_AUTO_DEVOLUCION:
            case VEHICULO_PROYECTAR_AUTO_SUBIR_ARCHIVO:

            case INMUEBLES_PROYECTAR_RESPUESTA_ENVIAR_MODIFICACIONES:
            case INMUEBLES_PROYECTAR_RESPUESTA_COORDINARO:
            case INMUEBLES_PROYECTAR_RESPUESTA_DEVOLUCION:
            case INMUEBLES_PROYECTAR_RESPUESTA_SUBIR_ARCHIVO:

            case INMUEBLES_ENVIAR_MODIFICACIONES:
            case INMUEBLES_COORDINARO:
            case INMUEBLES_DEVOLUCION:
             case DEVOLVER_PROCESO:
            case INMUEBLES_SUBIR_ARCHIVO:

            case INMUEBLES_EMBARGO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_EMBARGO_COORDINARO:
            case INMUEBLES_EMBARGO_DEVOLUCION:
            case INMUEBLES_EMBARGO_SUBIR_ARCHIVO:

            case INMUEBLES_FECHA_ENVIAR_MODIFICACIONES:
            case INMUEBLES_FECHA_COORDINARO:
            case INMUEBLES_FECHA_COORDINARO2:
            case INMUEBLES_FECHA_DEVOLUCION:
              case INMUEBLES_FECHA_DEVOLUCION2:
            case INMUEBLES_FECHA_SUBIR_ARCHIVO:
             case INMUEBLES_FECHA_SUBIR_ARCHIVO2:

            case INMUEBLES_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_SECUESTRO_COORDINARO:
            case INMUEBLES_SECUESTRO_DEVOLUCION:
            case INMUEBLES_SECUESTRO_SUBIR_ARCHIVO:

            case INMUEBLES_AUTO_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_AUTO_SECUESTRO_COORDINARO:
            case INMUEBLES_AUTO_SECUESTRO_DEVOLUCION:
            case INMUEBLES_AUTO_SECUESTRO_SUBIR_ARCHIVO:

            case INMUEBLES_COMISION_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_COMISION_SECUESTRO_COORDINARO:
            case INMUEBLES_COMISION_SECUESTRO_DEVOLUCION:
            case INMUEBLES_COMISION_SECUESTRO_SUBIR_ARCHIVO:

            case INMUEBLES_DESP_COM_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_DESP_COM_SECUESTRO_COORDINARO:
            case INMUEBLES_DESP_COM_SECUESTRO_DEVOLUCION:
            case INMUEBLES_DESP_COM_SECUESTRO_SUBIR_ARCHIVO:

            case INMUEBLES_DOCUMENTO_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_DOCUMENTO_SECUESTRO_COORDINARO:
            case INMUEBLES_DOCUMENTO_SECUESTRO_DEVOLUCION:
            case INMUEBLES_DOCUMENTO_SECUESTRO_SUBIR_ARCHIVO:

            case INMUEBLES_COMISORIO_SECUESTRO_ENVIAR_MODIFICACIONES:
            case INMUEBLES_COMISORIO_SECUESTRO_COORDINARO:
            case INMUEBLES_COMISORIO_SECUESTRO_DEVOLUCION:
            case INMUEBLES_COMISORIO_SECUESTRO_SUBIR_ARCHIVO:

            case VEHICULO_ENVIAR_MODIFICACIONES:
            case VEHICULO_COORDINARO:
            case VEHICULO_DEVOLUCION:
            case VEHICULO_SUBIR_ARCHIVO:

            case VEHICULO_EMBARGO_ENVIAR_MODIFICACIONES:
            case VEHICULO_EMBARGO_COORDINARO:
            case VEHICULO_EMBARGO_DEVOLUCION:
            case VEHICULO_EMBARGO_SUBIR_ARCHIVO:
            case VEHICULO_EMBARGO_ENTREGA_SUBIR_ARCHIVO:

            case VEHICULO_SECUESTRO_ENVIAR_MODIFICACIONES:
            case VEHICULO_SECUESTRO_COORDINARO:
            case VEHICULO_SECUESTRO_DEVOLUCION:
            case VEHICULO_SECUESTRO_SUBIR_ARCHIVO:

            case VEHICULO_COMISION_ENVIAR_MODIFICACIONES:
            case VEHICULO_COMISION_COORDINARO:
            case VEHICULO_COMISION_DEVOLUCION:
            case VEHICULO_COMISION_SUBIR_ARCHIVO:

            case VEHICULO_DESPACHO_ENVIAR_MODIFICACIONES:
            case VEHICULO_DESPACHO_COORDINARO:
            case VEHICULO_DESPACHO_DEVOLUCION:
            case VEHICULO_DESPACHO_SUBIR_ARCHIVO:

            case VEHICULO_INCORPORANDO_ENVIAR_MODIFICACIONES:
            case VEHICULO_INCORPORANDO_COORDINARO:
            case VEHICULO_INCORPORANDO_DEVOLUCION:
            case VEHICULO_INCORPORANDO_SUBIR_ARCHIVO:

            case VEHICULO_FECHA_ENVIAR_MODIFICACIONES:
            case VEHICULO_FECHA_COORDINARO:
            case VEHICULO_FECHA_DEVOLUCION:
            case VEHICULO_FECHA_SUBIR_ARCHIVO:

            case VEHICULO_DILIGENCIA_ENVIAR_MODIFICACIONES:
            case VEHICULO_DILIGENCIA_COORDINARO:
            case VEHICULO_DILIGENCIA_DEVOLUCION:
            case VEHICULO_DILIGENCIA_SUBIR_ARCHIVO:

            case VEHICULO_EMBARGO_OFICIO_ENVIAR_MODIFICACIONES:
            case VEHICULO_EMBARGO_OFICIO_COORDINARO:
            case VEHICULO_EMBARGO_OFICIO_DEVOLUCION:
            case VEHICULO_EMBARGO_OFICIO_SUBIR_ARCHIVO:

            case VEHICULO_OPOSICION_ENVIAR_MODIFICACIONES:
            case VEHICULO_OPOSICION_COORDINARO:
            case VEHICULO_OPOSICION_DEVOLUCION:
            case VEHICULO_OPOSICION_SUBIR_ARCHIVO:

                $this->guardar_datos_temporales($post);
                break;
        }
       // echo $this->db->last_query();die();
        if (isset($post['post']['cod_siguiente2'])) {
            
            $gestion = $this->tipogestion($post['post']['cod_siguiente2']);
//            $id_traza = trazar($gestion, $post['post']['cod_siguiente2'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
            trazarProcesoJuridico($gestion, $post['post']['cod_siguiente2'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        } else {
            $gestion = $this->tipogestion($post['post']['cod_siguiente']);
            
            trazarProcesoJuridico($gestion, $post['post']['cod_siguiente'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
//            trazar($gestion, $post['post']['cod_siguiente'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        }
    }

    function view_documentos($id) {
        $this->db->select('RUTA_DOCUMENTO_GEN,FECHA_CREACION,NOMBRE_OFICIO');
        $this->db->where('COD_MEDIDACAUTELAR', $id);
        $dato = $this->db->get('MC_OFICIOS_GENERADOS');
        return $dato->result_array;
    }

    function view_medida_cautelar($post) {
        $this->db->select('COD_MEDIDACAUTELAR');
        $this->db->where('COD_PROCESO_COACTIVO', $post['post']['id_mc']);
        $dato = $this->db->get('MC_MEDIDASCAUTELARES');
        return $dato->result_array;
    }

 function subir_documento_doc2($post, $file_name) {
       
//        echo "hola";
//        if($post['post']['tipo']!=2):
//        $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id_mc']);
//        $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
//        $this->db->set("RUTA_DOCUMENTO_GEN", $file_name);
//        $this->db->set("FECHA_CREACION", $post['post']['fecha_radicado']);
//        $this->db->set("REVISADO_POR", ID_USER);
//        $this->db->set("NRO_RADICADO", $post['post']['radicado']);
//        $this->db->set("ESTADO", 1);
//        $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo']);
//        $this->db->where("ESTADO", 0);
//        $this->db->update("MC_OFICIOS_GENERADOS");
//        endif;
//        if ($this->db->affected_rows() == '0' || $post['post']['tipo']!==2 ) {
        $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
        $this->db->set("RUTA_DOCUMENTO_GEN", $file_name);

        $this->db->set("REVISADO_POR", ID_USER);
        if ($post['post']['tipo'] == 1):
        if (!empty($post['post']['fecha_radicado']) ):
            $this->db->set("FECHA_CREACION", "to_date('" . $post['post']['fecha_radicado'] . "','dd/mm/yyyy')", false);
              else:
            $this->db->set("FECHA_CREACION", "to_date('" . $post['post']['fecha_resolucion'] . "','dd/mm/yyyy')", false);
             endif;
        else:
            //$this->db->set("FECHA_CREACION", 'SYSDATE', FALSE);
             $this->db->set("FECHA_CREACION", "to_date('" . $post['post']['fecha_radicado'] . "','dd/mm/yyyy')", false);
            $this->db->set('FECHA_RADICADO', "to_date('" . $post['post']['fecha_radicado'] . "','dd/mm/yyyy')", false);
        endif;
        
        if ($post['post']['tipo'] == 1):

         if (!empty($post['post']['radicado']) ):
            $this->db->set("NRO_RADICADO", $post['post']['radicado']);
             else:
               $this->db->set("NRO_RADICADO", $post['post']['numero_resolucion']);
             endif;
        
        else:
            $this->db->set("NRO_RADICADO", $post['post']['radicado']);
        endif;

        $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo']);
        $this->db->set("ESTADO", 1);
        $this->db->insert("MC_OFICIOS_GENERADOS");
   // echo $this->db->last_query();die();
        //}
    }

    function subir_documento_doc1($post) {
        /*
        echo "MODEL:<br><pre>";
        print_r($post['post']);
        echo "</pre>";
        exit();
        */
        $prelacion = $post['post']['cod_siguiente_prelacion'];
        $this->db->set("APROBADO_POR", ID_USER);
        if (isset($post['file']['upload_data']['file_name'])):
            $this->db->set("RUTA_DOCUMENTO_MC", $post['file']['upload_data']['file_name']);
        endif;
        $this->db->set("FECHA_APROBACION", FECHA, false);
        $this->db->set("NOMBRE_DOCUMENTO_MC", "");
        $this->db->set("RUTA_DOCUMENTO_MC", "");
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        if($this->data['post']['cod_siguiente_prelacion'] == 1711) {
            $this->db->set("COD_RESPUESTAGESTION", $post['post']['cod_siguiente_prelacion']);
            $this->db->set("COD_RESPUESTAGESTION_BIENES", NULL);
        }

        if (!empty($this->data['post']['cod_siguiente_prelacion'])) {
            $this->db->update("MC_MEDIDASCAUTELARES");

            $gestion = $this->tipogestion($post['post']['cod_siguiente_prelacion']);
//            $id_traza = trazar($gestion, $post['post']['cod_siguiente_prelacion'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
            $id_traza = trazarProcesoJuridico($gestion, $post['post']['cod_siguiente_prelacion'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
            $this->db->set("COD_TIPOGESTION", $post['post']['cod_siguiente_prelacion']);
            $this->db->set("FECHA", FECHA, false);
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
            $this->db->update("MC_MEDIDASPRELACION");
        } else {
            echo $post['post']['tipo'];
            if (APROVACION_BIENES_GENERALES == $post['post']['cod_siguiente']) {
                $this->db->set("COD_RESPUESTAGESTION_BIENES", $post['post']['cod_siguiente']);
            } else if ($post['post']['tipo'] < 2 || $post['post']['tipo'] == 3 || $post['post']['tipo'] == 55 || $post['post']['tipo'] == 95 || $post['post']['tipo'] == 97 || $post['post']['tipo'] == 96 || $post['post']['tipo'] == 98 || $post['post']['tipo'] == 11) {
                $this->db->set("COD_RESPUESTAGESTION", $post['post']['cod_siguiente']);
            } else if ($post['post']['tipo'] == 2) {
                $this->db->set("COD_RESPUESTAGESTION", $post['post']['cod_siguiente']);
                $this->db->set("COD_RESPUESTAGESTION_BIENES", APROVACION_BIENES_GENERALES);
            } else {
                $this->db->set("COD_RESPUESTAGESTION_BIENES", $post['post']['cod_siguiente']);
            }
            $this->db->update("MC_MEDIDASCAUTELARES");
//echo $this->db->last_query();die();
            $gestion = $this->tipogestion($post['post']['cod_siguiente']);
//            $id_traza = trazar($gestion, $post['post']['cod_siguiente'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
            $id_traza = trazarProcesoJuridico($gestion, $post['post']['cod_siguiente'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        }



        //print_r($id_traza);die();
        if ($post['post']['cod_siguiente'] == 1011) {
            $post['post']['traza'] = $id_traza;
            $post['post']['dato'] = $post['post']['cod_siguiente'];
            $this->envio_avaluo($post);
        }
         if ($prelacion == 1011) {
            $post['post']['traza'] = $id_traza;
            $post['post']['dato'] = $prelacion;
            $this->envio_avaluo($post);
        }        
    }

    function MC_TRAZABILIDAD($post) {
        $this->db->select('MC_TRAZABILIDAD.FECHA_MODIFICACION,MC_TRAZABILIDAD.COMENTARIOS,MC_TRAZABILIDAD.GENERADO_POR,'
                . 'USUARIOS.APELLIDOS, USUARIOS.NOMBRES');
        $this->db->join("USUARIOS", "USUARIOS.IDUSUARIO=MC_TRAZABILIDAD.GENERADO_POR");
        $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->order_by("MC_TRAZABILIDAD.FECHA_MODIFICACION", 'desc');
        $dato = $this->db->get('MC_TRAZABILIDAD');
        $datos = $dato->result_array;
        $valor = "";
        foreach ($datos as $consulta) {
            $valor.=$consulta['COMENTARIOS'] . "<br>" . $consulta['FECHA_MODIFICACION'] . "<br>" . $consulta['NOMBRES'] . " " . $consulta['APELLIDOS'] . "<hr>";
        }
        return $valor;
    }

    function guardar_trazabilidad($post) {
       
        if ($post['post']['devol'] == OFICIO_BIENES_DEVOLUCION)
            $this->db->set("COD_RESPUESTAGESTION_BIENES", $post['post']['devol']);
        else
            $this->db->set("COD_RESPUESTAGESTION", $post['post']['devol']);

        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");

        $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->set("FECHA_MODIFICACION", FECHA, false);
        $this->db->set("COMENTARIOS", $post['post']['infor']);
        $this->db->set("GENERADO_POR", ID_USER);
        $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->insert("MC_TRAZABILIDAD");
        $gestion = $this->tipogestion($post['post']['devol']);
        if ($this->data['post']['infor'] == 'devolucion'):
            $this->guardarDocDevolucion($post);
        endif;
//        $id_traza = trazar($gestion, $post['post']['devol'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        trazarProcesoJuridico($gestion, $post['post']['devol'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        return true;
    }

    function guardarDocDevolucion($post) {
        if ($post['post']['tipo_doc'] == 2)://
            if ($post['post']['id_documento'] != 0)://Cuando no corrigio ninguno
                $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
                $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
                $this->db->set("FECHA_CREACION", FECHA, false);
                $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
                $this->db->set("CREADO_POR", ID_USER);
                $this->db->where("COD_OFICIO_MC", $post['post']['id_documento']);
                $this->db->update("MC_OFICIOS_GENERADOS");
            endif;


            return true;
        else:
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
            $this->db->set("FECHA_CREACION", FECHA, false);
            $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
            $this->db->set("CREADO_POR", ID_USER);
            $this->db->where("COD_OFICIO_MC", $post['post']['id_documento']);
            $this->db->update("MC_OFICIOS_GENERADOS");
            return true;
        endif;
    }

    function guardar_trazabilidad_prelacion($post) {
        $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->set("FECHA_MODIFICACION", FECHA, false);
        $this->db->set("COMENTARIOS", $post['post']['infor']);
        $this->db->set("GENERADO_POR", ID_USER);
        $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->insert("MC_TRAZABILIDAD");

        $this->db->set("COD_TIPOGESTION", $post['post']['devol']);
        $this->db->set("FECHA", FECHA, false);
        if ($post['bloqueo'] == 0)
            $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        else {
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->where("COD_CONCURRENCIA", $post['bloqueo']);
        }
        $this->db->update("MC_MEDIDASPRELACION");
        $gestion = $this->tipogestion($post['post']['devol']);
//        $id_traza = trazar($gestion, $post['post']['devol'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        trazarProcesoJuridico($gestion, $post['post']['devol'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
    }

    function guardar_documentos_bancarios($id, $name) {
        $this->db->set("COD_MEDIDACAUTELAR", $id);
        $name = str_replace(" ", "_", $name);
        $this->db->set("NOMBRE_OFICIO_EMBARGO", $name);
        //nelson falta colocar el id del usuario pero se va a usar para colocar valor
        $this->db->insert("MC_EMBARGOBIENES");
    }

    function delete_documentos_bancarios($id) {
        $this->db->where('COD_EMBARGO_DINEROS', $id);
        $this->db->delete("MC_EMBARGOBIENES");
    }

    function delete_documentos_mc($id) {
        $this->db->where('COD_OFICIO_MC', $id);
        $this->db->delete("MC_OFICIOS_GENERADOS");
    }

    function select_documentos_bancarios($id) {
        $this->db->select("COD_EMBARGO_DINEROS,COD_MEDIDACAUTELAR,NOMBRE_OFICIO_EMBARGO");
        $this->db->where('COD_MEDIDACAUTELAR', $this->data['post']['id']);
        $dato = $this->db->get("MC_EMBARGOBIENES");
        $datos = $dato->result_array;


//        echo $this->db->last_query();
//        die();
        $informacion = "";
        $i = 1;
        foreach ($datos as $dato) {
            $dato['COD_EMBARGO_DINEROS'] = '"' . $dato['COD_EMBARGO_DINEROS'] . '","' . $dato['COD_MEDIDACAUTELAR'] . '"';

            if ($i % 2 == 0)
                $informacion.="<tr>";
            else
                $informacion.='<tr style="background-color: #CCC">';

            $informacion.=""
                    . "<td><a href='" . base_url() . RUTA_DES . $id . "/" . $dato['NOMBRE_OFICIO_EMBARGO'] . "' target='_blank'>" . $dato['NOMBRE_OFICIO_EMBARGO'] . "</a></td>"
                    . "<td><a href='javascript:' onclick='eliminar(" . $dato['COD_EMBARGO_DINEROS'] . ")'><i class='fa fa-trash-o' title='Eliminar'></i>
</a></td>"
                    . "</tr>";
            $i++;
        }

        return $informacion;
    }
function select_documentos_mc($id, $t_doc) {
        $this->db->select("COD_OFICIO_MC,COD_MEDIDACAUTELAR,RUTA_DOCUMENTO_GEN,NRO_RADICADO,FECHA_CREACION");
        $this->db->where('ESTADO', 1);
        $this->db->where('COD_MEDIDACAUTELAR', $id);
        $this->db->where('TIPO_DOCUMENTO', $t_doc);
        $dato = $this->db->get("MC_OFICIOS_GENERADOS");
        $datos = $dato->result_array;


       //  echo $this->db->last_query();
    // die();
        $informacion = "";
        $i = 1;
        foreach ($datos as $dato) {

            if ($i % 2 == 0)
                $informacion.="<tr>";
            else
                $informacion.='<tr style="background-color: #CCC">';

            $informacion.=""
                    . "<td><a href='" . base_url() . RUTA_DES . $id . "/" . $dato['RUTA_DOCUMENTO_GEN'] . "' target='_blank'>" . $dato['RUTA_DOCUMENTO_GEN'] . "</a></td>"
                    . "<td>" . $dato['NRO_RADICADO'] . "</td>"
                    . "<td>" . $dato['FECHA_CREACION'] . "</td>"
                    . "<td><a href='javascript:' onclick='eliminar(" . $dato['COD_OFICIO_MC'] . ")'><i class='fa fa-trash-o' title='Eliminar'></i>
</a></td>"
                    . "</tr>";
            $i++;
        }
        $campo = '<tr><td colspan="4"><input type="hidden" name="cantidad" id="cantidad" value="' . $i . '"></td></tr>';
        $informacion = $informacion . $campo;
    //  echo $informacion;//die();
        return $informacion;
    }

    function count_documentos_bancarios($id) {
        $this->db->select("count(COD_MEDIDACAUTELAR) as TOTAL");
        $this->db->where('COD_MEDIDACAUTELAR', $id);
        $dato = $this->db->get("MC_EMBARGOBIENES");
        $datos = $dato->result_array;
        return $datos;
    }

    function resumen_documentos_bancarios($post) {
       //  print_r($post);die();
        for ($i = 0; $i < count($post['post']['banco']); $i++) {
//            $this->db->set("NOMBRE_OFICIO_EMBARGO", $post['post']['banco'][]);
            $this->db->set("FECHA_GENERACION_DOC", FECHA, false);
           
            $this->db->set("ELABORADO_POR", ID_USER);
            $this->db->set("COD_BANCO", $post['post']['banco'][$i]);
            $this->db->set("VALOR", $post['post']['valor'][$i]);
             
            $this->db->set("OBSERVACIONES", $post['post']['observaciones'][$i]);
            $this->db->where("COD_EMBARGO_DINEROS", $post['post']['cod'][$i]);
            $this->db->update("MC_EMBARGOBIENES");
            if($post['post']['valor'][$i] > $post['deuda']){
                 $ecxedente=$post['post']['valor'][$i]-$post['deuda'];
               $this->db->set("EXCEDENTES", $ecxedente); 
               $this->db->where("COD_PROCESO_COACTIVO", $post['post']['cod_fis']);
                 $this->db->update("PROCESOS_COACTIVOS");   
                } 

             if($post['post']['valor'][$i] >= $post['deuda']){
                 $notificacion=1;
               $this->db->set("CAMPO", $notificacion); 
                 $this->db->where("COD_PROCESO_COACTIVO", $post['post']['cod_fis']);
                 $this->db->update("PROCESOS_COACTIVOS");  
                }
        }
        $this->db->set("OBSERVACIONES_REVISION", $post['post']['t']);
        echo $this->db->last_query();
        $this->documentos_bancarios($post);
    }

    function MC_EMBARGOBIENES($post) {
        $this->db->select("MC_EMBARGOBIENES.COD_BANCO,MC_EMBARGOBIENES.OBSERVACIONES,
        MC_EMBARGOBIENES.COD_EMBARGO_DINEROS,MC_EMBARGOBIENES.NOMBRE_OFICIO_EMBARGO,"
                . "MC_EMBARGOBIENES.VALOR,MC_EMBARGOBIENES.COD_BANCO,BANCO.NOMBREBANCO");
        $this->db->join("BANCO", "BANCO.IDBANCO=MC_EMBARGOBIENES.COD_BANCO", "LEFT");
        $this->db->where("MC_EMBARGOBIENES.COD_MEDIDACAUTELAR", $post['post']['id']);
        //nelson falta colocar el id del usuario pero se va a usar para colocar valor
        $dato = $this->db->get("MC_EMBARGOBIENES");
        //echo $this->db->last_query();
        return $dato->result_array;
    }

    function eliminar_documentos_bancarios($id, $name) {
        $this->db->where("COD_MEDIDACAUTELAR", $id);
        $this->db->where("NOMBRE_OFICIO_EMBARGO", $name);
        //nelson falta colocar el id del usuario pero se va a usar para colocar valor
        $this->db->delete("MC_EMBARGOBIENES");
    }

    function documentos_bancarios($post) {
//        echo $post['post']['cod_siguiente']; die();
        $this->db->set("APROBADO_POR", ID_USER);
        $this->db->set("COD_RESPUESTAGESTION", $post['post']['cod_siguiente']);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");

        $gestion = $this->tipogestion($post['post']['cod_siguiente']);
//        $id_traza = trazar($gestion, $post['post']['cod_siguiente'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        trazarProcesoJuridico($gestion, $post['post']['cod_siguiente'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
    }

    function subir_documentos_nuevos($post) {
        $this->db->set("COD_RESPUESTAGESTION", $post['post']['cod_siguiente']);
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->update("MC_MEDIDASCAUTELARES");
    }

    function bancos() {
        $this->db->select("IDBANCO,NOMBREBANCO");
        $dato = $this->db->get("BANCO");
        return $dato->result_array;
    }

    function MC_TIPO_PRIORIDAD() {
        $this->db->select("COD_PRIORIDAD,NOMBRE_PRIORIDAD");
        $this->db->where("ACTIVO", '0');
        $dato = $this->db->get("MC_TIPO_PRIORIDAD");
        return $dato->result_array;
    }

    function accion_de($post) {
     // print_r($post['post']['cod_fis']);die();
        if (!empty($post['post']['dato'])) {
          // print_r($post['post']['dato']);die();
            switch ($post['post']['dato']) {
                case 1:
                    $no_info = MUEBLE_COMISIONAR1;
                    $gestion = "144";
                    break;
                case 2:
              
                    $no_info = INMUEBLES_INICIO;
                    $gestion = "139";
                     // print_r($no_info );die();
                    break;
                case 3:
                    $no_info = VEHICULO_INICIO;
                    $gestion = "140";
                    break;
            }
        }
        $this->db->select('COD_MEDIDAPRELACION');
        $this->db->where('COD_MEDIDACAUTELAR', $post['post']['id']);
        $this->db->where('COD_CONCURRENCIA', $post['post']['dato']);
        $dato = $this->db->get('MC_MEDIDASPRELACION');

        $this->db->set('ACTIVO', '0');
         $this->db->set("COD_TIPOGESTION", $no_info);
        $this->db->where('COD_MEDIDACAUTELAR', $post['post']['id']);
        $this->db->where('COD_CONCURRENCIA', $post['post']['dato']);
        $this->db->update('MC_MEDIDASPRELACION');//echo $this->db->last_query();
        if (!empty($dato->result_array[0])) {
            
           // echo"aqui";die();
            $datos = $dato->result_array[0];
        } else {
            $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("COD_CONCURRENCIA", $post['post']['dato']);
            $this->db->set("COD_TIPOGESTION", $no_info);
            $this->db->set("FECHA", FECHA, false);
            $this->db->insert('MC_MEDIDASPRELACION');

            $this->db->select('COD_MEDIDAPRELACION');
            $this->db->where('COD_MEDIDACAUTELAR', $post['post']['id']);
            $this->db->where('COD_CONCURRENCIA', $post['post']['dato']);
            $dato = $this->db->get('MC_MEDIDASPRELACION');
            $datos = $dato->result_array[0];
        }
        $cantidad = count($post['post']['mueble']);

        $this->db->where("COD_MEDIDAPRELACION", $datos['COD_MEDIDAPRELACION']);
        $this->db->where("COD_TIPOINMUEBLE", $post['post']['dato']);
        $this->db->delete('MC_PRELACIONTITULO');//echo $this->db->last_query();

        for ($i = 0; $i < $cantidad; $i++) {
            $this->db->set("COD_PRIORIDAD", $post['post']['id_prioridad'][$i]);
            $this->db->set("COD_BANCO", $post['post']['id_banco'][$i]);
            $this->db->set("COD_TIPOINMUEBLE", $post['post']['dato']);
            $this->db->set("CREADO_POR", ID_USER);
            $this->db->set("FECHA_CREACION", FECHA, false);
            $this->db->set("VALOR", $post['post']['valor'][$i]);
            $this->db->set("OBSERVACIONES", $post['post']['observacion'][$i]);
            $this->db->set("NUM_MATRICULA", $post['post']['mueble'][$i]);
            $this->db->set("COD_MEDIDAPRELACION", $datos['COD_MEDIDAPRELACION']);
            $this->db->insert('MC_PRELACIONTITULO'); echo $this->db->last_query();
        }
        $id_traza = trazarProcesoJuridico($gestion, $no_info, '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
    }

    function bloquear_vehiculos($post) {
        $this->db->set("ACTIVO", '1');
        $this->db->set("FECHA", FECHA, false);
        $this->db->where("COD_MEDIDAPRELACION", $post['id_prelacion']);
        $this->db->update('MC_MEDIDASPRELACION');
        $id_traza = trazarProcesoJuridico('553', '1368', '', $post['cod_fis'], '', '', $comentarios = "", ID_USER);
    }

    function avance($post) {
        /*
        echo "MODELO:<br><pre>";
        print_r($post['post']);
        echo "</pre>";
        exit();
        */
        $this->db->set("COD_TIPOGESTION", $post['post']['dato']);
        //$this->db->set("FECHA_CREACION", "to_date('" . FECHA . "','dd/mm/yyyy')", false);
        $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        $this->db->update('MC_MEDIDASPRELACION');
        //echo $this->db->last_query();die();
        $gestion = $this->tipogestion($post['post']['dato']);
//        $id_traza = trazar($gestion, $post['post']['dato'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        $id_traza = trazarProcesoJuridico($gestion, $post['post']['dato'], '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
       // print_r($id_traza);die();
        $post['post']['traza'] = $id_traza;
       //echo "AVANCE:<br>".$this->db->last_query();
        //print_r($id_traza);
        $this->envio_avaluo($post);
        
    }

    function envio_avaluo($post) {
        if (
                $post['post']['dato'] == VIKY_OPOSICION ||
                $post['post']['dato'] == VIKY_FAVORABLE ||
                $post['post']['dato'] == VIKY_AVALUO) {

            $this->db->select('MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR,MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO,MC_MEDIDASPRELACION.COD_CONCURRENCIA as TIPO_INMUEBLE,
 MC_PRELACIONTITULO.COD_CONCURRENCIA AS CONCURRENCIA');
            $this->db->join('MC_MEDIDASPRELACION', ' MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR=MC_MEDIDASPRELACION.COD_MEDIDACAUTELAR');
            $this->db->join('MC_PRELACIONTITULO', ' MC_PRELACIONTITULO.COD_MEDIDAPRELACION=MC_MEDIDASPRELACION.COD_MEDIDAPRELACION');
            $this->db->where("MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR", $post['post']['id']);
            $dato = $this->db->get('MC_MEDIDASCAUTELARES');
            $datos = $dato->result_array;
            $cantidad = count($datos);
//            print_r($id_traza);
            for ($i = 0; $i < $cantidad; $i++) {
                $this->db->set('COD_PROCESO_COACTIVO', $datos[$i]['COD_PROCESO_COACTIVO']);
                $this->db->set('COD_TIPO_INMUEBLE', $datos[$i]['TIPO_INMUEBLE']);
                $this->db->set('COD_MEDIDACAUTELAR', $datos[$i]['COD_MEDIDACAUTELAR']);
                $this->db->set('COD_CONCURRENCIA', $datos[$i]['CONCURRENCIA']);
                $this->db->set('COD_TIPORESPUESTA', $post['post']['dato']);
                //$this->db->set('COD_GESTION_COBRO', 536);
                $this->db->set('COD_GESTION_COBRO', $post['post']['traza']);
                $this->db->insert('MC_AVALUO');
                //echo "AVALUO:<br>".$this->db->last_query();
            }
        }
    }

    function ofice($post) {
        $this->db->set("TIPO_DOCUMENTO", '99');
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->update("MC_OFICIOS_GENERADOS");
    }

    function reiniciar_proceso($post) {
//        $this->db->set("BLOQUEO", '1');
        $this->db->set("COD_RESPUESTAGESTION_BIENES", OFICIO_BIENES);
        $this->db->where("COD_PROCESO_COACTIVO", $post['post']['cod_fis']);
        $this->db->update("MC_MEDIDASCAUTELARES");
        echo $this->db->last_query();
    }

    function tipogestion($id) {
       // echo "<prehh>"; print_r($id); echo "</pre>";die();
        $this->db->select("COD_TIPOGESTION");
        $this->db->where("COD_RESPUESTA", $id);
        $dato = $this->db->get('RESPUESTAGESTION');
        $datos = $dato->result_array[0]['COD_TIPOGESTION'];
      //  echo "<pre>hhh"; print_r($dato); echo "</pre>";//die();
        return $datos;
    }

    function guardar_datos_temporales($post) {

     //echo "<pre> aver "; print_r($post); echo "</pre>";// die();

        if($post['post']['cod_siguiente'] == 320||$post['post']['cod_siguiente'] == 1628
         ||$post['post']['cod_siguiente'] == 675||$post['post']['cod_siguiente'] == 649
          ||$post['post']['cod_siguiente'] == 1034 ||$post['post']['cod_siguiente'] == 1657
          ||$post['post']['cod_siguiente'] == 702 ||$post['post']['cod_siguiente'] == 706
          ||$post['post']['cod_siguiente'] == 1655 ||$post['post']['cod_siguiente'] == 1640
           ||$post['post']['cod_siguiente'] == 1752||$post['post']['cod_siguiente'] == 2072
          ||$post['post']['cod_siguiente'] == 1751  ||$post['post']['cod_siguiente'] == 2068
          ||$post['post']['cod_siguiente'] == 2083  ||$post['post']['cod_siguiente'] == 2078
           ||$post['post']['cod_siguiente'] == 2085 ||$post['post']['cod_siguiente'] == 2089
            ||$post['post']['cod_siguiente'] == 2090  ||$post['post']['cod_siguiente'] == 1753
         
         ){ 
              $this->db->select("NOMBRE_OFICIO");
             $this->db->where("NOMBRE_OFICIO", $post['post']['titulo']);
             $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
             $dato = $this->db->get('MC_OFICIOS_GENERADOS');
             $datos = $dato->result_array[0]['NOMBRE_OFICIO'];
    echo "<pre>hhh"; print_r($datos); echo "</pre>";//die();
            

              if (empty($datos)){
          
                $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
                $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
                $this->db->set("FECHA_CREACION", FECHA, false);
                $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
                $this->db->set("CREADO_POR", ID_USER);
                $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
                $this->db->insert("MC_OFICIOS_GENERADOS");

            }
            else{
            
            }

   }
   else{
        if ($post['post']['tipo_doc'] == 2 )://Cuando es un oficio se generan varios oficios
           
//            echo $post['post']['id_documento'];
            if (empty($post['post']['id_documento'])):

                $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
                $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
                $this->db->set("FECHA_CREACION", FECHA, false);
                $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
                $this->db->set("CREADO_POR", ID_USER);
                $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
                $this->db->insert("MC_OFICIOS_GENERADOS");

            else:
                if ($post['post']['cod_siguiente'] == 620)://Actualiza todos los oficios


                    $this->db->set("ESTADO", 0);
                    $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
                    $this->db->where("TIPO_DOCUMENTO", 2);
                    $this->db->update("MC_OFICIOS_GENERADOS");

                else:
                
                    $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
                    $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
                    $this->db->set("FECHA_CREACION", FECHA, false);
                    $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
                    $this->db->set("CREADO_POR", ID_USER);


                    $this->db->where("COD_OFICIO_MC", $post['post']['id_documento']);
                    $this->db->update("MC_OFICIOS_GENERADOS");
                endif;

            endif;


   
 


        else:
     
            $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
            $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
            $this->db->set("FECHA_CREACION", FECHA, false);
            $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
            $this->db->set("CREADO_POR", ID_USER);
            $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
            $this->db->update("MC_OFICIOS_GENERADOS");

            if ($this->db->affected_rows() == '0'):
          
                $this->db->set("COD_MEDIDACAUTELAR", $post['post']['id']);
                $this->db->set("RUTA_DOCUMENTO_GEN", $post['post']['nombre'] . ".txt");
                $this->db->set("FECHA_CREACION", FECHA, false);
                $this->db->set("NOMBRE_OFICIO", $post['post']['titulo']);
                $this->db->set("CREADO_POR", ID_USER);
                $this->db->set("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
                $this->db->insert("MC_OFICIOS_GENERADOS");

            endif;

        endif;
   }
//
//      
echo $this->db->last_query();//die(); 
        $oficios = $this->consultaOficios($post);
    }

    function consultaOficios($post) {
//echo "<pre>"; print_r($post['post']['tipo_doc']); echo "</pre>"; die();
        $this->db->select('MC.COD_MEDIDACAUTELAR,MC.OBSERVACIONES,MC.COD_OFICIO_MC,MC.NOMBRE_OFICIO,RUTA_DOCUMENTO_GEN,FECHA_CREACION');
        $this->db->from('MC_OFICIOS_GENERADOS MC');
        $this->db->where('MC.COD_MEDIDACAUTELAR', $post['post']['id']);
        $this->db->where('MC.TIPO_DOCUMENTO', 2);
        $this->db->where('MC.ESTADO', 0);
        $query = $this->db->get();
        //echo $this->db->last_query();
        $resultado = $query->result_array();

        return $resultado;
    }

    function informacion_prelacion($post) {
        $this->db->select("MC_PRELACIONTITULO.NUM_MATRICULA,MC_PRELACIONTITULO.OBSERVACIONES");
        $this->db->join('MC_PRELACIONTITULO', 'MC_PRELACIONTITULO.COD_MEDIDAPRELACION=MC_MEDIDASPRELACION.COD_MEDIDAPRELACION');
        $this->db->where("MC_MEDIDASPRELACION.COD_MEDIDACAUTELAR", $post['id']);
        $this->db->where("MC_MEDIDASPRELACION.COD_CONCURRENCIA", $post['dato']);
        $dato = $this->db->get('MC_MEDIDASPRELACION');
        $datos = $dato->result_array;
        $html = "";
        $i = 3;
        foreach ($datos as $value) {
            $html.='<tr id="trr' . $i . '" align="center">'
                    . '<td><input class="infor_table" type="text" style="width: 100px;" value="' . $value['NUM_MATRICULA'] . '" maxlength="32" name="mueble[]"></td>'
                    . '<td></td>'
                    . '<td><input class="infor_table" type="text" value="' . $value['OBSERVACIONES'] . '" name="observacion[]"></td>'
                    . '<td></td>'
                    . '<td></td>'
                    . '<td><button id="del" class="eliminar btn btn-primary" onclick="eliminar_col(' . $i . ')" type="button">Eliminar</button></td>'
                    . "</tr>";
            $i++;
        }
       // echo $this->db->last_query();
        return $html;
    }

    /* Método que permite validar si el titulo recibido cubre la deuda */

    function consultaDeuda($cod_coactivo) {
        $this->db->select('VW.SALDO_DEUDA');
        $this->db->from('VW_PROCESOS_COACTIVOS_0002 VW');
        $this->db->where('VW.COD_PROCESO_COACTIVO', $cod_coactivo);
        $resultado = $this->db->get();
//echo $this->db->last_query();
        $resultado = $resultado->result_array();
        return $resultado[0]['SALDO_DEUDA'];
    }
   
    //////////////////////////YURI ALEXANDRA RAMIREZ CDS-POPYAN ///////////////////////////
    function consultaobligaciones($cod_empresa,$cod_regional) {
  //  echo $cod_regional;die();  
 $query = $this -> db -> query("SELECT DISTINCT  USUARIOS.NOMBRES|| APELLIDOS AS EJECUTOR, VW.FECHA_COACTIVO, VW.SALDO_DEUDA,
  PC.COD_PROCESOPJ, PC.COD_PROCESO_COACTIVO
 FROM  USUARIOS,REGIONAL,PROCESOS_COACTIVOS PC  
 JOIN VW_PROCESOS_COACTIVOS_0002 VW  ON VW.COD_PROCESO_COACTIVO= PC.COD_PROCESO_COACTIVO
  WHERE USUARIOS .Cod_Regional = REGIONAL.Cod_Regional
  AND VW.NOMBRE_REGIONAL =   REGIONAL.NOMBRE_REGIONAL
 AND PC.IDENTIFICACION =  '$cod_empresa'
 AND USUARIOS.IDUSUARIO=REGIONAL.CEDULA_COORDINADOR
AND REGIONAL.Cod_Regional='$cod_regional'");
        //echo $this->db->last_query();die();                           
    if ($query -> num_rows() > 0)
    {
      return $query ->result_array();
    }
  
    }

    function consultaobligaciones1($cod_empresa) {
        $this->db->select("PC.COD_PROCESOPJ,VW.FECHA_COACTIVO,VW.SALDO_DEUDA,PC.COD_PROCESO_COACTIVO");
        $this->db->from('PROCESOS_COACTIVOS PC');
         $this->db->join('VW_PROCESOS_COACTIVOS_0002 VW ', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->where('PC.IDENTIFICACION', $cod_empresa);
        $resultado = $this->db->get();
 //echo $this->db->last_query();die();
        $resultado = $resultado->result_array();
        return $resultado;
    }

     function getEmpresas($user) {
        $datoss = $this->db->query("
     SELECT DISTINCT PC.COD_PROCESO_COACTIVO,VW.IDENTIFICACION,VW.SALDO_DEUDA,VW.FECHA_COACTIVO,PC.EXCEDENTES,PC.CAMPO
        FROM VW_PROCESOS_COACTIVOS_0002 VW
        JOIN PROCESOS_COACTIVOS PC  ON VW.COD_PROCESO_COACTIVO= PC.COD_PROCESO_COACTIVO
        JOIN REGIONAL RN ON VW.COD_REGIONAL = RN.COD_REGIONAL
       AND( RN.CEDULA_COORDINADOR='$user') 
      
        AND VW.SALDO_DEUDA > 0 
        AND PC.NOTIFICACION = 0");

        $datoss = $datoss->result_array();
        return $datoss;
    }


    function getNotificaciones($user) {
        $datoss = $this->db->query("
     SELECT DISTINCT PC.COD_PROCESO_COACTIVO,VW.IDENTIFICACION,VW.SALDO_DEUDA,VW.FECHA_COACTIVO,PC.EXCEDENTES,PC.CAMPO
        FROM VW_PROCESOS_COACTIVOS_0002 VW
        JOIN PROCESOS_COACTIVOS PC  ON VW.COD_PROCESO_COACTIVO= PC.COD_PROCESO_COACTIVO
        JOIN REGIONAL RN ON VW.COD_REGIONAL = RN.COD_REGIONAL
           
         AND( VW.IDENTIFICACION='$user') 
        AND VW.SALDO_DEUDA > 0 
          
        AND PC.NOTIFICACION = 0");

        $datoss = $datoss->result_array();
        return $datoss;
    }
////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////
    function consultaValorTitulos($cod_cautelar) {
        $this->db->select('MC.VALOR');
        $this->db->from('MC_EMBARGOBIENES MC');
        $this->db->where('MC.COD_MEDIDACAUTELAR', $cod_cautelar);
//        $this->db->where('MC.COD_MEDIDACAUTELAR',$cod_cautelar);
        $resultado = $this->db->get();
//echo $this->db->last_query();
        $resultado = $resultado->result_array();
        return $resultado;
    }

    function actualizarMedida($cod_coactivo, $cod_medida , $codsiguiente) {
         
        $this->db->set('COD_RESPUESTAGESTION', $cod_medida);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo );
        $this->db->where('COD_MEDIDACAUTELAR', $codsiguiente);
        $this->db->update("MC_MEDIDASCAUTELARES");
 //echo $this->db->last_query(); die();
         $gestion = $this->tipogestion($codsiguiente);
//        $id_traza = trazar($gestion, $post['post']['dato'], $post['post']['cod_fis'], $post['post']['nit'], $cambiarGestionActual = 'S', $comentarios = "");
        $id_traza = trazarProcesoJuridico($gestion, $codsiguiente, '', $cod_coactivo, '', '', $comentarios = "", ID_USER);
       // print_r($id_traza);die();
        $post['post']['traza'] = $id_traza;
     // echo $this->db->last_query();
       // print_r($id_traza);die();
      
        //echo $this->db->last_query();

        return true;
    }

    function citacion($post) {
        $this->db->select("RUTA_DOCUMENTO_GEN AS RUTA_DOCUMENTO_MC, FECHA_RADICADO");
        $this->db->where("COD_MEDIDACAUTELAR", $post['post']['id']);
        $this->db->where("TIPO_DOCUMENTO", $post['post']['tipo_doc']);
        $this->db->where("ESTADO", 1);
        $this->db->order_by("FECHA_CREACION", "ASC");
        $dato = $this->db->get("MC_OFICIOS_GENERADOS");

        return $dato->result_array;
    }

    function getEmpresa($nit) {
        $this->db->select("NOMBRE_EMPRESA, CODEMPRESA, REPRESENTANTE_LEGAL,TELEFONO_FIJO,ACTIVO,DIRECCION");
        $this->db->where("CODEMPRESA", $nit);
        $dato = $this->db->get("EMPRESA");
        //echo $this->db->last_query();
        return $dato->result_array;
    }

    function Resolución($ID) {
        $this->db->select('PC.COD_PROCESOPJ, T.FECHA AS FECHA_AVOCA, VW.CONCEPTO');
        $this->db->from('MC_MEDIDASCAUTELARES MC');
        $this->db->join('PROCESOS_COACTIVOS PC', 'MC.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('TRAZAPROCJUDICIAL T', 'T.COD_JURIDICO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS_0001 VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->where('MC.COD_PROCESO_COACTIVO', $ID);
        $resultado = $this->db->get();
        //echo $this->db->last_query($resultado);die;
        $resultado = $resultado->result_array();

        return $resultado;
    }

    function getRespuesta($idGestion) {
        $array = array();
        $this->db->select('NOMBRE_GESTION,COD_TIPOGESTION,COD_RESPUESTA');
        $this->db->from("RESPUESTAGESTION");
        $this->db->where('COD_RESPUESTA', $idGestion);
        $query = $this->db->get();
        //echo $this->db->last_query();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            return $array[0];
        }
    }
    function getNotifica($id, $tipo, $estado, $cod_prelacion) {
        $array = array();
        $this->db->select('COD_AVISONOTIFICACION,OBSERVACIONES,PLANTILLA,COD_ESTADO,DOC_COLILLA,NUM_RADICADO_ONBASE,FECHA_ONBASE,COD_MOTIVODEVOLUCION,COD_COMUNICACION,FECHA_NOTIFICACION,COD_MANDAMIENTOPAGO,NOMBRE_DOC_CARGADO,ESTADO_NOTIFICACION,DOC_FIRMADO,RECURSO,EXCEPCION');
        $this->db->from("AVISONOTIFICACION");
        $this->db->where('COD_PROCESO_COACTIVO', $id);
        $this->db->where('COD_TIPONOTIFICACION', $tipo);
        $this->db->where('COD_MEDIDAPRELACION', $cod_prelacion);
        $this->db->where('ESTADO_NOTIFICACION', $estado);
        $this->db->order_by('COD_AVISONOTIFICACION', 'DESC');
        $query = $this->db->get();
         //print_r($this->db->last_query($query));die();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            return $array[0];
        }
    }

    function get_Notifica($id, $tipo, $excep, $rec, $estado) {
        $array = array();
        $this -> db -> select('COD_AVISONOTIFICACION,OBSERVACIONES,PLANTILLA,COD_ESTADO,DOC_COLILLA,NUM_RADICADO_ONBASE,FECHA_ONBASE,COD_MOTIVODEVOLUCION,COD_COMUNICACION,FECHA_NOTIFICACION,COD_MANDAMIENTOPAGO,NOMBRE_DOC_CARGADO,ESTADO_NOTIFICACION,DOC_FIRMADO,RECURSO,EXCEPCION');
        $this -> db -> from("AVISONOTIFICACION");
        $this -> db -> where('COD_PROCESO_COACTIVO', $id);
        $this -> db -> where('COD_TIPONOTIFICACION', $tipo);
        $this -> db -> where('ESTADO_NOTIFICACION', $estado);
        $this -> db -> where('EXCEPCION', $excep);
        $this -> db -> where('RECURSO', $rec);
        $this -> db -> order_by('COD_AVISONOTIFICACION', 'DESC');
        $query = $this -> db -> get();
        //print_r($this->db->last_query($query));die();
        if ($query -> num_rows() > 0) {
            $array = $query -> result();
            return $array[0];
        }
    }

    function add_aviso($data) {
        $this -> db -> trans_start();
        $this -> db -> set('COD_AVISONOTIFICACION', $data['COD_AVISONOTIFICACION']);
        $this -> db -> set('COD_TIPONOTIFICACION', $data['COD_TIPONOTIFICACION']);
        $this -> db -> set('FECHA_NOTIFICACION', "to_date('" . $data['FECHA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this -> db -> set('COD_ESTADO', $data['COD_ESTADO']);
        $this -> db -> set('OBSERVACIONES', $data['OBSERVACIONES']);
        $this -> db -> set('COD_PROCESO_COACTIVO', $data['COD_PROCESO_COACTIVO']);
        $this -> db -> set('COD_MEDIDAPRELACION', $data['COD_MEDIDAPRELACION']);
        $this -> db -> set('NOMBRE_DOC_CARGADO', $data['NOMBRE_DOC_CARGADO']);
        $this -> db -> set('EXCEPCION', $data['EXCEPCION']);
        $this -> db -> set('PLANTILLA', $data['PLANTILLA']);
        $this -> db -> set('RECURSO', $data['RECURSO']);
        $this -> db -> set('ESTADO_NOTIFICACION', $data['ESTADO_NOTIFICACION']);
        $this -> db -> insert('AVISONOTIFICACION');
       // print_r($this->db->last_query());//die();
        if ($this -> db -> affected_rows() > 0) {
           $this -> db -> trans_complete();
           //  print_r($this->db->affected_rows());die();
            return TRUE; 
        }

        return FALSE;
    }
     function add_avisos2($data) {
      //  $this -> db -> trans_start();
        $this -> db -> set('COD_AVISONOTIFICACION', $data['COD_AVISONOTIFICACION']);
        $this -> db -> set('COD_TIPONOTIFICACION', $data['COD_TIPONOTIFICACION']);
        $this -> db -> set('FECHA_NOTIFICACION', "to_date('" . $data['FECHA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this -> db -> set('COD_ESTADO', $data['COD_ESTADO']);
        $this -> db -> set('OBSERVACIONES', $data['OBSERVACIONES']);
        $this -> db -> set('COD_PROCESO_COACTIVO', $data['COD_PROCESO_COACTIVO']);
        $this -> db -> set('COD_MEDIDAPRELACION', $data['COD_MEDIDAPRELACION']);
        $this -> db -> set('NOMBRE_DOC_CARGADO', $data['NOMBRE_DOC_CARGADO']);
        $this -> db -> set('EXCEPCION', $data['EXCEPCION']);
        $this -> db -> set('PLANTILLA', $data['PLANTILLA']);
        $this -> db -> set('RECURSO', $data['RECURSO']);
        $this -> db -> set('ESTADO_NOTIFICACION', $data['ESTADO_NOTIFICACION']);
        $this -> db -> insert('AVISONOTIFICACION');
       // print_r($this->db->last_query());//die();
        if ($this -> db -> affected_rows() > 0) {
         //  $this -> db -> trans_complete();
            // print_r("hola");die();
            return TRUE; 
        }

        return FALSE;
    }

    function edit_aviso($data, $id) {
        $this -> db -> trans_start();
        $this -> db -> where('COD_AVISONOTIFICACION', $id);
        $this -> db -> set('COD_TIPONOTIFICACION', $data['COD_TIPONOTIFICACION']);
        $this -> db -> set('NUM_RADICADO_ONBASE', $data['NUM_RADICADO_ONBASE']);
        if ($data['FECHA_ONBASE'] == ''){
            $date = date('d/m/Y');
            $this -> db -> set('FECHA_ONBASE', "to_date('" . $date . "','dd/mm/yyyy')", false);
        } else {
            $this -> db -> set('FECHA_ONBASE', "to_date('" . $data['FECHA_ONBASE'] . "','dd/mm/yyyy')", false);
        }
        $this -> db -> set('FECHA_MODIFICA_NOTIFICACION', "to_date('" . $data['FECHA_MODIFICA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this -> db -> set('COD_ESTADO', $data['COD_ESTADO']);
        $this -> db -> set('OBSERVACIONES', $data['OBSERVACIONES']);
        $this -> db -> set('DOC_COLILLA', $data['DOC_COLILLA']);
        $this -> db -> set('DOC_FIRMADO', $data['DOC_FIRMADO']);
        $this -> db -> set('NOMBRE_DOC_CARGADO', $data['NOMBRE_DOC_CARGADO']);
        $this -> db -> set('NOMBRE_COL_CARGADO', $data['NOMBRE_COL_CARGADO']);
        $this -> db -> set('COD_MOTIVODEVOLUCION', $data['COD_MOTIVODEVOLUCION']);
        $this -> db -> set('EXCEPCION', $data['EXCEPCION']);
        $this -> db -> set('PLANTILLA', $data['PLANTILLA']);
        $this -> db -> set('RECURSO', $data['RECURSO']);
        $this -> db -> set('COD_COMUNICACION', $data['COD_COMUNICACION']);
        $this -> db -> set('ESTADO_NOTIFICACION', $data['ESTADO_NOTIFICACION']);
        $this -> db -> update('AVISONOTIFICACION');
        //print_r($this->db->last_query());die();
        if ($this -> db -> affected_rows() >= 0) {
            $this -> db -> trans_complete();
            return TRUE;
        }
        return FALSE;
    }
function edit_avisos2($data, $id) {
        
        $this -> db -> where('COD_AVISONOTIFICACION', $id);
        $this -> db -> set('COD_TIPONOTIFICACION', $data['COD_TIPONOTIFICACION']);
        $this -> db -> set('NUM_RADICADO_ONBASE', $data['NUM_RADICADO_ONBASE']);
        if ($data['FECHA_ONBASE'] == ''){
            $date = date('d/m/Y');
            $this -> db -> set('FECHA_ONBASE', "to_date('" . $date . "','dd/mm/yyyy')", false);
        } else {
            $this -> db -> set('FECHA_ONBASE', "to_date('" . $data['FECHA_ONBASE'] . "','dd/mm/yyyy')", false);
        }
        $this -> db -> set('FECHA_MODIFICA_NOTIFICACION', "to_date('" . $data['FECHA_MODIFICA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this -> db -> set('COD_ESTADO', $data['COD_ESTADO']);
        $this -> db -> set('OBSERVACIONES', $data['OBSERVACIONES']);
        $this -> db -> set('DOC_COLILLA', $data['DOC_COLILLA']);
        $this -> db -> set('DOC_FIRMADO', $data['DOC_FIRMADO']);
        $this -> db -> set('NOMBRE_DOC_CARGADO', $data['NOMBRE_DOC_CARGADO']);
        $this -> db -> set('NOMBRE_COL_CARGADO', $data['NOMBRE_COL_CARGADO']);
        $this -> db -> set('COD_MOTIVODEVOLUCION', $data['COD_MOTIVODEVOLUCION']);
        $this -> db -> set('EXCEPCION', $data['EXCEPCION']);
        $this -> db -> set('PLANTILLA', $data['PLANTILLA']);
        $this -> db -> set('RECURSO', $data['RECURSO']);
        $this -> db -> set('COD_COMUNICACION', $data['COD_COMUNICACION']);
        $this -> db -> set('ESTADO_NOTIFICACION', $data['ESTADO_NOTIFICACION']);
        $this -> db -> update('AVISONOTIFICACION');
        //print_r($this->db->last_query());die();
        if ($this -> db -> affected_rows() >= 0) {
          
            return TRUE;
        }
        return FALSE;
    }

    function actualizacion_mcinvestigacion($datos) {
        if (!empty($datos)) {
            $this->db->where("COD_MEDIDAPRELACION", $datos['COD_MEDIDAPRELACION']);
            unset($datos['COD_MEDIDAPRELACION']);
            $consul = $this->db->update("MC_MEDIDASPRELACION", $datos);
        //echo $this->db->last_query($consul);die;
            if ($this->db->affected_rows() >= 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
     }

    function actualizacion_mcinvestigaciones2($datos) {
        if (!empty($datos)) {
            $this->db->where("COD_MEDIDAPRELACION", $datos['COD_MEDIDAPRELACION']);
            unset($datos['COD_MEDIDAPRELACION']);
            $consul = $this->db->update("MC_MEDIDASPRELACION", $datos);

        //echo $this->db->last_query($consul);die;
            if ($this->db->affected_rows() >= 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        


     }
     function getNotifica2($id, $tipo, $excep, $rec, $estado, $cod_prelacion) {
        $array = array();
        $this -> db -> select('COD_AVISONOTIFICACION,OBSERVACIONES,PLANTILLA,COD_ESTADO,DOC_COLILLA,NUM_RADICADO_ONBASE,FECHA_ONBASE,COD_MOTIVODEVOLUCION,COD_COMUNICACION,FECHA_NOTIFICACION,COD_MANDAMIENTOPAGO,NOMBRE_DOC_CARGADO,ESTADO_NOTIFICACION,DOC_FIRMADO,RECURSO,EXCEPCION');
        $this -> db -> from("AVISONOTIFICACION");
        $this -> db -> where('COD_PROCESO_COACTIVO', $id);
        $this -> db -> where('COD_TIPONOTIFICACION', $tipo);
        $this -> db -> where('ESTADO_NOTIFICACION', $estado);
        $this->db->where('COD_MEDIDAPRELACION', $cod_prelacion);
        $this -> db -> where('EXCEPCION', $excep);
        $this -> db -> where('RECURSO', $rec);
        $this -> db -> order_by('COD_AVISONOTIFICACION', 'DESC');
        $query = $this -> db -> get();
       //print_r($this->db->last_query($query));//die();
        if ($query -> num_rows() > 0) {
            $array = $query -> result();
            return $array[0];
        }
    }
    function getSelect($table, $fields, $where = '', $order = '') {
        $sql = "SELECT " . $fields . "  FROM " . $table . " ";
        if ($where != '')
            $sql .= "WHERE " . $where . " ";
        if ($order != '')
            $sql .= "ORDER BY " . $order . " ";
        $query = $this->db->query($sql);
        //print_r($this->db->last_query($query));die();
        return $query->result();
    }
    function getTitulos($cod_coactivo) {

        $this->db->select("VW.SALDO_DEUDA, VW.SALDO_CAPITAL, VW.SALDO_INTERES,VW.COD_EXPEDIENTE_JURIDICA, VW.CONCEPTO");
        $this->db->from('PROCESOS_COACTIVOS PC');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO AND VW.COD_RESPUESTA=PC.COD_RESPUESTA');
        $this->db->join('ACUMULACION_COACTIVA AC', 'PC.COD_PROCESO_COACTIVO=AC.COD_PROCESO_COACTIVO');
        $this->db->where('PC.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->where('AC.COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $query = $query->result_array();
            return $query;
        }
    }

    function datos_gestion($cod_respuesta) {
        $this->db->select("RG.COD_TIPOGESTION, RG.COD_RESPUESTA, RG.NOMBRE_GESTION");
        $this->db->where("RG.COD_RESPUESTA", $cod_respuesta);
        $dato = $this->db->get("RESPUESTAGESTION RG");
        if ($dato->num_rows() > 0) {
            $dato = $dato->result_array();
            return $dato[0];
        }
    }

    function getSequence($table, $name){
        $query = $this -> db -> query("SELECT " . $name . "  FROM " . $table . " ");
        $row = $query -> row_array();
        return @$row['NEXTVAL'] - 1;
    }
    function generar($post) {
        $respuesta=$post['post']['cod_siguiente_prelacion'];
        $this->db->set("COD_TIPOGESTION",  $post['post']['cod_siguiente_prelacion']);
        $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        $this->db->update('MC_MEDIDASPRELACION');
        if($respuesta==1009){
            $id_traza = trazarProcesoJuridico('150', '1009', '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        }
        else{
            $id_traza = trazarProcesoJuridico('150', '999', '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        }
        
    }

    function generar_web($post) {
        $this->db->set("COD_TIPOGESTION",  $post['post']['cod_siguiente_prelacion']);
        $this->db->where("COD_MEDIDAPRELACION", $post['post']['id_prelacion']);
        $this->db->update('MC_MEDIDASPRELACION');
        $respuesta = $post['post']['cod_siguiente_prelacion'];
        if($respuesta==1730){
            $id_traza = trazarProcesoJuridico('763', '1730', '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
        }elseif($respuesta==1725){
            $id_traza = trazarProcesoJuridico('763', '1725', '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
            }elseif($respuesta==1735){
                    $id_traza = trazarProcesoJuridico('763', '1735', '', $post['post']['cod_fis'], '', '', $comentarios = "", ID_USER);
            }else{
                echo "error de base de respuesta";
        }   
    }

    function get_prelacion($cod_prelacion) {
        $this->db->select("COD_CONCURRENCIA");
        $this->db->from('MC_MEDIDASPRELACION');
        $this->db->where('COD_MEDIDAPRELACION', $cod_prelacion);
        $dato = $this->db->get();
        return $dato;
    }
    
//CONTENEDOR FUNCIONES SPRINT 4 Y 5
//FUNCION PARA OBTENER EL TIPO DEMANDA NULIDAD DE UN PROCESO COACTIVO
  function obtenerDemanda($codCoa)
  {      
      $this->db->select('DEM_NULIDAD');
      $this->db->from('PROCESOS_COACTIVOS');
      $this->db->where('COD_PROCESO_COACTIVO', $codCoa);      
      $query = $this->db->get();   
      $tipo_dem = $query -> result_array();
        if(!empty($tipo_dem))        {
            return $tipo_dem[0]['DEM_NULIDAD'];
        }
        else{
            return $tipo_dem = 0;
        } 
  }
  
//FUNCION PARA VERIFICAR QUE LA DILIGENCIA DE APLICACION DE TITULOS SE ENCUENTRA SUSPENDIDA
    function verificarSuspAptitulos($cod_coactivo, $cod_cautelar = null) {
        $this->db->select('COD_MEDIDACAUTELAR');
        $this->db->from('MC_MEDIDASCAUTELARES');
        if (!empty($cod_cautelar)) {
            $this->db->where('COD_MEDIDACAUTELAR', $cod_cautelar);
        }
        $this->db->where('COD_RESPUESTAGESTION', 1771);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }  
    
//FUNCION PARA OBTENER EL ESTADO DE LA REVOCATORIA DIRECTA
    public function estadoRevocatoria($codCoa) {
        $this->db->select('REVOCATORIA_DIRECTA');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $revocatoria = $query->result_array();
        if (!empty($revocatoria)) {
            return $revocatoria[0]['REVOCATORIA_DIRECTA'];
        } else {
            return $revocatoria = 0;
        }
    }
    
//FUNCION PARA VERIFICAR QUE LA SUSPENSION EN APLICACION DE TITULOS SE ENCUENTRA VIGENTE
    public function verfSuspensionApTitulos($codCoa) {
        $this->db->select('SUSPENSION_APTITULOS');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $suspendido = $query->result_array();
        if (!empty($suspendido)) {
            return $suspendido[0]['SUSPENSION_APTITULOS'];
        } else {
            return $suspendido = 0;
        }
    }    
//FIN CONTENEDOR FUNCIONES SPRINT 4 Y 5

    function delete_oficiosgenerado($cod_m, $tipodoc) {
        $this->db->where('COD_MEDIDACAUTELAR', $cod_m);
        $this->db->where('TIPO_DOCUMENTO', $tipodoc);
        $this->db->delete('MC_OFICIOS_GENERADOS');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        }

        return FALSE;
    }
}

?>
