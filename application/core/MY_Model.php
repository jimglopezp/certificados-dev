<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Community Auth - MY Model
 *
 * Community Auth is an open source authentication application for CodeIgniter 2.1.3
 *
 * @package     Community Auth
 * @author      Robert B Gottier
 * @copyright   Copyright (c) 2011 - 2013, Robert B Gottier. (http://brianswebdesign.com/)
 * @license     BSD - http://http://www.opensource.org/licenses/BSD-3-Clause
 * @link        http://community-auth.com
 */
class MY_Model extends CI_Model {

    /**
     * An array specifying the form validation error delimeters.
     * They can be conveniently set in either the controller or model.
     * I like to use a list for my errors, and CI default is for 
     * individual paragraphs, which I think is somewhat retarded.
     *
     * @var array
     * @access public
     */
    public $error_delimiters = array('<li>', '</li>');
    public $formatDate = "YYYY-MM-DD";
    public $debug = false;

    /**
     * An array specifying which fields to unset from 
     * the form validation class' protected error array.
     * This is helpful if you have hidden fields that 
     * are required, but the user shouldn't see them 
     * if form validation fails.
     *
     * @var string
     * @access public
     */
    public $hide_errors = array();

    /**
     * All form validation errors are stored as a string, 
     * and can be accessed from the controller or model.
     *
     * @var string
     * @access public
     */
    public $validation_errors = '';

    /**
     * Validation rules are set in the model, since 
     * the model is aware of what data should be inserted 
     * or updated. The exception would be when using the 
     * reauthentication feature, because we can optionally 
     * pass in our validation rules from the controller.
     *
     * @var string
     * @access public
     */
    public $validation_rules = array();
    public $condition = array();

    // --------------------------------------------------------------

    /**
     * Class constructor
     */
    public function __construct() {
        parent::__construct();
        $this->debug = false;
        $this->formatDate = "YYYY-MM-DD";
    }
    public function debug_($d = false) {
        if($this->debug == true || $d){
            echo "<pre>".$this->db->last_query()."</pre>";
            $this->debug = false;
        }
    }

    // --------------------------------------------------------------

    /**
     * Form validation consolidation.
     */
    public function validate() {
        // Load the form validation library
        $this->load->library('form_validation');

        // Apply the form validation error delimiters
        $this->_set_form_validation_error_delimiters();

        // Set form validation rules
        $this->form_validation->set_rules($this->validation_rules);

        // If form validation passes
        if ($this->form_validation->run() !== FALSE) {
            // Load var to confirm validation passed
            $this->load->vars(array('validation_passed' => 1));

            return TRUE;
        }

        /**
         * If form validation passes, none of the code below will be processed.
         */
        // Unset fields from the error array if they are in the hide errors array.
        if (!empty($this->hide_errors)) {
            foreach ($this->hide_errors as $field) {
                $this->form_validation->unset_error($field);
            }
        }

        // Load errors into class member for use in model or controller.
        $this->validation_errors = validation_errors();

        // Load var w/ validation errors
        $this->load->vars(array('validation_errors' => $this->validation_errors));

        /**
         * Do not repopulate with data that did not validate
         */
        // Get the errors
        $error_array = $this->form_validation->get_error_array();

        // Loop through the post array
        foreach ($this->input->post() as $k => $v) {
            // If a key is in the error array
            if (array_key_exists($k, $error_array)) {
                // kill set_value() for that key
                $this->form_validation->unset_field_data($k);
            }
        }

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Sometimes, when you have a successful form validation, 
     * you will not want to repopulate the form, but if you 
     * don't unset the field data, the form will repopulate.
     */
    public function kill_set_value() {
        $this->form_validation->unset_field_data('*');
    }

    // --------------------------------------------------------------

    /**
     * Set the form validation error delimiters with an array.
     */
    private function _set_form_validation_error_delimiters() {
        list( $prefix, $suffix ) = $this->error_delimiters;

        $this->form_validation->set_error_delimiters($prefix, $suffix);
    }

    // --------------------------------------------------------------

    /**
     * You might require a logged in user to provide their username and 
     * password to get past a certain point, or to make an important change. 
     * Whatever the case may be, reauthentication makes the logged in 
     * user provide a good username and password to proceed.
     *
     * @param   bool  to use simple login form validation (FALSE if you provide a different set)
     * @return  bool
     */
    public function reauthenticate($stand_alone = TRUE) {
        // Set form validation rules if they haven't been provided
        if ($stand_alone) {
            $this->config->load('form_validation/auth/login');
            $this->validation_rules = config_item('login_rules');
        }

        if ($this->validate()) {
            // Get the logged in user's record
            $query = $this->db->select('
				user_name,
				user_email,
				user_pass,
				user_salt,
				user_login_time,
				user_modified
			')->get_where(config_item('user_table'), array('user_id' => config_item('auth_user_id')));

            // If there is a matching row
            if ($query->num_rows() == 1) {
                $row = $query->row();

                // Hash the provided password using the user's salt
                $hashed_pwd = $this->authentication->hash_passwd(set_value('login_pass'), $row->user_salt);

                /**
                 * Reauthentication checks aren't quite as rigorous as a 
                 * real login, because we will assume there is a lower risk 
                 * based on the fact that the user is already logged in.
                 */
                if (
                // If the password matches
                        $hashed_pwd == $row->user_pass &&
                        // If the provided string is the user's email or username
                        ( set_value('login_string') == $row->user_email OR set_value('login_string') == $row->user_name ) &&
                        // The user login time matches one in session
                        $row->user_login_time == $this->authentication->expose_login_time($this->session->userdata('auth_identifier')) &&
                        // The user's last modified time matches the one in the session
                        $row->user_modified == $this->authentication->expose_user_last_mod($this->session->userdata('auth_identifier'))
                ) {
                    $this->load->vars(array('reauthenticated' => TRUE));

                    return TRUE;
                }
            }

            $this->load->vars(array('reauthenticated' => FALSE));
        }

        return FALSE;
    }

    public function get($table, $fields, $where = NULL, $perpage = NULL, $start = NULL, $one = false, $array = 'array') {
        $this->db->select($fields,false);
        $this->db->from($table,false);
        if ($perpage && $start) {
            $this->db->limit($perpage, $start);
        }
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        if($query) {
            $result = !$one ? $query->result($array) : $query->row();
            if (count($result) > 0) {
                return $result;
            }
        }
        return NULL;
    }

    public function execute_query($sql, $one = false) {
        $query = $this->db->query($sql);
        //echo $this->db->last_query();
        return $this->validation_array($query, $one);
    }

    public function get_option($table, $where, $id, $order_key = NULL, $order = "ASC") {
        $this->db->where($where, $id);
        if ($order_key) {
            $this->db->order_by($order_key, $order);
        }
        $result = $this->db->get($table);
        return $result;
    }

    function add($table, $data, $date = '', $hora = FALSE, $format = 'DD/MM/YYYY') {
        if ($hora == TRUE) {
            $format = $format . ' hh24:mi:ss';
        }
        if ($date != '') {
            foreach ($data as $key => $value) {
                if (is_numeric($value)) {
                    $this->db->set($key, $value, false);
                } else {
                    $this->db->set($key, $value);
                }
            }
            foreach ($date as $keyf => $valuef) {
                $this->db->set($keyf, "TO_DATE('" . $valuef . "','" . $format . "')", false);
            }

            $this->db->insert($table);
        } else {
            $this->db->insert($table, $data);
        }
        $this->debug_();
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        }

        return FALSE;
    }

    public function edit($table, $data, $fieldID, $ID, $date = '', $format = 'dd/mm/yyyy') {
        $this->db->where($fieldID, $ID);
        foreach ($data as $key => $value) {
            $this->db->set($key, $value);
        }
        if ($date != '') {
            foreach ($date as $keyf => $valuef) {
                $this->db->set($keyf, "to_date('" . $valuef . "','" . $format . "')", false);
            }
        }
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function edit_($table, $data, $codition, $date = NULL, $format = 'dd/mm/yyyy') {
        foreach ($codition as $value) {
            $this->db->where($value['id'], $value['value']);
        }
        foreach ($data as $key => $value) {
            $this->db->set($key, $value);
        }
        if ($date) {
            foreach ($date as $keyf => $valuef) {
                $this->db->set($keyf, "to_date('" . $valuef . "','" . $format . "')", false);
            }
        }
        $this->db->update($table);
        if ($this->db->affected_rows() >= 0) {
            return TRUE;
        }

        return FALSE;
    }

    function get_select($table, $fields, $where = '', $order = '') {
        if (!empty($where)) :
            //echo "query1: SELECT " . $fields . "  FROM " . $table . " " . $where . " " . $order . " ";
            $where = explode("WHERE", $where);
            if (count($where) > 1) :
                $tmp = explode("=", $where[1]);
                $pos1 = strpos($tmp[1], "'");
                $pos2 = strpos($tmp[1], '"');
                if ($pos1 < 1 and $pos2 < 1) :
                    $tmp[1] = "'" . trim($tmp[1]) . "'";
                endif;
            endif;
            $where[1] = implode("=", $tmp);
            $where = implode("WHERE", $where);
        endif;
        //echo "<br>query2: SELECT " . $fields . "  FROM " . $table . " " . $where . " " . $order . " ";//exit();
        $query = $this->db->query("SELECT " . $fields . "  FROM " . $table . " " . $where . " " . $order . " ");
        //echo $this->db->last_query();
        return $query->result();
    }

    function delete($table, $fieldID, $ID) {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        }

        return FALSE;
    }
    function delete_query($table, $where) {
        $this->db->where($where);
        $this->db->delete($table);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        }

        return FALSE;
    }

    function get_last_inserted($table, $id) {
        $this->db->select_max($id);
        $Q = $this->db->get($table);
        $row = $Q->row_array();
        return $row[$id];
    }

    public function new_condition($key, $value) {
        $this->condition = array();
        array_push($this->condition, array('id' => $key, 'value' => $value));
    }

    public function add_condition($key, $value) {
        array_push($this->condition, array('id' => $key, 'value' => $value));
    }

    public function validation_array($query, $one = false) {
        if ($query) {
            if (count($query->result_array()) > 0) {
                if ($one) {
                    return $query->result_array()[0];
                }
                return $query->result_array();
            }
        }
        if ($one) {
            return NULL;
        }
        return array();
    }

    public function report_log($table, $data, $log) {
        $datai = array(
            'TABLE' => $table,
            'STR_DATA' => $data,
            'LOG' => $log
        );
        $date = array(
            'DATE_LOG' => date("dd/mm/yyyy hh24:mi:ss")
        );
        $this->add("LOGDESCRIPTION", $datai, $date, TRUE);
    }

  
    function get_nombres_columnas($table){
        $query = $this->db->query("SELECT table_name, column_name, data_type, data_length
            FROM USER_TAB_COLUMNS
            WHERE table_name = '$table'");
        return $query->result_array();
    }

    // --------------------------------------------------------------
}

/* End of file MY_Model.php */
/* Location: /application/libraries/MY_Model.php */