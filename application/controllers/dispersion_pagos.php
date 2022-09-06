<?php

/**
 * Archivo para la administración de dispersión de pagos 
 *
 * @packageCertificados
 * @subpackage Controllers
 * @author jcdussan
 * @location./application/controllers/dispersion_pago.php
 * @last-modified 06/09/2022
*/

defined('BASEPATH') or exit('No direct script access allowed');
error_reporting(0);

class dispersion_pagos extends MY_Controller {

    function __construct() {
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

    function index() {
        /**
         * Función que renderiza la vista de busqueda de pagos
         * @param null
         * @return void
         */
        try {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login', 'refresh');
            } else {
                $this->template->load($this->template_file, 'dispersion_pagos/buscar');
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Dispersión Pagos';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'dispersion_pagos/error', $this->data);
        }
    }

    function buscar() {
    /**
     * Función que renderiza la vista pagos encontrados
     * @param null
     * @return void
     */
        try {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login', 'refresh');
            } else {
                $this->template->load($this->template_file, 'dispersion_pagos/listado', $this->data);
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Dispersión Pagos';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'dispersion_pagos/error', $this->data);
        }
    }

    function informacion() {
        /**
         * Función que renderiza la vista pagos encontrados
         * @param null
         * @return void
         */
        try {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login', 'refresh');
            } else {
                $this->template->load($this->template_file, 'dispersion_pagos/informacion', $this->data);
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Dispersión Pagos';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'dispersion_pagos/error', $this->data);
        }
    }

    function dispersar_exito() {
        /**
         * Función que renderiza la vista pagos encontrados
         * @param null
         * @return void
         */
        try {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login', 'refresh');
            } else {
                $this->template->load($this->template_file, 'dispersion_pagos/dispersar_exito', $this->data);
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Dispersión Pagos';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'dispersion_pagos/error', $this->data);
        }
    }

    function dispersar_error() {
        /**
         * Función que renderiza la vista pagos encontrados
         * @param null
         * @return void
         */
        try {
            if (!$this->ion_auth->logged_in()) {
                redirect('auth/login', 'refresh');
            } else {
                $this->template->load($this->template_file, 'dispersion_pagos/dispersar_error', $this->data);
            } 
        } catch (Exception $e) {
            $this->data['titulo'] = 'Dispersión Pagos';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'dispersion_pagos/error', $this->data);
        }
    }

    function nuevo_archivo() {
        try {
            if ($this->ion_auth->logged_in()) {
                if ($this->ion_auth->is_admin() || $this->ion_auth->in_menu('gerente_publico/nuevo_archivo')) {
                    $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                    $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">&times;
                    </button>No tiene permisos para acceder a esta área.</div>');
                    redirect(base_url() . 'index.php');
                }
            } else {
                redirect(base_url() . 'index.php/auth/login');
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Cargue archivo Gerente Público';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'gerente_publico/error', $this->data);
        }
    }

    /**
     * Función que permite cargar un nuevo archivo de gerente público
     * @param null
     * @return void
     */
    function cargar_archivo() {
        try {
            if ($this->ion_auth->logged_in()) {
                if ($this->ion_auth->is_admin() || $this->ion_auth->in_menu('gerente_publico/cargar_archivo')) {
                    define("RUTA_FICHERO", "./uploads/gerente-publico/");
                    if (!file_exists('./uploads/gerente-publico')) {
                        mkdir(RUTA_FICHERO, 0777, true);
                    }
                    $nombre_archivo = str_replace (" ", "_", $_FILES['archivo-gerente']['name']);
                    if (file_exists(RUTA_FICHERO . $nombre_archivo)) {
                        rename(RUTA_FICHERO . $nombre_archivo, RUTA_FICHERO . "old_". rand(1,99) . "_" . $nombre_archivo );
                    }
                    $config['upload_path'] = RUTA_FICHERO;
                    $config['allowed_types'] = 'xls|xlsx';
                    $config['max_size'] = 100000;
                    $ruta_carga = 'uploads/gerente-publico/';
                    $usuario = $this->ion_auth->user()->row();
                    $this->load->library('upload', $config);
                    if ( ! $this->upload->do_upload('archivo-gerente'))
                    {
                        $error = array('error' => $this->upload->display_errors());
                        $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $error['error'] . '</div>';
                        $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                        $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                    }
                    else
                    {
                        $data = array('upload_data' => $this->upload->data());
                        $data_archivo = array(
                            'nombre_archivo' => $data['upload_data']['file_name'],
                            'ruta_archivo' => $ruta_carga . $data['upload_data']['file_name'],
                            'estado_archivo' => '1',
                            'creador_archivo' => $usuario -> IDUSUARIO,
                        );
                        $carga = $this->gerente_publico_model->insertar_archivo($data_archivo);
                        if((int)$carga > 0){
                            $exito = array('exito' => $data['upload_data']['file_name']);
                            $this->data['message'] = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se ha cargado el archivo: ' . $exito['exito'] . ' con éxito. </div>';
                            $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                            $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                        } else {
                            $error = array('error' => 'error de registro en base de datos');
                            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $error['error'] . '</div>';
                            $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                            $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                        }
                    }

                } else {
                    $this->session->set_flashdata('message', '<div class = "alert alert-info"><button type = "button" class = "close" data-dismiss = "alert">&times;
                </button>No tiene permisos para acceder a esta área.</div>');
                    redirect(base_url() . 'index.php/cargarextractoasobancaria');
                }
            } else {
                redirect(base_url() . 'index.php/auth/login');
            }

        } catch (Exception $e) {
            $this->data['titulo'] = 'Cargue archivo Gerente Público';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'gerente_publico/error', $this->data);
        }
    }

    /**
     * Función que permite cargar un nuevo archivo de gerente público
     * @param id: identificador único de archivo 
     * @return void
     */
    function inactivar_archivo($id) {
        try {
            if ($this->ion_auth->logged_in()) {
                if ($this->ion_auth->is_admin() || $this->ion_auth->in_menu('gerente_publico/inactivar_archivo')) {
                    $inactivar = $this->gerente_publico_model->inactivar_archivo($id);
                    if ($inactivar === FALSE)
                    {
                        $error = array('error' => 'error de actualización en base de datos');
                        $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $error['error'] . '</div>';
                        $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                        $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                    }
                    else
                    {
                        $exito = array('exito' => $inactivar);
                        $this->data['message'] = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Se ha borrado el archivo ID: ' . $exito['exito'] . ' con éxito. </div>';
                        $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                        $this->template->load($this->template_file, 'gerente_publico/cargar_archivo', $this->data);
                    }

                } else {
                    $this->session->set_flashdata('message', '<div class = "alert alert-info"><button type = "button" class = "close" data-dismiss = "alert">&times;
                </button>No tiene permisos para acceder a esta área.</div>');
                    redirect(base_url() . 'index.php/cargarextractoasobancaria');
                }
            } else {
                redirect(base_url() . 'index.php/auth/login');
            }

        } catch (Exception $e) {
            $this->data['titulo'] = 'Cargue archivo Gerente Público';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'gerente_publico/error', $this->data);
        }
    }

    /**
     * Función que renderiza la vista de archivos cargados
     * @param null
     * @return void
     */
    function consultar_archivos() {
        try {
            if ($this->ion_auth->logged_in()) {
                if ($this->ion_auth->is_admin() || $this->ion_auth->in_menu('gerente_publico/consultar_archivos')) {
                    $this->data['listado'] = $this->gerente_publico_model->listar_archivos();
                    $this->template->load($this->template_file, 'gerente_publico/listado_descargar', $this->data);
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">&times;
                    </button>No tiene permisos para acceder a esta área.</div>');
                    redirect(base_url() . 'index.php');
                }
            } else {
                redirect(base_url() . 'index.php/auth/login');
            }
        } catch (Exception $e) {
            $this->data['titulo'] = 'Cargue archivo Gerente Público';
            $this->data['message'] = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Ha ocurrido un problema de ejecución : ' . $e->getMessage() . '</div>';
            $this->template->load($this->template_file, 'gerente_publico/error', $this->data);
        }
    }

}

        
