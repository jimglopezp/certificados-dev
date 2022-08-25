<?php

/*
 * 
 * Desarrollador				:	Luis Arcos
 * Fecha actualización  			:       07/06/2018	
 * Requerimientos				:	Como usuario final deseo generar diferentes acciones y comunicaciones cuando exista una demanda de nulidad y restablecimiento del derecho contra el tÃ­tulo ejecutivo
 *                                              :       Como usuario final deseo generar el proceso Cuando exista una demanda de nulidad y restablecimiento del derecho contra un tÃ­tulo ejecutivo mÃ¡s la orden de suspensiÃ³n provisional de dicho tÃ­tulo
 * PBI No.                                      :	061, 062, 063
 * TASK                                         :       15835, 15843, 15849  
 *
 */

class Demanda_nulidad_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

//FUNCION PARA: (1)-DECLARAR DEMANDA DE NULIDAD
    function declararDemanda($table, $datapc) {
        if ($datapc["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('DEM_NULIDAD', $datapc['DEM_NULIDAD']);
            if (isset($datapc['AJUSTE_LIQUIDACION'])) {
                $this->db->set("AJUSTE_LIQUIDACION", $datapc['AJUSTE_LIQUIDACION']);
            }
            if (isset($datapc['FECHA_NULIDAD'])) {
                $this->db->set("FECHA_NULIDAD", "to_date('" . $datapc['FECHA_NULIDAD'] . "','dd/mm/yyyy HH24:MI:SS')", false);
            }
            $this->db->set('RESPUESTA_FALLO', $datapc['RESPUESTA_FALLO']);
            if (isset($datapc['ALERTA_CONTENCIOSO'])) {
                $this->db->set("ALERTA_CONTENCIOSO", $datapc['ALERTA_CONTENCIOSO']);
            }
            $this->db->where('COD_PROCESO_COACTIVO', $datapc['COD_PROCESO_COACTIVO']);
            $this->db->update($table);
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL TIPO DEMANDA NULIDAD DE UN PROCESO COACTIVO
    public function obtenerDemanda($codCoa) {
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

//FUNCION PARA OBTENER EL NIT SEGUN CODIGO COACTIVO
    public function obtenerNitCoactivo($codCoa) {
        $this->db->select('IDENTIFICACION');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $nit = $query->result_array();
        return $nit[0]['IDENTIFICACION'];
    }

//FUNCION PARA OBTENER DATOS DE LA EMPRESA  
    function getEmpresa($nit) {
        $this->db->select("NOMBRE_EMPRESA, CODEMPRESA, REPRESENTANTE_LEGAL, TELEFONO_FIJO, ACTIVO, DIRECCION");
        $this->db->where("CODEMPRESA", $nit);
        $dato = $this->db->get("EMPRESA");
        return $dato->result_array;
    }

//FUNCION PARA OBTENER LA ULTIMA GESTION EN TRAZA
    function getGestion($codCoactivo) {
        /* $query = "SELECT MAX(COD_TIPO_RESPUESTA) AS COD_TIPO_RESPUESTA FROM TRAZAPROCJUDICIAL WHERE
          COD_JURIDICO=" . $codCoactivo; */
        $query = "SELECT COD_TIPO_RESPUESTA FROM TRAZAPROCJUDICIAL WHERE COD_TRAZAPROCJUDICIAL = (SELECT MAX(COD_TRAZAPROCJUDICIAL) FROM TRAZAPROCJUDICIAL WHERE
        COD_JURIDICO=" . $codCoactivo . ")";
        $resultado = $this->db->query($query);
        $resultado = $resultado->result_array;
        return $resultado;
    }

//FUNCION PARA OBTENER EL NOMBRE DE LA ULTIMA GESTION REALIZADA
    function getRespuesta($idGestion) {
        $array = array();
        $this->db->select('NOMBRE_GESTION,COD_TIPOGESTION,COD_RESPUESTA');
        $this->db->from("RESPUESTAGESTION");
        $this->db->where('COD_RESPUESTA', $idGestion);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $array = $query->result();
            return $array[0];
        }
    }

//FUNCION PARA GUARDAR EL AUTO  
    function guardarAuto($table, $data) {
        $this->db->trans_start();
        $this->db->set("COD_TIPO_AUTO", $data['COD_TIPO_AUTO']);
        $this->db->set("COD_PROCESO_COACTIVO", $data['COD_PROCESO_COACTIVO']);
        $this->db->set("COD_ESTADOAUTO", $data['COD_ESTADOAUTO']);
        if (isset($data['COD_TIPO_PROCESO'])) {
            $this->db->set("COD_TIPO_PROCESO", $data['COD_TIPO_PROCESO']);
        }
        $this->db->set("CREADO_POR", $data['CREADO_POR']);
        $this->db->set("ASIGNADO_A", $data['ASIGNADO_A']);
        $this->db->set('FECHA_GESTION', 'SYSDATE', FALSE);
        $this->db->set('FECHA_CREACION_AUTO', 'SYSDATE', FALSE);
        $this->db->set("COD_GESTIONCOBRO", $data['COD_GESTIONCOBRO']);
        $this->db->set("COMENTARIOS", $data['COMENTARIOS']);
        $this->db->set("NOMBRE_DOC_GENERADO", $data['NOMBRE_DOC_GENERADO']);
        $query = $this->db->insert($table);
        if ($this->db->affected_rows() == '1') {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA ACTUALIZAR LA RESPUESTA GESTION DEL REMATE
    function actualizarRespuestaRem($datamcr) {
        if ($datamcr["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('COD_RESPUESTA', $datamcr['COD_RESPUESTA']);
            $this->db->where('MC_REMATE.COD_PROCESO_COACTIVO', $datamcr['COD_PROCESO_COACTIVO']);
            $this->db->update('MC_REMATE');
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL NUMERO IDENTIFICADOR DEL AUTO GENERADO
    function getNumAuto($tpAuto, $codCoa) {
        $this->db->select_max('NUM_AUTOGENERADO');
        $this->db->from("AUTOSJURIDICOS");
        $this->db->where('COD_TIPO_AUTO', $tpAuto);
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $num = $query->result_array();
        if ($query->num_rows() > 0) {
            return $num[0]['NUM_AUTOGENERADO'];
        }
    }

//FUNCION PARA OBTENER DATOS DEL AUTO GENERADO
    function getAuto($auto) {
        $this->db->select('NUM_AUTOGENERADO,COD_FISCALIZACION,FECHA_CREACION_AUTO,CREADO_POR,COMENTARIOS,REVISADO,REVISADO_POR,
        APROBADO,APROBADO_POR,ASIGNADO_A,NOMBRE_DOC_GENERADO,COD_GESTIONCOBRO,COD_ESTADOAUTO,NOMBRE_DOC_FIRMADO,COD_TIPO_AUTO');
        $this->db->from("AUTOSJURIDICOS");
        $this->db->where('NUM_AUTOGENERADO', $auto);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $array = $query->result();
            return $array[0];
        }
    }

//FUNCION PARA ACTUALIZAR INFO DEL AUTO
    function actualizarAuto($table, $data, $num_auto) {
        $this->db->trans_start();
        $this->db->where('NUM_AUTOGENERADO', $num_auto);
        $this->db->set("COD_ESTADOAUTO", $data['COD_ESTADOAUTO']);
        $this->db->set("FECHA_GESTION", 'SYSDATE', FALSE);
        if (isset($data['ASIGNADO_A']) AND $data['ASIGNADO_A'] != '') {
            $this->db->set("ASIGNADO_A", $data['ASIGNADO_A']);
        }
        if (isset($data['REVISADO']) AND $data['REVISADO'] != '') {
            $this->db->set("REVISADO", $data['REVISADO']);
            $this->db->set("REVISADO_POR", $data['REVISADO_POR']);
        }
        if (isset($data['APROBADO']) AND $data['APROBADO'] != '') {
            $this->db->set("APROBADO", $data['APROBADO']);
            $this->db->set("APROBADO_POR", $data['APROBADO_POR']);
        }
        if (isset($data['NOMBRE_DOC_FIRMADO']) AND $data['NOMBRE_DOC_FIRMADO'] != '') {
            $this->db->set("NOMBRE_DOC_FIRMADO", $data['NOMBRE_DOC_FIRMADO']);
            $this->db->set("FECHA_DOC_FIRMADO", 'SYSDATE', FALSE);
        }
        $this->db->set("COMENTARIOS", $data['COMENTARIOS']);
        if (isset($data['NOMBRE_DOC_GENERADO']) AND $data['NOMBRE_DOC_GENERADO'] != '') {
            $this->db->set("NOMBRE_DOC_GENERADO", $data['NOMBRE_DOC_GENERADO']);
        }
        $this->db->set("COD_GESTIONCOBRO", $data['COD_GESTIONCOBRO']);

        $this->db->update($table);
        //print_r($this->db->last_query());die();
        if ($this->db->affected_rows() == '1') {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL CODIGO DE LA MEDIDA CAUTELAR
    public function obtenerMedCautelar($codCoa, $tpResp = null) {
        $this->db->select_max('COD_MEDIDACAUTELAR');
        $this->db->from('MC_MEDIDASCAUTELARES');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        if (isset($tpResp) AND ! empty($tpResp)) {
            $this->db->where('COD_RESPUESTAGESTION', $tpResp);
        }
        $query = $this->db->get();
        $cautelar = $query->result_array();
        if (!empty($cautelar)) {
            return $cautelar[0]['COD_MEDIDACAUTELAR'];
        } else {
            return $cautelar = 0;
        }
    }

//FUNCION PARA ACTUALIZAR LA RESPUESTA GESTION DE LA MEDIDA CAUTELAR
    function actualizarRespuestaMc($datamc) {
        if ($datamc["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('COD_RESPUESTAGESTION', $datamc['COD_RESPUESTAGESTION']);
            if (!empty($datamc['COD_MEDIDACAUTELAR'])) {
                $this->db->where('MC_MEDIDASCAUTELARES.COD_MEDIDACAUTELAR', $datamc['COD_MEDIDACAUTELAR']);
            }
            $this->db->where('MC_MEDIDASCAUTELARES.COD_PROCESO_COACTIVO', $datamc['COD_PROCESO_COACTIVO']);
            $this->db->update('MC_MEDIDASCAUTELARES');
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA GUARDAR LA AUTORIZACION DE LA APLICACION DE TITULOS
    function agregarAutorizacionAt($table, $dataaat) {
        $this->db->trans_start();
        $this->db->set("FECHA_RAD_ONBASE", "to_date('" . $dataaat['FECHA_RAD_ONBASE'] . "','dd/mm/yyyy HH24:MI:SS')", false);
        $this->db->set("NUMERO_RAD_ONBASE", $dataaat['NUMERO_RAD_ONBASE']);
        $this->db->set("NOMBRE_DOC_SOLICITUD", $dataaat['NOMBRE_DOC_SOLICITUD']);
        $this->db->set("RUTA_DOC_SOLICITUD", $dataaat['RUTA_DOC_SOLICITUD']);
        $this->db->set("COD_COACTIVO", $dataaat['COD_COACTIVO']);
        $query = $this->db->insert($table);
        if ($this->db->affected_rows() > 0) {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA VERIFICAR QUE UNA GESTION YA SE REALIZO
    function realizoTraza($cod_juridico, $tp_respuesta) {
        $this->db->select('COD_TIPOGESTION');
        $this->db->from('TRAZAPROCJUDICIAL');
        $this->db->where('COD_JURIDICO', $cod_juridico);
        $this->db->where('COD_TIPO_RESPUESTA', $tp_respuesta);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA RETIRAR DEMANDA
    function retirarDemanda($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('DEM_NULIDAD', 0);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA VERIFICAR QUE UN PROCESO SE ENCUENTRA EN REMATE
    function verificarRemate($cod_coactivo) {
        $this->db->select('COD_REMATE');
        $this->db->from('MC_REMATE');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
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

//FUNCION PARA VERIFICAR QUE LA DILIGENCIA DEL REMATE SE ENCUENTRA SUSPENDIDA
    function verificarSuspRemate($cod_coactivo) {
        $this->db->select('COD_REMATE');
        $this->db->from('MC_REMATE');
        $this->db->where('COD_RESPUESTA', 1744);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA SOLICITAR EL AJUSTE DE LIQUIDACION
    function solicAjusteLiquidacion($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('AJUSTE_LIQUIDACION', 1);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA SOLICITAR EL AJUSTE DE LIQUIDACION (TABLA LIQUIDACION)
    function solicAjusteLiquidacionOn($cod_fiscalizacion) {
        $this->db->trans_begin();
        $this->db->set('AJUSTE_LIQUIDACION', 3);
        $this->db->where('LIQUIDACION.COD_FISCALIZACION', $cod_fiscalizacion);
        $this->db->update('LIQUIDACION');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA SOLICITAR EL AJUSTE DE LIQUIDACION (TABLA CNM_CARTERANOMISIONAL)
    function solicAjusteCnmOn($cod_cartera) {
        $this->db->trans_begin();
        $this->db->set('AJUSTE_LIQUIDACION', 3);
        $this->db->where('CNM_CARTERANOMISIONAL.COD_CARTERA_NOMISIONAL', $cod_cartera);
        $this->db->update('CNM_CARTERANOMISIONAL');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL ESTADO EN AJUSTE DE LIQUIDACION
    public function estadoAjusteLiq($codCoa) {
        $this->db->select('AJUSTE_LIQUIDACION');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $estado = $query->result_array();
        if (!empty($estado)) {
            return $estado[0]['AJUSTE_LIQUIDACION'];
        } else {
            return $estado = 0;
        }
    }

//FUNCION PARA OBTENER LOS PROCESOS COACTIVOS EN ESPERA DE AJUSTE LIQUIDACION
    public function solicitudesAjusteLiq() {
        $this->db->select('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, ACUMULACION_COACTIVA.COD_RECEPCIONTITULO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->where('PROCESOS_COACTIVOS.AJUSTE_LIQUIDACION', 1);
        $query = $this->db->get();
        $procesos = $query->result_array();
        return $procesos;
    }

//FUNCION PARA OBTENER CODIGO DE CARTERA NO MISIONAL (SI EXISTE)
    public function obtenerCodCnm($cod_recepcion) {
        $this->db->select('COD_CARTERA_NOMISIONAL');
        $this->db->from('RECEPCIONTITULOS');
        $this->db->where('COD_RECEPCIONTITULO', $cod_recepcion);
        $query = $this->db->get();
        $recepcion = $query->result_array();
        return $recepcion[0]['COD_CARTERA_NOMISIONAL'];
    }

//FUNCION PARA OBTENER LA IDENTIFICACION EN CNM_CARTERANOMISIONAL
    public function obtenerIdentificacionCnm($cod_cnm) {
        $this->db->select('COD_EMPLEADO, COD_EMPRESA');
        $this->db->from('CNM_CARTERANOMISIONAL');
        $this->db->where('COD_CARTERA_NOMISIONAL', $cod_cnm);
        $query = $this->db->get();
        $identificacion = $query->result_array();
        return $identificacion[0];
    }

//FUNCION PARA OBTENER LA IDENTIFICACION EN PROCESOS_COACTIVOS
    public function obtenerIdentificacionPc($cod_coactivo) {
        $this->db->select('IDENTIFICACION');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $identificacion = $query->result_array();
        return $identificacion[0]['IDENTIFICACION'];
    }

//FUNCION PARA OBTENER INFO DEL EMPLEADO (CNM_EMPLEADO)
    public function infoCnmEmpleado($identificacion) {
        $this->db->select('NOMBRES, APELLIDOS, COD_REGIONAL');
        $this->db->from('CNM_EMPLEADO');
        $this->db->where('IDENTIFICACION', $identificacion);
        $query = $this->db->get();
        $info = $query->result_array();
        return $info[0];
    }

//FUNCION PARA OBTENER INFO DE LA EMPRESA (CNM_EMPRESA)
    public function infoCnmEmpresa($identificacion) {
        $this->db->select('RAZON_SOCIAL, COD_REGIONAL');
        $this->db->from('CNM_EMPRESA');
        $this->db->where('COD_ENTIDAD', $identificacion);
        $query = $this->db->get();
        $info = $query->result_array();
        return $info[0];
    }

//FUNCION PARA OBTENER DE LA EMPRESA (EMPRESA)
    public function infoEmpresa($identificacion) {
        $this->db->select('RAZON_SOCIAL, COD_REGIONAL');
        $this->db->from('EMPRESA');
        $this->db->where('CODEMPRESA', $identificacion);
        $query = $this->db->get();
        $info = $query->result_array();
        return $info[0];
    }

//FUNCION PARA OBTENER EL NOMBRE DEL EMPLEADO (CNM_EMPLEADO)
    public function nombreRegionales($cod_regional) {
        $this->db->select('NOMBRE_REGIONAL');
        $this->db->from('REGIONAL');
        $this->db->where('COD_REGIONAL', $cod_regional);
        $query = $this->db->get();
        $regional = $query->result_array();
        return $regional[0]['NOMBRE_REGIONAL'];
    }

//FUNCION PARA OBTENER SALDO DEUDA (CNM_CARTERANOMISIONAL)
    public function saldoDeudaCnm($cod_cartera) {
        $this->db->select('SALDO_DEUDA');
        $this->db->from('CNM_CARTERANOMISIONAL');
        $this->db->where('COD_CARTERA_NOMISIONAL', $cod_cartera);
        $query = $this->db->get();
        $saldo = $query->result_array();
        return $saldo[0]['SALDO_DEUDA'];
    }

//FUNCION PARA OBTENER CODIGO DE FISCALIZACION ASOCIADO A LA RECEPCION DE TITULOS
    public function codFiscalizacionRt($cod_recepcion) {
        $this->db->select('COD_FISCALIZACION_EMPRESA');
        $this->db->from('RECEPCIONTITULOS');
        $this->db->where('COD_RECEPCIONTITULO', $cod_recepcion);
        $query = $this->db->get();
        $fiscalizacion = $query->result_array();
        return $fiscalizacion[0]['COD_FISCALIZACION_EMPRESA'];
    }

//FUNCION PARA OBTENER CODIGO DE CARTERA NO MISIONAL ASOCIADA A LA RECEPCION DE TITULOS
    public function codCarteraNm($cod_recepcion) {
        $this->db->select('COD_CARTERA_NOMISIONAL');
        $this->db->from('RECEPCIONTITULOS');
        $this->db->where('COD_RECEPCIONTITULO', $cod_recepcion);
        $query = $this->db->get();
        $cartera = $query->result_array();
        return $cartera[0]['COD_CARTERA_NOMISIONAL'];
    }

//FUNCION PARA OBTENER INFO DE LA LIQUIDACION
    public function infoLiquidacion($fiscalizacion = null, $num_liquidacion = null) {
        $this->db->select('NITEMPRESA, NUM_LIQUIDACION, COD_FISCALIZACION, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, AJUSTE_LIQUIDACION');
        $this->db->from('LIQUIDACION');
        if (!empty($fiscalizacion)) {
            $this->db->where('COD_FISCALIZACION', $fiscalizacion);
        } else if (!empty($num_liquidacion)) {
            $this->db->where('NUM_LIQUIDACION', $num_liquidacion);
        }
        $query = $this->db->get();
        $info = $query->result_array();
        return $info[0];
    }

//FUNCION PARA OBTENER CODIGOS DE RECEPCION TITULOS EN ACUMULACION COACTIVA
    public function obtenerCodigosRt($cod_coactivo) {
        $this->db->select('COD_RECEPCIONTITULO');
        $this->db->from('ACUMULACION_COACTIVA');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $cods_recepcion = $query->result_array();
        return $cods_recepcion;
    }

//FUNCION PARA ACTUALIZAR LOS SALDOS DE LA LIQUIDACION
    function actualizarSaldosLiq($dataliq) {
        if ($dataliq["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('SALDO_CAPITAL', $dataliq['SALDO_CAPITAL']);
            $this->db->set('SALDO_INTERES', $dataliq['SALDO_INTERES']);
            $this->db->set('SALDO_DEUDA', $dataliq['SALDO_DEUDA']);
            $this->db->set('AJUSTE_LIQUIDACION', $dataliq['AJUSTE_LIQUIDACION']);
            $this->db->where('LIQUIDACION.NUM_LIQUIDACION', $dataliq['NUM_LIQUIDACION']);
            $this->db->update('LIQUIDACION');
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA ACTUALIZAR EL SALDO DE LA CARTERA NO MISIONAL
    function actualizarSaldosCnm($datacnm) {
        if ($datacnm["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('SALDO_DEUDA', $datacnm['SALDO_DEUDA']);
            $this->db->set('AJUSTE_LIQUIDACION', $datacnm['AJUSTE_LIQUIDACION']);
            $this->db->where('COD_CARTERA_NOMISIONAL', $datacnm['COD_CARTERA_NOMISIONAL']);
            $this->db->update('CNM_CARTERANOMISIONAL');
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL ESTADO DEL AJUSTE EN TABLA LIQUIDACION
    public function verificarAjusteLiq($cod_fiscalizacion) {
        $this->db->select('AJUSTE_LIQUIDACION');
        $this->db->from('LIQUIDACION');
        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $query = $this->db->get();
        $estado = $query->result_array();
        return $estado[0]["AJUSTE_LIQUIDACION"];
    }

//FUNCION PARA OBTENER EL ESTADO DEL AJUSTE EN TABLA CNM_CARTERA_NOMISIONAL
    public function verificarAjusteCmm($cod_cartera) {
        $this->db->select('AJUSTE_LIQUIDACION');
        $this->db->from('CNM_CARTERANOMISIONAL');
        $this->db->where('COD_CARTERA_NOMISIONAL', $cod_cartera);
        $query = $this->db->get();
        $estado = $query->result_array();
        return $estado[0]["AJUSTE_LIQUIDACION"];
    }

//FUNCION PARA FINALIZAR EL AJUSTE DE LIQUIDACION (TABLA PROCESOS_COACTIVOS)
    public function finalizarAjustePc($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('AJUSTE_LIQUIDACION', 2);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER SALDO DEUDA (CNM_CARTERANOMISIONAL)
    public function infoCnm($cod_cartera) {
        $this->db->select('COD_CARTERA_NOMISIONAL, COD_EMPLEADO, COD_EMPRESA, SALDO_DEUDA, AJUSTE_LIQUIDACION');
        $this->db->from('CNM_CARTERANOMISIONAL');
        $this->db->where('COD_CARTERA_NOMISIONAL', $cod_cartera);
        $query = $this->db->get();
        $datos = $query->result_array();
        return $datos[0];
    }

//FUNCION PARA OBTENER PROCESOS COACTIVOS EN PROCESO CONTENCIOSO SIN SUSPENSION (ALERTA CADA 3 MESES)
    public function procesosEnDemanda($id_abogado) {
        $this->db->select('RECEPCIONTITULOS.COD_RECEPCIONTITULO, PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, PROCESOS_COACTIVOS.COD_PROCESOPJ, PROCESOS_COACTIVOS.FECHA_NULIDAD, 
                          PROCESOS_COACTIVOS.RESPUESTA_FALLO, RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA, RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.ABOGADO', $id_abogado);
        $this->db->where('PROCESOS_COACTIVOS.DEM_NULIDAD', 2);
        $this->db->where('PROCESOS_COACTIVOS.FECHA_NULIDAD IS NOT NULL', NULL, FALSE);
        $this->db->where('PROCESOS_COACTIVOS.RESPUESTA_FALLO', 0);
        $query = $this->db->get();
        $contenciosos = $query->result_array();
        return $contenciosos;
    }

//FUNCION PARA ACTUALIZAR LA RESPUESTA AL FALLO
    public function responderFallo($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('RESPUESTA_FALLO', 1);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA GUARDAR EL OFICIO JURIDICO
    function guardarOficio($table, $data) {
        $this->db->trans_start();
        $this->db->set("COD_COACTIVO", $data['COD_COACTIVO']);
        $this->db->set("CREADO_POR", $data['CREADO_POR']);
        $this->db->set("COMENTARIOS", $data['COMENTARIOS']);
        $this->db->set("NOMBRE_DOC_GENERADO", $data['NOMBRE_DOC_GENERADO']);
        $this->db->set("RUTA_DOC_GENERADO", $data['RUTA_DOC_GENERADO']);
        $this->db->set("COD_GESTIONCOBRO", $data['COD_GESTIONCOBRO']);
        $query = $this->db->insert($table);
        if ($this->db->affected_rows() == '1') {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL NUMERO IDENTIFICADOR DEL OFICIO GENERADO
    function getCodOficio($codCoa) {
        $this->db->select_max('COD_OFICIO');
        $this->db->from("OFICIOS_JURIDICOS");
        $this->db->where('COD_COACTIVO', $codCoa);
        $query = $this->db->get();
        $cod = $query->result_array();
        if ($query->num_rows() > 0) {
            return $cod[0]['COD_OFICIO'];
        }
    }

//FUNCION PARA OBTENER DATOS DEL OFICIO GENERADO
    function getOficio($cod_oficio) {
        $this->db->select('COD_OFICIO, COD_COACTIVO, FECHA_CREACION, CREADO_POR, COMENTARIOS, NOMBRE_DOC_GENERADO, RUTA_DOC_GENERADO,NOMBRE_DOC_GENERADO,
        COD_GESTIONCOBRO');
        $this->db->from("OFICIOS_JURIDICOS");
        $this->db->where('COD_OFICIO', $cod_oficio);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $array = $query->result();
            return $array[0];
        }
    }

//FUNCION PARA ACTUALIZAR INFO DEL OFICIO
    function actualizarOficio($table, $data, $cod_oficio) {
        $this->db->trans_start();
        $this->db->where('COD_OFICIO', $cod_oficio);
        if (isset($data['REVISADO_POR']) AND $data['REVISADO_POR'] != '') {
            $this->db->set("REVISADO_POR", $data['REVISADO_POR']);
        }
        $this->db->set("COMENTARIOS", $data['COMENTARIOS']);
        $this->db->set("NOMBRE_DOC_GENERADO", $data['NOMBRE_DOC_GENERADO']);
        if (isset($data['NOMBRE_DOC_FIRMADO']) AND $data['NOMBRE_DOC_FIRMADO'] != '') {
            $this->db->set("NOMBRE_DOC_FIRMADO", $data['NOMBRE_DOC_FIRMADO']);
        }
        if (isset($data['RUTA_DOC_FIRMADO']) AND $data['RUTA_DOC_FIRMADO'] != '') {
            $this->db->set("RUTA_DOC_FIRMADO", $data['RUTA_DOC_FIRMADO']);
        }
        if (isset($data['FECHA_DOC_FIRMADO']) AND $data['FECHA_DOC_FIRMADO'] != '') {
            $this->db->set("FECHA_DOC_FIRMADO", "to_date('" . $data['FECHA_DOC_FIRMADO'] . "','dd/mm/yyyy HH24:MI:SS')", false);
        }
        $this->db->set("COD_GESTIONCOBRO", $data['COD_GESTIONCOBRO']);

        $this->db->update($table);
        if ($this->db->affected_rows() == '1') {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA VERIFICAR QUE UN PROCESO YA ES COACTIVO
    function verificarCoactivo($cod_proceso) {
        $this->db->select('COD_PROCESO_COACTIVO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_proceso);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA GUARDAR LA COMUNICACION DE SUSPENSION PROVISIONAL
    function agregarComunicacionDm($table, $datasp) {
        $this->db->trans_start();
        $this->db->set("COD_COACTIVO", $datasp['COD_COACTIVO']);
        $this->db->set("REGISTRADA_POR", $datasp['REGISTRADA_POR']);
        $this->db->set("NOMBRE_DOC_RADICADO", $datasp['NOMBRE_DOC_RADICADO']);
        $this->db->set("RUTA_DOC_RADICADO", $datasp['RUTA_DOC_RADICADO']);
        $this->db->set("NUMERO_RAD_ONBASE", $datasp['NUMERO_RAD_ONBASE']);
        $query = $this->db->insert($table);
        if ($this->db->affected_rows() > 0) {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA ACTUALIZAR LA GESTION DEL PROCESO COACTIVO
    public function actualizarCoactivo($datapc) {
        $this->db->trans_begin();
        $this->db->set('COD_RESPUESTA', $datapc['COD_RESPUESTA']);
        $this->db->where('COD_PROCESO_COACTIVO', $datapc['COD_PROCESO_COACTIVO']);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA ACTUALIZAR EL NUMERO DEL AUTO EN TABLA COM_DEMANDAS_NULIDAD
    public function actualizarNumAutoCdm($datasp) {
        $this->db->trans_begin();
        $this->db->set('NUM_AUTO_GENERADO', $datasp['NUM_AUTO_GENERADO']);
        $this->db->where('COD_COACTIVO', $datasp['COD_COACTIVO']);
        $this->db->update('COM_DEMANDAS_NULIDAD');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER PROCESOS COACTIVOS EN PROCESO CONTENCIOSO CON SUSPENSION (ALERTA CADA 3 MESES)
    public function procesosEnDemandaSusp($id_abogado) {
        $this->db->select('RECEPCIONTITULOS.COD_RECEPCIONTITULO, PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, PROCESOS_COACTIVOS.COD_PROCESOPJ, PROCESOS_COACTIVOS.FECHA_NULIDAD, 
                          PROCESOS_COACTIVOS.RESPUESTA_FALLO, RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA, RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL, PROCESOS_COACTIVOS.ALERTA_CONTENCIOSO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.ABOGADO', $id_abogado);
        $this->db->where('PROCESOS_COACTIVOS.DEM_NULIDAD', 1);
        $this->db->where('PROCESOS_COACTIVOS.FECHA_NULIDAD IS NOT NULL', NULL, FALSE);
        $this->db->where('PROCESOS_COACTIVOS.RESPUESTA_FALLO', 0);
        $query = $this->db->get();
        $contenciosos = $query->result_array();
        return $contenciosos;
    }

    /*
      //FUNCION PARA OBTENER EL CODIGO DE COMUNICACION DE LA DEMANDA DE NULIDAD
      function codigoComDemanda($codCoa) {
      $query = "SELECT COD_COMUNICACION, NUMERO_RAD_ONBASE, FECHA_ONBASE FROM COM_DEMANDAS_NULIDAD WHERE COD_COMUNICACION = (SELECT MAX(COD_COMUNICACION) FROM COM_DEMANDAS_NULIDAD WHERE
      COD_COACTIVO = " . $codCoa . ")";
      $resultado = $this->db->query($query);
      $resultado = $resultado->result_array;
      return $resultado[0];
      }
     */

//FUNCION PARA GUARDAR AVISO DE NOTIFICACION POR CORREO DE LA SUSPENSION COACTIVA
    function guardarAviso($data) {
        $this->db->trans_start();
        $this->db->set('COD_AVISONOTIFICACION', $data['COD_AVISONOTIFICACION']);
        $this->db->set('COD_ESTADO', $data['COD_ESTADO']);
        $this->db->set('COD_TIPONOTIFICACION', $data['COD_TIPONOTIFICACION']);
        $this->db->set('OBSERVACIONES', $data['OBSERVACIONES']);
        $this->db->set('FECHA_NOTIFICACION', "to_date('" . $data['FECHA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this->db->set('PLANTILLA', $data['PLANTILLA']);
        $this->db->set('ESTADO_NOTIFICACION', $data['ESTADO_NOTIFICACION']);
        $this->db->set('COD_PROCESO_COACTIVO', $data['COD_PROCESO_COACTIVO']);
        $this->db->insert('AVISONOTIFICACION');
        if ($this->db->affected_rows() > 0) {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER EL ULTIMO CODIGO EN AVISO DE NOTIFICACION
    function ultimoAviso() {
        $this->db->select_max('COD_AVISONOTIFICACION');
        $this->db->from("AVISONOTIFICACION");
        $query = $this->db->get();
        $cod = $query->result_array();
        if ($query->num_rows() > 0) {
            return $cod[0]['COD_AVISONOTIFICACION'];
        }
    }

//FUNCION PARA OBTENER EL ULTIMO CODIGO EN AVISO DE NOTIFICACION POR PROCESO COACTIVO
    function infoNotificacion($cod_coactivo) {
        $query = "SELECT COD_AVISONOTIFICACION, OBSERVACIONES, PLANTILLA, NUM_RADICADO_ONBASE, FECHA_NOTIFICACION FROM AVISONOTIFICACION WHERE COD_AVISONOTIFICACION = (SELECT MAX(COD_AVISONOTIFICACION) FROM AVISONOTIFICACION WHERE
        COD_PROCESO_COACTIVO = " . $cod_coactivo . " AND COD_TIPONOTIFICACION = 2)";
        $resultado = $this->db->query($query);
        $resultado = $resultado->result_array;
        return $resultado[0];
    }

//FUNCION PARA ACTUALIZAR EL AVISO DE NOTIFICACION
    public function actualizarAviso($dataan) {
        $this->db->trans_begin();
        if (isset($dataan['COD_COMUNICACION'])) {
            $this->db->set('COD_COMUNICACION', $dataan['COD_COMUNICACION']);
        }
        if (isset($dataan['NUM_RADICADO_ONBASE'])) {
            $this->db->set('NUM_RADICADO_ONBASE', $dataan['NUM_RADICADO_ONBASE']);
        }
        if (isset($dataan['NOMBRE_DOC_CARGADO'])) {
            $this->db->set('NOMBRE_DOC_CARGADO', $dataan['NOMBRE_DOC_CARGADO']);
        }
        if (isset($dataan['FECHA_NOTIFICACION'])) {
            $this->db->set('FECHA_NOTIFICACION', "to_date('" . $dataan['FECHA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        }
        if (isset($dataan['DOC_COLILLA'])) {
            $this->db->set('DOC_COLILLA', $dataan['DOC_COLILLA']);
        }
        if (isset($dataan['DEVUELTO'])) {
            $this->db->set('DEVUELTO', $dataan['DEVUELTO']);
        }
        if (isset($dataan['COD_MOTIVODEVOLUCION'])) {
            $this->db->set('COD_MOTIVODEVOLUCION', $dataan['COD_MOTIVODEVOLUCION']);
        }
        if (isset($dataan['FECHA_ONBASE'])) {
            $this->db->set('FECHA_ONBASE', "to_date('" . $dataan['FECHA_ONBASE'] . "','dd/mm/yyyy')", false);
        }
        if (isset($dataan['NOMBRE_COL_CARGADO'])) {
            $this->db->set('NOMBRE_COL_CARGADO', $dataan['NOMBRE_COL_CARGADO']);
        }
        if (isset($dataan['DOC_FIRMADO'])) {
            $this->db->set('DOC_FIRMADO', $dataan['DOC_FIRMADO']);
        }
        $this->db->set('OBSERVACIONES', $dataan['OBSERVACIONES']);
        $this->db->set('COD_ESTADO', $dataan['COD_ESTADO']);
        $this->db->set('PLANTILLA', $dataan['PLANTILLA']);
        $this->db->set('FECHA_MODIFICA_NOTIFICACION', "to_date('" . $dataan['FECHA_MODIFICA_NOTIFICACION'] . "','dd/mm/yyyy')", false);
        $this->db->where('COD_PROCESO_COACTIVO', $dataan['COD_PROCESO_COACTIVO']);
        $this->db->where('COD_AVISONOTIFICACION', $dataan['COD_AVISONOTIFICACION']);
        $this->db->update('AVISONOTIFICACION');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA OBTENER PARAMETROS
    function getSelect($table, $fields, $where = '', $order = '') {
        $sql = "SELECT " . $fields . "  FROM " . $table . " ";
        if ($where != '')
            $sql .= "WHERE " . $where . " ";
        if ($order != '')
            $sql .= "ORDER BY " . $order . " ";
        $query = $this->db->query($sql);
        return $query->result();
    }

//FUNCION PARA: (1)- ACTUALIZAR LA FECHA DE NULIDAD (ALERTA CADA 3 O 6 MESES A PARTIR DE LA FECHA)
    function updateFechaNulidad($table, $datapc) {
        if ($datapc["FLAG"] == 1) {
            $this->db->trans_begin();
            if (isset($datapc['SUSPENSION_APTITULOS'])) {
                $this->db->set('SUSPENSION_APTITULOS', $datapc['SUSPENSION_APTITULOS']);
            }
            if (isset($datapc['SUSPENSION_REMATE'])) {
                $this->db->set('SUSPENSION_REMATE', $datapc['SUSPENSION_REMATE']);
            }
            $this->db->set("FECHA_NULIDAD", "to_date('" . $datapc['FECHA_NULIDAD'] . "','dd/mm/yyyy HH24:MI:SS')", false);
            $this->db->where('COD_PROCESO_COACTIVO', $datapc['COD_PROCESO_COACTIVO']);
            $this->db->update($table);
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA LA PROCENDENCIA (MISIONAL O NO MISIONAL)
    function obtenerProcedecia($codCoa) {
        $this->db->select('RECEPCIONTITULOS.NOMISIONAL');
        $this->db->from("PROCESOS_COACTIVOS");
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $proc = $query->result_array();
        if ($query->num_rows() > 0) {
            return $proc[0]['NOMISIONAL'];
        }
    }

//FUNCION PARA OBTENER EL CONCEPTO DE LA FISCALIZACION
    function obtenerCptoFisc($codCoa) {
        $this->db->select('CONCEPTOSFISCALIZACION.NOMBRE_CONCEPTO AS CONCEPTO');
        $this->db->from("PROCESOS_COACTIVOS");
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->join('FISCALIZACION', 'RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA = FISCALIZACION.COD_FISCALIZACION');
        $this->db->join('CONCEPTOSFISCALIZACION', 'FISCALIZACION.COD_CONCEPTO = CONCEPTOSFISCALIZACION.COD_CPTO_FISCALIZACION');
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $cpto = $query->result_array();
        if ($query->num_rows() > 0) {
            return $cpto[0]['CONCEPTO'];
        }
    }

//FUNCION PARA OBTENER EL CONCEPTO DE LA CARTERA NO MISIONAL
    function obtenerCptoCnm($codCoa) {
        $this->db->select('TIPOCARTERA.NOMBRE_CARTERA AS CONCEPTO');
        $this->db->from("PROCESOS_COACTIVOS");
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->join('CNM_CARTERANOMISIONAL', 'RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL = CNM_CARTERANOMISIONAL.COD_CARTERA_NOMISIONAL');
        $this->db->join('TIPOCARTERA', 'CNM_CARTERANOMISIONAL.COD_TIPOCARTERA = TIPOCARTERA.COD_TIPOCARTERA');
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $cpto = $query->result_array();
        if ($query->num_rows() > 0) {
            return $cpto[0]['CONCEPTO'];
        }
    }

//FUNCION PARA GUARDAR LA SOLICITUD DE REVOCATORIA DIRECTA
    function guardarSolRevDirecta($table, $data) {
        $this->db->trans_start();
        $this->db->set("COD_COACTIVO", $data['COD_COACTIVO']);
        $this->db->set("REGISTRADA_POR", $data['REGISTRADA_POR']);
        $this->db->set("NOMBRE_DOC_SOPORTE", $data['NOMBRE_DOC_SOPORTE']);
        $this->db->set("RUTA_DOC_SOPORTE", $data['RUTA_DOC_SOPORTE']);
        $this->db->set("NUMERO_RAD_ONBASE", $data['NUMERO_RAD_ONBASE']);
        $this->db->set("FECHA_ONBASE", "to_date('" . $data['FECHA_ONBASE'] . "','dd/mm/yyyy HH24:MI:SS')", false);
        $this->db->set("COD_ESTADO", $data['COD_ESTADO']);
        $query = $this->db->insert($table);
        if ($this->db->affected_rows() == '1') {
            $this->db->trans_complete();
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA VALIDAR SI EL FALLO FUE RESPONDIDO
    public function obtenerResFallo($codCoa) {
        $this->db->select('RESPUESTA_FALLO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $codCoa);
        $query = $this->db->get();
        $fallo = $query->result_array();
        if (!empty($fallo)) {
            return $fallo[0]['RESPUESTA_FALLO'];
        } else {
            return $fallo = 0;
        }
    }

//FUNCION PARA VERIFICAR QUE LA SUSPENSION DE APLICACION DE TITULOS FUE NOTIFICADA
    function verificarNotiSuspAptitulos($cod_coactivo, $cod_cautelar = null) {
        $this->db->select('COD_MEDIDACAUTELAR');
        $this->db->from('MC_MEDIDASCAUTELARES');
        if (!empty($cod_cautelar)) {
            $this->db->where('COD_MEDIDACAUTELAR', $cod_cautelar);
        }
        $this->db->where('COD_RESPUESTAGESTION', 3018);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA VERIFICAR QUE LA SUSPENSION DL REMATE FUE NOTIFICADA
    function verificarNotiSuspRemate($cod_coactivo) {
        $this->db->select('COD_REMATE');
        $this->db->from('MC_REMATE');
        $this->db->where('COD_RESPUESTA', 3025);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $var = $this->db->count_all_results();
        return $var;
    }

//FUNCION PARA OBTENER PROCESOS COACTIVOS EN TRAMITE DE REVOCATORIA DIRECTA (ALERTA CADA 2 MESES)
    public function procesosEnRevocatoriaDir($id_abogado) {
        $this->db->select('RECEPCIONTITULOS.COD_RECEPCIONTITULO, PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, PROCESOS_COACTIVOS.COD_PROCESOPJ, PROCESOS_COACTIVOS.FECHA_REVOCATORIA, 
                          PROCESOS_COACTIVOS.REVOCATORIA_DECIDIDA, RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA, RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL, PROCESOS_COACTIVOS.ALERTA_REVOCATORIA');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.ABOGADO', $id_abogado);
        $this->db->where('PROCESOS_COACTIVOS.REVOCATORIA_DIRECTA', 1);
        $this->db->where('PROCESOS_COACTIVOS.FECHA_REVOCATORIA IS NOT NULL', NULL, FALSE);
        $this->db->where('PROCESOS_COACTIVOS.REVOCATORIA_DECIDIDA', 0);
        $query = $this->db->get();
        $tramites = $query->result_array();
        return $tramites;
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

//FUNCION PARA: (1)-DECLARAR SOLICITUD DE REVOCATORIA DIRECTA
    function declararRevocatoriaDir($table, $datapc) {
        if ($datapc["FLAG"] == 1) {
            $this->db->trans_begin();
            $this->db->set('REVOCATORIA_DIRECTA', $datapc['REVOCATORIA_DIRECTA']);
            if (isset($datapc['FECHA_REVOCATORIA'])) {
                $this->db->set("FECHA_REVOCATORIA", "to_date('" . $datapc['FECHA_REVOCATORIA'] . "','dd/mm/yyyy HH24:MI:SS')", false);
            }
            $this->db->set('REVOCATORIA_DECIDIDA', $datapc['REVOCATORIA_DECIDIDA']);
            $this->db->where('COD_PROCESO_COACTIVO', $datapc['COD_PROCESO_COACTIVO']);
            $this->db->update($table);
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA BLOQUEAR / DESBLOQUEAR LA APLICACION DE TITULOS O ACTUALIZAR FECHA DE REVOCATORIA (ALERTA CADA 2 MESES)
    function updateFechaRevocatoria($table, $datapc) {
        if ($datapc["FLAG"] == 1) {
            $this->db->trans_begin();
            if (isset($datapc['SUSPENSION_APTITULOS'])) {
                $this->db->set('SUSPENSION_APTITULOS', $datapc['SUSPENSION_APTITULOS']);
            }
            if (isset($datapc['SUSPENSION_REMATE'])) {
                $this->db->set('SUSPENSION_REMATE', $datapc['SUSPENSION_REMATE']);
            }
            $this->db->set("FECHA_REVOCATORIA", "to_date('" . $datapc['FECHA_REVOCATORIA'] . "','dd/mm/yyyy HH24:MI:SS')", false);
            $this->db->where('COD_PROCESO_COACTIVO', $datapc['COD_PROCESO_COACTIVO']);
            $this->db->update($table);
            $this->db->trans_commit();
        }
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA BLOQUEAR / DESBLOQUEAR EL REMATE
    public function gestionBloqueoRemate($cod_coactivo, $parametro) {
        $this->db->trans_begin();
        $this->db->set('SUSPENSION_REMATE', $parametro);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
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

//FUNCION PARA ACTUALIZAR LA RESPUESTA A LA REVOCATORIA DIRECTA
    public function responderRevocatoria($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('REVOCATORIA_DECIDIDA', 1);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA RETIRAR LA REVOCATORIA DIRECTA
    function retirarRevocatoria($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('REVOCATORIA_DIRECTA', 0);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

//FUNCION PARA REANUDAR LA APLICACION DE TITULOS
    function reanudarApTitulos($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('SUSPENSION_APTITULOS', 0);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
//FUNCION PARA REANUDAR EL REMATE
    function reanudarRemate($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('SUSPENSION_REMATE', 0);
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
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
    
//FUNCION PARA RETIRAR LA ALERTA QUE INFORMA EL PROCESO CONTENCIOSO
    function retirarContencioso($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('ALERTA_CONTENCIOSO', 0);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
//FUNCION PARA RETIRAR LA ALERTA QUE INFORMA EL TRAMITE DE REVOCATORIA DIRECTA
    function retirarAlertTramiteRev($cod_coactivo) {
        $this->db->trans_begin();
        $this->db->set('ALERTA_REVOCATORIA', 0);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $this->db->update('PROCESOS_COACTIVOS');
        $this->db->trans_commit();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }
    
//FUNCION PARA VERIFICAR EL ESTADO DE LA ALERTA PARA DEMANDAS CON SUSPENSION PROVISIONAL
    public function verificarAlerta($cod_coactivo) {
        $this->db->select('ALERTA_CONTENCIOSO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $alerta = $query->result_array();
        if (!empty($alerta)) {
            return $alerta[0]['ALERTA_CONTENCIOSO'];
        } else {
            return $alerta = 0;
        }
    }
    
//FUNCION PARA VERIFICAR EL ESTADO DE LA ALERTA PARA TRAMITES DE REVOCATORIA
    public function verificarAlertaRev($cod_coactivo) {
        $this->db->select('ALERTA_REVOCATORIA');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->where('COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $alerta = $query->result_array();
        if (!empty($alerta)) {
            return $alerta[0]['ALERTA_REVOCATORIA'];
        } else {
            return $alerta = 0;
        }
    }
    
//FUNCION PARA OBTENER DATOS DEL PROCESO COACTIVO CONTENCIOSO CON SUSPENSION PROVISIONAL (SECRETARIO Y EJECUTOR)
    public function procesoContencioso($cod_coactivo) {
        $this->db->select('RECEPCIONTITULOS.COD_RECEPCIONTITULO, PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, PROCESOS_COACTIVOS.COD_PROCESOPJ, PROCESOS_COACTIVOS.FECHA_NULIDAD, 
                          PROCESOS_COACTIVOS.RESPUESTA_FALLO, RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA, RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL, PROCESOS_COACTIVOS.ALERTA_CONTENCIOSO');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.DEM_NULIDAD', 1);
        $this->db->where('PROCESOS_COACTIVOS.FECHA_NULIDAD IS NOT NULL', NULL, FALSE);
        $this->db->where('PROCESOS_COACTIVOS.RESPUESTA_FALLO', 0);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $contencioso = $query->result_array();
        return $contencioso;
    }
    
//FUNCION PARA OBTENER DATOS DEL PROCESO COACTIVO EN TRAMITE DE REVOCATORIA DIRECTA(SECRETARIO Y EJECUTOR)
    public function procesoTramRevocatoria($cod_coactivo) {
        $this->db->select('RECEPCIONTITULOS.COD_RECEPCIONTITULO, PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO, PROCESOS_COACTIVOS.COD_PROCESOPJ, PROCESOS_COACTIVOS.FECHA_REVOCATORIA, 
                          PROCESOS_COACTIVOS.REVOCATORIA_DECIDIDA, RECEPCIONTITULOS.COD_FISCALIZACION_EMPRESA, RECEPCIONTITULOS.COD_CARTERA_NOMISIONAL, PROCESOS_COACTIVOS.ALERTA_REVOCATORIA');
        $this->db->from('PROCESOS_COACTIVOS');
        $this->db->join('ACUMULACION_COACTIVA', 'PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO = ACUMULACION_COACTIVA.COD_PROCESO_COACTIVO');
        $this->db->join('RECEPCIONTITULOS', 'ACUMULACION_COACTIVA.COD_RECEPCIONTITULO = RECEPCIONTITULOS.COD_RECEPCIONTITULO');
        $this->db->where('PROCESOS_COACTIVOS.REVOCATORIA_DIRECTA', 1);
        $this->db->where('PROCESOS_COACTIVOS.REVOCATORIA_DECIDIDA', 0);
        $this->db->where('PROCESOS_COACTIVOS.ALERTA_REVOCATORIA', 1);
        $this->db->where('PROCESOS_COACTIVOS.COD_PROCESO_COACTIVO', $cod_coactivo);
        $query = $this->db->get();
        $tramite_rev = $query->result_array();
        return $tramite_rev;
    }

}

?>