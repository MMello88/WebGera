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
    $sql = "SELECT *,CASE WHEN c.COLUMN_KEY IN ('MUL') 
                          THEN (SELECT k.COLUMN_NAME
                                  FROM information_schema.KEY_COLUMN_USAGE k
                                 WHERE k.table_schema = 'matilab872_gestao' 
                                   AND k.table_name = '$table'
                                   AND k.COLUMN_NAME = c.COLUMN_NAME) 
                           ELSE NULL END COLUMN_FK,
                      CASE WHEN c.COLUMN_KEY IN ('MUL') 
                           THEN (SELECT k.REFERENCED_TABLE_NAME
                                   FROM information_schema.KEY_COLUMN_USAGE k
                                  WHERE k.table_schema = 'matilab872_gestao' 
                                    AND k.table_name = '$table'
                                    AND k.COLUMN_NAME = c.COLUMN_NAME)
                          ELSE NULL END TABLE_FK,
                      CASE WHEN c.COLUMN_KEY IN ('MUL') 
                           THEN (SELECT k.REFERENCED_COLUMN_NAME
                                   FROM information_schema.KEY_COLUMN_USAGE k
                                  WHERE k.table_schema = 'matilab872_gestao' 
                                    AND k.table_name = '$table'
                                    AND k.COLUMN_NAME = c.COLUMN_NAME)
                          ELSE NULL END COLUMN_FK_PRI
            FROM information_schema.COLUMNS c
           WHERE c.table_schema = 'matilab872_gestao' 
             AND c.table_name = '$table'";
    return $this->db->query($sql)->result();
  }

  public function getTableReferences($table){
    $sql = "SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.key_column_usage 
          WHERE table_schema = 'matilab872_gestao' 
            AND table_name = '{$table}'
            AND constraint_name <> 'PRIMARY'";
    return $this->db->query($sql)->result();
  }
}