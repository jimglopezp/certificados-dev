<script>
  window.document.title = 'Descargar Certificado';
</script>

<?php
$button = array('type' => 'submit', 'name' => 'guarda_elaaud',  'class'=>'btn btn-primary btn-sm',  'id' => 'guarda_elaaud', 'value' => 'Guardar', 'content' => '<i class=""></i>Buscar');
$atributos = array('onsubmit' => 'return validateForm()', 'name' => 'frmtp', 'id' => 'frmtp', 'method' => 'post');
?>

<div class="center-form-large " style=" width:70%; ">
  <center>
    <h1 style=" font-weight: 550; "> Descargar Certificado </h1>
  </center>
  <br><br>


  <div class="p-3 col-sm-12 bg-light text-dark ">

    <div class="form-row">

      <div class="col-md-12">
        <legend class="GrupoTabTitle">Bienvenido </legend>
        <div data-align-inner="">
          <div class="TxtInfo" style="display:inline;" id="TXTINFO" data-gxformat="1">
            Por favor Seleccione el Tipo de Certificado que desea Descargar , tenga en cuenta que la
            información Ingresada debe ser correcta para que el proceso sea Exitoso, de lo contrario
            diríjase a su Centro de Formación.<br><br><b>Importante!: </b>No Olvide Desconectarse una vez
            Finalice la Descarga de su Certificado.<br>
          </div>
        </div>

        <?php echo form_open_multipart("iniciocertificados/tipoCertificados", $atributos); ?>
        <div class="row">

        </div>

      </div>
    </div>
  </div>

  <div class="p-3 col-sm-12 bg-light text-dark">
    <div id="result" style=" text-align:center;" title="Seleccione opción de busqueda"><strong>Seleccione opción de busqueda</strong> </div>
    <br>
    <table class="col-md-12" width="100%">
      <tbody>
        <tr style="display:block; visibility:visible">
          <td style="width: 300px" align="center"></td>
          <td style="width: 100%" align="center">
            <strong><i>Tipo Certificado: <i></i></i></strong><i><i>
                <select id="tipoCertificado" name="tipoCertificado" size="1" style="width: 230px; height: 30px;">
                  <?php echo $select; ?>
                </select>
                <br>
          <td width="21%">
        <tr>

          <td align="center">
            <?= form_button($button) ?>
          </td>

        </tr>
        </td>
        </tr>

      </tbody>
    </table>
    <br>

  </div>
</div>

<script>
  function validaNumericos(event) {
    if (event.charCode >= 48 && event.charCode <= 57) {
      return true;
    }
    return false;
  }
</script>

<script language="JavaScript">
  function pregunta() {
    var formulario = document.getElementById("frmtp");
    var dato = formulario[0];
    if (confirm('¿Seguro de realizar estos cambios?')) {
      formulario.submit();
      return true;
    } else {
      return false;
    }

  }
</script>

<script>
  function validateForm() {
    var x = document.forms["frmtp"]["archivos"].value;

    if (x == "") {
      alert("Name must be filled out");
      return false;
    } else {
      var opcion = confirm("Esta seguro de realizar este cambio");
      if (opcion == true) {
        //console.log("entra");
        document.getElementById('loading_oculto').style.visibility = 'visible';
        return true;
        //   mensaje = "Has clickado OK";
      } else {
        //   mensaje = "Has clickado Cancelar";
        return false;
      }


    }
  }
</script>
<script>
  jQuery(".preload, .load").hide();
  $('.push').click(function() {
    $(".preload, .load").show();
    setTimeout(function() {
      jQuery(".preload, .load").hide();
    }, 200);
  });
</script>