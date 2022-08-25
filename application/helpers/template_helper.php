<?php

function read_template($file_txt) {
    $name_file = $file_txt;
    if (is_file(realpath($name_file))) :
        $contenido = file_get_contents(realpath($name_file), true);
    elseif (file_exists($name_file)) :
       @$contenido = file_get_contents($name_file, true);
    else :
        return false;
    endif;
    return $data = $contenido;
}

function template_tags($file_txt, $arr_basic = array()) {
    //abrimos el archivo de texto y obtenemos el identificador
    $contet_file = read_template($file_txt);

    foreach ($arr_basic as $key => $value) {
        $contet_file = str_replace('%-' . $key . '-%', $value, $contet_file);
    }

    return $contet_file;
}

function create_template($route_template, $text) {
    $name = md5(date('ymdGis') . microtime('get_as_float')) . '.txt';
    $estructura = $route_template;
    //echo $estructura.$name;
    if (!file_exists($estructura)) {
        if (!mkdir($estructura, 0777, true)) {
            die('Fallo al crear las carpetas...');
        }
    }
    $ar = fopen($estructura . $name, "w+") or die();

    fputs($ar, $text);

    fclose($ar);
    return $name;
}

/*
 * data[0]= Tipo de Documento
 * 1.--- Resolucion (documento oficio con encabezado y espacio para un numero de resolucion)
 * 2.--- Auto(documento oficio con encabezado)
 * 3.--- Documentos por defecto (comunicados)
 * 
 * data[1]= Titulo del documento para el encabezado, necesario para autos y resoluciones
 * Ejemplo. Auto que avoca conocimiento al expediente
 * 
 * $name = Nombre que va a tomar el documento
 * $txt_file = Texto Html para el cuerpo del PDF
 * $print = TRUE abrir el PDF con cuadro de dialogo de impresion
 * 
 */

function createPdfTemplateOuput($name, $txt_file, $print = false, $data = null) {
//    /*
//     * PREPARAR TABLA DE ENCABEZADO
//     */
//    $DM = get_instance();
//    $DM->load->model('documentospj_model');
//    $Cabecera = $DM->documentospj_model->cabecera($data[2], 1111);
//    $encabezado = '<table width="100%" border="1" align="center">
//                    <tr>
//                      <td><div align="center"><strong>Identificación</strong></div></td>
//                      <td><div align="center"><strong>Ejecutado</strong></div></td>
//                      <td><div align="center"><strong>Concepto</strong></div></td>
//                      <td><div align="center"><strong>Telefono</strong></div></td>
//                      <td><div align="center"><strong>Direccion</strong></div></td>
//                      <td><div align="center"><strong>Saldo Deuda</strong></div></td>
//                    </tr>
//                    <tr>
//                      <td>' . $Cabecera['IDENTIFICACION'] . '</td>
//                      <td>' . $Cabecera['EJECUTADO'] . '</td>
//                      <td>' . $Cabecera['CONCEPTO'] . '</td>
//                      <td>' . $Cabecera['TELEFONO'] . '</td>
//                      <td>' . $Cabecera['DIRECCION'] . '</td>
//                      <td> $' . number_format($Cabecera[0]['SALDO_DEUDA'], 0, '.', '.') . '</td>
//                    </tr>
//                  </table>';
//
//    switch ($data[0]) {
//        case 1:
//        case 2:
//        case 3:
//            $txt_file = $encabezado . $txt_file;
//            break;
//    }
    /*
     * CONSULTAR DATOS DE LA REGIONAL
     */

    $CI = get_instance();
    $CI->load->library('tcpdf/Plantilla_DocJuridica.php');
    $data_usuario = $CI->ion_auth->user()->row();
    $COD_REGIONAL = $data_usuario->COD_REGIONAL;

    $CI->load->model('nulidad_model');
    $Datos = $CI->nulidad_model->get_regional($COD_REGIONAL);
    include_once(dirname(dirname(__FILE__)) . '/libraries/tcpdf/tcpdf.php');
    if (is_file(realpath($txt_file))) :
        $html = file_get_contents($txt_file, filesize($txt_file));
    else :
        $html = $txt_file;
    endif;
    ob_clean();
    switch ($data[0]) {
        case 1:
        case 2:
            $pdf = new Plantilla_DocJuridica(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            break;
        default:
            $pdf = new Plantilla_DocJuridica(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
            break;
    }

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->Tipo = $data[0];
    $pdf->Regional = $Datos["NOMBRE_REGIONAL"];
    $pdf->Direccion = $Datos["DIRECCION_REGIONAL"];
    $pdf->Telefono = $Datos["TELEFONO_REGIONAL"];
    $pdf->Nombre = $data[1];
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(70);
    $pdf->SetTopMargin(30);
    $pdf->SetFooterMargin(30);
    $pdf->SetLeftMargin(30);
    $pdf->SetRightMargin(30);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('dejavusans', '', 8, '', true);
    $pdf->setFooterData('sena');

    if ($print) {
        $js = '
            print();
            ';

        $pdf->IncludeJS($js);
    }

    $pdf->AddPage();
    $estilos = '<<<EOF
    <style>
    div {
        margin: 0;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 12px;
        line-height: 20px;
        color: #333333;
        background-color: #ffffff;
    }

    .table {
      width: 100%;
      font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 12px;
      line-height: 20px;
      color: #333333;
      background-color: #FFFFFF;
      border-spacing: 0;
      border: 1px solid #AAAAAA;
    }

    .table > th{
        background-color: #f9f9f9;
        font-weight: bold;
        font-size: 10px !important;
        border: 1px solid #AAAAAA;
    }

    .table > td{
        padding: 8px;
        text-align: left;
        vertical-align: top;
        border-spacing: 0px;
        border: 1px solid #AAAAAA;
    }

    .table-striped td {
    background-color: #f9f9f9;
    font-size: 10px !important;
    border-spacing: 0px;
     border: 1px solid #AAAAAA;
    }

    .table-bordered td {
    border-left: 1px solid #dddddd;
    font-size: 12px !important;
     border: 1px solid #AAAAAA;
    }

    .muted{
        color: #999999;
    }

    .info{
        color: #333333;
        background-color: #d9edf7;
    }
    .alert {
        background-color: #fcf8e3;
        color: #c09853;
        border: 1px solid #fbeed5;
        border-radius: 4px;
        margin-bottom: 20px;
        padding: 8px 35px 8px 14px;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .success{
        color: #468847;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .error{
        background-color: #f2dede;
        border-color: #eed3d7;
        color: #b94a48;
    }
    </style>
    EOF';
    $html .= $estilos;
    $file = 'img/fondosena2.png';
    $pdf->Image($file, 40, 90, 130, 130, '', '', '', false, 300, '', FALSE);
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Image($file, 40, 90, 130, 130, '', '', '', false, 300, '', FALSE);
    $pdf->Output($name, 'I');
}

function createPdfTemplateSave($txt_file, $pdf_route) {
    include_once(dirname(dirname(__FILE__)) . '/libraries/tcpdf/tcpdf.php');
    $name = md5(date('ymdGis') . microtime('get_as_float')) . '.pdf';
    //$html=  str_replace('"', '\"', read_template($txt_file));
    $html = read_template($txt_file);
    //ob_clean();
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    //$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetHeaderMargin(0);
    //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //$pdf->setFontSubsetting(true);
    //$pdf->SetFont('dejavusans', '', 8, '', true);
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output($pdf_route . $name, 'F');

    return $name;
}

function viewTempletePdf($name, $html, $print = false) {
    include_once(dirname(dirname(__FILE__)) . '/libraries/tcpdf/tcpdf.php');
    ob_clean();
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    //$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetHeaderMargin(0);
    //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    //$pdf->setFontSubsetting(true);
    //$pdf->SetFont('dejavusans', '', 8, '', true);
    if ($print) {
        $js = '
            print();
            ';

        $pdf->IncludeJS($js);
    }

    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output($name, 'I');
}

function donwloadFile($route, $name) {
    $enlace = $route;
    header("Contenszt-Disposition: attachment; filename='" . $name . "'");
    header("Content-Type: application/octet-stream");
    header("Content-Length: " . filesize($enlace));
    ob_clean();
    readfile($enlace);
    unlink($enlace);
}

/* function createPdfTemplateDonw($name, $txt_file){

  $this->load->library('tcpdf/tcpdf');

  $html=  str_replace('"', '\"', read_template($txt_file));

  //ob_clean();
  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetCreator(PDF_CREATOR);
  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->setFontSubsetting(true);
  $pdf->SetFont('dejavusans', '', 8, '', true);
  $pdf->AddPage();
  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->Output($name, 'I');

  exit();
  } */
?>
