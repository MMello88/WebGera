<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassController{

  protected $CI;
  private $filename;
  
  public function __constructor(){
  }

  private function saveToProject($folder){
    if (!file_exists("C:\\xampp\\htdocs\\{$folder}\\"))
      die("Caminho do Projeto não encontrado");
    $this->filename = "C:\\xampp\\htdocs\\{$folder}\\application\\controllers\\";
  }

  public function init($folder, $nameTable = ""){
    $this->saveToProject($folder);
    $this->CI = &get_instance();
    $tables = $this->CI->gera->getTablesPai($nameTable);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);

      $tabelasFilho = [];
      if($table->HAS_CHILD === 'TRUE'){
        $tabelasFilho = $this->CI->gera->getFKFilhos($table->TABLE_NAME);
      }

      $this->buildController($table, $fields, $tabelasFilho);
      
      if($table->HAS_CHILD === 'TRUE'){
        $this->initChild($tabelasFilho);
      }
    }
  }
  public function initChild($tabelasFilho){
    foreach ($tabelasFilho  as $tabelaFilho) {

      $tabelasFilhoFilho = [];
      if($tabelaFilho->TABLE->HAS_CHILD === 'TRUE'){
        $tabelasFilhoFilho = $this->CI->gera->getFKFilhos($tabelaFilho->TABLE->TABLE_NAME);
      }

      $this->buildControllerChild($tabelaFilho, $tabelasFilhoFilho);
      
      if($tabelaFilho->TABLE->HAS_CHILD === 'TRUE'){
        $this->initChild($tabelasFilhoFilho);
      }
    }
  }

  private function buildController($table, $fields, $tabelasFilho){
    $fieldPK = $this->getFieldPK($fields);
    $nameClass = ucfirst($table->TABLE_NAME);
    $scripts = $this->getScriptJS($tabelasFilho);
    $comeFromChild = $this->getComeFromChild($table);

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
        \$this->data['nameView'] = 'create';
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
        \$this->data['nameView'] = 'edit';
        if (\$this->session->flashdata('response')){
          \$this->data['response'] = \$this->session->flashdata('response');
{$comeFromChild}
        } else {
          \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);
        }

{$scripts}
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
        \$this->data['nameView'] = 'view';
        \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);

{$scripts}
        \$this->load->view('dashboard/template/header', \$this->data);
        \$this->load->view('api/{$table->TABLE_NAME}/View{$nameClass}', \$this->data);
        \$this->load->view('dashboard/template/footer', \$this->data);
      }
    }
    ";
    $this->saveFile($nameClass, $controller);
  }

  private function buildControllerChild($tabelaFilho, $tabelasFilhoFilho){
    $table = $tabelaFilho->TABLE;
    $fields = $tabelaFilho->TABLE->FIELDS;

    $scripts = "";
    foreach ($tabelasFilhoFilho as $tabelaFilhoFilho) {
      $scripts .= "\t\t\t\t\$this->scripts('assets/javascript/api/{$tabelaFilhoFilho->TABLE->TABLE_NAME}/{$tabelaFilhoFilho->TABLE->TABLE_NAME}.js');\n";
    }

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
        /*
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
        */
      }
    
      public function get(\$IdParent, \$Id = ''){
        if(empty(\$Id))
          echo json_encode(['data' => \$this->sendGet('api/{$table->TABLE_NAME}/getByParent/'.\$IdParent, \$this->data['login']->data->token)->data]);
        else 
          echo json_encode(['data' => \$this->sendGet('api/{$table->TABLE_NAME}/getByParent/'.\$IdParent.'/'.\$Id, \$this->data['login']->data->token)->data]);
      }
    
      public function create(\$parentView, \$IdParent){
        \$this->data['nameView'] = 'create';
        \$this->data['IdParent'] = \$IdParent;
        \$this->data['parentView'] = \$parentView;
        if(\$this->session->flashdata('response'))
          \$this->data['response'] = \$this->session->flashdata('response');
        
        \$this->load->view('dashboard/template/header', \$this->data);
        \$this->load->view('api/{$table->TABLE_NAME}/Create{$nameClass}', \$this->data);
        \$this->load->view('dashboard/template/footer', \$this->data);
      }
    
      public function add(\$parentView, \$IdParent){
        if(\$_POST){
          \$salvarEVoltar = isset(\$_POST['cbxSaveBack']) ? TRUE : FALSE;
          unset(\$_POST['cbxSaveBack']);

          \$response = \$this->sendPost('api/{$table->TABLE_NAME}/create', \$this->data['login']->data->token, \$this->input->post(), true);

          if(\$response['status'] == 'FALSE'){
            \$response['data'] = \$_POST;
          } else {
            \$response['comeFromChild'] = 'TRUE';
          }
          
          if(\$salvarEVoltar) \$response['data']['cbxSaveBack'] = 'on';
          
          \$this->session->set_flashdata('response', \$response); 

          if(\$response['status'] == 'FALSE'){
            redirect('{$table->TABLE_NAME}/create/'.\$parentView.'/'.\$IdParent);
          } else {
            \$salvarEVoltar ? redirect('{$tabelaFilho->REFERENCED_TABLE_NAME}/'.\$parentView.'/'.\$IdParent) : redirect('{$table->TABLE_NAME}/edit/'.\$parentView.'/'.\$IdParent.'/'.\$response['data'][0]['{$fieldPK->COLUMN_NAME}']);
          }
        }
      }
    
      public function edit(\$parentView, \$IdParent, \$Id){
        \$this->data['nameView'] = 'edit';
        \$this->data['IdParent'] = \$IdParent;
        \$this->data['parentView'] = \$parentView;
        if (\$this->session->flashdata('response')){
          \$this->data['response'] = \$this->session->flashdata('response');
        } else {
          \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);
        }
        
{$scripts}
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
    
      public function update(\$parentView, \$IdParent, \$Id){
        if(\$_POST){
          \$salvarEVoltar = isset(\$_POST['cbxSaveBack']) ? TRUE : FALSE;
          unset(\$_POST['cbxSaveBack']);

          \$response = \$this->sendPost('api/{$table->TABLE_NAME}/update/'.\$Id, \$this->data['login']->data->token, \$this->input->post(), true);

          if(\$response['status'] == 'FALSE'){
            \$_POST['{$fieldPK->COLUMN_NAME}'] = \$Id;
            \$response['data'][0] = \$_POST;
          } else {
            \$response['comeFromChild'] = 'TRUE';
          }

          if(\$salvarEVoltar) \$response['data']['cbxSaveBack'] = 'on';

          \$this->session->set_flashdata('response', \$response); 
          
          if(\$response['status'] == 'FALSE'){
            redirect('{$table->TABLE_NAME}/edit/'.\$parentView.'/'.\$IdParent.'/'.\$Id);
          } else {
            \$salvarEVoltar ? redirect('{$tabelaFilho->REFERENCED_TABLE_NAME}/'.\$parentView.'/'.\$IdParent) : redirect('{$table->TABLE_NAME}/edit/'.\$parentView.'/'.\$IdParent.'/'.\$Id);
          }
        }
      }
    
      public function delete(\$parentView, \$IdParent){
        if(\$_POST){
          \$Id = \$_POST['Id'];
    
          \$response = \$this->sendGet('api/{$table->TABLE_NAME}/getByParent/'.\$IdParent.'/'.\$Id, \$this->data['login']->data->token, true);    
    
          if(empty(\$response['data'])){
            \$this->data['heading'] = 'Dado não encontrado.';
            \$this->data['message'] = 'Não foi encontrado nenhum dado para este identificador.';
            \$this->load->view('errors/html/my_error_404', \$this->data);
          } else {
            \$response = \$this->sendDelete('api/{$table->TABLE_NAME}/delete/'.\$Id, \$this->data['login']->data->token, true);
            \$response['comeFromChild'] = 'TRUE';
            \$this->session->set_flashdata('response', \$response); 
            redirect('{$tabelaFilho->REFERENCED_TABLE_NAME}/'.\$parentView.'/'.\$IdParent);
          }
        }
      }

      public function view(\$parentView, \$IdParent, \$Id){
        \$this->data['nameView'] = 'view';
        \$this->data['IdParent'] = \$IdParent;
        \$this->data['parentView'] = \$parentView;
        \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/getByParent/'.\$IdParent.'/'.\$Id, \$this->data['login']->data->token, true);

{$scripts}
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

  private function getComeFromChild($table){
    $comeFromChild = "";
    if($table->HAS_CHILD === 'TRUE')
      $comeFromChild = "
          if(isset(\$this->data['response']['comeFromChild'])){
            \$this->data['response'] = \$this->sendGet('api/{$table->TABLE_NAME}/get/'.\$Id, \$this->data['login']->data->token, true);
          }
      ";
    return $comeFromChild;
  }

  private function getScriptJS($tabelasFilho){
    $scripts = "";
    foreach ($tabelasFilho as $tabelaFilho) {
      $scripts .= "\t\t\t\t\$this->scripts('assets/javascript/api/{$tabelaFilho->TABLE->TABLE_NAME}/{$tabelaFilho->TABLE->TABLE_NAME}.js');\n";
    }
    return $scripts;
  }

  private function getFieldPK($fields){
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY == "PRI"){
        return $field;
      }
    }
  }
}