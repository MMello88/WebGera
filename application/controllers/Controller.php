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

      $nameClass = ucfirst($table->TABLE_NAME);
      $file = "<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class {$nameClass} extends MY_Controller {

  public function  __construct() {
    parent::__construct();
    \$this->table = '{$table->TABLE_NAME}';
    \$this->nameId = '{$fieldPK->COLUMN_NAME}';
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
}";

      $this->saveFile($nameClass, $file);
      $files[$table->TABLE_NAME] = $file;
    }

    
    echo json_encode($files);
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
      if ($field->COLUMN_KEY != "PRI" && $field->IS_NULLABLE == "NO"){
        $validation .= "\$this->form_validation->set_rules('{$field->COLUMN_NAME}', '{$field->COLUMN_NAME}', 'required');\n\t\t";
      }
    }
    return $validation;
  }

  private function getFieldDefault($fields){
    $default = "";
    foreach ($fields as $key => $field) {
      if (!empty($field->COLUMN_DEFAULT)){
        $default = "\$_POST['{$field->COLUMN_NAME}'] = '{$field->COLUMN_DEFAULT}';";
      }
    }
    return $default;
  }

  private function saveFile($class, $txt){
    $filename = "C:\\xampp\\htdocs\\WebApi\\application\\controllers\\apiGerado\\{$class}.php";
    if(file_exists($filename))
      $file = fopen($filename, 'r+');
    else
      $file = fopen($filename, 'a+');
    fwrite($file, $txt);
    fclose($file);
  }
}
