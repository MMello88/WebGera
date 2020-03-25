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
    $sql = "SELECT *,(SELECT k.COLUMN_NAME
                        FROM information_schema.KEY_COLUMN_USAGE k
                       WHERE k.table_schema = 'matilab872_gestao' 
                         AND k.table_name = '$table'
                         AND k.REFERENCED_TABLE_NAME = 'users'
                         AND k.COLUMN_NAME = c.COLUMN_NAME) COLUMN_FK
            FROM information_schema.COLUMNS c
           WHERE c.table_schema = 'matilab872_gestao' 
             AND c.table_name = '$table'";
    return $this->db->query($sql)->result();
  }
}