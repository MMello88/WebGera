"uses strict";
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }
var DTRegsa100efdc =
function () {
  function DTRegsa100efdc() {
    _classCallCheck(this, DTRegsa100efdc);
    this.init();
  }

  _createClass(DTRegsa100efdc, [{
    key: "init",
    value: function init() {
      this.table = this.table();
      this.searchRecords();
      this.selecter();
      this.clearSelected();
      this.setbtnFloatedAdd();
      this.setDeleteClick();
      this.table.buttons().container().appendTo('#dt-buttons').unwrap();
    }
  }, {
    key: "table",
    value: function table() {
      return $('#myTableRegsa100efdc').DataTable({
        dom: "<'text-muted'Bi>\n        <'table-responsive'tr>\n        <'mt-4'p>",
        buttons: ['copyHtml5', {
          extend: 'print', 
          autoPrint: false
        }],
        language: {
          paginate: {
            previous: '<i class="fa fa-lg fa-angle-left"></i>',
            next: '<i class="fa fa-lg fa-angle-right"></i>'
          }
        },
        autoWidth: false,
        ajax: url_get,
        deferRender: true,
        order: [27, 'desc'],
        columns: [
  				{
					data: 'a100_Id',
					className: 'col-checker align-middle',
					orderable: false,
					searchable: false
				},
				{
					data: 'a100_Id',
					className: 'align-middle text-right',
					orderable: false,
					searchable: false
				},
				{
					data: 'a100_Reg',
					className: 'align-middle',
				},
				{
					data: 'a100_CNPJ',
					className: 'align-middle',
				},
				{
					data: 'a100_IndicadorTipoOperacao',
					className: 'align-middle',
				},
				{
					data: 'a100_IndicadorEmitenteDocumento',
					className: 'align-middle',
				},
				{
					data: 'Reg0150EFDCId',
					className: 'align-middle',
				},
				{
					data: 'a100_SituacaoDocumentoFiscal',
					className: 'align-middle',
				},
				{
					data: 'a100_Ser',
					className: 'align-middle',
				},
				{
					data: 'a100_Sub',
					className: 'align-middle',
				},
				{
					data: 'a100_NumDoc',
					className: 'align-middle',
				},
				{
					data: 'a100_ChaveEletronica',
					className: 'align-middle',
				},
				{
					data: 'a100_DtEmissaoDoc',
					className: 'align-middle',
				},
				{
					data: 'a100_DtConclusaoServico',
					className: 'align-middle',
				},
				{
					data: 'a100_VlDocumento',
					className: 'align-middle',
				},
				{
					data: 'a100_IndicadorTipoPagamento',
					className: 'align-middle',
				},
				{
					data: 'a100_VlDesconto',
					className: 'align-middle',
				},
				{
					data: 'a100_VlBcPis',
					className: 'align-middle',
				},
				{
					data: 'a100_VlPis',
					className: 'align-middle',
				},
				{
					data: 'a100_VlBcCofins',
					className: 'align-middle',
				},
				{
					data: 'a100_VlCofins',
					className: 'align-middle',
				},
				{
					data: 'a100_VlPisRetido',
					className: 'align-middle',
				},
				{
					data: 'a100_VlConfinsRetido',
					className: 'align-middle',
				},
				{
					data: 'a100_VlIss',
					className: 'align-middle',
				},
				{
					data: 'a100_DtIni',
					className: 'align-middle',
				},
				{
					data: 'a100_DtFin',
					className: 'align-middle',
				},
				{
					data: 'a100_PessoaJuridicaId',
					className: 'align-middle',
				},
				{
					data: 'a100_UsersId',
					className: 'align-middle',
				},

        ],
        columnDefs: [{
          targets: 0,
          render: function render(data, type, row, meta) {
            return `
            <div class='custom-control custom-control-nolabel custom-checkbox'>
              <input type='checkbox' class='custom-control-input' name='selectedRow[]' id='p${row['a100_Id']}' value='${row['a100_Id']}'>
              <label class='custom-control-label' for='p${row['a100_Id']}'></label>
            </div>`;
          }
        },{
          targets: 1,
          render: function render(data, type, row, meta) {
            return `
            <a class='btn btn-sm btn-icon btn-secondary' href='${url_view}/${data}'>
              <i class='fa fa-eye'></i>
            </a>
            <a class='btn btn-sm btn-icon btn-secondary' href='${url_upd}/${data}'>
              <i class='fa fa-pencil-alt'></i>
            </a>
            <a class='btn btn-sm btn-icon btn-secondary' id='btnDeleteClick' data-toggle='modal' data-target='#modalDeleteRegistro' data-id='${data}' href='#${data}'>
              <i class='far fa-trash-alt'></i>
            </a>
            `;
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
    key: 'setDeleteClick',
    value: function setDeleteClick(){
      var self = this;
      $('#modalDeleteRegistro').on('show.bs.modal', function (event) {
        document.getElementById('DeleteById').value = $(event.relatedTarget).data('id');
      })
    }
  },{
    key: "searchRecords",
    value: function searchRecords() {
      var self = this;
      $('#table-search, #filterBy').on('keyup change focus', function (e) {
        var filterBy = $('#filterBy').val();
        var hasFilter = filterBy !== '';
        var value = $('#table-search').val(); // clear selected rows

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
    key: "getSelectedInfo",
    value: function getSelectedInfo() {
      var $selectedRow = $('input[name="selectedRow[]"]:checked').length;
      var $info = $('.thead-btn');
      var $badge = $('<span/>').addClass('selected-row-info text-muted pl-1').text("".concat($selectedRow, " selected")); // remove existing info

      $('.selected-row-info').remove(); // add current info

      if ($selectedRow) {
        $info.prepend($badge);
      }
    }
  }, {
    key: "selecter",
    value: function selecter() {
      var self = this;
      $(document).on('change', '#check-handle', function () {
        var isChecked = $(this).prop('checked');
        $('input[name="selectedRow[]"]').prop('checked', isChecked); // get info

        self.getSelectedInfo();
      }).on('change', 'input[name="selectedRow[]"]', function () {
        var $selectors = $('input[name="selectedRow[]"]');
        var $selectedRow = $('input[name="selectedRow[]"]:checked').length;
        var prop = $selectedRow === $selectors.length ? 'checked' : 'indeterminate'; // reset props

        $('#check-handle').prop('indeterminate', false).prop('checked', false);

        if ($selectedRow) {
          $('#check-handle').prop(prop, true);
        } // get info


        self.getSelectedInfo();
      });
    }
  }, {
    key: "clearSelected",
    value: function clearSelected() {
      var self = this; // clear selected rows

      $('#myTableRegsa100efdc').on('page.dt', function () {
        self.clearSelectedRows();
      });
      $('#clear-search').on('click', function () {
        self.clearSelectedRows();
      });
    }
  }, {
    key: "clearSelectedRows",
    value: function clearSelectedRows() {
      $('#check-handle').prop('indeterminate', false).prop('checked', false).trigger('change');
    }
  }]);

  return DTRegsa100efdc;
}();

$(document).on('theme:init', function () {
  new DTRegsa100efdc();
});
