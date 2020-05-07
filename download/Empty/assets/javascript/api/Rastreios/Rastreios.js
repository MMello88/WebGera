"uses strict";
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }
var DTRastreios =
function () {
  function DTRastreios() {
    _classCallCheck(this, DTRastreios);
    this.init();
  }

  _createClass(DTRastreios, [{
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
      return $('#myTableRastreios').DataTable({
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
        ajax: url_get_rastreios,
        deferRender: true,
        order: [5, 'desc'],
        columns: [
  				{
					data: 'stt_Id',
					className: 'col-checker align-middle',
					orderable: false,
					searchable: false
				},
				{
					data: 'stt_Id',
					className: 'align-middle text-right',
					orderable: false,
					searchable: false
				},
				{
					data: 'stt_Status',
					className: 'align-middle',
				},
				{
					data: 'stt_Motivo',
					className: 'align-middle',
				},
				{
					data: 'stt_AlbumsId',
					className: 'align-middle',
				},
				{
					data: 'stt_UsersID',
					className: 'align-middle',
				},

        ],
        columnDefs: [{
          targets: 0,
          render: function render(data, type, row, meta) {
            return `
            <div class='custom-control custom-control-nolabel custom-checkbox'>
              <input type='checkbox' class='custom-control-input' name='selectedRow[]' id='p${row['stt_Id']}' value='${row['stt_Id']}'>
              <label class='custom-control-label' for='p${row['stt_Id']}'></label>
            </div>`;
          }
        },{
          targets: 1,
          render: function render(data, type, row, meta) {
            return `
            <a class='btn btn-sm btn-icon btn-secondary' href='${url_view_rastreios}/${data}'>
              <i class='fa fa-eye'></i>
            </a>
            <a class='btn btn-sm btn-icon btn-secondary' href='${url_upd_rastreios}/${data}'>
              <i class='fa fa-pencil-alt'></i>
            </a>
            <a class='btn btn-sm btn-icon btn-secondary' id='btnDeleteClick' data-toggle='modal' data-target='#modalDeleteRegistrorastreios' data-stt_id='${data}' href='#${data}'>
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
      $('#modalDeleteRegistrorastreios').on('show.bs.modal', function (event) {
        document.getElementById('DeleteBystt_id').value = $(event.relatedTarget).data('stt_id');
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

      $('#myTableRastreios').on('page.dt', function () {
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

  return DTRastreios;
}();

$(document).on('theme:init', function () {
  new DTRastreios();
});
