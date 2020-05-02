<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassController{

  protected $CI;
  private $filename = "C:\\xampp\\htdocs\\WebSite\\application\\controllers\\";
  
  public function __constructor(){
  }

  public function init($nameTable = ""){
    $this->CI = &get_instance();
    $tables = $this->CI->gera->getTables($nameTable);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);
      $this->buildController($table, $fields);
    }
  }

  private function buildController($table, $fields){
    $fieldPK = $this->getFieldPK($fields);
    $nameClass = ucfirst($table->TABLE_NAME);
    $controller = "
    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    
    class $nameClass extends MY_Controller {
      
      public function  __construct() {
        parent::__construct();
      }
    
      public function index(){
        if (\$this->session->flashdata('response')){
          \$this->data['response'] = \$this->session->flashdata('response');
          if(\$this->data['response']['status'] == 'FALSE'){
            \$this->data['response']['data'] = \$this->sendGet('api/{$table->TABLE_NAME}/get', \$this->data['login']->data->token, true)->data;
          }
        } else {
          \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get', \$this->data['login']->data->token, true);
        }
    
        \$this->scripts('assets/javascript/api/{$table->TABLE_NAME}/{$table->TABLE_NAME}.js');
        \$this->load->view('dashboard/template/header', \$this->data);
        \$this->load->view('api/{$table->TABLE_NAME}/Grid{$nameClass}', \$this->data);
        \$this->load->view('dashboard/template/footer', \$this->data);
      }
    
      public function get(){
        echo json_encode(['data' => \$this->sendGet('api/{$table->TABLE_NAME}/get', \$this->data['login']->data->token)->data]);
      }
    
      public function create(){
        if(\$this->session->flashdata('response'))
          \$this->data['response'] = \$this->session->flashdata('response');
        
        \$this->load->view('dashboard/template/header', \$this->data);
        \$this->load->view('api/{$table->TABLE_NAME}/Create{$nameClass}', \$this->data);
        \$this->load->view('dashboard/template/footer', \$this->data);
      }
    
      public function add(){
        if(\$_POST){
          \$salvarEVoltar = isset(\$_POST['cbxSaveBack']) ? TRUE : FALSE;
          unset(\$_POST['cbxSaveBack']);

          \$response = \$this->sendPost('api/{$table->TABLE_NAME}/create', \$this->data['login']->data->token, \$this->input->post(), true);

          if(\$response['status'] == 'FALSE')
            \$response['data'] = \$_POST;
          
          if(\$salvarEVoltar) \$response['data']['cbxSaveBack'] = 'on';
          
          \$this->session->set_flashdata('response', \$response); 

          if(\$response['status'] == 'FALSE'){
            redirect('{$table->TABLE_NAME}/create');
          } else {
            \$salvarEVoltar ? redirect('{$table->TABLE_NAME}') : redirect('{$table->TABLE_NAME}/edit/'.\$response['data'][0]['{$fieldPK->COLUMN_NAME}']);
          }
        }
      }
    
      public function edit(\$Id){
        if (\$this->session->flashdata('response')){
          \$this->data['response'] = \$this->session->flashdata('response');
        } else {
          \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);
        }
    
        if(empty(\$this->data['response']['data'])){
          \$this->data['heading'] = 'Dado não encontrado.';
          \$this->data['message'] = 'Não foi encontrado nenhum dado para este identificador.';
          \$this->load->view('errors/html/my_error_404', \$this->data);
        } else {
          \$this->load->view('dashboard/template/header', \$this->data);
          \$this->load->view('api/{$table->TABLE_NAME}/Edit{$nameClass}', \$this->data);
          \$this->load->view('dashboard/template/footer', \$this->data);
        }
      }
    
      public function update(\$Id){
        if(\$_POST){
          \$salvarEVoltar = isset(\$_POST['cbxSaveBack']) ? TRUE : FALSE;
          unset(\$_POST['cbxSaveBack']);

          \$response = \$this->sendPost('api/{$table->TABLE_NAME}/update/'.\$Id, \$this->data['login']->data->token, \$this->input->post(), true);

          if(\$response['status'] == 'FALSE'){
            \$_POST['{$fieldPK->COLUMN_NAME}'] = \$Id;
            \$response['data'][0] = \$_POST;
          }

          if(\$salvarEVoltar) \$response['data']['cbxSaveBack'] = 'on';

          \$this->session->set_flashdata('response', \$response); 
          
          if(\$response['status'] == 'FALSE'){
            redirect('{$table->TABLE_NAME}/edit/'.\$Id);
          } else {
            \$salvarEVoltar ? redirect('{$table->TABLE_NAME}') : redirect('{$table->TABLE_NAME}/edit/'.\$Id);
          }
        }
      }
    
      public function delete(){
        if(\$_POST){
          \$Id = \$_POST['Id'];
    
          \$response = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);    
    
          if(empty(\$response['data'])){
            \$this->data['heading'] = 'Dado não encontrado.';
            \$this->data['message'] = 'Não foi encontrado nenhum dado para este identificador.';
            \$this->load->view('errors/html/my_error_404', \$this->data);
          } else {
            \$response = \$this->sendDelete('api/{$table->TABLE_NAME}/delete/'.\$Id, \$this->data['login']->data->token, true);
            \$this->session->set_flashdata('response', \$response); 
            redirect('{$table->TABLE_NAME}');
          }
        }
      }

      public function view(\$Id){
        \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);

        \$this->load->view('dashboard/template/header', \$this->data);
        \$this->load->view('api/{$table->TABLE_NAME}/View{$nameClass}', \$this->data);
        \$this->load->view('dashboard/template/footer', \$this->data);
      }
    }
    ";
    $this->saveFile($nameClass, $controller);
  }

  private function saveFile($class, $txt){
    if (!in_array($class, ['Dashboard', 'Perfis', 'User', 'Users', 'Welcome'])){
      $filename = $this->filename . $class . ".php";

      $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
      fwrite($file, $txt);
      fclose($file);
    }
  }

  private function getFieldPK($fields){
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY == "PRI"){
        return $field;
      }
    }
  }
}