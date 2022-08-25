<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

    public $_field_data;
    public $_config_rules;
    public $_error_array = array();
    public $_error_messages = array();
    public $error_string = '';  
   function __construct($rules = array())
   {
     parent::__construct($rules); 
   } 
   
   public function uniques($str, $field)
   {
      if (substr_count($field, '.')==3)
      {
         list($table,$field,$id_field,$id_val) = explode('.', $field);
         $query = $this->CI->db->limit(1)->where($field,$str)->where($id_field.' != ',$id_val)->get($table);
      } else {
         list($table, $field)=explode('.', $field);
         $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
      }

      return $query->num_rows() === 0;
    }



   
   public function exists2($str, $field)
   {
      if (substr_count($field, '.')==3)
      {
         list($table,$field,$id_field,$id_val) = explode('.', $field);
         $query = $this->CI->db->limit(1)->where($field,$str)->where($id_field.' != ',$id_val)->get($table);
      } else {
         list($table, $field)=explode('.', $field);
         $query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
      }

      return $query->num_rows() !== 0;
    }      

}



