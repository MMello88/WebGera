<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controller extends CI_Controller {

  public function  __construct() {
    parent::__construct();
    header('Content-Type: application/json');
  }

  public function classes($table = ""){
    $this->generateclassapi->init($table);
    $this->generateclassview->init($table);
    $this->generateclassjavascript->init($table);
    $this->generateclasscontroller->init($table);
  }

  public function migrationTables($table = ""){
    $this->generatetabelas->init($table);
  }
}
