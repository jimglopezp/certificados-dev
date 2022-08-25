<?php

class Bandejaunificada_model Extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function ListadoTerminacion($cod_coactivo) {

        $array = array();
        if ($total == false) :
            $this->db->select('AUTOSJURIDICOS.NUM_AUTOGENERADO, AUTOSJURIDICOS.COD_TIPO_PROCESO, USUARIO_CREADOR.IDUSUARIO ID_CREADOR, 
												 USUARIO_CREADOR.NOMBREUSUARIO NOMBRE_CREADOR, USUARIO_ASIGNADO.IDUSUARIO ID_ASIGNADO, 
												 USUARIO_ASIGNADO.NOMBREUSUARIO NOMBRE_ASIGNADO, AUTOSJURIDICOS.FECHA_CREACION_AUTO, TIPOGESTION.TIPOGESTION, 
												 RESPUESTAGESTION.NOMBRE_GESTION, PROCESOS_COACTIVOS.ABOGADO AS COD_ABOGADO, PROCESOS_COACTIVOS.IDENTIFICACION AS NIT_EMPRESA,
												 VW_PROCESOS_COACTIVOS.EJECUTADO AS NOMBRE_EMPRESA, PROCESOS_COACTIVOS.COD_PROCESOPJ, VW_PROCESOS_COACTIVOS.CONCEPTO,VW_PROCESOS_COACTIVOS.NOMBRE_REGIONAL');
        elseif ($total == true) :
            $this->db->select('COUNT(AUTOSJURIDICOS.NUM_AUTOGENERADO) numero');
        endif;
        $this->db->from('AUTOSJURIDICOS');
        $this->db->join('PROCESOS_COACTIVOS', 'AUTOSJURIDICOS.COD_PROCESO_COACTIVO = PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('USUARIOS USUARIO_CREADOR', 'USUARIO_CREADOR.IDUSUARIO = AUTOSJURIDICOS.CREADO_POR', 'inner');
        $this->db->join('USUARIOS USUARIO_ASIGNADO', 'USUARIO_ASIGNADO.IDUSUARIO = AUTOSJURIDICOS.ASIGNADO_A', 'inner');
        $gestion = "GESTIONCOBRO.COD_GESTION_COBRO = AUTOSJURIDICOS.COD_GESTIONCOBRO AND GESTIONCOBRO.COD_TIPO_RESPUESTA != '1138'";
        $this->db->join('GESTIONCOBRO', $gestion, 'inner');
        $this->db->join('TIPOGESTION', 'TIPOGESTION.COD_GESTION = GESTIONCOBRO.COD_TIPOGESTION', 'inner');
        $this->db->join('RESPUESTAGESTION', 'RESPUESTAGESTION.COD_RESPUESTA = GESTIONCOBRO.COD_TIPO_RESPUESTA', 'inner');
        $this->db->join('VW_PROCESOS_COACTIVOS', 'AUTOSJURIDICOS.COD_PROCESO_COACTIVO = VW_PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO ', 'inner');
        $this->db->where('AUTOSJURIDICOS.COD_TIPO_PROCESO', 1);
        $this->db->where('AUTOSJURIDICOS.COD_TIPO_AUTO', 1);



        if ($this->session->userdata['id_secretario'] == $this->idusuario) :
            $this->db->where('AUTOSJURIDICOS.ASIGNADO_A', $this->idusuario);
        elseif ($this->session->userdata['id_coordinador'] == $this->idusuario) :
            $this->db->where('AUTOSJURIDICOS.ASIGNADO_A', $this->idusuario);
        else :
            $this->db->where('PROCESOS_COACTIVOS.ABOGADO', $this->idusuario);
        endif;
        $resultado = $this->db->get('');
        /* $resultado_final = */
        //echo $resultado_final;
//        $resultado = $resultado->result_array();
        return $resultado;
    }

    function titulos_coactivo($cod_coactivo) {
        $this->db->select('VW.NO_EXPEDIENTE');
        $this->db->from('VW_PROCESOS_COACTIVOS VW');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_RESPUESTA=VW.COD_RESPUESTA');
        $this->db->where('VW.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->join('RECEPCIONTITULOS RT', 'RT.COD_RECEPCIONTITULO=VW.NO_EXPEDIENTE');
        //$this->db->where('RT.CERRADO', 0);
        //$where = 'VW.SALDO_DEUDA >0';
        //$this->db->where($where);
        $this->db->group_by('VW.NO_EXPEDIENTE, VW.NUM_LIQUIDACION');
        $resultado = $this->db->get();
        $resultado = $resultado->result_array();
        //echo $this->db->last_query();die();
        return $resultado;
    }

    function consulta_responsable($respuesta) {
        $this->db->select('URLGESTION, IDCARGO');
        $this->db->from('RESPUESTAGESTION');
        $this->db->where('COD_RESPUESTA', $respuesta);
        $datos = $this->db->get();
        $query = $this->db->last_query();
//         echo $query;
        $datos = $datos->result_array();
        return $datos;
    }

    function consulta_secretario($regional) {
        $this->db->select('US.APELLIDOS,US.IDUSUARIO, US.NOMBRES,UG.IDGRUPO');
        $this->db->from('USUARIOS US');
        $this->db->join('USUARIOS_GRUPOS UG', 'UG.IDUSUARIO=US.IDUSUARIO');
        $this->db->where('UG.IDGRUPO', 41);
        $this->db->where('US.COD_REGIONAL', $regional);
        $datos = $this->db->get();
        $datos = $datos->result_array();
        return $datos[0];
    }

    function consulta_coordinador($regional) {
        $this->db->select('US.APELLIDOS,US.IDUSUARIO, US.NOMBRES,UG.IDGRUPO');
        $this->db->from('USUARIOS US');
        $this->db->join('USUARIOS_GRUPOS UG', 'UG.IDUSUARIO=US.IDUSUARIO');
        $this->db->where('UG.IDGRUPO', 42);
        $this->db->where('US.COD_REGIONAL', $regional);
        $datos = $this->db->get();
        $datos = $datos->result_array();
        return $datos[0];
    }

    function abogado($id) {
        $this->db->select('APELLIDOS, NOMBRES');
        $this->db->from('USUARIOS');
        $this->db->where('IDUSUARIO', $id);
        $query = $this->db->last_query();
        //echo $query;
        $abogado = $this->db->get();
        $abogado = $abogado->result_array();
        return $abogado[0];
    }

    function usuarioSubcomite($codCoactivo) {
        $query = "SELECT USU.NOMBRES,USU.IDUSUARIO, USU.APELLIDOS, REM.COD_PROCESO_COACTIVO FROM RESPUESTAGESTION RG
JOIN CARGOS C ON C.IDCARGO = RG.IDCARGO
JOIN USUARIOS USU  ON USU.IDCARGO = C.IDCARGO
JOIN REM_REMISIBILIDAD REM ON REM.COD_TIPORESPUESTA = RG.COD_RESPUESTA
WHERE  REM.COD_PROCESO_COACTIVO = " . $codCoactivo . " ORDER BY APELLIDOS ASC";
        $resultado = $this->db->query($query);
        return $resultado->result_array();
    }

    function consulta_regional($nit) {
        $this->db->select('COD_REGIONAL');
        $this->db->from('EMPRESA');
        $this->db->where('CODEMPRESA', $nit);
        $regional = $this->db->get();
        $regional = $regional->result_array();
        //echo $this->db->last_query();exit;
        return $regional[0];
    }


    function traeerelcodigocoactivo($nit) {
        $this->db->select('COD_FISCALIZACION_EMPRESA');
        $this->db->from('RECEPCIONTITULOS');
        $this->db->where('COD_RECEPCIONTITULO', $nit);
        $recepcion = $this->db->get();
        $recepcion = $recepcion->result_array();
        //echo $this->db->last_query();exit;
        return $recepcion[0];
    }
    function buscar_liquidacion($nit) {
        $this->db->select('NUM_LIQUIDACION');
        $this->db->from('LIQUIDACION');
        $this->db->where('COD_FISCALIZACION', $nit);
        $recepcion = $this->db->get();
        $recepcion = $recepcion->result_array();
        //echo $this->db->last_query();exit;
        return $recepcion[0];
    }

     function verificar_Terminacion($cod) {
        $this->db->select('COD_TIPO_RESPUESTA');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_TIPO_RESPUESTA', 1132);
        $this->db->where('COD_JURIDICO', $cod);
        $respuesta= $this->db->get();
        $respuesta = $respuesta->result_array();
      //  echo $this->db->last_query();exit;
        return $respuesta[0];
    }

    function Procesos($cod_regional, $usuario, $cod_coactivo, $titulo) {

        /* Acercamiento Persuasivo $subQuery2 */
        /* Medidas Cautelares investigacion  $subQuery3 */
        /* Medidas Cautelares Avaluo $subQuery4 */
        /* Mandamoento Pago $subQuery5 */
        /* Terminación de proceso $subQuery6 */
        /* Medidas Cautelares Remate */
        /* Procesos Coactivos  $subQuery7 */
        /* Traslado Judicial $subQuery8 */
        /* Resolución Prescripción  $subQuery9 */
        $regional = ' AND VW.COD_REGIONAL=' . $cod_regional . '';
        if ($this->ion_auth->is_admin()):
            $regional = '';
        endif;
        //  echo "-" . $regional;
        $this->db->select('CP.COD_COBRO_PERSUASIVO AS PROCESO,'
                . 'TO_CHAR(CP.COD_TIPO_RESPUESTA) AS COD_RESPUESTA,'
                . 'VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,'
                . 'PC.ABOGADO AS ABOGADO, '
                . 'PC.COD_PROCESOPJ AS PROCESOPJ,'
                . 'VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, '
                . 'US.NOMBRES, US.APELLIDOS, '
                . 'VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL, '
                . 'VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,'
                . 'VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,'
                . 'VW.COD_EXPEDIENTE_JURIDICA');

        $this->db->from('COBROPERSUASIVO CP');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=CP.COD_PROCESO_COACTIVO');
        /**/
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        /**/
        $where = 'VW.COD_RESPUESTA = CP.COD_TIPO_RESPUESTA AND CP.COD_TIPO_RESPUESTA NOT IN (204,196,193,201,3068) AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        
        $this->db->where($where);
        $query2 = $this->db->get('');
        $subQuery2 = $this->db->last_query();
        $query2 = $query2->result_array();
        
        //MEDIDAS CAUTELARES

        $this->db->select("MC.COD_MEDIDACAUTELAR AS PROCESO,MP.COD_TIPOGESTION || '*?*' || MC.COD_RESPUESTAGESTION AS COD_RESPUESTA, RG.NOMBRE_GESTION  || '*?*' || VW.RESPUESTA AS RESPUESTA, PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL, VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA");
        $this->db->from('MC_MEDIDASCAUTELARES MC');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MC.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $this->db->join('MC_MEDIDASPRELACION MP', 'MP.COD_MEDIDACAUTELAR=MC.COD_MEDIDACAUTELAR ', 'LEFT');
        $this->db->join('RESPUESTAGESTION RG', 'RG.COD_RESPUESTA= MP.COD_TIPOGESTION OR RG.COD_RESPUESTA=MC.COD_RESPUESTAGESTION');

        $where = 'VW.COD_RESPUESTA = MC.COD_RESPUESTAGESTION OR  VW.COD_RESPUESTA=MP.COD_TIPOGESTION AND MP.COD_TIPOGESTION NOT IN (378,617,1011)  '
                . 'AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;

        $this->db->where($where);
        $query3 = $this->db->get('');
        $subQuery3 = $this->db->last_query();
        // echo $subQuery3;
//        $query3 = $query3->result_array();
        //   return $query3;
        //   
        //Mc_Avaluo
        $this->db->select('MA.COD_AVALUO AS PROCESO,TO_CHAR(MA.COD_TIPORESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('MC_AVALUO MA');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MA.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = 'VW.COD_RESPUESTA = MA.COD_TIPORESPUESTA AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query4 = $this->db->get('');
        $subQuery4 = $this->db->last_query();
        $query4 = $query4->result_array();
//        echo $subQuery4;
//MC_remate
//Mandamiento

        $this->db->select('MP.COD_MANDAMIENTOPAGO AS PROCESO,TO_CHAR(MP.ESTADO) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL, VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('MANDAMIENTOPAGO MP');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MP.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = 'VW.COD_RESPUESTA = MP.ESTADO AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query5 = $this->db->get('');
        $query5 = $query5->result_array();
        $subQuery5 = $this->db->last_query();
        $subQuery5;
        /* Terminación de proceso */
        $this->db->select('AJ.NUM_AUTOGENERADO AS PROCESO, TO_CHAR(GC.COD_TIPO_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL, VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,'
                . 'VW.SALDO_DEUDA,VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA'); /* AJ.AUTOSJURIDICOS AS PROCESO,GC.COD_TIPO_RESPUES */
        $this->db->from('AUTOSJURIDICOS AJ');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=AJ.COD_PROCESO_COACTIVO');
        $this->db->join('TRAZAPROCJUDICIAL GC', 'GC.COD_TRAZAPROCJUDICIAL=AJ.COD_GESTIONCOBRO');
        $this->db->join('RESPUESTAGESTION RES', 'RES.COD_RESPUESTA=GC.COD_TIPO_RESPUESTA');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = 'VW.COD_RESPUESTA = GC.COD_TIPO_RESPUESTA AND PC.AUTO_CIERRE IS NULL ' . $regional;
        $this->db->where('AJ.COD_TIPO_AUTO', 1);
        $this->db->where('AJ.COD_TIPO_PROCESO', 1);
        $this->db->where($where);
        $query6 = $this->db->get('');
        $subQuery6 = $this->db->last_query();
//        echo "<br>";
//        echo $subQuery6;echo "<br>";
        $query6 = $query6->result_array();

        $cod_respuesta = '( 170 )';

        if (!empty($titulo)):
            $where_titulo = 'AND ( RT.COD_RECEPCIONTITULO = ' . $titulo . ')';
        else:
            $titulo = '';
        endif;

        $secretario = FALSE;
        $coordinador = FALSE;
        if ($usuario == ID_SECRETARIO || $usuario == ID_COORDINADOR):
            $abogado = FALSE;
        else://El usuario es abogado
            $abogado = TRUE;
        endif;

        if ($abogado == TRUE):
            // $abogado_titulos = ' AND RT.COD_ABOGADO=' . $usuario;
            // $abogado_procesos = ' WHERE  PR.ABOGADO=' . $usuario;
            $abogado_titulos = '';
            $where_abogado = '';


        else:
            $abogado_titulos = '';
            $where_abogado = '';
        endif;
        $where_coactivo = '';
        if (!empty($cod_coactivo)):
            if ($where_abogado != ''):
                $where_coactivo = 'AND ( PR.COD_PROCESO= ' . $cod_coactivo . ')';
            else:
                $where_coactivo = 'WHERE ( PR.COD_PROCESO = ' . $cod_coactivo . ')';
            endif;
        endif;
        $where_proceso = $where_abogado . " " . $where_coactivo;
        //echo "<br>","------".$where;
//        $regional = ' '; 
        $regional = ' AND (REG.COD_REGIONAL=' . $cod_regional . ')';
        if ($this->ion_auth->is_admin()):
            $regional = '';
        endif;
        $query1 = "
            SELECT DISTINCT 
            RT.COD_RECEPCIONTITULO AS PROCESO,
            RT.COD_TIPORESPUESTA AS COD_RESPUESTA,
            RG.NOMBRE_GESTION AS RESPUESTA, 
            TO_NUMBER(F.COD_FISCALIZACION) AS COD_PROCESO,
            TO_CHAR(F.COD_FISCALIZACION) AS PROCESOPJ,
            RT.COD_ABOGADO AS ABOGADO,
            '' AS NOMBRES,
            '' AS APELLIDOS,
            E.RAZON_SOCIAL AS NOMBRE, 
            E.CODEMPRESA AS IDENTIFICACION,
            REG.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,
            REG.COD_REGIONAL AS COD_REGIONAL,
            RG.URLGESTION AS URL,
            RG.IDCARGO AS CARGO,
            CF.NOMBRE_CONCEPTO AS CONCEPTO,
            RT.FECHA_CONSULTAONBASE,VR.SALDO_DEUDA,VR.SALDO_CAPITAL,VR.SALDO_INTERES
            FROM
            
            RECEPCIONTITULOS RT,
            FISCALIZACION F, ASIGNACIONFISCALIZACION AF, EMPRESA E, CONCEPTOSFISCALIZACION CF, RESPUESTAGESTION RG, TIPOGESTION TG,
            TIPOPROCESO TP , REGIONAL REG, VW_RECEPCIONTITULOS VR
            
            WHERE 
            
            (CF.COD_CPTO_FISCALIZACION = F.COD_CONCEPTO) AND (F.COD_FISCALIZACION = RT.COD_FISCALIZACION_EMPRESA) 
            AND (AF.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC) AND (E.CODEMPRESA = AF.NIT_EMPRESA) AND
            (TG.COD_GESTION = RG.COD_TIPOGESTION) AND
            (TG.CODPROCESO = TP.COD_TIPO_PROCESO) AND 
            (RG.COD_RESPUESTA=RT.COD_TIPORESPUESTA) 
             AND (E.COD_REGIONAL=REG.COD_REGIONAL) 
             AND (VR.SALDO_DEUDA > 0)
             " . $titulo . "
             AND (RT.COD_TIPORESPUESTA NOT IN (1325,623,1114,1123,178,1367,1506))
                " . $regional . "
                    " . $abogado_titulos . "
                        AND (RT.COD_RECEPCIONTITULO=VR.NO_EXPEDIENTE)
            UNION( SELECT DISTINCT 
            RT.COD_RECEPCIONTITULO AS PROCESO,
            RT.COD_TIPORESPUESTA AS COD_RESPUESTA,
              RG.NOMBRE_GESTION AS RESPUESTA, 
            NM.COD_CARTERA_NOMISIONAL AS COD_PROCESO,
           TO_CHAR( NM.COD_CARTERA_NOMISIONAL) AS PROCESOPJ,
            RT.COD_ABOGADO AS ABOGADO,
            'NOMBRE' AS NOMBRES,
            'APELLIDO' AS APELLIDOS,
            E.RAZON_SOCIAL AS NOMBRE,
              E.COD_ENTIDAD  AS IDENTIFICACION, 
            REG.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,
            REG.COD_REGIONAL AS COD_REGIONAL,
            RG.URLGESTION AS URL,
            RG.IDCARGO AS CARGO,
            TC.NOMBRE_CARTERA AS CONCEPTO,
             RT.FECHA_CONSULTAONBASE,VR.SALDO_DEUDA,VR.SALDO_CAPITAL,VR.SALDO_INTERES
            FROM 
            RECEPCIONTITULOS RT, CNM_CARTERANOMISIONAL NM, CNM_EMPRESA E, TIPOCARTERA TC, RESPUESTAGESTION RG,
            TIPOGESTION TG, TIPOPROCESO TP, REGIONAL REG,VW_RECEPCIONTITULOS VR
            WHERE (RT.COD_CARTERA_NOMISIONAL = NM.COD_CARTERA_NOMISIONAL) AND
           (E.COD_ENTIDAD = NM.COD_EMPRESA) AND (NM.COD_TIPOCARTERA = TC.COD_TIPOCARTERA) AND (NM.COD_EMPRESA = E.COD_ENTIDAD) AND 
           (TG.COD_GESTION = RG.COD_TIPOGESTION) AND
           
            (TG.CODPROCESO = TP.COD_TIPO_PROCESO) 
            AND (RG.COD_RESPUESTA=RT.COD_TIPORESPUESTA) 
           AND (RT.COD_RECEPCIONTITULO=VR.NO_EXPEDIENTE) AND
            (E.COD_REGIONAL=REG.COD_REGIONAL) 
            AND (VR.SALDO_DEUDA > 0)
             " . $titulo . "
             AND (RT.COD_TIPORESPUESTA NOT IN (1325,623,1114,1123,178,1367))
                " . $regional . "
                      " . $abogado_titulos . " 
             UNION( 
             SELECT DISTINCT 
             RT.COD_RECEPCIONTITULO AS PROCESO,
             RT.COD_TIPORESPUESTA AS COD_RESPUESTA,
             RG.NOMBRE_GESTION AS RESPUESTA, 
             NM.COD_CARTERA_NOMISIONAL AS COD_PROCESO,
             TO_CHAR( NM.COD_CARTERA_NOMISIONAL) AS PROCESOPJ,
            RT.COD_ABOGADO AS ABOGADO,
            'NOMBRE' AS NOMBRES,
            'APELLIDO' AS APELLIDOS,
             E.NOMBRES || E.APELLIDOS AS NOMBRE , 
             TO_CHAR(E.IDENTIFICACION) AS IDENTIFICACION,
             REG.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,
             REG.COD_REGIONAL AS COD_REGIONAL,
             RG.URLGESTION AS URL,
             RG.IDCARGO AS CARGO,
             TC.NOMBRE_CARTERA AS CONCEPTO,
              RT.FECHA_CONSULTAONBASE,VR.SALDO_DEUDA,VR.SALDO_CAPITAL,VR.SALDO_INTERES
             
             FROM 
             RECEPCIONTITULOS RT, CNM_CARTERANOMISIONAL NM, CNM_EMPLEADO E, TIPOCARTERA TC,
             RESPUESTAGESTION RG, TIPOGESTION TG, TIPOPROCESO TP, REGIONAL REG,VW_RECEPCIONTITULOS VR
             
             WHERE (RT.COD_CARTERA_NOMISIONAL = NM.COD_CARTERA_NOMISIONAL) 
             AND (E.IDENTIFICACION = NM.COD_EMPLEADO) AND
             (NM.COD_TIPOCARTERA = TC.COD_TIPOCARTERA) AND 
             (TG.COD_GESTION = RG.COD_TIPOGESTION) AND
             
             (TG.CODPROCESO = TP.COD_TIPO_PROCESO)
           AND (RT.COD_RECEPCIONTITULO=VR.NO_EXPEDIENTE)          AND
             (RG.COD_RESPUESTA=RT.COD_TIPORESPUESTA) 
             
             AND  (E.COD_REGIONAL=REG.COD_REGIONAL) 
               AND (VR.SALDO_DEUDA > 0)
             " . $titulo . "
                   AND (RT.COD_TIPORESPUESTA NOT IN (1325,623,1114,1123,178,1367))
                    " . $regional . "
                          " . $abogado_titulos . "  
            ) ) "
        ;
        //echo $query1;
        $regional = ' AND VW.COD_REGIONAL=' . $cod_regional . '';
        if ($this->ion_auth->is_admin()):
            $regional = '';
        endif;
//echo $query1;
        /* procesos coactivos */
        $this->db->select('PC.COD_PROCESO_COACTIVO AS PROCESO,TO_CHAR(PC.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('PROCESOS_COACTIVOS PC');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = 'VW.COD_RESPUESTA = PC.COD_RESPUESTA AND PC.COD_RESPUESTA NOT IN (1123,1114,3042,3068)  AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query7 = $this->db->get('');
        $query7 = $query7->result_array();
        $subQuery7 = $this->db->last_query();
        //echo $subQuery7;
        //TRASLADO DE PROCESO JUDICIAL

        $this->db->select('TJ.COD_TRASLADO AS PROCESO,TO_CHAR(TJ.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL, VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('TRASLADO_JUDICIAL TJ');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=TJ.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = 'VW.COD_RESPUESTA = TJ.COD_RESPUESTA AND TJ.COD_RESPUESTA NOT IN(1124) AND  VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query8 = $this->db->get('');
        $query8 = $query8->result_array();
        $subQuery8 = $this->db->last_query();
        $subQuery8;




        /* Documentos Liquidación de Credito */

        $this->db->select('AJ.NUM_AUTOGENERADO AS PROCESO, TO_CHAR(GC.COD_TIPO_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA'); /* AJ.AUTOSJURIDICOS AS PROCESO,GC.COD_TIPO_RESPUES */
        $this->db->from('AUTOSJURIDICOS AJ');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=AJ.COD_PROCESO_COACTIVO');
        $this->db->join('TRAZAPROCJUDICIAL GC', 'GC.COD_TRAZAPROCJUDICIAL=AJ.COD_GESTIONCOBRO');
        // $this->db->join('RESPUESTAGESTION RES', 'RES.COD_RESPUESTA=GC.COD_TIPO_RESPUESTA');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where = "VW.COD_RESPUESTA = GC.COD_TIPO_RESPUESTA  AND VW.SALDO_DEUDA != 0   AND EXISTS(
        SELECT 'X'
        FROM AUTOSJURIDICOS LA
        WHERE LA.COD_PROCESO_COACTIVO = AJ.COD_PROCESO_COACTIVO
              AND (LA.COD_TIPO_AUTO IN (3, 24) )
              AND (LA.COD_TIPO_PROCESO = 14)
        HAVING MAX(LA.NUM_AUTOGENERADO) = AJ.NUM_AUTOGENERADO
      )" . $regional;
        $this->db->where_in('AJ.COD_TIPO_AUTO', array(3, 24));
        $this->db->where('AJ.COD_TIPO_PROCESO', 14);
        $this->db->where($where);
        $query10 = $this->db->get('');
        $subQuery10 = $this->db->last_query();
        $query10 = $query10->result_array();
        echo $subQuery10;
        //RESOLUCION_PRESCRIPCION
        $this->db->select('RP.COD_PRESCRIPCION AS PROCESO,TO_CHAR(RP.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('RESOLUCION_PRESCRIPCION RP');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=RP.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=RP.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO', 'inner');
        $where = 'VW.COD_RESPUESTA = RP.COD_RESPUESTA  AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query9 = $this->db->get('');
        $query9 = $query9->result_array();
        $subQuery9 = $this->db->last_query();
        // echo $subQuery9; 
        //Acuerdo de Pago
        $this->db->select('AP.NRO_ACUERDOPAGO AS PROCESO, TO_CHAR(AP.COD_RESPUESTA) AS COD_RESPUESTA, VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,'
                . 'VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA'); /* AJ.AUTOSJURIDICOS AS PROCESO,GC.COD_TIPO_RESPUES */
        $this->db->from('ACUERDOPAGO AP');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=AP.COD_PROCESO_COACTIVO');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=PC.COD_PROCESO_COACTIVO');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO');
        $where2 = 'VW.COD_RESPUESTA = AP.COD_RESPUESTA AND AP.JURIDICO=1 AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL AND AP.COD_RESPUESTA!=1272' . $regional;
        $this->db->where($where2);
        $this->db->where('AP.JURIDICO', 1);
        $query11 = $this->db->get('');
        $subQuery11 = $this->db->last_query();
        // $query11 = $query11->result_array();
        //  echo $subQuery11 ; 
        //die();
        //REMATE
        $this->db->select('MR.COD_REMATE AS PROCESO,TO_CHAR(MR.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,VW.SALDO_DEUDA,VW.SALDO_CAPITAL,'
                . 'VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('MC_REMATE MR');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MR.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=MR.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO', 'inner');
        $where = 'VW.COD_RESPUESTA = MR.COD_RESPUESTA AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query12 = $this->db->get('');
        $query12 = $query12->result_array();
        $subQuery12 = $this->db->last_query();
        //echo $subQuery12;
        //NULIDAD
        $this->db->select('N.COD_NULIDAD AS PROCESO,TO_CHAR(N.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,'
                . 'VW.SALDO_DEUDA,VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('NULIDAD N');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=N.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=N.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO', 'inner');
        $where = 'VW.COD_RESPUESTA = N.COD_RESPUESTA AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL';
        $this->db->where($where);
        $query13 = $this->db->get('');
        $query13 = $query13->result_array();
        $subQuery13 = $this->db->last_query();
        //   echo $subQuery13;

        /* REMISIBILIDAD */
        $this->db->select('REM.COD_REMISIBILIDAD AS PROCESO,TO_CHAR(REM.COD_TIPORESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION, US.NOMBRES, US.APELLIDOS, VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL,VW.CONCEPTO,VW.FECHA_COACTIVO AS FECHA_RECEPCION,'
                . 'VW.SALDO_DEUDA,VW.SALDO_CAPITAL,VW.SALDO_INTERES,VW.NO_EXPEDIENTE,VW.COD_EXPEDIENTE_JURIDICA');
        $this->db->from('REM_REMISIBILIDAD REM');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=REM.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('VW_PROCESOS_COACTIVOS VW', 'VW.COD_PROCESO_COACTIVO=REM.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=PC.ABOGADO', 'inner');
        $where = 'VW.COD_RESPUESTA = REM.COD_TIPORESPUESTA AND REM.COD_TIPORESPUESTA IN (1116, 1117,1118, 1119,1121 ,1472,3068) AND VW.SALDO_DEUDA!=0 AND PC.AUTO_CIERRE IS NULL' . $regional;
        $this->db->where($where);
        $query14 = $this->db->get('');
        $query14 = $query14->result_array();
        $subQuery14 = $this->db->last_query();

        $querys = "($subQuery2   UNION $subQuery3  UNION $subQuery4 UNION $subQuery5 UNION $subQuery6 UNION $subQuery7 UNION $subQuery8  UNION $subQuery9 UNION  $subQuery10 UNION $subQuery12 UNION $subQuery11  UNION $subQuery13 UNION $subQuery14)";
        //$querys = "($subQuery3)";
        ////$querys = "($subQuery2  )";
        // $querys = "($subQuery10)";

        $qry_final = "SELECT PR.COD_PROCESO,PR.IDENTIFICACION,PR.NOMBRE,PR.NOMBRE_REGIONAL,PR.COD_REGIONAL,PR.NOMBRES,PR.APELLIDOS,
            PR.PROCESOPJ,PR.ABOGADO,
             LISTAGG (PR.NO_EXPEDIENTE,'?*') WITHIN GROUP (ORDER BY PR.NO_EXPEDIENTE) \"NUMEROS EXPEDIENTES\",
             LISTAGG (PR.SALDO_DEUDA,'?*') WITHIN GROUP (ORDER BY PR.SALDO_DEUDA) \"SALDOS DEUDAS\",
               LISTAGG (PR.SALDO_CAPITAL,'?*') WITHIN GROUP (ORDER BY PR.SALDO_CAPITAL) \"SALDOS CAPITAL\",
                 LISTAGG (PR.SALDO_INTERES,'?*') WITHIN GROUP (ORDER BY PR.SALDO_INTERES) \"SALDOS INTERESES\",
            LISTAGG (PR.FECHA_RECEPCION,'?*') WITHIN GROUP (ORDER BY PR.FECHA_RECEPCION) \"FECHAS RECEPCION\",
            LISTAGG (PR.CONCEPTO,'?*') WITHIN GROUP (ORDER BY PR.CONCEPTO) \"CONCEPTOS UNIDOS\",
        LISTAGG (PR.RESPUESTA,'*?*') WITHIN GROUP (ORDER BY PR.COD_RESPUESTA) \"RESPUESTAS UNIDAS\",
        LISTAGG (PR.COD_EXPEDIENTE_JURIDICA,'?*') WITHIN GROUP (ORDER BY PR.COD_EXPEDIENTE_JURIDICA) \"FISCALIZACIONES\",
        LISTAGG (PR.COD_RESPUESTA,'*?*') WITHIN GROUP (ORDER BY PR.COD_RESPUESTA) \"CODIGOS RESPUESTAS\"
        
        FROM " . $querys . " PR " . $where_proceso . " 
        GROUP BY PR.COD_PROCESO,PR.IDENTIFICACION,PR.NOMBRE,PR.NOMBRE_REGIONAL,PR.COD_REGIONAL,
        PR.NOMBRES,PR.APELLIDOS,PR.PROCESOPJ,PR.ABOGADO";
        //   echo "<br>";
        //echo $qry_final;
//        $qry_final2="SELECT MAX(FECHA),COD_PROCESO, IDENTIFICACION,NOMBRE,NOMBRE_REGIONAL,COD_REGIONAL,NOMBRES,APELLIDOS, "
//                . "PROCESOPJ,ABOGADO,NUMEROS EXPEDIENTES,SALDOS DEUDAS,SALDOS CAPITAL"
//                . " FROM ( ".$qry_final." ) PR, TRAZAPROCJUDICIAL TR WHERE TR.COD_JURIDICO=PR.COD_PROCESO  GROUP BY PR.COD_PROCESO"
//                . "ORDER BY 1 DESC ";  
//        echo  $qry_final2;
        $querys = $this->db->query($qry_final);
        $resultado = $querys->result_array();
        $query1 = $this->db->query($query1);
        $query1 = $query1->result_array();
        $resultado_final = array('titulos' => $query1, 'procesos' => $resultado);
        if (!empty($cod_coactivo)):
            $resultado_final = array('titulos' => $query1 = array(), 'procesos' => $resultado);
        endif;

        if (!empty($titulo)):
            $resultado_final = array('titulos' => $query1, 'procesos' => $resultado = array());
        endif;

        return $resultado_final;
    }

    function get_usuario($usuario) {
        $this->db->where('IDUSUARIO', $usuario);
        $query = $this->db->get('USUARIOS');
        return $query->result_array()[0];
    }

    function procesos_coactivos($admin, $regional, $usuario, $cod_coactivo, $titulo) {


        // echo $regional;
        /*         * Para listar los procesos que se encuentran en Recepción de titulos se consulta la vista 
          VW_RECEPCIONTITULOS la cual permite consultar los datos básicos
         * $subQuery1. Para listar los procesos coactivos se consulta la vista
          VW_PROCESOS_COACTIVOS la cual permite consultar los datos básicos
         * @param integer $regional
         * @param integer $idusuario
         * @param integer $cod_coactivo
         * @param integer $titulo
         * @return array $resultado
         */
        //echo $usuario."<br>";
        //echo ID_SECRETARIO. ' y '. ID_COORDINADOR;die();
        $user = $this->get_usuario($usuario);
        $cod_respuesta = '( 170 )';
        $cod_regional = $regional;
        //echo "<pre>Esta es la regional que esta llegando".$cod_regional;
        //echo "<pre>Esta es la regional que esta llegando".$titulo;
        if (!empty($titulo)):
            $where_titulo = 'AND ( RT.COD_RECEPCIONTITULO = ' . $titulo . ')';
        else:
            $titulo = '';
        endif;

        $secretario = FALSE;
        $coordinador = FALSE;
        if ($usuario == ID_SECRETARIO || $usuario == ID_COORDINADOR || $user['IDCARGO'] == 283):
            $abogado = FALSE;
            $abogado_titulos = '';
            $abogado_procesos = '';
        else://El usuario es abogado
            $abogado = TRUE;
            $abogado_titulos = ' AND RC.COD_ABOGADO=' . $usuario;
            $abogado_procesos = ' WHERE  PR.ABOGADO=' . $usuario;
        endif;

        $where_coactivo = '';
        if (!empty($cod_coactivo)):
            if ($abogado_procesos != ''):
                $where_coactivo = ' AND ( PR.COD_PROCESO= ' . $cod_coactivo . ')';
            else:
                $where_coactivo = ' WHERE ( PR.COD_PROCESO = ' . $cod_coactivo . ')';
            endif;
        endif;
        $where_proceso = $abogado_procesos . " " . $where_coactivo;
        //

        $regional = ' AND VW.COD_REGIONAL =' . $regional;
        //echo $regional
        if ($admin || $user['IDCARGO'] == 283):
            $regional = '';
            $where_proceso = '';
        endif;
        //echo $regional;
        // $regional = ' ';
        $this->db->select("RC.COD_RECEPCIONTITULO AS COD_RECEPCIONTITULO,
                           VW.IDENTIFICACION AS IDENTIFICACION,
                           VW.EJECUTADO AS NOMBRE, 
                        VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,
                           VW.COD_REGIONAL AS COD_REGIONAL,
                           US.NOMBRES,
                           US.APELLIDOS,
                           RC.COD_FISCALIZACION_EMPRESA || '' || RC.COD_CARTERA_NOMISIONAL AS PROCESOPJ,
                           RC.COD_ABOGADO AS ABOGADO,
                           VW.ULTIMA_ACTUACION,
                           TO_CHAR(VW.COD_CPTO_FISCALIZACION) AS CPTO,
                           TO_CHAR(VW.NO_EXPEDIENTE) AS NUMEROS_EXPEDIENTES,
                           TO_CHAR(VW.SALDO_DEUDA) AS SALDOS_DEUDAS ,
                           TO_CHAR(VW.SALDO_CAPITAL) AS SALDOS_CAPITAL,
                           TO_CHAR(VW.SALDO_INTERES) AS SALDOS_INTERESES,
                           TO_CHAR(VW.FECHA_COACTIVO) AS FECHAS_RECEPCION,
                           TO_CHAR(VW.CONCEPTO) AS CONCEPTOS_UNIDOS,
                           TO_CHAR(RG.NOMBRE_GESTION) AS RESPUESTAS_UNIDAS, 
                           TO_CHAR(RC.COD_FISCALIZACION_EMPRESA) || '' || TO_CHAR(RC.COD_CARTERA_NOMISIONAL) AS FISCALIZACIONES,
                           TO_CHAR(RG.COD_RESPUESTA) AS CODIGOS_RESPUESTAS,
                           ");
        $this->db->from('RECEPCIONTITULOS RC');
        $this->db->join('VW_RECEPCIONTITULOS VW', 'VW.NO_EXPEDIENTE=RC.COD_RECEPCIONTITULO', 'inner');
        $this->db->join('RESPUESTAGESTION RG', 'RG.COD_RESPUESTA=RC.COD_TIPORESPUESTA', 'inner');
        // $this->db->join('REGIONAL REG', 'REG.COD_REGIONAL=SUBSTR(RC.COD_FISCALIZACION_EMPRESA,0,2)', 'inner');
        $this->db->join('USUARIOS US', 'US.IDUSUARIO=RC.COD_ABOGADO', 'left');
        $where = 'RC.COD_TIPORESPUESTA NOT IN (1325,623,1114,1123,178,1367,1932)' . $regional;
        $where = 'RC.COD_TIPORESPUESTA NOT IN (1325,623,1114,1123,178,1367,1932)' . $abogado_titulos . $regional;

        $this->db->where($where);
        $query1 = $this->db->get();
        $subQuery1 = $this->db->last_query();
        // $resultado = $this->db->get();1


        $resultado = $query1->result_array();
        //echo "<pre> holoo";+
        //  print_r( $where );echo "</pre>";


        /* Consulto todos los procesos coactivos */

        // $no_int='(1114,1472,1124,1367,1175,1123)';
        $no_int = '(1114,1472,1124,1123,1979,1939,3068,3042)';
        $qry_vista = "  SELECT 
    DISTINCT 
      VW1.PROCESO, 
      VW1.COD_TIPO_RESPUESTA AS COD_RESPUESTA, 
      RG.NOMBRE_GESTION AS RESPUESTA, 
      VW1.COD_PROCESO_COACTIVO, 
      VW.ABOGADO AS ABOGADO, 
      VW.COD_PROCESOPJ AS PROCESOPJ, 
      VW.EJECUTADO AS NOMBRE, 
      VW.IDENTIFICACION AS IDENTIFICACION, 
      US.NOMBRES, 
      US.APELLIDOS, 
      VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL, 
      VW.COD_REGIONAL AS COD_REGIONAL, 
      VW.CONCEPTO, VW.FECHA_COACTIVO AS FECHA_RECEPCION, 
      VW.SALDO_DEUDA, 
      VW.SALDO_CAPITAL, 
      VW.SALDO_INTERES, 
      VW.NO_EXPEDIENTE, 
      VW.COD_EXPEDIENTE_JURIDICA, 
      VW.ULTIMA_ACTUACION,
      TO_CHAR(VW.COD_CPTO_FISCALIZACION) AS CPTO
      
  FROM VW_BANDEJA_01 VW1 
        INNER JOIN VW_PROCESOS_COACTIVOS_0002 VW ON VW.COD_PROCESO_COACTIVO=VW1.COD_PROCESO_COACTIVO 
        INNER JOIN USUARIOS US ON US.IDUSUARIO=VW.ABOGADO 
        INNER JOIN RESPUESTAGESTION RG ON RG.COD_RESPUESTA = VW1.COD_TIPO_RESPUESTA";
        if ($user['IDCARGO'] != 283) {
            $qry_vista = $qry_vista . " WHERE VW.COD_REGIONAL = $cod_regional";
        }


        $qry_procesos = "SELECT PR.COD_PROCESO_COACTIVO AS COD_PROCESO,PR.IDENTIFICACION,PR.NOMBRE,PR.NOMBRE_REGIONAL,
        PR.COD_REGIONAL,PR.NOMBRES,PR.APELLIDOS,
                    PR.PROCESOPJ,PR.ABOGADO,PR.ULTIMA_ACTUACION,PR.CPTO,
                    LISTAGG (PR.NO_EXPEDIENTE,'?*') WITHIN GROUP (ORDER BY PR.NO_EXPEDIENTE) \"NUMEROS EXPEDIENTES\",
                    LISTAGG (PR.SALDO_DEUDA,'?*') WITHIN GROUP (ORDER BY PR.SALDO_DEUDA) \"SALDOS DEUDAS\",
                    LISTAGG (PR.SALDO_CAPITAL,'?*') WITHIN GROUP (ORDER BY PR.SALDO_CAPITAL) \"SALDOS CAPITAL\",
                    LISTAGG (PR.SALDO_INTERES,'?*') WITHIN GROUP (ORDER BY PR.SALDO_INTERES) \"SALDOS INTERESES\",
                    LISTAGG (PR.FECHA_RECEPCION,'?*') WITHIN GROUP (ORDER BY PR.FECHA_RECEPCION) \"FECHAS_RECEPCION\",
                    LISTAGG (PR.CONCEPTO,'?*') WITHIN GROUP (ORDER BY PR.CONCEPTO) \"CONCEPTOS UNIDOS\",
                    LISTAGG (PR.RESPUESTA,'*?*') WITHIN GROUP (ORDER BY PR.COD_RESPUESTA) \"RESPUESTAS_UNIDAS\",
                    LISTAGG (PR.COD_EXPEDIENTE_JURIDICA,'?*') WITHIN GROUP (ORDER BY PR.COD_EXPEDIENTE_JURIDICA) \"FISCALIZACIONES\",
                    LISTAGG (PR.COD_RESPUESTA,'*?*') WITHIN GROUP (ORDER BY PR.COD_RESPUESTA) \"CODIGOS_RESPUESTAS\"
                    FROM (" . $qry_vista . " AND  VW1.COD_TIPO_RESPUESTA NOT IN " . $no_int . " ) PR " . $where_proceso . " 
                    GROUP BY PR.COD_PROCESO_COACTIVO,PR.IDENTIFICACION,PR.NOMBRE,PR.NOMBRE_REGIONAL,PR.COD_REGIONAL,
                    PR.NOMBRES,PR.APELLIDOS,PR.PROCESOPJ,PR.ABOGADO, PR.ULTIMA_ACTUACION,PR.CPTO";

        /*  $con = $this->db->query($qry_procesos);
          $rs = $con->result_array;
          echo "<pre>";
          print_r($rs);
          echo "</pre>";
          exit(); */


        $qry_final = "(  $subQuery1 UNION $qry_procesos ) ORDER BY ULTIMA_ACTUACION DESC NULLS LAST";
        $querys = $this->db->query($qry_final);
        $resultado = $querys->result_array;

        /* echo "<pre>";

          // print_r($resultado);
          echo "</pre>";
          //s$this->db->query($resultado);
          exit(); 
*/
//          $con = $this->db->query($qry_final);
//          $rs = $con->result_array;
//          echo "<pre>";
//          echo $this->db->last_query();
//          //print_r($rs);
//          echo "</pre>";
//          exit();
         

        //echo  $qry_final;
        //echo  $subQuery1; 
        // die();
        //  sizeof($resultado);
        return $resultado;
    }

    function numerocoactivo($id) {
        // echo "<prehh>"; print_r($id); echo "</pre>";die();
        $this->db->select("COD_PROCESO_COACTIVO");
        $this->db->where("COD_PROCESOPJ", $id);
        $dato = $this->db->get('PROCESOS_COACTIVOS');
        $datos = $dato->result_array[0]['COD_PROCESO_COACTIVO'];
        //  echo "<pre>hhh"; print_r($dato); echo "</pre>";//die();
        return $datos;
    }

    function select_traza($cod_coactivo) {
        $this->db->select('COD_TIPO_RESPUESTA');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_JURIDICO', $cod_coactivo);
        $this->db->where('COMENTARIOS', 'Abogado Subió el Archivo Firmado Y Aprobado');
        $resultado = $this->db->get('');

        $resultado = $resultado->result_array();
        return $resultado;
    }

      function select_traza4($cod_coactivo) {
        $this->db->select('COD_TIPO_RESPUESTA');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_JURIDICO', $cod_coactivo);
        $this->db->where('COMENTARIOS', 'Auto DE Reanudacion');
        $resultado = $this->db->get('');

        $resultado = $resultado->result_array();
        return $resultado;
    }

    /*
      CONTENEDOR FUNCIONES SPRINT 4 Y 5
      Desarrollador:  Luis Arcos
      Fecha Actualización:    26/04/2018
     */

//FUNCION PARA VERIFICAR QUE UNA GESTION NO SE MUESTRE SI YA SE HA REALIZADO OTRA
    function realizoTraza($cod_juridico, $tp_respuesta) {
        $this->db->select('COD_TIPOGESTION');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_JURIDICO', $cod_juridico);
        $this->db->where('COD_TIPO_RESPUESTA', $tp_respuesta);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA VERIFICAR QUE SE REALIZO LA LIQUIDACION DE CREDITO
    function verificarLiquidacion($cod_coactivo) {
        $this->db->select('NUM_AUTOGENERADO');
        $this->db->from('AUTOSJURIDICOS');
        $this->db->where('COD_TIPO_AUTO', 25);
        $this->db->where('COD_TIPO_PROCESO', 14);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }
    
//FUNCION PARA VERIFICAR QUE SE APROBO LA LIQUIDACION DE CREDITO
    function verificarLiquidacionAprove($cod_coactivo) {
        $this->db->select('COD_ESTADOAUTO');
        $this->db->from('AUTOSJURIDICOS');
        $this->db->where('COD_TIPO_AUTO', 25);
        $this->db->where('COD_TIPO_PROCESO', 14);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $estado = $query->result_array();
        if (!empty($estado)) {
            return $estado[0]['COD_ESTADOAUTO'];
        } else {
            return $estado = 0;
        }        
    }

//FUNCION PARA VERIFICAR EL TIPO DE BIEN ACTUALMENTE GESTIONADO
    function verificarTipoBien($cod_coactivo) {
        $query = $this->db->query("SELECT COD_CONCURRENCIA FROM MC_MEDIDASPRELACION WHERE COD_MEDIDAPRELACION = 
                                (SELECT MAX(COD_MEDIDAPRELACION) FROM MC_MEDIDASPRELACION WHERE COD_MEDIDACAUTELAR = 
                                (SELECT COD_MEDIDACAUTELAR FROM MC_MEDIDASCAUTELARES WHERE COD_PROCESO_COACTIVO = " . $cod_coactivo . "))");
        
        $cod_tipo = $query->result_array();
       
        if (!empty($cod_tipo)) {
            return $cod_tipo[0]['COD_CONCURRENCIA'];
        } else {
            return $cod_tipo = 0;
        }
    }

//FUNCION PARA OBTENER EL TIPO DEMANDA NULIDAD DE UN PROCESO COACTIVO
    function obtenerDemanda($codCoa) {
        $this->db->select('DEM_NULIDAD');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $tipo_dem = $query->result_array();
        if (!empty($tipo_dem)) {
            return $tipo_dem[0]['DEM_NULIDAD'];
        } else {
            return $tipo_dem = 0;
        }
    }

//FUNCION PARA VERIFICAR QUE LA DILIGENCIA DE APLICACION DE TITULOS SE ENCUENTRA BLOQUEADA
    function verificarSuspRemate($cod_coactivo) {
        $this->db->select('COD_REMATE');
        $this->db->from('MC_REMATE');
        $this->db->where('COD_RESPUESTA', 1744);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA OBTENER EL CODIGO COACTIVO POR NIT DE LA EMPRESA
    function cod_coactivo_xnit($identificacion) {
        $this->db->select_max('COD_PROCESO_COACTIVO');
        $this->db->from("PROCESOS_COACTIVOS");
        $this->db->where('IDENTIFICACION', $identificacion);
        $query = $this->db->get();
        $cod = $query->result_array();
        if ($query->num_rows() > 0) {
            return $cod[0]['COD_PROCESO_COACTIVO'];
        }
    }

//FUNCION PARA VERIFICAR QUE UN PROCESO YA ES COACTIVO
    function verificarCoactivo($cod_proceso) {
        $this->db->select('COD_PROCESO_COACTIVO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_proceso);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA VERIFICAR QUE LA DILIGENCIA DE APLICACION DE TITULOS SE ENCUENTRA BLOQUEADA
    function verificarCerrado($cod_coactivo) {
        $this->db->select('TERMINACION_PROCESO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $term = $query->result_array();
        if ($query->num_rows() > 0) {
            return $term[0]['TERMINACION_PROCESO'];
        }
    }

//FUNCION PARA OBTENER LA ULTIMA GESTION REALIZADA EN TRAZA POR UN PROCESO COACTIVO
    function obtenerUltimaGestion($cod_coactivo) {
        $query = $this->db->query("SELECT COD_TIPO_RESPUESTA FROM TRAZAPROCJUDICIAL
                                   WHERE COD_TRAZAPROCJUDICIAL = (SELECT MAX(COD_TRAZAPROCJUDICIAL) FROM TRAZAPROCJUDICIAL WHERE COD_JURIDICO = " . $cod_coactivo . ")");
        $cod_res = $query->result_array();
        if (!empty($cod_res)) {
            return $cod_res[0]['COD_TIPO_RESPUESTA'];
        } else {
            return $cod_res = 0;
        }
    }

//FUNCION PARA VERIFICAR QUE UN PROCESO COACTIVO SE ENCUENTRA SUSPENDIDO EN LA DILIGENCIA DE APLICACION DE TITULOS
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
    
//FUNCION PARA VERIFICAR QUE LA SUSPENSION EN REMATE SE ENCUENTRA VIGENTE
    public function verfSuspensionRemate($codCoa) {
        $this->db->select('SUSPENSION_REMATE');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $suspendido = $query->result_array();
        if (!empty($suspendido)) {
            return $suspendido[0]['SUSPENSION_REMATE'];
        } else {
            return $suspendido = 0;
        }
    }

//FIN CONTENEDOR FUNCIONES SPRINT 4 Y 5

    function cabecera($respuesta, $proceso) {

        $this->db->select('');
        $this->db->from('VW_PROCESOS_COACTIVOS VW');
        $this->db->where('VW.COD_RESPUESTA', $respuesta);
        $this->db->where('VW.COD_PROCESO_COACTIVO', $proceso);
        $resultado = $this->db->get();
        $resultado = $resultado->result_array();
        return $resultado[0];
    }

    function Liquidaciones($cod_coactivo) {
        /* Metodo que permite consultar las liquidaciones de un proceso coactivo */
    }

    function funcionarios($regional) {
        $this->db->select('');
        $this->db->from('REGIONAL RG');
        $this->db->where('RG.COD_REGIONAL', $regional);
        $regional = $this->db->get('');
        $regional = $regional->result_array();

        //Consulto los id
    }

    function mandamiento($cod_coactivo) {
        $respuesta = 0;
        $this->db->select("MP.COD_MANDAMIENTOPAGO, MP.ESTADO");
        $this->db->from("MANDAMIENTOPAGO MP");
        $this->db->where("MP.COD_PROCESO_COACTIVO=", $cod_coactivo, FALSE);
        $resultado = $this->db->get('');
        $resultado = $resultado->result_array();
        if ($resultado):
            if ($resultado[0]['ESTADO'] == 204):
                $respuesta = TRUE;
            endif;
        endif;
        return $respuesta;
    }

    function medidas($cod_coactivo) {
        $respuesta = 0;
        $this->db->select("MC.COD_MEDIDACAUTELAR, MC.COD_RESPUESTAGESTION");
        $this->db->from("MC_MEDIDASCAUTELARES MC");
        $this->db->where("MC.COD_PROCESO_COACTIVO=", $cod_coactivo, FALSE);
        //$this->db->where("MC.COD_RESPUESTAGESTION", '204');
        $resultado = $this->db->get('');
        $resultado = $resultado->result_array();
        foreach ($resultado as $key => $value) {
            if ($resultado):
                if ($value['COD_RESPUESTAGESTION'] == 204):
                    $respuesta = TRUE;
                endif;
            endif;
        }
        return $respuesta;
    }

    function acercamiento($cod_coactivo) {
        $respuesta = 0;
        $this->db->select("CP.COD_TIPO_RESPUESTA");
        $this->db->from("COBROPERSUASIVO CP");
        $this->db->where("CP.COD_PROCESO_COACTIVO=", $cod_coactivo, FALSE);
        $resultado = $this->db->get('');
        $resultado = $resultado->result_array();
        if ($resultado):
            if ($resultado[0]['COD_TIPO_RESPUESTA'] == 184):
                $respuesta = TRUE;
            endif;
        endif;
        return $respuesta;
    }

    function detalle_remate($cod_coactivo) {
        $this->db->select('MA.COD_TIPO_INMUEBLE, MR.COD_AVALUO,MR.COD_REMATE AS PROCESO,TO_NUMBER(MR.COD_RESPUESTA) AS COD_RESPUESTA,VW.RESPUESTA AS RESPUESTA,'
                . 'PC.COD_PROCESO_COACTIVO AS COD_PROCESO,PC.ABOGADO AS ABOGADO, PC.COD_PROCESOPJ AS PROCESOPJ,VW.EJECUTADO AS NOMBRE,'
                . 'PC.IDENTIFICACION AS IDENTIFICACION,VW.NOMBRE_REGIONAL AS NOMBRE_REGIONAL,'
                . ' VW.COD_REGIONAL AS COD_REGIONAL, GR.PARAMETRO,MR.COD_PROCESO_COACTIVO,GR.IDGRUPO, RG.IDCARGO');
        $this->db->from('MC_REMATE MR');
        $this->db->join('MC_AVALUO MA', 'MA.COD_AVALUO = MR.COD_AVALUO', 'inner');
        $this->db->join('PROCESOS_COACTIVOS PC', 'PC.COD_PROCESO_COACTIVO=MR.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('VW_PROCESOS_BANDEJA VW', 'VW.COD_PROCESO_COACTIVO=MR.COD_PROCESO_COACTIVO', 'inner');
        $this->db->join('MC_GESTIONREMATE GR', 'GR.COD_RESPUESTA=MR.COD_RESPUESTA', 'LEFT');
        $this->db->join('RESPUESTAGESTION RG', 'RG.COD_RESPUESTA = GR.COD_RESPUESTA');
        $where = 'VW.COD_RESPUESTA = MR.COD_RESPUESTA AND MR.COD_PROCESO_COACTIVO=' . $cod_coactivo;
        $this->db->where($where);

        $query12 = $this->db->get('');
        $query12 = $query12->result_array();
        /*
          $subQuery12 = $this->db->last_query();
          $usuario = $this->ion_auth->user()->row();
          print_r($usuario);die();
          echo $subQuery12;
          die();
         */
        return $query12;
    }

    function CrearFacilidadPago($datos) {
//        $this->db->select("NRO_ACUERDOPAGO");
//        $this->db->from("ACUERDOPAGO");
//        $this->db->where("ACUERDOPAGO.COD_PROCESO_COACTIVO=", $datos['cod_proceso'], FALSE);
//        $resultado1 = $this->db->get();
//
//        // echo $resultado1->num_rows();
//        if ($resultado1->num_rows() == 0):


        $this->db->set("NITEMPRESA", $datos['nit']);
        $this->db->set("COD_RESPUESTA", $datos['tipo_respuesta'], FALSE);
        $this->db->set("USUARIO_GENERA", ID_USUARIO, FALSE);
        $this->db->set("COD_CONCEPTO_COBRO", $datos['cod_concepto'], FALSE);
        $this->db->set("FECHA_CREACION", 'SYSDATE', FALSE);
        $this->db->set("COD_REGIONAL", $datos['cod_regional'], FALSE);
        $this->db->set("ESTADOACUERDO", 1);
        $this->db->set("JURIDICO", 1);
        $this->db->set("COD_PROCESO_COACTIVO", $datos['cod_proceso'], FALSE);
        $this->db->set('NRO_LIQUIDACION',  $datos['num_liquidacion'], FALSE);
        $this->db->insert("ACUERDOPAGO");

//        endif;

        if ($this->db->affected_rows() == '1'):
            $this->db->set('ACUERDO_PAGO', 0);
            $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
            $this->db->update('PROCESOS_COACTIVOS');

                 
               
                

            /* Actualizo cada liquidación  para cada titulo del proceso coactivo */
            foreach ($titulos_facilidad as $liquidacion):

                $this->db->set('COD_PROCESO_COACTIVO', $datos['cod_proceso'], FALSE);
                $this->db->set('COD_TIPOPROCESO', 18);
                $this->db->where('NUM_LIQUIDACION', $liquidacion);
                $this->db->update('LIQUIDACION');
                

               

            endforeach;
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function AutoTerminacionTitulo($datos) {
        /* Función que valida si un titulo tiene un auto de terminación de proceso creado */
        $resultado = 0;
        $this->db->select('AJ.NUM_AUTOGENERADO');
        $this->db->from('AUTOSJURIDICOS AJ');
        $this->db->join('RECEPCIONTITULOS RT', 'RT.NUM_AUTOGENERADO=AJ.NUM_AUTOGENERADO', 'inner');
        $this->db->where('AJ.COD_PROCESO_COACTIVO', $datos['COD_PROCESO']);
        $this->db->where('RT.COD_RECEPCIONTITULO', $datos['TITULO']);
        $this->db->where('RT.CERRADO', 0);
        $query = $this->db->get();
        if ($query->num_rows() > 0):
            $resultado = $resultado->result_array();
            $resultado = $resultado[0];
        endif;
        //echo $this->db->last_query();die();
        print_r($resultado);
        return $resultado;
    }

    function ActualizaProcesoCoactivo($cod_coactivo) {
        /** Función que permite actualizar un proceso coactivo cuando se ha enviado a terminación del proceso */
        $this->db->set('AUTO_CIERRE', 0);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        if ($this->db->affected_rows() == '1'):
            return TRUE;
        else:
            return FALSE;
        endif;
    }

    function regional($regional) {
        $this->db->select('REG.CEDULA_COORDINADOR, REG.CEDULA_SECRETARIO, USUARIO_COORDINADOR.NOMBREUSUARIO NOMBRE_COORDINADOR, USUARIO_SECRETARIO.NOMBREUSUARIO NOMBRE_SECRETARIO');
        $this->db->from('REGIONAL REG');
        $this->db->join('USUARIOS USUARIO_COORDINADOR', 'USUARIO_COORDINADOR.IDUSUARIO =REG.CEDULA_COORDINADOR', 'inner');
        $this->db->join('USUARIOS USUARIO_SECRETARIO', 'USUARIO_SECRETARIO.IDUSUARIO =REG.CEDULA_SECRETARIO', 'inner');
        $this->db->where('REG.COD_REGIONAL', $regional, FALSE);
        $resultado = $this->db->get();
        $resultado = $resultado->result_array();
        return $resultado[0];
    }

    function existeCodigo($codigo) {

        $this->db->select('COD_PROCESO_COACTIVO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codigo);
        $res = $this->db->get();

        if ($res->num_rows() > 0) {
            $resultado = 0;
        } else {
            $this->db->select('COD_RECEPCIONTITULO');
            $this->db->where('COD_RECEPCIONTITULO', $codigo);
            $result = $this->db->get('RECEPCIONTITULOS');
            if ($result->num_rows() > 0) {
                $resultado = 1;
            }
        }
        return $resultado;
    }

    function existeRespuestaIncumplimiento($codigo) {

        $this->db->select('COD_TIPO_RESPUESTA');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_TIPO_RESPUESTA', 1277);
        $this->db->where('COD_JURIDICO', $codigo);
        $res = $this->db->get();

        if ($res->num_rows() > 0) {
            $resultado = 1;
        } else {
            $resultado = 0;
        }
        return $resultado;
    }

    function consultarCodRespuestaProceso($codigoProceso, $procedencia)
    /**
     * Función que recibe el código de proceso a consultar y retorna el código de respuesta actual del proceso.
     *
     * @param integer $codigoProceso
     * @return string $codigoRespuesta
     * @return boolean FALSE - error
     */ {
        if ($procedencia == '1') {
            $this->db->select('COD_TIPORESPUESTA as COD_RESPUESTA');
            $this->db->from('RECEPCIONTITULOS');
            $this->db->where('COD_RECEPCIONTITULO', $codigoProceso);
            $resultado = $this->db->get();
        } else {
            $this->db->select('COD_RESPUESTA');
            $this->db->from('PROCESOS_COACTIVOS');
            $this->db->where('COD_PROCESO_COACTIVO', $codigoProceso);
            $resultado = $this->db->get();
        }
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $codigoRespuesta = $resultado->row_array();
            return $codigoRespuesta;
        else:
            return FALSE;
        endif;
    }

    function consultarProceso($codigoProceso, $codigoRespuesta, $procedencia)
    /**
     * Función que recibe el código de proceso y el codigo de respueta del proceso  a consultar y retorna la información y liquidaciones asociadas al proceso.
     *
     * @param integer $codigoProceso
     * @param integer $codigoRespuesta
     * @return string $codigoRespuesta
     * @return boolean FALSE - error
     */ {
        if ($procedencia == '0') {
            $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE,COD_PROCESOPJ, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
            $this->db->from('VW_PROCESOS_COACTIVOS');
            $this->db->where('COD_PROCESO_COACTIVO', $codigoProceso);
            $this->db->where('COD_RESPUESTA', $codigoRespuesta);
            $resultado = $this->db->get();
        } else {

            $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE,CODABOGADO, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
            $this->db->from('VW_RECEP_TITULO_LIQ');
            $this->db->where('NO_EXPEDIENTE', $codigoProceso);
            $this->db->where('COD_TIPO_RESPUESTA', $codigoRespuesta);
            $resultado = $this->db->get();

            if ($resultado->num_rows() == 0) {
                $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE,CODABOGADO, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
                $this->db->from('VW_NO_EXIST_TRAZA');
                $this->db->where('NO_EXPEDIENTE', $codigoProceso);
                $this->db->where('COD_TIPO_RESPUESTA', $codigoRespuesta);
                $resultado = $this->db->get();
            }
        }
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado):
            $tmp = NULL;
            foreach ($resultado->result_array() as $fiscalizacion):
                $tmp[] = $fiscalizacion;
            endforeach;
            $proceso = $tmp;
        else:
            $proceso = FALSE;
        endif;
        return $proceso;
    }

    function consultarProceso2($codigoProceso, $codigoRespuesta, $procedencia)

    /**
     * Función que recibe el código de proceso y el codigo de respueta del proceso  a consultar y retorna la información y liquidaciones asociadas al proceso.
     *
     * @param integer $codigoProceso
     * @param integer $codigoRespuesta
     * @return string $codigoRespuesta
     * @return boolean FALSE - error
     */ {
        if ($procedencia == '0') {

            $SQL = "SELECT TZ.COD_TRAZAPROCJUDICIAL,E.CODEMPRESA AS IDENTIFICACION,
                E.RAZON_SOCIAL AS EJECUTADO,E.REPRESENTANTE_LEGAL AS REPRESENTANTE,
                E.DIRECCION AS DIRECCION, E.CORREOELECTRONICO AS CORREO_ELECTRONICO, 
                F.COD_CONCEPTO as COD_CPTO_FISCALIZACION,TP.NOMBRE_TIPO  as CONCEPTO ,
                RT.COD_RECEPCIONTITULO AS NO_EXPEDIENTE,PC.Abogado as ABOGADO,
                L.Num_Liquidacion as NUM_LIQUIDACION ,
                T.TIPO_PROCESO  AS PROCESO,
                TZ.COD_TIPOGESTION ,
                TZ.COD_TIPO_RESPUESTA ,
                RG.NOMBRE_GESTION AS RESPUESTA,
                TP.TIPOGESTION,
                L.SALDO_DEUDA,L.SALDO_CAPITAL, 
                L.SALDO_INTERES, F.COD_FISCALIZACION AS COD_EXPEDIENTE_JURIDICA,
                PC.COD_PROCESOPJ,
                RT.COD_ABOGADO AS CODABOGADO,
                E.COD_REGIONAL
                FROM  TRAZAPROCJUDICIAL TZ
                INNER JOIN RESPUESTAGESTION RG ON RG.COD_RESPUESTA=TZ.COD_TIPO_RESPUESTA
                INNER JOIN TIPOGESTION TP ON TP.COD_GESTION = RG.COD_TIPOGESTION
                INNER JOIN TIPOPROCESO T ON  T.COD_TIPO_PROCESO=TP.CODPROCESO
                INNER JOIN PROCESOS_COACTIVOS PC ON PC.COD_PROCESO_COACTIVO =TZ.COD_JURIDICO
                LEFT JOIN RECEPCIONTITULOS RT ON RT.NIT_EMPRESA= PC.IDENTIFICACION
                LEFT JOIN EMPRESA E ON E.CODEMPRESA = RT.NIT_EMPRESA
                INNER JOIN FISCALIZACION F ON F.COD_FISCALIZACION =RT.COD_FISCALIZACION_EMPRESA
                INNER JOIN LIQUIDACION L ON F.COD_FISCALIZACION = L.COD_FISCALIZACION
                INNER JOIN  TIPOCONCEPTO TP ON TP.COD_TIPOCONCEPTO = F.COD_CONCEPTO
                WHERE  TZ.COD_JURIDICO =" . $codigoProceso . " AND TZ.COD_TRAZAPROCJUDICIAL =    (
                SELECT MAX(TZ.COD_TRAZAPROCJUDICIAL) FROM TRAZAPROCJUDICIAL TZ  where TZ.COD_JURIDICO =" . $codigoProceso . ")
                AND L.Bloqueada = '0'";
            $consulta = $this->db->query($SQL);
        } else {
            $SQL = "select IDENTIFICACION, EJECUTADO, REPRESENTANTE, COD_CPTO_FISCALIZACION, CONCEPTO, 
                NO_EXPEDIENTE,CODABOGADO, ABOGADO, COD_REGIONAL, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, 
                CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,COD_EXPEDIENTE_JURIDICA
                from  VW_NO_EXIST_TRAZA
                where NO_EXPEDIENTE =" . $codigoProceso;
            $consulta = $this->db->query($SQL);
        }
        if ($procedencia == '0' and $consulta->num_rows() == 0) {
            $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE,COD_PROCESOPJ, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
            $this->db->from('VW_PROCESOS_COACTIVOS');
            $this->db->where('COD_PROCESO_COACTIVO', $codigoProceso);
            $this->db->where('COD_RESPUESTA', $codigoRespuesta);
            $consulta = $this->db->get();
        } elseif($procedencia == '1' and $consulta->num_rows() == 0) {
            $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE,CODABOGADO, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
            $this->db->from('VW_RECEP_TITULO_LIQ');
            $this->db->where('NO_EXPEDIENTE', $codigoProceso);
            $this->db->where('COD_TIPO_RESPUESTA', $codigoRespuesta);
            $consulta = $this->db->get();

            if ($consulta->num_rows() == 0) {
                $this->db->select('IDENTIFICACION, EJECUTADO, REPRESENTANTE, COD_CPTO_FISCALIZACION, CONCEPTO, NO_EXPEDIENTE,CODABOGADO, ABOGADO, NUM_LIQUIDACION, PROCESO, RESPUESTA, DIRECCION, CORREO_ELECTRONICO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,COD_EXPEDIENTE_JURIDICA, COD_REGIONAL');
                $this->db->from('VW_NO_EXIST_TRAZA');
                $this->db->where('NO_EXPEDIENTE', $codigoProceso);
                $this->db->where('COD_TIPO_RESPUESTA', $codigoRespuesta);
                $consulta = $this->db->get();
            }
        }
        if ($consulta):
            $tmp = NULL;
            foreach ($consulta->result_array() as $fiscalizacion):
                $tmp[] = $fiscalizacion;
            endforeach;
            $proceso = $tmp;
        else:
            $proceso = FALSE;
        endif;
        return $proceso;
    }

    function getInfoUsuario($usuario) {
        /**
         * Función que retorna el nombre del abogado del proceso.
         * Esta función no debe retornar error, pues no se pueden logear usuarios no identificados en la DB
         *
         * @param integer $usuario
         * @return string $nombre
         */
        $this->db->select("NOMBRES || ' ' || APELLIDOS AS ABOGADO, IDUSUARIO");
        $this->db->from('USUARIOS');
        $this->db->where('IDUSUARIO', $usuario);
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $nombre = $resultado->row_array();
            return $nombre;
        endif;
    }

    function getresolucion($nit) {
        $this->db->select_max("COD_RESOLUCION");
        $this->db->from('RESOLUCION');
        $this->db->where('NITEMPRESA', $nit);
        $dato = $this->db->get();
        // echo $this->db->last_query();exit;
        return $dato;
    }

    function getresolucionporproeso($cod_proceso) {
        $this->db->select("COD_RESOLUCION");
        $this->db->from('REASIGNACIONABOGADO');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_proceso);
        $dato = $this->db->get();

          if ($dato->num_rows() == 0) {
            $this->db->select("COD_RESOLUCION");
            $this->db->from('REASIGNACIONABOGADO');
            $this->db->where('COD_RECEPCIONTITULO', $cod_proceso);
            $dato = $this->db->get();
        }
         //echo $this->db->last_query();exit;
        return $dato;
    }  

    function getresolucionporcodfiscalizacion($cod_fiscalizacion) {
        $this->db->select("COD_RESOLUCION");
        $this->db->from('RESOLUCION');
        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $dato = $this->db->get();
         //echo $this->db->last_query();exit;
        return $dato;
    }

    function getReasignacion($resolucion) {
        $this->db->select("COD_REASIGNACION");
        $this->db->from('REASIGNACIONABOGADO');
        $this->db->where('COD_RESOLUCION', $resolucion);
        $dato = $this->db->get();
        return $dato;
    }

    function getNumeroResolucion($codRes) {
        $this->db->select('NUMERO_RESOLUCION');
        $this->db->where('COD_RESOLUCION', $codRes);
        $query = $this->db->get('RESOLUCION');
        return $query->result_array();
    }

    function existeReasignacion($nit) {
        $this->db->select("RA.ESTADO , RE.COD_RESOLUCION ");
        $this->db->from('RESOLUCION RE');
        $this->db->join('REASIGNACIONABOGADO RA', 'RE.COD_RESOLUCION = RA.COD_RESOLUCION', 'inner');
        $this->db->where('RE.NITEMPRESA', $nit);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAsignacion($cod_regional) {

        $this->db->select(" L.COD_FISCALIZACION, 
        PRO.COD_PROCESOPJ , REC.COD_FISCALIZACION_EMPRESA,
        RA.COD_PROCESO_COACTIVO, RA.COD_RECEPCIONTITULO,
        PRO.COD_PROCESO_COACTIVO,REC.COD_RECEPCIONTITULO,
        REC.COD_TIPORESPUESTA, PRO.COD_RESPUESTA, 
                                                    E.CODEMPRESA AS COD_EMPRESA,
                                                    E.RAZON_SOCIAL,L.COD_RESOLUCION,
                                                    R.NOMBRE_REGIONAL,
                                                    RA.FECHA_REASIGNACION,
                                                    RA.COD_REASIGNACION, 
                                                    (U.NOMBRES ||' '|| U.APELLIDOS) AS COD_ABOGADO_ACTUAL
                                                    ");
        $this->db->from('EMPRESA E');
        $this->db->join('RESOLUCION L', 'L.NITEMPRESA = E.CODEMPRESA');
        $this->db->join('FISCALIZACION F', 'F.COD_FISCALIZACION = L.COD_FISCALIZACION');
        $this->db->join('ASIGNACIONFISCALIZACION AF', 'AF.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC');
        $this->db->join('REASIGNACIONABOGADO RA', 'RA.COD_RESOLUCION = L.COD_RESOLUCION');
        $this->db->join('RECEPCIONTITULOS REC', 'REC.COD_RECEPCIONTITULO = RA.COD_RECEPCIONTITULO', 'left');
        $this->db->join('PROCESOS_COACTIVOS PRO', 'PRO.COD_PROCESO_COACTIVO = RA.COD_PROCESO_COACTIVO', 'left');
        $this->db->join('REGIONAL R', 'R.COD_REGIONAL = RA.REGIONAL_ORIGEN', 'left');
        $this->db->join('USUARIOS U', 'U.IDUSUARIO = RA.COD_ABOGADO_ACTUAL', 'left');
        $this->db->where('RA.ESTADO = 1587');
        $this->db->where('RA.REGIONAL_ASIGNA_ABOGADO', $cod_regional);
        $this->db->where('RA.USUARIO_SOLICITA IS NOT NULL');
        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result_array();
    }

    function aprobarRechazos($cod_regional) {

        $this->db->select(" L.COD_FISCALIZACION, 
        PRO.COD_PROCESOPJ , REC.COD_FISCALIZACION_EMPRESA,
        RA.COD_PROCESO_COACTIVO, RA.COD_RECEPCIONTITULO,
        PRO.COD_PROCESO_COACTIVO,REC.COD_RECEPCIONTITULO,
        REC.COD_TIPORESPUESTA, PRO.COD_RESPUESTA, 
                                                    E.CODEMPRESA AS COD_EMPRESA,
                                                    E.RAZON_SOCIAL,L.COD_RESOLUCION,
                                                    R.NOMBRE_REGIONAL,
                                                    RA.FECHA_REASIGNACION,
                                                    RA.REGIONAL_ASIGNA_ABOGADO,
                                                    RA.COD_REASIGNACION, 
                                                    (U.NOMBRES ||' '|| U.APELLIDOS) AS COD_ABOGADO_ACTUAL
                                                    ");
        $this->db->from('EMPRESA E');
        $this->db->join('RESOLUCION L', 'L.NITEMPRESA = E.CODEMPRESA');
        $this->db->join('FISCALIZACION F', 'F.COD_FISCALIZACION = L.COD_FISCALIZACION');
        $this->db->join('ASIGNACIONFISCALIZACION AF', 'AF.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC');
        $this->db->join('REASIGNACIONABOGADO RA', 'RA.COD_RESOLUCION = L.COD_RESOLUCION');
        $this->db->join('RECEPCIONTITULOS REC', 'REC.COD_RECEPCIONTITULO = RA.COD_RECEPCIONTITULO', 'left');
        $this->db->join('PROCESOS_COACTIVOS PRO', 'PRO.COD_PROCESO_COACTIVO = RA.COD_PROCESO_COACTIVO', 'left');
        $this->db->join('REGIONAL R', 'R.COD_REGIONAL = RA.REGIONAL_ASIGNA_ABOGADO', 'left');
        $this->db->join('USUARIOS U', 'U.IDUSUARIO = RA.COD_ABOGADO_ACTUAL', 'left');
        $this->db->where('RA.ESTADO = 1630');
        $this->db->where('RA.REGIONAL_ORIGEN', $cod_regional);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        return $query->result_array();
    }

    function getdetalleReasignacion($cod_reasignacion) {
        $this->db->select("COMENTARIOS");
        $this->db->from('REASIGNACIONABOGADO');
        $this->db->where('COD_REASIGNACION', $cod_reasignacion);
        $dato = $this->db->get();
        return $dato;
    }

    function getAsignacionPendiente($cod_regional) {

        $this->db->select(" L.COD_FISCALIZACION, 
                PRO.COD_PROCESOPJ , REC.COD_FISCALIZACION_EMPRESA,
                RA.COD_PROCESO_COACTIVO, RA.COD_RECEPCIONTITULO,
                PRO.COD_PROCESO_COACTIVO,REC.COD_RECEPCIONTITULO,
                REC.COD_TIPORESPUESTA, PRO.COD_RESPUESTA, 
                                                            E.CODEMPRESA AS COD_EMPRESA,
                                                            E.RAZON_SOCIAL,L.COD_RESOLUCION,
                                                            R.NOMBRE_REGIONAL,
                                                            RA.FECHA_REASIGNACION,
                                                            RA.COD_REASIGNACION, 
                                                            (U.NOMBRES ||' '|| U.APELLIDOS) AS COD_ABOGADO_ACTUAL
                                                            ");
        $this->db->from('EMPRESA E');
        $this->db->join('RESOLUCION L', 'L.NITEMPRESA = E.CODEMPRESA');
        $this->db->join('FISCALIZACION F', 'F.COD_FISCALIZACION = L.COD_FISCALIZACION');
        $this->db->join('ASIGNACIONFISCALIZACION AF', 'AF.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC');
        $this->db->join('REASIGNACIONABOGADO RA', 'RA.COD_RESOLUCION = L.COD_RESOLUCION');
        $this->db->join('RECEPCIONTITULOS REC', 'REC.COD_RECEPCIONTITULO = RA.COD_RECEPCIONTITULO', 'left');
        $this->db->join('PROCESOS_COACTIVOS PRO', 'PRO.COD_PROCESO_COACTIVO = RA.COD_PROCESO_COACTIVO', 'left');
        $this->db->join('REGIONAL R', 'R.COD_REGIONAL = RA.REGIONAL_ORIGEN', 'left');
        $this->db->join('USUARIOS U', 'U.IDUSUARIO = RA.COD_ABOGADO_ACTUAL', 'left');
        $this->db->where('RA.ESTADO = 1629');
        $this->db->where('RA.REGIONAL_ASIGNA_ABOGADO', $cod_regional);
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        return $query->result_array();
    }

    function eliminar_gestion($cod_coactivo) {
        $this->db->select('TP.COD_TRAZAPROCJUDICIAL');
        $this->db->from('TRAZAPROCJUDICIAL TP');
        $this->db->where('TP.COD_JURIDICO', $cod_coactivo);
        $this->db->where('TP.COD_JURIDICO', 1132);
        $this->db->order_by("TP.COD_TRAZAPROCJUDICIAL", "DESC");
        $query = $this->db->get();
        //echo $this->db->last_query();
        $estado = $query->result_array();
        //echo"<pre>";print_r($estado[0]['COD_TRAZAPROCJUDICIAL']);echo"</pre>";
        if (!empty($estado)) {
            return $estado[0]['COD_TRAZAPROCJUDICIAL'];
        } else {
            return $estado = 0;
        }        
    }
    
//FUNCION PARA VERIFICAR EL ESTADO ACTUAL DEL PROCESO RESPECTO A TRASLADO
    function verificarTrasladado($cod_coactivo) {
        $this->db->select('TRASLADADO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $tras = $query->result_array();
        if ($query->num_rows() > 0) {
            return $tras[0]['TRASLADADO'];
        }
    }




    function getDepuracionCarteraCoactivo($idUsuario) {

        $str_query ="SELECT dc.COD_FISCALIZACION AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,rc.NIT_EMPRESA 
        from depuracion_contable dc
        inner join recepciontitulos rc on  dc.COD_FISCALIZACION= rc.COD_FISCALIZACION_EMPRESA
        where  dc.MOSTRARNOTIFICACION is null and rc.COD_ABOGADO='$idUsuario' and  COD_TIPORESPUESTA!=1367
        UNION (
        select CAST (dc.COD_NOMISIONAL AS INT) AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,rc.NIT_EMPRESA 
        from depuracion_contable dc
        inner join recepciontitulos rc on  dc.COD_NOMISIONAL= rc.COD_CARTERA_NOMISIONAL
        where dc.ESTADO = 1 and  dc.MOSTRARNOTIFICACION is null and rc.COD_ABOGADO='$idUsuario' and  COD_TIPORESPUESTA!=1367
        )";
        $query = $this->db->query($str_query);
      //   echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }
}

?>