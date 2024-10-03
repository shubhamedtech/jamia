<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
?>
<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingExamSessions">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseExamSessions" aria-expanded="false" aria-controls="collapseExamSessions">
        Exam Sessions
      </a>
    </div>
  </div>
  <div id="collapseExamSessions" class="collapse" role="tabcard" aria-labelledby="headingExamSessions">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-right">
          <button type="button" class="btn btn-primary" onclick="addComponents('exam-sessions', 'lg', <?= $university_id ?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableExamSessions">
              <thead>
                <tr>
                  <th width="20%">Name</th>
                  <th>Admission Sessions</th>
                  <th data-orderable="false">Re-Reg</th>
                  <th data-orderable="false">Back-Paper</th>
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
  var table = $('#tableExamSessions');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url': '/app/components/exam-sessions/server',
      type: 'POST',
      "data": function(data) {
        data.university_id = '<?= $university_id ?>';
      },
    },
    'columns': [{
        data: "Name"
      },
      {
        data: "Admission_Session"
      },
      {
        data: "RR_Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeRRStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "BP_Status",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeBPStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "ID",
        "render": function(data, type, row) {
          return '<div class="text-end">\
            <i class="fa text-warning fa-edit icon-xs cursor-pointer" onclick="editComponents(\'exam-sessions\', \'' + data + '\', \'lg\');"></i>\
            <i class="fa text-danger fa-trash icon-xs cursor-pointer" onclick="destroyComponents(\'exam-sessions\', \'ExamSessions\', \'' + data + '\');"></i>\
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
      var fname = $(this).data('fname');
      if (fname == "changeRRStatus") {
        changeRRStatus(rowId);
      } else if (fname == "changeBPStatus") {
        changeBPStatus(rowId)

      }
    });
  });

  function changeRRStatus(id) {
    $.ajax({
      url: '/app/components/exam-sessions/rr_status?id=' + id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
          $('#tableExamSessions').DataTable().ajax.reload(null, false);
        } else {
          notification('danger', data.message);
        }
      }
    })
  }

  function changeBPStatus(id) {
    $.ajax({
      url: '/app/components/exam-sessions/bp_status?id=' + id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
          $('#tableExamSessions').DataTable().ajax.reload(null, false);
        } else {
          notification('danger', data.message);
        }
      }
    })
  }
</script>