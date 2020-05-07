<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfis extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'perfis';
    $this->nameId = 'ps_Id';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }

  public function setDefaultValue(){
    $_POST['ps_Ativo'] = !isset($_POST['ps_Ativo']) ? 'True' : $_POST['ps_Ativo'];
		
  }

  public function create(){
    $this->form_validation->set_rules('ps_Nome', 'ps_Nome', 'required|max_length[250]');
		$this->form_validation->set_rules('ps_Ativo', 'ps_Ativo', 'required|in_list[True,False]');
		$this->form_validation->set_rules('ps_Descricao', 'ps_Descricao', 'required|max_length[255]');
		$this->form_validation->set_rules('ps_tag', 'ps_tag', 'max_length[255]');
		$this->form_validation->set_rules('ps_Imagem', 'ps_Imagem', 'required');
		
    parent::create();
  }

  public function update($Id){
    $this->form_validation->set_rules('ps_Nome', 'ps_Nome', 'required|max_length[250]');
		$this->form_validation->set_rules('ps_Ativo', 'ps_Ativo', 'required|in_list[True,False]');
		$this->form_validation->set_rules('ps_Descricao', 'ps_Descricao', 'required|max_length[255]');
		$this->form_validation->set_rules('ps_tag', 'ps_tag', 'max_length[255]');
		$this->form_validation->set_rules('ps_Imagem', 'ps_Imagem', 'required');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
  }
}
