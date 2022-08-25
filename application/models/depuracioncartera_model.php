<?php

/**
*  @author avrivera
*   
 * */
class depuracioncartera_model extends MY_Model
{

    private $nit;

    function __construct()
    {
        parent::__construct();
    }

    function SetNit($nit)
    {
        $this->nit = $nit;
    }

    function buscar($cod_regional)
    {
        $datos = NULL;
        if (!empty($this->nit)) {
            $query = "SELECT
            TO_CHAR(cnm_empleado.identificacion) AS codempresa,
            cnm_empleado.nombres
             || ' '
             || cnm_empleado.apellidos AS razon_social,
            cnm_empleado.cod_regional,
            1 AS tipocartera
        FROM
            cnm_empleado
        INNER JOIN CNM_CARTERANOMISIONAL CNM ON CNM.COD_EMPLEADO = cnm_empleado.identificacion
        WHERE
            TO_CHAR(cnm_empleado.identificacion) LIKE '%" . mb_strtoupper($this->nit) . "%' /*AND  cnm_empleado.cod_regional=*/
             OR  upper(cnm_empleado.nombres || ' ' || cnm_empleado.apellidos) LIKE '%" . mb_strtoupper($this->nit) . "%'
             
        UNION ( SELECT
            cnm_empresa.cod_entidad AS codempresa,
            cnm_empresa.razon_social,
            cnm_empresa.cod_regional,
            2 AS tipocartera
        FROM
            cnm_empresa
            INNER JOIN CNM_CARTERANOMISIONAL CNM ON CNM.COD_EMPRESA = cnm_empresa.cod_entidad
        WHERE  cnm_empresa.cod_entidad LIKE '%" . mb_strtoupper($this->nit) . "%' /*AND cnm_empresa.cod_regional = */ OR 
                upper(cnm_empresa.razon_social) LIKE '%" . mb_strtoupper($this->nit) . "%'
                
        UNION ( SELECT
            empresa.codempresa,
            empresa.razon_social,
            empresa.cod_regional,
            3 AS tipocartera
        FROM
            empresa
            INNER JOIN resolucion ON resolucion.nitempresa = empresa.codempresa
            INNER JOIN ejecutoria ej ON ej.cod_fiscalizacion = resolucion.cod_fiscalizacion
            INNER JOIN liquidacion lq ON lq.cod_fiscalizacion = resolucion.cod_fiscalizacion
            INNER JOIN fiscalizacion f ON f.cod_fiscalizacion= lq.cod_fiscalizacion
            INNER JOIN asignacionfiscalizacion af  ON af.COD_ASIGNACIONFISCALIZACION=f.COD_ASIGNACION_FISC
        WHERE  empresa.codempresa LIKE '%" . mb_strtoupper($this->nit) . "%'/* AND  af.cod_regional =*/ OR 
                upper(empresa.razon_social) LIKE '%" . mb_strtoupper($this->nit) . "%'
                AND lq.fecha_ejecutoria IS NOT NULL
             
            )
           ) 
            
             ORDER BY
            1,
            2 ASC";
            $query = $this->db->query($query);
            $datos = $query->result_array;

            $query2 = "SELECT TO_CHAR(CNM_EMPLEADO.IDENTIFICACION) AS CODEMPRESA,
            to_char(CNM_CARTERANOMISIONAL.COD_CARTERA_NOMISIONAL) as NUMERO,CNM_CARTERANOMISIONAL.COD_TIPOCARTERA as CONCEPTOCARTERA
            FROM CNM_EMPLEADO 
             inner join CNM_CARTERANOMISIONAL ON CNM_CARTERANOMISIONAL.COD_EMPLEADO=CNM_EMPLEADO.IDENTIFICACION
            WHERE (TO_CHAR(CNM_EMPLEADO.IDENTIFICACION) LIKE '%" . mb_strtoupper($this->nit) . "%') OR
           (UPPER(CNM_EMPLEADO.NOMBRES || ' ' || CNM_EMPLEADO.APELLIDOS) LIKE '%" . mb_strtoupper($this->nit) . "%')
           
           UNION(
           
           SELECT                   CNM_EMPRESA.COD_ENTIDAD AS CODEMPRESA,to_char(CNM_CARTERANOMISIONAL.COD_CARTERA_NOMISIONAL) as NUMERO,CNM_CARTERANOMISIONAL.COD_TIPOCARTERA as CONCEPTOCARTERA
           FROM                                   CNM_EMPRESA
           inner join CNM_CARTERANOMISIONAL ON CNM_CARTERANOMISIONAL.COD_EMPLEADO=CNM_EMPRESA.COD_ENTIDAD
           WHERE                                (CNM_EMPRESA.COD_ENTIDAD LIKE '%" . mb_strtoupper($this->nit) . "%') OR
           (UPPER(CNM_EMPRESA.RAZON_SOCIAL) LIKE '%" . mb_strtoupper($this->nit) . "%')
           )
           
           UNION(
           SELECT     EMPRESA.CODEMPRESA,RESOLUCION.NUMERO_RESOLUCION as NUMERO, RESOLUCION.COD_CPTO_FISCALIZACION as CONCEPTOCARTERA
           FROM                    EMPRESA
            JOIN RESOLUCION ON RESOLUCION.NITEMPRESA=EMPRESA.CODEMPRESA
            INNER JOIN EJECUTORIA EJ ON EJ.COD_FISCALIZACION=RESOLUCION.COD_FISCALIZACION
            INNER JOIN LIQUIDACION LQ ON LQ.COD_FISCALIZACION=RESOLUCION.COD_FISCALIZACION
            INNER JOIN fiscalizacion f ON f.cod_fiscalizacion= lq.cod_fiscalizacion
            INNER JOIN asignacionfiscalizacion af  ON af.COD_ASIGNACIONFISCALIZACION=f.COD_ASIGNACION_FISC
                                                   WHERE                     (EMPRESA.CODEMPRESA LIKE '%" . mb_strtoupper($this->nit) . "%') 
                                                  
                                                   OR
                                                                   (UPPER(EMPRESA.RAZON_SOCIAL) LIKE '%" . mb_strtoupper($this->nit) . "%')
                                                                    AND lq.fecha_ejecutoria IS NOT NULL
                                               ) 
                                           
                                           ORDER BY 1, 2 ASC";
            $query2 = $this->db->query($query2);
         //   echo "query: ".$this->db->last_query();exit();
            $datos2 = $query2->result_array;
        }
        if (!empty($datos)) {
            $tmp = NULL;
            foreach ($datos as $nit) {

                $tmp[] = array(
                    "value" => $nit['CODEMPRESA'],
                    "label" => $nit['CODEMPRESA'] . " :: " . $nit['RAZON_SOCIAL'],
                    "razon" => $nit['RAZON_SOCIAL'],
                    "regional" => $nit['COD_REGIONAL'],
                    "tipo" => $nit['TIPOCARTERA'],
                     "nit" => (is_numeric($nit['CODEMPRESA'])) ? number_format($nit['CODEMPRESA'], 0, ".", ".") : $nit['CODEMPRESA']
                );

                if (!empty($datos2)) {
                    foreach ($datos2 as $nit2) {

                        $tmp[] = array(
                            "numero" => $nit2['NUMERO'],
                            "concepto" => $nit2['CONCEPTOCARTERA']
                            
                        );
                    }
                }
            }

            $datos = $tmp;
        }
        return $datos;
    }

    function getResolucionMisional($codFis) {

        $str_query ="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    L.NITEMPRESA AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL, R.NUMERO_RESOLUCION,
                    R.FECHA_CREACION,L.SALDO_CAPITAL as SALDOACTUAL,L.SALDO_INTERES AS SALDO_INTERES, R.COD_CPTO_FISCALIZACION AS CONCEPTO
                    FROM LIQUIDACION L 
                    INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
                    INNER JOIN EMPRESA E ON E.CODEMPRESA=R.NITEMPRESA
                    INNER JOIN REGIONAL RG ON E.COD_REGIONAL=RG.COD_REGIONAL 
                    WHERE L.COD_FISCALIZACION = $codFis   " ;
                    $query = $this->db->query($str_query);
        return $query->result_array;

    }



    function getDataDepuracion2($codEmpresa, $nrescartera) {
        //   

        $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
        CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
        UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
        CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
        FROM CNM_CARTERANOMISIONAL CNM
        INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
        INNER JOIN CNM_CALAMIDAD_DOMESTICA CNM_CLD ON CNM_CLD.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
        LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
        WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_CLD.NUM_RESOLUCION = '$nrescartera'";
        $consulta = $this->db->query($str_query);
         $query1 = $consulta->result_array;
         
        if(count($query1) > 0){
        $query=$query1 ;
        return $query;
      
        }elseif(count($query1) == 0){
            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN CNM_CARTERA_PREST_EDUCATIVO CNM_PE ON CNM_PE.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
            LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
            WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_PE.NUMERO_RESOLUCION = '$nrescartera'";
            $consulta2= $this->db->query($str_query);
            $query2 = $consulta2->result_array;
            $query=$query2 ;
        }if(count($query2) > 0){
            $query=$query2 ;
        return $query;
        
        }
        elseif(count(@$query2) == 0){
            
            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN CNM_CARTERA_RESPON_BIENES CNM_FN ON CNM_FN.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
            LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
            WHERE CNM.COD_EMPLEADO = '$codEmpresa' AND  CNM_FN.NUMERO_RESOLUCION = '$nrescartera'";
            $consulta2= $this->db->query($str_query);
            $query3 = $consulta2->result_array;
            $query=$query3 ;
        }if(count($query3) > 0){
            $query=$query3 ;
        return $query;
        
        }

        elseif(count(@$query3) == 0){
        
            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN CNM_CARTERA_PRESTAMO_HIPOTEC CNM_PH ON CNM_PH.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
            LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
            WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_PH.NUMERO_ESCRITURA = '$nrescartera'";
            $consulta2= $this->db->query($str_query);
            $query4 = $consulta2->result_array;
            $query=$query4 ;
        } if(count($query4) > 0){
            $query=$query4 ;
        
        return $query;
        
        }elseif(count(@$query4) == 0){

            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN CNM_CARTERA_DOBLE_MESADA CNM_DP ON CNM_DP.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
            LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
            WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_DP.NUMERO_RESOLUCION = '$nrescartera'";
            $consulta2= $this->db->query($str_query);
            $query5 = $consulta2->result_array;
            $query=$query5 ;
        }if(count($query5) > 0){
            $query=$query5 ;
        
        return $query;
        
        }
        elseif(count(@$query5) == 0){
  
        $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN CNM_CARTERA_CONVENIOS CNM_CV ON CNM_CV.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
            LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
            WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_CV.NUMERO_CONVENIO = '$nrescartera'";
            $consulta2= $this->db->query($str_query);
           $query6 = $consulta2->result_array;
            $query=$query6 ;
        }if(count($query6) > 0){
            $query=$query6 ;
        
        return $query;
        
        }
        elseif(count(@$query6) == 0){
            
            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
                UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
                CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
                FROM CNM_CARTERANOMISIONAL CNM
                INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                INNER JOIN CNM_PRESTAMO_AHORRO CNM_PD ON CNM_PD.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
                LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_PD.NUMERO_RESOLUCION = '$nrescartera'";
                $consulta2= $this->db->query($str_query);
                $query7 = $consulta2->result_array;
                $query=$query7 ;
        }if(count($query7) > 0){
            $query=$query7 ;
        
        return $query;
        
        }
        elseif(count(@$query7) == 0){

            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
                UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
                CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
                FROM CNM_CARTERANOMISIONAL CNM
                INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                INNER JOIN CNM_CARTERA_CUOTA_PARTE CNM_CP ON CNM_CP.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
                LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_CP.NUMERO_RESOLUCION = '$nrescartera'";
                $consulta2= $this->db->query($str_query);
                $query8 = $consulta2->result_array;
                $query=$query8 ;
        }if(count($query8) > 0){
            $query=$query8 ;
        
        return $query;
        
        }

        elseif(count(@$query8) == 0){
            
            $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO, '1' NOMIS,
                UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
                CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
                FROM CNM_CARTERANOMISIONAL CNM
                INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                INNER JOIN CM_SEVICIOS_MEDICOS CNM_SM ON CNM_SM.COD_CARTERA= CNM.COD_CARTERA_NOMISIONAL 
                LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                WHERE CNM.COD_EMPLEADO = '$codEmpresa'  AND  CNM_SM.ORDEN = '$nrescartera'";
                $consulta2= $this->db->query($str_query);
                $query9 = $consulta2->result_array;
                $query=$query9 ;
        }if(count($query9) > 0){
            $query=$query9 ;
        
        return $query;
        
        }  
    }


    function getDataDepuracion($codEmpresa, $tipoCartera,$nrescartera) {

        switch ($tipoCartera) {
            case "3":
                $str_query ="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    L.NITEMPRESA AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL, R.NUMERO_RESOLUCION,
                    R.FECHA_CREACION,L.SALDO_DEUDA AS SALDODEUDA,L.SALDO_CAPITAL as SALDOACTUAL,L.SALDO_INTERES AS SALDO_INTERES, R.COD_CPTO_FISCALIZACION AS CONCEPTO
                    FROM LIQUIDACION L 
                    INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
                    INNER JOIN EMPRESA E ON E.CODEMPRESA=R.NITEMPRESA
                    INNER JOIN REGIONAL RG ON R.COD_REGIONAL=RG.COD_REGIONAL 
                    INNER JOIN EJECUTORIA EJ ON EJ.COD_FISCALIZACION=R.COD_FISCALIZACION
                    WHERE L.FECHA_EJECUTORIA IS NOT NULL AND L.NITEMPRESA = $codEmpresa  AND L.SALDO_CAPITAL>0 AND   R.NUMERO_RESOLUCION = '$nrescartera'  " ;
                $query = $this->db->query($str_query);
                 //echo $this->db->last_query();exit;
                return $query->result_array;
            break;
            case "1":         //   

                $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO,
                    UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,O AS SALDODEUDA,
                    CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO
                    FROM CNM_CARTERANOMISIONAL CNM
                    INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                    LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                    WHERE CNM.SALDO_DEUDA> 0 AND CNM.COD_EMPLEADO = $codEmpresa  AND  CNM.COD_CARTERA_NOMISIONAL = $nrescartera";
                $query = $this->db->query($str_query);
                // echo $this->db->last_query();exit;
                return $query->result_array;
            break;
            case "2":
                $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    CNM.COD_EMPRESA AS CODIGOEMPRESAEMPLEADO,
                    CNM_EM.RAZON_SOCIAL, O AS SALDODEUDA, CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES,CNM.COD_TIPOCARTERA AS CONCEPTO
                    FROM CNM_CARTERANOMISIONAL CNM 
                    INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD= CNM.COD_EMPRESA  
                    LEFT JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL 
                    WHERE CNM.SALDO_DEUDA> 0 AND CNM.COD_EMPRESA = $codEmpresa  AND  CNM.COD_CARTERA_NOMISIONAL = $nrescartera";
                $query = $this->db->query($str_query);
               // echo $this->db->last_query();exit;
                return $query->result_array;
            break;         
        }
    }


    function getConcepto2($codConcepto) {

            $this->db->select('COD_TIPOCARTERA as CODIGOCONCEPTO, NOMBRE_CARTERA as NOMBRE_CONCEPTO');
            $this->db->where('COD_TIPOCARTERA', $codConcepto);
            $query = $this->db->get('TIPOCARTERA ');
            $datos = $query->result_array();
            return $datos;
        
    }

    function getConcepto($codConcepto,$tipo) {

        if($tipo =='3'){
           
        $this->db->select('COD_CPTO_FISCALIZACION as CODIGOCONCEPTO,NOMBRE_CONCEPTO');
        $this->db->where('COD_CPTO_FISCALIZACION', $codConcepto);
        $query = $this->db->get('CONCEPTOSFISCALIZACION ');
        $datos = $query->result_array();

        if (count($datos) > 0) {
            return $datos;
        }
        }
         else {

            $this->db->select('COD_TIPOCARTERA as CODIGOCONCEPTO, NOMBRE_CARTERA as NOMBRE_CONCEPTO');
            $this->db->where('COD_TIPOCARTERA', $codConcepto);
            $query = $this->db->get('TIPOCARTERA ');
            $datos = $query->result_array();
            return $datos;
        }


        
    }

    function getCarteraNoMisional($campos,$codCartera,$tabla) {

        $this->db->select($campos);
        $this->db->where('COD_CARTERA', $codCartera);
        $query = $this->db->get($tabla);
        $datos = $query->result_array();
        return $datos;
    }


    public function ObtenerConceptos($tipocartera) {
  
        $str_query ="SELECT NOMBRE_CAUSAL
        FROM CAUSALDEPURACION
        where CODESTADO=1
        order by  NOMBRE_CAUSAL ASC
        ";
        $query = $this->db->query($str_query);
        return $query->result_array;
    }

    public function ObtenerConceptosNoMisionales() {
      
        $query = $this->db->query("
        SELECT  COD_TIPOCARTERA as COD_CONCEPTO_RECAUDO, NOMBRE_CARTERA as  NOMBRE_CONCEPTO FROM tipocartera 
        WHERE  COD_TIPOCARTERA in(1,2,11,8,9,10,5,3,4,7,42)
        ORDER BY NOMBRE_CARTERA ASC");
        if($query) {
            return $query -> result_array();
        } else {
            return NULL;
        }  
       
    }
    public function ObtenerConceptosMisionales() {
       
         $query = $this->db->query("
        SELECT COD_CPTO_FISCALIZACION , NOMBRE_CONCEPTO FROM CONCEPTOSFISCALIZACION 
        ORDER BY NOMBRE_CONCEPTO ASC");
        if($query) {
            return $query -> result_array();
        } else {
            return NULL;
        }
    }

    public function solicitudesDepuracion()
    {
        $query = "SELECT
        dc.cod_fiscalizacion   AS cod_fiscartera,
        dc.resolucion_depuracion,
        dc.fecha_depuracion,
        dc.causal              AS nombre_causal,
        CAST(l.nitempresa AS INT) AS cod_nit_emple_empre,
        e.razon_social         AS razon_social,
        3 AS tipo
        FROM
        depuracion_contable   dc
        INNER JOIN liquidacion           l ON l.cod_fiscalizacion = dc.cod_fiscalizacion
        INNER JOIN empresa               e ON e.codempresa = l.nitempresa
        INNER JOIN resolucion            r ON r.cod_fiscalizacion = l.cod_fiscalizacion
        INNER JOIN regional              rg ON r.cod_regional = rg.cod_regional
        LEFT JOIN causaldepuracion      cd ON cd.nombre_causal = dc.causal
        WHERE
            dc.estado IS NULL
        UNION
        ( SELECT
            CAST(dc.cod_nomisional AS INT) AS cod_fiscartera,
            dc.resolucion_depuracion,
            dc.fecha_depuracion,
            dc.causal         AS nombre_causal,
            cn.cod_empleado   AS cod_nit_emple_empre,
            upper(cnm_e.nombres
                || ' '
                    || cnm_e.apellidos) AS razon_social,
            1 AS tipo
        FROM
            depuracion_contable     dc
            INNER JOIN cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
            INNER JOIN cnm_empleado            cnm_e ON cn.cod_empleado = cnm_e.identificacion
            INNER JOIN regional                rg ON rg.cod_regional = cn.cod_regional
            LEFT JOIN causaldepuracion        cd ON CAST(cd.nombre_causal AS CHAR) = dc.causal
        WHERE
            dc.estado IS NULL
        )
        UNION
        ( SELECT
            CAST(dc.cod_nomisional AS INT) AS cod_fiscartera,
            dc.resolucion_depuracion,
            dc.fecha_depuracion,
            dc.causal AS nombre_causal,
            CAST(cn.cod_empresa AS INT) AS cod_nit_emple_empre,
            cnm_em.razon_social,
            2 AS tipo
        FROM
            depuracion_contable     dc
            INNER JOIN cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
            INNER JOIN cnm_empresa             cnm_em ON cnm_em.cod_entidad = cn.cod_empresa
            INNER JOIN regional                rg ON rg.cod_regional = cn.cod_regional
            LEFT JOIN causaldepuracion        cd ON CAST(cd.nombre_causal AS CHAR) = dc.causal
        WHERE
            dc.estado IS NULL
        )";
            $query = $this->db->query($query);
           // echo "query: ".$this->db->last_query();exit();
            $datos = $query->result_array;
            return $datos;
    }

    function getSaldoSancion($cod_fiscartera) {

        $this->db->select('SALDO_SANCION');
        $this->db->where('COD_FISCALIZACION', $cod_fiscartera);
        $query = $this->db->get('LIQUIDACION');
        $datos = $query->result_array();
        return $datos;
       
    }


    function getDataDepuracionFiscalizacion($codFiscalizacion) {

        
        $str_query ="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            L.NITEMPRESA AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL, R.NUMERO_RESOLUCION,
            R.FECHA_CREACION,L.SALDO_CAPITAL as SALDOACTUAL,L.SALDO_INTERES AS SALDO_INTERES, 
            R.COD_CPTO_FISCALIZACION AS CONCEPTO, 3 as TIPO
            FROM LIQUIDACION L 
            INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
            INNER JOIN EMPRESA E ON E.CODEMPRESA=R.NITEMPRESA
            INNER JOIN REGIONAL RG ON E.COD_REGIONAL=RG.COD_REGIONAL 
            INNER JOIN EJECUTORIA EJ ON EJ.COD_FISCALIZACION=R.COD_FISCALIZACION
            WHERE L.COD_FISCALIZACION = $codFiscalizacion";
            $query = $this->db->query($str_query);
            // echo $this->db->last_query();exit;
            $datos=$query->result_array;
            
            if (!empty($datos)) {
                return $datos;
            } else {
                $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO,
                UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
                CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES, CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO
                FROM CNM_CARTERANOMISIONAL CNM
                INNER JOIN  CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                INNER JOIN  REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                WHERE CNM.COD_CARTERA_NOMISIONAL= $codFiscalizacion";
                $query = $this->db->query($str_query);
                    //echo $this->db->last_query();exit;
                $datos=$query->result_array;
                    if (count($datos) > 0) {
                        return $datos;
                    } else {
                    $str_query ="SELECT CNM.COD_CARTERA_NOMISIONAL AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    CNM.COD_EMPRESA AS CODIGOEMPRESAEMPLEADO,
                    CNM_EM.RAZON_SOCIAL,CNM.SALDO_DEUDA as SALDOACTUAL,CNM.CALCULO_CORRIENTE AS SALDO_INTERES,
                    CNM.COD_TIPOCARTERA AS CONCEPTO, 2 AS TIPO
                    FROM CNM_CARTERANOMISIONAL CNM 
                    INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD= CNM.COD_EMPRESA  
                    INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL 
                    WHERE CNM.COD_CARTERA_NOMISIONAL = $codFiscalizacion";
                    $query = $this->db->query($str_query);
                    return $query->result_array;
            }


        }
                     
        }
        // return $query;
    
    function getDataMisional($codFiscalizacion) {


        $dataEmpresaFis=$this->getDataDepuracionFiscalizacion($codFiscalizacion);
        
        $tipo =$dataEmpresaFis[0]['TIPO'];
        $conceptoCartera=$dataEmpresaFis[0]['CONCEPTO'];

  
        $concepto=$this->depuracioncartera_model->getConcepto($conceptoCartera,$dataEmpresaFis[0]['TIPO']);
        $dataEmpresa = array(
            'CODIGOCARTERAFIS'=>$dataEmpresaFis[0]['CODIGOCARTERAFIS'],
            'COD_REGIONAL' =>$dataEmpresaFis[0]['COD_REGIONAL'],
            'NOMBRE_REGIONAL' =>$dataEmpresaFis[0]['NOMBRE_REGIONAL'],
            'CODIGOEMPRESAEMPLEADO' =>$dataEmpresaFis[0]['CODIGOEMPRESAEMPLEADO'],
            'RAZON_SOCIAL' =>$dataEmpresaFis[0]['RAZON_SOCIAL'],
            'SALDOACTUAL'  =>$dataEmpresaFis[0]['SALDOACTUAL'],
            'COD_CONCEPTO' =>$dataEmpresaFis[0]['CONCEPTO'],
            'NOMBRE_CONCEPTO' =>$concepto[0]['NOMBRE_CONCEPTO'],
            'TIPO'=> $tipo
        );

        if($tipo==3){
            if($conceptoCartera=1 || $conceptoCartera=2 || $conceptoCartera=3|| $conceptoCartera= 5){

                
                
                $dataEmpresa['NUMERO_RESOLUCION']= $dataEmpresaFis[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $dataEmpresaFis[0]['FECHA_CREACION'];   
            }
        }
        else{
            switch ($conceptoCartera) {
                case 1:  //    
                $campos= 'NUM_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CALAMIDAD_DOMESTICA';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 2:
                $campos= 'NUMERO_CONVENIO,FECHA_SUSCRIPCION,FECHA_ACTA_LIQ,NUMERO_ACTA_LIQ';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_CONVENIOS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_ACTA_LIQ'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ACTA_LIQ'];
    
    
                break;
                case 11://ahorro 
                $campos= 'NUMERO_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_PRESTAMO_AHORRO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 8://hipotecario
                $campos= 'NUMERO_ESCRITURA,FECHA_ESCRITURA';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_PRESTAMO_HIPOTEC';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_ESCRITURA'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ESCRITURA'];
    
    
    
                break;
                case 9://pensional
                $campos= 'NUMERO_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_CUOTA_PARTE';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 10://doble mesada pensional 
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_DOBLE_MESADA';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                case 5://excedentes medicos
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_EXCEDENTE_SERVICIO_MEDICO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                case 3://sancion
                $campos= 'FECHA_ACTA_COMP,NUM_ACTA_COMP';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_EDUCATIVO_SANCION';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_ACTA_COMP'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ACTA_COMP'];
                break;
                case 4://prestamos educativo 
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_PREST_EDUCATIVO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 7://bienes servicios 
                $campos= 'NUM_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_RESPON_FONDOS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                default:///cesantias
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_OTRAS_CARTERAS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
            }        
        }
        return $dataEmpresa;     
    }

       

    function getDepuracion($codFiscalizacion,$accion=null) {

        if($accion==3){
            $condicion='';
        }else{
            $condicion=' AND
            DC.ESTADO IS NULL';
        }

        $str_query ="SELECT DC.COD_FISCALIZACION AS COD_FISCAR, DC.RESOLUCION_DEPURACION, DC.FECHA_DEPURACION,
        DC.causal,DC.valor_depuracion,DC.ACTA_COMITE_DEPURACION,DC.COMENTARIOS,CD.NOMBRE_CAUSAL,DC.COMENTARIOS2
        FROM depuracion_contable DC
        LEFT JOIN Causaldepuracion CD ON CD.NOMBRE_CAUSAL=DC.CAUSAL
        WHERE DC.COD_FISCALIZACION = '$codFiscalizacion' "."$condicion";
        $query = $this->db->query($str_query);
        // echo $this->db->last_query();exit;
        $datos=$query->result_array;
            if (count($datos) > 0) {
                return $datos;
            } else {
      
                $str_query ="SELECT DC.COD_NOMISIONAL AS COD_FISCAR, DC.RESOLUCION_DEPURACION, DC.FECHA_DEPURACION,DC.causal,DC.valor_depuracion,
                DC.ACTA_COMITE_DEPURACION,DC.COMENTARIOS,CD.NOMBRE_CAUSAL,DC.COMENTARIOS2
                FROM depuracion_contable DC
                LEFT JOIN Causaldepuracion CD ON CD.NOMBRE_CAUSAL=DC.CAUSAL
                WHERE DC.COD_NOMISIONAL = $codFiscalizacion " . "$condicion";
                $query = $this->db->query($str_query);
                $datos=$query->result_array;
                return $datos;
        }
    }

    function getRespuestaGestion($cod_fiscartera,$tipogestion) {

        $str_query ="SELECT COD_GESTION_COBRO,COD_TIPO_RESPUESTA
        FROM GESTIONCOBRO
        WHERE COD_GESTION_COBRO=(SELECT MAX(COD_GESTION_COBRO)
        FROM GESTIONCOBRO 
        WHERE COD_TIPOGESTION=$tipogestion
        AND COD_FISCALIZACION_EMPRESA=$cod_fiscartera)";
        $query = $this->db->query($str_query);
         //echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos;        
    }

    function delete($fechadepuracion,$cod_fiscartera) {

        $str_query ="DELETE FROM historico_carteras
                    WHERE FECHA >=TO_DATE( '$fechadepuracion' ,  'YYYY-MM-DD') 
                    AND COD_FISCALIZACION=$cod_fiscartera";
        $query = $this->db->query($str_query);
        // echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }

    function getDiasMora($fechadepuracion,$cod_fiscartera) {

        $this->db->select('DIAS_MORA');
        $this->db->where('FECHA', ''.$fechadepuracion.'');
        $this->db->where('COD_FISCALIZACION', $cod_fiscartera);
        $query = $this->db->get('HISTORICO_CARTERAS ');
       // echo $this->db->last_query();exit;
        $datos = $query->result_array();
        return $datos;

    }


    function getDiasMoraAnteriores($cod_fiscalizacion,$fechaDepuracion)
     {
        
        $this->db->select('DIAS_MORA');
        $this->db->where("FECHA < TO_DATE('$fechaDepuracion','DD-MM-RR')");
        $this->db->where("COD_FISCALIZACION",$cod_fiscalizacion);
        $this->db->order_by('FECHA', 'DESC');
        $resultado = $this->db->get('HISTORICO_CARTERAS');
  //echo $this->db->last_query();exit;
        if ($resultado->num_rows() > 0):
            $tipos = $resultado->result_array()[0]['DIAS_MORA'];
            return $tipos;
        else:
            $tipos = 'Consulta sin datos en los tipos de combinaciÃ³n';
        endif;
    }
    
    
    public function getDepuracionCartera() {

        $str_query ="SELECT  COD_FISCALIZACION as COD_FISCAR, RESOLUCION_DEPURACION,
        FECHA_DEPURACION, 1 as tipo from depuracion_contable
        union (
        SELECT  CAST (COD_NOMISIONAL AS INT ) AS COD_FISCAR, RESOLUCION_DEPURACION,
        FECHA_DEPURACION, 2 as tipo from depuracion_contable 
        )
        ";
        $query = $this->db->query($str_query);
      //   echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }

    

    public function getResponsableDepuracion($cod_fiscartera) {

        $str_query ="SELECT
                dc.cod_fiscalizacion,
                dc.realizado_por ,
                u.NOMBRES || ' ' || u.APELLIDOS  AS tecnico,
                car.NOMBREGRUPO    AS cargotecnico,
                usu.NOMBRES || ' ' || usu.APELLIDOS    AS coordinador,
                carg.NOMBREGRUPO   AS cargocordinador
            FROM
                depuracion_contable   dc
                INNER JOIN usuarios              u ON dc.realizado_por = u.idusuario
                LEFT JOIN usuarios              usu ON dc.aprobado_por = usu.idusuario
                INNER JOIN GRUPOS                car ON car.IDGRUPO = u.idcargo
                LEFT JOIN GRUPOS                carg ON carg.IDGRUPO = usu.idcargo
            WHERE
                dc.cod_fiscalizacion = $cod_fiscartera
            UNION
            ( SELECT
                CAST(dc.cod_nomisional AS INT) AS cod_fiscalizacion,
                dc.realizado_por,
                u.NOMBRES || ' ' || u.APELLIDOS  AS tecnico,
                car.NOMBREGRUPO    AS cargotecnico,
                usu.NOMBRES || ' ' || usu.APELLIDOS    AS coordinador,
                carg.NOMBREGRUPO   AS cargocordinador
            FROM
                depuracion_contable   dc
                INNER JOIN usuarios              u ON dc.realizado_por = u.idusuario
                LEFT JOIN usuarios              usu ON dc.aprobado_por = usu.idusuario
                INNER JOIN GRUPOS                car ON car.IDGRUPO = u.idcargo
                LEFT JOIN GRUPOS                carg ON carg.IDGRUPO = usu.idcargo
            WHERE
                dc.cod_nomisional = $cod_fiscartera
        )";
        $query = $this->db->query($str_query);
        //echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }

    
    public function getDepuracionesAprobadas($estado,$tipo,$codFiscalizacion) {

        if(!empty($tipo)||!empty($codFiscalizacion)){

            if($tipo == 3){
                $condicion ="  AND dc.COD_FISCALIZACION = $codFiscalizacion";
            }
            else{
                $condicion ="  AND dc.COD_NOMISIONAL = $codFiscalizacion";
            }
            
        }else{
            $condicion="";
        }
        $str_query ="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
        CAST(L.NITEMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL,dc.SALDO_CAPITAL as SALDOACTUAL,
        dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
        R.COD_CPTO_FISCALIZACION AS CONCEPTO, 3 as TIPO,   dc.causal AS NOMBRE_CAUSAL, dc.VALOR_DEPURACION
        FROM LIQUIDACION L 
        INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
        INNER JOIN EMPRESA E ON E.CODEMPRESA=R.NITEMPRESA
        INNER JOIN REGIONAL RG ON R.COD_REGIONAL=RG.COD_REGIONAL 
        inner join depuracion_contable dc ON dc.COD_FISCALIZACION =L.COD_FISCALIZACION
        LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
        WHERE dc.estado=$estado.$condicion
        UNION (                  
        SELECT CAST(CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            dc.SALDO_DEUDA as SALDOACTUAL, dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
            CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO,
            dc.causal as NOMBRE_CAUSAL, dc.VALOR_DEPURACION                        
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
             inner join depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL
        LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
        WHERE dc.estado=$estado.$condicion
        )                        
        UNION (               
            SELECT CAST (CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                CAST (CNM.COD_EMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,
                CNM_EM.RAZON_SOCIAL,dc.SALDO_DEUDA as SALDOACTUAL,
                dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO,
                dc.causal AS NOMBRE_CAUSAL, dc.VALOR_DEPURACION FROM CNM_CARTERANOMISIONAL CNM 
                INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD= CNM.COD_EMPRESA  
                INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM_EM.COD_REGIONAL 
                 inner join depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL
        LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
        WHERE dc.estado=$estado.$condicion)";
        $query = $this->db->query($str_query);
       // echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }

    public function getDepuracionesRechazadas($tipo,$regional,$idUsuario) {

        if($tipo==1){
            $str_query ="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CAST(L.NITEMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL,dc.SALDO_CAPITAL as SALDOACTUAL,
            dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
            R.COD_CPTO_FISCALIZACION AS CONCEPTO, 3 as TIPO,   dc.causal AS NOMBRE_CAUSAL, dc.VALOR_DEPURACION
            FROM LIQUIDACION L 
            INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
            INNER JOIN FISCALIZACION F ON R.COD_FISCALIZACION =F.COD_FISCALIZACION
            INNER JOIN ASIGNACIONFISCALIZACION AF ON AF.COD_ASIGNACIONFISCALIZACION=F.COD_ASIGNACION_FISC
            INNER JOIN EMPRESA E ON E.CODEMPRESA=R.NITEMPRESA
            INNER JOIN REGIONAL RG ON R.COD_REGIONAL=RG.COD_REGIONAL 
            inner join depuracion_contable dc ON dc.COD_FISCALIZACION =L.COD_FISCALIZACION
            LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
            WHERE dc.estado=0 and AF.COD_REGIONAL =$regional 
            and dc.REALIZADO_POR =$idUsuario
            ";
            
            $query = $this->db->query($str_query);
        // echo $this->db->last_query();exit;
            $datos=$query->result_array;
            return $datos; 
        }
        else if($tipo==2){
            $str_query =" SELECT CAST(CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO,
                    UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
                    dc.SALDO_DEUDA as SALDOACTUAL, dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
                    CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO,
                    dc.causal as NOMBRE_CAUSAL, dc.VALOR_DEPURACION                        
                    FROM CNM_CARTERANOMISIONAL CNM
                    INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
                    INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM.COD_REGIONAL
                     inner join depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL
                LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
                WHERE dc.estado=0 and CNM.COD_REGIONAL=$regional 
                and dc.REALIZADO_POR =$idUsuario
                                       
                UNION (               
                    SELECT CAST (CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                        CAST (CNM.COD_EMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,
                        CNM_EM.RAZON_SOCIAL,dc.SALDO_DEUDA as SALDOACTUAL,
                        dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO,
                        dc.causal AS NOMBRE_CAUSAL, dc.VALOR_DEPURACION FROM CNM_CARTERANOMISIONAL CNM 
                        INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD= CNM.COD_EMPRESA  
                        INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM_EM.COD_REGIONAL 
                         inner join depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL
                LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
                WHERE dc.estado=0 and CNM.COD_REGIONAL=$regional 
                and dc.REALIZADO_POR =$idUsuario)";
            $query = $this->db->query($str_query);
           
             $datos=$query->result_array;
              // echo $this->db->last_query();exit;
             return $datos; 
     

        }
        
        


    }

    function getDataReporte($estado,$tipo,$codFiscalizacion,$coordinador) {

        
        $dataEmpresaFis=$this->getDepuracionesAprobadas($estado,$tipo,$codFiscalizacion);
        
        $tipo =$dataEmpresaFis[0]['TIPO'];
        $conceptoCartera=$dataEmpresaFis[0]['CONCEPTO'];


        $concepto=$this->depuracioncartera_model->getConcepto($conceptoCartera,$tipo);


        $dataEmpresa = array(
            'CODIGOCARTERAFIS'=>$dataEmpresaFis[0]['CODIGOCARTERAFIS'],
            'COD_REGIONAL' =>$dataEmpresaFis[0]['COD_REGIONAL'],
            'NOMBRE_REGIONAL' =>$dataEmpresaFis[0]['NOMBRE_REGIONAL'],
            'CODIGOEMPRESAEMPLEADO' =>$dataEmpresaFis[0]['CODIGOEMPRESAEMPLEADO'],
            'RAZON_SOCIAL' =>$dataEmpresaFis[0]['RAZON_SOCIAL'],
            'SALDOACTUAL'  =>$dataEmpresaFis[0]['SALDOACTUAL'],
            'COD_CONCEPTO' =>$dataEmpresaFis[0]['CONCEPTO'],
            'NOMBRE_CONCEPTO' =>$concepto[0]['NOMBRE_CONCEPTO'],
            'ACTA_COMITE_DEPURACION'=> $dataEmpresaFis[0]['ACTA_COMITE_DEPURACION'],
            'RESOLUCION_DEPURACION'=> $dataEmpresaFis[0]['RESOLUCION_DEPURACION'],
            'FECHA_DEPURACION'=> $dataEmpresaFis[0]['FECHA_DEPURACION'],
            'NOMBRE_CAUSAL'=> $dataEmpresaFis[0]['NOMBRE_CAUSAL'],
            'VALOR_DEPURACION'=> $dataEmpresaFis[0]['VALOR_DEPURACION'],
            'TIPO'=> $tipo
        );

        if($tipo==3){
            if($conceptoCartera=1 || $conceptoCartera=2 || $conceptoCartera=3|| $conceptoCartera= 5){
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $numeroResolucion=$this->getResolucionMisional($codCartera);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_CREACION'];   
            }
        }
        else{
            switch ($conceptoCartera) {
                case 1: 
                $campos= 'NUM_RESOLUCION,FECHA_RESOLUCION';
               $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CALAMIDAD_DOMESTICA';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                case 2:
                $campos= 'NUMERO_CONVENIO,FECHA_SUSCRIPCION,FECHA_ACTA_LIQ,NUMERO_ACTA_LIQ';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_CONVENIOS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_ACTA_LIQ'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ACTA_LIQ'];
                break;
                case 11://ahorro 
                $campos= 'NUMERO_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_PRESTAMO_AHORRO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 8://hipotecario 
                $campos= 'NUMERO_ESCRITURA,FECHA_ESCRITURA';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_PRESTAMO_HIPOTEC';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_ESCRITURA'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ESCRITURA'];
    
                break;
                case 9://pensional
                $campos= 'NUMERO_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_CUOTA_PARTE';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 10://doble mesada pensional 
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_DOBLE_MESADA';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                case 5://excedentes medicos
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_EXCEDENTE_SERVICIO_MEDICO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                case 3://sancion
                $campos= 'FECHA_ACTA_COMP,NUM_ACTA_COMP';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_EDUCATIVO_SANCION';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_ACTA_COMP'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_ACTA_COMP'];
                break;
                case 4://prestamos educativo
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_PREST_EDUCATIVO';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
    
                break;
                case 7://bienes servicios 
                $campos= 'NUM_RESOLUCION,FECHA_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_RESPON_FONDOS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUM_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
                default:///cesantias
                $campos= 'FECHA_RESOLUCION,NUMERO_RESOLUCION';
                $codCartera=$dataEmpresaFis[0]['CODIGOCARTERAFIS'];
                $tabla='CNM_CARTERA_OTRAS_CARTERAS';
                $numeroResolucion=$this->getCarteraNoMisional($campos,$codCartera,$tabla);
                $dataEmpresa['NUMERO_RESOLUCION']= $numeroResolucion[0]['NUMERO_RESOLUCION'];
                $dataEmpresa['FECHA_CREACION'] = $numeroResolucion[0]['FECHA_RESOLUCION'];
                break;
            }           
        }
        return $dataEmpresa;
    }

   
    function edit_($condicion) {
       
        $fecha=$condicion['fechadepuracion'];
        $cod_fiscalziacion= $condicion['cod_fiscalziacion'];          
                
        $sql= " UPDATE HISTORICO_CARTERAS SET SALDO_DEUDA =  0,
        SALDO_CAPITAL = 0,SALDO_INTERES = 0,SALDO_SANCION = 0
        WHERE TO_DATE(FECHA, 'DD/MM/RR') >  '$fecha' 
        AND COD_FISCALIZACION =  '$cod_fiscalziacion'";
            $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }

        return FALSE;
    }

    function edit_Cuotas($ncodfiscar) {
       
        $sql= " UPDATE CNM_CUOTAS SET SALDO_CUOTA =  0,
        SALDO_INTERES_C = 0,AMORTIZACION = 0
        WHERE 
         ID_DEUDA_E =  '$ncodfiscar'";
            $query = $this->db->query($sql);
            if ($this->db->affected_rows() > 0) {
            
                return TRUE;
            }else{
            
                return FALSE;
            }

    }


    function getDepuracionCoordinadorMisional($cod_empresa =null,$cod_regional=null,$fecha_inicio=null,$fecha_fin=null,$causal=null,$subconcepto=null){
      
        $condicion=NULL;
        if(IDCARGO==127){
            if(empty($cod_empresa) && empty($cod_regional)  && empty($subconcepto)  && empty($fecha_inicio) && empty($fecha_fin) ){
                $condicion ='';
             
            }else{
                if(!empty($cod_empresa)){
                    $condicion ="AND L.NITEMPRESA= $cod_empresa ";
                }
                elseif(!empty($cod_regional)){
                    $condicion =$condicion."AND AF.COD_REGIONAL =$cod_regional";
                }
                elseif(!empty($subconcepto)){
                    $condicion =$condicion."AND   R.COD_CPTO_FISCALIZACION =$subconcepto";
                }
              

                elseif(!empty($fecha_inicio) && !empty($fecha_inicio)){
          
                    $condicion =$condicion."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                                            AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
            
                }
               
                else{
                    $condicion='';
                 
                }
            }
        }else{
            $condicion ="AND AF.COD_REGIONAL =$cod_regional";
            if(empty($cod_empresa) && empty($fecha_inicio) && empty($fecha_fin) ){
                $condicion =$condicion.' ';
             
            }else{
                if(!empty($cod_empresa)){
                    $condicion =$condicion." AND L.NITEMPRESA= $cod_empresa ";
                }
               
                elseif(!empty($fecha_inicio) && !empty($fecha_inicio)){
          
                    $condicion =$condicion."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                                            AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
            
                }
               
                else{
                    $condicion='';
                 
                }
            }

        }
       
        
        $str_query="SELECT L.COD_FISCALIZACION AS CODIGOCARTERAFIS ,R.COD_REGIONAL,RG.NOMBRE_REGIONAL,
        CAST(L.NITEMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,E.RAZON_SOCIAL,dc.SALDO_CAPITAL as SALDOACTUAL,
        dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
        R.COD_CPTO_FISCALIZACION AS CONCEPTO, 3 as TIPO,  dc.causal, dc.VALOR_DEPURACION
        FROM depuracion_contable dc
        INNER JOIN  LIQUIDACION L  ON dc.COD_FISCALIZACION =L.COD_FISCALIZACION
        INNER JOIN RESOLUCION R ON R.COD_FISCALIZACION=L.COD_FISCALIZACION 
        INNER JOIN fiscalizacion F on F.COD_FISCALIZACION=r.COD_FISCALIZACION
        inner join asignacionfiscalizacion AF on F.COD_ASIGNACION_FISC = AF.COD_ASIGNACIONFISCALIZACION
        INNER JOIN EMPRESA E ON E.CODEMPRESA=AF.NIT_EMPRESA
        INNER JOIN REGIONAL RG ON R.COD_REGIONAL=RG.COD_REGIONAL 
        LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
        WHERE dc.estado='1'  $condicion ";
        $query = $this->db->query($str_query);
    //echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }

    function getDepuracionCoordinadorNoMisional($cod_empresa =null,$cod_regional=null,$fecha_inicio=null,$fecha_fin=null,$causal=null,$subconcepto=null){
      
     
        $condicion2=null;
        $condion3=null;

     
        if(IDCARGO==127){
            if(empty($cod_empresa) && empty($subconcepto)&& empty($cod_regional) && empty($fecha_inicio) && empty($fecha_fin) ){
        
                $condicion2='';
                $condicion3='';
            }else{
                if(!empty($cod_empresa)){
    
                    $condicion2 = "AND  CNM.COD_EMPRESA =$cod_empresa";
                    $condion3 = "AND  CNM.COD_EMPLEADO =$cod_empresa";
                    
                }
                elseif(!empty($cod_regional)){
                 
                    $condicion2=$condicion2."AND CNM.COD_REGIONAL = $cod_regional";
                    $condion3=$condion3."AND CNM.COD_REGIONAL = $cod_regional";
                }
                elseif(!empty($subconcepto)){
                 
                    $condicion2=$condicion2."AND CNM.COD_TIPOCARTERA = $subconcepto";
                    $condion3=$condion3."AND CNM.COD_TIPOCARTERA = $subconcepto";
                }

                elseif(!empty($fecha_inicio) && !empty($fecha_inicio)){
                
                    $condicion2 =$condicion2."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                    AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
                    $condion3 =$condion3."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                    AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
    
                }
               
                else{
                   $condicion2='';
                    $condion3='';
                }
            }

        }else{
            
                 
                $condicion2="AND CNM.COD_REGIONAL = $cod_regional";
                $condion3="AND CNM.COD_REGIONAL = $cod_regional";

            if(empty($cod_empresa) && empty($fecha_inicio) && empty($fecha_fin) ){
        
                $condicion2=$condicion2.' ';
                $condicion3=$condion3.' ';
            }else{
                if(!empty($cod_empresa)){
    
                    $condicion2 = $condicion2."AND  CNM.COD_EMPRESA =$cod_empresa";
                    $condion3 = $condion3."AND  CNM.COD_EMPLEADO =$cod_empresa";
                    
                }
                elseif(!empty($fecha_inicio) && !empty($fecha_inicio)){
                
                    $condicion2 =$condicion2."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                    AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
                    $condion3 =$condion3."AND TO_DATE(dc.fecha_depuracion,'DD/MM/RR') BETWEEN TO_DATE ('$fecha_inicio', 'DD/MM/RR') 
                    AND TO_DATE ('$fecha_fin', 'DD/MM/RR')";
    
                }
               
                else{
                   $condicion2='';
                    $condion3='';
                }
            }
        }
        

        $str_query="SELECT CAST(CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
            CNM.COD_EMPLEADO AS CODIGOEMPRESAEMPLEADO,
            UPPER(CNM_E.NOMBRES || ' ' || CNM_E.APELLIDOS) AS RAZON_SOCIAL,
            dc.SALDO_DEUDA as SALDOACTUAL, dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,
            CNM.COD_TIPOCARTERA as CONCEPTO,1 AS TIPO, dc.causal, dc.VALOR_DEPURACION       
            FROM CNM_CARTERANOMISIONAL CNM
            INNER JOIN CNM_EMPLEADO CNM_E ON CNM.COD_EMPLEADO= CNM_E.IDENTIFICACION 
            INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM_E.COD_REGIONAL
            INNER JOIN depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL
            LEFT join causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
            WHERE dc.estado='1' $condion3                     
            UNION (               
                SELECT CAST (CNM.COD_CARTERA_NOMISIONAL AS INT) AS CODIGOCARTERAFIS,CNM.COD_REGIONAL,RG.NOMBRE_REGIONAL,
                    CAST (CNM.COD_EMPRESA AS INT) AS CODIGOEMPRESAEMPLEADO,
                    CNM_EM.RAZON_SOCIAL,dc.SALDO_DEUDA as SALDOACTUAL,
                    dc.ACTA_COMITE_DEPURACION,dc.RESOLUCION_DEPURACION,dc.FECHA_DEPURACION,CNM.COD_TIPOCARTERA as CONCEPTO,2 AS TIPO,
                    dc.causal, dc.VALOR_DEPURACION FROM CNM_CARTERANOMISIONAL CNM 
                    INNER JOIN CNM_EMPRESA CNM_EM ON CNM_EM.COD_ENTIDAD= CNM.COD_EMPRESA  
                    INNER JOIN REGIONAL RG ON RG.COD_REGIONAL=CNM_EM.COD_REGIONAL 
                    INNER JOIN depuracion_contable dc ON dc.COD_NOMISIONAL =CNM.COD_CARTERA_NOMISIONAL 
                    LEFT JOIN causaldepuracion cd on cast(cd.NOMBRE_CAUSAL as char) =dc.CAUSAL
            WHERE dc.estado='1' $condicion2)";

        $query = $this->db->query($str_query);
        //echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }


    function getDepuracionConsolidadoMisional(){

        $str_query="SELECT r.COD_REGIONAL,rg.NOMBRE_REGIONAL,sum(dc.VALOR_DEPURACION) as totaldepuracion from depuracion_contable dc 
            inner join resolucion r on dc.COD_FISCALIZACION=r.COD_FISCALIZACION
            inner join regional rg on rg.COD_REGIONAL=r.COD_REGIONAL
            where dc.estado =1 AND dc.cod_fiscalizacion is not null
            group by r.COD_REGIONAL,rg.NOMBRE_REGIONAL
            ";
        $query = $this->db->query($str_query);
        $datos=$query->result_array;
        return $datos; 
    }

    function getDepuracionConsolidadoNoMisional(){

        $str_query="SELECT cnm.COD_REGIONAL,rg.NOMBRE_REGIONAL,sum(dc.VALOR_DEPURACION) as totaldepuracion  from depuracion_contable dc 
            inner join cnm_carteranomisional cnm on cnm.COD_CARTERA_NOMISIONAL=dc.COD_NOMISIONAL
            inner join regional rg on rg.COD_REGIONAL=cnm.COD_REGIONAL
            where dc.estado =1 AND dc.COD_NOMISIONAL is not null
            group by cnm.COD_REGIONAL,rg.NOMBRE_REGIONAL
           ";

        $query = $this->db->query($str_query);
     //echo $this->db->last_query();exit;
        $datos=$query->result_array;
        return $datos; 
    }
    

    function esconderNotifiacion($cod_fiscar){

        $this->db->set('MOSTRARNOTIFICACION', 1);
      
        $this->db->where('COD_FISCALIZACION', $cod_fiscar);
        $this->db->update('DEPURACION_CONTABLE');
       
        if ($this->db->affected_rows() == 0) {
            $this->db->set('MOSTRARNOTIFICACION', 1);
            $this->db->where('COD_NOMISIONAL', $cod_fiscar);
            $this->db->update('DEPURACION_CONTABLE');
        // echo $this->db->last_query();exit;
        }
        else{
        return TRUE;
        }

    }

    function add($table, $data, $date = '') {

        if ($date != '') {
            foreach ($data as $key => $value) {
                $this->db->set($key, $value);
            }
            foreach ($date as $keyf => $valuef) {
                $this->db->set($keyf, "to_date('" . $valuef . "','dd/mm/yyyy')", false);
            }

            $this->db->insert($table);
        } else {
            $this->db->insert($table, $data);
        }
        //$resultado = $this->db->last_query();
       //echo $this->db->last_query();die();
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }

    public function solicitudesDepuracionMisional($cod_regional)
    {
        $query = "SELECT
        dc.cod_fiscalizacion   AS cod_fiscartera,
        dc.resolucion_depuracion,
        dc.fecha_depuracion,
        dc.causal              AS nombre_causal,
        CAST(l.nitempresa AS INT) AS cod_nit_emple_empre,
        e.razon_social         AS razon_social,
        3 AS tipo
        FROM
        depuracion_contable   dc
        INNER JOIN liquidacion           l ON l.cod_fiscalizacion = dc.cod_fiscalizacion
        INNER JOIN empresa               e ON e.codempresa = l.nitempresa
        INNER JOIN resolucion            r ON r.cod_fiscalizacion = l.cod_fiscalizacion
        INNER JOIN regional              rg ON r.cod_regional = rg.cod_regional
        INNER JOIN causaldepuracion      cd ON cd.nombre_causal = dc.causal
        INNER JOIN usuarios             u on dc.realizado_por =u.idusuario
        WHERE
            dc.estado = 3
            and u.cod_regional=$cod_regional
        ";
            $query = $this->db->query($query);
           // echo "query: ".$this->db->last_query();exit();
            $datos = $query->result_array;
            return $datos;
    }



    public function solicitudesDepuracionNoMisional($cod_regional)
    {
        $query = " SELECT
        CAST(dc.cod_nomisional AS INT) AS cod_fiscartera,
        dc.resolucion_depuracion,
        dc.fecha_depuracion,
        dc.causal         AS nombre_causal,
        cn.cod_empleado   AS cod_nit_emple_empre,
        upper(cnm_e.nombres
              || ' '
                 || cnm_e.apellidos) AS razon_social,
        1 AS tipo
        FROM
            depuracion_contable     dc
            INNER JOIN cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
            INNER JOIN cnm_empleado            cnm_e ON cn.cod_empleado = cnm_e.identificacion
            INNER JOIN regional                rg ON rg.cod_regional = cn.cod_regional
            INNER JOIN causaldepuracion        cd  on cd.nombre_causal = dc.causal
            INNER JOIN usuarios             u on dc.realizado_por =u.idusuario
            WHERE
                dc.estado =3
                and u.cod_regional=$cod_regional
            UNION
            ( SELECT
                CAST(dc.cod_nomisional AS INT) AS cod_fiscartera,
                dc.resolucion_depuracion,
                dc.fecha_depuracion,
                dc.causal AS nombre_causal,
                CAST(cn.cod_empresa AS INT) AS cod_nit_emple_empre,
                cnm_em.razon_social,
                2 AS tipo
            FROM
                depuracion_contable     dc
                INNER JOIN cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
                INNER JOIN cnm_empresa             cnm_em ON cnm_em.cod_entidad = cn.cod_empresa
                INNER JOIN regional                rg ON rg.cod_regional = cn.cod_regional
                LEFT JOIN causaldepuracion        cd ON cd.nombre_causal = dc.causal
                INNER JOIN usuarios             u on dc.realizado_por =u.idusuario
            WHERE
                dc.estado =3
                and u.cod_regional=$cod_regional
            )";
            $query = $this->db->query($query);
           //echo "query: ".$this->db->last_query();exit();
            $datos = $query->result_array;
            return $datos;
    }

    function edita($table, $data, $date = '', $fieldID, $ID) {
        $this->db->where($fieldID, $ID);
        if ($date != '') {
            foreach ($data as $key => $value) {
                $this->db->set($key, $value);
            }
            foreach ($date as $keyf => $valuef) {
            
                $this->db->set($keyf, "to_date('" . $valuef . "','RRRR/MM/DD')", false);
            }
            $this->db->update($table);
        } else {
            $this->db->update($table, $data);
        }
//echo $this->db->last_query();die();
        if ($this->db->affected_rows() >= 0) {
           return TRUE;
        }
        else{
            return FALSE;
        }

    }



    function existeDepuracion($nitempresa) {

        $str_query="select dc.cod_fiscalizacion as cod_fis from DEPURACION_CONTABLE dc
        inner join resolucion r on r.cod_fiscalizacion=dc.cod_fiscalizacion
        where r.NITEMPRESA=$nitempresa";
        $query = $this->db->query($str_query);
        if(count($query)>0){
            $datos=$query->result_array;
            return $datos;
        }else{
            $str_query=" select dc.COD_NOMISIONAL as cod_fis from DEPURACION_CONTABLE dc
            inner join cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
            where cn.COD_EMPLEADO=$nitempresa";
            $query = $this->db->query($str_query);
            if(count($query)>0){
                 $datos=$query->result_array;
                 return $datos;
            }else{

                $str_query=" select dc.COD_NOMISIONAL as cod_fis from DEPURACION_CONTABLE dc
                inner join cnm_carteranomisional   cn ON dc.cod_nomisional = cn.cod_cartera_nomisional
                where cn.COD_EMPRESA=$nitempresa";
                $query = $this->db->query($str_query);
                if(count($query)>0){
                    $datos=$query->result_array;
                    return $datos;
                }
            }     

        }       
    }


    public function updateDepuracion($campo,$data,$date, $valorCampo) {
       
            $this->db->set("RESOLUCION_DEPURACION", $data['RESOLUCION_DEPURACION']);
            $this->db->set("FECHA_MODIFICACION ", "to_date('" . $date['FECHA_DEPURACION'] . "','DD/MM/YYYY HH:MI')", false);
            $this->db->set("CAUSAL", $data['CAUSAL']);
            $this->db->set("COMENTARIOS", $data['COMENTARIOS']);
            $this->db->set("ACTA_COMITE_DEPURACION", $data['ACTA_COMITE_DEPURACION']);
            $this->db->set("REALIZADO_POR", $data['REALIZADO_POR']);   
            $this->db->set("ESTADO", $data['ESTADO']);           
            $this->db->where($campo, $valorCampo);
            $this->db->update('DEPURACION_CONTABLE');
            if ($this->db->affected_rows() > 0) {
                return TRUE;
            }
            return FALSE;
        }

        function addHistoricoCartera($ncodfiscar,$fecha, $diasMoraActualizar)
         {
           

            $this->db->set('COD_FISCALIZACION', $ncodfiscar);
            $this->db->set('SALDO_DEUDA', 0);
            $this->db->set('SALDO_CAPITAL', 0);
            $this->db->set('FECHA', "TO_DATE('" . $fecha . "','DD/MM/YY')", FALSE);
            $this->db->set('SALDO_INTERES', 0);
            $this->db->set('SALDO_SANCION', 0);
            $this->db->set('DIAS_MORA', $diasMoraActualizar);
            $resultado = $this->db->insert('HISTORICO_CARTERAS');
            //#####BUGGER PARA LA CONSULTA ######
            // $resultado = $this -> db -> last_query();
             //echo $resultado; die();
            //#####BUGGER PARA LA CONSULTA ######
            return $resultado;
        }
    
}

