<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if (!function_exists('validar_pago_importado')) {

    function validar_pago_importado($pago)
    {
        $validarData = "";
        $contarLongitud = 0;

        $validarData = validarNit($pago[0]) .
            validarProcedencia($pago[1]) .
            validarConcepto($pago[2]) .
            validarConceptoSub($pago[3]) .
            validarRegional($pago[4]) .
            validarPeriodo($pago[5]) .
            validarFechaPago($pago[6]) .
            validarFechaAplicacion($pago[7]) .
            validarFechaTransaccion($pago[8]) .
            validarFormaPago($pago[9]) .
            validarDocumento($pago[10]) .
            validarDistribucionCapital($pago[11]) .
            validarInteresCapital($pago[12]) .
            validarCodigoEntidad($pago[13]) .
            validarNroReferencia($pago[14]) .
            validarValorAdeudado($pago[15]) .
            validarNroTrabajadores($pago[16]) .
            validarNroResolucion($pago[17]) .
            validarFechaResolucion($pago[18]) .
            validarNroLicenciaConstruccion($pago[19]) .
            validarNombreObra($pago[20]) .
            validarFechaInicioObra($pago[21]) .
            validarFechaFonObra($pago[22]) .
            validarCiudadObra($pago[23]) .
            validarTipoFic($pago[24]) .
            validarCostoTotalObra($pago[25]) .
            validarCostoTotalManoObra($pago[26]) .
            validarTipoCarnet($pago[27]) .
            validarNroConvenio($pago[28]) .
            validarValorPagado($pago[29]) .
            validarRegionalSiif($pago[30]) .
            validarCentroSiff($pago[31]) .
            validarCodigoSiff($pago[32]) .
            validarTicketId($pago[33]) .
            validarRadicadoOnbase($pago[34]) .
            validarFechaOnbase($pago[35]) .
            validarDistribucionSancion($pago[36]) . " <br>\n";

        $contarLongitud = mb_strlen(trim($validarData, " <br>\n"));

        if ($contarLongitud == 0) {
            return "OK";
        } else {
            return $validarData;
        }
    }
}


if (!function_exists('validarNit')) {
    function validarNit($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 15)) {
            return "";
        }
        return "El campo de NIT no debe estar vacío, ni exceder máximo de 15 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarProcedencia')) {
    function validarProcedencia($data)
    {
        $maxProce = strlen($data);
        $str = strtoupper($data);
        if (!(strcmp($str, $data) === 0) || !($maxProce < 50)) {
            return "El campo de la PROCEDENCIA no debe estar vacía ni exceder el máximo de 50 caracteres, debe estar en mayúsculas" . " <br>\n";
        }
        return "";
    }
}

if (!function_exists('validarConcepto')) {
    function validarConcepto($data)
    {
        if ((is_numeric($data)) && ($data > 0 && $data <= 9)) {
            return "";
        }
        return "El campo CONCEPTO debe ser numérico, no debe estar vacío, ni exceder máximo de 9 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarConceptoSub')) {
    function validarConceptoSub($data)
    {
        if ((is_numeric($data)) && ($data > 0 && $data <= 1000)) {
            return "";
        }
        return "El campo SUB CONCEPTO debe ser numérico, no debe estar vacío, ni exceder máximo de 9 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarRegional')) {
    function validarRegional($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 15)) {
            return "";
        }
        return "El campo de REGIONAL no debe estar vacío, ni exceder máximo de 15 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarFormaPago')) { 
    function validarFormaPago($data)
    {
        $contarLongitud = mb_strlen($data);
        if ($data < 100 && ($contarLongitud > 0 && $contarLongitud < 3)) {
            return "";
        }
        return "El campo de FORMA PAGO no debe estar vacía, ni exceder máximo de 2 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarDocumento')) {
    function validarDocumento($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 20)) {
            return "";
        }
        return "El campo DOCUMENTO  no debe exceder el máximo de 20 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarDistribucionCapital')) {
    function validarDistribucionCapital($data)
    {
        if((empty($data) || is_null($data))){ return ""; }
        if ((is_numeric($data)) && ($data >=0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo DISTRIBUCIÓN CAPITAL debe ser numérico, no debe estar vacío, ni exceder máximo de 15 digitos" . " <br>\n";
    }
}

if (!function_exists('validarInteresCapital')) {
    function validarInteresCapital($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data >= 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo DISTRIBUCIÓN INTERES debe ser numérico, no exceder máximo de 15 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarCodigoEntidad')) {
    function validarCodigoEntidad($data)
    {
        if ((is_numeric($data)) && ($data >= 0 && $data < 100000000000)) {
            return "";
        }
        return "El campo CODIGO ENTIDAD debe ser numérico, no exceder máximo de 11 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarNroReferencia')) {
    function validarNroReferencia($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

       // if (/*(is_numeric($data)) &&*//* ($data > 0 && $data < 1000000000000000)*/) {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 15)) {
            return "";
        }
        return "El campo NRO REFERENCIA debe ser numérico, no debe estar vacío, ni exceder máximo de 15 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarValorAdeudado')) {
    function validarValorAdeudado($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data > 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo VALOR ADEUDADO debe ser numérico,  no exceder máximo de 15 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarNroTrabajadores')) {
    function validarNroTrabajadores($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data >= 0 && $data < 100000000)) {
            return "";
        }
        return "El campo NUMERO DE TRABAJADORES debe ser numérico, no debe estar Vacío, ni exceder máximo de 8 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarNroResolucion')) {
    function validarNroResolucion($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 30)) {
            return "";
        }
        return "El campo NRO RESOLUCION REGULACION no debe exceder máximo de 30 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarNroLicenciaConstruccion')) {
    function validarNroLicenciaConstruccion($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 50)) {
            return "";
        }
        return "El campo NRO LICENCIA CONSTRUCCION  no debe estar vacío, ni exceder máximo de 30 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarNombreObra')) {
    function validarNombreObra($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 200)) {
            return "";
        }
        return "El campo NOMBRE OBRA  no debe estar vacío, ni exceder máximo de 200 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarCiudadObra')) {
    function validarCiudadObra($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 50)) {
            return "";
        }
        return "El campo NOMBRE CIUDAD  no debe estar vacío, ni exceder máximo de 200 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarTipoFic')) {
    function validarTipoFic($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 35)) {
            return "";
        }
        return "El campo TIPO FIC  no debe estar vacío, ni exceder máximo de 200 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarCostoTotalObra')) {
    function validarCostoTotalObra($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data >= 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo COSTO TOTAL OBRA debe ser numérico, no debe estar vacío, ni exceder máximo de 8 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarCostoTotalManoObra')) {
    function validarCostoTotalManoObra($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data >= 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo COSTO TOTAL MANO OBRA debe ser numérico, no debe estar vacío, ni exceder máximo de 8 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarTipoCarnet')) {
    function validarTipoCarnet($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 50)) {
            return "";
        }
        return "El campo TIPO CARNET  no debe exceder máximo de 50 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarNroConvenio')) {
    function validarNroConvenio($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 45)) {
            return "";
        }
        return "El campo  NRO CONVENIO no debe exceder máximo de 45 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarValorPagado')) {
    function validarValorPagado($data)
    {
        if ((is_numeric($data)) && ($data > 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo  VALIDAR PAGO debe ser numérico, no debe estar Vacío, no exceder máximo de 15 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarRegionalSiif')) {
    function validarRegionalSiif($data)
    {
        $contarLongitud = mb_strlen($data);     
        if (($contarLongitud >= 0 && $contarLongitud < 32)) {
            return "";
        }
        return "El campo  REGIONAL SIFF  no debe estar vacío, ni exceder máximo de 32 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarCentroSiff')) {
    function validarCentroSiff($data)
    {
        $contarLongitud = mb_strlen($data);       
        if (($contarLongitud >= 0 && $contarLongitud < 30)) {
            return "";
        }
        return "El campo  CENTRO SIFF  no debe estar Vacío, ni exceder máximo de 30 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarCodigoSiff')) {
    function validarCodigoSiff($data)
    {
        $contarLongitud = mb_strlen($data);       
        if (($contarLongitud >= 0 && $contarLongitud < 25)) {
            return "";
        }
        return "El campo  CÓDIGO SIFF  no debe estar vacío, ni exceder máximo de 25 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarTicketId')) {
    function validarTicketId($data)
    {
        $contarLongitud = mb_strlen($data); 
        if (($contarLongitud >= 0 && $contarLongitud < 15)) {
            return "";
        }
        return "El campo TICKETID  no debe estar vacío, ni exceder máximo de 15 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarRadicadoOnbase')) {
    function validarRadicadoOnbase($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud >= 0 && $contarLongitud < 20)) {
            return "";
        }
        return "El campo RADICADO ON BASE no debe exceder máximo de 20 caracteres" . " <br>\n";
    }
}

if (!function_exists('validarDistribucionSancion')) {
    function validarDistribucionSancion($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        if ((is_numeric($data)) && ($data >= 0 && $data < 1000000000000000)) {
            return "";
        }
        return "El campo DISTRIBUCIÓN SANCIÓN debe ser numérico, no exceder máximo de 15 dígitos" . " <br>\n";
    }
}

if (!function_exists('validarFechaPago')) {
    function validarFechaPago($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2 || substr_count($data, '/')==2) {
            return "";
        }
        return "El campo FECHA PAGO vacío o fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaAplicacion')) {
    function validarFechaAplicacion($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2 || substr_count($data, '/')==2  ) {
            return "";
        }
      /*  if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2) {
            return "";
        }*/
        return "El campo FECHA APLICACIÓN vacío o fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaTransaccion')) {
    function validarFechaTransaccion($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2 || substr_count($data, '-')==2)    {
            return "";
        }
       /* if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2) {
            return "";
        }*/
        return "El campo FECHA TRANSACCIÓN vacío o fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaResolucion')) {
    function validarFechaResolucion($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2 || substr_count($data, '/')==2) {
            return "";
        }
       /* if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2) {
            return "";
        }*/
        return "El campo FECHA RESOLUCIÓN  fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaInicioObra')) {
    function validarFechaInicioObra($data)
    {
        if((empty($data) || is_null($data))){ return ""; }
        
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2 || substr_count($data, '/')==2) {
            return "";
        }
       /* if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2) {
            return "";
        }*/
        return "El campo FECHA INICIO OBRA vacío o fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaFonObra')) {
    function validarFechaFonObra($data)
    {

        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '-')==2 || substr_count($data, '/')==2) {
            return "";
        }
        /*if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2) {
            return "";
        }*/
        return "El campo FECHA FIN OBRA vacío o fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarFechaOnbase')) {
    function validarFechaOnbase($data)
    {
        if((empty($data) || is_null($data))){ return ""; }

        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 11) && substr_count($data, '/')==2 || substr_count($data, '-')==2) {
            return "";
        }
        return "El campo FECHA ONBASE fecha incorrecta" . " <br>\n";
    }
}

if (!function_exists('validarPeriodo')) {
    function validarPeriodo($data)
    {
        $contarLongitud = mb_strlen($data);
        if (($contarLongitud > 0 && $contarLongitud < 8)) {
            return "";
        }
        return "El campo PERIODO PAGO vacío o  incorrecta" . " <br>\n";
    }
}

if (!function_exists('validar_fecha_espanol')) {
    function validar_fecha_espanol($fecha)
    {
        $valores = explode('/', $fecha);
        if (count($valores) == 3 && checkdate($valores[1], $valores[0], $valores[2])) {
            return true;
        }
        return false;
    }
}

if (!function_exists('validarFechaPago2')) {
    function validarFechaPago2($data)
    {
        if ($data == '') {
            return "FECHAPAGO vacía";
        } else {
            try {
                $timestamp = PHPExcel_Shared_Date::ExcelToPHP($data);
                $fecha_php = gmdate("Y-m-d", $timestamp);
            } catch (Exception $exc) {
                return "La fecha no es válida";
            }
        }
    }
}

if (!function_exists('es_registro_vacio')) {
    function es_registro_vacio($registro){
        $campos_vacios = 0;
        for($i=0; $i<count($registro); $i++){
            if(!isset($registro[$i])){
                $campos_vacios++;
            }
        }

        return $campos_vacios == 37;
    }
}

?>