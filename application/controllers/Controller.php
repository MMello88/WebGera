<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controller extends CI_Controller {

  public function  __construct() {
    parent::__construct();
    header('Content-Type: application/json');
  }

  public function classes($table = ""){
    $tables = $this->gera->getTables($table);

    foreach ($tables as $key => $table) {
      $fields = $this->gera->getFields($table->TABLE_NAME);
      $fieldPK = $this->getFieldPK($fields);
      $validation = $this->getFieldValidation($fields);
      $postDelfault = $this->getFieldDefault($fields);
      $fieldFKUsuario = $this->getFieldFKUsuario($fields);
      $joins = $this->getFieldJoin($table->TABLE_NAME, $fields);

      $nameClass = ucfirst($table->TABLE_NAME);

      $inputs = $this->getInputs($table->TABLE_COMMENT, $fields);

      $file = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$nameClass} extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    \$this->table = '{$table->TABLE_NAME}';
    \$this->nameId = '{$fieldPK->COLUMN_NAME}';
    \$this->usersId = '{$fieldFKUsuario->COLUMN_FK}';
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

{$inputs}
";

      $this->saveFile($nameClass, $file);
      $files[$table->TABLE_NAME] = $file;
    }

    
    echo json_encode($files);
  }

  private function getFieldFKUsuario($fields){
    foreach ($fields as $key => $field) {
      if ($field->TABLE_FK == "users"){
        return $field;
      }
    }
    $field = new stdClass;
    $field->COLUMN_FK = "";
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

  private function saveFile($class, $txt){
    $filename = "C:\\xampp\\htdocs\\WebApi\\application\\controllers\\apiGerado\\{$class}.php";
    $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
    fwrite($file, $txt);
    fclose($file);
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

  private function getInputs($table, $fields){
    $input = "";
    $input .= "/*\n";
    $input .= "\t<div class='card-body'>\n";
    $input .= "\t\t<form>\n";
    $input .= "\t\t\t<fieldset>\n";
    $input .= "\t\t\t\t<legend>{$table}</legend>\n";
    foreach ($fields as $key => $field) {
      $input .= "\t\t\t\t<div class='form-group'>\n";
      $input .= "\t\t\t\t\t<label for='{$field->COLUMN_NAME}'>{$field->COLUMN_COMMENT}</label>\n";
      if ($field->COLUMN_KEY == "PRI"){
        $input .= "\t\t\t\t\t<input type='hidden' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}'>\n";
      } else {
        $type = $this->getType($field);
        $isnull = $field->IS_NULLABLE == "NO" ? "required" : "";
        if($type == "select"){
          $list = str_replace(")", "", str_replace("'", "", str_replace("enum(", "", $field->COLUMN_TYPE)));
          $items = explode(",", $list);
                            
          $input .= "\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
          $input .= "\t\t\t\t\t\t<option value=''> Selecione </option>\n";
          foreach ($items as $key => $item) {
            $input .= "\t\t\t\t\t\t<option value='{$item}'> {$item} </option>\n";
          }
          $input .= "\t\t\t\t\t</select>\n";
        } else {
          $input .= "\t\t\t\t\t<input type='{$type}' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='form-control' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
        }
      }
      $input .= "\t\t\t\t</div>\n";
    }
    $input .= "\t\t\t\t<div class='form-actions'>\n";
    $input .= "\t\t\t\t\t<button class='btn btn-primary' type='submit'>Salvar</button>\n";
    $input .= "\t\t\t\t</div>\n";
    $input .= "\t\t\t\t<div class='form-actions'>\n";
    $input .= "\t\t\t\t\t<button class='btn btn-secondary' type='submit'>Cancelar</button>\n";
    $input .= "\t\t\t\t</div>\n";
    $input .= "\t\t\t</fieldset>\n";
    $input .= "\t\t</form>\n";
    $input .= "\t</div>\n";
    $input .= "*/\n";
    return empty($input) ? null : $input;
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
      if(!empty($field->TABLE_FK)){
        if ($field->TABLE_FK !== "users"){
          $typeJoin = $field->IS_NULLABLE == "NO" ? "inner" : "left";
          $joins.= "\n\t\t\t['table' => '{$field->TABLE_FK}', 'condition' => '{$field->TABLE_FK}.{$field->COLUMN_FK_PRI} = {$table}.{$field->COLUMN_FK}', 'type' => '{$typeJoin}'],";
        }
      }
    }
    return $joins;
  }
}
