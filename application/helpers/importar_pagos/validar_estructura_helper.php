<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


    if (!function_exists('estructura_es_valida')) {
        function estructura_es_valida($excelImportadoArray){

            if(!archivo_cargado_exitosamente($excelImportadoArray)){
                return respuesta_error_estructura();
            }

            if(!nro_columnas_es_valido($excelImportadoArray)){
                return respuesta_error_estructura();
            }

            if(!nro_registros_es_valido($excelImportadoArray)){
                return respuesta_error_estructura();
            }

            return array('es_valido' =>true, 'error'=>'');
        }
    }

    if (!function_exists('archivo_cargado_exitosamente')) {
        function archivo_cargado_exitosamente($excelImportadoArray){
            return isset($excelImportadoArray);
        }
    }    

    if (!function_exists('nro_columnas_es_valido')) {
        function nro_columnas_es_valido($excelImportadoArray){
            $registros_invalidos = 0;
            foreach ($excelImportadoArray as $registro){
                if(count($registro)!=37){
                    $registros_invalidos++;
                    break;
                }        
            }

            return $registros_invalidos==0;
        }
    }

    if (!function_exists('nro_registros_es_valido')) {
        function nro_registros_es_valido($excelImportadoArray){
            $nro_registros = count($excelImportadoArray);
            return $nro_registros>1;
        }
    }

    if (!function_exists('respuesta_error_estructura')) {
        function respuesta_error_estructura(){
            return array('es_valido' =>false,
            'error' =>'El archivo no pudo ser cargado, por favor revise si cumple con la estructura de pagos');
        }
    }
    
?>    