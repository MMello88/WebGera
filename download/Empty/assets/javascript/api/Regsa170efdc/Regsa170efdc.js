"uses strict";
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }
var DTRegsa170efdc =
function () {
  function DTRegsa170efdc() {
    _classCallCheck(this, DTRegsa170efdc);
    this.init();
  }

  _createClass(DTRegsa170efdc, [{
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
      return $('#myTableRegsa170efdc').DataTable({
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
        order: [21, 'desc'],
        columns: [
  				{
					data: 'a170_Id',
					className: 'col-checker align-middle',
					orderable: false,
					searchable: false
				},
				{
					data: 'a170_Id',
					className: 'align-middle text-right',
					orderable: false,
					searchable: false
				},
				{
					data: 'a170_Reg',
					className: 'align-middle',
				},
				{
					data: 'a170_NumItem',
					className: 'align-middle',
				},
				{
					data: 'a170_Reg0200EFDCId',
					className: 'align-middle',
				},
				{
					data: 'a170_DescricaoComplementar',
					className: 'align-middle',
				},
				{
					data: 'a170_VlItem',
					className: 'align-middle',
				},
				{
					data: 'a170_VlDesconto',
					className: 'align-middle',
				},
				{
					data: 'a170_Ref431Id',
					className: 'align-middle',
				},
				{
					data: 'a170_IndicadorOrigemCredito',
					className: 'align-middle',
				},
				{
					data: 'a170_Ref433Id',
					className: 'align-middle',
				},
				{
					data: 'a170_VlBcPis',
					className: 'align-middle',
				},
				{
					data: 'a170_VlAliqPis',
					className: 'align-middle',
				},
				{
					data: 'a170_VlPis',
					className: 'align-middle',
				},
				{
					data: 'a170_Ref434Id',
					className: 'align-middle',
				},
				{
					data: 'a170_VlBcCofins',
					className: 'align-middle',
				},
				{
					data: 'a170_VlAliqCofins',
					className: 'align-middle',
				},
				{
					data: 'a170_VlCofins',
					className: 'align-middle',
				},
				{
					data: 'a170_Reg0500EFDCId',
					className: 'align-middle',
				},
				{
					data: 'a170_Reg0600EFDCId',
					className: 'align-middle',
				},
				{
					data: 'a170_RegA100EFDCId',
					className: 'align-middle',
				},
				{
					data: 'a170_UsersId',
					className: 'align-middle',
				},

        ],
        columnDefs: [{
          targets: 0,
          render: function render(data, type, row, meta) {
            return `
            <div class='custom-control custom-control-nolabel custom-checkbox'>
              <input type='checkbox' class='custom-control-input' name='selectedRow[]' id='p${row['a170_Id']}' value='${row['a170_Id']}'>
              <label class='custom-control-label' for='p${row['a170_Id']}'></label>
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

      $('#myTableRegsa170efdc').on('page.dt', function () {
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

  return DTRegsa170efdc;
}();

$(document).on('theme:init', function () {
  new DTRegsa170efdc();
});
