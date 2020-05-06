<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controller extends CI_Controller {

  public function  __construct() {
    parent::__construct();
    header('Content-Type: application/json');
  }

  public function teste(){
    print_r($this->db->database);
  }

  public function classes($folder, $table = ""){
    $this->generateclassapi->init($folder, $table);
    $this->generateclassview->init($folder, $table);
    $this->generateclassjavascript->init($folder, $table);
    $this->generateclasscontroller->init($folder, $table);
  }

  public function migrationTables($folder, $table = ""){
    $this->generatetabelas->init($folder, $table);
  }
}
