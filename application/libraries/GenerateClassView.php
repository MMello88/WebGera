<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassView{

  protected $CI;
  private $filename = "C:\\xampp\\htdocs\\WebSite\\application\\views\\api\\";
  
  public function __constructor(){
  }

  public function init($nameTable = ""){
    $this->CI = &get_instance();
    $tables = $this->CI->gera->getTables($nameTable);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);
      $this->buildView($table, $fields);
    }
  }

  private function buildView($table, $fields){
    $opt = $this->getOptionSelected($fields);
    $th  = $this->getFieldTh($fields);
    $td  = $this->getFieldTd($fields);
    $nameClass = ucfirst($table->TABLE_NAME);

    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
        <?php if(\$response->method !== 'GET'): ?>
          <?php if(\$response->status == 'FALSE'): ?>
          <!-- .page-message -->
          <div class='page-message bg-warning' role='alert'>
            <span class='mr-5'><?= \$response->message ?></span> <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'><span aria-hidden='true'><i class='fa fa-times'></i></span></a>
          </div><!-- /.page-message -->
          <?php else: ?>
          <!-- .page-message -->
          <div class='page-message bg-success' role='alert'>
            <span class='mr-5'><?= \$response->message ?></span> <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'><span aria-hidden='true'><i class='fa fa-times'></i></span></a>
          </div><!-- /.page-message -->    
          <?php endif; ?>
        <?php else: ?>
          <?php if(\$response->status == 'FALSE'): ?>
          <!-- .page-message -->
          <div class='page-message bg-warning' role='alert'>
            <span class='mr-5'>Falha ao consultar o usuário!</span> <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'><span aria-hidden='true'><i class='fa fa-times'></i></span></a>
          </div><!-- /.page-message -->
          <?php endif; ?>
        <?php endif; ?>
          <!-- .page-inner -->
          <div class='page-inner'>

            <!-- .page-title-bar -->
            <header class='page-title-bar'>
              <!-- .breadcrumb -->
              <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                  <li class='breadcrumb-item active'>
                    <a href='#'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>{$table->TABLE_COMMENT}</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
              <!-- floating action -->
              <button type='button' class='btn btn-success btn-floated'><span class='fa fa-plus'></span></button> <!-- /floating action -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .card -->
              <div class='card card-fluid'>
                <!-- .card-header -->
                <div class='card-header d-md-flex align-items-md-start'>
                  <h1 class='page-title mr-sm-auto'> {$table->TABLE_COMMENT} </h1><!-- .btn-toolbar -->
                  <div id='dt-buttons' class='btn-toolbar'></div><!-- /.btn-toolbar -->
                </div><!-- /.card-header -->
                <!-- .card-body -->
                <div class='card-body'>
                  <!-- .form-group -->
                  <div class='form-group'>
                    <!-- .input-group -->
                    <div class='input-group input-group-alt'>
                      <!-- .input-group-prepend -->
                      <div class='input-group-prepend'>
                        <select id='filterBy' class='custom-select'>
                          <option value='' selected> Filtrar por </option>
{$opt}
                        </select>
                      </div><!-- /.input-group-prepend -->
                      <!-- .input-group -->
                      <div class='input-group has-clearable'>
                        <button id='clear-search' type='button' class='close' aria-label='Close'>
                          <span aria-hidden='true'><i class='fa fa-times-circle'></i></span>
                        </button>
                        <div class='input-group-prepend'>
                          <span class='input-group-text'><span class='oi oi-magnifying-glass'></span></span>
                        </div>
                        <input id='table-search' type='text' class='form-control' placeholder='Search products'>
                      </div><!-- /.input-group -->
                    </div><!-- /.input-group -->
                  </div><!-- /.form-group -->
                  <!-- .table -->
                  <table id='myTable' class='table'>
                    <!-- thead -->
                    <thead>
                      <tr>
                        <th colspan='2' style='min-width: 320px;'>
                          <div class='thead-dd dropdown'>
                            <span class='custom-control custom-control-nolabel custom-checkbox'><input type='checkbox' class='custom-control-input' id='check-handle'> <label class='custom-control-label' for='check-handle'></label></span>
                            <div class='thead-btn' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                              <span class='fa fa-caret-down'></span>
                            </div>
                            <div class='dropdown-menu'>
                              <div class='dropdown-arrow'></div>
                              <a class='dropdown-item' href='#'>Select all</a> 
                              <a class='dropdown-item' href='#'>Unselect all</a>
                              <div class='dropdown-divider'></div>
                              <a class='dropdown-item' href='#'>Bulk remove</a> 
                              <a class='dropdown-item' href='#'>Bulk edit</a> 
                              <a class='dropdown-item' href='#'>Separate actions</a>
                            </div>
                          </div>
                        </th>
{$th}
                      </tr>
                    </thead><!-- /thead -->
                    <!-- tbody -->
                    <tbody>
                      <!-- create empty row to passing html validator -->
                      <tr>
{$td}
                      </tr>
                    </tbody><!-- /tbody -->
                  </table><!-- /.table -->
                </div><!-- /.card-body -->
              </div><!-- /.card -->
            </div><!-- /.page-section -->



          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->

    <title>{$table->TABLE_COMMENT}</title>
<script>
var url_get = '<?= base_url('{$table->TABLE_NAME}/get'); ?>';
</script>";
    $this->saveFile($nameClass, $view);
  }
  
  private function saveFile($class, $txt){
    if(!file_exists($this->filename)){
      mkdir($this->filename);
    }

    if(!file_exists($this->filename . $class . "\\")){
      mkdir($this->filename . $class . "\\");
    }
      
    $filename = $this->filename . $class . "\\" . $class . ".php";

    $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
    fwrite($file, $txt);
    fclose($file);
  }

  private function getOptionSelected($fields){
    $opt = "";
    foreach ($fields as $key => $field) {
      $opt .= "\t\t\t\t\t\t\t\t\t\t\t\t\t<option value='{$key}'> {$field->COLUMN_NAME} </option>\n";
    }
    return $opt;
  }

  private function getFieldTh($fields){
    $th = "";
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY <> "PRI"){
        $th .= "\t\t\t\t\t\t\t\t\t\t\t\t\t<th> {$field->COLUMN_NAME} </th>\n";
      }
    }
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY == "PRI"){
        $th .= "\t\t\t\t\t\t\t\t\t\t\t\t\t<th style='width:100px; min-width:100px;'> &nbsp; </th>\n";
      }
    }
    return $th;
  }

  private function getFieldTd($fields){
    $td = "";
    foreach ($fields as $key => $field) {
      $td .= "\t\t\t\t\t\t\t\t\t\t\t\t\t<td></td>\n";
    }
    return $td;
  }

  private function oldbuildView($table, $fields){
    $inputs = $this->getInput($fields);
    $nameClass = ucfirst($table->TABLE_NAME);
    $view = "
<header class='page-title-bar'>
  <legend>{$table->TABLE_NAME}</legend>
</header>
<div class='page-section'>
  <div class='section-block'>
    <div class='card' id='floating-label'>
      <div class='card-body'>
        <?= form_open(base_url('{$nameClass}/')) ?>
        <fieldset>
          $inputs
          <div class='form-actions'>
            <button class='btn btn-primary mr-auto' type='submit'>Salvar</button>
            <button class='btn btn-secondary ml-auto' type='submit'>Cancelar</button>
          </div>
        </fieldset>
        <?= form_close() ?>
      </div>
    </div>
  </div>
</div>";

    $this->saveFile($nameClass, $view);
  }

  private function getInput($fields){
    $inputs = "";
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY == "PRI"){
        $inputs .= "\t\t\t\t\t\t\t<input type='hidden' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}'>";
      } else {
        $inputs .= "\t\t\t\t\t\t\t<div class='form-group'>\n";
        $inputs .= "\t\t\t\t\t\t\t\t<label for='{$field->COLUMN_NAME}'>{$field->COLUMN_COMMENT}</label>\n";
        $type = $this->getType($field);
        $isnull = $field->IS_NULLABLE == "NO" ? "required" : "";
        if($type == "select"){
          $list = str_replace(")", "", str_replace("'", "", str_replace("enum(", "", $field->COLUMN_TYPE)));
          $items = explode(",", $list);
                            
          $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<option value=''> Selecione </option>\n";
          foreach ($items as $key => $item) {
            $inputs .= "\t\t\t\t\t\t\t\t<option value='{$item}'> {$item} </option>\n";
          }
          $inputs .= "\t\t\t\t\t\t\t</select>\n";
        } else {
          $inputs .= "\t\t\t\t\t\t\t<input type='{$type}' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='form-control' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php if(isset(\$response)): ?>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<div class='invalid-feedback' style='display:block'><?= isset(\$response->error->{$field->COLUMN_NAME}) ? \$response->error->{$field->COLUMN_NAME} : ''; ?></div>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php endif; ?>\n";
        }
        $inputs .= "\t\t\t\t\t\t</div>\n";
      }
    }
    return $inputs;
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
}