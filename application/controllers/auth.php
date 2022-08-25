<?php
/*Por: Yuri Ramirez CDS */

defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(0);

class Auth extends MY_Controller
{

  //private $cliente;
  function __construct()
  {
    parent::__construct();
    //  $this->load->library('ion_auth');
    $this->load->library('form_validation');
    $this->load->library('Nu_soap');
    $this->load->helper('url');


    $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
    $this->lang->load('auth');
    $this->load->helper('language');
    $this->template_file = 'templates/main';

    $this->data['user'] = $this->session->all_userdata();

    define("COD_USUARIO", $this->data['user']['user_id']);
    define("COD_REGIONAL", $this->data['user']['regional']);
  }

  //redirect if needed, otherwise display the user list
  function index()
  {

    if (!$this->ion_auth->logged_in()) {
      //redirect them to the login page
      redirect('auth/login', 'refresh');
    } elseif (!$this->ion_auth->is_admin()) { //remove this elseif if you want to enable this for non-admins
      //redirect them to the home page because they must be an administrator to view this
      //return show_error('Esta área es sólo para administradores.');
    } else {
      //set the flash data error message if there is one
      $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

      //list the users
      $this->data['users'] = $this->ion_auth->users()->result();
      foreach ($this->data['users'] as $k => $user) {
        $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->IDUSUARIO)->result();
      }

      //$this->_render_page('auth/index', $this->data);
      //this->template->load($this->template_file, 'aplicaciones', $this->data);
      redirect('auth/login', 'refresh');
    }
  }
  function consulta()
  {


   // print_r($this->session->all_userdata());die;
    if (!$this->ion_auth->logged_in()) {
    // print_r("ff");die;
      //redirect them to the login page
    redirect('auth/login', 'refresh');
    } else {
      //redirect them to the login page
    //  $session_id = $this->session->userdata('email');
   
    
    $data = array(
      'LASTSESSIONID' => $this->session->userdata('session_id')
    );  
$sesion=$data['LASTSESSIONID'];
$id= COD_USUARIO;
     //http://192.168.157.185/certificados $cliente = new nusoap_client("http://localhost:8080/SIREC/Release/SENA.SIREC-Integration/index.php/certificados_services/index/wsdl?wsdl", false);
       $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificados_services/index/wsdl?wsdl",false);
      $parametros = array('numero' => "1" ,'sesion' => $sesion,'id' => $id,);
      $respuesta = $cliente->call('Certificados_services..ListaCertificados', $parametros);
      $this->data['select'] = $respuesta['Respuesta'];
      $this->template->load($this->template_file, 'auth/otro', $this->data);
    }
    //$this->load->view('auth/otro');
    //redirect('inicio', 'refresh');
  }
  //log the user in

  function login1()
  {

    $myVar = $this->session->flashdata('item2');
   // print_r($myVar);
if( $myVar==''){
  redirect('auth/login', 'refresh');

}else{
    $contador=1;
    $this->data['message'] = 'Contraseña No Valida ';
    $parametros = array('id1' => $myVar['PASSWORD'], 'correo' => $myVar['CORREO'], 'intentos' =>$contador);
  //  $cliente = new nusoap_client("http://localhost:8080/SIREC/Release/SENA.SIREC-Integration/index.php/certificadosempresa_services/index/wsdl?wsdl",false);
    $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl", false);
    $respuesta2 = $cliente->call('Certificadosempresa_services..verificar', $parametros);
    //print_r($respuesta2);die;
    $this->template->load($this->template_file, 'auth/login', $this->data);
}
  }



  function login()
  {
   // $session_id= $this->session->all_userdata();

   // print_r( $session_id);die;
    
    session_start();
  //  print_r($this->input->post());die;
    if ($this->ion_auth->logged_in()) {
      //redirect them to the login page
   
      $this->session->set_userdata('token', $this->session->userdata('session_id'));

      $mensale =   $this->session->set_flashdata('message', $this->ion_auth->messages());
      //  echo$mensale;die;
      redirect('auth/consulta', 'refresh');
    }else{

      if($_POST['captcha']!='' && $_SESSION['captcha'] != $_POST['captcha']){
       
  
     
        $_SESSION['msg'] = "<div class='kTNrif' jsname='cyzLac' aria-live='assertive' style='color: red;'>Error! el codígo ingresado no es correcto </div>";
        redirect('auth/login', 'refresh');
      }
//die;

    $myVar = $this->session->flashdata('item');
   
    if($myVar!=''){
      $correo=  $myVar['identity'];
      $contraseña= $myVar['password'];
      

    }else{
      $correo=  $this->input->post('identity');
      $contraseña= $this->input->post('password');
    }

    $this->data['title'] = "Bienvenidos Al Portal de Certificados En Línea";
    //validate form input
    $this->form_validation->set_rules('identity', 'Identity', 'required');
    $this->form_validation->set_rules('password', 'Password', 'required');

    if ($this->form_validation->run() == true || $myVar!='') {

      $remember = (bool) $this->input->post('remember');
      $parametros = array('usuario' => $correo, 'pass' => $contraseña);

     // $cliente = new nusoap_client("http://localhost:8080/SIREC/Release/SENA.SIREC-Integration/index.php/certificadosempresa_services/index/wsdl?wsdl", false);
      $cliente = new nusoap_client("http://192.168.157.185/sirec/index.php/certificadosempresa_services/index/wsdl?wsdl",false);
      $respuesta = $cliente->call('Certificadosempresa_services..LoginSirec', $parametros);
    // echo "<pre>";print_r($respuesta['empresa'][0]);exit("</pre>");
      $datosconsulta = @$respuesta['empresa'][0];
      if($datosconsulta['CODVERIFICACION']==''){
      

      if($datosconsulta['INTENTOS']>=3){
        $this->data['message'] = 'Sesion Bloqueada Recuperar  la Contraseña';
        $this->template->load($this->template_file, 'auth/login', $this->data);
      }else{
       
      if ($respuesta['Respuesta'] == '' || $datosconsulta['PASSWORD']=='') {
        $this->data['message'] = 'Usuario No Registrado';
        $this->template->load($this->template_file, 'auth/login', $this->data);
      } else {
        if ($ver = $this->ion_auth->login($correo, $contraseña, $remember, $datosconsulta)) {
       //  print_r($ver);die;
        
          if($ver['Respuesta']==4){

            $this->load->library('session');
            $dataS = array(
              'PASSWORD' => $datosconsulta['CODEMPRESA'],
              'CORREO'=> $correo
            
            );
            $this->session->set_flashdata('item2',$dataS);
              redirect("/auth/login1");

          
        }else{
      
          $data = array(
            'LASTSESSIONID' => $this->session->userdata('session_id')
          );  
//print_r($data );die;
          $this->session->set_userdata('token', $this->session->userdata('session_id'));

          $mensale =   $this->session->set_flashdata('message', $this->ion_auth->messages());
          //  echo$mensale;die;
          redirect('auth/consulta', 'refresh');

        } }else {
         
          $this->data['message'] = $respuesta['Mensaje'];
          $this->template->load($this->template_file, 'auth/login', $this->data);
        }
      }}
    } 
    else{


      $this->data['message'] = 'Su Contraseña se encuantra Bloqueada Intente Recuperararla ';
      $this->template->load($this->template_file, 'auth/login', $this->data);
    }

    }else {
      //the user is not logging in so display the login page
      //set the flash data error message if there is one
      //  $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

      $this->data['identity'] = array(
        'name' => 'identity',
        'id' => 'identity',
        'type' => 'text',
        'value' => $correo,
      );
      $this->data['password'] = array(
        'name' => 'password',
        'id' => 'password',
        'type' => 'password',
      );

      //$this->_render_page('auth/login', $this->data);
      // $this->lang->load('auth/login', $this->data);
      $this->template->load($this->template_file, 'auth/login', $this->data);
    }
  }
  }



  

  //log the user out
  function logout()
  {
    $this->data['title'] = "Desconectado";

    //log the user out
    $logout = $this->ion_auth->logout();

    //redirect them to the login page
    $this->session->set_flashdata('message', $this->ion_auth->messages());
    redirect('auth/login', 'refresh');
  }

  //change password
  function change_password()
  {
    $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
    $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
    $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

    if (!$this->ion_auth->logged_in()) {
      redirect('auth/login', 'refresh');
    }

    $user = $this->ion_auth->user()->row();

    if ($this->form_validation->run() == false) {
      //display the form
      //set the flash data error message if there is one
      $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

      $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
      $this->data['old_password'] = array(
        'name' => 'old',
        'id' => 'old',
        'type' => 'password',
      );
      $this->data['new_password'] = array(
        'name' => 'new',
        'id' => 'new',
        'type' => 'password',
        'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
      );
      $this->data['new_password_confirm'] = array(
        'name' => 'new_confirm',
        'id' => 'new_confirm',
        'type' => 'password',
        'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
      );
      $this->data['user_id'] = array(
        'name' => 'user_id',
        'id' => 'user_id',
        'type' => 'hidden',
        'value' => $user->id,
      );

      $this->template->load($this->template_file, 'auth/change_password', $this->data);
    } else {
      $identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

      $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

      if ($change) {
        //if the password was successfully changed
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->logout();
      } else {
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('auth/change_password', 'refresh');
      }
    }
  }

  //forgot password
  function forgot_password()
  {
    $this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required');
    if ($this->form_validation->run() == false) {
      //setup the input
      $this->data['email'] = array(
        'name' => 'email',
        'id' => 'email',
      );

      if ($this->config->item('identity', 'ion_auth') == 'username') {
        $this->data['identity_label'] = $this->lang->line('forgot_password_username_identity_label');
      } else {
        $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
      }

      //set any errors and display the form
      $this->data['message'] = (validation_errors()) ? validation_errors() : '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $this->session->flashdata('message') . '</div>';
      $this->template->load($this->template_file, 'auth/forgot_password', $this->data);
    } else {
      // get identity for that email
      $identity = $this->ion_auth->where('EMAIL', strtolower($this->input->post('email')))->users()->row();
      if (empty($identity)) {
        $this->ion_auth->set_message('forgot_password_email_not_found');
        $this->session->set_flashdata('message', '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $this->ion_auth->messages() . '</div>');
        redirect("auth/forgot_password", 'refresh');
      }

      //run the forgotten password method to email an activation code to the user

      $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

      if ($forgotten) {
        //if there were no errors
        $this->session->set_flashdata('message', '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $this->ion_auth->messages() . '</div>');
        redirect("auth/login", 'refresh'); //we should display a confirmation page here instead of the login page
      } else {
        $this->session->set_flashdata('message', '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $this->ion_auth->errors() . '</div>');
        //redirect("auth/forgot_password", 'refresh');
      }
    }
  }

  //reset password - final step for forgotten password
  public function reset_password($code = NULL)
  {
    if (!$code) {
      show_404();
    }

    $user = $this->ion_auth->forgotten_password_check($code);
    //echo "string".$user;
    if ($user) {
      //if the code is valid then display the password reset form

      $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
      $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

      if ($this->form_validation->run() == false) {
        //display the form
        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->data['new_password'] = array(
          'name' => 'new',
          'id' => 'new',
          'type' => 'password',
          'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        );
        $this->data['new_password_confirm'] = array(
          'name' => 'new_confirm',
          'id' => 'new_confirm',
          'type' => 'password',
          'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
        );
        $this->data['user_id'] = array(
          'name' => 'user_id',
          'id' => 'user_id',
          'type' => 'hidden',
          'value' => $user->IDUSUARIO,
        );
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['code'] = $code;

        //render
        //$this->_render_page('auth/reset_password', $this->data);
        $this->template->load($this->template_file, 'auth/reset_password', $this->data);
      } else {
        // do we have a valid request?
        if ($this->_valid_csrf_nonce() === FALSE || $user->IDUSUARIO != $this->input->post('user_id')) {

          //something fishy might be up
          $this->ion_auth->clear_forgotten_password_code($code);

          show_error($this->lang->line('error_csrf'));
        } else {
          // finally change the password
          $identity = $user->{$this->config->item('identity', 'ion_auth')};

          $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

          if ($change) {
            //if the password was successfully changed
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            $this->logout();
          } else {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect('auth/reset_password/' . $code, 'refresh');
          }
        }
      }
    } else {
      //if the code is invalid then send them back to the forgot password page
      $this->session->set_flashdata('message', $this->ion_auth->errors());
      redirect("auth/forgot_password", 'refresh');
    }
  }

  //create a new user
  function create_user()
  {
    $this->data['title'] = "Create User";

    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
      redirect('auth', 'refresh');
    }

    //validate form input
    $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
    $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
    $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[users.email]');
    $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
    $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
    $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
    $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

    if ($this->form_validation->run() == true) {
      $username = strtolower($this->input->post('first_name')) . ' ' . strtolower($this->input->post('last_name'));
      $email = strtolower($this->input->post('email'));
      $password = $this->input->post('password');

      $additional_data = array(
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'company' => $this->input->post('company'),
        'phone' => $this->input->post('phone'),
      );
    }
    if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data)) {
      //check to see if we are creating the user
      //redirect them back to the admin page
      $this->session->set_flashdata('message', $this->ion_auth->messages());
      redirect("auth", 'refresh');
    } else {
      //display the create user form
      //set the flash data error message if there is one
      $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

      $this->data['first_name'] = array(
        'name' => 'first_name',
        'id' => 'first_name',
        'type' => 'text',
        'value' => $this->form_validation->set_value('first_name'),
      );
      $this->data['last_name'] = array(
        'name' => 'last_name',
        'id' => 'last_name',
        'type' => 'text',
        'value' => $this->form_validation->set_value('last_name'),
      );
      $this->data['email'] = array(
        'name' => 'email',
        'id' => 'email',
        'type' => 'text',
        'value' => $this->form_validation->set_value('email'),
      );
      $this->data['company'] = array(
        'name' => 'company',
        'id' => 'company',
        'type' => 'text',
        'value' => $this->form_validation->set_value('company'),
      );
      $this->data['phone'] = array(
        'name' => 'phone',
        'id' => 'phone',
        'type' => 'text',
        'value' => $this->form_validation->set_value('phone'),
      );
      $this->data['password'] = array(
        'name' => 'password',
        'id' => 'password',
        'type' => 'password',
        'value' => $this->form_validation->set_value('password'),
      );
      $this->data['password_confirm'] = array(
        'name' => 'password_confirm',
        'id' => 'password_confirm',
        'type' => 'password',
        'value' => $this->form_validation->set_value('password_confirm'),
      );

      $this->_render_page('auth/create_user', $this->data);
    }
  }

  //edit a user
  function edit_user($id)
  {
    $this->data['title'] = "Edit User";

    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
      redirect('auth', 'refresh');
    }

    $user = $this->ion_auth->user($id)->row();
    $groups = $this->ion_auth->groups()->result_array();
    $currentGroups = $this->ion_auth->get_users_groups($id)->result();

    //validate form input
    $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
    $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
    $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required|xss_clean');
    $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
    $this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

    if (isset($_POST) && !empty($_POST)) {
      // do we have a valid request?
      if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
        show_error($this->lang->line('error_csrf'));
      }

      $data = array(
        'first_name' => $this->input->post('first_name'),
        'last_name' => $this->input->post('last_name'),
        'company' => $this->input->post('company'),
        'phone' => $this->input->post('phone'),
      );

      //Update the groups user belongs to
      $groupData = $this->input->post('groups');

      if (isset($groupData) && !empty($groupData)) {

        $this->ion_auth->remove_from_group('', $id);

        foreach ($groupData as $grp) {
          $this->ion_auth->add_to_group($grp, $id);
        }
      }

      //update the password if it was posted
      if ($this->input->post('password')) {
        $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');

        $data['password'] = $this->input->post('password');
      }

      if ($this->form_validation->run() === TRUE) {
        $this->ion_auth->update($user->id, $data);

        //check to see if we are creating the user
        //redirect them back to the admin page
        $this->session->set_flashdata('message', "User Saved");
        redirect("auth", 'refresh');
      }
    }

    //display the edit user form
    $this->data['csrf'] = $this->_get_csrf_nonce();

    //set the flash data error message if there is one
    $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

    //pass the user to the view
    $this->data['user'] = $user;
    $this->data['groups'] = $groups;
    $this->data['currentGroups'] = $currentGroups;

    $this->data['first_name'] = array(
      'name' => 'first_name',
      'id' => 'first_name',
      'type' => 'text',
      'value' => $this->form_validation->set_value('first_name', $user->first_name),
    );
    $this->data['last_name'] = array(
      'name' => 'last_name',
      'id' => 'last_name',
      'type' => 'text',
      'value' => $this->form_validation->set_value('last_name', $user->last_name),
    );
    $this->data['company'] = array(
      'name' => 'company',
      'id' => 'company',
      'type' => 'text',
      'value' => $this->form_validation->set_value('company', $user->company),
    );
    $this->data['phone'] = array(
      'name' => 'phone',
      'id' => 'phone',
      'type' => 'text',
      'value' => $this->form_validation->set_value('phone', $user->phone),
    );
    $this->data['password'] = array(
      'name' => 'password',
      'id' => 'password',
      'type' => 'password'
    );
    $this->data['password_confirm'] = array(
      'name' => 'password_confirm',
      'id' => 'password_confirm',
      'type' => 'password'
    );

    $this->_render_page('auth/edit_user', $this->data);
  }

  // create a new group
  function create_group()
  {
    $this->data['title'] = $this->lang->line('create_group_title');

    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
      redirect('auth', 'refresh');
    }

    //validate form input
    $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
    $this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

    if ($this->form_validation->run() == TRUE) {
      $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
      if ($new_group_id) {
        // check to see if we are creating the group
        // redirect them back to the admin page
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        redirect("auth", 'refresh');
      }
    } else {
      //display the create group form
      //set the flash data error message if there is one
      $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

      $this->data['group_name'] = array(
        'name' => 'group_name',
        'id' => 'group_name',
        'type' => 'text',
        'value' => $this->form_validation->set_value('group_name'),
      );
      $this->data['description'] = array(
        'name' => 'description',
        'id' => 'description',
        'type' => 'text',
        'value' => $this->form_validation->set_value('description'),
      );

      $this->_render_page('auth/create_group', $this->data);
    }
  }

  //activate the user
  function activate($id, $code = false)
  {
    if ($code !== false) {
      $activation = $this->ion_auth->activate($id, $code);
    } else if ($this->ion_auth->is_admin()) {
      $activation = $this->ion_auth->activate($id);
    }

    if ($activation) {
      //redirect them to the auth page
      $this->session->set_flashdata('message', $this->ion_auth->messages());
      redirect("auth", 'refresh');
    } else {
      //redirect them to the forgot password page
      $this->session->set_flashdata('message', $this->ion_auth->errors());
      redirect("auth/forgot_password", 'refresh');
    }
  }

  //edit a group
  function edit_group($id)
  {
    // bail if no group id given
    if (!$id || empty($id)) {
      redirect('auth', 'refresh');
    }

    $this->data['title'] = $this->lang->line('edit_group_title');

    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
      redirect('auth', 'refresh');
    }

    $group = $this->ion_auth->group($id)->row();

    //validate form input
    $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
    $this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

    if (isset($_POST) && !empty($_POST)) {
      if ($this->form_validation->run() === TRUE) {
        $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

        if ($group_update) {
          $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
        } else {
          $this->session->set_flashdata('message', $this->ion_auth->errors());
        }
        redirect("auth", 'refresh');
      }
    }

    //set the flash data error message if there is one
    $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

    //pass the user to the view
    $this->data['group'] = $group;

    $this->data['group_name'] = array(
      'name' => 'group_name',
      'id' => 'group_name',
      'type' => 'text',
      'value' => $this->form_validation->set_value('group_name', $group->name),
    );
    $this->data['group_description'] = array(
      'name' => 'group_description',
      'id' => 'group_description',
      'type' => 'text',
      'value' => $this->form_validation->set_value('group_description', $group->description),
    );

    $this->_render_page('auth/edit_group', $this->data);
  }

  function _get_csrf_nonce()
  {
    $this->load->helper('string');
    $key = random_string('alnum', 8);
    $value = random_string('alnum', 20);
    $this->session->set_flashdata('csrfkey', $key);
    $this->session->set_flashdata('csrfvalue', $value);

    return array($key => $value);
  }

  function _valid_csrf_nonce()
  {
    if (
      $this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
      $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
    ) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function _render_page($view, $data = null, $render = false)
  {

    $this->viewdata = (empty($data)) ? $this->data : $data;

    $view_html = $this->load->view($view, $this->viewdata, $render);

    if (!$render)
      return $view_html;
  }

  function datatable()
  {

    $this->load->library('datatables');
    $this->datatables->select('USUARIOS.IDCARGO,USUARIOS.DESCRIPCIONCARGO');
    $this->datatables->from('USUARIOS');
    $this->datatables->add_column('edit', '<a href="' . base_url() . 'index.php/cargos/edit/$1">Editar</a>', 'USUARIOS.IDCARGO');
    echo $this->datatables->generate();
  }
}