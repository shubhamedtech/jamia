<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingPageAccess">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapsePageAccess" aria-expanded="false" aria-controls="collapsePageAccess">
        Page Access
      </a>
    </div>
  </div>
  <div id="collapsePageAccess" class="collapse" role="tabcard" aria-labelledby="headingPageAccess">
    <div class="card-body">
      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tablePageAccess">
              <thead>
                <tr>
                  <th width="30%">Name</th>
                  <th data-orderable="false">Inhouse</th>
                  <th data-orderable="false">Center</th>
                  <th data-orderable="false">Sub-Center</th>
                  <th data-orderable="false">Student</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$lms = false;
$has_lms = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Has_LMS = 1");
if ($has_lms->num_rows > 0) {
  $lms = true;
}
?>

<script type="text/javascript">
  var hasLMS = '<?= $lms ?>';
  var table = $('#tablePageAccess');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url': '/app/components/page-access/server',
      type: 'POST',
      "data": function(data) {
        data.university_id = '<?= $university_id ?>';
      },
    },
    'columns': [{
        data: "Name"
      },
      {
        data: "Inhouse",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeInhouseStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "Center",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeCenterStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "Sub_Center",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeSubCenterStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        }
      },
      {
        data: "Student",
        "render": function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<input class="bs_switch" data-size="small" type="checkbox" data-fname="changeStudentStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
        },
        visible: hasLMS == 1 ? true : false
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
    "iDisplayLength": 100,
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
      if (fname == "changeInhouseStatus") {
        changeInhouseStatus(rowId);
      } else if (fname == "changeCenterStatus") {
        changeCenterStatus(rowId)
      } else if (fname == "changeSubCenterStatus") {
        changeSubCenterStatus(rowId)
      } else if (fname == "changeStudentStatus") {
        changeStudentStatus(rowId)
      }
    });
  });
</script>

<script type="text/javascript">
  function changeInhouseStatus(id) {
    $.ajax({
      url: '/app/components/page-access/inhouse?id=' + id + '&university_id=<?= $university_id ?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
        } else {
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeCenterStatus(id) {
    $.ajax({
      url: '/app/components/page-access/center?id=' + id + '&university_id=<?= $university_id ?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
        } else {
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeSubCenterStatus(id) {
    $.ajax({
      url: '/app/components/page-access/sub-center?id=' + id + '&university_id=<?= $university_id ?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
        } else {
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }

  function changeStudentStatus(id) {
    $.ajax({
      url: '/app/components/page-access/student?id=' + id + '&university_id=<?= $university_id ?>',
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.status == 200) {
          notification('success', data.message);
        } else {
          notification('danger', data.message);
        }
        $('#tablePageAccess').DataTable().ajax.reload(null, false);
      }
    })
  }
</script>