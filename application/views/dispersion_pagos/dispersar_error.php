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
<h2><strong> <i class="fa fa-circle" aria-hidden="true"></i> Listado de errores tras intentar la dispersión </h2>
    <div class="p-3 col-sm-12 bg-light text-center">
        <div class="row">
        <br>
        <div class="col-md-12 col-sm-12">
            <table class="table table-striped table-hover" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Línea</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>Fecha no corresponde</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>3</td>
                        <td>Pago no encontrado</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>7</td>
                        <td>Fecha no corresponde</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>1</td>
                        <td>Fecha no corresponde</td>
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
