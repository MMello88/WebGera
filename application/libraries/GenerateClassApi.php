<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassApi {

  private $CI;
  private $filename = "C:\\xampp\\htdocs\\WebApi\\application\\controllers\\api\\";
  
  public function __constructor(){
  }

  public function saveToProject($folder){
    if (!file_exists("C:\\xampp\\htdocs\\{$folder}\\"))
      die("Caminho do Projeto não encontrado");
    $this->filename = "C:\\xampp\\htdocs\\{$folder}Api\\application\\controllers\\api\\";
  }

  public function init($folder, $nameTable = ""){
    $this->saveToProject($folder);
    $this->CI =& get_instance();
    $tables = $this->CI->gera->getTablesPai($nameTable);
    print_r($tables);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);
      
      $this->buildApi($table, $fields);
      
      $this->initChild($table);
    }
  }

  private function initChild($table){
    if($table->HAS_CHILD === 'TRUE'){

      $foreignKeys = $this->CI->gera->getFKFilhos($table->TABLE_NAME);

      foreach ($foreignKeys  as $foreignKey) {
        $tableFK = $this->CI->gera->getTables($foreignKey->TABLE_NAME)[0];

        $fieldsFK = $this->CI->gera->getFields($foreignKey->TABLE_NAME);
        $this->buildApiChild($tableFK, $fieldsFK, $foreignKey);
        $this->initChild($tableFK);
      }
    }
  }

  private function buildApi($table, $fields){
    $fieldPK = $this->getFieldPK($fields);
    $validation = $this->getFieldValidation($fields);
    $postDelfault = $this->getFieldDefault($fields);
    $fieldFKUsuario = $this->getFieldFKUsuario($fields);
    $joins = $this->getFieldJoin($table->TABLE_NAME, $fields);
    $nameClass = ucfirst($table->TABLE_NAME);

    $file = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$nameClass} extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    \$this->table = '{$table->TABLE_NAME}';
    \$this->nameId = '{$fieldPK->COLUMN_NAME}';
    \$this->usersId = '{$fieldFKUsuario->COLUMN_NAME}';
    \$this->joins = [$joins
    ];
  }

  public function get(\$Id = '', \$date = ''){
    parent::get(\$Id, \$date);
  }

  public function setDefaultValue(){
    {$postDelfault}
  }

  public function create(){
    {$validation}
    parent::create();
  }

  public function update(\$Id){
    {$validation}
    parent::update(\$Id);
  }

  public function delete(\$Id){
    parent::delete(\$Id);
  }
}
";

    $this->saveFile($nameClass, $file);
  }

  private function buildApiChild($table, $fields, $foreignKey){
    $fieldPK = $this->getFieldPK($fields);
    $validation = $this->getFieldValidation($fields);
    $postDelfault = $this->getFieldDefault($fields);
    $fieldFKUsuario = $this->getFieldFKUsuario($fields);
    $joins = $this->getFieldJoin($table->TABLE_NAME, $fields);
    $nameClass = ucfirst($table->TABLE_NAME);

    $file = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$nameClass} extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    \$this->table = '{$table->TABLE_NAME}';
    \$this->nameId = '{$fieldPK->COLUMN_NAME}';
    \$this->usersId = '{$fieldFKUsuario->COLUMN_NAME}';
    \$this->joins = [$joins
    ];
    \$this->tableParent = '$foreignKey->REFERENCED_TABLE_NAME';
    \$this->nameIdParent = '$foreignKey->REFERENCED_COLUMN_NAME';
  }

  public function get(\$Id = '', \$date = ''){
    parent::get(\$Id, \$date);
  }

  public function getByParent(\$IdParent, \$Id = ''){
    parent::getByParent(\$IdParent, \$Id);
  }

  public function setDefaultValue(){
    {$postDelfault}
  }

  public function create(){
    {$validation}
    parent::create();
  }

  public function update(\$Id){
    {$validation}
    parent::update(\$Id);
  }

  public function delete(\$Id){
    parent::delete(\$Id);
  }
}
";

    $this->saveFile($nameClass, $file);
  }

  private function saveFile($class, $txt){
    $filename = $this->filename . $class.".php";
    $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
    fwrite($file, $txt);
    fclose($file);
  }

  private function getFieldFKUsuario($fields){
    foreach ($fields as $key => $field) {
      if ($field->REFERENCED_TABLE_NAME == "users"){
        return $field;
      }
    }
    $field = new stdClass;
    $field->COLUMN_NAME = "";
    return $field;
  }

  private function getFieldPK($fields){
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY == "PRI"){
        return $field;
      }
    }
  }

  private function getFieldValidation($fields){
    $validation = "";
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY != "PRI"){
        $rules = $this->getRules($field);
        if(!empty($rules))
          $validation .= "\$this->form_validation->set_rules('{$field->COLUMN_NAME}', '{$field->COLUMN_NAME}', '{$rules}');\n\t\t";
      }
    }
    return $validation;
  }

  private function getFieldDefault($fields){
    $default = "";
    foreach ($fields as $key => $field) {
      if (!empty($field->COLUMN_DEFAULT)){
        if ($field->COLUMN_DEFAULT == 'CURRENT_TIMESTAMP(6)'){
          $default .= "\$_POST['{$field->COLUMN_NAME}'] = !isset(\$_POST['{$field->COLUMN_NAME}']) ? date('Y-m-d H:i:s') : \$_POST['{$field->COLUMN_NAME}'];\n\t\t";
        } else {
          $default .= "\$_POST['{$field->COLUMN_NAME}'] = !isset(\$_POST['{$field->COLUMN_NAME}']) ? '{$field->COLUMN_DEFAULT}' : \$_POST['{$field->COLUMN_NAME}'];\n\t\t";
        }
      }
    }
    return $default;
  }

  private function getRules($field){
    $rules = $field->IS_NULLABLE == "NO" ? "required|" : "";
    $rules .= $field->DATA_TYPE == "int" ? "integer|" : "";
    $rules .= $field->DATA_TYPE == "varchar" ? "max_length[{$field->CHARACTER_MAXIMUM_LENGTH}]|" : "";
    $rules .= $field->DATA_TYPE == "char" ? "max_length[{$field->CHARACTER_MAXIMUM_LENGTH}]|" : "";
    $rules .= $field->DATA_TYPE == "float" ? "numeric|" : "";
    $rules .= $field->DATA_TYPE == "decimal" ? "decimal|" : "";
    $rules .= $field->DATA_TYPE == "double" ? "numeric|" : "";
    $rules .= $field->DATA_TYPE == "datetime" ? "valid_datetime|" : "";
    $rules .= $field->DATA_TYPE == "date" ? "valid_date|" : "";
    $column_type = str_replace(")", "", str_replace("'", "", str_replace("enum(", "", $field->COLUMN_TYPE)));
    $rules .= $field->DATA_TYPE == "enum" ? "in_list[$column_type]|" : "";
    return substr($rules, 0, -1);
  }

  

  private function getType($field){
    $type = $field->DATA_TYPE == "varchar" ? "text" : "";
    $type .= $field->DATA_TYPE == "longtext" ? "text" : "";
    $type .= $field->DATA_TYPE == "int" ? "number" : "";
    $type .= $field->DATA_TYPE == "float" ? "number" : "";
    $type .= $field->DATA_TYPE == "enum" ? "select" : "";
    $type .= $field->DATA_TYPE == "date" ? "date" : "";
    $type .= $field->DATA_TYPE == "datetime" ? "datetime-local" : "";
    return $type;
  }

  private function getFieldJoin($table, $fields){
    $joins = "";
    foreach ($fields as $key => $field) {
      if(!empty($field->REFERENCED_TABLE_NAME)){
        if ($field->REFERENCED_TABLE_NAME !== "users"){
          $typeJoin = $field->IS_NULLABLE == "NO" ? "inner" : "left";
          $joins.= "\n\t\t\t['table' => '{$field->REFERENCED_TABLE_NAME}', 'condition' => '{$field->REFERENCED_TABLE_NAME}.{$field->REFERENCED_COLUMN_NAME} = {$table}.{$field->COLUMN_NAME}', 'type' => '{$typeJoin}'],";
        }
      }
    }
    return $joins;
  }  
}