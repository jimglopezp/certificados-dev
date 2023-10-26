
<p><br>
<center><h1><?php
        if ($input == 1 || $input == 2 || $input == 17 || $input == 20)
            echo $titulo;
        ?>
    </h1></center>

<p><br> 
<form id="form1" action="<?php echo base_url('index.php/iniciocertificados/imprimir') ?>" method="post" onsubmit="return confirmar();">
    <input type="hidden" name="vista" id="vista" value="<?php echo $vista; ?>">
    <input type="hidden" name="name_reporte" id="name_reporte" value="<?php echo $titulo; ?>">
 <?php if ($input == 101) { ?>
        <?php
        if (empty($liquidacion_pagos)) {
            echo '<center><h2>No tiene pagos por liquidaciones</h2></center>';
        } else {
            ?>
            <table id="tabla_pagos_liquidacion">
                <thead>
                    <tr>        
                        <th>Seleccionar</th>
                        <th>Concepto</th>
                        <th># de Liquidación</th>
                        <th>Fecha liquidación</th>
                        <th>Periodo inicial</th>
                        <th>Periodo Final</th>
                        <th># Ticket</th>
                        <th>Fecha Pago</th>
                        <th>Valor Pagado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($liquidacion_pagos)) {
                        foreach ($liquidacion_pagos as $data) {
                            ?> 
                            <tr> 
                                <td>
                                    <input type='checkbox' class='liquidaciones_check'  value='<?php echo $data['COD_PAGO']; ?>' >
                                </td>
                                <td><?php echo $data['NOMBRE_TIPO']; ?></td>
                                <td><?php echo $data['NUM_LIQUIDACION']; ?></td> 
                                <td><?php echo $data['FECHA_LIQUIDACION']; ?></td>
                                <td><?php echo $data['FECHA_INICIO']; ?></td>
                                <td><?php echo $data['FECHA_FIN']; ?></td>     
                                <td><?php echo $data['TICKETID']; ?></td>  
                                <td><?php echo $data['FECHA_PAGO']; ?></td>                                 
                                <td><?php echo '$' . number_format($data['VALOR_PAGADO']); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>     
            </table>
            <?php
        }
        ?>
        <table width="350px" border="0" style="margin: 0 auto;">
            <tr>
                <td align="center" colspan="2">
                    <input type="hidden"  id="accion" name="accion" value="<?php echo $input; ?>">
                    <button id="generar" class="btn btn-success">Generar</button>
                </td>
            </tr>

        </table>
        <input type="hidden" id="codigos_liquidaciones" name="codigos_liquidaciones">
        <input type="hidden" name="nit_empresa_referencia" value="<?php echo $nit; ?>">
    <?php } ?>
</form>
<script>
    invocar_datatable();
    function ajaxValidationCallback(status, form, json, options) { }
    function invocar_datatable() {
        $('#tabla_pagos_liquidacion').dataTable({
            "bJQueryUI": true,
            "bPaginate": false,

            "oLanguage": {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "fnInfoCallback": null,
            },
            "sServerMethod": "POST",
            "aoColumns": [
                {"sClass": "center"}, /*id 0*/
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"},
                {"sClass": "center"}
            ],
        });
    }
    function confirmar() {
        if ($('#tabla_pagos_liquidacion').length > 0) {
            $('#tabla_pagos_liquidacion').dataTable().fnDestroy();
        }
        var deleteids_arr = [];
        $("input:checkbox[class=liquidaciones_check]:checked").each(function () {
            deleteids_arr.push($(this).val());
        });
        invocar_datatable();
        $("#codigos_liquidaciones").val(deleteids_arr);

    }

</script>