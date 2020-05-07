<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menus extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'menus';
    $this->nameId = 'mns_Id';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }
  
  public function setDefaultValue(){
    $_POST['mns_Ativo'] = !isset($_POST['mns_Ativo']) ? 'True' : $_POST['mns_Ativo'];
  }

  public function create(){
    $this->form_validation->set_rules('mns_Nome', 'Nome', 'required|max_length[250]');
		$this->form_validation->set_rules('mns_Ativo', 'Ativo', 'required|in_list[True,False]');
		$this->form_validation->set_rules('mns_url', 'url', 'required');
		
    parent::create();
  }
  
  public function update($Id){
    $this->form_validation->set_rules('mns_Nome', 'Nome', 'required|max_length[250]');
		$this->form_validation->set_rules('mns_Ativo', 'Ativo', 'required|in_list[True,False]');
		$this->form_validation->set_rules('mns_url', 'url', 'required');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
	}
	
  public function getPerfilMenu($PerfisId){
    $user_data = $this->_apiConfig([
      "methods" => ["GET"],
      "requireAuthorization" => true,
    ]);

    $data = $this->api->getPerfilMenu($user_data["token_data"]["uu_Id"], $PerfisId);
    $this->api_return(
      $data,
      200
    );
  }
}