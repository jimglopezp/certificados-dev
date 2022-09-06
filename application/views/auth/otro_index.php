<script>
  window.document.title = 'Seleccionar opción';
</script>

<div class="center-form-large " style=" width:70%; ">
  <center>
    <h1 style=" font-weight: 550; "> Bienvenido </h1>
  </center>
  <br><br>


  <div class="p-3 col-sm-12 bg-light text-dark ">

    <div class="form-row">

      <div class="col-md-12">
        <legend class="GrupoTabTitle">Estimado contribuyente seleccione una opción: </legend>
        <div data-align-inner="">
          <div class="TxtInfo" style="display:inline;" id="TXTINFO" data-gxformat="1">
            <div class="zona-boton">
              <a href="<?php echo site_url(); ?>/auth/consulta" class="btn-3d">Generar Certificado <i class="fa fa-file-text" aria-hidden="true"></i></a>
            </div>
            <div class="zona-boton">
              <a href="<?php echo site_url(); ?>/dispersion_pagos/index" class="btn-3d">Dispersar pagos <i class="fa fa-money" aria-hidden="true"></i></a>
            </div>
          </div>
        </div>

        <?php echo form_open_multipart("iniciocertificados/tipoCertificados", $atributos); ?>
        <div class="row">

        </div>

      </div>
    </div>
  </div>
