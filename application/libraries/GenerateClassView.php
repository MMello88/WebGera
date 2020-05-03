<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassView{

  protected $CI;
  private $filename = "C:\\xampp\\htdocs\\WebSite\\application\\views\\api\\";
  
  public function __constructor(){
  }

  public function init($nameTable = ""){
    $this->CI = &get_instance();
    $tables = $this->CI->gera->getTablesPai($nameTable);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);
      $foreignKeys = $this->CI->gera->getFKReferences($table->TABLE_NAME);

      $tabelasFilho = [];
      if($table->HAS_CHILD === 'TRUE'){
        $tabelasFilho = $this->CI->gera->getFKFilhos($table->TABLE_NAME);
      }

      $this->buildViewGrid($table, $fields);
      $this->buildViewView($table, $fields, $foreignKeys, $tabelasFilho);
      $this->buildViewCreate($table, $fields, $foreignKeys, $tabelasFilho);
      $this->buildViewUpdate($table, $fields, $foreignKeys, $tabelasFilho);
      
      if($table->HAS_CHILD === 'TRUE'){
        $this->initToReferencial($tabelasFilho);
      }
    }
  }

  private function initToReferencial($tabelasFilho){
    //print_r($tabelasFilho);
    foreach ($tabelasFilho as $key => $tabelaFilho) {

      $tabelasFilhoFilho = [];
      if($tabelaFilho->TABLE->HAS_CHILD === 'TRUE'){
        $tabelasFilhoFilho = $this->CI->gera->getFKFilhos($tabelaFilho->TABLE->TABLE_NAME);
      }
      $this->buildViewGridReferencial($tabelaFilho, $tabelasFilhoFilho);
      $this->buildViewViewReferencial($tabelaFilho, $tabelasFilhoFilho);
      $this->buildViewCreateReferencial($tabelaFilho, $tabelasFilhoFilho);
      $this->buildViewUpdateReferencial($tabelaFilho, $tabelasFilhoFilho);
      
      if($tabelaFilho->TABLE->HAS_CHILD === 'TRUE'){
        $this->initToReferencial($tabelasFilhoFilho);
      }
    }
  }

  private function buildViewGrid($table, $fields){
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
        <?php if(\$response['method'] !== 'GET'): ?>
          <?php if(\$response['status'] == 'FALSE'): ?>
          <!-- .page-message -->
          <div class='page-message bg-warning' role='alert'>
            <span class='mr-5'><?= \$response['message'] ?></span> <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'><span aria-hidden='true'><i class='fa fa-times'></i></span></a>
          </div><!-- /.page-message -->
          <?php else: ?>
          <!-- .page-message -->
          <div class='page-message bg-success' role='alert'>
            <span class='mr-5'><?= \$response['message'] ?></span> <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'><span aria-hidden='true'><i class='fa fa-times'></i></span></a>
          </div><!-- /.page-message -->    
          <?php endif; ?>
        <?php else: ?>
          <?php if(\$response['status'] == 'FALSE'): ?>
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
              <button type='button' class='btn btn-success btn-floated' onclick=\"window.location.href='<?= base_url('{$nameClass}/create') ?>'\"><span class='fa fa-plus'></span></button> <!-- /floating action -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .card -->
              <div class='card card-fluid'>
                <!-- .card-header -->
                <div class='card-header d-md-flex align-items-md-start'>
                  <h1 class='page-title mr-sm-auto'> {$table->TABLE_COMMENT} </h1><!-- .btn-toolbar -->
                  <div id='dt-buttons' class='btn-toolbar'></div><!-- /.btn-toolbar -->
                  <div class='dropdown'>
                  <button type='button' class='btn btn-icon btn-light' data-toggle='dropdown'>
                  <i class='fa fa-ellipsis-v'></i></button>
                  <div class='dropdown-menu dropdown-menu-right'>
                    <div class='dropdown-arrow'></div>
                    <a href='#' class='dropdown-item' id='btnFloatedAdd'>Atualizar</a>
                  </div>
                </div>
                </div><!-- /.card-header -->
                <!-- .card-body -->
                <div class='card-body'>
                  <!-- .form-group -->
                  <div class='form-group'>
                    <!-- .input-group -->
                    <div class='input-group input-group-alt'>
                      <!-- .input-group-prepend -->
                      <div class='input-group-prepend'>
                        <select id='filterBy' class='custom-select' style='width: 150px'>
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
                  <table id='myTable{$nameClass}' class='table'>
                    <!-- thead -->
                    <thead>
                      <tr>
                        <th>
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
var url_upd = '<?= base_url('{$table->TABLE_NAME}/edit'); ?>';
var url_view = '<?= base_url('{$table->TABLE_NAME}/view'); ?>';
</script>

<!-- Central modal -->
<div class='modal fade' id='modalDeleteRegistro' tabindex='-1' role='dialog' aria-labelledby='ModalDeleteLabel' aria-hidden='true'>
  <!-- .modal-dialog -->
  <div class='modal-dialog modal-dialog-centered' role='document'>
    <!-- .modal-content -->
    <div class='modal-content'>
      <!-- .modal-header -->
      <div class='modal-header'>
        <h5 id='ModalDeleteLabel' class='modal-title'> Deseja Deletar este Registro? </h5>
      </div><!-- /.modal-header -->
      <?= form_open('{$table->TABLE_NAME}/delete') ?>
        <!-- .modal-body -->
        <div class='modal-body'>
          <input type='hidden' id='DeleteById' name='Id' value=''>
        </div><!-- /.modal-body -->
        <!-- .modal-footer -->
        <div class='modal-footer'>
          <button type='submit' class='btn btn-primary'>Deletar</button>
          <button type='button' class='btn btn-light' data-dismiss='modal'>Cancelar</button>
        </div><!-- /.modal-footer -->
      <?= form_close() ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
";
    $this->saveFile($nameClass, "Grid".$nameClass, $view);
  }
  
  private function buildViewCreate($table, $fields, $foreignKeys, $tabelasFilho){
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputInsert($fields, $foreignKeys, null);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilho, $fieldPK);
    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClass}') ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                    <?= form_open(base_url('{$nameClass}/add')); ?>
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Adicionar um novo registro</legend> <!-- .form-group -->
{$inputs}
                        <div class='form-actions'>
                          <button class='btn btn-primary mr-3' type='submit'>Salvar</button>
                          <div class='form-group mt-2'>
														<label class='switcher-control'> 
															<input type='checkbox' name='cbxSaveBack' class='switcher-input' <?= isset(\$response['data']['cbxSaveBack']) ? 'checked' : '' ?>>
															<span class='switcher-indicator'></span>
															<span class='switcher-label-on'><i class='fas fa-check'></i></span>
															<span class='switcher-label-off'><i class='fas fa-times'></i></span>
														</label>
														<span>Salvar e voltar?</span>
													</div>
                          <button class='btn btn-secondary ml-auto' type='button' onclick=\"window.location.href='<?= base_url('{$nameClass}') ?>'\">Cancelar</button>
                        </div>
                      </fieldset><!-- /.fieldset -->
                    <?= form_close(); ?><!-- /.form -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->
";
    $this->saveFile($nameClass, "Create".$nameClass, $view);
  }

  private function buildViewUpdate($table, $fields, $foreignKeys, $tabelasFilho){
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputUpdate($fields, $foreignKeys, null);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilho, $fieldPK);
    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClass}') ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                    <?= form_open(base_url('{$nameClass}/update/'.\$response['data'][0]['{$fieldPK->COLUMN_NAME}'])); ?>
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Alteração do registro</legend> <!-- .form-group -->
{$inputs}
                        <div class='form-actions'>
                          <button class='btn btn-primary mr-3' type='submit'>Salvar</button>
                          <div class='form-group mt-2'>
														<label class='switcher-control'> 
															<input type='checkbox' name='cbxSaveBack' class='switcher-input' <?= isset(\$response['data']['cbxSaveBack']) ? 'checked' : '' ?>>
															<span class='switcher-indicator'></span>
															<span class='switcher-label-on'><i class='fas fa-check'></i></span>
															<span class='switcher-label-off'><i class='fas fa-times'></i></span>
														</label>
														<span>Salvar e voltar?</span>
													</div>
                          <button class='btn btn-secondary ml-auto' type='button' onclick=\"window.location.href='<?= base_url('{$nameClass}') ?>'\">Cancelar</button>
                        </div>
                      </fieldset><!-- /.fieldset -->
                    <?= form_close(); ?><!-- /.form -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->

";
    $this->saveFile($nameClass, "Edit".$nameClass, $view);
  }

  private function buildViewView($table, $fields, $foreignKeys, $tabelasFilho){
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputView($fields, $foreignKeys, null);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilho, $fieldPK);

    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClass}') ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Alteração do registro</legend> <!-- .form-group -->
{$inputs}
                      </fieldset><!-- /.fieldset -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->
";
    $this->saveFile($nameClass, "View".$nameClass, $view);
  }

  private function buildViewGridFilho($tabelasFilho, $fieldPK){
    if(count($tabelasFilho) == 0) return;

    $view = "";
    
    $view .= "
      <!-- .page-section -->
      <div class='page-section'>

        <!-- .card -->
        <div class='card card-fluid'>

          <!-- .card-header -->
          <div class='card-header d-md-flex align-items-md-start'>
            <ul class='nav nav-tabs card-header-tabs'>";
    foreach ($tabelasFilho as $key => $tableFilho) {
      $activeFirst = $key == 0 ? "active show" : "";
      $view .= "
              <li class='nav-item'>
                <a class='nav-link $activeFirst' data-toggle='tab' href='#TabRef{$key}'>{$tableFilho->TABLE->TABLE_COMMENT}</a>
              </li>";
    }
    $view .="                
            </ul>
          </div><!-- /.card-header -->

          <!-- .card-body -->
          <div class='card-body'>
            <!-- .tab-content -->
            <div id='myTabContent' class='tab-content'>
          ";
    foreach ($tabelasFilho as $key => $tableFilho) {
      $fields = $tableFilho->TABLE->FIELDS;
      $opt = $this->getOptionSelected($fields);
      $th  = $this->getFieldTh($fields);
      $td  = $this->getFieldTd($fields);
      $nameClass = ucfirst($tableFilho->TABLE->TABLE_NAME);
      $activeFirst = $key == 0 ? "active show" : "";

      $view .= "
                  <!-- .tab-pane -->
                  <div class='tab-pane fade $activeFirst' id='TabRef{$key}'>
                    <div class='d-flex align-items-center mb-4'>
                      <?php if(isset(\$response['data'][0]['{$fieldPK->COLUMN_NAME}'])): ?>
                      <button type='button' class='btn btn-outline-success' onclick=\"window.location.href='<?= base_url('{$nameClass}/create/'.\$nameView.'/'. \$response['data'][0]['{$fieldPK->COLUMN_NAME}']) ?>'\">
                      <?php else: ?>
                      <button type='button' class='btn btn-outline-success' onclick=\"window.location.href='<?= base_url('{$nameClass}/create/'.\$nameView.'/') ?>'\" disabled>
                      <?php endif; ?>
                        <span class='fa fa-plus'></span>
                      </button> <!-- /floating action -->
                      <div class='dropdown ml-auto'>
                        <button type='button' class='btn btn-icon btn-light' data-toggle='dropdown'>
                        <i class='fa fa-ellipsis-v'></i></button>
                        <div class='dropdown-menu dropdown-menu-right'>
                          <div class='dropdown-arrow'></div>
                          <a href='#' class='dropdown-item' id='btnFloatedAdd'>Atualizar</a>
                        </div>
                      </div>
                    </div>

                    <!-- .form-group -->
                    <div class='form-group'>
                      <!-- .input-group -->
                      <div class='input-group input-group-alt'>
                        <!-- .input-group-prepend -->
                        <div class='input-group-prepend'>
                          <select id='filterBy' class='custom-select' style='width: 150px'>
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
                    <table id='myTable{$nameClass}' class='table'>
                      <!-- thead -->
                      <thead>
                        <tr>
                          <th>
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

                  </div><!-- /.tab-pane -->
      ";
    }
    $view .= "
      </div><!-- /.tab-content -->
    </div><!-- /.card-body -->
  </div><!-- /.card -->
</div><!-- /.page-section -->
    ";

    foreach ($tabelasFilho as $key => $tableFilho) {
      $view .= "
      <!-- Central modal -->
      <div class='modal fade' id='modalDeleteRegistro' tabindex='-1' role='dialog' aria-labelledby='ModalDeleteLabel' aria-hidden='true'>
        <!-- .modal-dialog -->
        <div class='modal-dialog modal-dialog-centered' role='document'>
          <!-- .modal-content -->
          <div class='modal-content'>
            <!-- .modal-header -->
            <div class='modal-header'>
              <h5 id='ModalDeleteLabel' class='modal-title'> Deseja Deletar este Registro? </h5>
            </div><!-- /.modal-header -->
            <?= form_open('{$tableFilho->TABLE->TABLE_NAME}/delete') ?>
              <!-- .modal-body -->
              <div class='modal-body'>
                <input type='hidden' id='DeleteById' name='Id' value=''>
              </div><!-- /.modal-body -->
              <!-- .modal-footer -->
              <div class='modal-footer'>
                <button type='submit' class='btn btn-primary'>Deletar</button>
                <button type='button' class='btn btn-light' data-dismiss='modal'>Cancelar</button>
              </div><!-- /.modal-footer -->
            <?= form_close() ?>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

<?php if(isset(\$response['data'][0]['{$fieldPK->COLUMN_NAME}'])): ?>
  <script>
  var url_get_{$tableFilho->TABLE->TABLE_NAME} = '<?= base_url('{$tableFilho->TABLE->TABLE_NAME}/get/'.\$response['data'][0]['{$fieldPK->COLUMN_NAME}']); ?>';
  var url_upd_{$tableFilho->TABLE->TABLE_NAME} = '<?= base_url('{$tableFilho->TABLE->TABLE_NAME}/edit/'.\$nameView.'/'. \$response['data'][0]['{$fieldPK->COLUMN_NAME}']); ?>';
  var url_view_{$tableFilho->TABLE->TABLE_NAME} = '<?= base_url('{$tableFilho->TABLE->TABLE_NAME}/view/'.\$nameView.'/'. \$response['data'][0]['{$fieldPK->COLUMN_NAME}']); ?>';
</script>
<?php endif; ?>
      ";
    }
    
    return $view;
  }

  private function saveFile($pasta, $class, $txt){
    if(!file_exists($this->filename)){
      mkdir($this->filename);
    }

    if(!file_exists($this->filename . $pasta . "\\")){
      mkdir($this->filename . $pasta . "\\");
    }
      
    $filename = $this->filename . $pasta . "\\" . $class . ".php";

    $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
    fwrite($file, $txt);
    fclose($file);
  }

  private function getOptionSelected($fields){
    $opt = "";
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY <> "PRI"){
        $opt .= "\t\t\t\t\t\t\t\t\t\t\t\t\t<option value='{$key}'> {$field->COLUMN_COMMENT} </option>\n";
      }
    }
    return $opt;
  }

  private function getFieldTh($fields){
    $th = "";
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY == "PRI"){
        $th .= "\t\t\t\t\t\t\t\t\t\t\t\t<th style='width:100px; min-width:120px;'> # </th>\n";
      }
    }
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY <> "PRI"){
        $th .= "\t\t\t\t\t\t\t\t\t\t\t\t<th> {$field->COLUMN_COMMENT} </th>\n";
      }
    }

    return $th;
  }

  private function getFieldTd($fields){
    $td = "";
    foreach ($fields as $key => $field) {
      $td .= "\t\t\t\t\t\t\t\t\t\t\t\t<td></td>\n";
    }
    return $td;
  }

  private function getInputInsert($fields, $foreignKeys, $tableFilho = null){
    $inputs = "";
    foreach ($fields as $key => $field) {
      if(strpos(strtolower($field->COLUMN_NAME), 'usersid') !== FALSE)
        continue;
      if ($field->COLUMN_KEY <> "PRI"){
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
            $inputs .= "\t\t\t\t\t\t\t\t<option value='{$item}' <?= isset(\$response['data']['{$field->COLUMN_NAME}']) ? \$response['data']['{$field->COLUMN_NAME}'] == '{$item}' ? 'selected' : '' : '' ?>> {$item} </option>\n";
          }
          $inputs .= "\t\t\t\t\t\t\t</select>\n";
        } else {
          $hasFK = FALSE;
          foreach ($foreignKeys as $key => $fk) {
            if($fk->COLUMN_NAME == $field->COLUMN_NAME){
              $hasFK = TRUE;
              if ($tableFilho !== null && $fk->COLUMN_NAME == $tableFilho->COLUMN_NAME){
                $inputs .= "\t\t\t\t\t\t\t<input type='hidden' name='{$field->COLUMN_NAME}' value='<?= \$IdParent ?>' required>\n";
                $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull} disabled>\n";
                $inputs .= "\t\t\t\t\t\t\t\t<?= getOptionToSelect('{$fk->REFERENCED_TABLE_NAME}','{$fk->REFERENCED_COLUMN_NAME}', '', \$IdParent, \$login->data->token) ?>\n";
                $inputs .= "\t\t\t\t\t\t\t</select>\n";
              } else {
                $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
                $inputs .= "\t\t\t\t\t\t\t\t<?= getOptionToSelect('{$fk->REFERENCED_TABLE_NAME}','{$fk->REFERENCED_COLUMN_NAME}', '', isset(\$response['data']['{$field->COLUMN_NAME}']) ? \$response['data']['{$field->COLUMN_NAME}'] : '', \$login->data->token) ?>\n";
                $inputs .= "\t\t\t\t\t\t\t</select>\n";
              }
            }
          }
          if(!$hasFK)
            $inputs .= "\t\t\t\t\t\t\t<input type='{$type}' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='form-control' placeholder='{$field->COLUMN_COMMENT}' value='<?= isset(\$response['data']['{$field->COLUMN_NAME}']) ? \$response['data']['{$field->COLUMN_NAME}'] : '{$field->COLUMN_DEFAULT}' ?>' {$isnull}>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php if(isset(\$response)): ?>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<div class='invalid-feedback' style='display:block'><?= isset(\$response['error']['{$field->COLUMN_NAME}']) ? \$response['error']['{$field->COLUMN_NAME}'] : ''; ?></div>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php endif; ?>\n";
        }
        $inputs .= "\t\t\t\t\t\t</div>\n";
        
      }
    }
    return $inputs;
  }

  private function getInputUpdate($fields, $foreignKeys, $tableFilho = null){
    $inputs = "";
    foreach ($fields as $key => $field) {
      if(strpos(strtolower($field->COLUMN_NAME), 'usersid') !== FALSE)
        continue;
      if ($field->COLUMN_KEY <> "PRI"){
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
            $inputs .= "\t\t\t\t\t\t\t\t<option value='{$item}' <?= \$response['data'][0]['{$field->COLUMN_NAME}'] == '{$item}' ? 'selected' : '' ?>> {$item} </option>\n";
          }
          $inputs .= "\t\t\t\t\t\t\t</select>\n";
        } else {
          $hasFK = FALSE;
          foreach ($foreignKeys as $key => $fk) {
            if($fk->COLUMN_NAME == $field->COLUMN_NAME){
              $hasFK = TRUE;
              if ($tableFilho !== null && $fk->COLUMN_NAME == $tableFilho->REFERENCED_COLUMN_NAME){
                $inputs .= "\t\t\t\t\t\t\t<input type='hidden' name='{$field->COLUMN_NAME}' value='<?= \$IdParent ?>' required>\n";
                $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull} disabled>\n";
                $inputs .= "\t\t\t\t\t\t\t\t<?= getOptionToSelect('{$fk->REFERENCED_TABLE_NAME}','{$fk->REFERENCED_COLUMN_NAME}', '', \$IdParent, \$login->data->token) ?>\n";
                $inputs .= "\t\t\t\t\t\t\t</select>\n";
              } else {
                $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull}>\n";
                $inputs .= "\t\t\t\t\t\t\t\t<?= getOptionToSelect('{$fk->REFERENCED_TABLE_NAME}','{$fk->REFERENCED_COLUMN_NAME}', '', \$response['data'][0]['{$field->COLUMN_NAME}'], \$login->data->token) ?>\n";
                $inputs .= "\t\t\t\t\t\t\t</select>\n";
              }
            }
          }          
          if(!$hasFK)
            $inputs .= "\t\t\t\t\t\t\t<input type='{$type}' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='form-control' placeholder='{$field->COLUMN_COMMENT}' value='<?= \$response['data'][0]['{$field->COLUMN_NAME}'] ?>' {$isnull}>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php if(isset(\$response)): ?>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<div class='invalid-feedback' style='display:block'><?= isset(\$response['error']['{$field->COLUMN_NAME}']) ? \$response['error']['{$field->COLUMN_NAME}'] : ''; ?></div>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php endif; ?>\n";
        }
        $inputs .= "\t\t\t\t\t\t</div>\n";
        
      }
    }
    return $inputs;
  }

  private function getInputView($fields, $foreignKeys){
    $inputs = "";
    foreach ($fields as $key => $field) {
      if(strpos(strtolower($field->COLUMN_NAME), 'usersid') !== FALSE)
        continue;
      if ($field->COLUMN_KEY <> "PRI"){
        $inputs .= "\t\t\t\t\t\t\t<div class='form-group'>\n";
        $inputs .= "\t\t\t\t\t\t\t\t<label for='{$field->COLUMN_NAME}'>{$field->COLUMN_COMMENT}</label>\n";
        $type = $this->getType($field);
        $isnull = $field->IS_NULLABLE == "NO" ? "required" : "";
        if($type == "select"){
          $list = str_replace(")", "", str_replace("'", "", str_replace("enum(", "", $field->COLUMN_TYPE)));
          $items = explode(",", $list);
          $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull} disabled>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<option value=''> Selecione </option>\n";
          foreach ($items as $key => $item) {
            $inputs .= "\t\t\t\t\t\t\t\t<option value='{$item}' <?= isset(\$response['data'][0]['{$field->COLUMN_NAME}']) ? \$response['data'][0]['{$field->COLUMN_NAME}'] == '{$item}' ? 'selected' : '' : '' ?>> {$item} </option>\n";
          }
          $inputs .= "\t\t\t\t\t\t\t</select>\n";
        } else {
          $hasFK = FALSE;
          foreach ($foreignKeys as $key => $fk) {
            if($fk->COLUMN_NAME == $field->COLUMN_NAME){
              $hasFK = TRUE;
              $inputs .= "\t\t\t\t\t\t\t<select name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='custom-select' placeholder='{$field->COLUMN_COMMENT}' {$isnull} disabled>\n";
              $inputs .= "\t\t\t\t\t\t\t\t<?= getOptionToSelect('{$fk->REFERENCED_TABLE_NAME}','{$fk->REFERENCED_COLUMN_NAME}', '', isset(\$response['data'][0]['{$field->COLUMN_NAME}']) ? \$response['data'][0]['{$field->COLUMN_NAME}'] : '', \$login->data->token) ?>\n";
              $inputs .= "\t\t\t\t\t\t\t</select>\n";
            }
          }
          if(!$hasFK)
            $inputs .= "\t\t\t\t\t\t\t<input type='{$type}' name='{$field->COLUMN_NAME}' id='{$field->COLUMN_NAME}' class='form-control' placeholder='{$field->COLUMN_COMMENT}' value='<?= isset(\$response['data'][0]['{$field->COLUMN_NAME}']) ? \$response['data'][0]['{$field->COLUMN_NAME}'] : '{$field->COLUMN_DEFAULT}' ?>' {$isnull} disabled>\n";
          $inputs .= "\t\t\t\t\t\t\t<?php if(isset(\$response)): ?>\n";
          $inputs .= "\t\t\t\t\t\t\t\t<div class='invalid-feedback' style='display:block'><?= isset(\$response['error']['{$field->COLUMN_NAME}']) ? \$response['error']['{$field->COLUMN_NAME}'] : ''; ?></div>\n";
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

  private function getFieldPK($fields){
    foreach ($fields as $key => $field) {
      if ($field->COLUMN_KEY == "PRI"){
        return $field;
      }
    }
  }

  private function buildViewGridReferencial($tabelaFilho, $tabelasFilhoFilho){
    $table = $tabelaFilho->TABLE;
    $fields = $tabelaFilho->TABLE->FIELDS;
    $foreignKeys = $tabelaFilho->TABLE->FOREIGNKEYS;
  }

  private function buildViewViewReferencial($tabelaFilho, $tabelasFilhoFilho){
    $table = $tabelaFilho->TABLE;
    $fields = $tabelaFilho->TABLE->FIELDS;
    $foreignKeys = $tabelaFilho->TABLE->FOREIGNKEYS;

    $nameClassPai = ucfirst($tabelaFilho->REFERENCED_TABLE_NAME);
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputView($fields, $foreignKeys);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilhoFilho, $fieldPK);

    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClassPai}') ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Alteração do registro</legend> <!-- .form-group -->
{$inputs}
                      </fieldset><!-- /.fieldset -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->

";
    $this->saveFile($nameClass, "View".$nameClass, $view);
  }

  private function buildViewCreateReferencial($tabelaFilho, $tabelasFilhoFilho){
    $table = $tabelaFilho->TABLE;
    $fields = $tabelaFilho->TABLE->FIELDS;
    $foreignKeys = $tabelaFilho->TABLE->FOREIGNKEYS;


    $nameClassPai = ucfirst($tabelaFilho->REFERENCED_TABLE_NAME);
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputInsert($fields, $foreignKeys, $tabelaFilho);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilhoFilho, $fieldPK);
    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClassPai}/'.\$parentView.'/'.\$IdParent) ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar Matheus</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                    <?= form_open(base_url('{$nameClass}/add/'.\$parentView.'/'.\$IdParent)); ?>
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Adicionar um novo registro</legend> <!-- .form-group -->
{$inputs}
                        <div class='form-actions'>
                          <button class='btn btn-primary mr-3' type='submit'>Salvar</button>
                          <div class='form-group mt-2'>
														<label class='switcher-control'> 
															<input type='checkbox' name='cbxSaveBack' class='switcher-input' <?= isset(\$response['data']['cbxSaveBack']) ? 'checked' : '' ?>>
															<span class='switcher-indicator'></span>
															<span class='switcher-label-on'><i class='fas fa-check'></i></span>
															<span class='switcher-label-off'><i class='fas fa-times'></i></span>
														</label>
														<span>Salvar e voltar?</span>
													</div>
                          <button class='btn btn-secondary ml-auto' type='button' onclick=\"window.location.href='<?= base_url('{$nameClassPai}/'.\$parentView.'/'.\$IdParent) ?>'\">Cancelar</button>
                        </div>
                      </fieldset><!-- /.fieldset -->
                    <?= form_close(); ?><!-- /.form -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->
";
    $this->saveFile($nameClass, "Create".$nameClass, $view);
  }

  private function buildViewUpdateReferencial($tabelaFilho, $tabelasFilhoFilho){
    $table = $tabelaFilho->TABLE;
    $fields = $tabelaFilho->TABLE->FIELDS;
    $foreignKeys = $tabelaFilho->TABLE->FOREIGNKEYS;

    $nameClassPai = ucfirst($tabelaFilho->REFERENCED_TABLE_NAME);
    $nameClass = ucfirst($table->TABLE_NAME);
    $inputs = $this->getInputUpdate($fields, $foreignKeys, $tabelaFilho);
    $fieldPK = $this->getFieldPK($fields);
    $gridFilhos = $this->buildViewGridFilho($tabelasFilhoFilho, $fieldPK);
    $view = "
    <!-- .app-main -->
    <main class='app-main'>
      <!-- .wrapper -->
      <div class='wrapper'>
        <!-- .page -->
        <div class='page'>
          <?php if(isset(\$response)): ?>
            <?php if(\$response['method'] !== 'GET'): ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                  <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                    <span aria-hidden='true'><i class='fa fa-times'></i></span>
                  </a>
              </div><!-- /.page-message -->
              <?php else: ?>
              <!-- .page-message -->
              <div class='page-message bg-success' role='alert'>
                <span class='mr-5'><?= \$response['message'] ?></span>
                <a href='#' class='btn btn-sm btn-icon btn-success' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->    
              <?php endif; ?>
            <?php else: ?>
              <?php if(\$response['status'] == 'FALSE'): ?>
              <!-- .page-message -->
              <div class='page-message bg-warning' role='alert'>
                <span class='mr-5'>Falha ao consultar o registro!</span>
                <a href='#' class='btn btn-sm btn-icon btn-warning' aria-label='Close' onclick='$(this).parent().fadeOut()'>
                  <span aria-hidden='true'><i class='fa fa-times'></i></span>
                </a>
              </div><!-- /.page-message -->
              <?php endif; ?>
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
                    <a href='<?= base_url('{$nameClassPai}/'.\$parentView.'/'.\$IdParent) ?>'><i class='breadcrumb-icon fa fa-angle-left mr-2'></i>Voltar Matheus</a>
                  </li>
                </ol>
              </nav><!-- /.breadcrumb -->
            </header><!-- /.page-title-bar -->
            <!-- .page-section -->
            <div class='page-section'>
              <!-- .section-block -->
              <div class='section-block'>            
                <!-- .page-title-bar -->
                <header class='page-title-bar'>
                  <!-- page title stuff goes here -->
                  <h1 class='page-title'> {$table->TABLE_COMMENT} </h1>
                </header><!-- /.page-title-bar -->
                <!-- .base-style -->
                <div id='base-style' class='card'>
                  <!-- .card-body -->
                  <div class='card-body'>
                    <!-- .form -->
                    <?= form_open(base_url('{$nameClass}/update/'.\$parentView.'/'.\$IdParent.'/'.\$response['data'][0]['{$fieldPK->COLUMN_NAME}'])); ?>
                      <!-- .fieldset -->
                      <fieldset>
                        <legend>Alteração do registro</legend> <!-- .form-group -->
{$inputs}
                        <div class='form-actions'>
                          <button class='btn btn-primary mr-3' type='submit'>Salvar</button>
                          <div class='form-group mt-2'>
														<label class='switcher-control'> 
															<input type='checkbox' name='cbxSaveBack' class='switcher-input' <?= isset(\$response['data']['cbxSaveBack']) ? 'checked' : '' ?>>
															<span class='switcher-indicator'></span>
															<span class='switcher-label-on'><i class='fas fa-check'></i></span>
															<span class='switcher-label-off'><i class='fas fa-times'></i></span>
														</label>
														<span>Salvar e voltar?</span>
													</div>
                          <button class='btn btn-secondary ml-auto' type='button' onclick=\"window.location.href='<?= base_url('{$nameClassPai}/'.\$parentView.'/'.\$IdParent) ?>'\">Cancelar</button>
                        </div>
                      </fieldset><!-- /.fieldset -->
                    <?= form_close(); ?><!-- /.form -->
                  </div><!-- /.card-body -->
                </div><!-- /.base-style -->
              </div><!-- /.section-block -->
            </div><!-- /.page-section -->
{$gridFilhos}
          </div><!-- /.page-inner -->
        </div><!-- /.page -->
      </div><!-- /.wrapper -->
    </main><!-- /.app-main -->
";
    $this->saveFile($nameClass, "Edit".$nameClass, $view);
  }
}