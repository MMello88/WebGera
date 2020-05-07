<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Foreignkeys extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'foreignkeys';
    $this->nameId = 'fks_Id';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }

  public function setDefaultValue(){
    $_POST['COLUMN_DELETED'] = !isset($_POST['COLUMN_DELETED']) ? 'FALSE' : $_POST['COLUMN_DELETED'];
		$_POST['HAS_PARENT'] = !isset($_POST['HAS_PARENT']) ? ''FALSE'' : $_POST['HAS_PARENT'];
		
  }

  public function create(){
    $this->form_validation->set_rules('CONSTRAINT_NAME', 'CONSTRAINT_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('TABLE_NAME', 'TABLE_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_NAME', 'COLUMN_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('REFERENCED_TABLE_NAME', 'REFERENCED_TABLE_NAME', 'max_length[64]');
		$this->form_validation->set_rules('REFERENCED_COLUMN_NAME', 'REFERENCED_COLUMN_NAME', 'max_length[64]');
		$this->form_validation->set_rules('UPDATE_RULE', 'UPDATE_RULE', 'max_length[64]');
		$this->form_validation->set_rules('DELETE_RULE', 'DELETE_RULE', 'max_length[64]');
		$this->form_validation->set_rules('COLUMN_DELETED', 'COLUMN_DELETED', 'required|in_list[TRUE,FALSE]');
		$this->form_validation->set_rules('HAS_PARENT', 'HAS_PARENT', 'max_length[64]');
		
    parent::create();
  }

  public function update($Id){
    $this->form_validation->set_rules('CONSTRAINT_NAME', 'CONSTRAINT_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('TABLE_NAME', 'TABLE_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_NAME', 'COLUMN_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('REFERENCED_TABLE_NAME', 'REFERENCED_TABLE_NAME', 'max_length[64]');
		$this->form_validation->set_rules('REFERENCED_COLUMN_NAME', 'REFERENCED_COLUMN_NAME', 'max_length[64]');
		$this->form_validation->set_rules('UPDATE_RULE', 'UPDATE_RULE', 'max_length[64]');
		$this->form_validation->set_rules('DELETE_RULE', 'DELETE_RULE', 'max_length[64]');
		$this->form_validation->set_rules('COLUMN_DELETED', 'COLUMN_DELETED', 'required|in_list[TRUE,FALSE]');
		$this->form_validation->set_rules('HAS_PARENT', 'HAS_PARENT', 'max_length[64]');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
  }
}
