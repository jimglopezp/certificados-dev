<?php

/**
 * @author Omar David Pino O
 * @description Realiza pagos
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function misional_h_recla($codPago, $new_date, $codfis_destino, $cod_concepto, $cod_subconcepto, $edit = TRUE) {
    $CI = get_instance();
    $CI->load->model('liquidaciones_model');
    //$CI->load->controller('Cartera_service');
    $pago = $CI->liquidaciones_model->execute_query("SELECT COD_PAGO,DISTRIBUCION_CAPITAL,DISTRIBUCION_INTERES_MORA, DISTRIBUCION_INTERES,COD_FISCALIZACION,VALOR_PAGADO,COD_CONCEPTO,COD_SUBCONCEPTO,to_char(FECHA_APLICACION, 'YYYY-MM-DD') AS FECHA_APLICACION FROM PAGOSRECIBIDOS WHERE COD_PAGO=$codPago", TRUE);
    if (!$pago) {
        return NULL;
    }
    $pagoMisional = pago_a_misional_h_recla($pago['COD_CONCEPTO'], $pago['COD_SUBCONCEPTO']);
    $liquidacion = NULL;
    if ($pagoMisional) {
        $datosc = quitar_pago_misional_h_recla($CI, $pago, $codfis_destino, $edit);
        $liquidacion = $datosc[0];
        $pagos = $datosc[1];
    }
    if ($codfis_destino != NULL) {
        //if ($codfis_destino != $pago['COD_FISCALIZACION'] || !$pagoMisional) {
        //if ($pagoMisional) {
        $select = "NUM_LIQUIDACION,COD_CONCEPTO,COD_TIPOPROCESO,NITEMPRESA,COD_FISCALIZACION,TOTAL_LIQUIDADO,TOTAL_INTERESES,TOTAL_CAPITAL,SALDO_CAPITAL,SALDO_DEUDA,SALDO_INTERES,VALOR_SANCION,SALDO_SANCION,DIAS_MORA,DIAS_MORA_APLICADA,to_char(FECHA_EJECUTORIA, 'YYYY-MM-DD') AS FECHA_EJECUTORIA,TO_CHAR(LIQUIDACION.FECHA_VENCIMIENTO, 'YYYY-MM-DD') AS FECHA_VENCIMIENTO, SALDO_SANCION, SALDO_DEUDA_TMP, SALDO_INTERESES_TMP,SALDO_SANCION_TMP";
        $liquidacion = $CI->liquidaciones_model->execute_query("SELECT $select FROM LIQUIDACION WHERE COD_FISCALIZACION = '$codfis_destino'", TRUE);

        $select = "COD_PAGO,DISTRIBUCION_CAPITAL,DISTRIBUCION_INTERES_MORA, DISTRIBUCION_INTERES,TO_CHAR(FECHA_APLICACION, 'YYYY-MM-DD') AS FECHA_APLICACION,VALOR_PAGADO,COD_FISCALIZACION";
        $pagos = $CI->liquidaciones_model->execute_query("SELECT $select FROM PAGOSRECIBIDOS WHERE COD_FISCALIZACION = '$codfis_destino' AND (FECHA_APLICACION >= TO_DATE('2018-09-01','YYYY-MM-DD') OR RECLASIFICADO = 1) AND COD_PAGO <> $codPago ORDER BY FECHA_APLICACION ASC");
        //}
        if ($liquidacion['COD_CONCEPTO'] == 1 || $liquidacion['COD_CONCEPTO'] == 2 || $liquidacion['COD_CONCEPTO'] == 3 || $liquidacion['COD_CONCEPTO'] == 5) {
            if($edit) {
                $CI->liquidaciones_model->delete('HISTORICO_CARTERAS','COD_FISCALIZACION',$codfis_destino);
            }
            return add_pago_misional_h_recla($CI, $liquidacion, $pagos, $pago, $codfis_destino, $new_date, $edit);
        }
    }
    return $liquidacion;
}
function reiniciar_cartera($liquidacion) {
    $liquidacion['SALDO_INTERES'] = $liquidacion['TOTAL_INTERESES'];
    $liquidacion['SALDO_CAPITAL'] = $liquidacion['TOTAL_CAPITAL'];
    $liquidacion['SALDO_SANCION'] = $liquidacion['VALOR_SANCION'];
    if ($liquidacion['SALDO_DEUDA_TMP']) {
        $liquidacion['SALDO_CAPITAL'] = $liquidacion['SALDO_DEUDA_TMP'];
        $liquidacion['SALDO_INTERES'] = 0;
    }
    if ($liquidacion['SALDO_INTERESES_TMP']) {
        $liquidacion['SALDO_INTERES'] = $liquidacion['SALDO_INTERESES_TMP'];
    }
    if ($liquidacion['SALDO_SANCION_TMP']) {
        $liquidacion['SALDO_SANCION'] = $liquidacion['SALDO_SANCION_TMP'];
    }
    return $liquidacion;
}
function quitar_pagos_array_misional_h_recla($liquidacion, $pagos, $codPago = 0) {
    $interator = -1;
    $pagos_reclasificar = array();
    $length = count($pagos);
    $liquidacion = reiniciar_cartera($liquidacion);
    
    for ($i = 0; $i < $length; $i++) {
        $value = $pagos[$i];
        if ($value['COD_PAGO'] != $codPago) {
            array_push($pagos_reclasificar, $value);
        } else {
            $interator = $i;
        }
    }
    return array($liquidacion, $pagos_reclasificar, $interator);
}

function add_pagos_misional_h_recla($CI, $liquidacion, $pagos_reclasificar, $edit) {
    $editPagos = array();
    $reiniciar = true;
    $length = count($pagos_reclasificar);
    for ($i = 0; $i < $length; $i++) {
        //$liquidacion_u = $CI->liquidaciones_model->liquidacion_en_mora($codfis,$pagos_reclasificar[$i]['FECHA_APLICACION']);
        $fecha = new DateTime($pagos_reclasificar[$i]['FECHA_APLICACION']);
        if ($i != 0) {
            $reiniciar = false;
        }
        $liquidacion = liquidacion_en_mora_h_recla($CI, $liquidacion, $fecha, $liquidacion['DIAS_MORA'], $reiniciar);
        $interes_mora = $CI->liquidaciones_model->interes_mora($liquidacion, $fecha, FALSE);
        //echo "<pre>";print_r($interes_mora);echo "</pre>";

        $liquidacion['DIAS_MORA'] = $liquidacion['DIAS_MORA'] + $liquidacion['DIAS_MORA_A'];
        $liquidacion['DIAS_MORA_APLICADA'] = $liquidacion['DIAS_MORA_APLICADA'] + $liquidacion['DIAS_MORA_A'];
        $liquidacion['SALDO_INTERES'] = $liquidacion['SALDO_INTERES'] + round($interes_mora);
        $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_DEUDA'] + round($interes_mora);
        
        $pago_sancion = 0;
        $pago_re = $pagos_reclasificar[$i]['VALOR_PAGADO'];
        if ($liquidacion['SALDO_SANCION'] > 0) {
            if ($pago_re >= $liquidacion['SALDO_SANCION']) {
                $pago_sancion = $liquidacion['SALDO_SANCION'];
                $pago_re = $pago_re - $liquidacion['SALDO_SANCION'];
            } else {
                $pago_sancion = $pago_re;
                $pago_re = 0;
            }
        }
        
        $pago_interes = 0;
        $pago_capital = $pago_re;
        if ($liquidacion['SALDO_INTERES'] > 0) {
            $porcentaje = ($liquidacion['SALDO_INTERES'] * 100) / ($liquidacion['SALDO_DEUDA'] - $liquidacion['SALDO_SANCION']);
            $pago_interes = ceil(($porcentaje * $pago_capital) / 100);
            $pago_capital = $pago_capital - $pago_interes;
        }
        
        $liquidacion['SALDO_SANCION'] = $liquidacion['SALDO_SANCION'] - $pago_sancion;
        $liquidacion['SALDO_INTERES'] = $liquidacion['SALDO_INTERES'] - $pago_interes;
        $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_DEUDA'] - $pagos_reclasificar[$i]['VALOR_PAGADO'];
        $liquidacion['SALDO_CAPITAL'] = $liquidacion['SALDO_CAPITAL'] - $pago_capital;

        $data_pago = array(
            'VALOR_ADEUDADO' => $liquidacion['SALDO_DEUDA'],
            'DISTRIBUCION_SANCION' => $pago_sancion,
            'DISTRIBUCION_CAPITAL' => $pago_capital,
            'DISTRIBUCION_INTERES' => $pago_interes,
            'COD_FISCALIZACION' => $pagos_reclasificar[$i]['COD_FISCALIZACION'],
            'COD_PAGO' => $pagos_reclasificar[$i]['COD_PAGO']
        );
        array_push($editPagos, $data_pago);
    }
    if ($edit) {
        $data0 = array(
            'DIAS_MORA' => $liquidacion['DIAS_MORA'],
            'DIAS_MORA_APLICADA' => $liquidacion['DIAS_MORA_APLICADA'],
            'SALDO_INTERES' => $liquidacion['SALDO_INTERES'],
            'SALDO_DEUDA' => $liquidacion['SALDO_DEUDA'],
            'SALDO_CAPITAL' => $liquidacion['SALDO_CAPITAL'],
            'SALDO_SANCION' => $liquidacion['SALDO_SANCION']
        );
        $CI->liquidaciones_model->edit('LIQUIDACION', $data0, 'NUM_LIQUIDACION', $liquidacion['NUM_LIQUIDACION']);
        foreach ($editPagos as $key => $value) {
            $CI->liquidaciones_model->edit('PAGOSRECIBIDOS', $value, 'COD_PAGO', $value['COD_PAGO']);
        }
    }
    return $liquidacion;
}

function quitar_pago_misional_h_recla($CI, $pago, $codfis_destino, $edit) {
    $cod_concepto = $pago['COD_CONCEPTO'];
    $cod_subconcepto = $pago['COD_SUBCONCEPTO'];
    $codfis = $pago['COD_FISCALIZACION'];
    $codPago = $pago['COD_PAGO'];
    $datePago1 = new DateTime($pago['FECHA_APLICACION']);
    //$datePago1 = new DateTime('2018-08-29');
    //$liquidacion = $CI->liquidaciones_model->validation_array($CI->liquidaciones_model->get_option('LIQUIDACION', 'COD_FISCALIZACION', $codfis))[0];
    $select = "NUM_LIQUIDACION,COD_CONCEPTO,COD_TIPOPROCESO,NITEMPRESA,COD_FISCALIZACION,TOTAL_LIQUIDADO,TOTAL_INTERESES,TOTAL_CAPITAL,SALDO_CAPITAL,SALDO_DEUDA,SALDO_INTERES,VALOR_SANCION,SALDO_SANCION,DIAS_MORA,DIAS_MORA_APLICADA,to_char(FECHA_EJECUTORIA, 'YYYY-MM-DD') AS FECHA_EJECUTORIA,TO_CHAR(FECHA_VENCIMIENTO, 'YYYY-MM-DD') AS FECHA_VENCIMIENTO, SALDO_SANCION, SALDO_DEUDA_TMP, SALDO_INTERESES_TMP, SALDO_SANCION_TMP";
    $liquidacion = $CI->liquidaciones_model->execute_query("SELECT $select FROM LIQUIDACION WHERE COD_FISCALIZACION = '$codfis_destino'", TRUE);
    $select = "COD_PAGO,DISTRIBUCION_CAPITAL,DISTRIBUCION_INTERES_MORA, DISTRIBUCION_INTERES,to_char(FECHA_APLICACION, 'YYYY-MM-DD') AS FECHA_APLICACION,VALOR_PAGADO,COD_FISCALIZACION";
    $pagos = $CI->liquidaciones_model->execute_query("SELECT $select FROM PAGOSRECIBIDOS WHERE COD_FISCALIZACION = '$codfis_destino' AND (FECHA_APLICACION >= TO_DATE('2018-09-01','YYYY-MM-DD') OR RECLASIFICADO = 1) ORDER BY FECHA_APLICACION DESC");
    $data_array = quitar_pagos_array_misional_h_recla($liquidacion, $pagos, $codPago);
    $liquidacion = $data_array[0];
    $pagos_reclasificar = $data_array[1];
    $interator = $data_array[2];

    $interes_mora = 0;
    $length = count($pagos_reclasificar);
    $liquidacion['DIAS_MORA'] = 0;
    $liquidacion['DIAS_MORA_APLICADA'] = 0;
    $liquidacion['DIAS_MORA_A'] = 0;
    $liquidacion['SALDO_INTERES'] = $liquidacion['TOTAL_INTERESES'];
    $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_CAPITAL'] + $liquidacion['TOTAL_INTERESES'];
    //$liquidacion['SALDO_CAPITAL'] = $liquidacion['SALDO_CAPITAL'];
    $editPagos = array();
    $reiniciar = true;
    for ($i = $length - 1; $i >= 0; $i--) {
        //$liquidacion_u = $CI->liquidaciones_model->liquidacion_en_mora($codfis,$pagos_reclasificar[$i]['FECHA_APLICACION']);
        $fecha = new DateTime($pagos_reclasificar[$i]['FECHA_APLICACION']);
        if ($i != $length - 1) {
            $reiniciar = false;
        }
        $liquidacion = liquidacion_en_mora_h_recla($CI, $liquidacion, $fecha, $liquidacion['DIAS_MORA_A'], $reiniciar);
        $interes_mora = $CI->liquidaciones_model->interes_mora($liquidacion, $fecha, FALSE);

        $liquidacion['DIAS_MORA'] = $liquidacion['DIAS_MORA'] + $liquidacion['DIAS_MORA_A'];
        $liquidacion['DIAS_MORA_APLICADA'] = $liquidacion['DIAS_MORA_APLICADA'] + $liquidacion['DIAS_MORA_A'];
        $liquidacion['SALDO_INTERES'] = $liquidacion['SALDO_INTERES'] + round($interes_mora);
        $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_DEUDA'] + round($interes_mora);

        $pago_re = $pagos_reclasificar[$i]['VALOR_PAGADO'];
        $pago_sancion = 0;
        if ($liquidacion['SALDO_SANCION'] > 0) {
            if ($pago_re >= $liquidacion['SALDO_SANCION']) {
                $pago_sancion = $liquidacion['SALDO_SANCION'];
                $pago_re = $pago_re - $liquidacion['SALDO_SANCION'];
            } else {
                $pago_sancion = $pago_re;
                $pago_re = 0;
            }
        }
        $pago_interes = 0;
        $pago_capital = $pago_re;
        if ($liquidacion['SALDO_INTERES'] > 0 && $pago_capital > 0) {
            $porcentaje = ($liquidacion['SALDO_INTERES'] * 100) / $liquidacion['SALDO_DEUDA'];
            $pago_interes = ceil(($porcentaje * $pago_capital) / 100);
            $pago_capital = $pago_capital - $pago_interes;
        }
        
        $liquidacion['SALDO_SANCION'] = $liquidacion['SALDO_SANCION'] - $pago_sancion;
        $liquidacion['SALDO_INTERES'] = $liquidacion['SALDO_INTERES'] - $pago_interes;
        $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_DEUDA'] - $pagos_reclasificar[$i]['VALOR_PAGADO'];
        $liquidacion['SALDO_CAPITAL'] = $liquidacion['SALDO_CAPITAL'] - $pago_capital;

        if ($i < $interator) {
            $data_pago = array(
                'DISTRIBUCION_CAPITAL' => $pago_capital,
                'DISTRIBUCION_INTERES' => $pago_interes,
                'DISTRIBUCION_SANCION' => $pago_sancion,
                'COD_PAGO' => $pagos_reclasificar[$i]['COD_PAGO']
            );
            $pagos_reclasificar[$i]['DISTRIBUCION_CAPITAL'] = $pago_capital;
            $pagos_reclasificar[$i]['DISTRIBUCION_INTERES'] = $pago_interes;
            $pagos_reclasificar[$i]['DISTRIBUCION_INTERES'] = $pago_sancion;
            array_push($editPagos, $data_pago);
        }
    }
    if ($edit) {
        $data0 = array(
            'DIAS_MORA' => $liquidacion['DIAS_MORA'],
            'DIAS_MORA_APLICADA' => $liquidacion['DIAS_MORA_APLICADA'],
            'SALDO_INTERES' => $liquidacion['SALDO_INTERES'],
            'SALDO_DEUDA' => $liquidacion['SALDO_DEUDA'],
            'SALDO_SANCION' => $liquidacion['SALDO_SANCION'],
            'SALDO_CAPITAL' => $liquidacion['SALDO_CAPITAL'],
        );
        $CI->liquidaciones_model->edit('LIQUIDACION', $data0, 'NUM_LIQUIDACION', $liquidacion['NUM_LIQUIDACION']);
        foreach ($editPagos as $key => $value) {
            $CI->liquidaciones_model->edit('PAGOSRECIBIDOS', $value, 'COD_PAGO', $value['COD_PAGO']);
        }
    }
    return array($liquidacion, $pagos_reclasificar);
}

function add_pago_misional_h_recla($CI, $liquidacion, $pagos, $pago, $codfis, $new_date, $edit) {

    $data_array = quitar_pagos_array_misional_h_recla($liquidacion, $pagos, $pago['COD_PAGO']);
    $liquidacion = $data_array[0];
    //$pagos = $data_array[1];
    $interator = $data_array[2];
    $codPago = $pago['COD_PAGO'];
    $pago['FECHA_APLICACION'] = $new_date;
    $datePago1 = new DateTime($new_date);

    $pagos_reclasificar = array();
    $interator = 0;
    $length = count($pagos);
    $encontro_pago = FALSE;
    //adiciona todos los pagos al total de la deuda y según la fecha intoduce el nuevo pago
    //echo "<pre>";print_r($liquidacion);echo "</pre>";
    for ($i = 0; $i < $length; $i++) {
        $value = $pagos[$i];
        $fechaa = new DateTime($value['FECHA_APLICACION']);
        if ($value['COD_PAGO'] != $codPago) {
            array_push($pagos_reclasificar, $value);
        }
        //busca la fecha para del pago para intrucirlo
        if(($i + 1) < $length) {
            $fecha2 = new DateTime($pagos[$i + 1]['FECHA_APLICACION']);
            if ($datePago1 >= $fechaa && $datePago1 <= $fecha2 && !$encontro_pago) {
                $pago['COD_FISCALIZACION'] = $codfis;
                array_push($pagos_reclasificar, $pago);
                $encontro_pago = TRUE;
                $interator = $i;
            }
        }
    }
    //echo "<pre>";print_r($liquidacion);echo "</pre>";die();
    //si no encuentra ningun pago, lo adiciona
    if (!$encontro_pago) {
        array_push($pagos_reclasificar, $pago);
    }
    $interes_mora = 0;
    $length = count($pagos_reclasificar);
    $liquidacion['DIAS_MORA'] = 0;
    $liquidacion['DIAS_MORA_A'] = 0;
    $liquidacion['DIAS_MORA_APLICADA'] = 0;
    $liquidacion = reiniciar_cartera($liquidacion);
    $liquidacion['SALDO_DEUDA'] = $liquidacion['SALDO_CAPITAL'] + $liquidacion['TOTAL_INTERESES'] + $liquidacion['SALDO_SANCION'];
    $liquidacion = add_pagos_misional_h_recla($CI, $liquidacion, $pagos_reclasificar, $edit);
    return $liquidacion;
}

function liquidacion_en_mora_h_recla($CI, $liquidacion, $fechapago, $dias = 0, $reiniciar = false) {
    if ($liquidacion['FECHA_EJECUTORIA']) {
        $dias_pagos = $CI->liquidaciones_model->insertar_dias_mora_migracion($liquidacion['COD_FISCALIZACION']);
        //echo "<pre>$dias_pagos</pre>";
        //echo "<pre>Hola=";print_r($dias_pagos);echo "</pre>";die();
        $liquidacion2 = $CI->liquidaciones_model->liquidaciones_en_mora($fechapago->format('Y') . '-' . $fechapago->format('m') . '-' . $fechapago->format('d'), NULL, $liquidacion['COD_FISCALIZACION'], $dias + $dias_pagos, $reiniciar,FALSE)[0];
        $liquidacion['DIAS_MORA_A'] = $liquidacion2['DIAS_MORA_A'];
        $liquidacion['DIAS_MORA'] = $dias_pagos + $dias;
        $liquidacion['DIAS_MORA_APLICADA'] = $dias_pagos + $dias;
        /* $fechaejecutoria = new DateTime($liquidacion['FECHA_EJECUTORIA']);
          $interval = $fechapago->diff($fechaejecutoria);
          $signo = $interval->format('%R');
          $dias = $interval->format('%a');
          if($signo = '-') {
          $dias = $dias - $liquidacion['DIAS_MORA_APLICADA'];
          if($dias > 0) {
          return $dias;
          }
          } */
    }
    return $liquidacion;
}

function pago_a_misional_h_recla($concepto, $subconcepto) {
    if ($concepto == 1 || $concepto == 2 || $concepto == 3 || $concepto == 5) {
        if ($subconcepto == 49 || $subconcepto == 3 || $subconcepto == 7 || $subconcepto == 48 || $subconcepto == 6 || $subconcepto == 4 || $subconcepto == 47 || $subconcepto == 80 || $subconcepto == 1 || $subconcepto == 50 || $subconcepto == 9) {
            return true;
        }
    }
    return false;
}

?>
