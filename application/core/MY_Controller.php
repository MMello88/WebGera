<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class MY_Controller extends CI_Controller {
  
  protected $table;
  protected $nameId;

  public function  __construct() {
    parent::__construct();
    header('Content-Type: application/json');
  }

  protected function get($Id = '', $date = ''){
    $where = "";
    $where.= !empty($Id) || $Id > 0 ? " {$this->table}.{$this->nameId} = {$Id} and" : "";
    $where.= !empty($date) ? " '{$date}' between DtIni and IF(ISNULL(DtFin),SYSDATE(),DtFin) and" : "";
    $where = !empty($where) ? substr($where, 0, -3) : "";

    echo json_encode($this->api->get($this->table, $where));
  }

  protected function create(){
  	if ($this->form_validation->run() == TRUE){
      $this->setDefaultValue();
      $Id = $this->api->create($this->table, $_POST);
      if (is_numeric($Id)) 
      	$this->get($Id);
    } else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity');
      echo json_encode(
        [
          "Validation" => $this->form_validation->error_array()
        ]
      );
    }
  }

  protected function update($Id){
    if(!is_numeric($Id)){
      header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity');
      echo json_encode(
        [
          "Validation" => ["Id" => "The Id field must contain only numbers."]
        ]
      );
    }

    if ($this->form_validation->run() == TRUE){
      $this->api->update($this->table, $_POST, [$this->nameId => $Id]);
      $this->get($Id);
    } else {
      header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity');
      echo json_encode(
        [
          "Validation" => $this->form_validation->error_array()
        ]
      );
    }
  }

  protected function delete($Id){
    if(!is_numeric($Id)){
      header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity');
      echo json_encode(
        [
          "Validation" => ["Id" => "The Id field must contain only numbers."]
        ]
      );
    }
    $this->api->delete($this->table, [$this->nameId => $Id]);
  }

	abstract function setDefaultValue(); 
}