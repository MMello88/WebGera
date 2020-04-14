<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GenerateClassJavascript {

  protected $CI;
  private $filename = "C:\\xampp\\htdocs\\WebSite\\assets\\javascript\\api\\";
  
  public function __constructor(){
  }

  public function init($nameTable = ""){
    $this->CI = &get_instance();
    $tables = $this->CI->gera->getTables($nameTable);
    foreach ($tables as $key => $table) {
      $fields = $this->CI->gera->getFields($table->TABLE_NAME);
      $this->buildJS($table, $fields);
    }
  }

  private function buildJS($table, $fields){
    $nomeClass = ucfirst($table->TABLE_NAME);
    $columns = $this->getFieldColumn($fields);
    $fieldPK = $this->getFieldColumnPK($fields);
    $lastNum = count($fields);
    $js  = "\"uses strict\";
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }
var DT{$nomeClass} =
function () {
  function DT{$nomeClass}() {
    _classCallCheck(this, DT{$nomeClass});
    this.init();
  }

  _createClass(DT{$nomeClass}, [{
    key: \"init\",
    value: function init() {
      this.table = this.table();
      this.searchRecords();
      this.selecter();
      this.clearSelected();
      this.table.buttons().container().appendTo('#dt-buttons').unwrap();
    }
  }, {
    key: \"table\",
    value: function table() {
      return \$('#myTable').DataTable({
        dom: \"<'text-muted'Bi>\\n        <'table-responsive'tr>\\n        <'mt-4'p>\",
        buttons: ['copyHtml5', {
          extend: 'print', 
          autoPrint: false
        }],
        language: {
          paginate: {
            previous: '<i class=\"fa fa-lg fa-angle-left\"></i>',
            next: '<i class=\"fa fa-lg fa-angle-right\"></i>'
          }
        },
        autoWidth: false,
        ajax: url_get,
        deferRender: true,
        order: [{$lastNum}, 'desc'],
        columns: [
  {$columns}
        ],
        columnDefs: [{
          targets: 0,
          render: function render(data, type, row, meta) {
            return `
            <div class='custom-control custom-control-nolabel custom-checkbox'>
              <input type='checkbox' class='custom-control-input' name='selectedRow[]' id='p\${row['{$fieldPK->COLUMN_NAME}']}' value='\${row['{$fieldPK->COLUMN_NAME}']}'>
              <label class='custom-control-label' for='p\${row['{$fieldPK->COLUMN_NAME}']}'></label>
            </div>`;
          }
        },{
          targets: {$lastNum},
          render: function render(data, type, row, meta) {
            return `
            <a class='btn btn-sm btn-icon btn-secondary' href='\${url_upd}/\${data}'>
              <i class='fa fa-pencil-alt'></i>
            </a>
            <a class='btn btn-sm btn-icon btn-secondary' href='#\${data}'>
              <i class='far fa-trash-alt'></i>
            </a>`;
          }
        }]
      });
    }
  },{
    key: 'setbtnFloatedAdd',
    value: function setbtnFloatedAdd(){
      var self = this;
      $('#btnFloatedAdd').on('click', function(e){
        self.table.ajax.reload();
      })
    }
  },{
    key: \"searchRecords\",
    value: function searchRecords() {
      var self = this;
      \$('#table-search, #filterBy').on('keyup change focus', function (e) {
        var filterBy = \$('#filterBy').val();
        var hasFilter = filterBy !== '';
        var value = \$('#table-search').val(); // clear selected rows

        if (value.length && (e.type === 'keyup' || e.type === 'change')) {
          self.clearSelectedRows();
        } // reset search term


        self.table.search('').columns().search('').draw();

        if (hasFilter) {
          self.table.columns(filterBy).search(value).draw();
        } else {
          self.table.search(value).draw();
        }
      });
    }
  }, {
    key: \"getSelectedInfo\",
    value: function getSelectedInfo() {
      var \$selectedRow = \$('input[name=\"selectedRow[]\"]:checked').length;
      var \$info = \$('.thead-btn');
      var \$badge = \$('<span/>').addClass('selected-row-info text-muted pl-1').text(\"\".concat(\$selectedRow, \" selected\")); // remove existing info

      \$('.selected-row-info').remove(); // add current info

      if (\$selectedRow) {
        \$info.prepend(\$badge);
      }
    }
  }, {
    key: \"selecter\",
    value: function selecter() {
      var self = this;
      \$(document).on('change', '#check-handle', function () {
        var isChecked = \$(this).prop('checked');
        \$('input[name=\"selectedRow[]\"]').prop('checked', isChecked); // get info

        self.getSelectedInfo();
      }).on('change', 'input[name=\"selectedRow[]\"]', function () {
        var \$selectors = \$('input[name=\"selectedRow[]\"]');
        var \$selectedRow = \$('input[name=\"selectedRow[]\"]:checked').length;
        var prop = \$selectedRow === \$selectors.length ? 'checked' : 'indeterminate'; // reset props

        \$('#check-handle').prop('indeterminate', false).prop('checked', false);

        if (\$selectedRow) {
          \$('#check-handle').prop(prop, true);
        } // get info


        self.getSelectedInfo();
      });
    }
  }, {
    key: \"clearSelected\",
    value: function clearSelected() {
      var self = this; // clear selected rows

      \$('#myTable').on('page.dt', function () {
        self.clearSelectedRows();
      });
      \$('#clear-search').on('click', function () {
        self.clearSelectedRows();
      });
    }
  }, {
    key: \"clearSelectedRows\",
    value: function clearSelectedRows() {
      \$('#check-handle').prop('indeterminate', false).prop('checked', false).trigger('change');
    }
  }]);

  return DT{$nomeClass};
}();

\$(document).on('theme:init', function () {
  new DT{$nomeClass}();
});
";
    $this->saveFile($nomeClass, $js);
  }

  private function saveFile($class, $txt){
    if(!file_exists($this->filename)){
      mkdir($this->filename);
    }

    if(!file_exists($this->filename . $class . "\\")){
      mkdir($this->filename . $class . "\\");
    }

    $filename = $this->filename . $class."\\".$class.".js";
    $file = fopen($filename, 'w+'); //Abre para leitura e escrita; coloca o ponteiro do arquivo no começo do arquivo e reduz o comprimento do arquivo para zero. Se o arquivo não existir, tenta criá-lo. 
    fwrite($file, $txt);
    fclose($file);
  }

  private function getFieldColumnPK($fields){
    $columns = "";
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY == "PRI"){
        return $field;
      }
    }
  }

  private function getFieldColumn($fields){
    $columns = "";
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY == "PRI"){
        $columns .= "\t\t\t\t{\n";
        $columns .= "\t\t\t\t\tdata: '{$field->COLUMN_NAME}',\n";
        $columns .= "\t\t\t\t\tclassName: 'col-checker align-middle',\n";
        $columns .= "\t\t\t\t\torderable: false,\n";
        $columns .= "\t\t\t\t\tsearchable: false\n";
        $columns .= "\t\t\t\t},\n";
      }
    }
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY <> "PRI"){
        $columns .= "\t\t\t\t{\n";
        $columns .= "\t\t\t\t\tdata: '{$field->COLUMN_NAME}',\n";
        $columns .= "\t\t\t\t\tclassName: 'align-middle',\n";
        $columns .= "\t\t\t\t},\n";
      }
    }
    foreach ($fields as $key => $field) {
      if($field->COLUMN_KEY == "PRI"){
        $columns .= "\t\t\t\t{\n";
        $columns .= "\t\t\t\t\tdata: '{$field->COLUMN_NAME}',\n";
        $columns .= "\t\t\t\t\tclassName: 'align-middle text-right',\n";
        $columns .= "\t\t\t\t\torderable: false,\n";
        $columns .= "\t\t\t\t\tsearchable: false\n";
        $columns .= "\t\t\t\t}\n";
      }
    }
    return $columns;
  }
}