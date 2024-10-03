<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingFeeStructures">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFeeStructures" aria-expanded="false" aria-controls="collapseFeeStructures">
        Fee Structures
      </a>
    </div>
  </div>
  <div id="collapseFeeStructures" class="collapse" role="tabcard" aria-labelledby="headingFeeStructures">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-right">
          <button type="button" class="btn btn-primary" onclick="addComponents('fee-structures', 'md', <?= $university_id ?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableFeeStructures">
              <thead>
                <tr>
                  <th width="20%">Name</th>
                  <th data-orderable="false">Sharing</th>
                  <th data-orderable="false">Constant</th>
                  <th data-orderable="false">Applicable on</th>
                  <th data-orderable="false">Status</th>
                  <th data-orderable="false">Actions</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table = $('#tableFeeStructures');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url': '/app/components/fee-structures/server',
      type: 'POST',
      "data": function(data) {
        data.university_id = '<?= $university_id ?>';
      },
    },
    'columns': [{
        data: "Name"
      },
      {
        data: "Sharing"
      },
      {
        data: "Is_Conctant"
      },
      {
        data: "Applicable"
      },
      {
        data: "Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "ID",
        "render": function(data, type, row) {
          return '<div class="text-end">\
            <i class="fa text-warning fa-edit icon-xs cursor-pointer" onclick="editComponents(\'fee-structures\', \'' + data + '\', \'md\');"></i>\
            <i class="fa text-danger fa-trash icon-xs cursor-pointer" onclick="destroyComponents(\'fee-structures\', \'FeeStructures\', \'' + data + '\');"></i>\
          </div>'
        }
      },
    ],
    "sDom": "<t><'row'<p i>>",
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
      "sLengthMenu": "_MENU_ ",
      "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 5,
    "initComplete": function() {
      $('.bs_switch').bootstrapSwitch();
    }
  };

  table.dataTable(settings);
  table.on('draw.dt', function() {
    $('.bs_switch').bootstrapSwitch();
    $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
      var rowId = $(this).data('row-id');
      changeComponentStatus('Fee-Structures', 'FeeStructures', rowId);
    });
  });
</script>