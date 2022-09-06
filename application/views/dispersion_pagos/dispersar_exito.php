<?php
/**
 * Formulario para la busqueda de pagos FIC por Nit y/o Tickets
 *
 * @package          Cartera
 * @subpackage       Views
 * @author           jdussan
 * @location         application/views/dispersion_pago/
 * @last-modified    23/06/2014
 * @copyright
*/
if( ! defined('BASEPATH') ) exit('No direct script access allowed'); 
?>

<br><br>
<h1>Dispersar Pagos</h1>
<br><br>
<div class="center-form-xlarge" id="formulario-busqueda">
    <?php
    if (isset($message)):
        echo $message;
    endif;
    ?>
<h2><strong> <i class="fa fa-circle" aria-hidden="true"></i> Listado de pagos creados tras dipersión con éxito </h2>
    <div class="p-3 col-sm-12 bg-light text-center">
        <div class="row">
        <br>
        <div class="col-md-12 col-sm-12">
        <table class="table table-striped table-hover" width="100%">
            <thead>
                <tr>
                    <th>Ticket pago</th>
                    <th>Nombre constructor</th>
                    <th>Valor pago</th>
                    <th>Fecha pago</th>
                    <th>Período pago</th>
                    <th>Ciudad obra</th>
                    <th>Nombre obra</th>
                    <th>Número obra</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>61579818</td>
            <td>COVI CONSTRUCCIONES SAS</td>
            <td>123.000</td>
            <td>2021-08-20 12:11:40</td>
            <td>JUL-2021</td>
            <td>FLORENCIA</td>
            <td>PRADOS DEL NORTE VILLA DANIELA</td>
            <td>426</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>ACOXX DE COLOMBIA SAS</td>
            <td>277.450</td>
            <td>2021-07-13 14:20:00</td>
            <td>JUN-2021</td>
            <td>PAIME</td>
            <td>EDIFICIO CALLE 100 X 11B</td>
            <td>051</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>JCP CONSTRUCCIONES S.A.S</td>
            <td>221.850</td>
            <td>2021-06-08 09:00:00</td>
            <td>MAY-2021</td>
            <td>CALI</td>
            <td>OPTIMIZACION BOCATOMAS SANTAGE</td>
            <td>003</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>C D Y L LLTDA</td>
            <td>220.850</td>
            <td>2021-05-01 11:11:11</td>
            <td>ABR-2021</td>
            <td>BOGOTA</td>
            <td>MANO DE OBRA PARA LA CONSTRUCC</td>
            <td>236</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>MONTAJES Y SERVICIOS SOLDILEC S.A.S</td>
            <td>54.500</td>
            <td>2021-04-20 12:11:40</td>
            <td>MAR-2021</td>
            <td>GUADALUPE</td>
            <td>PROYECTO VILLA MANUELA 1 Y 2</td>
            <td>25752017</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>LEDING S.A.S</td>
            <td>117.200</td>
            <td>2021-03-12 12:10:00</td>
            <td>FEB-2021</td>
            <td>CIMITARRA</td>
            <td>MANTENIMIENTO Y REAPRACION MIO</td>
            <td>011197</td>
        </tr>
        <tr>
            <td>61579818</td>
            <td>ISE INGENIERIA SAS</td>
            <td>136.750</td>
            <td>2021-02-06 10:00:10</td>
            <td>ENE-2021</td>
            <td>MOSQUERA</td>
            <td>Construcción A</td>
            <td>4560012</td>
        </tr>
        </tbody>
    </table>
            <p class="text-center">
                <a class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Dispersar pago" href="#"> <i class="fa fa-file-excel-o"></i> Descargar Informe</a>
            </p>
            <br>
        </div>
        </div>
    </div>
<h2><?php if(isset($mensaje)) echo $mensaje; ?></h2>
    <?php echo validation_errors();?><!--mostrar los errores de validación-->
 </div>
