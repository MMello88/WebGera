<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gera_model extends CI_Model {

  public function  __construct() {
      parent::__construct();
  }

  /**
   * 
   * Consultas
   * 
   */
  public function getTables($table = ""){
    $sql = "SELECT T.*, 
                   (SELECT CASE WHEN COUNT(*) = 0 THEN 'FALSE' ELSE 'TRUE' END
                      FROM FOREIGNKEYS F
                     WHERE F.HAS_PARENT = 'TRUE'
                       AND F.REFERENCED_TABLE_NAME = T.TABLE_NAME) HAS_CHILD
              FROM TABELAS T";

    $sql .= !empty($table) ? " WHERE T.TABLE_NAME = '$table'" : "";

    return $this->db->query($sql)->result();
  }
  
  public function getTablesPai($table = ""){
    $sql = "SELECT T.*, 
                   (SELECT CASE WHEN COUNT(*) = 0 THEN 'FALSE' ELSE 'TRUE' END
                      FROM FOREIGNKEYS F
                     WHERE F.HAS_PARENT = 'TRUE'
                       AND F.REFERENCED_TABLE_NAME = T.TABLE_NAME) HAS_CHILD
              FROM TABELAS T
             WHERE NOT EXISTS (SELECT F.TABLE_NAME
                                 FROM FOREIGNKEYS F
                                WHERE F.HAS_PARENT = 'TRUE'
                                  AND F.TABLE_NAME = T.TABLE_NAME)";
    if(!empty($table)){
      $sql .= " AND T.TABLE_NAME = '$table'";
    }
    
    return $this->db->query($sql)->result();
  }

  public function getFields($table){
    $sql = "SELECT *
              FROM COLUNAS
             WHERE TABLE_NAME = '$table'";
    return $this->db->query($sql)->result();
  }

  public function getFKReferences($table){
    $sql = "SELECT *
              FROM FOREIGNKEYS
             WHERE TABLE_NAME = '$table'";
    return $this->db->query($sql)->result();
  }
  /**getTablesFilho PARA getFKFilhos */
  public function getFKFilhos($table){
    $sql = "SELECT *
              FROM FOREIGNKEYS
             WHERE REFERENCED_TABLE_NAME = '$table'
               AND HAS_PARENT = 'TRUE'";
    $FKs = $this->db->query($sql)->result();
    foreach ($FKs as $key => $FK) {
      $FKs[$key]->TABLE = $this->getTables($FK->TABLE_NAME)[0];
      $FKs[$key]->TABLE->FIELDS = $this->getFields($FK->TABLE_NAME);
      $FKs[$key]->TABLE->FOREIGNKEYS = $this->getFKReferences($FK->TABLE_NAME);
      $FKs[$key]->REFERENCED_TABLE = $this->getTables($FK->REFERENCED_TABLE_NAME)[0];
    }
    return $FKs;
  }

  /*
   *
   * Ações Update/Insert nas Tabelas, Colunas e FK 
   * 
   */

  public function insert($table, $array){
    $this->db->insert($table, $array);
  }
  
  public function insert_batch($table, $array){
    $this->db->insert_batch($table, $array);
  }

  public function update($table, $array, $where){
    $this->db->update($table, $array, $where);
  }

  public function update_batch($table, $array, $where){
    $this->db->update_batch($table, $array, $where);
  }

  /*
   *
   * Funções Broadcast
   * 
   */
  public function getTablesTo($table = ""){
    //drives: cubrid, ibase, mssql, mysql, mysqli, oci8, odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
    switch ($this->db->dbdriver) {
      case 'mysql':
        return $this->getTablesToMysql($table);
        break;
      case 'mysqli':
        return $this->getTablesToMysql($table);
        break;
      case 'mssql':
        # code...
        break;
      case 'oci8':
        # code...
        break;
    }
    
  }

  public function getFieldsTo($table){
    //drives: cubrid, ibase, mssql, mysql, mysqli, oci8, odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
    switch ($this->db->dbdriver) {
      case 'mysql':
        return $this->getFieldsToMysql($table);
        break;
      case 'mysqli':
        return $this->getFieldsToMysql($table);
        break;
      case 'mssql':
        # code...
        break;
      case 'oci8':
        # code...
        break;
    }
  }

  public function getFKReferencesTo($table){
    //drives: cubrid, ibase, mssql, mysql, mysqli, oci8, odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
    switch ($this->db->dbdriver) {
      case 'mysql':
        return $this->getFKReferencesToMysql($table);
        break;
      case 'mysqli':
        return $this->getFKReferencesToMysql($table);
        break;
      case 'mssql':
        # code...
        break;
      case 'oci8':
        # code...
        break;
    } 
  }

  /*
   *
   * Mysql Model Querys
   * 
   */
  public function getTablesToMysql($table = ""){
    $sql = "SELECT TABLE_NAME, 
                   AUTO_INCREMENT, 
                   CREATE_TIME, 
                   UPDATE_TIME, 
                   TABLE_COMMENT
              FROM information_schema.TABLES
              WHERE table_schema = 'matilab872_gestao'
                AND TABLE_NAME NOT IN ('api_keys', 'api_limit', 'tabelas', 'colunas', 'foreignkeys', 'menus', 'users', '__efmigrationshistory') ";
    $sql .= !empty($table) ? " AND table_name = '$table'" : "";
    return $this->db->query($sql)->result();
  }

  public function getFieldsToMysql($table){
    $sql = "SELECT TABLE_NAME, 
                   COLUMN_NAME,
                   IS_NULLABLE,
                   DATA_TYPE,
                   CHARACTER_MAXIMUM_LENGTH,
                   CHARACTER_OCTET_LENGTH,
                   NUMERIC_PRECISION,
                   NUMERIC_SCALE,
                   COLUMN_TYPE,
                   COLUMN_KEY,
                   COLUMN_COMMENT,
                   COLUMN_DEFAULT,
                  CASE WHEN c.COLUMN_KEY IN ('MUL') 
                        THEN (SELECT k.REFERENCED_TABLE_NAME
                                FROM information_schema.KEY_COLUMN_USAGE k
                              WHERE k.table_schema = 'matilab872_gestao' 
                                AND k.table_name = c.table_name
                                AND k.COLUMN_NAME = c.COLUMN_NAME)
                      ELSE NULL END REFERENCED_TABLE_NAME,
                  CASE WHEN c.COLUMN_KEY IN ('MUL') 
                        THEN (SELECT k.REFERENCED_COLUMN_NAME
                                FROM information_schema.KEY_COLUMN_USAGE k
                              WHERE k.table_schema = 'matilab872_gestao' 
                                AND k.table_name = c.table_name
                                AND k.COLUMN_NAME = c.COLUMN_NAME)
                      ELSE NULL END REFERENCED_COLUMN_NAME
            FROM information_schema.COLUMNS c
           WHERE c.table_schema = 'matilab872_gestao' 
             AND c.table_name = '$table'";
    return $this->db->query($sql)->result();
  }

  public function getFKReferencesToMysql($table){
    $sql = "SELECT 
              u.CONSTRAINT_NAME,
              u.TABLE_NAME,
              u.COLUMN_NAME,
              u.REFERENCED_TABLE_NAME,
              u.REFERENCED_COLUMN_NAME,
              r.UPDATE_RULE,
              r.DELETE_RULE,
              CASE WHEN r.DELETE_RULE = 'CASCADE' THEN 'TRUE' ELSE 'FALSE' END HAS_PARENT
            FROM
              information_schema.key_column_usage u 
              LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS r 
                ON (
                  r.CONSTRAINT_SCHEMA = u.table_schema 
                  AND r.constraint_name = u.constraint_name 
                  AND r.TABLE_NAME = u.table_name 
                  AND r.REFERENCED_TABLE_NAME = u.REFERENCED_TABLE_NAME
                ) 
            WHERE u.table_schema = 'matilab872_gestao' 
              AND u.table_name = '$table' 
              AND u.constraint_name <> 'PRIMARY'
            ";
    return $this->db->query($sql)->result();
  }
}