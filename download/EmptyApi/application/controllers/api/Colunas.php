<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Colunas extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    $this->table = 'colunas';
    $this->nameId = 'cln_Id';
    $this->usersId = '';
    $this->joins = [
    ];
  }

  public function get($Id = '', $date = ''){
    parent::get($Id, $date);
  }

  public function setDefaultValue(){
    
  }

  public function create(){
    $this->form_validation->set_rules('TABLE_NAME', 'TABLE_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_NAME', 'COLUMN_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('IS_NULLABLE', 'IS_NULLABLE', 'required|max_length[3]');
		$this->form_validation->set_rules('DATA_TYPE', 'DATA_TYPE', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_TYPE', 'COLUMN_TYPE', 'required');
		$this->form_validation->set_rules('COLUMN_KEY', 'COLUMN_KEY', 'required|max_length[3]');
		$this->form_validation->set_rules('COLUMN_COMMENT', 'COLUMN_COMMENT', 'required|max_length[1024]');
		$this->form_validation->set_rules('REFERENCED_TABLE_NAME', 'REFERENCED_TABLE_NAME', 'max_length[64]');
		$this->form_validation->set_rules('REFERENCED_COLUMN_NAME', 'REFERENCED_COLUMN_NAME', 'max_length[64]');
		
    parent::create();
  }

  public function update($Id){
    $this->form_validation->set_rules('TABLE_NAME', 'TABLE_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_NAME', 'COLUMN_NAME', 'required|max_length[64]');
		$this->form_validation->set_rules('IS_NULLABLE', 'IS_NULLABLE', 'required|max_length[3]');
		$this->form_validation->set_rules('DATA_TYPE', 'DATA_TYPE', 'required|max_length[64]');
		$this->form_validation->set_rules('COLUMN_TYPE', 'COLUMN_TYPE', 'required');
		$this->form_validation->set_rules('COLUMN_KEY', 'COLUMN_KEY', 'required|max_length[3]');
		$this->form_validation->set_rules('COLUMN_COMMENT', 'COLUMN_COMMENT', 'required|max_length[1024]');
		$this->form_validation->set_rules('REFERENCED_TABLE_NAME', 'REFERENCED_TABLE_NAME', 'max_length[64]');
		$this->form_validation->set_rules('REFERENCED_COLUMN_NAME', 'REFERENCED_COLUMN_NAME', 'max_length[64]');
		
    parent::update($Id);
  }

  public function delete($Id){
    parent::delete($Id);
  }
}
