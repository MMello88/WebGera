<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'users';
    $this->nameId = 'uu_Id';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }
  
  public function setDefaultValue(){
    $_POST['uu_Ativo'] = !isset($_POST['uu_Ativo']) ? 'True' : $_POST['uu_Ativo'];
		$_POST['uu_Criacao'] = !isset($_POST['uu_Criacao']) ? date('Y-m-d H:i:s') : $_POST['uu_Criacao'];
    $_POST['uu_Senha'] = !isset($_POST['uu_Senha']) ? "" : md5($_POST['uu_Senha']);
    $_POST['uu_SenhaConf'] = !isset($_POST['uu_SenhaConf']) ? "" : md5($_POST['uu_SenhaConf']);
  }

  public function create(){
    $this->_apiConfig([
      "methods" => ["POST"],
    ]);

    $this->api_return(
      ["status" => "FALSE", "error" => "", "message" => "Utilize o método add para cadastrar o usuário.", "method" => "POST"],
      422
    );
		
  }

  public function updatePassword($Id){
    $this->form_validation->set_rules('uu_Senha', 'Senha', 'required|min_length[6]|max_length[64]');
    $this->form_validation->set_rules('uu_SenhaConf', 'Senha Confirmation', 'required|matches[uu_Senha]|max_length[64]');
    
    if ($this->form_validation->run() == TRUE){
      $_POST['uu_Senha'] = !isset($_POST['uu_Senha']) ? "" : md5($_POST['uu_Senha']);
      unset($_POST["uu_SenhaConf"]);

      $this->api->update($this->table, $_POST, [$this->nameId => $Id]);
      $data = $this->api->get($this->table, [$this->nameId => $Id]);
      $this->api_return(
        ["status" => "TRUE", "data" => $data, "message" => "Senha alteração com sucesso!", "method" => "POST"],
        200
      );
    } else {
      $this->api_return(
        [
          "status" => "FALSE", 
          "error" => $this->form_validation->error_array(),
          "message" => "Erro ao validar o Formulário.", 
          "method" => "POST"
        ],
        422
      );
    }
  }
  
  public function update($Id){
    $this->form_validation->set_rules('uu_Nome', 'Nome', 'required|max_length[350]');
		$this->form_validation->set_rules('uu_Sobrenome', 'Sobrenome', 'required|max_length[250]');
		$this->form_validation->set_rules('uu_DataNascimento', 'DataNascimento', 'required|valid_date');
		$this->form_validation->set_rules('uu_Ativo', 'Ativo', 'required|in_list[True,False]');
		$this->form_validation->set_rules('uu_Email', 'Email', 'required|valid_email|max_length[250]');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
  }

  public function add(){
    $user_data = $this->_apiConfig([
      "methods" => ["POST"],
    ]);

    $this->form_validation->set_rules('uu_Nome', 'Nome', 'required|max_length[350]');
    $this->form_validation->set_rules('uu_Email', 'Email', 'required|max_length[250]|valid_email|is_unique[users.uu_Email]');
    $this->form_validation->set_rules('uu_Senha', 'Senha', 'required|min_length[6]|max_length[64]');
    $this->form_validation->set_rules('uu_SenhaConf', 'Senha Confirmation', 'required|matches[uu_Senha]|max_length[64]');

    $this->setDefaultValue();
    
    if ($this->form_validation->run() == TRUE){
      
      unset($_POST["uu_SenhaConf"]);
      $Id = $this->api->create($this->table, $_POST);
      if (is_numeric($Id)) {
        $data = $this->api->get($this->table, [$this->nameId => $Id]);
        unset($data[0]->uu_Senha);
        $token = $this->authorization_token->generateToken($data);
        $data[0]->token = $token;
        $this->api_return(
          ["status" => "TRUE", "data" => $data, "message" => "Cadastro realizado com sucesso!", "method" => "POST"],
          200
        );
      }
        
    
    } else {

      $this->api_return(
        [
          "status" => "FALSE", 
          "error" => $this->form_validation->error_array(),
          "message" => "Erro ao validar o Formulário.", 
          "method" => "POST"
        ],
        422
      );
    }
  }

  public function forgot($paraHash = ""){
    $user_data = $this->_apiConfig([
      "methods" => ["POST"],
    ]);

    if(empty($paraHash)){

      $this->form_validation->set_rules('uu_Email', 'Email', 'required|max_length[250]|valid_email');

      if ($this->form_validation->run() == TRUE){
        $data = $this->api->forgot($_POST["uu_Email"]);
        if($data["status"] == "FALSE"){
          $this->api_return($data,422);
        } else {
          $this->sendemail->enviarEmailRecuperarSenha($data["data"]["uu_Nome"], $data["data"]["uu_Email"], $data["data"]["uu_IdReset"]);
          $this->api_return($data,200);
        }
      } else {
        $this->api_return(
          [
            "status" => "FALSE", 
            "error" => $this->form_validation->error_array(),
            "message" => "Erro ao validar o Formulário.",
            "method" => "POST"
          ],
          422
        );
      }
    } else {

      $this->form_validation->set_rules('uu_Senha', 'Senha', 'required|min_length[6]|max_length[64]');
      $this->form_validation->set_rules('uu_SenhaConf', 'Senha Confirmation', 'required|matches[uu_Senha]|max_length[64]');

      if ($this->form_validation->run() == TRUE){
        $data = $this->api->recover($paraHash, md5($_POST["uu_Senha"]));
        $this->api_return($data,200);
      } else {
        $this->api_return(
          [
            "status" => "FALSE", 
            "error" => $this->form_validation->error_array(),
            "message" => "Erro ao validar o formulário.",
            "method" => "POST"
          ],
          422
        );
      }
    }
  }
}