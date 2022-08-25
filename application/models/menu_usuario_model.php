<?php

class Menu_usuario_model extends CI_Model {

  function __construct()
  {
    parent::__construct();
  }

  function getmenus()
  {
    $this -> db -> cache_on();
    $id = $this -> session -> userdata('user_id');
    //$id =intval($id);
    $this -> db -> select('ME.IDMENU,ME.NOMBREMENU,ME.URL,ME.ICONOMENU,MO.NOMBREMODULO,MO.IDMODULO AS MODULOID,MO.URL AS MODULOURL,MO.ICONOMODULO,AP.NOMBREAPLICACION,AP.IDAPLICACION AS APLICACIONID,AP.URL AS URLAPLICACION,AP.ICONOAPLICACION,MP.CODMACROPROCESO,MP.NOMBREMACROPROCESO,MP.ICONO AS ICONOMACROPROCESO,AP.CODPROCESO');
    $this -> db -> from('MENUS ME');
    $this -> db -> join('MODULOS MO', 'ME.IDMODULO=MO.IDMODULO AND MO.IDESTADO=1', 'inner');
    $this -> db -> join('APLICACIONES AP', 'MO.IDAPLICACION=AP.IDAPLICACION AND AP.IDESTADO=1', 'inner');
    $this -> db -> join('MACROPROCESO MP', 'AP.CODPROCESO=MP.CODMACROPROCESO AND MP.IDESTADO=1', 'inner');
    $this -> db -> join('PERMISOS_USUARIOS PU', "ME.IDMENU=PU.IDMENU AND PU.IDUSUARIO='" . $id . "'", 'inner');
    $this -> db -> where('ME.IN_MENU', 1);
    $this -> db -> where('ME.IDESTADO', 1);
    $this -> db -> order_by("MP.NOMBREMACROPROCESO", "asc");
    $this -> db -> order_by("AP.NOMBREAPLICACION", "asc");
    $this -> db -> order_by("MO.NOMBREMODULO", "asc");
    $this -> db -> order_by("ME.NOMBREMENU", "asc");
    $query = $this -> db -> get();
    //echo $this -> db -> last_query();
    //exit();
    if ($query -> num_rows() > 0)
    {
      return $query -> result();
    }
    $this -> db -> cache_off();
  }

  function getmenusadmin()
  {
    $this -> db -> cache_on();
    $id = $this -> session -> userdata('user_id');
    $id = intval($id);
    if ( ! empty($id)) :
      $this -> db -> select('ME.IDMENU,ME.NOMBREMENU,ME.URL,ME.ICONOMENU,MO.NOMBREMODULO,MO.IDMODULO AS MODULOID,MO.URL AS MODULOURL,MO.ICONOMODULO,AP.NOMBREAPLICACION,AP.IDAPLICACION AS APLICACIONID,AP.URL AS URLAPLICACION,AP.ICONOAPLICACION,MP.CODMACROPROCESO,MP.NOMBREMACROPROCESO,MP.ICONO AS ICONOMACROPROCESO,AP.CODPROCESO');
      $this -> db -> from('MENUS ME');
      $this -> db -> join('MODULOS MO', 'ME.IDMODULO=MO.IDMODULO', 'inner');
      $this -> db -> join('APLICACIONES AP', 'MO.IDAPLICACION=AP.IDAPLICACION', 'inner');
      $this -> db -> join('MACROPROCESO MP', 'AP.CODPROCESO=MP.CODMACROPROCESO AND MP.IDESTADO=1', 'inner');
      $this -> db -> where('ME.IN_MENU', '1');
      $this -> db -> where('ME.IDESTADO', '1');
      $this -> db -> order_by("MP.NOMBREMACROPROCESO", "asc");
      $this -> db -> order_by("AP.NOMBREAPLICACION", "asc");
      $this -> db -> order_by("MO.NOMBREMODULO", "asc");
      $this -> db -> order_by("ME.NOMBREMENU", "asc");
      $query = $this -> db -> get();
      if ($query -> num_rows() > 0)
      {
        return $query -> result();
      }
    endif;
    $this -> db -> cache_off();
  }

}
