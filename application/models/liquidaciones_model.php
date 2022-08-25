<?php

/**
 * Archivo para ala administraciÃ³n de los modelos necesarios para las liquidaciones en el proceso administrativo
 *
 * @packageCartera
 * @subpackage Models
 * @author jdussan
 * @location./application/models/liquidaciones_model.php
 * @last-modified 27/11/2014
 */
class Liquidaciones_model extends MY_Model {

    public $tasaActual = 0;

    function __construct() {
        parent::__construct();
    }

    //CONSULTA CODIGO FISCALIZACIÃ“N
    function consultarCodigoFiscalizacion($codigoFiscalizacion, $idusuario)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n asociada al codigo de fiscalizaciÃ³n, necesaria para el proceso de liquidaciÃ³n. Se asocia el id de usuario que lanza la consulta para comproborar su permiso al proceso.
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de fiscalizaciÃ³n existente
     *
     * @param integer $codigoFiscalizacion
     * @param integer $idusuario
     * @return array $fiscalizacion
     * @return boolean false - error
     */ {
        $this->db->select('F.COD_FISCALIZACION, F.COD_ASIGNACION_FISC, F.COD_CONCEPTO, CF.NOMBRE_CONCEPTO,TG.TIPOGESTION, AF.ASIGNADO_POR, AF.ASIGNADO_A, AF.NIT_EMPRESA, EMP.RAZON_SOCIAL, REG.NOMBRE_REGIONAL, EMP.CIIU, CIIU.DESCRIPCION, LIQ.EN_FIRME');
        $this->db->select('to_char("F"."PERIODO_INICIAL",' . "'DD/MM/YYYY') AS PERIODO_INICIAL", FALSE);
        $this->db->select('to_char("F"."PERIODO_FINAL",' . "'DD/MM/YYYY') AS PERIODO_FINAL", FALSE);
        $this->db->from('FISCALIZACION "F"');
        $this->db->join('TIPOGESTION "TG"', 'F.COD_TIPOGESTION = TG.COD_GESTION', 'left');
        $this->db->join('CONCEPTOSFISCALIZACION "CF"', 'F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION', 'left');
        $this->db->join('ASIGNACIONFISCALIZACION "AF"', 'F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION', 'left');
        $this->db->join('EMPRESA "EMP"', 'AF.NIT_EMPRESA = EMP.CODEMPRESA', 'left');
        $this->db->join('CIIU', 'EMP.CIIU = CIIU.CLASE', 'left');
        $this->db->join('REGIONAL "REG"', 'EMP.COD_REGIONAL = REG.COD_REGIONAL', 'left');
        $this->db->join('LIQUIDACION "LIQ"', 'F.COD_FISCALIZACION = LIQ.COD_FISCALIZACION', 'left');
        $condicion = "F.COD_FISCALIZACION ='" . $codigoFiscalizacion . "' AND (AF.ASIGNADO_POR = " . $idusuario . " OR AF.ASIGNADO_A = " . $idusuario . ")";
        $this->db->where($condicion);
        $resultado = $this->db->get();
        //#####DEBUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####DEBUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $fiscalizacion = $resultado->row_array();
            return $fiscalizacion;
        else:
            return FALSE;
        endif;
    }

    //CONSULTA CODIGO FISCALIZACIÃ“N
    function consultarCodigoFiscalizacionBloqueada($codigoFiscalizacion, $idusuario)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n asociada al codigo de fiscalizaciÃ³n, necesaria para el proceso de liquidaciÃ³n. Se asocia el id de usuario que lanza la consulta para comproborar su permiso al proceso.
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de fiscalizaciÃ³n existente
     *
     * @param integer $codigoFiscalizacion
     * @param integer $idusuario
     * @return array $fiscalizacion
     * @return boolean false - error
     */ {
        $this->db->select('F.COD_FISCALIZACION, F.COD_ASIGNACION_FISC, F.COD_CONCEPTO, CF.NOMBRE_CONCEPTO,TG.TIPOGESTION, AF.ASIGNADO_POR, AF.ASIGNADO_A, AF.NIT_EMPRESA, EMP.RAZON_SOCIAL, REG.NOMBRE_REGIONAL, EMP.CIIU, CIIU.DESCRIPCION, LIQ.EN_FIRME, LIQ.BLOQUEADA');
        $this->db->select('to_char("F"."PERIODO_INICIAL",' . "'DD/MM/YYYY') AS PERIODO_INICIAL", FALSE);
        $this->db->select('to_char("F"."PERIODO_FINAL",' . "'DD/MM/YYYY') AS PERIODO_FINAL", FALSE);
        $this->db->from('FISCALIZACION "F"');
        $this->db->join('TIPOGESTION "TG"', 'F.COD_TIPOGESTION = TG.COD_GESTION', 'left');
        $this->db->join('CONCEPTOSFISCALIZACION "CF"', 'F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION', 'left');
        $this->db->join('ASIGNACIONFISCALIZACION "AF"', 'F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION', 'left');
        $this->db->join('EMPRESA "EMP"', 'AF.NIT_EMPRESA = EMP.CODEMPRESA', 'left');
        $this->db->join('CIIU', 'EMP.CIIU = CIIU.CLASE', 'left');
        $this->db->join('REGIONAL "REG"', 'EMP.COD_REGIONAL = REG.COD_REGIONAL', 'left');
        $this->db->join('LIQUIDACION "LIQ"', 'F.COD_FISCALIZACION = LIQ.COD_FISCALIZACION', 'left');
        $condicion = "F.COD_FISCALIZACION ='" . $codigoFiscalizacion . "' AND (AF.ASIGNADO_POR = " . $idusuario . " OR AF.ASIGNADO_A = " . $idusuario . " AND LIQ.BLOQUEADA = 0)";
        $this->db->where($condicion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $fiscalizacion = $resultado->row_array();
            return $fiscalizacion;
        else:
            return FALSE;
        endif;
    }

    function consultarFiscalizaciones($nit, $razonSocial, $representante, $expediente, $concepto, $usuario)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n asociada a unos de los parametros de $nit, $razonSocial, $representante en la tbala fiscalizaciÃ³n, por ende puede traer multiples fiscalizaciones que pueden estar asociadas al usuario.
     * Si se indican los datos de filtro: $expediente, $concepto, los resultados se pueden reducir considerablemente.
     *
     * @param integer $nit
     * @param integer $razonSocial
     * @param integer $representante
     * @param integer $expediente
     * @param integer $concepto
     * @param integer $usuario
     * @return array $fiscalizacion
     * @return boolean false - error
     */ {
        // $this -> db -> select('FISC.COD_FISCALIZACION, FISC.COD_CONCEPTO, FISC.COD_TIPOGESTION, ASIG.ASIGNADO_A, ASIG.ASIGNADO_POR, EMP.CODEMPRESA, EMP.NOMBRE_EMPRESA, EMP.REPRESENTANTE_LEGAL, CON.NOMBRE_CONCEPTO, TIP.TIPOGESTION');
        $this->db->select('FISC.COD_ASIGNACION_FISC, FISC.COD_FISCALIZACION, EMP.CODEMPRESA, EMP.RAZON_SOCIAL, CON.NOMBRE_CONCEPTO, TIP.TIPOGESTION');
        $this->db->select('to_char("FISC"."PERIODO_INICIAL",' . "'DD/MM/YYYY') AS PERIODO_INICIAL", FALSE);
        $this->db->select('to_char("FISC"."PERIODO_FINAL",' . "'DD/MM/YYYY') AS PERIODO_FINAL", FALSE);
        $this->db->from('FISCALIZACION "FISC"');
        $this->db->from('ASIGNACIONFISCALIZACION "ASIG"');
        $this->db->from('EMPRESA "EMP"');
        $this->db->from('CONCEPTOSFISCALIZACION "CON"');
        $this->db->from('TIPOGESTION "TIP"');
        $condicion = "FISC.COD_TIPOGESTION not in (309, 440) and FISC.COD_CONCEPTO not in (3,5) and FISC.CODIGO_PJ is NULL ";

        if ($nit != ''):
            $condicion .= "and ( (EMP.CODEMPRESA = '" . $nit . "'";
            if ($razonSocial == '' && $representante == ''):
                $condicion .= ")";
            endif;
        endif;
        if ($razonSocial != ''):
            if ($nit != ''):
                $condicion .= " and EMP.RAZON_SOCIAL = '" . $razonSocial . "')";
            else:
                $condicion .= "and ( (EMP.RAZON_SOCIAL = '" . $razonSocial . "'";
                if ($representante == ''):
                    $condicion .= ")";
                endif;
            endif;
        endif;
        if ($representante != ''):
            if ($nit != '' || $razonSocial != ''):
                $condicion .= " and EMP.REPRESENTANTE_LEGAL = '" . $representante . "')";
            else:
                $condicion .= "and ((EMP.REPRESENTANTE_LEGAL = '" . $representante . "'";
                if ($nit == '' && $razonSocial == ''):
                    $condicion .= ")";
                endif;
            endif;
        endif;

        if ($razonSocial != '' || $nit != '' || $representante != ''):
            $condicion .= ")";
        endif;
        $condicion .= " and ( FISC.COD_ASIGNACION_FISC = ASIG.COD_ASIGNACIONFISCALIZACION and ASIG.NIT_EMPRESA = EMP.CODEMPRESA and (ASIG.ASIGNADO_A = '" . $usuario . "' or ASIG.ASIGNADO_POR = '" . $usuario . "') and FISC.COD_CONCEPTO = CON.COD_CPTO_FISCALIZACION and FISC.COD_TIPOGESTION = TIP.COD_GESTION";
        if ($expediente != ''):
            $condicion .= " and FISC.COD_FISCALIZACION = '" . $expediente . "'";
        endif;
        if ($concepto != 'null'):
            $condicion .= " and FISC.COD_CONCEPTO = " . $concepto;
        endif;
        $condicion .= ")";

        $this->db->where($condicion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado):
            $tmp = NULL;
            foreach ($resultado->result_array() as $fiscalizacion):
                $tmp[] = $fiscalizacion;
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;

        return $datos;
    }

    //CONSULTA DE CABECERAS PARA LIQUIDACIONES
    function consultarCabeceraLiquidacion($codigoFiscalizacion, $idusuario)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n asociada al codigo de fiscalizaciÃ³n, necesaria para el proceso de liquidaciÃ³n y que se muestra en las cabeceras de las liquidaciones. Se asocia el id de usuario que lanza la consulta para comproborar su permiso de acceso al proceso.
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de fiscalizaciÃ³n existente
     *
     * @param integer $codigoFiscalizacion
     * @param integer $idusuario
     * @return array $fiscalizacion
     * @return boolean false - error
     */ {
        $this->db->select('F.COD_FISCALIZACION, F.COD_ASIGNACION_FISC, F.COD_CONCEPTO, F.NRO_EXPEDIENTE, CF.NOMBRE_CONCEPTO, TG.TIPOGESTION, AF.ASIGNADO_POR, AF.ASIGNADO_A, AF.NIT_EMPRESA, EMP.RAZON_SOCIAL, REG.NOMBRE_REGIONAL, CIIU.CLASE CIIU, CIIU.DESCRIPCION, EMP.DIRECCION, EMP.TELEFONO_FIJO, EMP.FAX, EMP.EMAILAUTORIZADO, EMP.EMPRESA_NUEVA, EMP.REPRESENTANTE_LEGAL, EMP.COD_REPRESENTANTELEGAL, MUN.NOMBREMUNICIPIO, EMP.NOM_CAJACOMPENSACION, EMP.RESOLUCION,EMP.NUM_EMPLEADOS,EMP.NRO_ESCRITURAPUBLICA, EMP.NOTARIA');
        $this->db->select('to_char("F"."PERIODO_INICIAL",' . "'DD/MM/YYYY') AS PERIODO_INICIAL", FALSE);
        $this->db->select('to_char("F"."PERIODO_FINAL",' . "'DD/MM/YYYY') AS PERIODO_FINAL", FALSE);
        $this->db->from('FISCALIZACION "F"');
        $this->db->join('TIPOGESTION "TG"', 'F.COD_TIPOGESTION = TG.COD_GESTION', 'LEFT');
        $this->db->join('CONCEPTOSFISCALIZACION "CF"', 'F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION');
        $this->db->join('ASIGNACIONFISCALIZACION "AF"', 'F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION');
        $this->db->join('EMPRESA "EMP"', 'AF.NIT_EMPRESA = EMP.CODEMPRESA');
        $this->db->join('CIIU', 'CIIU.COD_CIUU = EMP.CIIU', 'LEFT');
        $this->db->join('REGIONAL "REG"', 'EMP.COD_REGIONAL = REG.COD_REGIONAL');
        $this->db->join('DEPARTAMENTO "DEP"', 'EMP.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO');
        $this->db->join('MUNICIPIO "MUN"', 'EMP.COD_MUNICIPIO = MUN.CODMUNICIPIO AND DEP.COD_DEPARTAMENTO = MUN.COD_DEPARTAMENTO', 'LEFT');
        //$this -> db -> from('LIQUIDACION "LIQ"');
        //$condicion = "(F.COD_TIPOGESTION not in (309, 440) AND F.CODIGO_PJ is NULL 	AND F.COD_FISCALIZACION ='" . $codigoFiscalizacion . "' AND (AF.ASIGNADO_POR = " . $idusuario . " OR AF.ASIGNADO_A = " . $idusuario . ")) AND (F.COD_TIPOGESTION = TG.COD_GESTION 	AND F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION 	AND F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION AND AF.NIT_EMPRESA = EMP.CODEMPRESA AND EMP.CIIU = CIIU.COD_CIUU AND EMP.COD_REGIONAL = REG.COD_REGIONAL AND EMP.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO AND EMP.COD_MUNICIPIO = MUN.CODMUNICIPIO AND DEP.COD_DEPARTAMENTO = MUN.COD_DEPARTAMENTO)";
        $condicion = "((F.COD_TIPOGESTION not in (309, 440) OR F.COD_TIPOGESTION IS NULL)  AND F.CODIGO_PJ is NULL 	AND F.COD_FISCALIZACION ='" . $codigoFiscalizacion . "' AND (AF.ASIGNADO_POR = " . $idusuario . " OR AF.ASIGNADO_A = " . $idusuario . ")) ";
        $this->db->where($condicion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($resultado->num_rows() > 0):
            $fiscalizacion = $resultado->row_array();
            return $fiscalizacion;
        else:
            return FALSE;
        endif;
    }

    //CONSULTA DE CABECERAS PARA LIQUIDACIONES
    function consultarCabeceraLiquidacionFicBloqueada($codigoFiscalizacion, $idusuario, $liquidacion)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n asociada al codigo de fiscalizaciÃ³n, necesaria para el proceso de liquidaciÃ³n y que se muestra en las cabeceras de las liquidaciones. Se asocia el id de usuario que lanza la consulta para comproborar su permiso de acceso al proceso.
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de fiscalizaciÃ³n existente
     *
     * @param integer $codigoFiscalizacion
     * @param integer $idusuario
     * @return array $fiscalizacion
     * @return boolean false - error
     */ {
        /* $this->db->select('F.COD_FISCALIZACION, F.COD_ASIGNACION_FISC, F.COD_CONCEPTO, LIQ.NUM_LIQUIDACION AS NRO_EXPEDIENTE, CF.NOMBRE_CONCEPTO, TG.TIPOGESTION, AF.ASIGNADO_POR, AF.ASIGNADO_A, AF.NIT_EMPRESA, EMP.RAZON_SOCIAL, REG.NOMBRE_REGIONAL, EMP.CIIU, CIIU.DESCRIPCION, EMP.DIRECCION, EMP.TELEFONO_FIJO, EMP.FAX, EMP.EMAILAUTORIZADO, EMP.EMPRESA_NUEVA, EMP.REPRESENTANTE_LEGAL, EMP.COD_REPRESENTANTELEGAL, MUN.NOMBREMUNICIPIO, EMP.NOM_CAJACOMPENSACION, EMP.RESOLUCION,EMP.NUM_EMPLEADOS,EMP.NRO_ESCRITURAPUBLICA, EMP.NOTARIA');
          $this->db->select('to_char("F"."PERIODO_INICIAL",' . "'DD/MM/YYYY') AS PERIODO_INICIAL", FALSE);
          $this->db->select('to_char("F"."PERIODO_FINAL",' . "'DD/MM/YYYY') AS PERIODO_FINAL", FALSE);
          $this->db->from('FISCALIZACION "F"');
          $this->db->join('TIPOGESTION "TG"','F.COD_TIPOGESTION = TG.COD_GESTION');
          $this->db->join('CONCEPTOSFISCALIZACION "CF"','F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION');
          $this->db->join('ASIGNACIONFISCALIZACION "AF"','F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION');
          $this->db->join('EMPRESA "EMP"', 'AF.NIT_EMPRESA = EMP.CODEMPRESA');
          $this->db->join('CIIU','CIIU.COD_CIUU = EMP.CIIU','LEFT');
          $this->db->join('REGIONAL "REG"','EMP.COD_REGIONAL = REG.COD_REGIONAL');
          $this->db->join('DEPARTAMENTO "DEP"','EMP.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO');
          $codi = "LIQ.NUM_LIQUIDACION ='$liquidacion'";
          $this->db->join('MUNICIPIO "MUN"', 'EMP.COD_MUNICIPIO = MUN.CODMUNICIPIO AND DEP.COD_DEPARTAMENTO = MUN.COD_DEPARTAMENTO');
          $this->db->join('LIQUIDACION LIQ',$codi,TRUE);
          $condicion = "(F.COD_TIPOGESTION not in (309, 440) AND F.CODIGO_PJ is NULL 	AND F.COD_FISCALIZACION ='" . $codigoFiscalizacion . "' AND (AF.ASIGNADO_POR = " . $idusuario . " OR AF.ASIGNADO_A = " . $idusuario . "))";
          $this->db->where($condicion);
          $resultado = $this->db->get(); */

        $query = $this->db->query("SELECT F.COD_FISCALIZACION, F.COD_ASIGNACION_FISC, F.COD_CONCEPTO, LIQ.NUM_LIQUIDACION AS NRO_EXPEDIENTE, CF.NOMBRE_CONCEPTO, 
            TG.TIPOGESTION, AF.ASIGNADO_POR, AF.ASIGNADO_A, 
            AF.NIT_EMPRESA, EMP.RAZON_SOCIAL, REG.NOMBRE_REGIONAL, EMP.CIIU, CIIU.DESCRIPCION, EMP.DIRECCION, EMP.TELEFONO_FIJO, EMP.FAX, 
            EMP.EMAILAUTORIZADO, EMP.EMPRESA_NUEVA, EMP.REPRESENTANTE_LEGAL, EMP.COD_REPRESENTANTELEGAL, MUN.NOMBREMUNICIPIO, EMP.NOM_CAJACOMPENSACION, 
            EMP.RESOLUCION, EMP.NUM_EMPLEADOS, EMP.NRO_ESCRITURAPUBLICA, EMP.NOTARIA, to_char(F.PERIODO_INICIAL, 'DD/MM/YYYY') AS PERIODO_INICIAL, 
            to_char(F.PERIODO_FINAL, 'DD/MM/YYYY') AS PERIODO_FINAL 
            FROM FISCALIZACION F JOIN TIPOGESTION TG ON F.COD_TIPOGESTION = TG.COD_GESTION 
            JOIN CONCEPTOSFISCALIZACION CF ON F.COD_CONCEPTO = CF.COD_CPTO_FISCALIZACION 
            JOIN ASIGNACIONFISCALIZACION AF ON F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION 
            JOIN EMPRESA EMP ON AF.NIT_EMPRESA = EMP.CODEMPRESA 
            LEFT JOIN CIIU ON CIIU.COD_CIUU = EMP.CIIU 
            JOIN REGIONAL REG ON EMP.COD_REGIONAL = REG.COD_REGIONAL 
            JOIN DEPARTAMENTO DEP ON EMP.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO 
            JOIN MUNICIPIO MUN ON EMP.COD_MUNICIPIO = MUN.CODMUNICIPIO AND DEP.COD_DEPARTAMENTO = MUN.COD_DEPARTAMENTO 
            JOIN LIQUIDACION LIQ ON LIQ.NUM_LIQUIDACION ='$liquidacion' 
            WHERE (F.COD_TIPOGESTION not in (309, 440) AND F.CODIGO_PJ is NULL AND F.COD_FISCALIZACION =' $codigoFiscalizacion' AND (AF.ASIGNADO_POR = $idusuario OR AF.ASIGNADO_A = $idusuario))");
        $resultado = $this->db->query($query);
        //#####BUGGER PARA LA CONSULTA ######
        
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $fiscalizacion = $resultado->row_array();
            return $fiscalizacion;
        else:
            return FALSE;
        endif;
    }

    //CONSULTA PARA DETERMINAR SI LA FISCALIZACIÃ“N YA TIENE LIQUIDACION APORTES
    function consultarLiquidacionAportes($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos los registros disponibles de una liquidaciÃ³n en la tabla detalle para aportes parafizcales
     * @param string $codigoLiquidacion
     * @return array $detalles
     * @return boolean false - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
      
        $str_query = "SELECT substr(PERIODO, 1, 4) as ANO,SUM(SUPERNUMERARIOS) AS SUPERNUMERARIOS,
        SUM(SALARIOESPECIE) AS SALARIOESPECIE,
        SUM(VALORSUELDOS) AS VALORSUELDOS,
        SUM(VALORSOBRESUELDOS) AS VALORSOBRESUELDOS,
        SUM(SALARIOINTEGRAL) AS SALARIOINTEGRAL,
        SUM(COMISIONES) AS COMISIONES,
        SUM(PORCENTAJEVENTAS) AS POR_SOBREVENTAS,
        SUM(VACACIONES) AS VACACIONES,
        SUM(TRABAJODOMICILIO) AS TRAB_DOMICILIO,
        SUM(CONTRATOSSUBCONTRATOS) AS SUBCONTRATO,
        SUM(PRIMASALARIAL) AS PRIMA_TEC_SALARIAL,
        SUM(AUX_SUBSIDIOALIMENTACION) AS AUXILIO_ALIMENTACION,
        SUM(PRIMA_SERVICIO) AS PRIMA_SERVICIO,
        SUM(PRIMA_LOCALIZACION) AS PRIMA_LOCALIZACION,
        SUM(PRIMA_VIVIENDA) AS PRIMA_VIVIENDA,
        SUM(GASTOS_REPRESENTACION) AS GAST_REPRESENTACION,
        SUM(PRIMA_INCREMENTO_ANTIGUEDAD) AS PRIMA_ANTIGUEDAD,
        SUM(PRIMA_PRODUCTIVIDAD) AS PRIMA_EXTRALEGALES,
        SUM(PRIMA_VACACIONES) AS PRIMA_VACACIONES,
        SUM(PRIMA_NAVIDAD) AS PRIMA_NAVIDAD,
        SUM(JORNALES) AS JORNALES,
        SUM(AUXILIOTRANSPORTE) AS AUXILIOTRANSPORTE,
        SUM(HORASEXTRAS) AS HORASEXTRAS,
        SUM(DOMINICALES_FESTIVOS)AS DOMINICALES_FESTIVOS,
        SUM(RECARGONOCTURNO) AS RECARGONOCTURNO,
        SUM(VIATICOS) AS VIATICOS,
        SUM(BONIFICACIONES) AS BONIFICACIONES,
        SUM(CONTRATOS_AGRICOLAS) AS CONTRATOS_AGRICOLAS,
        SUM(REMU_SOCIOS_INDUSTRIALES) AS REMU_SOCIOS_INDUSTRIALES,
        SUM(HORA_CATEDRA) AS HORA_CATEDRA,
        SUM(OTROS_PAGOS) AS OTROS_PAGOS
        FROM LIQ_APORTESPARAFISCALES_MES
        WHERE NUM_LIQUIDACION =  $codigoLiquidacion
        GROUP BY substr(PERIODO, 1, 4)";

        $query = $this->db->query($str_query);
        $array = $query->result_array();
      
   

       $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
      
        
        $datos = $query->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $detalle):
                $tmp[] = $detalle;
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }

    function consultarLiquidacionSGVA($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos los registros disponibles de una liquidaciÃ³n en la tabla detalle para aportes parafizcales
     * @param string $codigoLiquidacion
     * @return array $detalles
     * @return boolean false - error
     */ {
        $resultado = $this->db->get_where('LIQ_SGVA_DET', "CODLIQUIDACIONCONTRATOS_P = '" . $codigoLiquidacion . "'");
        // $resultado = $this -> db -> get_where('LIQ_APORTESPARAFISCALES_DET',  "CODLIQUIDACIONAPORTES_P = '1179951894110'");
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $resultado->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $detalle):
                $tmp[] = $detalle;
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }

    //CONSULTA PARA DETERMINAR SI LA FISCALIZACIÃ“N YA TIENE LIQUIDACION APORTES
    function consultarLiquidacionBloqueadaFisc($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos los registros disponibles de una fiscalizaciÃ³n en la tabla liquidaciÃ³n
     * @param string $codigoFiscalizaciÃ³n
     * @return array $resultado
     * @return boolean false - error
     */ {

        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('NUM_LIQUIDACION');
        $this->db->from('LIQUIDACION');
        $condicion = "COD_FISCALIZACION = '" . $codigoLiquidacion . "' ORDER BY NUM_LIQUIDACION DESC";
        $this->db->where($condicion);
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$datos = $this -> db -> last_query();
        //echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $registros = $datos->num_rows();

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();

            return $registros;

        endif;
    }

    //CONSULTA PARA DETERMINAR SI LA FISCALIZACIÃ“N YA TIENE LIQUIDACION APORTES
    function consultarLiquidacionBloqueada($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos los registros disponibles de una fiscalizaciÃ³n en la tabla liquidaciÃ³n
     * @param string $codigoFiscalizaciÃ³n
     * @return array $resultado
     * @return boolean false - error
     */ {

        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('NUM_LIQUIDACION');
        $this->db->from('LIQUIDACION');
        $condicion = "COD_FISCALIZACION = '" . $codigoLiquidacion . "' ORDER BY NUM_LIQUIDACION DESC";
        $this->db->where($condicion);
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$datos = $this -> db -> last_query();
        //echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $registros = $datos->num_rows();

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->row_array();
            if ($registros > 1):

                return $resultado = $datos;

            else:

                return $datos = FALSE;

            endif;

        endif;
    }

    //CONSULTA PARA INFORMACIÃ“N DE DOCUMENTACIÃ“N EN LIQUIDACION APORTES
    function consultarLiquidacionAportesDetalle($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos la informaciÃ³n de documentaciÃ³n disponibles de una liquidaciÃ³n en la tabla maestra
     * @param string $codigoLiquidacion
     * @return array $detalles
     * @return boolean false - error
     */ {
        $this->db->select('OBSERVACIONES, DOCU_APORTADA');
        $this->db->from('LIQ_APORTESPARAFISCALES');
        $this->db->where('CODLIQUIDACIONAPORTES_P', $codigoLiquidacion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $detalles = $resultado->row_array();
            return $detalles;
        else:
            return FALSE;
        endif;
    }

    function consultarLiquidacionSGVADetalle($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna todos la informaciÃ³n de documentaciÃ³n disponibles de una liquidaciÃ³n en la tabla maestra
     * @param string $codigoLiquidacion
     * @return array $detalles
     * @return boolean false - error
     */ {
        $this->db->select('OBSERVACIONES, DOCU_APORTADA');
        $this->db->from('LIQ_SGVA');
        $this->db->where('CODLIQUIDACIONCONTRATOS_P', $codigoLiquidacion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $detalles = $resultado->row_array();
            return $detalles;
        else:
            return FALSE;
        endif;
    }

    function consultarLiquidacionFic($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna si existe una liquidaciÃ³n Fic  en el maestro de liquidaciones
     * @param string $codigoLiquidacion
     * @return array $datos
     * @return boolean false - error
     */ {
        $resultado = $this->db->get_where('LIQUIDACION_FIC', "NRO_EXPEDIENTE = '" . $codigoLiquidacion . "'");
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $resultado->result_array();
        if (!empty($datos)):
            return $datos;
        else:
            return $datos = FALSE;
        endif;
    }

    function consultarLiquidacionMulta($codigoLiquidacion)
    /**
     * Funcion que retorna si existe una liquidaciÃ³n en la tabla maestra de Multas de Ministerio
     * @param string $codigoLiquidacion
     * @return array $datos
     * @return boolean false - error
     */ {
        $resultado = $this->db->get_where('INTERES_MULTAMIN_ENC', "COD_INTERES_MULTA_MIN = '" . $codigoLiquidacion . "'");
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $resultado->result_array();
        if (!empty($datos)):
            return $datos;
        else:
            return $datos = FALSE;
        endif;
    }

    function cargarLiquidacionFic($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla maestra y detalle de liquidacion FIC
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $presuntiva = $liquidacion['detalle_presuntiva'];
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('CODEMPRESA', $liquidacion['nitEmpresa']);
        $this->db->set('CODTIPOLIQUIDACION', $liquidacion['tipoLiquidacion'], FALSE);
        $this->db->set('FECHALIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('VALORINVERSION', $liquidacion['valorInversion'], FALSE);
        $this->db->set('GASTOSFINANCIACION', $liquidacion['gastosFinanciacion'], FALSE);
        $this->db->set('VALORLOTE', $liquidacion['valorLote'], FALSE);
        $this->db->set('INDEM_TERCEROS', $liquidacion['indenmizacion'], FALSE);
        $this->db->set('PERI_INICIAL', "TO_DATE('" . $liquidacion['periodoInicial'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('PERI_FINAL', "TO_DATE('" . $liquidacion['periodoFinal'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('VALOR_FIC', $liquidacion['valorFic'], FALSE);
        $this->db->set('INTERESES_FIC', $liquidacion['interesesFic'], FALSE);
        $this->db->set('VALOR_TOTAL_FIC', $liquidacion['valorTotalFic'], FALSE);
        $this->db->set('COD_FUNCIONARIO', $liquidacion['codFuncionario'], FALSE);
        // $this -> db -> set ('CODLIQUIDACIONAPORTES_P', 1, FALSE);
        $this->db->set('COD_FISCALIZACION', $liquidacion['fiscalizacion'], FALSE);
        $this->db->set('NRO_EXPEDIENTE', $liquidacion['id']);

        if ($liquidacion_previa == 0):
            $this->db->set('CODLIQUIDACIONFIC', $liquidacion['id']);
            $resultado = $this->db->insert('LIQUIDACION_FIC');
        else:
            $this->db->where('CODLIQUIDACIONFIC', $liquidacion['id']);
            $resultado = $this->db->update('LIQUIDACION_FIC');
        endif;


        $datos_fecha_inicial = explode("/", $liquidacion['periodoInicial']);
        $anno_fecha_inicial = (int) $datos_fecha_inicial[2];
        $datos_fecha_final = explode("/", $liquidacion['periodoFinal']);
        $anno_fecha_final = (int) $datos_fecha_final[2];
        //detalle por aÃ±o
        $normativa = $liquidacion['detalle_normativa'];
        $presuntiva = $liquidacion['detalle_presuntiva'];
        for ($i = $anno_fecha_inicial; $i <= $anno_fecha_final; $i ++):
            /* /insertar detalle normativa
              $this -> db -> set('ANO', $i, FALSE);
              $this -> db -> set('NRO_TRABAJADORES', $normativa['empleados']['empleados_' . $i], FALSE);
              $this -> db -> set('TOTAL_ANO', number_format($normativa['valor_anno']['valor_total_' . $i], 2, '.', ''), FALSE);
              $this -> db -> set('MESCOBRO', 1, FALSE);
              $this -> db -> set('CODLIQUIDACIONFIC', $liquidacion['id']);
              $resultado = $this -> db -> insert('LIQ_FIC_NORMATIVA');
              //insertar detalle presuntiva
              $this -> db -> set('COD_TIPOLIQ_PRESUNTIVA', 1, FALSE);
              $this -> db -> set('VLR_CONTRATO_TODOCOSTO', number_format($presuntiva['ValorContratoCosto_' . $i], 2, '.', ''), FALSE);
              $this -> db -> set('VLR_CONTRATO_MANO_OBRA', number_format($presuntiva['ValorContratoObra_' . $i], 2, '.', ''), FALSE);
              $this -> db -> set('PAGOS_FIC_DESCONTAR', number_format(0, 2, '.', ''), FALSE);
              $this -> db -> set('COD_LIQ_PRESUNTIVA', $liquidacion['id']);
              $this -> db -> set('CODLIQUIDACIONFIC', $liquidacion['id']);
              $resultado = $this -> db -> insert('LIQ_FIC_PRESUNTIVA'); */
        endfor;
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacion($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla liquidacion
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->where('BLOQUEADA', '0');
        $this->db->where('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        $query = $this->db->get('LIQUIDACION');
        // echo $this->db->last_query();die();
        $respuesta = $query->result_array();
        $antigua = '';
        $antigua = @$respuesta[0]['COD_FISCALIZACION'];
        if ($antigua == '') {
            $liquidacion_previa = 0;
        } else {
            $liquidacion_previa = 1;
        }
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('COD_CONCEPTO', $liquidacion['codigoConcepto'], FALSE);
        $this->db->set('NITEMPRESA', $liquidacion['nitEmpresa']);
        $this->db->set('FECHA_INICIO', "TO_DATE('" . $liquidacion['fechaInicio'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('FECHA_FIN', "TO_DATE('" . $liquidacion['fechaFin'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('FECHA_LIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('FECHA_VENCIMIENTO', "TO_DATE('" . $liquidacion['fechaVencimiento'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('TOTAL_LIQUIDADO', $liquidacion['totalLiquidado'], FALSE);

        if (!empty($liquidacion['en_firme'])) {
            $this->db->set('EN_FIRME', $liquidacion['en_firme']);
        }



        $this->db->set('TOTAL_INTERESES', $liquidacion['totalInteres'], FALSE);
        $this->db->set('SALDO_DEUDA', $liquidacion['saldoDeuda'], FALSE);
        $this->db->set('TOTAL_CAPITAL', $liquidacion['totalCapital'], FALSE);
        $this->db->set('COD_TIPOPROCESO', $liquidacion['tipoProceso'], FALSE);
        $this->db->set('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        $this->db->set('SALDO_CAPITAL', $liquidacion['totalCapital'], FALSE);
        $this->db->set('SALDO_INTERES', $liquidacion['totalInteres'], FALSE);

        if ($liquidacion['codigoConcepto'] == '5'):
            $this->db->set('FECHA_RESOLUCION', "TO_DATE('" . $liquidacion['FECHA_RESOLUCION'] . "','YYYY-MM-DD')", FALSE);
            $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $liquidacion['fechaFin'] . "','DD/MM/YYYY')", FALSE);
            $this->db->set('DIAS_MORA', '0', FALSE);
            $this->db->set('DIAS_MORA_APLICADA', '0', FALSE);
        endif;

        //  if ($liquidacion['codigoConcepto'] == '5' || $liquidacion['codigoConcepto'] == '3'):
        // $this -> db -> set('EN_FIRME', 'S');
        //endif;
        if ($liquidacion_previa == 0):
            $this->db->set('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);


            $resultado = $this->db->insert('LIQUIDACION');
        else:
            $this->db->where('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->update('LIQUIDACION');
        endif;
        //#####BUGGER PARA LA CONSULTA ######
       //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
           // return FALSE;
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function update_resolucion($cod_fiscalizacion, $num) {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('NUM_LIQUIDACION', $num, FALSE);

        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $resultado = $this->db->update('RESOLUCION');
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionContrato($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla liquidacion
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        //   print_r($liquidacion);die();

        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('COD_CONCEPTO', $liquidacion['codigoConcepto'], FALSE);
        $this->db->set('NITEMPRESA', "TRIM('" . $liquidacion['nitEmpresa'] . "')", FALSE);
        $this->db->set('FECHA_INICIO', "TO_DATE('" . $liquidacion['fechaInicio'] . "','YYYY-MM-DD" . '"T"' . "HH24:MI:SS')", FALSE);
        $this->db->set('FECHA_FIN', "TO_DATE('" . $liquidacion['fechaFin'] . "','YYYY-MM-DD" . '"T"' . "HH24:MI:SS')", FALSE);
        $this->db->set('FECHA_LIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD-MM-YYYY')", FALSE);
        $this->db->set('FECHA_VENCIMIENTO', "TO_DATE('" . $liquidacion['fechaVencimiento'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('TOTAL_LIQUIDADO', $liquidacion['totalLiquidado'], FALSE);
        $this->db->set('TOTAL_INTERESES', $liquidacion['totalInteres'], FALSE);
        $this->db->set('SALDO_DEUDA', $liquidacion['saldoDeuda'], FALSE);
        $this->db->set('TOTAL_CAPITAL', $liquidacion['totalCapital'], FALSE);
        $this->db->set('COD_TIPOPROCESO', $liquidacion['tipoProceso'], FALSE);
        $this->db->set('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        $this->db->set('SALDO_CAPITAL', $liquidacion['saldoCapital'], FALSE);
        $this->db->set('SALDO_INTERES', $liquidacion['saldoInteres'], FALSE);

        if ($liquidacion['codigoConcepto'] == '5'):
            $this->db->set('FECHA_RESOLUCION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
            $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        endif;

        //  if ($liquidacion['codigoConcepto'] == '5' || $liquidacion['codigoConcepto'] == '3'):
        // $this -> db -> set('EN_FIRME', 'S');
        //endif;
        if ($liquidacion_previa == 0):
            $this->db->set('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);
            $this->db->set('VALOR_SANCION', $liquidacion['valorSancion']);
            $this->db->set('VALOR_OBLIGACION', $liquidacion['valorObligacion']);
            $this->db->set('SALDO_SANCION', $liquidacion['saldoSancion']);

            $resultado = $this->db->insert('LIQUIDACION');
        else:
            $this->db->where('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->update('LIQUIDACION');
        endif;
        //#####BUGGER PARA LA CONSULTA ######
        /*  $resultado = $this -> db -> last_query();
          echo $resultado; */
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionContrato2($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla liquidacion
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        //   print_r($liquidacion);die();

        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('COD_CONCEPTO', $liquidacion['codigoConcepto'], FALSE);
        $this->db->set('NITEMPRESA', "TRIM('" . $liquidacion['nitEmpresa'] . "')", FALSE);
        $this->db->set('FECHA_INICIO', "TO_DATE('" . $liquidacion['fechaInicio'] . "','YYYY-MM-DD" . '"T"' . "HH24:MI:SS')", FALSE);
        $this->db->set('FECHA_FIN', "TO_DATE('" . $liquidacion['fechaFin'] . "','YYYY-MM-DD" . '"T"' . "HH24:MI:SS')", FALSE);
        $this->db->set('FECHA_LIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD-MM-YYYY')", FALSE);
        $this->db->set('FECHA_VENCIMIENTO', "TO_DATE('" . $liquidacion['fechaVencimiento'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('TOTAL_LIQUIDADO', $liquidacion['totalLiquidado'], FALSE);
        $this->db->set('TOTAL_INTERESES', $liquidacion['totalInteres'], FALSE);
        $this->db->set('SALDO_DEUDA', $liquidacion['saldoDeuda'], FALSE);
        $this->db->set('TOTAL_CAPITAL', $liquidacion['totalCapital'], FALSE);
        $this->db->set('COD_TIPOPROCESO', $liquidacion['tipoProceso'], FALSE);
        $this->db->set('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        $this->db->set('SALDO_CAPITAL', $liquidacion['saldoCapital'], FALSE);
        $this->db->set('SALDO_INTERES', $liquidacion['saldoInteres'], FALSE);

        if ($liquidacion['codigoConcepto'] == '5'):
            $this->db->set('FECHA_RESOLUCION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
            $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        endif;

        //  if ($liquidacion['codigoConcepto'] == '5' || $liquidacion['codigoConcepto'] == '3'):
        // $this -> db -> set('EN_FIRME', 'S');
        //endif;

        $this->db->set('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);
        $this->db->set('VALOR_SANCION', $liquidacion['valorSancion']);
        $this->db->set('VALOR_OBLIGACION', $liquidacion['valorObligacion']);
        $this->db->set('SALDO_SANCION', $liquidacion['saldoSancion']);

        //  $this->db->where('NUM_LIQUIDACION', $liquidacion['numeroLiquidacion']);
        $this->db->where('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        $resultado = $this->db->update('LIQUIDACION');

        //#####BUGGER PARA LA CONSULTA ######
        /*  $resultado = $this -> db -> last_query();
          echo $resultado; */
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function consultarLiquidacion($codigoLiquidacion)
    /**
     * FunciÃ³n que retorna valores en la tabla liquidacion de un numero de liquidaciÃ³n especÃ­fico
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return boolean false - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('LIQUIDACION.COD_CONCEPTO, LIQUIDACION.NITEMPRESA, LIQUIDACION.NUM_LIQUIDACION, LIQUIDACION.COD_FISCALIZACION, CONCEPTOSFISCALIZACION.NOMBRE_CONCEPTO, EMPRESA.RAZON_SOCIAL, LIQUIDACION.TOTAL_CAPITAL, LIQUIDACION.TOTAL_INTERESES, LIQUIDACION.TOTAL_LIQUIDADO');
        $this->db->select('to_char("LIQUIDACION"."FECHA_VENCIMIENTO", ' . "'DD/MM/YYYY') AS FECHA_VENCIMIENTO", FALSE);
        $this->db->from('LIQUIDACION');
        $this->db->from('CONCEPTOSFISCALIZACION');
        $this->db->from('EMPRESA');
        $this->db->where('LIQUIDACION.NUM_LIQUIDACION', $codigoLiquidacion);
        $this->db->where('LIQUIDACION.COD_CONCEPTO = CONCEPTOSFISCALIZACION.COD_CPTO_FISCALIZACION');
        $this->db->where('LIQUIDACION.NITEMPRESA = EMPRESA.CODEMPRESA');
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() >= 0):
            $liquidacion = $resultado->row_array();
            return $liquidacion;
        else:
            return FALSE;
        endif;
    }

    function cargarLiquidacionSgva($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla maestro y detalle de liquidacion de aportes
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('CODEMPRESA', $liquidacion['nitEmpresa']);
        $this->db->set('FECHALIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('TOTAL_SENA', $liquidacion['totalCapital'], FALSE);
        $this->db->set('TOTALINTERESES', $liquidacion['totalInteres'], FALSE);
        $this->db->set('TOTAL_CONTRATOS', $liquidacion['totalAportes'], FALSE);
        $this->db->set('PAG_CONTRATOS_DESCONTAR', $liquidacion['pagoAportes'], FALSE);
        $this->db->set('INTERESES', $liquidacion['intereses'], FALSE);
        $this->db->set('TOTAL_DEUDA', $liquidacion['saldoDeuda'], FALSE);
        $this->db->set('ENTIDAD_PUBLICA', $liquidacion['entidadPublica']);
        $this->db->set('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);

        if ($liquidacion_previa == 0):
            $this->db->set('CODLIQUIDACIONCONTRATOS_P', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->insert('LIQ_SGVA');
        else:
            $this->db->where('CODLIQUIDACIONCONTRATOS_P', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->update('LIQ_SGVA');
        endif;
//#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionAporte($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla maestro y detalle de liquidacion de aportes
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('CODEMPRESA', $liquidacion['nitEmpresa']);
        $this->db->set('FECHALIQUIDACION', "TO_DATE('" . $liquidacion['fechaLiquidacion'] . "','DD/MM/YYYY')", FALSE);
        $this->db->set('TOTAL_SENA', $liquidacion['totalCapital'], FALSE);
        $this->db->set('TOTALINTERESES', $liquidacion['totalInteres'], FALSE);
        $this->db->set('TOTAL_APORTES', $liquidacion['totalAportes'], FALSE);
        $this->db->set('PAG_APORTES_DESCONTAR', $liquidacion['pagoAportes'], FALSE);
        $this->db->set('INTERESES', $liquidacion['intereses'], FALSE);
        $this->db->set('TOTAL_DEUDA', $liquidacion['saldoDeuda'], FALSE);
        $this->db->set('ENTIDAD_PUBLICA', $liquidacion['entidadPublica']);
        $this->db->set('COD_FISCALIZACION', $liquidacion['codigoFiscalizacion']);
        if ($liquidacion_previa == 0):
            $this->db->set('CODLIQUIDACIONAPORTES_P', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->insert('LIQ_APORTESPARAFISCALES');
        else:
            $this->db->where('CODLIQUIDACIONAPORTES_P', $liquidacion['numeroLiquidacion']);
            $resultado = $this->db->update('LIQ_APORTESPARAFISCALES');
        endif;

        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function actualizarLiquidacionAporte($adjuntos)
    /**
     * FunciÃ³n que actualiza los datos de observaciones y documentaciÃ³n en la tabla de liquidaciones aportes
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $adjuntos
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('OBSERVACIONES', $adjuntos['observaciones']);
        $this->db->set('DOCU_APORTADA', $adjuntos['documentacion']);
        $this->db->where('CODLIQUIDACIONAPORTES_P', $adjuntos['liquidacion']);
        $resultado = $this->db->update('LIQ_APORTESPARAFISCALES');
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function actualizarLiquidacionSGVA($adjuntos)
    /**
     * FunciÃ³n que actualiza los datos de observaciones y documentaciÃ³n en la tabla de liquidaciones aportes
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $adjuntos
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('OBSERVACIONES', $adjuntos['observaciones']);
        $this->db->set('DOCU_APORTADA', $adjuntos['documentacion']);
        $this->db->where('CODLIQUIDACIONCONTRATOS_P', $adjuntos['liquidacion']);
        $resultado = $this->db->update('LIQ_SGVA');
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionAporteDetalle($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta los valores anuales sobre la tabla de detalle para liquidaciones parafiscales.
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param int $ano
     * @param int $sueldos
     * @param int $valorsobresueldos
     * @param int $salariointegral
     * @param int $salarioespecie
     * @param int $supernumerarios
     * @param int $jornales
     * @param int $auxiliotransporte
     * @param int $horasextras
     * @param int $dominicales_festivos
     * @param int recargonocturno
     * @param int $viaticos
     * @param int $bonificaciones
     * @param int $comisiones
     * @param int $por_sobreventas
     * @param int $vacaciones
     * @param int $trab_domicilio
     * @param int $prima_tec_salarial
     * @param int $auxilio_alimentacion
     * @param int $prima_servicio
     * @param int $prima_localizacion
     * @param int $prima_vivienda
     * @param int $gast_representacion
     * @param int $prima_antiguedad
     * @param int $prima_extralegales
     * @param int $prima_vacaciones
     * @param int $prima_navidad
     * @param int $contratos_agricolas
     * @param int $remu_socios_industriales
     * @param int $hora_catedra
     * @param int $otros_pagos,
     * @param int $subcontrato,
     * @param string $codliquidacionaportes_p
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        //$this->db->trans_begin();
        //$this->db->trans_strict(TRUE);
        $this->db->set('VALORSUELDOS', $liquidacion['sueldos'], FALSE);
        $this->db->set('VALORSOBRESUELDOS', $liquidacion['sobresueldos'], FALSE);
        $this->db->set('SALARIOINTEGRAL', $liquidacion['salarioIntegral'], FALSE);
        $this->db->set('SALARIOESPECIE', $liquidacion['salarioEspecie'], FALSE);
        $this->db->set('SUPERNUMERARIOS', $liquidacion['supernumerarios'], FALSE);
        $this->db->set('JORNALES', $liquidacion['jornales'], FALSE);
        $this->db->set('AUXILIOTRANSPORTE', $liquidacion['auxilioTransporte'], FALSE);
        $this->db->set('HORASEXTRAS', $liquidacion['horasExtras'], FALSE);
        $this->db->set('DOMINICALES_FESTIVOS', $liquidacion['dominicales'], FALSE);
        $this->db->set('RECARGONOCTURNO', $liquidacion['recargoNocturno'], FALSE);
        $this->db->set('VIATICOS', $liquidacion['viaticos'], FALSE);
        $this->db->set('BONIFICACIONES', $liquidacion['bonificacionesHabituales'], FALSE);
        $this->db->set('COMISIONES', $liquidacion['comisiones'], FALSE);
        $this->db->set('POR_SOBREVENTAS', $liquidacion['porcentajeVentas'], FALSE);
        $this->db->set('VACACIONES', $liquidacion['vacaciones'], FALSE);
        $this->db->set('TRAB_DOMICILIO', $liquidacion['trabajoDomicilio'], FALSE);
        $this->db->set('PRIMA_TEC_SALARIAL', $liquidacion['primaTecnicaSalarial'], FALSE);
        $this->db->set('AUXILIO_ALIMENTACION', $liquidacion['auxilioAlimentacion'], FALSE);
        $this->db->set('PRIMA_SERVICIO', $liquidacion['primaServicios'], FALSE);
        $this->db->set('PRIMA_LOCALIZACION', $liquidacion['primaLocalizacion'], FALSE);
        $this->db->set('PRIMA_VIVIENDA', $liquidacion['primaVivienda'], FALSE);
        $this->db->set('GAST_REPRESENTACION', $liquidacion['gastosRepresentacion'], FALSE);
        $this->db->set('PRIMA_ANTIGUEDAD', $liquidacion['primaAntiguedad'], FALSE);
        $this->db->set('PRIMA_EXTRALEGALES', $liquidacion['primaProductividad'], FALSE);
        $this->db->set('PRIMA_VACACIONES', $liquidacion['primaVacaciones'], FALSE);
        $this->db->set('PRIMA_NAVIDAD', $liquidacion['primaNavidad'], FALSE);
        $this->db->set('CONTRATOS_AGRICOLAS', $liquidacion['contratosAgricolas'], FALSE);
        $this->db->set('REMU_SOCIOS_INDUSTRIALES', $liquidacion['remuneracionSocios'], FALSE);
        $this->db->set('HORA_CATEDRA', $liquidacion['horaCatedra'], FALSE);
        $this->db->set('OTROS_PAGOS', $liquidacion['otrosPagos'], FALSE);
        $this->db->set('SUBCONTRATO', $liquidacion['subcontratos'], FALSE);

        if($liquidacion['primer_mes_base']!=0){
            $this->db->set('PRIMER_MES_BASE', "TO_DATE('" .  $liquidacion['primer_mes_base']. "','DD/MM/YYYY')", FALSE);
          
        }      
        
        if ($liquidacion_previa == 0):
            $this->db->set('ANO', $liquidacion['ano'], FALSE);
            $this->db->set('CODLIQUIDACIONAPORTES_P', $liquidacion['liquidacion']);
            $this->db->set('PRIMER_MES_BASE', "TO_DATE('" .  $liquidacion['primer_mes_base']. "','DD/MM/YYYY')", FALSE);
            $resultado = $this->db->insert('LIQ_APORTESPARAFISCALES_DET');
        else:
            if( !empty( $liquidacion['primer_mes_base'])){
                $this->db->set('PRIMER_MES_BASE', "TO_DATE('" .  $liquidacion['primer_mes_base']. "','DD/MM/YYYY')", FALSE);
            }
            $this->db->where('ANO', $liquidacion['ano'], FALSE); 
            $this->db->where('CODLIQUIDACIONAPORTES_P', $liquidacion['liquidacion']);
            $resultado = $this->db->update('LIQ_APORTESPARAFISCALES_DET');
        endif;

        //#####BUGGER PARA LA CONSULTA ######
       // $resultado = $this -> db -> last_query();
        //echo $resultado;
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionSgvaDetalle($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta los valores anuales sobre la tabla de detalle para liquidaciones parafiscales.
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param int $ano
     * @param int $remu_socios_industriales
     * @param int $hora_catedra
     * @param int $otros_pagos,
     * @param int $subcontrato,
     * @param string $codliquidacionaportes_p
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);

        if ($liquidacion_previa == 0):
            $this->db->set('ANO', $liquidacion['ano'], FALSE);
            $this->db->set('CODLIQUIDACIONCONTRATOS_P', $liquidacion['liquidacion']);
            $resultado = $this->db->insert('LIQ_SGVA_DET');
        else:
            $this->db->where('ANO', $liquidacion['ano'], FALSE);
            $this->db->where('CODLIQUIDACIONCONTRATOS_P', $liquidacion['liquidacion']);
            $resultado = $this->db->update('LIQ_SGVA_DET');
        endif;

        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionMes($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta los valores mensuales sobre la tabla de detalle para liquidaciones parafiscales.
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param string $liquidacion,
     * @param int $liquidacion_previa,
     * @param string $periodo
     * @param int $valor
     * @param int $base
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        //$this->db->trans_begin();
      //  $this->db->trans_strict(TRUE);      
   
        $this->db->set('SUPERNUMERARIOS', $liquidacion['SUPERNUMERARIOS'], TRUE);
        $this->db->set('SALARIOESPECIE', $liquidacion['SALARIOESPECIE'], TRUE);
        $this->db->set('VALORSUELDOS', $liquidacion['VALORSUELDOS'], TRUE);
        $this->db->set('VALORSOBRESUELDOS', $liquidacion['VALORSOBRESUELDOS'], TRUE);
        $this->db->set('SALARIOINTEGRAL', $liquidacion['SALARIOINTEGRAL'], TRUE);
        $this->db->set('COMISIONES', $liquidacion['COMISIONES'], TRUE);
        $this->db->set('PORCENTAJEVENTAS', $liquidacion['PORCENTAJEVENTAS'], TRUE);
        $this->db->set('VACACIONES', $liquidacion['VACACIONES'], TRUE);
        $this->db->set('TRABAJODOMICILIO', $liquidacion['TRABAJODOMICILIO'], TRUE);       
        $this->db->set('CONTRATOSSUBCONTRATOS', $liquidacion['CONTRATOSSUBCONTRATOS'], TRUE);
        $this->db->set('PRIMASALARIAL', $liquidacion['PRIMASALARIAL'], TRUE); 
        $this->db->set('AUX_SUBSIDIOALIMENTACION', $liquidacion['AUX_SUBSIDIOALIMENTACION'], TRUE);
        $this->db->set('PRIMA_SERVICIO', $liquidacion['PRIMA_SERVICIO'], TRUE);
        $this->db->set('PRIMA_LOCALIZACION', $liquidacion['PRIMA_LOCALIZACION'], TRUE);
        $this->db->set('PRIMA_VIVIENDA', $liquidacion['PRIMA_VIVIENDA'], TRUE);
        $this->db->set('GASTOS_REPRESENTACION', $liquidacion['GASTOS_REPRESENTACION'], TRUE);
        $this->db->set('PRIMA_INCREMENTO_ANTIGUEDAD', $liquidacion['PRIMA_INCREMENTO_ANTIGUEDAD'], TRUE);
        $this->db->set('PRIMA_PRODUCTIVIDAD',$liquidacion['PRIMA_PRODUCTIVIDAD'], TRUE);
        $this->db->set('PRIMA_VACACIONES', $liquidacion['PRIMA_VACACIONES'], TRUE);
        $this->db->set('PRIMA_NAVIDAD', $liquidacion['PRIMA_NAVIDAD'], TRUE);
        $this->db->set('JORNALES',$liquidacion['JORNALES'], TRUE);
        $this->db->set('AUXILIOTRANSPORTE', $liquidacion['AUXILIOTRANSPORTE'], TRUE); 
        $this->db->set('HORASEXTRAS', $liquidacion['HORASEXTRAS'], TRUE);
        $this->db->set('DOMINICALES_FESTIVOS', $liquidacion['DOMINICALES_FESTIVOS'], TRUE);
        $this->db->set('RECARGONOCTURNO', $liquidacion['RECARGONOCTURNO'], TRUE);
        $this->db->set('VIATICOS', $liquidacion['VIATICOS'], TRUE);
        $this->db->set('BONIFICACIONES', $liquidacion['BONIFICACIONES'], TRUE);
        $this->db->set('CONTRATOS_AGRICOLAS', $liquidacion['CONTRATOS_AGRICOLAS'], TRUE);
        $this->db->set('REMU_SOCIOS_INDUSTRIALES',$liquidacion['REMU_SOCIOS_INDUSTRIALES'], TRUE);
        $this->db->set('HORA_CATEDRA', $liquidacion['HORA_CATEDRA'], TRUE); 
        $this->db->set('OTROS_PAGOS', $liquidacion['OTROS_PAGOS'], TRUE);
        $this->db->set('TOTAL', $liquidacion['TOTAL'], TRUE);
        $this->db->set('INTERES', $liquidacion['INTERES'], TRUE);
        //liquidacion

        if($liquidacion['VALOR']==0 ){
            $this->db->set('BASE', 0, TRUE);
            $this->db->set('VALOR',0, TRUE);
        }else{
            $this->db->set('BASE', $liquidacion['BASE_ANUAL'], TRUE);
            $this->db->set('VALOR',$liquidacion['VALOR'], TRUE);
        }
        
      
        
        if ($liquidacion_previa == 0):

            $this->db->set('NUM_LIQUIDACION',$liquidacion['NUM_LIQUIDACION'], TRUE);
            $this->db->set('PERIODO', $liquidacion['PERIODO'], TRUE);
            $resultado = $this->db->insert('LIQ_APORTESPARAFISCALES_MES');

        else:

            $this->db->set('NUM_LIQUIDACION',$liquidacion['NUM_LIQUIDACION'], TRUE);
            $this->db->set('PERIODO', $liquidacion['PERIODO'], TRUE);
            $this->db->where('NUM_LIQUIDACION',$liquidacion['NUM_LIQUIDACION'], TRUE);
            $this->db->where('PERIODO',$liquidacion['PERIODO'], TRUE);
            $resultado = $this->db->update('LIQ_APORTESPARAFISCALES_MES');

        endif;

        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
      //echo $resultado;
     
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            
            $this->db->trans_rollback();
            return False;
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function cargarLiquidacionMesSGVA($liquidacion, $liquidacion_previa, $periodo, $valor, $base)
    /**
     * FunciÃ³n que inserta los valores mensuales sobre la tabla de detalle para liquidaciones parafiscales.
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param string $liquidacion,
     * @param int $liquidacion_previa,
     * @param string $periodo
     * @param int $valor
     * @param int $base
     * @return boolean true - exito
     * @return string last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('VALOR', $valor, FALSE);
        $this->db->set('BASE', $base, FALSE);
        if ($liquidacion_previa == 0):

            $this->db->set('NUM_LIQUIDACION', $liquidacion);
            $this->db->set('PERIODO', $periodo);
            $resultado = $this->db->insert('LIQ_SGVA_MES');

        else:

            $this->db->where('NUM_LIQUIDACION', $liquidacion);
            $this->db->where('PERIODO', $periodo);
            $resultado = $this->db->update('LIQ_SGVA_MES');

        endif;

        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function getCabecerasMultasMinisterio($codigoMulta)
    /**
     * Funcion que devuelve la informaciÃ³n asociada a la multa de ministerio consultada a travÃ©s del cÃ³digo asociado
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de multa existente
     *
     * @param integer $codigoMulta
     * @return array $empresa
     * @return boolean false - error
     */ {
        $query = "SELECT
	MUL.COD_MULTAMINISTERIO,
	EMP.CODEMPRESA,
	EMP.RAZON_SOCIAL,
	REG.NOMBRE_REGIONAL,
	MUL.NRO_RESOLUCION,
	MUL.VALOR,
	MUL.RESPONSABLE,
	TIP.TIPOGESTION,
	to_char( MUL.FECHA_CREACION, 'DD/MM/YYYY' ) AS FECHA_CREACION,
	to_char( MUL.FECHA_EJECUTORIA, 'DD/MM/YYYY' ) AS FECHA_EJECUTORIA 
            FROM
                    MULTASMINISTERIO MUL
            LEFT JOIN EMPRESA EMP ON MUL.NIT_EMPRESA = EMP.CODEMPRESA 
            LEFT JOIN REGIONAL REG ON MUL.REGIONAL = REG.COD_REGIONAL 
            LEFT JOIN GESTIONCOBRO GES ON MUL.COD_GESTION_COBRO = GES.COD_GESTION_COBRO 
            LEFT JOIN TIPOGESTION TIP ON GES.COD_TIPOGESTION = TIP.COD_GESTION 
            WHERE
            MUL.COD_MULTAMINISTERIO = '{$codigoMulta}'";
        $resultado = $this->db->query($query);



        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() >= 0):
            $empresa = $resultado->row_array();
            return $empresa;
        else:
            return FALSE;
        endif;
    }

    function cargarMultaMinisterio($maestro, $detalle, $liquidacion_previa)
    /**
     * FunciÃ³n que almacena en DB los registros generados como maestro - detalle de las liquidaciones de una multa de ministerio
     * Inicia la transacciÃ³n de la inserciÃ³n en el encabezado, consulta el ID de la secuencia y se lo asigna al detalle.
     *
     * @param array $maestro
     * @param array $detalle
     * @param int $liquidacion_previa
     * @return boolean true
     * @return string $last_query - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        //insertar encabezado intereses
        $this->db->set('COD_MULTAMINISTERIO', $maestro['codigo_multa']);
        $this->db->set('VALOR_CAPITAL', $maestro['valor_multa'], FALSE);
        $this->db->set('FECHA_ELABORACION', "TO_DATE('" . $maestro['fecha_elaboracion'] . "','DD/MM/YY')", FALSE);
        $this->db->set('FECHA_EJECUTORIA', "TO_DATE('" . $maestro['fecha_ejecutoria'] . "','DD/MM/YY')", FALSE);
        $this->db->set('FECHA_LIQUIDACION', "TO_DATE('" . $maestro['fecha_liquidacion'] . "','DD/MM/YY')", FALSE);
        $this->db->set('TOTAL_DIAS_MORA', $maestro['total_dias_mora'], FALSE);
        $this->db->set('TOTAL_CAPITAL', $maestro['total_capital'], FALSE);
        $this->db->set('TOTAL_INTERESES', $maestro['total_interes'], FALSE);
        $this->db->set('VALOR_TOTAL', $maestro['total_valor'], FALSE);

        if ($liquidacion_previa == 0):
            $this->db->set('COD_INTERES_MULTA_MIN', $maestro['codigo_multa']);
            $this->db->insert('INTERES_MULTAMIN_ENC');
        else:
            $this->db->set('COD_INTERES_MULTA_MIN', $maestro['codigo_multa']);
            $this->db->where('COD_INTERES_MULTA_MIN', $maestro['codigo_multa']);
            $this->db->update('INTERES_MULTAMIN_ENC');
        endif;

        //Adquirir cod_interes_multamin_enc
        $id = $maestro['codigo_multa'];

        //insertar detalles mes a mes
        foreach ($detalle as $linea):
            $this->db->set('VALOR_CAPITAL', number_format($linea['valorCapital'], 2, '.', ''), FALSE);
            $this->db->set('VALOR_INTERESES', number_format($linea['valorInteres'], 2, '.', ''), FALSE);
            $this->db->set('VALOR_TOTAL', number_format($linea['valorTotal'], 2, '.', ''), FALSE);
            // foreach($linea as $value):
            // endforeach;
            if ($liquidacion_previa == 0):
                $this->db->set('MES', $linea['mes']);
                $this->db->set('ANNO', $linea['anno']);
                $this->db->set('COD_INTERES_MULTAMIN', $id);
                $this->db->insert('INTERESES_MULTAMIN_DET');
            else:
                $this->db->select('COD_INTERES_MULTAMIN, MES, ANNO');
                $this->db->from('INTERESES_MULTAMIN_DET');
                $this->db->where('COD_INTERES_MULTAMIN', $id);
                $this->db->where('MES', $linea['mes']);
                $this->db->where('ANNO', $linea['anno']);
                $resultado = $this->db->get();
                if ($resultado->num_rows() > 0):
                    $this->db->where('COD_INTERES_MULTAMIN', $id);
                    $this->db->where('MES', $linea['mes']);
                    $this->db->where('ANNO', $linea['anno']);
                    $this->db->update('INTERESES_MULTAMIN_DET');
                else:
                    $this->db->set('MES', $linea['mes']);
                    $this->db->set('ANNO', $linea['anno']);
                    $this->db->set('COD_INTERES_MULTAMIN', $id);
                    $this->db->insert('INTERESES_MULTAMIN_DET');
                endif;
            //#####BUGGER PARA LA CONSULTA ######
            // $resultado = $this -> db -> last_query();
            // echo $resultado; die();
            //#####BUGGER PARA LA CONSULTA ######
            endif;
        endforeach;
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function getCombinacionTipoAportante($value = 1)
    /**
     * FunciÃ³n que consulta los tipos de combinaciÃ³n de correspondencia para aportantes
     * Esta funciÃ³n provee los tipos para mostrar las opciones en pantalla y si no se han cargado aÃºn por parametrizaciÃ³n devuelve error
     *
     * @return array $tipos
     * @return string $tipos - error
     */ {
        $this->db->select('COD_COMB_TIP_APORTANTE, DESCRIPCION_COMB_TIPO');
        $this->db->where('TIPO_COMB', $value);
        $this->db->order_by('COD_COMB_TIP_APORTANTE', 'ASC');
        $resultado = $this->db->get('COMBINACION_TIPO_APORTANTE');

        if ($resultado->num_rows() > 0):
            $tipos = $resultado->result_array();
            return $tipos;
        else:
            $tipos = 'Consulta sin datos en los tipos de combinaciÃ³n';
        endif;
    }

    function getCombinacionRespuesta($codigoCombinacion)
    /**
     * FunciÃ³n que retorna la respuesta segÃºn el codigo de la combinaciÃ³n seleccionado por el usuario
     * Esta funciÃ³n provee la respuesta en HTML almacendao en la DB
     *
     * @param integer $codigoCombinacion
     * @return array $respuesta
     * @return boolean False - error
     */ {
        $this->db->select('COD_COMB_TIP_APORTANTE, TEXTO_COMBINATORIO');
        $this->db->from('COMBINACION_TIPO_APORTANTE');
        $this->db->where('COD_COMB_TIP_APORTANTE', $codigoCombinacion);
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $respuesta = $resultado->row_array();
            return $respuesta;
        else:
            return FALSE;
        endif;
    }

    function getFechaVisita($codigoGestion)
    /**
     * FunciÃ³n que retorna la fecha en la cual fue visitada la empresa para la generaciÃ³n de la comunicaciÃ³n de aportante
     * Si la empresa consultada no tiene fecha de visita devuelve error
     *
     * @param integer $codigoGestion
     * @return array $fecha
     * @return boolean False - error
     */ {
        $this->db->select('INF.COD_GESTION_COBRO');
        $this->db->select('to_char("INF"."FECHA_DOCUMENTO", ' . "'DD/MM/YYYY') AS FECHA_DOCUMENTO", FALSE);
        $this->db->from('INFORMEVISITA "INF"');
        $this->db->join('GESTIONCOBRO "GC"', 'GC.COD_GESTION_COBRO = INF.COD_GESTION_COBRO');
        $this->db->where('GC.COD_FISCALIZACION_EMPRESA', $codigoGestion);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $fecha = $resultado->row_array();
            return $fecha;
        else:
            return FALSE;
        endif;
    }

    function getInfoUsuarios($usuario)
    /**
     * FunciÃ³n que retorna la informaciÃ³n asociada al usuario logeado.
     * Esta funciÃ³n no debe retornar error, pues no se pueden logear usuarios no identificados en la DB
     *
     * @param integer $usuario
     * @return array $usuario
     * @return boolean False - error
     */ {
        $this->db->select('USU.IDUSUARIO, USU.COD_REGIONAL, REG.COD_REGIONAL, REG.NOMBRE_REGIONAL, REG.CEDULA_DIRECTOR, fn_Nombre_Usuario(CEDULA_DIRECTOR) AS Nombre_Director, REG.CEDULA_COORDINADOR_RELACIONES, fn_Nombre_Usuario(CEDULA_COORDINADOR_RELACIONES) AS NOMBRE_COORDINADOR_RELACIONES, REG.COD_CIUDAD, MUN.NOMBREMUNICIPIO');
        $this->db->from('USUARIOS "USU"');
        $this->db->from('REGIONAL "REG"');
        $this->db->from('MUNICIPIO "MUN"');
        $this->db->where('USU.COD_REGIONAL = REG.COD_REGIONAL');
        $this->db->where('REG.COD_REGIONAL = MUN.CODMUNICIPIO');
        $this->db->where('USU.IDUSUARIO', $usuario);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $usuario = $resultado->row_array();
            return $usuario;
        else:
            return FALSE;
        endif;
    }

    function getEmpresa($nitEmpresa)
    /**
     * FunciÃ³n que retorna la informaciÃ³n de la empresa consultadapor nit
     * Si la empresa consultada no se encuentra registrada en la DB, reporta un error lÃ³gico
     *
     * @param integer $nitEmpresa
     * @return array $empresa
     * @return boolean False - error
     */ {
        $this->db->select('EMP.CODEMPRESA, EMP.RAZON_SOCIAL, EMP.DIRECCION, EMP.TELEFONO_FIJO, EMP.REPRESENTANTE_LEGAL, EMP.RAZON_SOCIAL, EMP.COD_REGIONAL, EMP.COD_PAIS, EMP.COD_DEPARTAMENTO, EMP.COD_MUNICIPIO, MUN.CODMUNICIPIO, MUN.NOMBREMUNICIPIO, DEP.COD_DEPARTAMENTO, DEP.NOM_DEPARTAMENTO');
        $this->db->from('EMPRESA "EMP"');
        $this->db->join('MUNICIPIO "MUN"', 'EMP.COD_MUNICIPIO = MUN.CODMUNICIPIO', 'LEFT');
        $this->db->join('DEPARTAMENTO "DEP"', 'EMP.COD_DEPARTAMENTO = DEP.COD_DEPARTAMENTO', 'LEFT');
        // $this-> db -> join('PAIS "PAI"','EMP.COD_PAIS = PAI.CODPAIS');
        $this->db->where('CODEMPRESA', $nitEmpresa);
        //$this->db->where('EMP.COD_DEPARTAMENTO = MUN.COD_DEPARTAMENTO');
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $empresa = $resultado->row_array();
            return $empresa;
        else:
            return FALSE;
        endif;
    }

    function getCabecerasSoportesLiquidacion($codigoConcepto)
    /**
     * FunciÃ³n que retorna la informaciÃ³n asociada al soporte de liquidaciÃ³n consultado por el codigo de gestiÃ³n
     * Si el cÃ³digo de concepto no tiene liquidaciones asociadas no deberia reportar datos (PENDIENTE)
     *
     * @param integer $codigoConcepto
     * @return array $empresa
     * @return boolean False - error
     */ {
        $this->db->select('L.NUM_LIQUIDACION, L.COD_CONCEPTO, L.NITEMPRESA, L.COD_FISCALIZACION, E.RAZON_SOCIAL, C.NOMBRE_CONCEPTO');
        $this->db->select('to_char("L"."FECHA_LIQUIDACION", ' . "'DD/MM/YYYY') AS FECHA_LIQUIDACION", FALSE);
        $this->db->from('LIQUIDACION L');
        $this->db->join('EMPRESA E', 'L.NITEMPRESA = E.CODEMPRESA');
        $this->db->join('CONCEPTOSFISCALIZACION C', 'L.COD_CONCEPTO = C.COD_CPTO_FISCALIZACION');
        $this->db->where('L.COD_FISCALIZACION', $codigoConcepto);
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $empresa = $resultado->row_array();
            return $empresa;
        endif;
    }

    function loadSoportesLiquidacion($liquidacion, $nis, $fecha, $radicado, $archivo, $fiscalizador)
    /**
     * FunciÃ³n para cargar los soportes de liquidaciÃ³n en la DB y en el repo de uploads
     *
     * @param string $liquidacion
     * @param string $nis
     * @param string $fecha
     * @param string $radicado
     * @param string $archivo
     * @param string $fiscalizador
     * @return boolean $empresa
     */ {
        $this->db->set('sl.NUM_LIQUIDACION', $liquidacion);
        $this->db->set('sl.NRO_RADICADO', $radicado);
        $this->db->set('sl.FECHA_RADICADO', "TO_DATE('" . $fecha . "','DD/MM/YYYY')", FALSE);
        $this->db->set('sl.NOMBRE_ARCHIVO', $archivo);
        $this->db->set('sl.NOMBRE_RADICADOR', $fiscalizador);
        $this->db->set('sl.NIS', $nis);
        $resultado = $this->db->insert('SOPORTE_LIQUIDACION "sl"');
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        return $resultado;
    }

    function getTasaParametrizada()
    /**
     * FunciÃ³n que retorna la informaciÃ³n de la tasa parametrizada para los calculos de multas de ministerio y FIC
     *
     * @return array $tasaInteres
     * @return boolean False - error
     */ {
        $this->db->select('VALOR_TASA');
        $this->db->from('TASA_PARAMETRIZADO');
        $this->db->where('IDTASA = 3'); // Basado en el parametro creado Â¡cuidado en la migraciÃ³n!
        $this->db->where('IDESTADO = 1');
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $tasa = $resultado->row_array();
            return $tasa;
        else:
            return FALSE;
        endif;
    }

    function getTasaInteresSF_actual()
    /**
     * FunciÃ³n que retorna la informaciÃ³n de la ultima tasa de la superintendencia almacenada en la DB
     *
     * @return array $tasaInteres
     * @return boolean False - error
     */ {
        $resultado = $this->db->query('
        SELECT * FROM (
            SELECT s.ID_TASA_SUPERINTENDENCIA, s.TASA_SUPERINTENDENCIA, s.VIGENCIA_DESDE, s.VIGENCIA_HASTA, s.FECHACREACION
            FROM TASA_SUPERINTENDENCIA s
            ORDER BY s.FECHACREACION DESC
        )
        WHERE ROWNUM = 1');
        if ($resultado->num_rows > 0):
            $tasaInteres = $resultado->row_array();
            return $tasaInteres;
        endif;
    }

    function getTasaInteresSF_mes($mes, $anno)
    /**
     * FunciÃ³n que retorna la informaciÃ³n de la tasa de la superintendencia para el mes y aÃ±o consultado
     * Si el mes y aÃ±o consultados no concuerdan con una tasa registra reporta error de tasa
     *
     * @param integer $mes
     * @param integer $anno
     * @return integer $tasa
     * @return boolean False - error
     */ {
        $this->db->select('TASA_SUPERINTENDENCIA');
        $this->db->from('TASA_SUPERINTENDENCIA');
        $this->db->where('VIGENCIA_DESDE >=', "to_date('" . $mes . '/' . $anno . "'," . "'MM/YYYY')", FALSE);
        $this->db->where('ID_TIPO_TASA', 1);
        $this->db->order_by('VIGENCIA_DESDE', 'ASC');
        $resultado = $this->db->get();
         // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        if ($resultado->num_rows() > 0):
            $tasa = $resultado->row_array();
            return $tasa;
        else:
            return FALSE;
        endif;
    }

   /* function getAporte_mes($nitEmpresa, $concepto, $subconcepto, $periodo)
    /**
     * FunciÃ³n que retorna si la empresa consultada con el nit, tiene aportes regulares por el concepto y subconcepto de pago, enviado en el periodo selecionado (yyyy-mm)
     * Si la empresa consultada no tiene aportes en el periodo retorna un 0
     *
     * @param varchar $nitEmpresa
     * @param integer $concepto
     * @param integer $subconcepto
     * @param varchar $periodo
     * @return integer $cuota
     * @return boolean false - error
     *//* {
        $cuota = 0;
        $this->db->select('VALOR_PAGADO, PERIODO_PAGADO');
        $this->db->from('PAGOSRECIBIDOS');
        $this->db->where('COD_CONCEPTO', $concepto, FALSE);
        $this->db->where('COD_SUBCONCEPTO', $subconcepto, FALSE);
        $this->db->where('NITEMPRESA', $nitEmpresa);
        $this->db->where('PERIODO_PAGADO', $periodo);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        return array('VALOR_PAGADO' => 0);
        if ($resultado->num_rows() > 0):
            $cuota = $resultado->row_array();
            return $cuota;
        else:
            return FALSE;
        endif;
    }
*/

    function getAporte_mes($nitEmpresa, $concepto,$periodo=null,$cod_fiscalizacion)
    /**
     * FunciÃ³n que retorna si la empresa consultada con el nit, tiene aportes regulares por el concepto y subconcepto de pago, enviado en el periodo selecionado (yyyy-mm)
     * Si la empresa consultada no tiene aportes en el periodo retorna un 0
     *
     * @param varchar $nitEmpresa
     * @param integer $concepto
     * @param integer $subconcepto
     * @param varchar $periodo
     * @return integer $cuota
     * @return boolean false - error
     */ {

        
        $cuota = 0;
        $this->db->select('FP.COD_FISCALIZACION ,PR.VALOR_PAGADO , PR.PERIODO_PAGADO,FP.COD_PAGO,PR.FECHA_PAGO');
        $this->db->from('FISCALIZACION_PAGO  FP');
        $this->db->join('PAGOSRECIBIDOS PR', 'FP.COD_PAGO = PR.COD_PAGO');
        $this->db->where('PR.COD_CONCEPTO', $concepto, FALSE);
        $this->db->where('FP.COD_FISCALIZACION', $cod_fiscalizacion, FALSE);
        $this->db->where('PR.NITEMPRESA', $nitEmpresa);

        if (!empty($periodo)) {
            $this->db->where('PR.PERIODO_PAGADO', $periodo);
        }
 
     //   $this->db->where('PR.PERIODO_PAGADO', $periodo);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
       /* $resultado = $this -> db -> last_query();
         echo $resultado; die();*/
        //#####BUGGER PARA LA CONSULTA ######
       // return array('VALOR_PAGADO' => 0);
      //  if ($resultado->num_rows() > 0):
           // $cuota = $resultado->row_array();
            $cuota = $resultado->result_array();
           // $datos->result_array();
            return $cuota;
       /* else:
            return FALSE;
        endif;*/
    }











    function getAporte_Fic($nitEmpresa, $concepto, $subconcepto, $periodo, $tipoFic, $contrato = FALSE)
    /**
     * FunciÃ³n que retorna si la empresa consultada con el nit, tiene aportes por Fic Presuntivo Anuales
     * Si la empresa consultada no tiene aportes en el periodo retorna un 0
     *
     * @param varchar $nitEmpresa
     * @param integer $concepto
     * @param integer $subconcepto
     * @param varchar $periodo
     * @return integer $cuota
     */ {

        $cuota = 0;
        $this->db->select('VALOR_PAGADO, PERIODO_PAGADO');
        $this->db->from('PAGOSRECIBIDOS');
        $this->db->where('COD_CONCEPTO', $concepto, FALSE);
        $this->db->where('COD_SUBCONCEPTO', $subconcepto, FALSE);
        $this->db->where('NITEMPRESA', $nitEmpresa);
        $this->db->where('PERIODO_PAGADO', $periodo);
        $this->db->where('PERIODO_PAGADO', $tipoFic);
        if (!empty($contrato)) {
            $this->db->where('NRO_LICENCIA_CONTRATO', $contrato);
        }

        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $cuota = $resultado->row_array();
            return $cuota;
        else:
            return FALSE;
        endif;
    }

    function getLiquidacionMultaMinisterio($codigoMulta)
    /**
     * Funcion que devuelve la informaciÃ³n asociada a la multa consultada
     * Solo funcional si se lanza la consulta a travÃ©s de un codigo de multa existente
     *
     * @param integer $codigoMulta
     * @return array $liquidacion
     * @return boolean false - error
     */ {
        $this->db->select('IME.COD_INTERES_MULTA_MIN, IME.COD_MULTAMINISTERIO, IME.TOTAL_CAPITAL, IME.TOTAL_INTERESES, IME.VALOR_TOTAL, MM.NIT_EMPRESA, EMP.RAZON_SOCIAL');
        $this->db->select('to_char("IME"."FECHA_LIQUIDACION", ' . "'DD/MM/YYYY') AS FECHA_LIQUIDACION", FALSE);
        $this->db->from('INTERES_MULTAMIN_ENC "IME"');
        $this->db->join('MULTASMINISTERIO "MM"', 'IME.COD_MULTAMINISTERIO = MM.COD_MULTAMINISTERIO');
        $this->db->join('EMPRESA "EMP"', 'MM.NIT_EMPRESA = EMP.CODEMPRESA');
        $this->db->where('IME.COD_MULTAMINISTERIO', $codigoMulta, FALSE);
        $this->db->order_by('IME.FECHA_LIQUIDACION', 'DESC');
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $liquidacion = $resultado->row_array();
            return $liquidacion;
        else:
            return FALSE;
        endif;
    }

    // FUNCIONES DE SIMULACÃ“N SGVA---------------------------------------------------------

    function buscarEmpresaSgva($nit)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de la empresa en SGVA
     *
     * @param string $nit
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('CODEMPRESA, RAZON_SOCIAL, REPRESENTANTE_LEGAL, COD_REGIONAL');
        $this->db->from('EMPRESA');
        $this->db->where('CODEMPRESA', $nit);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $empresa = $resultado->row_array();
            return $empresa;
        else:
            return FALSE;
        endif;
    }

    function datosSGVA($cod)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de la empresa en SGVA
     *
     * @param string $cod
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('*');
        $this->db->from('LIQUIDACION');
        $this->db->where('COD_FISCALIZACION', $cod);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $datos = $resultado->row_array();
            return $datos;
        else:
            return FALSE;
        endif;
    }

    function buscarRegulacionesSgva($nit, $fechaInicio, $fechaFin)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de contratos de la empresa en SGVA
     *
     * @param string $nit
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('NUMERORESOLUCION, TRABAJADORES, CUOTA,');
        $this->db->select('to_char("FECHARESOLUCION", ' . "'DD/MM/YYYY') AS FECHARESOLUCION", FALSE);
        $this->db->select('to_char("EJECUTORIA", ' . "'DD/MM/YYYY') AS EJECUTORIA", FALSE);
        $this->db->select('to_char("FECHAINICIAL", ' . "'DD/MM/YYYY') AS FECHAINICIAL", FALSE);
        $this->db->select('to_char("FECHAFINAL", ' . "'DD/MM/YYYY') AS FECHAFINAL", FALSE);
        $this->db->from('REGULACIONES');
        $condicion = "TRUNC(FECHAINICIAL) >= TRUNC(TO_DATE('" . $fechaInicio . "', 'DD/MM/YYYY')) AND TRUNC(FECHAFINAL) <= TRUNC(TO_DATE('" . $fechaFin . "', 'DD/MM/YYYY')) AND CODEMPRESA = '" . $nit . "'";
        $this->db->where($condicion);
        $this->db->order_by('NUMERORESOLUCION', 'ASC');
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $datos->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $regulacion):
                $tmp[] = array("NUMERORESOLUCION" => $regulacion['NUMERORESOLUCION'], "TRABAJADORES" => $regulacion['TRABAJADORES'], "CUOTA" => $regulacion['CUOTA'], "FECHARESOLUCION" => $regulacion['FECHARESOLUCION'], "EJECUTORIA" => $regulacion['EJECUTORIA'], "FECHAINICIAL" => $regulacion['FECHAINICIAL'], "FECHAFINAL" => $regulacion['FECHAFINAL']);
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }

    function buscarContratosSgva($nit, $fechaInicio, $fechaFin)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de contratos de la empresa en SGVA
     *
     * @param string $nit
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('TIPOCONRATO, APRENDIZ, DOCUMENTO, TOTALDIAS');
        $this->db->select('to_char("ACUERDO", ' . "'DD/MM/YYYY') AS ACUERDO", FALSE);
        $this->db->select('to_char("FECHAINICIAL", ' . "'DD/MM/YYYY') AS FECHAINICIAL", FALSE);
        $this->db->select('to_char("FECHAFINAL", ' . "'DD/MM/YYYY') AS FECHAFINAL", FALSE);
        $this->db->from('CONTRATOS');
        $condicion = "TRUNC(FECHAINICIAL) >= TRUNC(TO_DATE('" . $fechaInicio . "', 'DD/MM/YYYY')) AND TRUNC(FECHAFINAL) <= TRUNC(TO_DATE('" . $fechaFin . "', 'DD/MM/YYYY')) AND CODEMPRESA = '" . $nit . "'";
        $this->db->where($condicion);
        $this->db->order_by('APRENDIZ', 'ASC');
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $datos->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $contrato):
                $tmp[] = array("TIPOCONTRATO" => $contrato['TIPOCONRATO'], "APRENDIZ" => $contrato['APRENDIZ'], "DOCUMENTO" => $contrato['DOCUMENTO'], "TOTALDIAS" => $contrato['TOTALDIAS'], "ACUERDO" => $contrato['ACUERDO'], "FECHAINICIAL" => $contrato['FECHAINICIAL'], "FECHAFINAL" => $contrato['FECHAFINAL']);
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }

    function buscarMonetizacionSgva($nit, $fechaInicio, $fechaFin)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de monetizaciÃ³n de la empresa en SGVA
     *
     * @param string $nit
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('PERIODOPAGO, TRANSACCION, PAGONETO, INTERESES');
        $this->db->select('to_char("FECHAPAGO", ' . "'DD/MM/YYYY') AS FECHAPAGO", FALSE);
        $this->db->from('MONETIZACION');
        $condicion = "TO_DATE(PERIODOPAGO, 'MM-YYYY') BETWEEN TO_DATE('" . $fechaInicio . "', 'MM-YYYY') AND TO_DATE('" . $fechaFin . "', 'MM-YYYY') AND CODEMPRESA = '" . $nit . "'";
        $this->db->where($condicion);
        $this->db->order_by('FECHAPAGO', 'ASC');
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $datos->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $monetizacion):
                $tmp[] = array("PERIODOPAGO" => $monetizacion['PERIODOPAGO'], "FECHAPAGO" => $monetizacion['FECHAPAGO'], "TRANSACCION" => $monetizacion['TRANSACCION'], "PAGONETO" => $monetizacion['PAGONETO'], "INTERESES" => $monetizacion['INTERESES']);
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }

    function buscarResumenSgva($periodo, $nit)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de los resumenes por periodos de las  empresa de SGVA
     *
     * @param string $periodo
     * @param string $nit
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('DIASASIGNADOS,DIASCUMPLIDOS, MONETIZACION');
        $this->db->from('RESUMEN');
        $this->db->where('CODEMPRESA', $nit);
        $this->db->where('ANNO', $periodo);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $empresa = $resultado->row_array();
            return $empresa;
        else:
            return FALSE;
        endif;
    }

    function cargarFiscalizacionSgva($codigoAsignacion, $codigoConcepto, $codigoTipoGestion, $periodoInicial, $periodoFinal)
    /**
     * FunciÃ³n que inserta valores en la tabla fiscalizacÃ³n
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param string $codigoAsignacion
     * @param string $codigoConcepto
     * @param string $periodoInicial
     * @param string $periodoFinal
     * @return boolean true - exito
     * @return string false - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('COD_ASIGNACION_FISC', $codigoAsignacion, FALSE);
        $this->db->set('COD_CONCEPTO', $codigoConcepto, FALSE);
        $this->db->set('COD_TIPOGESTION', $codigoTipoGestion, FALSE);
        $this->db->set('PERIODO_INICIAL', "TO_DATE('" . $periodoInicial . "','DD/MM/YYYY')", FALSE);
        $this->db->set('PERIODO_FINAL', "TO_DATE('" . $periodoFinal . "','DD/MM/YYYY')", FALSE);
        $resultado = $this->db->insert('FISCALIZACION');
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n

        if ($this->db->trans_status() === FALSE):

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            return $datos = TRUE;

        endif;
    }

    function consultarFiscalizacionSgva($codigoAsignacion, $codigoConcepto)
    /**
     * Funcion que devuelve la informaciÃ³n de una fiscalizaciÃ³n creada por el proceso de liquidaciÃ³n de contratos de aprendizaje
     *
     * @param string $codigoAsignacion
     * @param string $codigoConcepto
     * @return $fiscalizacion
     * @return boolean false - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('NRO_EXPEDIENTE, COD_FISCALIZACION');
        $this->db->from('FISCALIZACION');
        $this->db->where('COD_ASIGNACION_FISC', $codigoAsignacion, FALSE);
        $this->db->where('COD_CONCEPTO', $codigoConcepto, FALSE);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n

        if ($this->db->trans_status() === FALSE):

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();

            if ($resultado->num_rows() > 0):
                $fiscalizacion = $resultado->row_array();
                return $fiscalizacion;

            else:

                return $fiscalizacion = FALSE;

            endif;

        endif;
    }

    function getSalarioMinimoVigente($anno)
    /**
     * Funcion que devuelve el Salario MÃ­nimo Legal Vigente para el aÃ±o parametrizado
     *
     * @param integer $anno
     * @return array $smlv
     * @return boolean false - error
     */ {

        $this->db->select('SALARIO_MINIMO');
        $this->db->from('HISTORICOSALARIOMINIMO_UVT');
        $this->db->where('ANNO', $anno, FALSE);
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $smlv = $resultado->row_array();
            return $smlv;
        else:
            return FALSE;
        endif;
    }

    function buscarfiscalizacion($fis) {

        $this->db->select('COD_FISCALIZACION');
        $this->db->from('LIQUIDACION');
        $this->db->where('COD_FISCALIZACION', $fis, FALSE);
        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0):
            $cod = $resultado->row_array();
            return $cod;
        else:
            return FALSE;
        endif;
    }

    function getSMLV_rango($anno_inicial, $anno_final)
    /**
     * Funcion que devuelve el Salario MÃ­nimo Legal Vigente para un rango de fechas
     *
     * @param integer $anno
     * @return array $smlv
     * @return boolean false - error
     */ {
        $this->db->select(' ANNO, SALARIO_MINIMO');
        $this->db->from('HISTORICOSALARIOMINIMO_UVT');
        $where = "ANNO >= " . $anno_inicial . " and ANNO <= " . $anno_final;
        $this->db->where($where);
        $resultado = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($resultado->num_rows() > 0):
            $smlv = $resultado->row_array();
            return $smlv;
        else:
            return FALSE;
        endif;
    }

    // FUNCIONES DE AUTOCOMPLETAR---------------------------------------------------------

    function buscarConceptos()
    /**
     * FunciÃ³n que devuelve los conceptos de fiscalizaciÃ³n para los formularios de consulta.
     *
     * @param string $nit
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('COD_CPTO_FISCALIZACION, NOMBRE_CONCEPTO');
        $condicion = 'COD_CPTO_FISCALIZACION not in (3, 5)';
        $this->db->where($condicion);
        $this->db->order_by('NOMBRE_CONCEPTO', 'ASC');
        $datos = $this->db->get('CONCEPTOSFISCALIZACION');
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->result_array();

            if (!empty($datos)):

                $tmp = NULL;

                foreach ($datos as $nit):

                    $tmp[] = array("value" => $nit['COD_CPTO_FISCALIZACION'], "label" => $nit['NOMBRE_CONCEPTO']);

                endforeach;

                return $datos = $tmp;

            else:

                return $datos = FALSE;

            endif;

        endif;
    }

    function buscarNits($nit, $regional)
    /**
     * FunciÃ³n que devuelve nits para los formularios de consulta. Condiciona la busqueda a la regional asociada al usuario y solo muestra los primeros 500 resultados como lÃ­mite
     *
     * @param string $nit
     * @param string $regional
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('CODEMPRESA, RAZON_SOCIAL');
        $this->db->where('COD_REGIONAL', $regional);
        if (!empty($nit)):
            $this->db->like('CODEMPRESA', $nit, 'after');
        endif;
        $this->db->order_by('CODEMPRESA', 'ASC');
        $this->db->limit(500);
        $datos = $this->db->get('EMPRESA');
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->result_array();

            if (!empty($datos)):

                $tmp = NULL;

                foreach ($datos as $nit):

                    $tmp[] = array("value" => $nit['CODEMPRESA'], "label" => $nit['CODEMPRESA'] . " :: " . $nit['RAZON_SOCIAL']);

                endforeach;

                return $datos = $tmp;

            else:

                return $datos = FALSE;

            endif;

        endif;
    }

    function buscarRazonSocial($nombre, $regional)
    /**
     * FunciÃ³n que devuelve razon social para los formularios de consulta. Limita a las razones sociales registradasen la regional del usuario y solo muestra los primeros 500 resultados como lÃ­mite
     *
     * @param string $nombre
     * @param string $regional
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('CODEMPRESA, RAZON_SOCIAL');
        $this->db->where('COD_REGIONAL', $regional);
        if (!empty($nombre)):
            $this->db->like('RAZON_SOCIAL', $nombre, 'after');
        endif;
        $this->db->order_by('RAZON_SOCIAL', 'ASC');
        $this->db->limit(500);
        $datos = $this->db->get('EMPRESA');
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->result_array();

            if (!empty($datos)):

                $tmp = NULL;

                foreach ($datos as $nombre):

                    $tmp[] = array("value" => $nombre['RAZON_SOCIAL'], "label" => $nombre['CODEMPRESA'] . " :: " . $nombre['RAZON_SOCIAL']);

                endforeach;

                return $datos = $tmp;

            else:

                return $datos = FALSE;

            endif;

        endif;
    }

    function buscarRepresentante($nombre, $regional)
    /**
     * FunciÃ³n que devuelve representantes para los formularios de consulta. Solo muestra las empresas registradas en la regional del usuario y los primeros 500 resultados como lÃ­mite
     *
     * @param string $nombre
     * @param string $regional
     * @return array $datos
     * @return boolean false - error
     */ {
        $this->db->select('CODEMPRESA, RAZON_SOCIAL, REPRESENTANTE_LEGAL');
        $this->db->where('COD_REGIONAL', $regional);
        if (!empty($nombre)):
            $this->db->like('REPRESENTANTE_LEGAL', $nombre, 'after');
        endif;
        $this->db->order_by('REPRESENTANTE_LEGAL', 'ASC');
        $this->db->limit(500);
        $datos = $this->db->get('EMPRESA');
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->result_array();

            if (!empty($datos)):

                $tmp = NULL;

                foreach ($datos as $nombre):

                    $tmp[] = array("value" => $nombre['REPRESENTANTE_LEGAL'], "label" => $nombre['REPRESENTANTE_LEGAL'] . " :: " . $nombre['RAZON_SOCIAL']);

                endforeach;

                return $datos = $tmp;

            else:

                return $datos = FALSE;

            endif;

        endif;
    }

    // FINAL FUNCIONES DE AUTOCOMPLETAR---------------------------------------------------------

    function getLiquidaciones($cod_fiscalizacion = null)
    /**
     * FunciÃ³n que devuelve las liquidaciones asociadas al usuario
     *
     * @param string $cod_fiscalizacion
     * @return obj $dato
     * @return array $dato - error
     */ {
        $this->db->select('E.CODEMPRESA, E.RAZON_SOCIAL, F.COD_CONCEPTO, CF.NOMBRE_CONCEPTO, F.COD_FISCALIZACION, L.NUM_LIQUIDACION');
        $this->db->join('FISCALIZACION F', 'F.COD_FISCALIZACION = L.COD_FISCALIZACION');
        $this->db->join('ASIGNACIONFISCALIZACION AF', 'AF.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC');
        $this->db->join('EMPRESA E', 'E.CODEMPRESA = AF.NIT_EMPRESA');
        $this->db->join('CONCEPTOSFISCALIZACION CF', 'CF.COD_CPTO_FISCALIZACION = F.COD_CONCEPTO');

        if ($cod_fiscalizacion != NULL):

            $this->db->where('F.COD_FISCALIZACION', $cod_fiscalizacion);
            $this->db->where('L.EN_FIRME', 'N');
            $this->db->where('L.BLOQUEADA', '0');
            $this->db->where('AF.ASIGNADO_A', COD_USUARIO);
            $this->db->or_where('AF.ASIGNADO_POR', COD_USUARIO);
            $this->db->group_by('E.CODEMPRESA, E.RAZON_SOCIAL, F.COD_CONCEPTO,CF.NOMBRE_CONCEPTO, F.COD_FISCALIZACION, L.NUM_LIQUIDACION');
            $this->db->where('NOT EXISTS (SELECT * FROM SOPORTE_LIQUIDACION WHERE Soporte_Liquidacion.Num_liquidacion = L.Num_Liquidacion)', '', FALSE);
            $this->db->where('F.COD_TIPOGESTION not in (309, 440) and F.COD_CONCEPTO not in (3,5) and F.CODIGO_PJ is NULL ');
            $dato = $this->db->get("LIQUIDACION L");
            //#####BUGGER PARA LA CONSULTA ######
            // $resultado = $this -> db -> last_query();
            // echo $resultado; die();
            //#####BUGGER PARA LA CONSULTA ######
            $dato = $dato->result_array();

            if (!empty($dato)) :

                return $dato[0];

            endif;

        else:
            $this->db->where('L.BLOQUEADA', '0');
            $this->db->where('L.EN_FIRME', 'N');
            $this->db->where('AF.ASIGNADO_A', COD_USUARIO);
            $this->db->or_where('AF.ASIGNADO_POR', COD_USUARIO);
            $this->db->group_by('E.CODEMPRESA, E.RAZON_SOCIAL, F.COD_CONCEPTO,CF.NOMBRE_CONCEPTO, F.COD_FISCALIZACION, L.NUM_LIQUIDACION');
            $this->db->where('NOT EXISTS (SELECT * FROM SOPORTE_LIQUIDACION WHERE Soporte_Liquidacion.Num_liquidacion = L.Num_Liquidacion)', '', FALSE);
            $this->db->where('F.COD_TIPOGESTION not in (309, 440) and F.COD_CONCEPTO not in (3,5) and F.CODIGO_PJ is NULL ');
            $dato = $this->db->get("LIQUIDACION L");
            //#####BUGGER PARA LA CONSULTA ######
            // $resultado = $this -> db -> last_query();
            // echo $resultado; die();
            //#####BUGGER PARA LA CONSULTA ######
            if ($dato->num_rows() > 0):

                return $dato->result();

            endif;

        endif;
    }

    function getArchivosSubidos($num_liquidacion)
    /**
     * FunciÃ³n que retorna la informaciÃ³n de los archivos cargados en la legalizaciÃ³n de la liquidaciÃ³n
     *
     * @param string $num_liquidacion
     * @return obj $dato
     * @return  boolean false - error
     */ {
        $this->db->select('NRO_RADICADO, FECHA_RADICADO, NOMBRE_ARCHIVO, NIS,COD_SOPORTE_LIQUIDACION');
        $this->db->where('NUM_LIQUIDACION', $num_liquidacion);
        $dato = $this->db->get("SOPORTE_LIQUIDACION");
        $dato = $dato->result_array();
        if (!empty($dato)) :
            return $dato;
        else:
            return FALSE;
        endif;
    }

    public function eliminar_soporte($num_liquidacion, $nombre_archivo)
    /**
     * FunciÃ³n que elimina los datos de los soportes cargados en la legalizaciÃ³n de la liquidaciÃ³n
     *
     * @param string $cod_soporte
     */ {
        if (!empty($num_liquidacion)) :
            $this->db->where('NUM_LIQUIDACION', $num_liquidacion);
            $this->db->where('NOMBRE_ARCHIVO', $nombre_archivo);
            $this->db->delete('SOPORTE_LIQUIDACION');
        endif;
    }

    public function actualizacion_liquidacion($datos)
    /**
     * FunciÃ³n que actualiza los datos de los soportes cargados en la legalizaciÃ³n de la liquidaciÃ³n
     *
     * @param string $cod_soporte
     */ {
        if (!empty($datos)) :

            $this->db->where("NUM_LIQUIDACION", $datos['NUM_LIQUIDACION']);
            unset($datos['NUM_LIQUIDACION']);
            $resultado = $this->db->update("LIQUIDACION", $datos);
        //#####BUGGER PARA LA CONSULTA ######
        // $resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######

        endif;
    }

    public function consultarLiquidacion_acuerdoPago($liquidacion)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n necesaria para el recalculo de la liquidaciÃ³n hasta el momento de la generaciÃ³n de un acuerdo de pago
     * Solo funcional en liquidaciÃ³n en firme y recalcula capital e intereses de forma independiente
     *
     * @param string $liquidacion
     */ {
        $this->db->select('to_char("FECHA_LIQUIDACION", ' . "'DD/MM/YYYY') AS FECHA_LIQUIDACION", FALSE);
        $this->db->select('COD_CONCEPTO, TOTAL_LIQUIDADO, SALDO_INTERES, SALDO_CAPITAL, SALDO_DEUDA');
        $this->db->from('LIQUIDACION');
        $condicion = "NUM_LIQUIDACION = '" . $liquidacion . "' AND EN_FIRME = 'S'";
        $this->db->where($condicion);
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $datos->row_array();
        if (!empty($datos)):

            return $datos;

        else:

            return FALSE;

        endif;
    }

    public function consultarPagosPeriodo($nit, $periodo, $concepto, $subconcepto)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de los pagos por el concepto de monetizaciÃ³n dentro de un aÃ±o especÃ­fico
     * Solo funcional en la consulta estados de cuenta de contrato de aprendizaje
     *
     * @param string $nit
     * @param string $periodo
     * @param string $concepto
     * @param string $subconcepto
     * @return array $resultado
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('sum(VALOR_PAGADO) as MONETIZACION ');
        $this->db->from('PAGOSRECIBIDOS');
        $condicion = "NITEMPRESA = '" . $nit . "' AND COD_CONCEPTO = " . $concepto . " AND COD_SUBCONCEPTO = " . $subconcepto . " AND substr(PERIODO_PAGADO, 1, 4) = '" . $periodo . "'";
        $this->db->where($condicion);
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        //$datos = $this -> db -> last_query();
        //echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->row_array();
            if (empty($datos['MONETIZACION'])):

                return $datos = 0;

            else:

                return $datos;

            endif;

        endif;
    }

    public function consultarTransaccion($transaccion)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de los pagos por el concepto de aportes FIC
     * El nÃºmero de transacciÃ³n esta asociado al nÃºmero de documento o al ticket ID
     *
     * @param string $transaccion
     * @return array $datos;
     * @return boolean FALSE;
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->select('NITEMPRESA, COD_CONCEPTO, COD_SUBCONCEPTO, PERIODO_PAGADO, NUM_DOCUMENTO, TICKETID, NRO_LICENCIA_CONTRATO, FECHA_INICIO_OBRA, FECHA_FIN_OBRA, VALOR_PAGADO');
        $this->db->from('PAGOSRECIBIDOS');
        $condicion = "(COD_CONCEPTO = 2) AND (NUM_DOCUMENTO = '" . $transaccion . "' OR TICKETID = '" . $transaccion . "')";
        $this->db->where($condicion);
        $datos = $this->db->get();
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######

        if ($this->db->trans_status() === FALSE) :

            $this->db->trans_rollback();
            return $datos = FALSE;

        else:

            $this->db->trans_commit();
            $datos = $datos->row_array();
            if (empty($datos['NITEMPRESA'])):

                return $datos = FALSE;

            else:

                return $datos;

            endif;

        endif;
    }

    public function consultarTransaccionesAsociadas($liquidacion)
    /**
     * FunciÃ³n que devuelve la informaciÃ³n de las transacciones asociadas a una liquidaciÃ³n FIC
     * Solo retorna datos si la liquidaciÃ³n tiene transacciones asociadas
     *
     * @param string $transaccion
     * @return array $datos;
     * @return boolean FALSE;
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(true);
        $condicion = "COD_LIQUIDACION_FIC = '" . $liquidacion . "'";
        $datos = $this->db->get_where('TRANSACCIONES_FIC', $condicion);
        //#####BUGGER PARA LA CONSULTA ######
        // $datos = $this -> db -> last_query();
        // echo $datos; die();
        //#####BUGGER PARA LA CONSULTA ######
        if ($this->db->trans_status() === false):
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            if ($datos):
                $tmp = null;
                foreach ($datos->result_array() as $transaccion):
                    $tmp[] = $transaccion;
                endforeach;
                $datos = $tmp;
            else:
                $datos = false;
            endif;
        endif;
    }

    public function get_fiscalizacion_obra($fis) {
        $this->db->select('O.ID_OBRA,
                                                O.NOMBRE_OBRA,
                                                O.COD_EMPRESA,
                                                O.FECHA_INICIO_OBRA,
                                                O.FECHA_TERMINACION_OBRA,
                                                O.COSTO_TOTAL_OBRA_TODO_COSTO,
                                                O.COSTO_TOTAL_MANO_OBRA,
                                                O.NUMERO_TRABAJADORES_PERIODO,
                                                O.DATA,
                                                PD.VALOR');
        $this->db->from('OBRA O');
        $this->db->join('FISCALIZACION_POR_OBRA FO', 'FO.ID_OBRA = O.ID_OBRA', 'inner');
        $this->db->join('PARAMETRO_DEF PD', 'PD.COD_PARAMETRO_DEF = O.TIPO_FIC', 'inner');
        $this->db->where('FO.COD_FISCALIZACION', $fis);

        return $this->validation_array($this->db->get());
    }

    public function get_pagos_fiscalizacion($nit, $cod_fis, $obra = NULL, $concepto, $presuntivo = TRUE) {
        $str_query = "SELECT to_char(P.FECHA_PAGO,'yyyy-mm-dd') as FECHA_PAGO, P.NITEMPRESA,P.COD_PAGO, P.VALOR_PAGADO, SUBSTR(P.PERIODO_PAGADO, 1,4) AS ANIO, TO_NUMBER(SUBSTR(P.PERIODO_PAGADO, 6)) AS MES, 
            TO_CHAR(P.FECHA_PAGO,'YYYY') AS ANIO2, TO_CHAR(P.FECHA_PAGO,'MM') AS MES2, TO_CHAR(P.FECHA_PAGO,'DD') AS DIA, P.COD_SUBCONCEPTO,P.COD_CONCEPTO
            FROM FISCALIZACION_PAGO FP
            JOIN PAGOSRECIBIDOS P ON P.COD_PAGO = FP.COD_PAGO 
            WHERE P.COD_CONCEPTO = $concepto AND FP.COD_FISCALIZACION = $cod_fis";

        if ($obra) {
            $str_query .= " AND P.NRO_LICENCIA_CONTRATO = '$obra'";
        }
        $str_query .= " ORDER BY P.PERIODO_PAGADO ASC";
        $query = $this->db->query($str_query);
 //$this->debug_(true);
        if ($query) {
            $array = $query->result_array();
            $length = count($array);
            for ($index = 0; $index < $length; $index++) {
                if ($array[$index]['ANIO'] != $array[$index]['ANIO2'] || $array[$index]['MES'] != $array[$index]['MES2']) {
                    $date = new DateTime($array[$index]['ANIO2'] . '-' . $array[$index]['MES2'] . '-' . $array[$index]['DIA']);
                    $date->sub(new DateInterval('P10D'));
                    if ($array[$index]['ANIO'] == $date->format('Y') && $array[$index]['MES'] == $date->format('m')) {
                        $array[$index]['MES2'] = $array[$index]['MES'];
                        $array[$index]['ANIO2'] = $array[$index]['ANIO'];
                    }
                }
            }
            return $array;
        }
        return array();
    }

    public function get_sum_pagos_obra($nit, $obra, $year) {
        $subc = $this->get_subcontratistas($obra);
        $query = $this->db->query("SELECT SUM(P.VALOR_PAGADO) AS PAGOS FROM PAGOSRECIBIDOS P 
        LEFT JOIN OBRA O ON O.ID_OBRA = '$obra' 
        WHERE P.COD_CONCEPTO = 2 AND (P.NRO_LICENCIA_CONTRATO = '$obra' $subc) 
        AND P.FECHA_PAGO BETWEEN to_date('01/01/$year','dd/mm/yyyy') AND to_date('31/12/$year','dd/mm/yyyy')");
        if ($query) {
            if ($query->result_array()[0]['PAGOS']) {
                return $query->result_array()[0]['PAGOS'];
            }
        }
        return 0;
    }

    public function get_sum_pagos_aportes($nitEmpresa, $cod_fiscalizacion, $year) {

        $query = $this->db->query("SELECT SUM(P.VALOR_PAGADO) AS PAGOS FROM PAGOSRECIBIDOS P 
         JOIN FISCALIZACION_PAGO FP ON FP.COD_PAGO = P.COD_PAGO 
        WHERE P.COD_CONCEPTO = 1 AND FP.COD_FISCALIZACION = '$cod_fiscalizacion ' AND P.NITEMPRESA ='$nitEmpresa'
        AND substr(PERIODO_PAGADO, 1, 4) BETWEEN '$year' and '$year'");
        if ($query) {
            if ($query->result_array()[0]['PAGOS']) {
                return $query->result_array()[0]['PAGOS'];
            }
        }
        return 0;
    }

    
    public function get_sum_pagos_aportes_total($nitEmpresa, $cod_fiscalizacion) {

        $query = $this->db->query("SELECT SUM(P.VALOR_PAGADO) AS PAGOS FROM PAGOSRECIBIDOS P 
         JOIN FISCALIZACION_PAGO FP ON FP.COD_PAGO = P.COD_PAGO 
        WHERE P.COD_CONCEPTO = 1 AND FP.COD_FISCALIZACION = '$cod_fiscalizacion ' AND P.NITEMPRESA ='$nitEmpresa'");
        //$this->debug_(true);
        if ($query) {
            if ($query->result_array()[0]['PAGOS']) {
                return $query->result_array()[0]['PAGOS'];
            }
        }
        return 0;
    }


    public function get_subcontratistas($obra) {
        $query = $this->db->query("SELECT CONTRATO_OBRA FROM SUBCONTRATISTA WHERE ID_OBRA = '$obra'");
        if ($query) {
            if ($query->result_array()) {
                $array = $query->result_array();
                $length = count($array);
                if ($length > 0) {
                    $str = "OR P.NRO_LICENCIA_CONTRATO in(";
                    for ($i = 0; $i < $length; $i++) {
                        if ($i == 0) {
                            $str = $str . "'" . $array[$i]['CONTRATO_OBRA'] . "'";
                        } else {
                            $str = $str . ",'" . $array[$i]['CONTRATO_OBRA'] . "'";
                        }
                    }
                    return $str . ")";
                }
            }
        }
        return "";
    }

    /**
     * @author Omar David Pino O.
     * @param varchar $nitEmpresa
     * @param varchar $periodo
     * @return array de pagos
     */
    function get_pagos_recividos($nitEmpresa, $periodo, $contrato) {
        $this->db->select('VALOR_PAGADO, PERIODO_PAGADO');
        $this->db->from('PAGOSRECIBIDOS');
        $this->db->where('NITEMPRESA', $nitEmpresa);
        //$this -> db -> where('COD_CONCEPTO', $concepto, FALSE);
        $this->db->where('PERIODO_PAGADO', $periodo);
        if (!empty($contrato)) {
            $this->db->where('NRO_LICENCIA_CONTRATO', $contrato);
        }

        $resultado = $this->db->get();
        if ($resultado->num_rows() > 0) {
            return $resultado->row_array();
        } else {
            return FALSE;
        }
    }

    function update_obra($idobra, $atodocosto, $manoobra, $ntrabajadores, $interes, $pagos) {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('COSTO_TOTAL_OBRA_TODO_COSTO', $atodocosto);
        $this->db->set('COSTO_TOTAL_MANO_OBRA', $manoobra);
        $this->db->set('NUMERO_TRABAJADORES_PERIODO', $ntrabajadores);
        $this->db->set('VALOR_INTERESES_MORATORIOS', $interes);
        $this->db->set('PAGOS_REALIZADOS', $pagos);

        $this->db->where('ID_OBRA', $idobra);
        $resultado = $this->db->update('OBRA');


        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->db->last_query();
        }
        $this->db->trans_commit();
        return TRUE;
    }

    function getNotificaciones($user) {
        $datoss = $this->db->query("SELECT * FROM(
        SELECT DISTINCT L.COD_FISCALIZACION,L.NITEMPRESA, L.NUM_LIQUIDACION, L.FECHA_LIQUIDACION, trunc(sysdate) - trunc(FECHA_LIQUIDACION) FROM LIQUIDACION L 
        LEFT JOIN FISCALIZACION F ON F.COD_FISCALIZACION = L.COD_FISCALIZACION 
        LEFT JOIN ASIGNACIONFISCALIZACION ASIG ON ASIG.COD_ASIGNACIONFISCALIZACION = F.COD_ASIGNACION_FISC 
        WHERE trunc(sysdate) - trunc(L.FECHA_LIQUIDACION) > 29 
        AND ( ASIG.ASIGNADO_POR = '$user' OR ASIG.ASIGNADO_A = '$user') 
        AND L.SALDO_DEUDA > 0 AND L.NOTIFICACION = 0
        ORDER BY L.FECHA_LIQUIDACION DESC) WHERE ROWNUM < 100");

        $datoss = $datoss->result_array();
        return $datoss;
    }

    function pagos_obra($nit, $obra, $fechaini, $fechafin) {
        $this->db->select('SUM(P.VALOR_PAGADO) AS SUMA');
        $this->db->join('PAGOSRECIBIDOS P', 'P.NITEMPRESA=EMPRESA.CODEMPRESA and P.COD_CONCEPTO = 2', 'LEFT');
        $this->db->where("EMPRESA.CODEMPRESA", $nit);
        $this->db->where("P.NRO_LICENCIA_CONTRATO", $obra);
        $this->db->where("P.FECHA_PAGO BETWEEN TO_DATE('" . $fechaini . "', 'dd-mm-yyyy') AND TO_DATE('" . $fechafin . "', 'dd-mm-yyyy')");
        $consulta = $this->db->get('EMPRESA');
        /* $SQL = "SELECT SUM(P.VALOR_PAGADO)
          FROM EMPRESA
          LEFT JOIN PAGOSRECIBIDOS P ON P.NITEMPRESA=EMPRESA.CODEMPRESA and P.COD_CONCEPTO = 2
          WHERE EMPRESA.CODEMPRESA ='" . $nit . "' and P.NRO_LICENCIA_CONTRATO='" . $obra . "' AND P.FECHA_PAGO BETWEEN TO_DATE('" . $fechaini . "', 'dd-mm-yyyy') AND TO_DATE('" . $fechafin . "', 'dd-mm-yyyy');";
          $consulta = $this->db->query($SQL); */
        if ($consulta) {
            return $consulta->result_array()[0]['SUMA'];
        }
        return 0;
    }

    function pagos_liquidacion($cod_fis, $key = "FECHA_APLICACION") {
        $query = "SELECT TO_CHAR(P.FECHA_APLICACION,'YYYY-MM-DD') AS FECHA_APLICACION, TO_CHAR(P.FECHA_PAGO,'YYYY-MM-DD') AS FECHA_PAGO, P.VALOR_PAGADO,P.COD_PAGO
            FROM PAGOSRECIBIDOS P
            WHERE P.COD_FISCALIZACION = '$cod_fis' AND (P.$key >= TO_DATE('2018-09-01','YYYY-MM-DD') OR RECLASIFICADO = '1')
            ORDER BY P.$key ASC";
        $query = $this->db->query($query);
        return $this->validation_array($query);
    }

    /**
     * @author Omar David Pino Ordoñez
     * @description: trae el ultimo pago antes de 1 septiembre de 2018
     * */
    function pagos_liquidacion_ante($cod_fis) {
        $query = "SELECT TO_CHAR(P.FECHA_APLICACION,'YYYY-MM-DD') AS FECHA_APLICACION, TO_CHAR(P.FECHA_PAGO,'YYYY-MM-DD') AS FECHA_PAGO, P.VALOR_PAGADO,P.COD_PAGO
            FROM PAGOSRECIBIDOS P
            WHERE P.COD_FISCALIZACION = '$cod_fis' AND P.FECHA_PAGO < TO_DATE('2018-09-01','YYYY-MM-DD') AND RECLASIFICADO <> '1'
            ORDER BY P.FECHA_PAGO DESC";
        $query = $this->db->query($query);
        return $this->validation_array($query, TRUE);
    }

    function liquidaciones_sin_mora() {
        $this->db->query('INSERT INTO HISTORICO_CARTERAS (COD_FISCALIZACION,SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, FECHA, SALDO_SANCION, DIAS_MORA)
			SELECT COD_FISCALIZACION, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES,CURRENT_DATE AS FECHA, SALDO_SANCION, DIAS_MORA
			FROM LIQUIDACION  
			WHERE SALDO_DEUDA > 0 AND FECHA_EJECUTORIA IS NOT NULL AND CALCULO_MORA = 0');
    }

    function carteras_con_pagos($cod_fiscalizacion) {
        $query = "SELECT *
                    FROM LIQUIDACION 
                   WHERE COD_FISCALIZACION ='$cod_fiscalizacion'";
        $query = $this->db->query($query);
        return $this->validation_array($query);
    }

    function historico_carteras($cod_fiscalizacion) {
        $query = "SELECT *
            FROM HISTORICO_CARTERAS
            WHERE COD_FISCALIZACION = {$cod_fiscalizacion}";
        $query = $this->db->query($query);
        return $this->validation_array($query);
    }

    /**
     * @author Omar David Pino Ordoñez
     * @description: trae las liquidaciones que tengan pagos antes del 1 de sptiembre de 2018 
     * pero que no contengan despues de esta fecha, excluye instancias y fuentes 
     * que no deben calcuarse dias de mora, tampoco que no tengan fecha de ejecutoria y bloqueadas
     * */
    function carteras_con_pagos_migracion($cod_fiscalizacion = NULL) {
        $adicion = '';
        if ($cod_fiscalizacion) {
            $adicion = " AND COD_FISCALIZACION='$cod_fiscalizacion'";
        }
        $query = "SELECT DISTINCT /*+ PARALLEL(AUTO) */
                            A .COD_FISCALIZACION
                    FROM
                            LIQUIDACION A
                    WHERE DIAS_MORA = 0
                    AND FECHA_EJECUTORIA IS NOT NULL
                    AND COD_FISCALIZACION IN (SELECT COD_FISCALIZACION FROM PAGOSRECIBIDOS WHERE FECHA_PAGO < TO_DATE ('2018-09-01', 'YYYY-MM-DD') AND RECLASIFICADO <> '1'$adicion)";
        if ($cod_fiscalizacion) {
            $query = $query . " AND COD_FISCALIZACION='$cod_fiscalizacion'";
        }

        $query = $this->db->query($query);
        return $this->validation_array($query);
    }

    function liquidaciones_en_mora($date, $fechau = NULL, $cod_fiscalizacion = NULL, $dias_mora = 0, $reiniciar = FALSE, $c_saldo = TRUE) {
        $condicion = '';
        if ($fechau == NULL) {
            $fechau = "(L.FECHA_EJECUTORIA)";
        }
        if ($cod_fiscalizacion != NULL) {
            $condicion = " AND L.COD_FISCALIZACION = '{$cod_fiscalizacion}'";
        }
        $format = $this->formatDate;

        $saldo_deuda = '';
        if ($c_saldo) {
            $saldo_deuda = ' AND L.SALDO_DEUDA > 0 ';
        }
        if ($dias_mora == 0 && !$reiniciar) {
            $queryd = "L.DIAS_MORA, L.DIAS_MORA_APLICADA, ";
            $dias_mora_a = "L.DIAS_MORA_APLICADA";
        } else {
            $queryd = "$dias_mora AS DIAS_MORA, $dias_mora AS DIAS_MORA_APLICADA, ";
            $dias_mora_a = $dias_mora;
        }
        $query = "SELECT DISTINCT L.NITEMPRESA, L.COD_FISCALIZACION, L.NUM_LIQUIDACION ,L.COD_CONCEPTO,$queryd L.SALDO_CAPITAL, L.SALDO_INTERES, L.TOTAL_INTERESES, L.SALDO_DEUDA, L.SALDO_SANCION,
            DECODE(SIGN(CEIL(TO_DATE('$date', '$format') - $fechau) - $dias_mora_a), -1, 0, (CEIL(TO_DATE('$date', '$format') - $fechau)- $dias_mora_a)) AS DIAS_MORA_A, L.FECHA_EJECUTORIA
            FROM LIQUIDACION L
            WHERE CALCULO_MORA = 1 AND L.FECHA_EJECUTORIA IS NOT NULL $saldo_deuda $condicion";
        $query = $this->db->query($query);
        //$this->debug_(true);
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array();
            }
        }
        $array = array();
        $arrayp = array(
            'DIAS_MORA_A' => 0
        );
        return array_push($array, $arrayp);
    }

    /*
     * @description: Calcula los dias en mora desde la fecha ejecutoria o desde el ultimo pago si existe
     */

    function liquidacion_en_mora($cod_fis, $date, $fechau = "(L.FECHA_EJECUTORIA)") {
        $format = $this->formatDate;
        $query = "SELECT DISTINCT L.NITEMPRESA, L.COD_FISCALIZACION, L.NUM_LIQUIDACION ,L.COD_CONCEPTO, L.SALDO_CAPITAL, L.DIAS_MORA, L.DIAS_MORA_APLICADA, L.SALDO_INTERES, L.TOTAL_INTERESES, L.SALDO_DEUDA, L.SALDO_SANCION,
            DECODE(SIGN(CEIL(TO_DATE('$date', '$format') - $fechau) - L.DIAS_MORA_APLICADA), -1, 0, (CEIL(to_date('$date','$format') - $fechau) - L.DIAS_MORA_APLICADA)) AS DIAS_MORA_A, L.FECHA_EJECUTORIA
            FROM LIQUIDACION L 
            WHERE L.BLOQUEADA = 0 AND L.SALDO_DEUDA > 0 AND L.COD_FISCALIZACION = $cod_fis";
        $query = $this->db->query($query);
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array()[0];
            }
        }
        return NULL;
    }

    function liquidacion_en_mora2($cod_fis, $date, $fechau = NULL) {
        if ($fechau == NULL) {
            $fechau = "(L.FECHA_EJECUTORIA)";
        }
        $format = $this->formatDate;
        $query = "SELECT DISTINCT L.NITEMPRESA, F.COD_FISCALIZACION, L.NUM_LIQUIDACION ,F.COD_CONCEPTO, L.SALDO_CAPITAL, 0 AS DIAS_MORA, 0 AS DIAS_MORA_APLICADA, L.SALDO_INTERES, L.TOTAL_INTERESES, L.SALDO_DEUDA, L.SALDO_SANCION,
            DECODE(SIGN(CEIL(TO_DATE('$date', '$format') - $fechau)), -1, 0, (CEIL(to_date('$date','$format') - $fechau) )) AS DIAS_MORA_A, L.FECHA_EJECUTORIA
            FROM FISCALIZACION F
            JOIN LIQUIDACION L ON L.COD_FISCALIZACION = F.COD_FISCALIZACION 
            WHERE L.BLOQUEADA = 0 AND L.SALDO_DEUDA > 0 AND F.COD_FISCALIZACION = $cod_fis";
        $query = $this->db->query($query);
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array()[0];
            }
        }
        return NULL;
    }

    function interes_mora($liquidacion, $fecha, $historico = TRUE, $interes_acumulado = '-1') {
        if ($liquidacion['SALDO_CAPITAL'] < 1) {
            return 0;
        }
        //echo print_r($liquidacion);die();
        $fechaA1 = new DateTime('2006-07-28');
        $fechaA2 = new DateTime('2012-12-26');
        $fechaFIC1 = new DateTime('2006-12-25');
        $fechaFIC2 = new DateTime('2012-12-26');
        $fechaC1 = new DateTime('2014-04-15');
        $fechaCorte = new DateTime('2018-01-01');
        if ($interes_acumulado != '-1') {
            $interes_mora = $interes_acumulado;
        } else {
            $interes_mora = 0;
        }
        $dias = $liquidacion['DIAS_MORA_A'] - 1;
        if ($dias < 0) {
            $dias = 0;
        }
        $fecha->sub(new DateInterval('P' . $dias . 'D'));
        $liquidacion['DIAS_MORA_A'] = $liquidacion['DIAS_MORA_A'] - 1;
        for ($i = 0; $i <= $liquidacion['DIAS_MORA_A']; $i++) {
            if ($liquidacion['COD_CONCEPTO'] == 1) {
                if ($fecha < $fechaA1) {
                    $this->tasaActual = 12;
                    $interes_mora = $interes_mora + ($liquidacion['SALDO_CAPITAL'] * ($this->tasaActual / 100)) / 365;
                } else {
                    if ($fecha < $fechaA2) {
                        $interes_mora = $this->interes_compuesto($liquidacion, $fecha, $interes_mora, TRUE);
                    } else {
                        $interes_mora = $this->interes_simple_ts($liquidacion, $fecha, $interes_mora);
                    }
                }
            }
            if ($liquidacion['COD_CONCEPTO'] == 2) {
                if ($fecha < $fechaFIC1) {
                    $this->tasaActual = 12;
                    $interes_mora = $interes_mora + ($liquidacion['SALDO_CAPITAL'] * ($this->tasaActual / 100) ) / 365;
                } else {
                    if ($fecha < $fechaFIC2) {
                        $interes_mora = $this->interes_compuesto($liquidacion, $fecha, $interes_mora, FALSE);
                    } else {
                        $interes_mora = $this->interes_simple_ts($liquidacion, $fecha, $interes_mora);
                        //echo number_format($interes_mora) . ' ' . $fecha->format('Y') . '/' . $fecha->format('m') . '/' . $fecha->format('d') . '<BR>';die();
                    }
                }
            }
            if ($liquidacion['COD_CONCEPTO'] == 3) {
                if ($fecha < $fechaC1) {
                    $this->tasaActual = 12;
                    $interes_mora = $interes_mora + ($liquidacion['SALDO_CAPITAL'] * ($this->tasaActual / 100)) / 365;
                } else {
                    $interes_mora = $this->interes_simple_ts($liquidacion, $fecha, $interes_mora);
                }
            }
            if ($liquidacion['COD_CONCEPTO'] == 5) {
                $this->tasaActual = 12;
                $interes_mora = $interes_mora + ($liquidacion['SALDO_CAPITAL'] * ($this->tasaActual / 100)) / 365;
            }
            $fecha->add(new DateInterval('P1D'));
            $liquidacion['DIAS_MORA'] = $liquidacion['DIAS_MORA'] + 1;

            if ($historico == TRUE) {
                $fechah = $fecha->format('d') . '/' . $fecha->format('m') . '/' . $fecha->format('Y');
                $datah = array(
                    'COD_FISCALIZACION' => $liquidacion['COD_FISCALIZACION'],
                    'SALDO_DEUDA' => $liquidacion['SALDO_DEUDA'] + round($interes_mora),
                    'SALDO_CAPITAL' => $liquidacion['SALDO_CAPITAL'],
                    'SALDO_INTERES' => $liquidacion['SALDO_INTERES'] + round($interes_mora),
                    'SALDO_SANCION' => $liquidacion['SALDO_SANCION'],
                    'DIAS_MORA' => $liquidacion['DIAS_MORA']
                );
                $dateh = array(
                    'FECHA' => $fechah
                );
                $this->liquidaciones_model->add("HISTORICO_CARTERAS", $datah, $dateh);
            }
        }
        return $interes_mora;
    }

    function interes_simple_ts($liquidacion, $fecha, $interes_mora) {
        $this->tasaActual = $this->tasa_efectiva($fecha->format('Y') . '/' . $fecha->format('m') . '/' . $fecha->format('d'));
        if ($this->tasaActual != -1) {
            $dias_mora = 0;
            $dias_mora = ($liquidacion['SALDO_CAPITAL'] * ($this->tasaActual / 100)) / 365;
            $interes_mora = $interes_mora + $dias_mora;
        } else {
            $interes_mora = 0;
        }
        return $interes_mora;
    }

    function interes_compuesto($liquidacion, $fecha, $interes_mora, $ts) {
        if ($ts) {
            $this->tasaActual = $this->tasa_efectiva($fecha->format('Y') . '/' . $fecha->format('m') . '/' . $fecha->format('d'));
        } else {
            $this->tasaActual = 12;
        }
        if ($this->tasaActual != -1) {
            $tasa = $this->tasaActual / 100;
            $interes1 = pow((1 + $tasa), (1 / 365));
            $interes = (($liquidacion['SALDO_CAPITAL'] + $interes_mora) * $interes1) - ($liquidacion['SALDO_CAPITAL'] + $interes_mora);
        } else {
            $interes = 0;
        }
        //echo (int) $interes_mora . ' - ' . (int) $interes . '<br>';
        return $interes_mora + $interes;
    }

    function pagos_cartera($codfis) {
        $query = "SELECT P.FECHA_PAGO AS FECHA_PAGO2, P.COD_PAGO FROM PAGOSRECIBIDOS P WHERE P.COD_FISCALIZACION = '$codfis' ORDER BY P.FECHA_PAGO DESC";
        $query = $this->db->query($query);
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                $date = new DateTime($query->result_array()[0]['FECHA_PAGO2']);
                return $date->format('Y-m-d');
            }
        }
        return NULL;
    }

    public function soportesdepagoasignados($codfis) {
        $query = "SELECT P.COD_PAGO, P.NRO_LICENCIA_CONTRATO, P.NITEMPRESA,P.PROCEDENCIA, P.COD_CONCEPTO,P.COD_SUBCONCEPTO, P.COD_REGIONAL, P.PERIODO_PAGADO,P.VALOR_PAGADO, TO_CHAR(P.FECHA_PAGO, 'YYYY') AS ANIO, TO_CHAR(P.FECHA_PAGO, 'MM') FROM PAGOS_DIFERIDOS_LIQUIDACION PL JOIN PAGOSRECIBIDOS P ON P.COD_PAGO = PL.COD_PAGO WHERE PL.COD_FISCALIZACION = '$codfis'";
        $query = $this->db->query($query);
        return $query->result_array();
    }

    public function suma_soportesdepago($codfis) {
        $query = "SELECT SUM(P.VALOR_PAGADO) SUMA FROM PAGOS_DIFERIDOS_LIQUIDACION PL JOIN PAGOSRECIBIDOS P ON P.COD_PAGO = PL.COD_PAGO WHERE PL.COD_FISCALIZACION = '$codfis'";
        $query = $this->db->query($query);
        if (count($query->result_array()) > 0) {
            return $query->result_array()[0]['SUMA'];
        }
        return 0;
    }

    public function insertar_dias_mora_migracion($cod_fiscalizacion) {
        $fechapago = "FECHA_PAGO";
        $pago_ante = $this->pagos_liquidacion_ante($cod_fiscalizacion);
        if ($pago_ante) {
            $liquidacion = $this->liquidacion_en_mora2($cod_fiscalizacion, $pago_ante[$fechapago]);
            return $liquidacion['DIAS_MORA_A'];
        }
        return 0;
    }

    function correccion_planillas() {
        $fecha_base = "2018-10-01";
        $fecha_actual = date("Y-m-d");

        /*
         * CORREGIR NUMERO DE PLANILLAS DIFERENTES A NUMERO DE RADICADO
         */
        $cantidad_resultado = 0;
        $query = "SELECT COD_PLANILLAUNICA,N_RADICACION,N_PLANILLA_ FROM PLANILLAUNICA_ENC
                    WHERE N_RADICACION <> N_PLANILLA_ AND N_RADICACION IS NOT NULL
                    AND FECHA_CREACION > TO_DATE('$fecha_base','YYYY-MM-DD')";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            $log['CANTIDAD_CONSOLIDADA'] = $cantidad_resultado;
            $log['DESCRIPCION'] = 'CORRECCION PLANILLAS DIFERENTES A RADICADO';
            $this->db->insert('LOG_CONSOLIDACION_ASOBANCARIA', $log);
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_planilla = $value['COD_PLANILLAUNICA'];
                    $correccion['N_PLANILLA_'] = $value['N_RADICACION'];
                    $this->db->where('COD_PLANILLAUNICA', $cod_planilla);
                    $this->db->update('PLANILLAUNICA_ENC', $correccion);
                }
            }
        }
        /*
         * CORREGIR NUMERO DE PLANILLAS VACIAS
         */
        $cantidad_resultado = 0;
        $query = "SELECT COD_PLANILLAUNICA,N_RADICACION,N_PLANILLA_ FROM PLANILLAUNICA_ENC
                    WHERE N_PLANILLA_ IS NULL AND N_RADICACION IS NOT NULL
                    AND FECHA_CREACION > TO_DATE('$fecha_base','YYYY-MM-DD')";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            $log['CANTIDAD_CONSOLIDADA'] = $cantidad_resultado;
            $log['DESCRIPCION'] = 'CORRECCION PLANILLAS VACIAS';
            $this->db->insert('LOG_CONSOLIDACION_ASOBANCARIA', $log);
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_planilla = $value['COD_PLANILLAUNICA'];
                    $correccion['N_PLANILLA_'] = $value['N_RADICACION'];
                    $this->db->where('COD_PLANILLAUNICA', $cod_planilla);
                    $this->db->update('PLANILLAUNICA_ENC', $correccion);
                }
            }
        }
        return true;
    }

    function consolidacion_asobancaria() {
        $fecha_base = "2018-10-01";
        $fecha_actual = date("Y-m-d");

        /*
         * PASO 1, CORREGIR NIT EN EL CASO DE EXISTIR, PERO ESTO SOLO EN LA TABLA DE PAGOS ASOBANCARIA SIGUE IGUAL
         */
        $cantidad_resultado = 0;
        $query = " SELECT
                    /*+ PARALLEL(AUTO) */
                            a.COD_PAGO,
                            c.N_INDENT_APORTANTE 
                    FROM
                            pagosrecibidos A
                            JOIN asobancaria_det B ON b.COD_DETALLE = a.COD_PROCEDENCIA 
                            JOIN planillaunica_enc C ON c.n_planilla_ = b.nro_planilla AND REPLACE(c.PERIDO_PAGO, '-' ) = REPLACE(b.PERIODO_PAGO, '-' )
                    WHERE
                            A.PROCEDENCIA = 'ASOBANCARIA' 
                            AND b.periodo_pago IS NOT NULL 
                            AND a.fecha_pago >= TO_DATE('2018-10-01','YYYY-MM-DD')
                            AND c.n_indent_aportante  <> A.NITEMPRESA";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            $log['CANTIDAD_CONSOLIDADA'] = $cantidad_resultado;
            $log['DESCRIPCION'] = 'CORRECCION DE NIT';
            $this->db->insert('LOG_CONSOLIDACION_ASOBANCARIA', $log);
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_pago = $value['COD_PAGO'];
                    $consolidacion['NITEMPRESA'] = $value['N_INDENT_APORTANTE'];
                    $this->db->where('COD_PAGO', $cod_pago);
                    $this->db->update('PAGOSRECIBIDOS', $consolidacion);
                }
            }
        }
        /*
         * PASO 2, REVISAR EN BASE DE DATOS PLANILLAS NULAS 
         * Y VERIFICAR COINCIDENCIAS PARA COLOCAR EL NUMERO DE PLANILLA 
         * EN PAGOS RECIBIDOS
         */
        $i = 0;
        while ($fecha_base != $fecha_actual) {
            $query_2 = "SELECT DISTINCT
                    /*+ PARALLEL(AUTO) */
                        C.COD_PAGO,
                        A.COD_PLANILLAUNICA
                    FROM
                        planillaunica_enc A
                        JOIN REGISTROTIPO3 Z on z.COD_CAMPO = a.COD_PLANILLAUNICA 
                        JOIN asobancaria_det B ON A.n_planilla_ = b.nro_planilla AND REPLACE ( a.PERIDO_PAGO, '-' ) = b.periodo_pago AND b.VALOR_PLANILLA = TO_NUMBER(Z.TOTAL_APORTES)	
                        JOIN pagosrecibidos C ON C.cod_procedencia = B.COD_DETALLE 
                    WHERE
                        c.PROCEDENCIA = 'ASOBANCARIA' 
                        AND C.COD_PLANILLAUNICA IS NULL
                        AND C.NITEMPRESA = a.n_indent_aportante
                        AND c.FECHA_PAGO = TO_DATE('{$fecha_base}','YYYY-MM-DD')";
            $query_2 = $this->db->query($query_2);
            $cantidad_resultado_2 = count($query_2->result_array());
            $resultado2 = $query_2->result_array();
            if ($cantidad_resultado_2 > 0) {
                foreach ($resultado2 as $key => $value) {
                    $cod_pago = $value['COD_PAGO'];
                    $consolidacion['COD_PLANILLAUNICA'] = $value['COD_PLANILLAUNICA'];
                    $consolidacion['CONCILIADO'] = '1';
                    $consolidacion['APLICADO'] = '1';
                    $this->db->where('COD_PAGO', $cod_pago);
                    $this->db->update('PAGOSRECIBIDOS', $consolidacion);
                }
                $i = $i + $cantidad_resultado_2;
            }
            $fecha_base = date("Y-m-d", strtotime($fecha_base . "+ 1 days"));
            //echo $fecha_base .' - '.$cantidad_resultado_2;die();
        }

        $log2['CANTIDAD_CONSOLIDADA'] = $i;
        $log2['DESCRIPCION'] = 'PLANILLAS CONSOLIDADAS';
        $this->db->insert('LOG_CONSOLIDACION_ASOBANCARIA', $log2);
        return true;
    }

    function ajustar_saldos_vencidos() {
        $query = "SELECT COD_FISCALIZACION, TOTAL_LIQUIDADO, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, SALDO_SANCION FROM LIQUIDACION
                    WHERE SALDO_DEUDA BETWEEN -100 AND 
                    100 AND SALDO_DEUDA <> 0 
                    AND total_liquidado <> saldo_deuda";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_fiscalizacion = $value['COD_FISCALIZACION'];
                    $correccion['SALDO_DEUDA'] = '0';
                    $correccion['SALDO_CAPITAL'] = '0';
                    $correccion['SALDO_INTERES'] = '0';
                    $correccion['SALDO_SANCION'] = '0';
                    $log_cambio['COD_FISCALIZACION'] = $cod_fiscalizacion;
                    $log_cambio['TOTAL_LIQUIDADO'] = $value['TOTAL_LIQUIDADO'];
                    $log_cambio['SALDO_DEUDA'] = $value['SALDO_DEUDA'];
                    $log_cambio['SALDO_CAPITAL'] = $value['SALDO_CAPITAL'];
                    $log_cambio['SALDO_INTERES'] = $value['SALDO_INTERES'];
                    $log_cambio['SALDO_SANCION'] = $value['SALDO_SANCION'];
                    $this->db->insert('LOGS_AJUSTE_SALDOS', $log_cambio);
                    $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
                    $this->db->update('LIQUIDACION', $correccion);
                }
            }
        }
    }

    function validacion_compensacion_im() {
        $query = "select  liquidacion.cod_fiscalizacion
                    from liquidacion 
                    join resolucion on resolucion.cod_fiscalizacion = liquidacion.cod_fiscalizacion
                    join (
                    select liquidacion.cod_fiscalizacion, sum(valor_pagado) as valor_pagado
                    from liquidacion 
                    join pagosrecibidos on pagosrecibidos.cod_fiscalizacion = liquidacion.cod_fiscalizacion
                    where intencion_compensacion = 'S' and calculo_mora = '1'
                    group by liquidacion.cod_fiscalizacion) b on b.cod_fiscalizacion = liquidacion.cod_fiscalizacion
                    where liquidacion.total_intereses + liquidacion.valor_sancion = b.valor_pagado";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_fiscalizacion = $value['COD_FISCALIZACION'];
                    $correccion['CALCULO_MORA'] = '0';
                    $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
                    $this->db->update('LIQUIDACION', $correccion);
                    //echo $this->db->last_query();die();
                }
                return $resultado;
            }
        } else {
            return 0;
        }
    }

    function actualizar_nombreempresa() {
        $query = "SELECT DISTINCT  B.N_INDENT_APORTANTE, C.NOM_APORTANTE, B.FECHA_CREACION, a.RAZON_SOCIAL
                    FROM EMPRESA A 
                    JOIN (SELECT DISTINCT /*+ PARALLEL(AUTO) */ MAX(cod_planillaunica) AS cod_planillaunica , N_INDENT_APORTANTE, MAX(FECHA_CREACION) AS FECHA_CREACION FROM PLANILLAUNICA_ENC group by N_INDENT_APORTANTE) B ON b.n_indent_aportante = A.CODEMPRESA
                    JOIN PLANILLAUNICA_ENC C ON b.cod_planillaunica = C.cod_planillaunica
                    WHERE C.NOM_APORTANTE <> A.RAZON_SOCIAL";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $cod_empresa = $value['N_INDENT_APORTANTE'];
                    $correccion['RAZON_SOCIAL'] = $value['NOM_APORTANTE'];
                    $correccion['NOMBRE_EMPRESA'] = $value['NOM_APORTANTE'];
                    $log_cambio['CODEMPRESA'] = $cod_empresa;
                    $log_cambio['NOMBRE_EMPRESA_ANTERIOR'] = $value['RAZON_SOCIAL'];
                    $log_cambio['NOMBRE_EMPRESA_NUEVO'] = $value['NOM_APORTANTE'];
                    $this->db->insert('LOG_EMPRESAS_ACTUALIZADAS', $log_cambio);
                    $this->db->where('CODEMPRESA', $cod_empresa);
                    $this->db->update('EMPRESA', $correccion);
                }
            }
        }
    }

    function insertar_historicos_encero() {
        $query = "INSERT INTO HISTORICO_CARTERAS (COD_FISCALIZACION, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES, FECHA, SALDO_SANCION, DIAS_MORA)
                    SELECT /*+ PARALLEL(AUTO) */ A.COD_FISCALIZACION, 0,0,0, A.FECHA+1 AS FECHA, 0, 0 FROM HISTORICO_CARTERAS A 
                    JOIN LIQUIDACION B ON A.COD_FISCALIZACION = B.COD_FISCALIZACION 
                    WHERE A.SALDO_DEUDA <> 0 AND B.SALDO_DEUDA = 0 AND A.FECHA =  (SELECT MAX(FECHA) FROM HISTORICO_CARTERAS WHERE HISTORICO_CARTERAS.COD_FISCALIZACION = A.COD_FISCALIZACION)";
        $this->db->query($query);
        return true;
    }

    function liquidaciones_pendientes_notificar_SGVA($cod_fiscalizacion = null) {
        /*
         * AUMENTAR EN UN DIA LAS RESOLUCIONES NO LEIDAS POR SGVA
         */
        $this->db->query("UPDATE RESOLUCION SET FECHA_PUBLICACION_SGVA = SYSDATE - 1 WHERE LEIDA_SGVA = '0'");
        $this->db->query("UPDATE RESOLUCIONES_SALDADAS_SGVA SET FECHA_PUBLICACION_SGVA = SYSDATE - 1 WHERE LEIDA_SGVA = '0'");
        /*
         * ENVIAR CARTERAS NO NOTIFICADAS A SGVA Y QUE YA ESTAN SALDADAS
         */
        $where = '';
        if (!is_null($cod_fiscalizacion)) {
            $where = " AND COD_FISCALIZACION = '$cod_fiscalizacion'";
        }
        $query = "SELECT NITEMPRESA,COD_FISCALIZACION 
                    FROM LIQUIDACION 
                    WHERE cod_concepto = '3' and SGVA_NOTIFICADO = '0' AND (SALDO_DEUDA <=0 OR CERRAR_ESTADO_CUENTA = '1')$where";

        $query = $this->db->query($query);
        //echo $this->db->last_query();die();
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
        return array();
    }

    function insertar_registro_compensacion($data_insertar) {
        $this->db->set('FECHA_COMPENSACION', "TO_DATE('" . $data_insertar['FECHA_COMPENSACION'] . "','YYYY-MM-DD')", false);
        UNSET($data_insertar['FECHA_COMPENSACION']);
        $this->db->insert('REGISTRO_COMPENSACION', $data_insertar);
    }

    function insertar_log_SGVA($data_insertar) {
        $this->db->insert('LOGS_SGVA', $data_insertar);
    }

    function actualizar_liquidacion_compensacion($data_update) {
        $this->db->set('FECHA_COMPENSACION', "TO_DATE('" . $data_update['FECHA_COMPENSACION'] . "','YYYY-MM-DD')", false);
        UNSET($data_update['FECHA_COMPENSACION']);
        $this->db->where('COD_FISCALIZACION', $data_update['COD_FISCALIZACION']);
        UNSET($data_update['COD_FISCALIZACION']);
        $this->db->update('LIQUIDACION', $data_update);
    }

    function liquidaciones_pendientes_notificar_SIREC($cod_fiscalizacion = null) {
        $where = '';
        if (!is_null($cod_fiscalizacion)) {
            $where = " AND LIQUIDACION.COD_FISCALIZACION = '$cod_fiscalizacion'";
        }
        $query = "select liquidacion.cod_fiscalizacion, resolucion.numero_resolucion, compensacion_soportes.onbase, valorpagado, liquidacion.saldo_deuda, liquidacion.saldo_capital, liquidacion.saldo_interes, TO_CHAR(compensacion_soportes.fecha_documento, 'YYYY-MM-DD') AS fecha_documento
                        from compensacion 
                        join compensacion_soportes on compensacion_soportes.id_compensacion = compensacion.id_compensacion
                        join liquidacion on liquidacion.cod_fiscalizacion = compensacion.cod_fiscalizacion
                        join resolucion on resolucion.cod_fiscalizacion = compensacion.cod_fiscalizacion
                        where compensacion_soportes.documento_ejecutoria is not null and liquidacion.fecha_compensacion is null and valorpagado = liquidacion.saldo_deuda and liquidacion.saldo_interes=0 and liquidacion.saldo_sancion=0$where";

        $query = $this->db->query($query);
        //echo $this->db->last_query();die();
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
        return array();
    }

    function actualizar_liquidacion_notificada_sgva($datos) {
        $this->db->where("COD_FISCALIZACION", $datos['COD_FISCALIZACION']);
        unset($datos['COD_FISCALIZACION']);
        $this->db->update("LIQUIDACION", $datos);
    }

    function getValorLiquidacion($liquidacion)
    /**
     * Función para traer Saldo deuda
     * @para recibe el número de liquidación
     *
     * @return array $datos
     * @return string $datos - error
     */ {
        $this->db->select('SALDO_DEUDA, TOTAL_LIQUIDADO');
        $this->db->where('NUM_LIQUIDACION', $liquidacion);
        $resultado = $this->db->get('LIQUIDACION');
        //echo $this->db->last_query();die();

        if ($resultado->num_rows() > 0):
            $datos = $resultado->result_array();
            return $datos;
        else:
            $datos = 'No existe un Saldo deuda para el número de liquidación';
        endif;
    }

    function consultar_fecha_resolucion($cod_fiscalizacion) {
        $query = "SELECT TO_CHAR(B.PERIODO_INICIAL,'YYYY-MM-DD') AS PERIODO_INICIAL FROM RESOLUCION A JOIN MULTASMINISTERIO B ON B.NRO_RESOLUCION = A.NUMERO_RESOLUCION WHERE A.COD_FISCALIZACION = '$cod_fiscalizacion'";
        $query = $this->db->query($query);
        // echo $this->db->last_query();die();
        if (!empty($query)) {
            if (count($query->result_array()) > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
    }

    function reiniciar_historico_carteras($cod_fiscalizacion) {
        $query = "DELETE FROM HISTORICO_CARTERAS
            WHERE COD_FISCALIZACION = {$cod_fiscalizacion}";
        $query = $this->db->query($query);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    function consultar_compensacion($cod_fiscalizacion) {
        $query = "SELECT INTENCION_COMPENSACION FROM LIQUIDACION WHERE COD_FISCALIZACION =  '{$cod_fiscalizacion}'";
        $query = $this->db->query($query);
        $resultado = $query->result_array();
        if ($resultado[0]['INTENCION_COMPENSACION'] == 'S') {
            return $resultado[0]['INTENCION_COMPENSACION'];
        } else {
            return null;
        }
    }

    function calcula_mora($cod_fiscalizacion) {
        $query = "SELECT CALCULO_MORA FROM LIQUIDACION WHERE COD_FISCALIZACION =  '{$cod_fiscalizacion}'";
        $query = $this->db->query($query);
        $resultado = $query->result_array();
        return $resultado[0]['CALCULO_MORA'];
    }

    function valores_cartera_negativa($cod_fiscalizacion) {
        $query = "SELECT total_liquidado, total_capital, total_intereses, valor_sancion,COD_FISCALIZACION FROM LIQUIDACION WHERE COD_FISCALIZACION =  '{$cod_fiscalizacion}' and (total_liquidado <0 OR total_capital <0 OR total_intereses < 0 OR VALOR_SANCION<0)";
        $query = $this->db->query($query);
        if (count($query->result_array()) > 0) {
            $resultado = $query->result_array();
            return $resultado;
        } else {
            return false;
        }
    }

    function consultar_pagos_cartera($cod_fiscalizacion = null) {
        $where = '';
        if (!is_null($cod_fiscalizacion)) {
            $where = " AND COD_FISCALIZACION = '{$cod_fiscalizacion}'";
        }
        $query = "SELECT COD_PAGO, COD_FISCALIZACION, FECHA_PAGO, COD_SUBCONCEPTO FROM PAGOSRECIBIDOS WHERE FECHA_PAGO >=  TO_DATE('2018-10-01','YYYY-MM-DD') AND COD_FISCALIZACION IS NOT NULL $where";
        $query = $this->db->query($query);
        if (count($query->result_array()) > 0) {
            $resultado = $query->result_array();
            return $resultado;
        } else {
            return false;
        }
    }

    function consultar_pagos_no_registrados_ecollect($ticket = null) {
        $where = '';
        if (!is_null($ticket)) {
            $where = " TICKETID = '{$ticket}' AND ";
        } else {
            $where = " estado = 'CORRECTO' AND ";
        }

        $query = "SELECT * FROM logs_ecollect WHERE $where ticketid not in (select /*+ PARALLEL(AUTO) */ ticketid from pagosrecibidos where ticketid is not null group by ticketid) AND fecha_creacion >= TO_DATE('2019-10-08','YYYY-MM-DD')";
        $query = $this->db->query($query);
        //echo $this->db->last_query();die();
        if (count($query->result_array()) > 0) {
            $resultado = $query->result_array();
            return $resultado;
        } else {
            return false;
        }
    }

    function consultar_si_es_resolucion($cod_fiscalizacion, $fecha_pago) {
        $query = "SELECT COD_FISCALIZACION, FECHA_EJECUTORIA FROM LIQUIDACION WHERE FECHA_EJECUTORIA < TO_DATE('{$fecha_pago}','YYYY-MM-DD') AND COD_FISCALIZACION = '{$cod_fiscalizacion}'";
        $query = $this->db->query($query);
        if (count($query->result_array()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function consultar_fecha_ejecutoria($cod_fiscalizacion) {
        $query = "SELECT COD_FISCALIZACION, TO_CHAR(FECHA_EJECUTORIA,'YYYY-MM-DD') AS FECHA_EJECUTORIA FROM LIQUIDACION WHERE COD_FISCALIZACION = '{$cod_fiscalizacion}' AND BLOQUEADA = '0'";
        $query = $this->db->query($query);
        return $query->result_array();
    }

    function borrar_historico_cartera($cod_fiscalizacion) {
        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $this->db->delete('HISTORICO_CARTERAS');
    }

    function consulta_carteras_reiniciar_masiva() {
        $query = "SELECT COD_FISCALIZACION FROM LIQUIDACION WHERE SOLICITUD_REINICIO = '1'";
        $query = $this->db->query($query);
        return $query->result_array();
    }

    function reiniciar_cartera_correccion($cod_fiscalizacion) {
        $query = "SELECT COD_FISCALIZACION, NVL(TOTAL_LIQUIDADO,0) AS TOTAL_LIQUIDADO, NVL(TOTAL_CAPITAL,0) AS TOTAL_CAPITAL,NVL(TOTAL_INTERESES,0) AS TOTAL_INTERESES,NVL(VALOR_SANCION,0) AS VALOR_SANCION,NVL(SALDO_DEUDA_TMP,0) AS SALDO_DEUDA_TMP,NVL(SALDO_INTERESES_TMP,0) AS SALDO_INTERESES_TMP,NVL(SALDO_SANCION_TMP,0) AS SALDO_SANCION_TMP from liquidacion where cod_fiscalizacion = '{$cod_fiscalizacion}'";
        $query = $this->db->query($query);
        $cantidad_resultado = count($query->result_array());
        $resultado = $query->result_array();
        if (!empty($query)) {
            if ($cantidad_resultado > 0) {
                foreach ($resultado as $key => $value) {
                    $total = 0;
                    $capital = 0;
                    $interes = 0;
                    $sancion = 0;
                    if ($value['SALDO_DEUDA_TMP'] == '0') {
                        $total = $value['TOTAL_LIQUIDADO'];
                        $capital = $value['TOTAL_CAPITAL'];
                        $interes = $value['TOTAL_INTERESES'];
                        $sancion = $value['VALOR_SANCION'];
                    } else {
                        $total = $value['SALDO_DEUDA_TMP'] + $value['SALDO_INTERESES_TMP'] + $value['SALDO_SANCION_TMP'];
                        $capital = $value['SALDO_DEUDA_TMP'];
                        $interes = $value['SALDO_INTERESES_TMP'];
                        $sancion = $value['SALDO_SANCION_TMP'];
                    }

                    $this->db->where('COD_FISCALIZACION', $value['COD_FISCALIZACION']);
                    $this->db->set('DIAS_MORA', '0');
                    $this->db->set('DIAS_MORA_APLICADA', '0');
                    $this->db->set('SALDO_DEUDA', $total);
                    $this->db->set('SALDO_CAPITAL', $capital);
                    $this->db->set('SALDO_INTERES', $interes);
                    $this->db->set('SALDO_SANCION', $sancion);
                    $this->db->update('LIQUIDACION');
                }
            }
        }
    }

    public function consultar_existencia_tasa() {
        $fecha_actual = date('Y-m-d');
        $query = $this->db->query("SELECT ID_TASA_SUPERINTENDENCIA from TASA_SUPERINTENDENCIA where VIGENCIA_HASTA >=  TO_DATE('$fecha_actual','YYYY-MM-DD')");
        $resultado = $query->result_array();
        if (@$resultado[0] == '') {
            return false;
        } else {
            return true;
        }
    }

    function validar_resultado_correccion($cod_fiscalizacion) {
        $query = "SELECT COD_FISCALIZACION, SALDO_DEUDA, SALDO_CAPITAL, SALDO_INTERES from liquidacion where cod_fiscalizacion = '{$cod_fiscalizacion}'";
        $query = $this->db->query($query);
        $resultado = $query->result_array();
        $query = "SELECT * FROM historico_carteras WHERE COD_FISCALIZACION = '{$cod_fiscalizacion}' and ROWNUM = 1 order by fecha desc";
        $query = $this->db->query($query);
        $resultado_historico = $query->result_array();
        $cantidad_resultado_historico = count($resultado_historico);
        $query = "SELECT * FROM (SELECT COD_PAGO,FECHA_PAGO,DISTRIBUCION_CAPITAL,DISTRIBUCION_INTERES FROM pagosrecibidos WHERE COD_FISCALIZACION = '{$cod_fiscalizacion}' AND DISTRIBUCION_CAPITAL > 0 order by cod_pago DESC) WHERE ROWNUM =1";
        $query = $this->db->query($query);
        $resultado_ultimo_pago = $query->result_array();
        if ($resultado[0]['SALDO_DEUDA'] < 0) {
            if ($cantidad_resultado_historico == 0) {
                $valor_descontar = 0;
                $valor_descontar = abs($resultado[0]['SALDO_DEUDA']);
                $this->db->query("UPDATE PAGOSRECIBIDOS SET DISTRIBUCION_CAPITAL = VALOR_PAGADO - {$valor_descontar} WHERE COD_FISCALIZACION = '{$cod_fiscalizacion}'");
            } else {
                $distribucion_capital = $resultado_ultimo_pago[0]['DISTRIBUCION_CAPITAL'] + $resultado[0]['SALDO_CAPITAL'];
                $distribucion_interes = $resultado_ultimo_pago[0]['DISTRIBUCION_INTERES'] + $resultado[0]['SALDO_INTERES'];
                //$distribucion_capital = $resultado_historico[0]['SALDO_CAPITAL'];
                //$distribucion_interes = $resultado_historico[0]['SALDO_INTERES'];
                $this->db->query("UPDATE PAGOSRECIBIDOS SET DISTRIBUCION_CAPITAL ={$distribucion_capital} ,DISTRIBUCION_INTERES = {$distribucion_interes} WHERE COD_PAGO = '{$resultado_ultimo_pago[0]['COD_PAGO']}'");
            }
            $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
            $this->db->set('SALDO_DEUDA', 0);
            $this->db->set('SALDO_CAPITAL', 0);
            $this->db->set('SALDO_INTERES', 0);
            $this->db->set('SALDO_SANCION', 0);
            $this->db->update('LIQUIDACION');
        }
        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $this->db->set('SOLICITUD_REINICIO', '0');
        $this->db->update('LIQUIDACION');
    }

    function guardar_estado_cuenta_SGVA($cod_fiscalizacion, $estado_cuenta) {
        $this->db->set('ID_ESTADOCUENTA', $estado_cuenta);
        $this->db->where('COD_FISCALIZACION', $cod_fiscalizacion);
        $this->db->update('FISCALIZACION');
    }

    function getDepuracionCartera() {

        $str_query = "SELECT  dc.COD_FISCALIZACION as COD_FISCAR, dc.RESOLUCION_DEPURACION,
        dc.FECHA_DEPURACION, gc.NIT_EMPRESA from depuracion_contable dc
        inner join  ejecutoria e on e.COD_FISCALIZACION=dc.COD_FISCALIZACION
        inner join gestioncobro gc on gc.COD_GESTION_COBRO =e.COD_GESTION_COBRO
        where dc.ESTADO = 1 and e.COD_FISCALIZACION not in(
      select COD_FISCALIZACION_EMPRESA from recepciontitulos
   ) and dc.MOSTRARNOTIFICACION is null
        ";
        $query = $this->db->query($str_query);
        //   echo $this->db->last_query();exit;


        $datos = $query->result_array;
        return $datos;
    }

    function getDepuracionCarteraNoMisional($cod_regional) {

        $str_query = "SELECT dc.COD_NOMISIONAL AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION, 
        dc.CAUSAL,CNM_E.IDENTIFICACION  AS NIT_EMPRESA, 
        UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL
        FROM depuracion_contable dc
        inner join cnm_carteranomisional cn on dc.COD_NOMISIONAL=cn.COD_CARTERA_NOMISIONAL
        INNER JOIN CNM_EMPLEADO CNM_E ON cn.COD_EMPLEADO= CNM_E.IDENTIFICACION 
        LEFT join causaldepuracion cd on  CAST ( cd.NOMBRE_CAUSAL AS char )= dc.causal 
        where dc.ESTADO = 1 and cn.cod_regional=$cod_regional
        and  NOT EXISTS  (
        SELECT
            NULL
        FROM
            recepciontitulos RC   
        WHERE 
        
        dc.cod_nomisional=RC.cod_cartera_nomisional
    )and dc.MOSTRARNOTIFICACION is null
       
        UNION (
        SELECT  dc.COD_NOMISIONAL  AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION, 
        dc.CAUSAL, cast(CNM_EM.COD_ENTIDAD as int) as NIT_EMPRESA ,CNM_EM.RAZON_SOCIAL
        FROM depuracion_contable dc
        inner join cnm_carteranomisional cn on dc.COD_NOMISIONAL=cn.COD_CARTERA_NOMISIONAL 
        INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD=  cn.COD_EMPRESA
        LEFT join causaldepuracion cd on  CAST ( cd.NOMBRE_CAUSAL AS char )= dc.causal
        where cn.cod_regional=$cod_regional
        and  NOT EXISTS  (
        SELECT
            NULL
        FROM
            recepciontitulos RC   
        WHERE 
        
        dc.cod_nomisional=RC.cod_cartera_nomisional
    ) and dc.MOSTRARNOTIFICACION is null
         )
        ";
        $query = $this->db->query($str_query);
        //echo $this->db->last_query();exit;
        $datos = $query->result_array;
        return $datos;
    }

    function getDepuracionCarteraCoactivo() {

        $str_query = "SELECT dc.COD_FISCALIZACION AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,rc.NIT_EMPRESA 
        from depuracion_contable dc
        inner join recepciontitulos rc on  dc.COD_FISCALIZACION= rc.COD_FISCALIZACION_EMPRESA
        
        UNION (
        select CAST (dc.COD_NOMISIONAL AS INT) AS COD_FISCAR,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,rc.NIT_EMPRESA 
        from depuracion_contable dc
        inner join recepciontitulos rc on  dc.COD_NOMISIONAL= rc.COD_CARTERA_NOMISIONAL
        )";
        $query = $this->db->query($str_query);
        //   echo $this->db->last_query();exit;
        $datos = $query->result_array;
        return $datos;
    }


    function tasa_efectivaAportes($date) {

        $consulta = $this->db->query("
        SELECT TS.TASA_SUPERINTENDENCIA FROM TASA_SUPERINTENDENCIA TS 
        WHERE TO_DATE(ts.vigencia_desde) = TO_DATE('$date', 'YY/MM/DD')");
        if ($consulta) {
           
            if (count($consulta->result_array()) > 0) {
               return $consulta->result_array()[0]['TASA_SUPERINTENDENCIA'];
            }
        }else{
            return -1;
        }
        
    }


    function updateTipoLiquidacion($codFiscalizacion,$tipoLiquidacion){
        $this->db->set('TIPOLIQUIDACION', $tipoLiquidacion);
        $this->db->where('COD_FISCALIZACION', $codFiscalizacion);
        $resultado = $this->db->update('LIQUIDACION');
    }

    function updateUGLiquidacion($codFiscalizacion){
        $this->db->set('UG', 1);
        $this->db->where('COD_FISCALIZACION', $codFiscalizacion);
        $resultado = $this->db->update('LIQUIDACION');
    }

    function getExisteLiquidacionDet($liquidacion,$anio)
    /**
     * Función para traer Saldo deuda
     * @para recibe el número de liquidación
     *
     * @return array $datos
     * @return string $datos - error
     */ {
        $this->db->select("*", FALSE);
      //  $this->db->select('TO_CHAR(PRIMER_MES_BASE, "MM/DD/YYYY")');
        $this->db->where('CODLIQUIDACIONAPORTES_P', $liquidacion);
        $this->db->where('ANO', $anio);
        $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_DET');
        //echo $this->db->last_query();die();

        if ($resultado->num_rows() > 0):
            $datos = $resultado->result_array()[0];
            return 1;
        else:
            return 0;
        endif;
    }

    function getBaseMensual($liquidacion,$anio)
    {


        $this->db->select("SUM(BASE) AS BASEANUAL, SUM(INTERES) AS INTERES ,SUM(VALOR) AS BASE", FALSE);
      //  $this->db->select('TO_CHAR(PRIMER_MES_BASE, "MM/DD/YYYY")');
        $this->db->where('NUM_LIQUIDACION', $liquidacion);
        $this->db->where('SUBSTR(PERIODO,1,4)', $anio);
        $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
       // echo $this->db->last_query();die();

        if ($resultado->num_rows() > 0):
            $datos = $resultado->result_array();
            return $datos;
        else:
            $datos = 'No hay fecha de la primera base';
        endif;
    }
    function getBaseMensualTotal($liquidacion)
    {


        $this->db->select("SUM(VALOR)AS BASE, SUM(BASE) AS BASEANUAL, SUM(INTERES) AS INTERES, SUM(TOTAL) AS TOTAL", FALSE);
      //  $this->db->select('TO_CHAR(PRIMER_MES_BASE, "MM/DD/YYYY")');
        $this->db->where('NUM_LIQUIDACION', $liquidacion);
       // $this->db->where('SUBSTR(PERIODO,1,4)', $anio);
        $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
       // echo $this->db->last_query();die();

        if ($resultado->num_rows() > 0):
            $datos = $resultado->result_array();
            return $datos;
        else:
            $datos = 'No hay fecha de la primera base';
        endif;
    }

   
    function updateLiquidacionMensual($cod_fiscalizacion, $num) {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
        $this->db->set('NUM_LIQUIDACION', $num, FALSE);
        $this->db->where('NUM_LIQUIDACION', $cod_fiscalizacion);
        $resultado = $this->db->update('LIQ_APORTESPARAFISCALES_MES');
        if ($this->db->trans_status() === FALSE) :
            $this->db->trans_rollback();
            return $this->db->last_query();
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

    function getExisteLiquidacion($liquidacion,$periodo)
    {
        $this->db->select("*");
        $this->db->where('NUM_LIQUIDACION', $liquidacion);
        if($periodo!=0){
            $this->db->where('PERIODO', $periodo);
        }
      
        $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
///echo $this->db->last_query();
        if ($resultado->num_rows() > 0):
           
            return TRUE;
        else:
            return FALSE;
        endif;
    }
   
    
    function getValoresMensual($liquidacion,$anio)
     {
        $this->db->select("*", FALSE);
      //  $this->db->select('TO_CHAR(PRIMER_MES_BASE, "MM/DD/YYYY")');
        $this->db->where('NUM_LIQUIDACION', $liquidacion);
        $this->db->where('SUBSTR(PERIODO,1,4)', $anio);
        $this->db->order_by('PERIODO', 'ASC');
        $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
       //echo $this->db->last_query();die();

        if ($resultado->num_rows() > 0):
            $datos = $resultado->result_array();
            return $datos;
        else:
            $datos = 'No hay fecha de la primera base';
        endif;
    }

    //CONSULTA PARA DETERMINAR SI LA FISCALIZACIÃ“N YA TIENE LIQUIDACION APORTES
    function consultarLiquidacionAportesAnio($codigoLiquidacion,$anio)
    /**
     * FunciÃ³n que retorna todos los registros disponibles de una liquidaciÃ³n en la tabla detalle para aportes parafizcales
     * @param string $codigoLiquidacion
     * @return array $detalles
     * @return boolean false - error
     */ {
        $this->db->trans_begin();
        $this->db->trans_strict(TRUE);
      
        $str_query = "SELECT substr(PERIODO, 1, 4) as ANO,SUM(SUPERNUMERARIOS) AS SUPERNUMERARIOS,
        SUM(SALARIOESPECIE) AS SALARIOESPECIE,
        SUM(VALORSUELDOS) AS VALORSUELDOS,
        SUM(VALORSOBRESUELDOS) AS VALORSOBRESUELDOS,
        SUM(SALARIOINTEGRAL) AS SALARIOINTEGRAL,
        SUM(COMISIONES) AS COMISIONES,
        SUM(PORCENTAJEVENTAS) AS POR_SOBREVENTAS,
        SUM(VACACIONES) AS VACACIONES,
        SUM(TRABAJODOMICILIO) AS TRAB_DOMICILIO,
        SUM(CONTRATOSSUBCONTRATOS) AS SUBCONTRATO,
        SUM(PRIMASALARIAL) AS PRIMA_TEC_SALARIAL,
        SUM(AUX_SUBSIDIOALIMENTACION) AS AUXILIO_ALIMENTACION,
        SUM(PRIMA_SERVICIO) AS PRIMA_SERVICIO,
        SUM(PRIMA_LOCALIZACION) AS PRIMA_LOCALIZACION,
        SUM(PRIMA_VIVIENDA) AS PRIMA_VIVIENDA,
        SUM(GASTOS_REPRESENTACION) AS GAST_REPRESENTACION,
        SUM(PRIMA_INCREMENTO_ANTIGUEDAD) AS PRIMA_ANTIGUEDAD,
        SUM(PRIMA_PRODUCTIVIDAD) AS PRIMA_EXTRALEGALES,
        SUM(PRIMA_VACACIONES) AS PRIMA_VACACIONES,
        SUM(PRIMA_NAVIDAD) AS PRIMA_NAVIDAD,
        SUM(JORNALES) AS JORNALES,
        SUM(AUXILIOTRANSPORTE) AS AUXILIOTRANSPORTE,
        SUM(HORASEXTRAS) AS HORASEXTRAS,
        SUM(DOMINICALES_FESTIVOS)AS DOMINICALES_FESTIVOS,
        SUM(RECARGONOCTURNO) AS RECARGONOCTURNO,
        SUM(VIATICOS) AS VIATICOS,
        SUM(BONIFICACIONES) AS BONIFICACIONES,
        SUM(CONTRATOS_AGRICOLAS) AS CONTRATOS_AGRICOLAS,
        SUM(REMU_SOCIOS_INDUSTRIALES) AS REMU_SOCIOS_INDUSTRIALES,
        SUM(HORA_CATEDRA) AS HORA_CATEDRA,
        SUM(OTROS_PAGOS) AS OTROS_PAGOS
        FROM LIQ_APORTESPARAFISCALES_MES
        WHERE NUM_LIQUIDACION =  $codigoLiquidacion
        AND substr(PERIODO, 1, 4)=$anio
        GROUP BY substr(PERIODO, 1, 4)";

        $query = $this->db->query($str_query);
        $array = $query->result_array();
      
   

       $resultado = $this->db->get('LIQ_APORTESPARAFISCALES_MES');
       // $resultado = $this->db->get_where('LIQ_APORTESPARAFISCALES_DET', "CODLIQUIDACIONAPORTES_P = '" . $codigoLiquidacion . "'");
        // $resultado = $this -> db -> get_where('LIQ_APORTESPARAFISCALES_DET',  "CODLIQUIDACIONAPORTES_P = '1179951894110'");
        //#####BUGGER PARA LA CONSULTA ######
       // $resultado = $this -> db -> last_query();
        //echo $resultado; //die();
        //#####BUGGER PARA LA CONSULTA ######
        
        $datos = $query->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $detalle):
                $tmp[] = $detalle;
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    }




    function agregarDevolucion($liquidacion, $liquidacion_previa)
    /**
     * FunciÃ³n que inserta valores en la tabla maestro y detalle de liquidacion de aportes
     * Responde si la transacciÃ³n fue exitosa, de no serlo realiza un rollback sobre la tabla.
     *
     * @param array $liquidacion
     * @param int $liquidacion_previa
     * @return boolean true - exito
     * @return string last_query - error
     */ {

      //  $this->db->trans_begin();
       // $this->db->trans_strict(TRUE);
        $this->db->set('VALOR_DEVOLUCION', $liquidacion['VALOR_DEVOLUCION']);
        $this->db->set('NIT', $liquidacion['NIT']);
        $this->db->set('COD_CONCEPTO', $liquidacion['COD_CONCEPTO']);
        $this->db->set('REGIONAL_DEVOLUCION', $liquidacion['REGIONAL_DEVOLUCION']);
        $this->db->set('OBSERVACIONES', "Devolucion de pago a favor del empresario en la liquidacion");
        $this->db->set('COD_RESPUESTA', 1593);
      

        if ($liquidacion_previa == 0):
           
            $resultado = $this->db->insert('SOLICITUDDEVOLUCION');
        else:
            $this->db->where('NIT', $liquidacion['NIT']);
            $this->db->where('COD_CONCEPTO', $liquidacion['COD_CONCEPTO']);
            $this->db->where('REGIONAL_DEVOLUCION', $liquidacion['REGIONAL_DEVOLUCION']);
            $this->db->where('COD_RESPUESTA', 1593);
            $resultado = $this->db->update('SOLICITUDDEVOLUCION');
        endif;
//#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        // echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        //verificacion de la transacciÃ³n
        if ($this->db->trans_status() === FALSE) :
            
            $this->db->trans_rollback();
            return False;
        else:
            $this->db->trans_commit();
            return TRUE;
        endif;
    }

   

    function existeDevolucion($liquidacion)
    {
        $this->db->select("*");
        $this->db->where('NIT', $liquidacion['NIT']);
        $this->db->where('COD_CONCEPTO', $liquidacion['COD_CONCEPTO']);
        $this->db->where('REGIONAL_DEVOLUCION', $liquidacion['REGIONAL_DEVOLUCION']);
        $this->db->where('OBSERVACIONES', "Devolucion de pago a favor del empresario en la liquidacion");
        $this->db->where('COD_RESPUESTA', 1593);
        $resultado = $this->db->get('SOLICITUDDEVOLUCION');
///echo $this->db->last_query();
        if ($resultado->num_rows() > 0):
           
            return TRUE;
        else:
            return FALSE;
        endif;
    }

      //CONSULTA PARA DETERMINAR SI LA FISCALIZACIÃ“N YA TIENE LIQUIDACION APORTES
      function consultarLiquidacionAportesConsolidado($codigoLiquidacion)
       {
        $resultado = $this->db->get_where('LIQ_APORTESPARAFISCALES_DET', "CODLIQUIDACIONAPORTES_P = '" . $codigoLiquidacion . "'");
        // $resultado = $this -> db -> get_where('LIQ_APORTESPARAFISCALES_DET',  "CODLIQUIDACIONAPORTES_P = '1179951894110'");
        //#####BUGGER PARA LA CONSULTA ######
        //$resultado = $this -> db -> last_query();
        //echo $resultado; die();
        //#####BUGGER PARA LA CONSULTA ######
        $datos = $resultado->result_array();
        if (!empty($datos)):
            $tmp = NULL;
            foreach ($datos as $detalle):
                $tmp[] = $detalle;
            endforeach;
            $datos = $tmp;
        else:
            $datos = FALSE;
        endif;
        return $datos;
    
      }
}

/* End of file liquidaciones_model.php */        