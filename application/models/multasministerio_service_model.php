

<?php

class Multasministerio_service_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    /* public function aplicar_cadenas($data)
      {
      //print_r( $data);exit;

      if (!empty($data)) {
      $this->db->select("COD_REGIONAL");
      $this->db->from('REGIONAL');
      $this -> db -> where('COD_REGIONAL',$data['ID_TERRITORIAL']);
      $dato = $this->db->get();
      $resultado = $dato -> result_array;
      @$datos=$resultado[0]['COD_REGIONAL'];
      if (!empty($datos)) {



      $this -> db -> set("ID_TERRITORIAL", $data['ID_TERRITORIAL']);
      $this -> db -> set("NOMBRE_TERRITORIAL", $data['NOMBRE_TERRITORIAL']);
      $this -> db -> set("COD_DEPARTAMENTO", $datos);
      $query = $this -> db -> insert('MM_TERRITORIAL');
      print_r($this->db->last_query($query));//die();
      }
      if ($this -> db -> affected_rows() == '1')
      {
      // $this -> db -> trans_complete();
      return TRUE;
      }
      return FALSE;


      }
      } */

    function ultimoinsertterritorial() {
        $id = $this->db->query("

                  SELECT MAX (COD_TERRITORIAL) AS CODIGO FROM MM_TERRITORIAL
                  ");
        return $id->result_array();
    }

    function ultimoinsertunidad() {
        $id = $this->db->query("

                  SELECT MAX (COD_GRUPO) AS CODIGO FROM MM_GRUPO_UNIDAD_ORIGEN
                  ");
        return $id->result_array();
    }

    function regional($codigo) {
        //print_r($codigo);//die();
        $query = "SELECT CEDULA_COORDINADOR_RELACIONES AS CORDINADO  FROM REGIONAL
 WHERE COD_REGIONAL=" . $codigo . "";
        $resultado = $this->db->query($query);
        $resultado = $resultado->result_array;
        //print_r($resultado);die();
        return $resultado;
    }

    function cons_devolcion($id, $idmulta) {
        $datos = $this->db->query("
                   select distinct MM.*,
            (SELECT COUNT(GESTIONCOBRO.COD_TIPO_RESPUESTA) CODIGO_RESPUESTA_LIQUIDACION FROM GESTIONCOBRO
            WHERE COD_FISCALIZACION_EMPRESA=RES.COD_FISCALIZACION AND GESTIONCOBRO.COD_TIPO_RESPUESTA='3002')  CODIGO_RESPUESTA_LIQUIDACION,
                EM.NOMBRE_EMPRESA,RES.COD_RESOLUCION,F.COD_FISCALIZACION,RES.DOCUMENTO_COBRO_COACTIVO,RES.COD_FISCALIZACION AS ESSS,
                ( us.nombres
                     || ' '
                || us.apellidos )as RESPONSABLES
from MULTASMINISTERIO MM
 inner join EMPRESA EM ON EM.CODEMPRESA = MM.NIT_EMPRESA
       INNER join RESOLUCION RES ON RES.NUMERO_RESOLUCION = MM.NRO_RESOLUCION
       INNER join FISCALIZACION F ON F.COD_FISCALIZACION = RES.COD_FISCALIZACION
        JOIN USUARIOS us ON us.idusuario = MM.responsable
       WHERE
          MM.responsable = MM.RESPONSABLE
           AND MM.ID =" . $id . "
        AND MM.COD_MULTAMINISTERIO =" . $idmulta . "
                  ");
        // echo $this->db->last_query();die();
        return $datos->result_array();
    }

    function addMultaAsignacion($dataAsig) {
        $this->db->set('FECHA_ASIGNACION', 'SYSDATE', FALSE);
        $this->db->set('COMENTARIOS_ASIGNACION', 'Asignacion Multa ministerio');
        $this->db->insert('ASIGNACIONFISCALIZACION', $dataAsig);
        //echo $this->db->last_query();die();
        $this->db->select('COD_ASIGNACIONFISCALIZACION,FECHA_ASIGNACION');
        $this->db->from('ASIGNACIONFISCALIZACION');
        $this->db->where($dataAsig);
        $this->db->order_by('FECHA_ASIGNACION', 'DESC');
        $query = $this->db->get();
        return $query->row();
    }

    function addMultaFiscalizacion($dataFisc) {
        $this->db->set('PERIODO_INICIAL', 'SYSDATE', FALSE);
        $this->db->set('PERIODO_FINAL', 'SYSDATE', FALSE);
        $this->db->insert('FISCALIZACION', $dataFisc);
        //print_r($this->db->last_query());die();
        $this->db->select("COD_FISCALIZACION ||' || '|| NRO_EXPEDIENTE AS COD_FISCALIZACION");
        $this->db->from('FISCALIZACION');
        $this->db->where($dataFisc);
        $query = $this->db->get();
         //print_r($this->db->last_query($query));die();
        return $query->row();
    }

    function addMultaResolucion($dataRes) {
        //  $this -> db -> trans_start();
        $this->db->set('NUMERO_RESOLUCION', $dataRes['NUMERO_RESOLUCION']);
        $this->db->set('COD_REGIONAL', $dataRes['COD_REGIONAL']);
        $this->db->set('NOMBRE_EMPLEADOR', $dataRes['NOMBRE_EMPLEADOR']);
        $this->db->set('NITEMPRESA', $dataRes['NITEMPRESA']);
        $this->db->set('ELABORO', $dataRes['ELABORO']);
        $this->db->set('COD_FISCALIZACION', $dataRes['COD_FISCALIZACION']);
        $this->db->set('VALOR_TOTAL', $dataRes['VALOR_TOTAL']);
        $this->db->set('COD_CPTO_FISCALIZACION', $dataRes['COD_CPTO_FISCALIZACION']);
        $this->db->set('FECHA_CREACION', "TO_DATE('" . $dataRes['FECHA_CREACION'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('FECHA_CONSTANCIA_EJECUTORIA', "TO_DATE('" . $dataRes['FECHA_CONSTANCIA_EJECUTORIA'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('NRO_RESOLUCION_RE_REP', $dataRes['NRO_RESOLUCION_RE_REP']);
        $this->db->set('FECHA_RESOLUCION_RE_REP', "TO_DATE('" . $dataRes['FECHA_RESOLUCION_RE_REP'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('NRO_RESOLUCION_RE_APE', $dataRes['NRO_RESOLUCION_RE_APE']);
        $this->db->set('FECHA_RESOLUCION_RE_APE', "TO_DATE('" . $dataRes['FECHA_RESOLUCION_RE_APE'] . "', 'dd-mm-yyyy')", FALSE);
        // $this -> db -> set('COD_REGIONAL',$dataRes['COD_REGIONAL']);

        $this->db->insert('RESOLUCION');
        //print_r($this->db->last_query());die();
        if ($this->db->affected_rows() == '1') {


            // $this -> db -> trans_complete();
            return TRUE;
        }
        return FALSE;
    }

    function addMultaResolucion_update($dataRes, $nit, $codigo) {

//////////////////////////////////////////////////////////////////////
        $this->db->select("NRO_RESOLUCION");
        $this->db->from('MULTASMINISTERIO');
        $this->db->where('COD_MULTAMINISTERIO', $codigo);
        $datoid = $this->db->get();
        $resultadoS = $datoid->result_array;
        $arraydatos = $resultadoS[0]['NRO_RESOLUCION'];
        //  $this -> db -> trans_start();
        $this->db->set('NUMERO_RESOLUCION', $dataRes['NUMERO_RESOLUCION']);
        $this->db->set('COD_REGIONAL', $dataRes['COD_REGIONAL']);
        $this->db->set('NOMBRE_EMPLEADOR', $dataRes['NOMBRE_EMPLEADOR']);
        $this->db->set('NITEMPRESA', $nit);
        $this->db->set('VALOR_TOTAL', $dataRes['VALOR_TOTAL']);
        $this->db->set('FECHA_CREACION', "TO_DATE('" . $dataRes['FECHA_CREACION'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('FECHA_CONSTANCIA_EJECUTORIA', "TO_DATE('" . $dataRes['FECHA_CONSTANCIA_EJECUTORIA'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('NRO_RESOLUCION_RE_REP', $dataRes['NRO_RESOLUCION_RE_REP']);
        $this->db->set('FECHA_RESOLUCION_RE_REP', "TO_DATE('" . $dataRes['FECHA_RESOLUCION_RE_REP'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('NRO_RESOLUCION_RE_APE', $dataRes['NRO_RESOLUCION_RE_APE']);
        $this->db->set('FECHA_RESOLUCION_RE_APE', "TO_DATE('" . $dataRes['FECHA_RESOLUCION_RE_APE'] . "', 'dd-mm-yyyy')", FALSE);
        // $this -> db -> set('COD_REGIONAL',$dataRes['COD_REGIONAL']);
        $this->db->where('NUMERO_RESOLUCION"', $arraydatos);
        $this->db->update('RESOLUCION');

        //  print_r($this->db->last_query());
        if ($this->db->affected_rows() == '1') {


            // $this -> db -> trans_complete();
            return TRUE;
        }
        return FALSE;
    }

    function cons_proceso($id) {
        $this->db->select("FECHA_ENVIO,ID");
        $this->db->where("ID", $id);
        $dato = $this->db->get("MM_TIPO_PROCESO");
        $dato = $dato->row();
        // print_r($this->db->last_query());
        return (!empty($dato)) ? $dato : "";
    }

    function cons_radicado($id) {
        $this->db->select("NUMERO_RADICACION");
        $this->db->where("NUMERO_RADICACION", $id);
        $dato = $this->db->get("MM_TIPO_PROCESO");
        $dato = $dato->row();
        // print_r($this->db->last_query());
        return (!empty($dato)) ? $dato : "";
    }

    function cons_multa($id) {
        $this->db->select("COD_MULTAMINISTERIO");
        $this->db->where("COD_MULTAMINISTERIO", $id);
        $dato = $this->db->get("MULTASMINISTERIO");
        $dato = $dato->row();
        return (!empty($dato)) ? $dato : "";
    }

    function cons_multaincidencias($id) {
        $this->db->select("COD_MULTAMINISTERIO");
        $this->db->where("COD_MULTAMINISTERIO", $id);
        $dato = $this->db->get("MM_CORRESPONDENCIA");
        $dato = $dato->row();
        // print_r($this->db->last_query());
        return (!empty($dato)) ? $dato : "";
    }

    function cons_multacorrespondencias($id) {
        $this->db->select("NMRO_RADICACION_ENTRADA_CORRES");
        $this->db->where("NMRO_RADICACION_ENTRADA_CORRES", $id);
        $dato = $this->db->get("MM_CORRESPONDENCIA");
        $dato = $dato->row();
        //print_r($this->db->last_query());die();
        return (!empty($dato)) ? $dato : "";
    }

    function cons_empresa($id) {
        $this->db->select("CODEMPRESA");
        $this->db->where("CODEMPRESA", $id);
        $dato = $this->db->get("EMPRESA");
        $dato = $dato->row();
        // print_r($this->db->last_query());
        return (!empty($dato)) ? $dato : "";
    }

    function confirmar_resolucion($id) {
        $this->db->select("FECHA_CREACION,NUMERO_RESOLUCION");
        $this->db->where("NUMERO_RESOLUCION", $id);
        $dato = $this->db->get("RESOLUCION");
        $dato = $dato->row();
        return (!empty($dato)) ? $dato : "";
    }

    function getCodigoResolucion($numRes) {
        // print_r($numRes);
        $this->db->select('COD_RESOLUCION');

        $this->db->where('NUMERO_RESOLUCION', $numRes);
        $query = $this->db->get('RESOLUCION');
        //  echo "este es el valor". $this->db->last_query();
        /* die();
         * 
         */
        return $query;
    }

    function getQUERELLANTE($ID) {
        // print_r($numRes);
        $this->db->select('ID_QUERELLANTE');

        $this->db->where('ID', $ID);
        $query = $this->db->get('MM_QUERELLANTE');
        //  echo "es". $this->db->last_query();
        /* die();
         * 
         */

        return $query;
    }

    function getQUERELLADO($ID) {
        // print_r($numRes);
        $this->db->select('ID_QUERELLADO');

        $this->db->where('ID', $ID);
        $query = $this->db->get('MM_QUERELLADO');
        // echo "ester". $this->db->last_query();
        /* die();
         * 
         */
        return $query;
    }

    function fecha_ini($ID) {
        // print_r($numRes);
        $this->db->select('NRO_RESOLUCION');

        $this->db->where('COD_MULTAMINISTERIO', $ID);
        $query = $this->db->get('MULTASMINISTERIO');
        // echo "ester". $this->db->last_query();
        /* die();
         * 
         */
        return $query;
    }

    function fiscalizacion($ID) {
        // print_r($numRes);
        $this->db->select('COD_FISCALIZACION');

        $this->db->where('NUMERO_RESOLUCION', $ID);
        $query = $this->db->get('RESOLUCION');
        // echo "ester". $this->db->last_query();
        /* die();
         * 
         */
        return $query;
    }

    function liquidacion($ID) {
        // print_r($numRes);
        $this->db->select('FECHA_INICIO,SALDO_DEUDA,TOTAL_LIQUIDADO');

        $this->db->where('COD_FISCALIZACION', $ID);
        $query = $this->db->get('LIQUIDACION');
        // echo "ester". $this->db->last_query();
        /* die();
         * 
         */
        return $query;
    }

    function getmultas2($user) {
        $datoss = $this->db->query("
    SELECT DISTINCT  COD_MULTAMINISTERIO FROM MULTASMINISTERIO,MM_INCIDENCIAS,RECEPCIONTITULOS
          WHERE  INCIDENCIA = 'S'
       AND( RECEPCIONTITULOS.COD_ABOGADO='$user') 
          AND MM_INCIDENCIAS.COD_MULTASMINISTERIO=MULTASMINISTERIO.COD_MULTAMINISTERIO
      
       ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function fechasrecepcion($ID) {
        // print_r($numRes);
        $this->db->select('COD_RECEPCIONTITULO, FECHA_ENTREGA ');

        $this->db->where('COD_FISCALIZACION_EMPRESA', $ID);
        $query = $this->db->get('RECEPCIONTITULOS');
        // echo "ester". $this->db->last_query();die();
        /* die();
         * 
         */
        return $query;
    }

    function fechasprocesoscoactivos($ID) {
        // print_r($numRes);
        $this->db->select('PC.COD_PROCESO_COACTIVO, PC.FECHA_CREACION, PC.FECHA_AVOCA');
        $this->db->join("PROCESOS_COACTIVOS PC", "AC.COD_PROCESO_COACTIVO = PC.COD_PROCESO_COACTIVO");
        $this->db->where('COD_RECEPCIONTITULO', $ID);
        $query = $this->db->get('ACUMULACION_COACTIVA AC');
        //echo "ester" . $this->db->last_query();
        //die();
        /* die();
         * 
         */
        return $query;
    }

    function fechassuspencion($ID) {
        // print_r($numRes);
        $this->db->select(' COD_PROCESO_COACTIVO,SUSPENCION_ACURDO,
        FECHA_SUSPENCION_ACUERDO,SUSPENSION_APTITULOS,SUSPENSION_REMATE,TERMINACION_PROCESO,REANUDACION
        ,FECHA_TERMINACION,REVOCATORIA_DECIDIDA,REVOCATORIA_DIRECTA,DEM_NULIDAD,TERMINACION_ACU_FORMALIZACION');

        $this->db->where('COD_PROCESO_COACTIVO', $ID);
        $query = $this->db->get('PROCESOS_COACTIVOS');
        // echo "ester". $this->db->last_query();die();
        /* die();
         * 
         */
        return $query;
    }

    function fechasavoca($ID) {
        // print_r($numRes);
        $this->db->select('CREACION_FECHA');

        $this->db->where('COD_PROCESO_COACTIVO', $ID);
        $query = $this->db->get('COMUNICADOS_PJ');
        // echo "ester". $this->db->last_query();die();
        /* die();
         * 
         */
        return $query;
    }

    function fechamandamiento($ID) {
        // print_r($numRes);
        $this->db->select('FECHA_MODIFICA_MANDAMIENTO,COD_MANDAMIENTOPAGO,FECHA_SENTENCIA');

        $this->db->where('COD_PROCESO_COACTIVO', $ID);
        $query = $this->db->get('MANDAMIENTOPAGO');
        // echo "ester". $this->db->last_query();die();
        /* die();
         * 
         */
        return $query;
    }

    function codigomandamiento($ID) {
        // print_r($numRes);
        $this->db->select('FECHA_EXCEPCION,PRESENTA_EXCEPCIONES');

        $this->db->where('COD_MANDAMIENTO', $ID);
        $query = $this->db->get('EXCEPCIONESNOTIFICACION');
        // echo "ester". $this->db->last_query();die();
        /* die();
         * 
         */
        return $query;
    }

    function addTraza($table, $data, $date = '') {
        if ($date != '') {
            foreach ($data as $key => $value) {
                $this->db->set($key, $value);
            }
            foreach ($date as $keyf => $valuef) {
                $this->db->set($keyf, "to_date('" . $valuef . "','DD/MM/YYYY hh24:mi')", false);
            }
            $this->db->insert($table);
        } else {
            $this->db->insert($table, $data);
        }//echo "<br>".__FILE__." > ".__LINE__.": ".$this->db->last_query();die();
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        }
        return FALSE;
    }

    function getLastInserted($table, $id) {
        $this->db->select_max($id);
        $Q = $this->db->get($table);
        $row = $Q->row_array();
        return $row[$id];
    }

    function updateGestionActual($table, $gestion, $fiscalizacion) {

        $query = $this->db->query(" UPDATE " . $table . "  SET COD_GESTIONACTUAL='" . $gestion . "' WHERE COD_FISCALIZACION=" . $fiscalizacion . "");
        // echo "<br>".__FILE__." > ".__LINE__.": ".$this->db->last_query();
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

    function noDispararRecordatorios($codfiscalizacion, $tipogestion, $tiporespuesta = '') {

        $sqlres = "";
        if (!empty($tiporespuesta)) {
            //$sqlres = " AND COD_TIPO_RESPUESTA <> $tiporespuesta ";
        }
        $sql = "
        UPDATE DISPARARECORDATORIO SET ACTIVO = 'N' WHERE CODDISPARARECORDATORIO IN(
            SELECT CODDISPARARECORDATORIO FROM DISPARARECORDATORIO WHERE ACTIVO = 'S' AND
            COD_GESTION_COBRO IN(
                SELECT COD_GESTION_COBRO FROM GESTIONCOBRO WHERE COD_FISCALIZACION_EMPRESA = $codfiscalizacion AND cod_tipogestion<> $tipogestion $sqlres
            )
        )";
        $dato = $this->db->query($sql);
        return true;
    }

    function siguienteGestion($tipogestion, $tiporespuesta = '') {

        $this->db->select('GESTIONDESTINO');
        $this->db->select('RTADESTINO');
        $this->db->where("GESTIONORIGEN", $tipogestion);
        if (!empty($tiporespuesta)) {
            $this->db->where("TIPORESPUESTA", $tiporespuesta);
        }
        $query = $this->db->get('FLUJO');  //echo "<br>".__FILE__." > ".__LINE__.": ".$this->db->last_query();
        $res = $query->row_array();
        if (!empty($res)) {
            return $res; //['GESTIONDESTINO'];
        }
    }

    function addMulta($data, $codGestion, $querellante, $querellado) {

        $this->db->set('FECHA_CREACION', 'SYSDATE', FALSE);
        $this->db->set('FECHA_GESTION', 'SYSDATE', FALSE);
        $this->db->set('COD_GESTION_COBRO', $codGestion);
        $this->db->set('FECHA_RADICACION_SALIDA', "TO_DATE('" . $data['FECHA_RADICACION_SALIDA'] . "', 'dd-mm-yyyy')", FALSE);

        $this->db->set('COD_MULTAMINISTERIO', $data['COD_MULTAMINISTERIO']);
        $this->db->set('VALOR', $data['VALOR']);
        $this->db->set('RESPONSABLE', $data['RESPONSABLE']);
        $this->db->set('EXIGIBILIDAD_TITULO', $data['EXIGIBILIDAD_TITULO']);
        $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $data['FECHA_EJECUTORIA'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('PERIODO_INICIAL', "TO_DATE('" . $data['PERIODO_INICIAL'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('COD_CONCEPTO', $data['COD_CONCEPTO']);
        $this->db->set('CONDUCTA_SANCIONADA', $data['CONDUCTA_SANCIONADA']);
        $this->db->set('NRO_RADICACION_SALIDA_CORRESP', $data['NRO_RADICACION_SALIDA_CORRESP']);
        $this->db->set('REGIONAL_SENA_DESTINO', $data['REGIONAL_SENA_DESTINO']);
        $this->db->set('ID', $data['ID']);
        $this->db->set('NIT_EMPRESA', $data['NIT_EMPRESA']);
        $this->db->set('NRO_RADICADO', $data['NRO_RADICADO']);
        $this->db->set('NRO_RESOLUCION', $data['NRO_RESOLUCION']);
        $this->db->set('REGIONAL', $data['REGIONAL_SENA_DESTINO']);
        $this->db->set('ID_QUERELLANTE', $querellante);
        $this->db->set('ID_QUERELLADO', $querellado);
        $this->db->insert('MULTASMINISTERIO');
        $this->db->select('COD_MULTAMINISTERIO');
        $this->db->from('MULTASMINISTERIO');
        $this->db->where('NIT_EMPRESA', $data['NIT_EMPRESA']);
        $this->db->where('NRO_RADICADO', $data['NRO_RADICADO']);
        $this->db->where('NRO_RESOLUCION', $data['NRO_RESOLUCION']);


        $this->db->where('RESPONSABLE', $data['RESPONSABLE']);
        $this->db->order_by('FECHA_CREACION', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
        //return $query->row();
    }

    function addMulta_update($data, $nit, $codigo) {
        /////////////////////////////////////////////////////



        $this->db->set('FECHA_RADICACION_SALIDA', "TO_DATE('" . $data['FECHA_RADICACION_SALIDA'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('VALOR', $data['VALOR']);
        $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $data['FECHA_EJECUTORIA'] . "', 'dd-mm-yyyy')", FALSE);
        $this->db->set('CONDUCTA_SANCIONADA', $data['CONDUCTA_SANCIONADA']);
        $this->db->set('NRO_RADICACION_SALIDA_CORRESP', $data['NRO_RADICACION_SALIDA_CORRESP']);
        $this->db->set('REGIONAL_SENA_DESTINO', $data['REGIONAL_SENA_DESTINO']);
        $this->db->set('ID', $data['ID']);
        $this->db->set('NIT_EMPRESA', $nit);
        $this->db->set('NRO_RADICADO', $data['NRO_RADICADO']);
        $this->db->set('NRO_RESOLUCION', $data['NRO_RESOLUCION']);
        $this->db->set('REANUDACION', 'S');
        $this->db->where('COD_MULTAMINISTERIO', $codigo);
        $this->db->update('MULTASMINISTERIO');
        // $this->db->insert('MULTASMINISTERIO');
        //   echo "este es el valor". $this->db->last_query();
        $this->db->select('COD_MULTAMINISTERIO');
        $this->db->from('MULTASMINISTERIO');
        $this->db->where('NIT_EMPRESA', $nit);
        $this->db->where('NRO_RADICADO', $data['NRO_RADICADO']);
        $this->db->where('NRO_RESOLUCION', $data['NRO_RESOLUCION']);



        $query = $this->db->get();
        return $query->result_array();
        //return $query->row();
    }

    function updateAbogadoResolucion($abogado, $estado, $cor, $codGestion) {

        $this->db->set('ABOGADO', $abogado);
        $this->db->set('COD_ESTADO', $estado);
        $this->db->set('COORDINADOR', $cor);

        $this->db->where('COD_RESOLUCION', $codGestion);
        $this->db->update('RESOLUCION');
        if ($this->db->affected_rows() >= 0) {
            //echo 
            return $this->db->last_query();
        }

        return FALSE;
    }
    public function borrar_mal_insertado($ID){
        $dato_borrar['ID'] = $ID;
        $this->db->delete('MM_TIPO_PROCESO',$dato_borrar);
        $this->db->delete('MM_QUERELLANTE',$dato_borrar);
    }

    public function aplicar_proceso($datos) {

        if (!empty($datos)) {

            $this->db->select("COD_REGIONAL");
            $this->db->from('REGIONAL');
            $this->db->where('COD_REGIONAL', $datos['TERRITORIAL']);
            $dato = $this->db->get();
            $resultado = $dato->result_array;
            @$datoss = $resultado[0]['COD_REGIONAL'];
            if (!empty($datoss)) {

                $this->db->set("ID", $datos['ID']);
                $this->db->set("NUMERO_RADICACION", $datos['NUMERO_RADICACION']);

                $this->db->set("FECHA_ENVIO", "TO_DATE('" . $datos['FECHA_ENVIO'] . "', 'dd-mm-yyyy')", FALSE);
                // echo"false 5"; // entra al hacer el insert
                //$this -> db -> set("FECHA_ENVIO", $datos['FECHA_ENVIO']);
                $this->db->set("TERRITORIAL", $datos['TERRITORIAL']);
                $this->db->set("GRUPO_UNIDAD_ORIGEN", $datos['GRUPO_UNIDAD_ORIGEN']);
                $this->db->set("NOMBRE_GRUPO_UNIDAD_ORIGEN", $datos['NOMBRE_GRUPO_UNIDAD_ORIGEN']);
                $this->db->set("NOMBRE_TERRITORIAL", $datos['NOMBRE_TERRITORIAL']);

                $query = $this->db->insert('MM_TIPO_PROCESO');
            }
            // print_r($this->db->last_query($query));die();
            if ($this->db->affected_rows() == '1') {
                // print_r($this->db->last_query($query));//die();
                // $this -> db -> trans_complete();
                return TRUE;
            }
            return FALSE;
        }
    }

    public function aplicar_proceso_update($datos) {

        if (!empty($datos)) {

            // $this -> db -> set("ID", $datos['ID']);
            $this->db->set("NUMERO_RADICACION", $datos['NUMERO_RADICACION']);
            $this->db->set("FECHA_ENVIO", "TO_DATE('" . $datos['FECHA_ENVIO'] . "', 'dd-mm-yyyy')", FALSE);
            // echo"false 5"; // entra al hacer el insert
            //$this -> db -> set("FECHA_ENVIO", $datos['FECHA_ENVIO']);
            //$this->db->set("TERRITORIAL", $datos['TERRITORIAL']);
            $this->db->set("GRUPO_UNIDAD_ORIGEN", $datos['GRUPO_UNIDAD_ORIGEN']);
            $this->db->set("NOMBRE_GRUPO_UNIDAD_ORIGEN", $datos['NOMBRE_GRUPO_UNIDAD_ORIGEN']);
            $this->db->set("NOMBRE_TERRITORIAL", $datos['NOMBRE_TERRITORIAL']);
            $this->db->where("ID", $datos['ID']);
            $query = $this->db->update('MM_TIPO_PROCESO');
        }
        // print_r($this->db->last_query($query));die();
        if ($this->db->affected_rows() == '1') {
            // print_r($this->db->last_query($query));//die();
            // $this -> db -> trans_complete();
            return TRUE;
        }
        return FALSE;
    }

    public function aplicar_incidencia($datos) {

        if (!empty($datos)) {


            $this->db->set("FEHA_REGISTRO", "TO_DATE('" . $datos['FEHA_REGISTRO'] . "', 'dd-mm-yyyy')", FALSE);
            // echo"false 5"; // entra al hacer el insert
            //$this -> db -> set("FECHA_ENVIO", $datos['FECHA_ENVIO']);
            $this->db->set("TIPO_INCIDENCIA", $datos['TIPO_INCIDENCIA']);
            $this->db->set("NUMERO_RADI_SALIDA_CORRES", $datos['NUMERO_RADI_SALIDA_CORRES']);

            $this->db->set("COD_MULTASMINISTERIO", $datos['COD_MULTASMINISTERIO']);
            $query = $this->db->insert('MM_INCIDENCIAS');
            // print_r($this->db->last_query($query));//die();
            if ($query == TRUE) {
                $this->db->set('INCIDENCIA', 'S');

                // $this->db->set('DEVOLUCION', '5');
                $this->db->where("COD_MULTAMINISTERIO", $datos['COD_MULTASMINISTERIO']);
                $this->db->update('MULTASMINISTERIO');
            }

            // print_r($this->db->last_query($query));//die();
            if ($this->db->affected_rows() == '1') {
                // print_r($this->db->last_query($query));//die();
                // $this -> db -> trans_complete();
                return TRUE;
            }
            return FALSE;
        }
    }

    public function aplicar_incidencia_update($datos) {

        if (!empty($datos)) {


            $this->db->set("FEHA_REGISTRO", "TO_DATE('" . $datos['FEHA_REGISTRO'] . "', 'dd-mm-yyyy')", FALSE);
            // echo"false 5"; // entra al hacer el insert
            //$this -> db -> set("FECHA_ENVIO", $datos['FECHA_ENVIO']);
            $this->db->set("TIPO_INCIDENCIA", $datos['TIPO_INCIDENCIA']);
            $this->db->set("NUMERO_RADI_SALIDA_CORRES", $datos['NUMERO_RADI_SALIDA_CORRES']);
            $this->db->where("COD_MULTASMINISTERIO", $datos['COD_MULTASMINISTERIO']);

            $this->db->update('MM_INCIDENCIAS');

            // print_r($this->db->last_query($query));//die();
            if ($this->db->affected_rows() == '1') {
                // print_r($this->db->last_query($query));//die();
                // $this -> db -> trans_complete();
                return TRUE;
            }
            return FALSE;
        }
    }

    public function aplicar_multa($datos) {

        if (!empty($datos)) {
            $this->db->set("ID", $datos['ID']);
            $this->db->set("NMRO_RADICACION_ENTRADA_CORRES", $datos['NMRO_RADICACION_ENTRADA_CORRES']);
            $this->db->set("FECHA_RADICACION", "TO_DATE('" . $datos['FECHA_RADICACION'] . "', 'dd-mm-yyyy')", FALSE);
            $this->db->set("TIPO_COMFIRMACION", $datos['TIPO_COMFIRMACION']);
            $this->db->set("COD_MULTAMINISTERIO", $datos['COD_MULTAMINISTERIO']);

            $query = $this->db->insert('MM_CORRESPONDENCIA');
            if ($query == TRUE) {


                $this->db->select("COD_CORRESPONDENCIA");
                $this->db->from('MM_CORRESPONDENCIA');
                $this->db->where("COD_MULTAMINISTERIO", $datos['COD_MULTAMINISTERIO']);
                $dato = $this->db->get();
                $resultado = $dato->result_array;
                @$datoss = $resultado[0]['COD_CORRESPONDENCIA'];
            }
            if (!empty($datoss)) {
                $this->db->set('NMRO_RADICACION_ENTRADA_CORRES', $datoss);
                $this->db->set('REANUDACION', 'N');
                // $this->db->set('DEVOLUCION', '5');
                $this->db->where("COD_MULTAMINISTERIO", $datos['COD_MULTAMINISTERIO']);
                $this->db->update('MULTASMINISTERIO');
            }

// print_r($this->db->last_query($query));//die();
            if ($this->db->affected_rows() == '1') {




                // $this -> db -> trans_complete();
                return TRUE;
            }
            return FALSE;
        }
    }

    public function aplicar_multa_update($datos) {

        if (!empty($datos)) {
            $this->db->set("ID", $datos['ID']);
            $this->db->set("NMRO_RADICACION_ENTRADA_CORRES", $datos['NMRO_RADICACION_ENTRADA_CORRES']);
            $this->db->set("FECHA_RADICACION", "TO_DATE('" . $datos['FECHA_RADICACION'] . "', 'dd-mm-yyyy')", FALSE);
            $this->db->set("TIPO_COMFIRMACION", $datos['TIPO_COMFIRMACION']);
            // $this -> db -> set("COD_MULTAMINISTERIO", $datos['COD_MULTAMINISTERIO']);
            $this->db->where("COD_MULTAMINISTERIO", $datos['COD_MULTAMINISTERIO']);
            $query = $this->db->update('MM_CORRESPONDENCIA');
            //   print_r($this->db->last_query($query));die();
            if ($this->db->affected_rows() == '1') {




                // $this -> db -> trans_complete();
                return TRUE;
            }
            return FALSE;
        }
    }

    public function guardar_querrellante($Quer, $id) {

        //valido que que el numero enviado coinsida conlos nuemros
        //// guardados en la tabla MM_TIPOIDENTIFICACION/////
        if (!empty($Quer)) {
            $this->db->select("NUMERO");
            $this->db->from('MM_TIPOIDENTIFICACION');
            $this->db->where('NUMERO', $Quer['TIPOIDENTIFICACION']);
            $dato = $this->db->get();
            $resultado = $dato->result_array;
            $array = $resultado[0]['NUMERO'];

            if ($array && $id == $Quer['ID']) {
                if ($Quer['TIPO_PERSONA'] == 1) {
                    $this->db->set("NOMBRE", $Quer['NOMBRE']);
                    $this->db->set("PRIMER_APELLIDO", $Quer['PRIMER_APELLIDO']);
                    $this->db->set("SEGUNDO_APELLIDO", $Quer['SEGUNDO_APELLIDO']);
                } else {
                    $this->db->set("NOMBRE_RAZON_SOCIAL", $Quer['NOMBRE_RAZON_SOCIAL']);
                }
                $this->db->set("ID", $Quer['ID']);
                $this->db->set("TIPOIDENTIFICACION", $array);
                $this->db->set("NRO_IDENTIFICACION", $Quer['NRO_IDENTIFICACION']);
                $this->db->set("TIPO_PERSONA", $Quer['TIPO_PERSONA']);
                $this->db->set("DIRECCION_PRINCIPAL", $Quer['DIRECCION_PRINCIPAL']);
                $this->db->set("CIUDAD", $Quer['CIUDAD']);
                $this->db->set("DEPARTAMENTO", $Quer['DEPARTAMENTO']);
                $this->db->set("TELEFONOS", $Quer['TELEFONOS']);
                $this->db->set("CORREO_ELECTRONICO", $Quer['CORREO_ELECTRONICO']);

                $query = $this->db->insert('MM_QUERELLANTE');
                //print_r($this->db->last_query($query));die();
                if ($this->db->affected_rows() == '1') {
                    // $this -> db -> trans_complete();
                    return TRUE;
                }
                return FALSE;
            }
        }
    }

    public function guardar_querrellante_update($Quer, $id, $codigo) {

        //valido que que el numero enviado coincida conlos nuemros
        //// guardados en la tabla MM_TIPOIDENTIFICACION/////
        if (!empty($Quer)) {
            $this->db->select("NUMERO");
            $this->db->from('MM_TIPOIDENTIFICACION');
            $this->db->where('NUMERO', $Quer['TIPOIDENTIFICACION']);
            $dato = $this->db->get();
            $resultado = $dato->result_array;
            $array = $resultado[0]['NUMERO'];


            $this->db->select("ID");
            $this->db->from('MULTASMINISTERIO');
            $this->db->where('COD_MULTAMINISTERIO', $codigo);
            $datoS = $this->db->get();
            $resultadoS = $datoS->result_array;
            $array2 = $resultadoS[0]['ID'];

            if ($array && $id == $Quer['ID']) {
                if ($Quer['TIPO_PERSONA'] == 1) {
                    $this->db->set("NOMBRE", $Quer['NOMBRE']);
                    $this->db->set("PRIMER_APELLIDO", $Quer['PRIMER_APELLIDO']);
                    $this->db->set("SEGUNDO_APELLIDO", $Quer['SEGUNDO_APELLIDO']);
                } else {
                    $this->db->set("NOMBRE_RAZON_SOCIAL", $Quer['NOMBRE_RAZON_SOCIAL']);
                }
                $this->db->set("ID", $Quer['ID']);
                $this->db->set("TIPOIDENTIFICACION", $array);
                $this->db->set("NRO_IDENTIFICACION", $Quer['NRO_IDENTIFICACION']);
                $this->db->set("TIPO_PERSONA", $Quer['TIPO_PERSONA']);
                $this->db->set("DIRECCION_PRINCIPAL", $Quer['DIRECCION_PRINCIPAL']);
                $this->db->set("CIUDAD", $Quer['CIUDAD']);
                $this->db->set("DEPARTAMENTO", $Quer['DEPARTAMENTO']);
                $this->db->set("TELEFONOS", $Quer['TELEFONOS']);
                $this->db->set("CORREO_ELECTRONICO", $Quer['CORREO_ELECTRONICO']);
                $this->db->where('ID', $array2);
                $this->db->update('MM_QUERELLANTE');



                // $query = $this -> db -> insert('MM_QUERELLANTE');
                //print_r($this->db->last_query($query));die();
                if ($this->db->affected_rows() >= 1) {
                    // $this -> db -> trans_complete();
                    return TRUE;
                }
                return FALSE;
            }
        }
    }

    public function buscar_regional($datos) {


        $this->db->select("COD_REGIONAL");
        $this->db->from('REGIONAL');
        $this->db->where('COD_REGIONAL', $datos);
        $dato = $this->db->get();
        $resultado = $dato->result_array;
        @$datoss = $resultado[0]['COD_REGIONAL'];
        //  $dato = $dato -> row();
        //  return ( ! empty($dato)) ? $dato : "";

        return $datoss;
    }

    public function guardar_querrellado($Quer, $id) {
         //print_r($Quer);
        //echo " por que "; die();
        //valido que que el numero enviado coinsida conlos nuemros
        //// guardados en la tabla MM_TIPOIDENTIFICACION/////
        if (!empty($Quer)) {
            //echo " por que "; die();
            $this->db->select("NUMERO");
            $this->db->from('MM_TIPOIDENTIFICACION');
            $this->db->where('NUMERO', $Quer['TIPOIDENTIFICACION']);
            $dato = $this->db->get();
            $resultado = $dato->result_array;
            $array = $resultado[0]['NUMERO'];
            if (!empty($resultado)) {
                $this->db->select("IDENTIFICADOR_EMPRESA");
                $this->db->from('MM_TIPOEMPRESA');
                $this->db->where('IDENTIFICADOR_EMPRESA', $Quer['TIPO_EMPRESA']);
                $datoS = $this->db->get();
                $resultadoS = $datoS->result_array;
            }
            $array2 = @$resultadoS[0]['IDENTIFICADOR_EMPRESA'];
            if (!empty($resultadoS)) {
                $this->db->select("CODIGO");
                $this->db->from('MM_SECTOR_TLC');
                $this->db->where('CODIGO', $Quer['SECTOR_TLC']);
                $dat = $this->db->get();
                $result = $dat->result_array;
            }
            $array3 = @$result[0]['CODIGO'];

            if (!empty($array) && !empty($array2) && !empty($array3) && $id == $Quer['ID']) {
                if ($Quer['TIPO_PERSONA'] == 1) {
                    $this->db->set("NOMBRE", $Quer['NOMBRE']);
                    $this->db->set("PRIMER_APELLIDO", $Quer['PRIMER_APELLIDO']);
                    $this->db->set("SEGUNDO_APELLIDO", $Quer['SEGUNDO_APELLIDO']);
                } else {
                    $this->db->set("NOMBRE_RAZON_SOCIAL", $Quer['NOMBRE_RAZON_SOCIAL']);
                }
                $this->db->set("ID", $Quer['ID']);
                $this->db->set("TIPOIDENTIFICACION", $array);
                $this->db->set("NUMERO_IDENTIFICACION", $Quer['NRO_IDENTIFICACION']);
                $this->db->set("TIPO_PERSONA", $Quer['TIPO_PERSONA']);
                $this->db->set("TIPO_EMPRESA", $array2);
                $this->db->set("CIIU_PRINCIPAL", $Quer['CIIU_PRINCIPAL']);
                $this->db->set("SECTOR_TLC", $array3);
                $this->db->set("DIRECCION_PRINCIPAL", $Quer['DIRECCION_PRINCIPAL']);
                $this->db->set("CIUDAD", $Quer['CIUDAD']);
                $this->db->set("DEPARTAMENTO", $Quer['DEPARTAMENTO']);
                $this->db->set("TELEFONOS", $Quer['TELEFONOS']);
                $this->db->set("CORREO_ELECTRONICO", $Quer['CORREO_ELECTRONICO']);

                $query = $this->db->insert('MM_QUERELLADO');
                //print_r($this->db->last_query($query));die();
                if ($this->db->affected_rows() == '1') {
                    // $this -> db -> trans_complete();
                    return TRUE;
                }
                return FALSE;
            }
        }
    }

    public function guardar_querrellado_update($Quer, $id, $codigo) {
        //echo " por que "; //die();

        if (!empty($Quer)) {

            $this->db->select("NUMERO");
            $this->db->from('MM_TIPOIDENTIFICACION');
            $this->db->where('NUMERO', $Quer['TIPOIDENTIFICACION']);
            $dato = $this->db->get();
            $resultado = $dato->result_array;
            $array = $resultado[0]['NUMERO'];
            if (!empty($resultado)) {
                $this->db->select("IDENTIFICADOR_EMPRESA");
                $this->db->from('MM_TIPOEMPRESA');
                $this->db->where('IDENTIFICADOR_EMPRESA', $Quer['TIPO_EMPRESA']);
                $datoS = $this->db->get();
                $resultadoS = $datoS->result_array;
            }
            $array2 = $resultadoS[0]['IDENTIFICADOR_EMPRESA'];
            if (!empty($resultadoS)) {
                $this->db->select("CODIGO");
                $this->db->from('MM_SECTOR_TLC');
                $this->db->where('CODIGO', $Quer['SECTOR_TLC']);
                $dat = $this->db->get();
                $result = $dat->result_array;
            }
            $array3 = $result[0]['CODIGO'];


            $this->db->select("ID");
            $this->db->from('MULTASMINISTERIO');
            $this->db->where('COD_MULTAMINISTERIO', $codigo);
            $datoid = $this->db->get();
            $resultadoS = $datoid->result_array;
            $arraydatos = $resultadoS[0]['ID'];




            if (!empty($array) && !empty($array2) && !empty($array3) && $id == $Quer['ID']) {
                ///  echo " por que ";
                if ($Quer['TIPO_PERSONA'] == 1) {
                    $this->db->set("NOMBRE", $Quer['NOMBRE']);
                    $this->db->set("PRIMER_APELLIDO", $Quer['PRIMER_APELLIDO']);
                    $this->db->set("SEGUNDO_APELLIDO", $Quer['SEGUNDO_APELLIDO']);
                } else {
                    $this->db->set("NOMBRE_RAZON_SOCIAL", $Quer['NOMBRE_RAZON_SOCIAL']);
                }
                $this->db->set("ID", $Quer['ID']);
                $this->db->set("TIPOIDENTIFICACION", $array);
                $this->db->set("NUMERO_IDENTIFICACION", $Quer['NRO_IDENTIFICACION']);
                $this->db->set("TIPO_PERSONA", $Quer['TIPO_PERSONA']);
                $this->db->set("TIPO_EMPRESA", $array2);
                $this->db->set("CIIU_PRINCIPAL", $Quer['CIIU_PRINCIPAL']);
                $this->db->set("SECTOR_TLC", $array3);
                $this->db->set("DIRECCION_PRINCIPAL", $Quer['DIRECCION_PRINCIPAL']);
                $this->db->set("CIUDAD", $Quer['CIUDAD']);
                $this->db->set("DEPARTAMENTO", $Quer['DEPARTAMENTO']);
                $this->db->set("TELEFONOS", $Quer['TELEFONOS']);
                $this->db->set("CORREO_ELECTRONICO", $Quer['CORREO_ELECTRONICO']);
                $this->db->where('ID', $arraydatos);
                $this->db->update('MM_QUERELLADO');
                //$query = $this -> db -> insert('MM_QUERELLADO');
                // print_r($this->db->last_query($query));//die();
                if ($this->db->affected_rows() >= 1) {
                    // $this -> db -> trans_complete();
                    return TRUE;
                }
                return FALSE;
            }
        }
    }

    function getmultas($user) {
        $datoss = $this->db->query("
    SELECT DISTINCT  COD_MULTAMINISTERIO, MM_INCIDENCIAS. *  FROM MULTASMINISTERIO,MM_INCIDENCIAS
          WHERE  INCIDENCIA = 'S'
       AND( RESPONSABLE='$user') 
          AND MM_INCIDENCIAS.COD_MULTASMINISTERIO=MULTASMINISTERIO.COD_MULTAMINISTERIO
        AND MM_INCIDENCIAS.NOTIFICACION  IS NULL
       ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function consultaestados($titulo) {
        $datoss = $this->db->query("

  SELECT RECEPCIONTITULOS.*,PROCESOS_COACTIVOS.*, MC_MEDIDASCAUTELARES.*,MANDAMIENTOPAGO.*  
  FROM  RECEPCIONTITULOS, PROCESOS_COACTIVOS,MC_MEDIDASCAUTELARES,MANDAMIENTOPAGO
  WHERE RECEPCIONTITULOS.COD_RECEPCIONTITULO='$titulo'
  AND PROCESOS_COACTIVOS.COD_TITULOS=RECEPCIONTITULOS.COD_RECEPCIONTITULO
  AND MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO=PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO
  AND  MANDAMIENTOPAGO.COD_PROCESO_COACTIVO=PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO
   
    ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function consuliquidacion_credito($titulo) {
        $datoss = $this->db->query("

  SELECT PROCESOS_COACTIVOS.* 
  FROM   PROCESOS_COACTIVOS
  WHERE PROCESOS_COACTIVOS.COD_TITULOS='$titulo'
    ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function autosjuridicos($titulo) {
        $datoss = $this->db->query("

  SELECT COD_ESTADOAUTO
  FROM   AUTOSJURIDICOS
  WHERE COD_PROCESO_COACTIVO='$titulo'
    ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function aceptacion($codigo) {
        $datoss = $this->db->query("

  SELECT FECHA_ACEPTACION
  FROM   MULTASMINISTERIO
  WHERE COD_MULTAMINISTERIO='$codigo'
    ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function suspencion_insolvencia($codigo) {
        $datoss = $this->db->query("

  SELECT COD_RECEPCION_TITULO,FECHA,COD_ESTADOPROCESO
  FROM   RI_REGIMENINSOLVENCIA
  WHERE COD_RECEPCION_TITULO='$codigo'
    ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function getfiscalizacion($fisca, $user) {
        $datoss = $this->db->query("
     SELECT DISTINCT  NRO_RESOLUCION, RESOLUCION.COD_FISCALIZACION, MM_INCIDENCIAS.*,RECEPCIONTITULOS.COD_ABOGADO
 FROM MULTASMINISTERIO,RESOLUCION,RECEPCIONTITULOS,MM_INCIDENCIAS
            WHERE COD_FISCALIZACION_EMPRESA ='$fisca'
            AND RECEPCIONTITULOS.COD_ABOGADO='$user'
            AND MM_INCIDENCIAS.COD_MULTASMINISTERIO=MULTASMINISTERIO.COD_MULTAMINISTERIO
            AND MULTASMINISTERIO.NRO_RESOLUCION=RESOLUCION.NUMERO_RESOLUCION
            AND RESOLUCION.COD_FISCALIZACION=RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA
           
       
       ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function pagos($user) {
        $datoss = $this->db->query("

        select * from PAGOSRECIBIDOS
       WHERE COD_FISCALIZACION= '$user'

       
       ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function numeroresolucion($fis) {

        $this->db->select('NRO_RESOLUCION');

        $this->db->where('COD_MULTAMINISTERIO', $fis);
        $query = $this->db->get('MULTASMINISTERIO');
        /* echo "este es el valor". $this->db->last_query();
          die();
         * 
         */
        return $query->result_array();
    }

    function numefiscalizacion($fis) {

        $this->db->select('COD_FISCALIZACION');

        $this->db->where('NUMERO_RESOLUCION', $fis);
        $query = $this->db->get('RESOLUCION');
        /* echo "este es el valor". $this->db->last_query();
          die();
         * 
         */
        return $query->result_array();
    }

    function pagos_FECHA($user) {
        $datoss = $this->db->query("

     SELECT MAX(FECHA_PAGO) FROM PAGOSRECIBIDOS
WHERE COD_FISCALIZACION= '$user'

       
       ");
//print_r($this->db->last_query($datoss));die();
        $datoss = $datoss->result_array();
        // print_r($this->db->last_query($datoss));die();
        return $datoss;
    }

    function getmultasproceso($user) {

        $datoss = $this->db->query("
   SELECT * FROM MM_INCIDENCIAS 
          WHERE  COD_MULTASMINISTERIO = '$user'
     ");

        $datoss = $datoss->result_array();

        return $datoss;
    }

}
