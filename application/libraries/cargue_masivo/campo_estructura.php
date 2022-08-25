<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class campo_estructura
{
    public $NITEMPRESA; 
    public $PROCEDENCIA;
    public $COD_CONCEPTO;
    public $COD_SUBCONCEPTO;
    public $COD_REGIONAL;
    public $PERIODO_PAGADO;
    public $FECHA_PAGO;
    public $FECHA_APLICACION;
    public $FECHA_TRANSACCION;
    public $COD_FORMAPAGO;
    public $NUM_DOCUMENTO;
    public $DISTRIBUCION_CAPITAL;
    public $DISTRIBUCION_INTERES;
    public $COD_ENTIDAD;
    public $NRO_REFERENCIA;
    public $VALOR_ADEUDADO;
    public $NRO_TRABAJADORES_PERIODO;
    public $NRO_RESOLUCION_REGULACION;
    public $FECHA_RESOLUCION;
    public $NRO_LICENCIA_CONTRATO;
    public $NOMBRE_OBRA;
    public $FECHA_INICIO_OBRA;
    public $FECHA_FIN_OBRA;
    public $CIUDAD_OBRA;
    public $TIPO_FIC;
    public $COSTO_TOTAL_OBRA_TODO_COSTO;
    public $COSTO_TOTAL_MANO_DE_OBRA;
    public $TIPO_CARNE;
    public $NRO_CONVENIO;
    public $VALOR_PAGADO;
    public $REGIONAL_SIIF;
    public $CENTRO_SIIF;
    public $CODIGO_SIIF;
    public $TICKETID;
    public $RADICADO_ONBASE;
    public $FECHA_ONBASE;
    public $DISTRIBUCION_SANCION;


    public function __construct()
    {
        $this-> NITEMPRESA =0;
        $this-> PROCEDENCIA =1;
        $this-> COD_CONCEPTO =2;
        $this-> COD_SUBCONCEPTO =3;
        $this-> COD_REGIONAL =4;
        $this-> PERIODO_PAGADO =5;
        $this-> FECHA_PAGO =6;
        $this-> FECHA_APLICACION =7;
        $this-> FECHA_TRANSACCION =8;
        $this-> COD_FORMAPAGO =9;
        $this-> NUM_DOCUMENTO =10;
        $this-> DISTRIBUCION_CAPITAL =11;
        $this-> DISTRIBUCION_INTERES =12;
        $this-> COD_ENTIDAD =13;
        $this-> NRO_REFERENCIA =14;
        $this-> VALOR_ADEUDADO =15;
        $this-> NRO_TRABAJADORES_PERIODO =16;
        $this-> NRO_RESOLUCION_REGULACION =17;
        $this-> FECHA_RESOLUCION =18;
        $this-> NRO_LICENCIA_CONTRATO =19;
        $this-> NOMBRE_OBRA =20;
        $this-> FECHA_INICIO_OBRA =21;
        $this-> FECHA_FIN_OBRA =22;
        $this-> CIUDAD_OBRA =23;
        $this-> TIPO_FIC =24;
        $this-> COSTO_TOTAL_OBRA_TODO_COSTO =25;
        $this-> COSTO_TOTAL_MANO_DE_OBRA =26;
        $this-> TIPO_CARNE =27;
        $this-> NRO_CONVENIO =28;
        $this-> VALOR_PAGADO =29;
        $this-> REGIONAL_SIIF =30;
        $this-> CENTRO_SIIF =31;
        $this-> CODIGO_SIIF =32;
        $this-> TICKETID =33;
        $this-> RADICADO_ONBASE =34;
        $this-> FECHA_ONBASE =35;
        $this-> DISTRIBUCION_SANCION =36;
    }


    public function setNITEMPRESA($NITEMPRESA){
        $this ->NITEMPRESA = $NITEMPRESA;
      }

    public function getNITEMPRESA(){
        return $this->NITEMPRESA;
      }
   
   
}

?>