<?php

/**
 * @author Omar David Pino O
 * @description Realiza pagos a las cuotas CNM, y reproyecta la cartera segun el pago
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function pago_extraordinario($pago, $cod_cartera, $edit, $fecha_actual, $calc_intereses = TRUE, $facilidad = FALSE, $cesantias = FALSE, $kactus = FALSE) {
    /* if($cesantias) {
      $fecha_actual_n = explode("-", $fecha_actual);
      $fecha_actual = $fecha_actual_n[0]."-".((int)$fecha_actual_n[1] - 1)."-".$fecha_actual_n[2];
      } */
    $fecha_actual_n = explode("-", $fecha_actual);
    $CI = get_instance();
    $CI->load->model('carteranomisional_model');
    $ahorrosincuota = FALSE;
    $pago_sig_mes = FALSE;
    $cnm_cartera = $CI->carteranomisional_model->get_option('CNM_CARTERANOMISIONAL', 'COD_CARTERA_NOMISIONAL', $cod_cartera);
    if ($cnm_cartera) {
        if (count($cnm_cartera->result_array()) > 0) {
            $cnm_cartera = $cnm_cartera->result_array()[0];
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
    $valor_t_mora = $cnm_cartera['VALOR_T_MORA'];
    if ($calc_intereses == FALSE || $valor_t_mora == NULL || $valor_t_mora == 0) {
        $valor_t_mora = 0;
    } else {
        $valor_t_mora = ((pow(1 + ($valor_t_mora / 100), 1 / 365) - 1) * 100) / 100;
    }
    $pagos_despues = $CI->carteranomisional_model->pagos_cartera($cod_cartera, $fecha_actual_n[0] . "-" . $fecha_actual_n[1]);
    if ($pagos_despues != NULL) {
        $fecha_actual = date('Y-m-d');
        $fecha_actual_n = explode("-", $pagos_despues['PERIODO_PAGADO']);
    }
    /* if($cnm_cartera['COD_TIPOCARTERA'] == 1 || $cnm_cartera['COD_TIPOCARTERA'] || $cnm_cartera['COD_TIPOCARTERA'] ==4 || $cnm_cartera['COD_TIPOCARTERA'] == 10) {} */
    $data = $CI->carteranomisional_model->cuotas_cartera($cod_cartera, $fecha_actual, $valor_t_mora, $facilidad); //trae todas las cuotas para la cartera
    $saldo_deuda = $CI->carteranomisional_model->saldo_deuda_cartera($cod_cartera);
    if ($data) {
        $data = $data->result_array();
        $result = array();
        $length = count($data);
        $pagoExtra = 0;
        $pago_mora = pago_mora($data);
        //$pago_interes_mora = interes_c_pendiente($data);
        //$pago = $pago_total - $pago_mora - $pago_interes_mora;
        $pago_origin = $pago;
        if ($pago > 0) {//verificamos que algun pago
            for ($i = 0; $i < $length; $i++) {
                $cuota = $data[$i];
                $saldo_interes_c_nuevo = $pago - $cuota['AMORTIZACION'];

                //$result = NULL;
                //$pago_anticipado = fecha_mayor($fecha_actual_n,$cuota['MES_PROYECTADO'];
                if ($cuota['SALDO_CUOTA'] > 0) {
                    $interes_mora = interes_mora($cuota, $pago);
                    if($cesantias) {
                        $result = pago_mayor_cuota($result, $data, $cuota, $pago_origin, $i, $pagoExtra, $fecha_actual_n, $cnm_cartera, $cesantias);
                        $result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra);
                        $result['CESANTIAS'] = $cesantias;
                    } else {
                        if ($ahorrosincuota) {
                            $result = pago_sin_cuota($CI, $data, $result, $data[$i - 1], $pago, $i - 1, $pagoExtra, $cnm_cartera);
                        } else {
                            if ($pago_sig_mes) {
                                $result = pago_siguiente_mes($CI, $data, $result, $pago, $i - 1, $pagoExtra, $cnm_cartera);
                            } else {
                                if ($pago == $cuota['SALDO_CUOTA']) {
                                    $result = pago_igual_cuota($data, $cuota, $saldo_deuda, $interes_mora);
                                } else {
                                    if ($pago < $cuota['SALDO_CUOTA']) {
                                        $saldo_cuota = $cuota['SALDO_CUOTA'] - $pago;
                                        if ($pago >= $cuota['SALDO_INTERES_C']) {
                                            $result = pago_menor_cuota_y_mayor_interes($data, $cuota, $saldo_cuota, $pago, $saldo_deuda, $interes_mora);
                                        } else {
                                            $result = pago_menor_cuota_y_menor_interes($data, $cuota, $saldo_cuota, $pago, $saldo_deuda, $interes_mora);
                                        }
                                    } else {
                                        $result = pago_mayor_cuota($result, $data, $cuota, $pago_origin, $i, $pagoExtra, $fecha_actual_n, $cnm_cartera, $cesantias);
                                        $result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra);
                                        $result['CESANTIAS'] = $cesantias;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if ($cuota['SALDO_CUOTA'] == 0 && $cuota['MES_PROYECTADO'] == $fecha_actual) {
                        $ahorrosincuota = TRUE;
                        //$result = pago_sin_cuota($CI, $data, $result, $cuota, $pago, $i, $pagoExtra,$cnm_cartera);
                        //$result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra);
                        /* $result = recalcular_cuotas($data, $result, $cuota['CAPITAL'], $cuota['AMORTIZACION'], $pago, $i + 1, $pago, $pagoExtra);
                          $result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra); */
                    } else {
                        if (fecha_mayor($fecha_actual, $cuota['FECHA_LIM_PAGO'])) {
                            $pago_sig_mes = TRUE;
                            $ahorrosincuota = FALSE;
                            //$result = pago_siguiente_mes($CI, $data, $result, $pago, $i, $pagoExtra,$cnm_cartera);
                        }
                    }
                }
                if ($result != NULL) {
                    if ($kactus) {
                        $result = actualizar_cuotas($result);
                    }
                    if ($edit) {
                        actualizar_eliminar_db($CI, $result, $cod_cartera);
                    }
                    return $result;
                }
            }
        } else {
            return array_result(0, $CI->carteranomisional_model->saldo_deuda_cartera($cod_cartera), $res = array(), $res2 = array(), $data);
        }
    }
    return NULL;
}

function array_result($pe, $sd, $actualizar, $eliminar, $data) {
    return array(
        'PAGOEXTRA' => $pe,
        'SALDO_DEUDA' => $sd,
        'ACTUALIZAR' => $actualizar,
        'ELIMINAR' => $eliminar,
        'CUOTAS_ANTIGUAS' => $data
    );
}

function data_cuota($cuota, $capital, $saldo, $s_interesc, $s_amortizacion, $interes_mora = 0) {
    $data_cuota1 = array(
        'CAPITAL' => $capital,
        'SALDO_CUOTA' => $saldo,
        'SALDO_INTERES_C' => $s_interesc,
        'SALDO_AMORTIZACION' => $s_amortizacion,
        'SALDO_INTERES_NO_PAGOS' => $s_interesc,
        'ID_DEUDA_E' => $cuota['ID_DEUDA_E'],
        'NO_CUOTA' => $cuota['NO_CUOTA'],
        'SALDO_INTERES_MORA_GEN' => $cuota['INTERES_MORA_PAGO'] - $interes_mora[0],
        'SALDO_INTERES_NO_PAGOS' => $cuota['SALDO_INTERES_NO_PAGOS'] - $interes_mora[1]
    );
    return $data_cuota1;
}

function add_interes_mora($data1, $cuota, $interes_mora) {
    if (array_key_exists('INTERES_MORA_PAGO', $cuota)) {
        if ($cuota['INTERES_MORA_PAGO'] > 0 && $interes_mora[0] > 0) {
            $data1['INTERES_MORA_PAGO'] = $interes_mora[0];
        }
        if ($cuota['SALDO_INTERES_NO_PAGOS'] > 0 && $interes_mora[1] > 0) {
            $data1['INTERES_NO_PAGOS_PAGO'] = $interes_mora[1];
        }
    }
    return $data1;
}

function pago_igual_cuota($data, $cuota, $saldo_deuda, $interes_mora = 0) {
    $data1 = pago_igual_cuota_($data, $cuota, $interes_mora);
    $res = array();
    array_push($res, $data1);
    return array_result(0, $saldo_deuda - $cuota['SALDO_CUOTA'], $res, $res2 = array(), $data);
}

function pago_igual_cuota_($data, $cuota, $interes_mora) {
    $data_cuota1 = data_cuota($cuota, $cuota['CAPITAL'], 0, 0, 0, $interes_mora);
    $data1 = array(
        'CUOTA' => $data_cuota1,
        'PAGO' => $cuota['SALDO_CUOTA'],
        'PAGO_INTERES' => $cuota['SALDO_INTERES_C']
    );
    return add_interes_mora($data1, $cuota, $interes_mora);
}

function pago_menor_cuota_y_mayor_interes($data, $cuota, $saldo_cuota, $pago, $saldo_deuda, $interes_mora = 0) {
    $data1 = pago_menor_cuota_y_mayor_interes_($data, $cuota, $saldo_cuota, $pago, $interes_mora);
    $res = array();
    array_push($res, $data1);
    return array_result(0, $saldo_deuda - $saldo_cuota, $res, $res2 = array(), $data);
}

function pago_menor_cuota_y_mayor_interes_($data, $cuota, $saldo_cuota, $pago, $interes_mora) {
    $saldo_interes_c = 0;
    $pago_interes = 0;
    if ($cuota['VALOR_INTERES_C'] > 0) {
        $saldo_interes_c = $pago - $cuota['VALOR_INTERES_C'];
        $pago_interes = $cuota['SALDO_INTERES_C'];
    }
    $saldo_amortizacion = $cuota['SALDO_AMORTIZACION'] - $saldo_interes_c;
    if ($saldo_amortizacion > $saldo_cuota) {
        $saldo_amortizacion = $saldo_cuota;
    }
    $data_cuota1 = data_cuota($cuota, $cuota['CAPITAL'], $saldo_cuota, 0, $saldo_amortizacion, $interes_mora);
    $data1 = array(
        'CUOTA' => $data_cuota1,
        'PAGO' => $pago,
        'PAGO_INTERES' => $pago_interes
    );
    return add_interes_mora($data1, $cuota, $interes_mora);
}

function pago_menor_cuota_y_menor_interes($data, $cuota, $saldo_cuota, $pago, $saldo_deuda, $interes_mora = 0) {
    $data1 = pago_menor_cuota_y_menor_interes_($data, $cuota, $saldo_cuota, $pago, $interes_mora);
    $res = array();
    array_push($res, $data1);
    return array_result(0, $saldo_deuda - $saldo_cuota, $res, $res2 = array(), $data);
}

function pago_menor_cuota_y_menor_interes_($data, $cuota, $saldo_cuota, $pago, $interes_mora) {
    $saldo_interes_c = $cuota['VALOR_INTERES_C'] - $pago;
    $data_cuota1 = data_cuota($cuota, $cuota['CAPITAL'], $saldo_cuota, $saldo_interes_c, $cuota['AMORTIZACION'], $interes_mora);
    $data1 = array(
        'CUOTA' => $data_cuota1,
        'PAGO' => $pago,
        'PAGO_INTERES' => $pago
    );
    return add_interes_mora($data1, $cuota, $interes_mora);
}

function interes_mora($cuota, &$pago) {
    $interes_mora = 0;
    if (array_key_exists('INTERES_MORA_PAGO', $cuota)) {
        if ($cuota['INTERES_MORA_PAGO'] > 0) {
            if ($pago >= $cuota['INTERES_MORA_PAGO']) {
                $interes_mora = $cuota['INTERES_MORA_PAGO'];
                $pago = $pago - $interes_mora;
            } else {
                $interes_mora = $pago;
                $pago = 0;
            }
        }
    }
    $pago = round($pago);

    $interes_mora_nop = 0;
    if ($cuota['SALDO_INTERES_NO_PAGOS'] > 0) {
        if ($pago >= $cuota['SALDO_INTERES_NO_PAGOS']) {
            $interes_mora_nop = $cuota['SALDO_INTERES_NO_PAGOS'];
            $pago = $pago - $interes_mora_nop;
        } else {
            $interes_mora_nop = $pago;
            $pago = 0;
        }
    }
    $pago = round($pago);
    return array($interes_mora, $interes_mora_nop);
}

function pago_mayor_cuota($result, $datos, $cuota, $pago, $iterator, &$pagoExtra, $fecha_actual_n, $cnm_cartera, $cesantias) {
    $fecha_actual2 = $fecha_actual_n[0] . '-' . $fecha_actual_n[1];
    $fecha_actual = new DateTime($fecha_actual_n[0] . '-' . $fecha_actual_n[1] . '-' . $fecha_actual_n[2]);
    if (!$cesantias) {
        while (fecha_menor($fecha_actual, $cuota['FECHA_LIM_PAGO']) && $pago > 0) {
            $interes_mora = interes_mora($cuota, $pago);
            if (($pago - $cuota['SALDO_CUOTA'] >= 0)) {
                $pago_aplicar = $cuota['SALDO_CUOTA'];
            } else {
                $pago_aplicar = $pago;
            }
            $pago = $pago - $pago_aplicar;
            $result = pago_menor_o_igual($datos, $result, $cuota, $pago_aplicar, $interes_mora);
            $iterator = $iterator + 1;
            $cuota = $datos[$iterator];
        }
    } else {
        while (fecha_menor($fecha_actual, $cuota['FECHA_LIM_PAGO']) && $pago > 0) {
            $interes_mora = interes_mora($cuota, $pago);
            if (($pago - $cuota['SALDO_AMORTIZACION'] >= 0)) {
                $pago_aplicar = $cuota['SALDO_AMORTIZACION'];
            } else {
                $pago_aplicar = $pago;
            }
            $pago = $pago - $pago_aplicar;
            $cuota['SALDO_AMORTIZACION'] = $cuota['SALDO_AMORTIZACION'] - $pago_aplicar;
            $data1 = array(
                'CUOTA' => $cuota,
                'PAGO' => $pago_aplicar,
                'PAGO_INTERES' => 0
            );
            array_push($result, $data1);
            $iterator = $iterator + 1;
            $cuota = $datos[$iterator];
        }
    }
    $pago = round($pago);
    $interes_mora = interes_mora($cuota, $pago);
    if ($pago > 0) {
        if ($pago <= $cuota['SALDO_CUOTA']) {
            $result = pago_menor_o_igual($datos, $result, $cuota, $pago, $interes_mora);
        } else {
            $saldo_cuota = $cuota['SALDO_CUOTA'] - $pago;
            if ($saldo_cuota < 0) {
                $saldo_cuota = 0;
            }
            $pago_interes = 0;
            if ($cuota['VALOR_INTERES_C'] > 0) {
                $pago_interes = $cuota['SALDO_INTERES_C'];
            }

            $capital = $cuota['CAPITAL'];
            $pago_cuota = $cuota['SALDO_CUOTA'];
            if (!$cesantias) {
                $saldo_interes_c_nuevo = $pago - $cuota['SALDO_INTERES_C'];
                $saldo_amortizacion_mes = $saldo_interes_c_nuevo - $cuota['SALDO_AMORTIZACION'];
                $data_cuota1 = data_cuota($cuota, $capital, $saldo_cuota, 0, 0);
                $pago_capital = $pago - $cuota['SALDO_CUOTA'];
            } else {
                $capital = $capital - $pago;
                $saldo_amortizacion_mes = $cuota['SALDO_AMORTIZACION'];
                $data_cuota1 = data_cuota($cuota, $capital, $cuota['SALDO_CUOTA'], $cuota['SALDO_INTERES_C'], $cuota['SALDO_AMORTIZACION']);
                $pago_capital = $pago;
                $pago_interes = 0;
                $pago_cuota = 0;
                //$capital = $capital - $pago;
            }

            $data1 = array(
                'CUOTA' => $data_cuota1,
                'PAGO' => $pago_cuota,
                'PAGO_CAPITAL' => $pago_capital,
                'PAGO_INTERES' => $pago_interes
            );
            $data1 = add_interes_mora($data1, $cuota, array(0, 0));
            array_push($result, $data1);
            return recalcular_cuotas($datos, $result, $capital, $cuota['AMORTIZACION'], $saldo_amortizacion_mes, $iterator + 1, NULL, $pagoExtra, FALSE, $cnm_cartera);
        }
    }
    return $result;
}

function pago_menor_o_igual($datos, $result, $cuota, $pago_aplicar, $interes_mora = 0) {
    if ($pago_aplicar == $cuota['SALDO_CUOTA']) {
        array_push($result, pago_igual_cuota_($datos, $cuota, $interes_mora));
    } else {
        if ($pago_aplicar < $cuota['SALDO_CUOTA']) {
            $saldo_cuota = $cuota['SALDO_CUOTA'] - $pago_aplicar;
            if ($pago_aplicar >= $cuota['SALDO_INTERES_C']) {
                array_push($result, pago_menor_cuota_y_mayor_interes_($datos, $cuota, $saldo_cuota, $pago_aplicar, $interes_mora));
            } else {
                array_push($result, pago_menor_cuota_y_menor_interes_($datos, $cuota, $saldo_cuota, $pago_aplicar, $interes_mora));
            }
        }
    }
    return $result;
}

function pago_sin_cuota($CI, $data, $result, $cuota, $pago, $i, &$pagoExtra, $cnm_cartera) {
    //$pago_capital = $cuota[$i + 1];
    $pagoExtra1 = 0;
    $capitalnuevo = $data[$i + 1]['CAPITAL'] - $pago;
    if ($capitalnuevo < 0) {
        $pagoExtra1 = abs($capitalnuevo);
        $capitalnuevo = 0;
    }
    $capital_a = $pago - $pagoExtra1;
    $data_cuota1 = data_cuota($cuota, $cuota['CAPITAL'], $cuota['SALDO_CUOTA'], $cuota['SALDO_INTERES_C'], $cuota['SALDO_AMORTIZACION']);

    $data1 = array(
        'CUOTA' => $data_cuota1,
        'PAGO_CAPITAL' => $capital_a
    );
    $data1 = add_interes_mora($data1, $cuota, array(0, 0));
    array_push($result, $data1);

    $cuota_nueva = calcular_cuota($capitalnuevo, $data[$i + 1]);
    $data1 = array(
        'CUOTA' => $cuota_nueva,
        'PAGO_CAPITAL' => 0
    );
    $data1 = add_interes_mora($data1, $cuota_nueva, array(0, 0));
    array_push($result, $data1);

    $result = recalcular_cuotas($data, $result, $cuota_nueva['CAPITAL'], $cuota_nueva['AMORTIZACION'], 0, $i + 2, NULL, $pagoExtra, FALSE, $cnm_cartera);
    $result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra);
    return $result;
}

function pago_siguiente_mes($CI, $data, $result, $pago, $i, &$pagoExtra, $cnm_cartera) {
    $cuota = $data[$i];
    $pago_capital = $CI->carteranomisional_model->saldo_a_capital_cuota($cuota['ID_DEUDA_E'], $cuota['NO_CUOTA']);
    if ($pago_capital || $cuota['SALDO_CUOTA'] == 0) {
        $pago_capital = TRUE;
        $i = $i + 1;
        //$cuota['CAPITAL'] = $data[$i]['CAPITAL'];
        $data_cuota1 = data_cuota($cuota, $cuota['CAPITAL'], 0, $cuota['SALDO_INTERES_C'], $cuota['SALDO_AMORTIZACION']);

        $data1 = array(
            'CUOTA' => $data_cuota1,
            'PAGO_CAPITAL' => $pago,
            'PAGO_ANTICIPADO' => FALSE
        );
        $data1 = add_interes_mora($data1, $cuota, array(0, 0));
        array_push($result, $data1);
        $cuota = $data[$i];
    }
    $capital_n = $cuota['CAPITAL'];
    $amortizacion_n = $cuota['AMORTIZACION'];
    $result = recalcular_cuotas($data, $result, $capital_n, $amortizacion_n, $pago, $i, $pago, $pagoExtra, $pago_capital, $cnm_cartera);
    $result = cuotas_a_eliminar_y_actualizar($data, $result, $pagoExtra);
    return $result;
}

function recalcular_cuotas($datos, $result, $capital, $amortizacion, $saldo_amortizacion_mes, $iterator, $pago, &$pagoExtra, $pagoanticipado2, $cnm_cartera) {
    $interes_c_mensual = ((pow(1 + ($cnm_cartera['VALOR_T_CORRIENTE'] / 100), 1 / 12) - 1) * 100) / 100;
    for ($index = $iterator; $index < count($datos); $index++) {
        $capital_a = 0;
        if ($index == $iterator) {
            if ($pagoanticipado2) {
                $capitalnuevo = $capital - $saldo_amortizacion_mes;
                $pagoanticipado2 = FALSE;
                $pago = 0;
            } else {
                $capitalnuevo = $capital - $amortizacion - $saldo_amortizacion_mes;
            }
            if ($capitalnuevo < 0) {
                $pagoExtra = abs($capitalnuevo);
                $capitalnuevo = 0;
            }
            if ($pago != NULL) {
                $capital_a = $pago - $pagoExtra;
            }
        } else {
            $capitalnuevo = $capital - $amortizacion;
        }
        $cuota_nueva = calcular_cuota($capitalnuevo, $datos[$index], $interes_c_mensual);
        $data1 = array(
            'CUOTA' => $cuota_nueva,
            'PAGO_CAPITAL' => $capital_a
        );
        if (array_key_exists('INTERES_MORA_PAGO', $datos[$index])) {
            if ($datos[$index]['INTERES_MORA_PAGO'] > 0) {
                $data1['INTERES_MORA_PAGO'] = $datos[$index]['INTERES_MORA_PAGO'];
            }
        }
        $data1 = add_interes_mora($data1, $cuota_nueva, array(0, 0));
        array_push($result, $data1);
        $capital = $cuota_nueva['CAPITAL'];
        $amortizacion = $cuota_nueva['AMORTIZACION'];
    }
    return $result;
}

function calcular_cuota($capital, $cuota, $interes_c_mensual) {
    $valor_interes_c = 0;
    if ($cuota['VALOR_INTERES_C'] > 0) {
        $valor_interes_c = $capital * $interes_c_mensual;
    }
    $amortizacion = $cuota['VALOR_CUOTA'] - $valor_interes_c;
    if ($capital < $cuota['VALOR_CUOTA']) {
        $amortizacion = $capital;
        $cuota['VALOR_CUOTA'] = $capital + $valor_interes_c;
        $cuota['SALDO_CUOTA'] = $capital + $valor_interes_c;
    }
    $saldo_final_capital = $capital - $amortizacion;
    if ($saldo_final_capital < 0) {
        $saldo_final_capital = 0;
    }
    $data_cuota1 = array(
        'VALOR_CUOTA' => $cuota['VALOR_CUOTA'],
        'SALDO_CUOTA' => $cuota['SALDO_CUOTA'],
        'VALOR_INTERES_C' => number_format($valor_interes_c, 0, '.', ''),
        'SALDO_INTERES_C' => number_format($valor_interes_c, 0, '.', ''),
        'AMORTIZACION' => round($amortizacion, 0),
        'SALDO_AMORTIZACION' => round($amortizacion, 0),
        'SALDO_FINAL_CAP' => number_format($saldo_final_capital, 0, '.', ''),
        'CAPITAL' => $capital,
        'ID_DEUDA_E' => $cuota['ID_DEUDA_E'],
        'NO_CUOTA' => $cuota['NO_CUOTA']
    );
    return $data_cuota1;
}

function pago_mora($data) {
    $pago_mora = 0;
    $length = count($data);
    for ($i = 0; $i < $length; $i++) {
        if (array_key_exists('INTERES_MORA_PAGO', $data[$i])) {
            if ($data[$i]['SALDO_AMORTIZACION'] > 0 && $data[$i]['INTERES_MORA_PAGO'] > 0) {
                $pago_mora = $pago_mora + $data[$i]['INTERES_MORA_PAGO'];
            }
        }
    }
    return $pago_mora;
}

function interes_c_pendiente($data) {
    $mora = 0;
    $length = count($data);
    for ($i = 0; $i < $length; $i++) {
        if (array_key_exists('DIAS_MORA', $data[$i])) {
            if ($data[$i]['SALDO_AMORTIZACION'] > 0 && $data[$i]['DIAS_MORA'] > 0) {
                $mora = $mora + $data[$i]['SALDO_INTERES_C'];
            }
        }
    }
    return $mora;
}

function cuotas_a_eliminar_y_actualizar($data2, $data, $pagoExtra, $kactus = FALSE) {
    $eliminar = array();
    $actualizar = array();
    $length = count($data);
    $band = false;
    for ($i = 0; $i < $length; $i++) {
        $value = $data[$i];
        if (array_key_exists('CAPITAL', $value['CUOTA'])) {
            if ($value['CUOTA']['CAPITAL'] <= 0) {
                //array_push($eliminar, $value);
                $data_cuota1 = limpiar_cuota($value['CUOTA']['ID_DEUDA_E'], $value['CUOTA']['NO_CUOTA']);
                array_push($actualizar, $data_cuota1);
            } else {
                if (!$band && ($i + 1) < $length) {
                    $value2 = $data[$i + 1];
                    /* if ($value2['CUOTA']['CAPITAL'] < 0 && $pagoExtra == 0) {
                      $value['CUOTA']['SALDO_CUOTA'] = $value['CUOTA']['CAPITAL'] + $value['CUOTA']['AMORTIZACION'] + $value['CUOTA']['VALOR_INTERES_C'];
                      $value['CUOTA']['VALOR_CUOTA'] = $value['CUOTA']['SALDO_CUOTA'];
                      $band = true;
                      } */
                }
                if ($kactus) {
                    $value['CUOTA']['KACTUS'] = 2;
                }
                array_push($actualizar, $value);
            }
        } else {
            if ($kactus) {
                $value['CUOTA']['KACTUS'] = 2;
            }
            //$value['CAPITAL'] = 0;
            array_push($actualizar, $value);
        }
    }

    return array_result($pagoExtra, get_saldo_deuda($actualizar), $actualizar, $eliminar, $data2);
}

function get_saldo_deuda($actualizar) {
    $saldo_deuda = 0;
    $length = count($actualizar);
    for ($i = 0; $i < $length; $i++) {
        $value = $actualizar[$i];
        $saldo_deuda += $value['CUOTA']['SALDO_CUOTA'];
    }
    return $saldo_deuda;
}

function actualizar_eliminar_db($CI, $datos, $cod_cartera) {
    if ($datos != NULL) {
        foreach ($datos['ACTUALIZAR'] as $value) {
            $data = condicion_cuota($value['CUOTA']['ID_DEUDA_E'], $value['CUOTA']['NO_CUOTA']);
            $CI->carteranomisional_model->edit_('CNM_CUOTAS', $value['CUOTA'], $data);
        }
        foreach ($datos['ELIMINAR'] as $value) {
            $data = condicion_cuota($value['CUOTA']['ID_DEUDA_E'], $value['CUOTA']['NO_CUOTA']);
            //$CI->codegen_model->delete_('CNM_CUOTAS', $data);

            $data_cuota1 = limpiar_cuota($value['CUOTA']['ID_DEUDA_E'], $value['CUOTA']['NO_CUOTA']);
            $CI->carteranomisional_model->edit_('CNM_CUOTAS', $data_cuota1, $data);
        }

        $interes_acumulado = $CI->carteranomisional_model->interes_acumulado_cartera($cod_cartera);
        $interes_moratorio = $CI->carteranomisional_model->interes_moratorio_cartera($cod_cartera);
        $saldo_deuda = $CI->carteranomisional_model->saldo_deuda_cartera($cod_cartera);
        $data_cartera = array(
            'SALDO_INTERES_ACUMULADO' => $interes_acumulado,
            'SALDO_INTERES_MORATORIO' => $interes_moratorio,
            'SALDO_DEUDA' => $saldo_deuda
        );
        $CI->carteranomisional_model->edit('CNM_CARTERANOMISIONAL', $data_cartera, "COD_CARTERA_NOMISIONAL", $cod_cartera);
    }
}

function actualizar_eliminar_db_($datos, $cod_cartera) {
    $CI = get_instance();
    $CI->load->model('carteranomisional_model');
    actualizar_eliminar_db($CI, $datos, $cod_cartera);
}

function limpiar_cuota($idd, $nc) {
    $data_cuota1 = array(
        'ID_DEUDA_E' => $idd,
        'NO_CUOTA' => $nc,
        'CAPITAL' => 0,
        'SALDO_CUOTA' => 0,
        'VALOR_CUOTA' => 0,
        'VALOR_INTERES_C' => 0,
        'INTERES_MORA_GEN' => 0,
        'AMORTIZACION' => 0,
        'SALDO_FINAL_CAP' => 0,
        'SALDO_AMORTIZACION' => 0,
        'VALOR_INTERES_NO_PAGOS' => 0,
        'SALDO_INTERES_NO_PAGOS' => 0,
        'AMORTIZACION' => 0,
        'CESANTIAS' => 0,
        'SEGURO_VIDA' => 0,
        'SEGURO_INCENDIO' => 0,
        'SALDO_SEGURO_VIDA' => 0,
        'SALDO_SEGURO_INCENDIO' => 0
    );
    return array('CUOTA' => $data_cuota1);
}

function eliminar_cuotas_sinsaldo($data) {
    $length = count($data);
    $newData = array();
    for ($i = 0; $i < $length; $i++) {
        if ($data[$i]['SALDO_CUOTA'] > 0) {
            array_push($newData, $data[$i]);
        }
    }
    return $newData;
}

function condicion_cuota($iddeuda, $nrocuota) {
    $data = array();
    $datav = array('id' => 'ID_DEUDA_E', 'value' => $iddeuda);
    array_push($data, $datav);
    $datav = array('id' => 'NO_CUOTA', 'value' => $nrocuota);
    array_push($data, $datav);
    return $data;
}

function pagos_a_capital($CI, $cod_cartera, $nro) {
    $pagos = $CI->carteranomisional_model->pagos_cuota($cod_cartera, $nro);
    foreach ($pagos as $value) {
        if ($value['DISTRIBUCION_CAPITAL'] > 0) {
            return TRUE;
        }
    }
    return FALSE;
}

function fecha_mayor($fecha1, $fecha2) {
    $fecha1 = new DateTime($fecha1);
    $fecha2 = new DateTime($fecha2);
    if ($fecha2 > $fecha1) {
        return TRUE;
    }
    return FALSE;
}

function fecha_menor($fecha1, $fecha2) {
    $fecha_2 = new DateTime($fecha2);
    if ($fecha_2 < $fecha1) {
        return TRUE;
    }
    return FALSE;
}

function aplicar_pago($datos, $cuotas_recalculadas, $saldo_aplicar, $cod_cartera, $post = NULL) {
    $text = $datosNm = array("", 1);
    $CI = get_instance();
    $CI->load->model('aplicacionautomaticadepago_model');
    $numero_pagos = 0;
    if ($cuotas_recalculadas != NULL) {
        /* echo "<pre>";
          print_r( $cuotas_recalculadas['CUOTAS_ANTIGUAS']);
          echo "</pre>";die(); */
        foreach ($cuotas_recalculadas['ACTUALIZAR'] as $cuota_r) {
            $band = FALSE;
            $interes_mora = 0;
            $interes_mora_no_pagos = 0;
            if (array_key_exists('PAGO', $cuota_r)) {
                $band = TRUE;
            } else {
                if (array_key_exists('PAGO_CAPITAL', $cuota_r)) {
                    $pcapital = $cuota_r['PAGO_CAPITAL'];
                    if ($pcapital > 0) {
                        $band = TRUE;
                    }
                }
            }
            if (array_key_exists('INTERES_MORA_PAGO', $cuota_r)) {
                $interes_mora = $cuota_r['INTERES_MORA_PAGO'];
                $band = TRUE;
            }
            if (array_key_exists('INTERES_NO_PAGOS_PAGO', $cuota_r)) {
                $interes_mora_no_pagos = $cuota_r['INTERES_NO_PAGOS_PAGO'];
                $band = TRUE;
            }
            if ($band == TRUE) {
                foreach ($cuotas_recalculadas['CUOTAS_ANTIGUAS'] as $cuota_origin) {
                    if ($cuota_origin['NO_CUOTA'] == $cuota_r['CUOTA']['NO_CUOTA']) {
                        $pago_a_capital = 0;
                        if (array_key_exists('PAGO_CAPITAL', $cuota_r)) {
                            $pago_a_capital = $cuota_r['PAGO_CAPITAL'];
                        }
                        $pago_cuota = 0;
                        if (array_key_exists('PAGO', $cuota_r)) {
                            $pago_cuota = $cuota_r['PAGO'];
                        }
                        $pago_interes = 0;
                        if (array_key_exists('PAGO_INTERES', $cuota_r)) {
                            $pago_interes = $cuota_r['PAGO_INTERES'];
                        }
                        $nro_cuota = $cuota_r['CUOTA']['NO_CUOTA'];
                        $datos['NRO_REFERENCIA'] = '' . $cod_cartera;
                        $datos['NRO_CUOTA'] = $nro_cuota;
                        $datos['DISTRIBUCION_CAPITAL'] = $pago_a_capital;
                        $datos['VALOR_PAGADO'] = $pago_cuota + $pago_a_capital + $interes_mora; //$saldo_aplicar - $cuotas_recalculadas['PAGOEXTRA']
                        $datos['VALOR_ADEUDADO'] = $cuota_origin['CAPITAL'];
                        $datos['DISTRIBUCION_INTERES_MORA'] = $interes_mora;
                        $datos['DISTRIBUCION_INTERES'] = $pago_interes + $interes_mora_no_pagos;
                        $datos['PERIODO_PAGADO'] = $cuota_origin['MES_PROYECTADO'];
                        if ($datos['PROCEDENCIA'] == 'CESANTIAS') {
                            $datos['PAGO_CESANTIAS'] = $datos['VALOR_PAGADO'];
                        }


                        $datos['APLICADO'] = 1;
                        if ($post == NULL || $numero_pagos) {
                            $aplicado = $CI->aplicacionautomaticadepago_model->aplicar_pago($datos);
                            if (is_array($aplicado)) {
                                $class = "error";
                                $text[1] = 0;
                                $text[0] = "EL PAGO A LA CARTERA NRO. " . $cod_cartera . " CUOTA NRO. " . $nro_cuota . ". presento errores.<br>" . $aplicado[1];
                            } else {
                                $class = "info";
                                $text[1] = 1;
                                $text[0] = "EL PAGO A LA CARTERA NRO. " . $cod_cartera . " CUOTA NRO. " . $nro_cuota . " VALOR: " . $datos['VALOR_PAGADO'] . ". SE REGISTRO CORRECTAMENTE<BR>EL PAGO FUE APLICADO AUTOMATICAMENTE.";
                            }
                        } else {
                            reclasificar_pago($post, $datos);
                            $numero_pagos = $numero_pagos + 1;
                        }
                    }
                }
            }
        }
    }
    return $text;
}

function aplicar_pago_unico($datos, $saldo_aplicar, $cod_cartera, $nro_cuota, $valorAdeudado, $data_cuota1) {
    $text = $datosNm = array();
    $CI = get_instance();
    $CI->load->model('aplicacionautomaticadepago_model');
    $datos['NRO_REFERENCIA'] = $cod_cartera;
    $datos['NRO_CUOTA'] = $nro_cuota;
    $datos['DISTRIBUCION_CAPITAL'] = 0;
    $datos['VALOR_PAGADO'] = $saldo_aplicar;
    $datos['VALOR_ADEUDADO'] = $valorAdeudado;
    $datos['DISTRIBUCION_INTERES_MORA'] = 0;
    $datos['DISTRIBUCION_INTERES'] = 0;
    $datos['APLICADO'] = 1;

    $aplicado = $CI->aplicacionautomaticadepago_model->aplicar_pago($datos);
    if (is_array($aplicado)) {
        $class = "error";
        $text[] = "EL PAGO A LA CARTERA NRO. " . $cod_cartera . " CUOTA NRO. " . $nro_cuota . ". presento errores.<br>" . $aplicado[1];
    } else {
        $data = condicion_cuota($cod_cartera, $nro_cuota);
        $CI->carteranomisional_model->edit_('CNM_CUOTAS', $data_cuota1, $data);

        $class = "info";
        $text[] = "EL PAGO A LA CARTERA NRO. " . $cod_cartera . " CUOTA NRO. " . $nro_cuota . " VALOR: " . $datos['VALOR_PAGADO'] . ". SE REGISTRO CORRECTAMENTE<BR>EL PAGO FUE APLICADO AUTOMATICAMENTE.";
    }
    return $text;
}

function reclasificar_pago($post, $datos) {
    $date = array(
        'FECHA_PAGO' => $post['fecha_pago'],
        'FECHA_APLICACION' => $post['fecha_aplicacion'],
        'FECHA_TRANSACCION' => $post['fecha_transaccion'],
        'FECHA_INICIO_OBRA' => $post['fec_ini_obra'],
        'FECHA_FIN_OBRA' => $post['fec_fin_obra'],
        'FECHA_ONBASE' => $post['campo_fecha'],
        'FECHA_RECLASIFICACION' => $post['fecha_aplicacion']
    );

    $data = array(
        'NRO_REFERENCIA' => $datos['NRO_REFERENCIA'],
        'DISTRIBUCION_CAPITAL' => $datos['DISTRIBUCION_CAPITAL'],
        'VALOR_PAGADO' => $datos['VALOR_PAGADO'],
        'DISTRIBUCION_INTERES_MORA' => $datos['DISTRIBUCION_INTERES_MORA'],
        'DISTRIBUCION_INTERES' => $datos['DISTRIBUCION_INTERES'],
        'COD_CONCEPTO' => $post['concepto'],
        'COD_SUBCONCEPTO' => $post['subconcepto'],
        'COD_REGIONAL' => $post['regional'],
        'PERIODO_PAGADO' => $datos['PERIODO_PAGADO'],
        'VALOR_ADEUDADO' => $datos['VALOR_ADEUDADO'],
        'NRO_CUOTA' => $datos['NRO_CUOTA'],
        'NRO_TRABAJADORES_PERIODO' => $post['nro_trab_periodo'],
        'NOMBRE_OBRA' => $post['nom_obra'],
        'NRO_LICENCIA_CONTRATO' => $post['nro_lic_contrato'],
        'CIUDAD_OBRA' => $post['cui_obra'],
        'TIPO_FIC' => $post['tp_fic'],
        'COSTO_TOTAL_OBRA_TODO_COSTO' => $post['ctotd'],
        'COSTO_TOTAL_MANO_DE_OBRA' => $post['ctmo'],
        'REGIONAL_SIIF' => $post['reg_siif'],
        'CENTRO_SIIF' => $post['centro_siff'],
        'CODIGO_SIIF' => $post['cod_siif'],
        'RADICADO_ONBASE' => $post['rad_onbase'],
        'NRO_RESOLUCION_MULTA' => $post['nro_res_multa'],
        'TIPO_CARNE' => $post['tipo_carne'],
        'NRO_ORDEN_VIAJE' => $post['nov'],
        'RECLASIFICADO' => 1
    );
    /* if($bandera) {
      if ($bandera == true) {
      $data['COD_FISCALIZACION'] = $liquidacionReclasificada['COD_FISCALIZACION'];
      } else {
      if ($bandera == 3) {
      $data['COD_FISCALIZACION'] = '';
      }
      }
      } */

    $dateReclasificacion = array(
        'FECHA_PAGO' => $post['fechaPago_a'],
        'FECHA_RECLASIFICACION' => $post['fecha_aplicacion']
    );

    $dataReclasificacion = array(
        'COD_PAGO' => $post['codpago'],
        'NIT_EMPRESA' => $post['nit'],
        'TICKETID' => $post['ticket_id'],
        'VALOR_PAGADO' => $datos['VALOR_PAGADO'],
        'COD_CPTO_INICIAL' => $post['concepto_pago_r'],
        'CONCEPTO_INICIAL' => $post['cod_subconcepto_r'],
        'NUMERO_LIQUIDACION_ORIGEN' => $post['num_liquidacion_r'],
        'COD_CPTO_RECLASIFICADO' => $post['concepto'],
        'CONCEPTO_RECLASIFICADO' => $post['subconcepto'],
        'MTVO_RECLASIFICACION' => $post['comentarios'],
        'COD_REGIONAL' => $post['regional'],
        'ID_USU_RECLASIFICO' => $post['userr']
    );
    /* if($liquidacionReclasificada) {die
      $dataReclasificacion['NUMERO_LIQUIDACION_DESTIONO'] = $liquidacionReclasificada['NUM_LIQUIDACION'];
      } */
    $CI = get_instance();
    $CI->load->model('carteranomisional_model');
    $INSERT = $CI->carteranomisional_model->add('REP_PAGOS_RECLASIFICADOS', $dataReclasificacion, $dateReclasificacion, FALSE, 'yyyy-mm-dd');
    $result = $CI->carteranomisional_model->edit('PAGOSRECIBIDOS', $data, "COD_PAGO", $post['codpago'], $date, 'yyyy-mm-dd');
}

function actualizar_cuotas($cuotas_recalculadas) {
    if ($cuotas_recalculadas != NULL) {
        foreach ($cuotas_recalculadas['ACTUALIZAR'] as $key => $cuota_r) {
            if (array_key_exists('PAGO', $cuota_r)) {
                $cuotas_recalculadas['ACTUALIZAR'][$key]['CUOTA']['KACTUS'] = 2;
            }
        }
    }
    return $cuotas_recalculadas;
}

?>
