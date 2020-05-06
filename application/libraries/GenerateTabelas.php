<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateTabelas {

  private $CI;

  public function  __construct() {
    $this->CI =& get_instance();
  }

  public function init($folder, $nameTable = ''){
    $this->saveToProject($folder);
    $dbTables = $this->CI->gera->getTablesTo($nameTable);
    foreach ($dbTables as $key => $dbTable) {
      $this->table($dbTable);
      $this->column($dbTable);
      $this->foreignKey($dbTable);
    }
  }

  private function table($dbTable){
    $myTable = $this->CI->gera->getTables($dbTable->TABLE_NAME);
    if(empty($myTable)){ 
      $this->CI->gera->insert('tabelas', $dbTable);
    } else {
      $this->CI->gera->update('tabelas', $dbTable, ['TABLE_NAME' => $dbTable->TABLE_NAME]);
    }
  }

  private function column($dbTable){
    $dbColumns = $this->CI->gera->getFieldsTo($dbTable->TABLE_NAME);
    $myColumns = $this->CI->gera->getFields($dbTable->TABLE_NAME);
    
    if(empty($myColumns)){
      $this->CI->gera->insert_batch('colunas', $dbColumns); 
    } else {
      /*
       *
       * Da Origem para a minha Tabela => Altera e Insere
       * 
       */
      foreach ($dbColumns as $dbColumn) {
        $columnFound = False;
        foreach ($myColumns as $myColumn) {
          if ($dbColumn->COLUMN_NAME == $myColumn->COLUMN_NAME){
            $this->CI->gera->update('colunas', $dbColumn, ['TABLE_NAME' => $dbTable->TABLE_NAME, 'COLUMN_NAME' => $dbColumn->COLUMN_NAME]);
            $columnFound = True;
          }
        }
        if (!$columnFound){
          $this->CI->gera->insert('colunas', $dbColumn);
        }
      }

      /*
       *
       * Da minha Tabela para a Tabela de origem => Remove
       * 
       */
      foreach ($myColumns as $myColumn) {
        $columnFound = False;
        foreach ($dbColumns as $dbColumn) {
          if ($dbColumn->COLUMN_NAME == $myColumn->COLUMN_NAME){
            $columnFound = True;
          }
        }
        if (!$columnFound){
          $this->CI->gera->update('colunas', ['COLUMN_DELETED' => 'TRUE'], ['TABLE_NAME' => $dbTable->TABLE_NAME, 'COLUMN_NAME' => $dbColumn->COLUMN_NAME]);
        }
      }
    }
  }

  private function foreignKey($dbTable){
    echo $dbTable->TABLE_NAME;
    $dbforeignKeys = $this->CI->gera->getFKReferencesTo($dbTable->TABLE_NAME);
    $myforeignKeys = $this->CI->gera->getFKReferences($dbTable->TABLE_NAME);

    if(empty($myforeignKeys)){
      if(!empty($dbforeignKeys)){
        $this->CI->gera->insert_batch('foreignkeys', $dbforeignKeys);
      }
    } else {
      if(!empty($dbforeignKeys)){
        
        /*
        *
        * Da Origem para a minha Tabela => Altera e Insere
        * 
        */
        foreach ($dbforeignKeys as $key => $dbforeignKey) {
          $columnFound = False;
          foreach ($myforeignKeys as $key => $myforeignKey) {
            if($myforeignKey->REFERENCED_COLUMN_NAME == $dbforeignKey->REFERENCED_COLUMN_NAME && $myforeignKey->COLUMN_NAME == $dbforeignKey->COLUMN_NAME){
              $this->CI->gera->update('foreignkeys', $dbforeignKey, ['CONSTRAINT_NAME' => $dbforeignKey->CONSTRAINT_NAME]);
              $columnFound = True;
            }
          }
          if(!$columnFound){
            $this->CI->gera->insert('foreignkeys', $dbforeignKey);
          }
        }

        /*
        *
        * Da minha Tabela para a Tabela de origem => Remove
        * 
        */
        foreach ($myforeignKeys as $myforeignKey) {
          $columnFound = False;
          foreach ($dbforeignKeys as $dbforeignKey) {
            if($myforeignKey->REFERENCED_COLUMN_NAME == $dbforeignKey->REFERENCED_COLUMN_NAME && $myforeignKey->COLUMN_NAME == $dbforeignKey->COLUMN_NAME){
              $columnFound = True;
            }
          }
          if(!$columnFound){
            $this->CI->gera->update('foreignkeys', ['COLUMN_DELETED' => 'TRUE'], ['CONSTRAINT_NAME' => $myforeignKey->CONSTRAINT_NAME]);
          }
        }
      }
    }
  }
}