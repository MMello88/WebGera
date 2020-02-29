<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gera_model extends CI_Model {

  public function  __construct() {
      parent::__construct();
  }

  public function getTables($table = ""){
    if(!empty($table)){
      $sql = "SELECT *
                FROM information_schema.TABLES
                WHERE table_schema = 'matilab872_gestao'
                  AND table_name = '$table'";
      return $this->db->query($sql)->result();
    } else {
      $sql = "SELECT *
                FROM information_schema.TABLES
                WHERE table_schema = 'matilab872_gestao'";
      return $this->db->query($sql)->result();
    }
  }

  public function getFields($table){
    $sql = "SELECT *
              FROM information_schema.COLUMNS 
             WHERE table_schema = 'matilab872_gestao' 
               AND table_name = '$table'";
    return $this->db->query($sql)->result();
  }
}