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
<h2><strong> <i class="fa fa-circle" aria-hidden="true"></i> Seleccionar tipo de pago a dispersar</h2>
    <?php
    // Opciones de tipo FIC
    $tiposFicOpciones  =  array (
        '1'   =>  'Pago Mensual',
        '2'   =>  'Pago a Todo Costo',
        '3'   =>  'Pago Mano de Obra',
    );

    // Atributos para el boton Consultar
    $btnContinuar=array(
        'name'=>'btnContinuar',
        'type' => 'submit',
        'class'=>'btn btn-success',
        'required' => 'true',
        'content' => '<i class="fa fa-caret-square-o-right"></i> Continuar'
    );
    ?>
    <div class="p-3 col-sm-12 bg-light text-dark text-center">
        <div class="row">
        <br>
        <div class="col-md-3 col-sm-12"></div>
        <div class="col-md-6 col-sm-12">
            <?php echo form_open('dispersion_pagos/buscar'); ?>
            <table class="table">
              <tbody>
                <tr>
                    <td align="center">
                        <p>Seleccionar tipo de FIC:</p>
                        <br>
                        <?php echo form_dropdown('tipoFic', $tiposFicOpciones, '1', 'id="tiposFic" class="form-control"'); ?>
                        <br>
                    </td>
                <tr>
                <tr>
                    <td align="center">
                        <?php echo form_button($btnContinuar) ?>
                    </td>
                </tr>
              </tbody>
            </table>
            <?php echo form_close(); ?>
        </div>
        <div class="col-md-3 col-sm-12">
            &nbsp;
        </div>
        </div>
    </div>
    <h2><?php if(isset($mensaje)) echo $mensaje; ?></h2>
    <?php echo validation_errors();?><!--mostrar los errores de validación-->
 </div>
