<?php
if (isset($message)) {
    echo $message;
}
?>


<h1>Certificados</h1>
<p>
    <b>Hola <?php echo $user->NOMBRES . ' ' . $user->APELLIDOS; ?></b>
</p>

<div id="dialognotificaciones">
<label for="cars">Certificado De:</label>

<select id="tipoCertificado"  style="width:300px;">
    <?php  echo $select;  ?>
  <!--<option value="APORTESPARAFISCALES">APORTES PARAFISCALES</option>
  <option value="RECIPROCAS">RECIPROCAS</option>
  <option value="ESTADOPILA">ESTADO PILA</option>
  <option value="FIC">FIC</option>
  <option value="TRIBUTARIOYRECAUDO" selected>TRIBUTARIO Y RECAUDO DE PAGOS</option>-->
</select>
    
</div>



</div>
<p>
    Esta página estará destinada a mostrar algunas alertas, mensajes de entrada... etc.
</p>
