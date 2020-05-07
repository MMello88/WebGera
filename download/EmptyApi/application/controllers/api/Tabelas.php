<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tabelas extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'tabelas';
    $this->nameId = 'TABLE_NAME';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }

  public function setDefaultValue(){
    $_POST['TABLE_DELETED'] = !isset($_POST['TABLE_DELETED']) ? 'FALSE' : $_POST['TABLE_DELETED'];
		
  }

  public function create(){
    $this->form_validation->set_rules('CREATE_TIME', 'CREATE_TIME', 'valid_datetime');
		$this->form_validation->set_rules('UPDATE_TIME', 'UPDATE_TIME', 'valid_datetime');
		$this->form_validation->set_rules('TABLE_COMMENT', 'TABLE_COMMENT', 'required|max_length[2048]');
		$this->form_validation->set_rules('TABLE_DELETED', 'TABLE_DELETED', 'in_list[TRUE,FALSE]');
		$this->form_validation->set_rules('CLASS_NAME', 'CLASS_NAME', 'max_length[64]');
		
    parent::create();
  }

  public function update($Id){
    $this->form_validation->set_rules('CREATE_TIME', 'CREATE_TIME', 'valid_datetime');
		$this->form_validation->set_rules('UPDATE_TIME', 'UPDATE_TIME', 'valid_datetime');
		$this->form_validation->set_rules('TABLE_COMMENT', 'TABLE_COMMENT', 'required|max_length[2048]');
		$this->form_validation->set_rules('TABLE_DELETED', 'TABLE_DELETED', 'in_list[TRUE,FALSE]');
		$this->form_validation->set_rules('CLASS_NAME', 'CLASS_NAME', 'max_length[64]');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
  }
}
