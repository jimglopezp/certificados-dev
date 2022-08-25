<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//CONVERTIR LA TASA EFECTIVA ANUAL  PARA INTERES SIMPLE MENSUAL
function convertirTasaSimple($tasa_interes)
/**
* Funcion que pasa la TEA a TNM
* Esta conversión no es correcta, pues no se convierte TEA a TNA, pero se ajusta a los documentos provistos por el SENA
*
* @param string $tasa_interes
* @return float $tasa_mensual
*/
{
	$tasa_interes = (float)$tasa_interes;
	$tasa_mensual = $tasa_interes/12;
	return $tasa_mensual;
}

//CONVERTIR LA TASA EFECTIVA ANUAL PARA INTERES SIMPLE DIARIO
function convertirTasaSimple_diaria($tasa_interes)
/**
* Funcion que pasa la TEA a TND expresada en porcentaje
* Esta conversión no es correcta, pues no se convierte TEA a TNA, pero se ajusta a los documentos provistos por el SENA
*
* @param string $tasa_interes
* @return float $tasa_mensual
*/
{

	// $tasa_interes = str_replace(".","",$tasa_interes); //borro los separadores de miles, si hay
	// $tasa_interes = str_replace(",",".",$tasa_interes); //convierto las comas en puntos
	$tasa_interes = (float)$tasa_interes;
	$tasa_mensual = $tasa_interes/365;
	return $tasa_mensual;
}

//CONVERTIR LA TASA EFECTIVA ANUAL A TASA NOMINAL MENSUAL
function convertirTasa($tasa_interes, $periodo)
/**
* Funcion que calcula la conversion de TEA a TNM expresada en porcentaje
*
* @param string $tasa_interes
* @param string $periodo
* @return float $tasa_mensual
*/
{
	$tasa_interes = (float)$tasa_interes;
	$tasa_mensual = (pow(1+($tasa_interes/100),$periodo/365)-1)*100;
	return $tasa_mensual;
}

//CONVERTIR LA TASA EFECTIVA ANUAL A TASA NOMINAL DIARIA
function convertirTasa_diaria($tasa_interes)
/**
   * Funcion que calcula la conversion de TEA a TND expresada en porcentaje
   *
   * @param string $tasa_interes
   * @return float $tasa_diaria
*/
{
	$tasa_interes = (float)$tasa_interes;
	$tasa_diaria = (pow(1+($tasa_interes/100),1/365)-1)*100;
	return $tasa_diaria;
}


//EVALUACIÓN AÑOS BISIESTOS
function esBisiesto($anno_parametro)
{
/**
   * Función que calcula si es un año bisiesto
   *
   * @param string $anno_parametro
   * @return boolean
*/
	//devuelve true si el año es múltiplo de 4 y es multiplo de 100 pero no de 400
	//false en caso contrario
	$anno = $anno_parametro;
	return ($anno % 4 == 0) && !($anno % 100 == 0 && $anno % 400 != 0);
}


//EVALUACIÓN DÍAS
function cuentaDias($mes_parametro,$anno_bisiesto)
{
/**
   * Función que calcula los días de un mes específico incluyendo si es un año bisiesto
   *
   * @param string $mes_parametro
   * @param integer $anno_bisiesto
   * @return boolean
*/
	$mes = $mes_parametro;
	//MESES DE 31 DIAS
	if($mes==1 || $mes==3 || $mes==5 || $mes==7 || $mes==8 || $mes==10 || $mes==12):
		return 31;
	//MESES DE 30 DIAS
	elseif($mes==4 || $mes==6 || $mes==9 || $mes==11):
		return 30;
	//FEBRERO EN AÑOS BISIESTOS
	elseif($mes==2 && $anno_bisiesto==1):
		return 29;
	//FEBRERO EN AÑOS NO BISIESTOS
	else:
		return 28;
	endif;
}

  function digitoverifica($nit, $str = 0) {
    $nit = trim($nit);
    $suma = 0;
    $len = strlen(trim($nit)) - 1;
    $factores = array(71, 67, 59, 53, 47, 43, 41, 37, 29, 23, 19, 17, 13, 7, 3);
    if ($len > 0 && $len < 16) {
      $pos = str_split(str_pad($nit, 16, "0", STR_PAD_LEFT));
      for ($x = 0; $x < 15; $x++) {
        $suma += ($pos[$x] * $factores[$x]);
      }
      $digtemp = ($suma % 11);
      if ($digtemp != 0 && $digtemp != 1) {
        $digito = trim(11 - $digtemp);
      } else {
        $digito = trim($digtemp);
      }
      if ($str == 0) {
        $dig = substr($nit, -1);
        if ($dig === $digito) {
          $ret = substr($nit, 0, $len);
          return $ret;
        } else {
          return $nit;
        }
      } else {
        return $digito;
      }
    } else {
      return false;
    }
  }

//CALCULAR INTERES COMPUESTO X DIAS
function calcularDiasCorrientes($dia_parametro, $valor_parametro,$interes_parametro)
{
	$deuda = $valor_parametro * $dia_parametro * $interes_parametro;
	return $deuda;
}

//CALCULAR INTERES COMPUESTO X MES :: Aportes
function calcularMesesCorrientes($fecha_liquidacion, $capital, $mes, $anno)
/**
 * Funcion que calcula los intereses corrientes generados por un mes específico hasta la fecha de la liquidación.
 * Solo funcional si se encuentra deuda en el mes inspeccionado
	*
 * @param string $fecha_liquidacion
 * @param float $capital
 * @param integer $mes
 * @param integer $anno
 * @return float $interes_compuesto
 */
{
	$fechas_liquidación = explode('/', $fecha_liquidacion);
	$dia_fecha_liquidacion = (int)$fechas_liquidación[0];
	$mes_fecha_liquidacion = (int)$fechas_liquidación[1];
	$anno_fecha_liquidacion = (int)$fechas_liquidación[2];
	$intereses_generados = 0;

	for($i = $anno; $i <= $anno_fecha_liquidacion; $i ++):

		if ($i == $anno && $anno != $anno_fecha_liquidacion):

			for ($j = $mes+1; $j <= 12; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes +1):

					$dias = cuentaDias($j,$anno_bisiesto) - 10;

				else:

					$dias = cuentaDias($j,$anno_bisiesto);

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados += $capital  *  $dias * ($tasa/100);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);
						$dias = 6;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados += $capital  *  $dias * ($tasa/100);

					else:

						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);

					endif;

				endif;

			endfor;

		elseif($i == $anno_fecha_liquidacion && $anno != $anno_fecha_liquidacion):

			for ($j = 1; $j <= $mes_fecha_liquidacion; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes_fecha_liquidacion):

					$dias = $dia_fecha_liquidacion;

				elseif($j == $mes):

					$dias = cuentaDias($j,$anno_bisiesto);

				else:

					$dias = cuentaDias($j,$anno_bisiesto);

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados += $capital  *  $dias * ($tasa/100);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);
						$dias = 6;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados += $capital  *  $dias * ($tasa/100);

					else:

						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);

					endif;


				endif;

			endfor;

		elseif($anno == $anno_fecha_liquidacion):

			for ($j = $mes+1; $j <= $mes_fecha_liquidacion; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes_fecha_liquidacion):

					$dias = $dia_fecha_liquidacion;

				elseif($j == $mes+1):

					$dias = cuentaDias($j,$anno_bisiesto) - 10;

				else:

					$dias = cuentaDias($j,$anno_bisiesto);

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados += $capital  *  $dias * ($tasa/100);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);
						$dias = 6;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados += $capital  *  $dias * ($tasa/100);

					else:

						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);

					endif;

				endif;

			endfor;

		else:
			for ($j = 1; $j <= 12; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				$dias = cuentaDias($j,$anno_bisiesto);
				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados += $capital  *  $dias * ($tasa/100);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);
						$dias = 6;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados += $capital  *  $dias * ($tasa/100);

					else:

						$acumuladoCapital = $capital + $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados += $acumuladoCapital  * ($tasa_mensual/100);

					endif;


				endif;

			endfor;

		endif;

	endfor;

	return $intereses_generados;
}

//DEBUGG PARA PRUEBAS EN APORTES
function calcularInteresesAportesDebug($fecha_liquidacion, $capital, $mes, $anno, $aporte)
/**
 * Funcion que calcula los intereses corrientes generados por un mes específico hasta la fecha de la liquidación.
 * Solo funcional si se encuentra deuda en el mes inspeccionado
	*
 * @param string $fecha_liquidacion
 * @param float $capital
 * @param integer $mes
 * @param integer $anno
 * @return float $interes_compuesto
 */
{
	$aporte_mes = (int)$aporte;
	$fechas_liquidación = explode('/', $fecha_liquidacion);
	$dia_fecha_liquidacion = (int)$fechas_liquidación[0];
	$mes_fecha_liquidacion = (int)$fechas_liquidación[1];
	$anno_fecha_liquidacion = (int)$fechas_liquidación[2];
	$intereses_generados = 0;
	$calculos  = array();
	$acumuladoCapital = $capital;
	$acumuladoDias = 0;

	for($i = $anno; $i <= $anno_fecha_liquidacion; $i ++):

		if ($i == $anno && $anno != $anno_fecha_liquidacion):

			for ($j = $mes+1; $j <= 12; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes+1):

					$dias = cuentaDias($j,$anno_bisiesto) - 10;
					$acumuladoDias += $dias;

				else:

                        $dias = cuentaDias($j,$anno_bisiesto);
					    $acumuladoDias += $dias;

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados = $capital  *  $dias * ($tasa/100);
					$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
					array_push($calculos, $linea);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoDias += $dias;
						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,   'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);
						$dias = 6;
						$acumuladoDias += $dias;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados = $capital  *  $dias * ($tasa/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					else:

						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					endif;

				endif;

			endfor;

		elseif($i == $anno_fecha_liquidacion && $anno != $anno_fecha_liquidacion):

			for ($j = 1; $j <= $mes_fecha_liquidacion; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes_fecha_liquidacion):

					$dias = $dia_fecha_liquidacion;
					$acumuladoDias += $dias;

				elseif($j == $mes):

					$dias = cuentaDias($j,$anno_bisiesto);
					$acumuladoDias += $dias;

				else:

					$dias = cuentaDias($j,$anno_bisiesto);
					$acumuladoDias += $dias;

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados = $capital  *  $dias * ($tasa/100);
					$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
					array_push($calculos, $linea);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoDias += $dias;
						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);
						$dias = 6;
						$acumuladoDias += $dias;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados = $capital  *  $dias * ($tasa/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					else:

						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					endif;

				endif;

			endfor;

		elseif($anno == $anno_fecha_liquidacion):

			for ($j = $mes+1; $j <= $mes_fecha_liquidacion; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j == $mes_fecha_liquidacion):

					$dias = $dia_fecha_liquidacion;
					$acumuladoDias += $dias;

				elseif($j == $mes+1):

					$dias = cuentaDias($j,$anno_bisiesto) - 10;
					$acumuladoDias += $dias;

				else:

					$dias = cuentaDias($j,$anno_bisiesto);
					$acumuladoDias += $dias;

				endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

					$tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados = $capital  *  $dias * ($tasa/100);
					$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
					array_push($calculos, $linea);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoDias += $dias;
						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);
						$dias = 6;
						$acumuladoDias += $dias;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados = $capital  *  $dias * ($tasa/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					else:

						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					endif;

				endif;

			endfor;

		else:
			for ($j = 1; $j <= 12; $j ++):

				if(esBisiesto($i)):

					$anno_bisiesto =  1;

				else:

					$anno_bisiesto =  0;

				endif;

				if($j != 12):

                //$dias = cuentaDias($j,$anno_bisiesto);
				//$acumuladoDias += $dias;

                endif;

				$tasa = getTasaSuper($j, $i);
				$tasa_real = $tasa;

				if ($i > 2012):

                    $dias = cuentaDias($j,$anno_bisiesto);
                    $acumuladoDias += $dias;
                    $tasa_mensual = convertirTasaSimple($tasa);
					$tasa = convertirTasaSimple_diaria($tasa);
					$intereses_generados = $capital  *  $dias * ($tasa/100);
					$linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
					array_push($calculos, $linea);

				else:

					if($i == 2012 && $j == 12):

						$dias = 25;
						$acumuladoDias += $dias;
						$acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa_real,$dias);
						$tasa = convertirTasa_diaria($tasa_real);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);
						$dias = 6;
						$acumuladoDias += $dias;
						$tasa_mensual = convertirTasaSimple($tasa_real);
						$tasa = convertirTasaSimple_diaria($tasa_real);
						$intereses_generados = $capital  *  $dias * ($tasa/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					else:

                        $dias = cuentaDias($j,$anno_bisiesto);
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
						$tasa_mensual = convertirTasa($tasa,$dias);
						$tasa = convertirTasa_diaria($tasa);
						$intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
						$linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real, 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
						array_push($calculos, $linea);

					endif;

				endif;

			endfor;

		endif;

	endfor;

	// $linea = array('total_dias' => $acumuladoDias);
	// array_push($calculos, $linea);

	return $calculos;
}

//DEBUGG PARA PRUEBAS EN APORTES
function calcularInteresesFicDebug($fecha_liquidacion, $capital, $mes, $anno, $aporte)
    /**
     * Funcion que calcula los intereses corrientes generados por un mes específico hasta la fecha de la liquidación.
     * Solo funcional si se encuentra deuda en el mes inspeccionado
     *
     * @param string $fecha_liquidacion
     * @param float $capital
     * @param integer $mes
     * @param integer $anno
     * @return float $interes_compuesto
     */
{
    $aporte_mes = (int)$aporte;
    $fechas_liquidación = explode('/', $fecha_liquidacion);
    $dia_fecha_liquidacion = (int)$fechas_liquidación[0];
    $mes_fecha_liquidacion = (int)$fechas_liquidación[1];
    $anno_fecha_liquidacion = (int)$fechas_liquidación[2];
    $intereses_generados = 0;
    $calculos  = array();
    $acumuladoCapital = $capital;
    $acumuladoDias = 0;

    for($i = $anno; $i <= $anno_fecha_liquidacion; $i ++):

        if ($i == $anno && $anno != $anno_fecha_liquidacion):

            for ($j = $mes+1; $j <= 12; $j ++):

                if(esBisiesto($i)):

                    $anno_bisiesto =  1;

                else:

                    $anno_bisiesto =  0;

                endif;

                if($j == $mes+1):

                    $dias = cuentaDias($j,$anno_bisiesto) - 10;
                    $acumuladoDias += $dias;

                else:

                    if($j == 12 && $i == 2012):

                    else:

                        $dias = cuentaDias($j,$anno_bisiesto);
                        $acumuladoDias += $dias;

                    endif;

                endif;

                if ($i > 2012):

                    $tasa = getTasaSuper($j, $i);
                    $tasa_real = $tasa;
                    $tasa_mensual = convertirTasaSimple($tasa);
                    $tasa = convertirTasaSimple_diaria($tasa);
                    $intereses_generados = $capital  *  $dias * ($tasa/100);
                    $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real .'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                    array_push($calculos, $linea);

                else:

                    if($i == 2012 && $j == 12):

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $dias = 25;
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa_real,$dias);
                        $tasa = convertirTasa_diaria($tasa_real);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,   'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);
                        $tasa = getTasaSuper($j, $i);
                        $tasa_real = $tasa;
                        $dias = 6;
                        $acumuladoDias += $dias;
                        $tasa_mensual = convertirTasaSimple($tasa_real);
                        $tasa = convertirTasaSimple_diaria($tasa_real);
                        $intereses_generados = $capital  *  $dias * ($tasa/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    else:

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa,$dias);
                        $tasa = convertirTasa_diaria($tasa);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    endif;

                endif;

            endfor;

        elseif($i == $anno_fecha_liquidacion && $anno != $anno_fecha_liquidacion):

            for ($j = 1; $j <= $mes_fecha_liquidacion; $j ++):

                if(esBisiesto($i)):

                    $anno_bisiesto =  1;

                else:

                    $anno_bisiesto =  0;

                endif;

                if($j == $mes_fecha_liquidacion):

                    $dias = $dia_fecha_liquidacion;
                    $acumuladoDias += $dias;

                elseif($j == $mes):

                    $dias = cuentaDias($j,$anno_bisiesto);
                    $acumuladoDias += $dias;

                else:

                    $dias = cuentaDias($j,$anno_bisiesto);
                    $acumuladoDias += $dias;

                endif;

                if ($i > 2012):

                    $tasa = getTasaSuper($j, $i);
                    $tasa_real = $tasa;
                    $tasa_mensual = convertirTasaSimple($tasa);
                    $tasa = convertirTasaSimple_diaria($tasa);
                    $intereses_generados = $capital  *  $dias * ($tasa/100);
                    $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                    array_push($calculos, $linea);

                else:


                    if($i == 2012 && $j == 12):

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $dias = 25;
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa_real,$dias);
                        $tasa = convertirTasa_diaria($tasa_real);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);
                        $tasa = getTasaSuper($j, $i);
                        $tasa_real = $tasa;
                        $dias = 6;
                        $acumuladoDias += $dias;
                        $tasa_mensual = convertirTasaSimple($tasa_real);
                        $tasa = convertirTasaSimple_diaria($tasa_real);
                        $intereses_generados = $capital  *  $dias * ($tasa/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    else:

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa,$dias);
                        $tasa = convertirTasa_diaria($tasa);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,    'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    endif;

                endif;

            endfor;

        elseif($anno == $anno_fecha_liquidacion):

            for ($j = $mes+1; $j <= $mes_fecha_liquidacion; $j ++):

                if(esBisiesto($i)):

                    $anno_bisiesto =  1;

                else:

                    $anno_bisiesto =  0;

                endif;

                if($j == $mes_fecha_liquidacion):

                    $dias = $dia_fecha_liquidacion;
                    $acumuladoDias += $dias;

                elseif($j == $mes+1):

                    $dias = cuentaDias($j,$anno_bisiesto) - 10;
                    $acumuladoDias += $dias;

                else:

                    $dias = cuentaDias($j,$anno_bisiesto);
                    $acumuladoDias += $dias;

                endif;

                if ($i > 2012):

                    $tasa = getTasaSuper($j, $i);
                    $tasa_real = $tasa;
                    $tasa_mensual = convertirTasaSimple($tasa);
                    $tasa = convertirTasaSimple_diaria($tasa);
                    $intereses_generados = $capital  *  $dias * ($tasa/100);
                    $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes,  'total_dias' => $acumuladoDias);
                    array_push($calculos, $linea);

                else:

                    if($i == 2012 && $j == 12):

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $dias = 25;
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa_real,$dias);
                        $tasa = convertirTasa_diaria($tasa_real);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);
                        $tasa = getTasaSuper($j, $i);
                        $tasa_real = $tasa;
                        $dias = 6;
                        $acumuladoDias += $dias;
                        $tasa_mensual = convertirTasaSimple($tasa_real);
                        $tasa = convertirTasaSimple_diaria($tasa_real);
                        $intereses_generados = $capital  *  $dias * ($tasa/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    else:

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa,$dias);
                        $tasa = convertirTasa_diaria($tasa);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    endif;

                endif;

            endfor;

        else:
            for ($j = 1; $j <= 12; $j ++):

                if(esBisiesto($i)):

                    $anno_bisiesto =  1;

                else:

                    $anno_bisiesto =  0;

                endif;

                if ($i > 2012):

                    $tasa = getTasaSuper($j, $i);
                    $tasa_real = $tasa;
                    $dias = cuentaDias($j,$anno_bisiesto);
                    $acumuladoDias += $dias;
                    $tasa_mensual = convertirTasaSimple($tasa);
                    $tasa = convertirTasaSimple_diaria($tasa);
                    $intereses_generados = $capital  *  $dias * ($tasa/100);
                    $linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                    array_push($calculos, $linea);

                else:

                    if($i == 2012 && $j == 12):

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $dias = 25;
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa_real,$dias);
                        $tasa = convertirTasa_diaria($tasa_real);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);
                        $tasa = getTasaSuper($j, $i);
                        $tasa_real = $tasa;
                        $dias = 6;
                        $acumuladoDias += $dias;
                        $tasa_mensual = convertirTasaSimple($tasa_real);
                        $tasa = convertirTasaSimple_diaria($tasa_real);
                        $intereses_generados = $capital  *  $dias * ($tasa/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes,  'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $capital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    else:

                        $tasa = 12;
                        $tasa_real = $tasa;
                        $dias = cuentaDias($j,$anno_bisiesto);
                        $acumuladoDias += $dias;
                        $acumuladoCapital += $intereses_generados;
                        $tasa_mensual = convertirTasa($tasa,$dias);
                        $tasa = convertirTasa_diaria($tasa);
                        $intereses_generados = $acumuladoCapital * ($tasa_mensual/100);
                        $linea = array( 'anno' => $anno, 'mes' => $mes, 'anno_interes' => $i,  'mes_interes' =>  $j, 'dias' => $dias, 'capital' => $acumuladoCapital,  'tasa_EA' => $tasa_real.'%', 'tasa_mensual' => $tasa_mensual, 'tasa_diaria' => $tasa,  'intereses' => $intereses_generados, 'aportes_mes' => $aporte_mes, 'total_dias' => $acumuladoDias);
                        array_push($calculos, $linea);

                    endif;

                endif;

            endfor;

        endif;

    endfor;

    // $linea = array('total_dias' => $acumuladoDias);
    // array_push($calculos, $linea);

    return $calculos;
}

//CALCULAR INTERES SIMPLE x DÍAS :: Multas
function calcularDias($mes_parametro, $anno_parametro, $dia_parametro, $valor_parametro, $interes_parametro)
/**
 * Funcion que calcula los intereses simples generados por un mes específico.
 * Solo funcional si se encuentra liquidacndo días de un mes específico
	*
 * @param string $mes_parametro
 * @param string $anno_parametro
 * @param string $dia_parametro
 * @param string $valor_parametro
 * @param string $interes_parametro
 * @return array $data
 */
{
	//SE CAPTURAN PARAMETROS
	$dia = $dia_parametro;
 	$mes = $mes_parametro;
	$anno = $anno_parametro;
	$valor = $valor_parametro;

	//DETERMINA SI EL AÑO ES BISIESTO
	if(EsBisiesto($anno)):
		$anno_bisiesto = 1;
	else:
		$anno_bisiesto = 0;
	endif;

	//CALCULA LOS DIAS DEL MES SELECCIONADO
	$dia_interes = cuentaDias($mes,$anno_bisiesto);

	//CALCULA EL INTERES DIARIO DEL MES SELECCIONADO
	$interes_diario = $interes_parametro;
	$intereses_cuota = $dia * ($valor * ($interes_diario/100));
	$total_cuota = $valor + $intereses_cuota;

	//FORMATO DE PESOS
	$c_valor = $valor;
	$c_intereses_cuota = $intereses_cuota;
	$c_total_cuota = $total_cuota;

	$valor = "$".number_format($valor, 0, '.', '.');
	$intereses_cuota = "$".number_format($intereses_cuota, 0, '.', '.');
	$total_cuota = "$".number_format($total_cuota, 0, '.', '.');

	//GENERACIÓN DEL ARRAY
	$data = array('anno' => $anno, 'mes' => $mes, 'diaMora' => $dia, 'capital' => $valor, 'intereses' => $intereses_cuota, 'total' => $total_cuota, 'c_valor' => $c_valor, 'c_intereses_cuota' => $c_intereses_cuota, 'c_total_cuota' => $c_total_cuota);
	return $data;
}


//CALCULAR INTERES SIMPLE x MESES :: Multas
function calcularMeses($mes_parametro, $anno_parametro, $valor_parametro, $interes_parametro)
/**
 * Funcion que calcula los intereses simples generados por un mes específico.
 * Solo funcional si se encuentra calculando un mes específico
	*
 * @param string $mes_parametro
 * @param string $anno_parametro
 * @param string $dia_parametro
 * @param string $valor_parametro
 * @param string $interes_parametro
 * @return array $data
 */
{
	//CAPTURA DE PARAMETROS
	$mes = $mes_parametro;
	$anno = $anno_parametro;
	$valor = $valor_parametro;
	$interes = $interes_parametro;

	//DETERMINA SI EL AÑO ES BISIESTO
	if(EsBisiesto($anno)):
		$anno_bisiesto = 1;
	else:
		$anno_bisiesto = 0;
	endif;

	//CALCULA LOS DIAS DEL MES SELECCIONADO
	$dia = cuentaDias($mes,$anno_bisiesto);

	//CALCULA EL INTERES DIARIO DEL MES SELECCIONADO
	$interes_diario = $interes_parametro;

	//CALCULA EL INTERES DEL MES SELECCIONADO
	$intereses_cuota = $dia * ($valor * ($interes_diario/100));
	$total_cuota = $valor + $intereses_cuota;

	//FORMATO DE PESOS
	$c_valor = $valor;
	$c_intereses_cuota = $intereses_cuota;
	$c_total_cuota = $total_cuota;

	$valor = "$".number_format($valor, 0, '.', '.');
	$intereses_cuota = "$".number_format($intereses_cuota, 0, '.', '.');
	$total_cuota = "$".number_format($total_cuota, 0, '.', '.');
	$dia = 30;

	//GENERACIÓN DEL ARRAY
	$data = array('anno' => $anno, 'mes' => $mes, 'diaMora' => $dia, 'capital' => $valor, 'intereses' => $intereses_cuota, 'total' => $total_cuota, 'c_valor' => $c_valor, 'c_intereses_cuota' => $c_intereses_cuota, 'c_total_cuota' => $c_total_cuota);
	return $data;
}


//RETORNA EL TRIMESTRE DEL MES SELECCIONADO
function getTrimestre($mes)
/**
 * Función que apoya la consulta de la tasa de interes de la superfinanciera por mes
 * Es necesario consultar el trimestre para evaluar que tasa aplicar, retorna un entero que representa el  primer mes del trimestre
	*
 * @param integer $mes
 * @return integer $trimestre
 */
{
	if ($mes >= 1 && $mes <=3):
		return $trimestre = 1;
	elseif ($mes >= 4 && $mes <= 6):
		return $trimestre = 4;
	elseif ($mes >= 7 && $mes <=9):
		return $trimestre = 7;
	else:
		return $trimestre = 10;
	endif;
}

//RETORNA LA TASA DIARIA DE LA SUPERINTENDENCIA
function getTasaSuper($mes, $anno)
/**
 * Función que hace consulta del modelo para traer la tasa de la superfinanciera aplicada el mes seleccionado
 * Retorna la tasa de la superfinanciera expresada en tasas efectivas anuales como string
	*
 * @param integer $mes
 * @param integer $anno
 * @return string $tasa
 */
{
	$CI =& get_instance();
	$CI -> load -> model('liquidaciones_model');
	$trimestre = getTrimestre($mes);
	$tasa_mes = $CI -> liquidaciones_model -> getTasaInteresSF_mes($trimestre,$anno);
	$tasa = $tasa_mes['TASA_SUPERINTENDENCIA'];
	return $tasa;
}

//RETORNA LA FECHA ACTUAL EN EL FORMATO SOLICITADO EN LOS CAMPOS DE FECHA
function getFechaActual()
/**
 * Función que devuelve la fecha actual para los selectores de fecha con calendario
 * EL formato es el solicitado por SENA en la documentación
 * @return string $fecha_actual
 */
{
	$datestring = "%d/%m/%Y";
	$fecha_actual = mdate($datestring);
	return $fecha_actual;
}



function limpiar_cadena($string)
/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @param string $string  cadena a limpiar
 * @return string $string cadena limpia
 */
{
	$string = trim($string);

	$string = str_replace(array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string);
	$string = str_replace(array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),$string);
	$string = str_replace(array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),$string);
	$string = str_replace(array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),$string);
	$string = str_replace(array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),$string);
	$string = str_replace(array('ñ', 'Ñ', 'ç', 'Ç'),array('n', 'N', 'c', 'C',),$string);
	//Esta parte se encarga de eliminar cualquier caracter extraño
	$string = str_replace(array("\\", "¨", "º", "-", "~","#", "@", "|", "!", "\"","·", "$", "%", "&", "/","(", ")", "?", "'", "¡","¿", "[", "^", "`", "]","+", "}", "{", "¨", "´",">", "< ", ";", ",", ":","."),'',$string);
	return $string;
}

//RETORNA EL CALCULO DE LA LIQUIDACIÓN A LA FECHA ACTUAL - PROVISTA PARA ACUERDO DE PAGO
function recalcularLiquidacion_acuerdoPago($liquidacion)
/**
   * Función que hace consulta del modelo para traer la información de la liquidación consultada y recalcula capital e interes a la fecha actual
   * Retorna el valor del capital e interes recalculado a la fecha pero no almacena los datos en la DB
   *
   * @param string $liquidacion
   * @return array $liquidacion
*/
{
	$CI =& get_instance();
	$CI -> load -> model('liquidaciones_model');
	$data = (string)$liquidacion;
	$liquidacion = $CI -> liquidaciones_model -> consultarLiquidacion_acuerdoPago($data);

	if($liquidacion === FALSE):

		return  $mensajeError = "No existen datos asociado a la liquidación N° ".$data;

	else:

		$fecha_liquidacion = $liquidacion['FECHA_LIQUIDACION'];
		$fechas_liquidación = explode('/', $fecha_liquidacion);
		$dia_fecha_liquidacion = (int)$fechas_liquidación[0];
		$mes_fecha_liquidacion = (int)$fechas_liquidación[1];
		$anno_fecha_liquidacion = (int)$fechas_liquidación[2];
		$dia_fecha_acuerdo = date('j');
		$mes_fecha_acuerdo = date('n');
		$anno_fecha_acuerdo = date('Y');
		$concepto = (int)$liquidacion['COD_CONCEPTO'];
		$total_capital = (int)$liquidacion['TOTAL_LIQUIDADO'];
		$saldo_deuda = (int)$liquidacion['SALDO_DEUDA'];
		$capital = (int)$liquidacion['SALDO_CAPITAL'];
		$intereses = (int)$liquidacion['SALDO_INTERES'];
		$meses = array();
		$cuenta_dias = 0;
		$acumuladoIntereses = 0;
		$tasa_interes_multas = 12;

		switch($concepto):
			case 1:
				$nuevo_capital = 0;
				$nuevo_intereses = 0;

				for($j = $anno_fecha_liquidacion; $j <= $anno_fecha_acuerdo; $j++):

					$anno_bisiesto = esBisiesto($j);

					if($anno_fecha_liquidacion == $anno_fecha_acuerdo):

						if($mes_fecha_liquidacion == $mes_fecha_acuerdo):

							$diferencia_dias = $dia_fecha_acuerdo - $dia_fecha_liquidacion;
							$cuenta_dias += $diferencia_dias;
							$tasa = getTasaSuper($mes_fecha_acuerdo, $j);
							$interes_diario = convertirTasaSimple_diaria($tasa);
							$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
							$acumuladoIntereses += $nuevo_intereses;
							$mes = array('anno' => $j, 'mes' => $mes_fecha_liquidacion, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
							array_push($meses, $mes);

						else:

							for($i = $mes_fecha_liquidacion; $i <= $mes_fecha_acuerdo; $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias - $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								elseif ($i  == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								endif;

							endfor;

						endif;

					else:

						if($j == $anno_fecha_liquidacion):

							for($i = $mes_fecha_liquidacion;  $i <= 12;  $i++):

								if($i == $mes_fecha_liquidacion):

									if($j > 2012):

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias  - $dia_fecha_liquidacion;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$acumuladoIntereses += $nuevo_intereses;
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
										array_push($meses, $mes);

									else:

										if($j == 2012 && $i == 12):

											if($dia_fecha_liquidacion > 25):

												$diferencia_dias = 31 - $dia_fecha_liquidacion;
												$cuenta_dias += $diferencia_dias;
												$tasa = getTasaSuper($i, $j);
												$interes_diario = convertirTasaSimple_diaria($tasa);
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$acumuladoIntereses += $nuevo_intereses;
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
												if($diferencia_dias != 0):

													array_push($meses, $mes);

												endif;


											else:

												$diferencia_dias = 25 - $dia_fecha_liquidacion;
												$cuenta_dias += $diferencia_dias;
												$tasa = getTasaSuper($i, $j);
												$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$acumuladoIntereses += $nuevo_intereses;
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);

												if($diferencia_dias != 0):

													array_push($meses, $mes);

												endif;

												$dias = 6;
												$diferencia_dias = $dias;
												$cuenta_dias += $diferencia_dias;
												$tasa = getTasaSuper($i, $j);
												$interes_diario = convertirTasaSimple_diaria($tasa);
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$acumuladoIntereses += $nuevo_intereses;
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
												array_push($meses, $mes);

											endif;


										else:

											$dias = cuentaDias($i,$anno_bisiesto);
											$diferencia_dias = $dias  - $dia_fecha_liquidacion;
											$cuenta_dias += $diferencia_dias;
											$tasa = getTasaSuper($i, $j);
											$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$acumuladoIntereses += $nuevo_intereses;
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
											array_push($meses, $mes);

										endif;

									endif;

								else:

									if($j > 2012):

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$acumuladoIntereses += $nuevo_intereses;
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
										array_push($meses, $mes);

									else:

										if($j == 2012 && $i == 12):

											$dias = 25;
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$tasa = getTasaSuper($i, $j);
											$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$acumuladoIntereses += $nuevo_intereses;
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
											array_push($meses, $mes);
											$dias = 6;
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$tasa = getTasaSuper($i, $j);
											$interes_diario = convertirTasaSimple_diaria($tasa);
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$acumuladoIntereses += $nuevo_intereses;
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
											array_push($meses, $mes);

										else:

											$dias = cuentaDias($i,$anno_bisiesto);
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$tasa = getTasaSuper($i, $j);
											$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$acumuladoIntereses += $nuevo_intereses;
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
											array_push($meses, $mes);

										endif;

									endif;

								endif;

							endfor;

						elseif($j == $anno_fecha_acuerdo):

							for($i = 1;  $i <= $mes_fecha_acuerdo;  $i++):

								if($i == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								endif;

							endfor;

						else:

							for($i = 1;  $i <= 12;  $i++):

								if($j > 2012):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								else:

									if($j == 2012 && $i == 12):

										$dias = 25;
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$acumuladoIntereses += $nuevo_intereses;
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
										array_push($meses, $mes);
										$dias = 6;
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$acumuladoIntereses += $nuevo_intereses;
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
										array_push($meses, $mes);

									else:

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = (convertirTasa($tasa, $diferencia_dias))/$diferencia_dias;
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$acumuladoIntereses += $nuevo_intereses;
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
										array_push($meses, $mes);

									endif;

								endif;

							endfor;

						endif;

					endif;

				endfor;
				$acumuladoIntereses += $intereses;
				return $liquidacion_recalculo = array('capital' => $capital, 'intereses' => $acumuladoIntereses, 'meses' => $meses, 'dias' => $cuenta_dias);
				break;

			case 2:
				$interes_diario = convertirTasaSimple_diaria($tasa_interes_multas);
				$nuevo_capital = 0;
				$nuevo_intereses = 0;

				for($j = $anno_fecha_liquidacion; $j <= $anno_fecha_acuerdo; $j++):

					$anno_bisiesto = esBisiesto($j);

					if($anno_fecha_liquidacion == $anno_fecha_acuerdo):

						if($mes_fecha_liquidacion == $mes_fecha_acuerdo):

							$diferencia_dias = $dia_fecha_acuerdo - $dia_fecha_liquidacion;
							$cuenta_dias += $diferencia_dias;
							$tasa = getTasaSuper($mes_fecha_acuerdo, $j);
							$interes_diario = convertirTasaSimple_diaria($tasa);
							$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
							$mes = array('anno' => $j, 'mes' => $mes_fecha_liquidacion, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
							array_push($meses, $mes);

						else:

							for($i = $mes_fecha_liquidacion; $i <= $mes_fecha_acuerdo; $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias - $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								elseif ($i  == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								endif;

							endfor;

						endif;

					else:

						if($j == $anno_fecha_liquidacion):

							for($i = $mes_fecha_liquidacion;  $i <= 12;  $i++):

								if($i == $mes_fecha_liquidacion):

									if($j > 2012):

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
										array_push($meses, $mes);

									else:

										if($j == 2012 && $i == 12):

											if($dia_fecha_liquidacion > 25):

												$diferencia_dias = 31 - $dia_fecha_liquidacion;
												$cuenta_dias += $diferencia_dias;
												$tasa = getTasaSuper($i, $j);
												$interes_diario = convertirTasaSimple_diaria($tasa);
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
												if($diferencia_dias != 0):

													array_push($meses, $mes);

												endif;


											else:

												$diferencia_dias = 25 - $dia_fecha_liquidacion;
												$cuenta_dias += $diferencia_dias;
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);

												if($diferencia_dias != 0):

													array_push($meses, $mes);

												endif;

												$dias = 6;
												$diferencia_dias = $dias;
												$cuenta_dias += $diferencia_dias;
												$tasa = getTasaSuper($i, $j);
												$interes_diario = convertirTasaSimple_diaria($tasa);
												$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
												$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
												array_push($meses, $mes);

											endif;


										else:

											$dias = cuentaDias($i,$anno_bisiesto);
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
											array_push($meses, $mes);

										endif;

									endif;

								else:

									if($j > 2012):

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
										array_push($meses, $mes);

									else:

										if($j == 2012 && $i == 12):

											$dias = 25;
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
											array_push($meses, $mes);
											$dias = 6;
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$tasa = getTasaSuper($i, $j);
											$interes_diario = convertirTasaSimple_diaria($tasa);
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
											array_push($meses, $mes);

										else:

											$dias = cuentaDias($i,$anno_bisiesto);
											$diferencia_dias = $dias;
											$cuenta_dias += $diferencia_dias;
											$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
											$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
											array_push($meses, $mes);

										endif;

									endif;

								endif;

							endfor;

						elseif($j == $anno_fecha_acuerdo):

							for($i = 1;  $i <= $mes_fecha_acuerdo;  $i++):

								if($i == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						else:

							for($i = 1;  $i <= 12;  $i++):

								if($j > 2012):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$tasa = getTasaSuper($i, $j);
									$interes_diario = convertirTasaSimple_diaria($tasa);
									$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									if($j == 2012 && $i == 12):

										$dias = 25;
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
										array_push($meses, $mes);
										$dias = 6;
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$tasa = getTasaSuper($i, $j);
										$interes_diario = convertirTasaSimple_diaria($tasa);
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
										array_push($meses, $mes);

									else:

										$dias = cuentaDias($i,$anno_bisiesto);
										$diferencia_dias = $dias;
										$cuenta_dias += $diferencia_dias;
										$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
										$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
										array_push($meses, $mes);

									endif;

								endif;

							endfor;

						endif;

					endif;

				endfor;
				$acumuladoIntereses += $intereses;
				return $liquidacion_recalculo = array('capital' => $capital, 'intereses' => $acumuladoIntereses, 'meses' => $meses, 'dias' => $cuenta_dias);
				break;

			case 3:
				$interes_diario = convertirTasa_diaria($tasa_interes_multas);
				$nuevo_capital = 0;
				$nuevo_intereses = 0;

				for($j = $anno_fecha_liquidacion; $j <= $anno_fecha_acuerdo; $j++):

					$anno_bisiesto = esBisiesto($j);

					if($anno_fecha_liquidacion == $anno_fecha_acuerdo):

						if($mes_fecha_liquidacion == $mes_fecha_acuerdo):

							$diferencia_dias = $dia_fecha_acuerdo - $dia_fecha_liquidacion;
							$cuenta_dias += $diferencia_dias;
							$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
							$mes = array('anno' => $j, 'mes' => $mes_fecha_liquidacion, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
							array_push($meses, $mes);

						else:

							for($i = $mes_fecha_liquidacion; $i <= $mes_fecha_acuerdo; $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias -  $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
                                    //$tasa = getTasaSuper($i, $j);
                                    $interes_diario = convertirTasaSimple_diaria($tasa_interes_multas);
                                    $nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
                                    $mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								elseif ($i  == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
                                    //$tasa = getTasaSuper($i, $j);
                                    $interes_diario = convertirTasaSimple_diaria($tasa_interes_multas);
                                    $nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
                                    $mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
                                    //$tasa = getTasaSuper($i, $j);
                                    $interes_diario = convertirTasaSimple_diaria($tasa_interes_multas);
                                    $nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
                                    $acumuladoIntereses += $nuevo_intereses;
                                    $mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $acumuladoIntereses);
									array_push($meses, $mes);

								endif;

							endfor;

						endif;

					else:

						if($j == $anno_fecha_liquidacion):

							for($i = $mes_fecha_liquidacion;  $i <= 12;  $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias -  $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						elseif($j == $anno_fecha_acuerdo):

							for($i = 1;  $i <= $mes_fecha_acuerdo;  $i++):

								if($i == $mes_fecha_acuerdo):

									// $dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						else:

							for($i = 1;  $i <= 12;  $i++):

								$dias = cuentaDias($i,$anno_bisiesto);
								$diferencia_dias = $dias;
								$cuenta_dias += $diferencia_dias;
								$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
								$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
								$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
								array_push($meses, $mes);

							endfor;

						endif;

					endif;

				endfor;
				$acumuladoIntereses += $intereses;
				return $liquidacion_recalculo = array('capital' => $capital, 'intereses' => $acumuladoIntereses, 'meses' => $meses, 'dias' => $cuenta_dias);
				break;

			case 5:
				$interes_diario = convertirTasaSimple_diaria($tasa_interes_multas);
				$nuevo_capital = 0;
				$nuevo_intereses = 0;

				for($j = $anno_fecha_liquidacion; $j <= $anno_fecha_acuerdo; $j++):

					$anno_bisiesto = esBisiesto($j);

					if($anno_fecha_liquidacion == $anno_fecha_acuerdo):

						if($mes_fecha_liquidacion == $mes_fecha_acuerdo):

							$diferencia_dias = $dia_fecha_acuerdo - $dia_fecha_liquidacion;
							$cuenta_dias += $diferencia_dias;
							$nuevo_intereses = $capital * ($diferencia_dias * ($interes_diario/100));
							$mes = array('anno' => $j, 'mes' => $mes_fecha_liquidacion, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $nuevo_intereses, 'total' => $nuevo_intereses);
							array_push($meses, $mes);

						else:

							for($i = $mes_fecha_liquidacion; $i <= $mes_fecha_acuerdo; $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias -  $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								elseif ($i  == $mes_fecha_acuerdo):

									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						endif;

					else:

						if($j == $anno_fecha_liquidacion):

							for($i = $mes_fecha_liquidacion;  $i <= 12;  $i++):

								if($i == $mes_fecha_liquidacion):

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias -  $dia_fecha_liquidacion;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						elseif($j == $anno_fecha_acuerdo):

							for($i = 1;  $i <= $mes_fecha_acuerdo;  $i++):

								if($i == $mes_fecha_acuerdo):

									// $dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dia_fecha_acuerdo;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								else:

									$dias = cuentaDias($i,$anno_bisiesto);
									$diferencia_dias = $dias;
									$cuenta_dias += $diferencia_dias;
									$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
									$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
									$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
									array_push($meses, $mes);

								endif;

							endfor;

						else:

							for($i = 1;  $i <= 12;  $i++):

								$dias = cuentaDias($i,$anno_bisiesto);
								$diferencia_dias = $dias;
								$cuenta_dias += $diferencia_dias;
								$nuevo_intereses += $capital * ($diferencia_dias * ($interes_diario/100));
								$intereses_mes = $capital * ($diferencia_dias * ($interes_diario/100));
								$mes = array('anno' => $j, 'mes' => $i, 'dias' => $diferencia_dias, 'capital' => $capital, 'interes' => $intereses, 'tasa' => $tasa_interes_multas.'%', 'interes_generado' => $intereses_mes, 'total' => $nuevo_intereses);
								array_push($meses, $mes);

							endfor;

						endif;

					endif;

				endfor;
				$nuevo_intereses += $intereses;
				return $liquidacion_recalculo = array('capital' => $capital, 'intereses' => $nuevo_intereses, 'meses' => $meses, 'dias' => $cuenta_dias);
				break;

			default:
				return  $mensajeError = "No existen métodos asociado al concepto N° ".$concepto;
				break;

		endswitch;

	endif;
}

/* End of file liquidaciones_helper.php*/
