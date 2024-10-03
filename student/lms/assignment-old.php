<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<div class="wrapper boxed-wrapper">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>
  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">
        <div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="card card-transparent">
        <div class="card-header">
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="subjects-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap w-100" id="subjects_table">
              <thead>
                <tr>
                  <th>Course Name</th>
                  <th>Subjects Name</th>
                  <th>Assignment Name</th>
                  <th>Total Marks</th>
                  <th>Obtained Marks</th>
                  <th>Teacher Status</th>
                  <th>Remark</th>
                  <th>Start Name</th>
                  <th>End Date</th>
                  <th>Student Status</th>
                  <th>Teacher Assignment</th>
                  <th>Student Assignment</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
</div>
<script>
  var role = '<?= $_SESSION['Role'] ?>';
  var table = $('#subjects_table');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, 'All']
    ],
    'ajax': {
      'url': '/app/assignments/student_get_assignments_details'
    },
    'columns': [{
        data: "Grade"
      },
      {
        data: "subject_name"
      },
      {
        data: "assignment_name"
      },
      {
        data: "marks"
      },
      {
        data: "obt_mark"
      },
      {
        data: "status"
      },
      {
        data: "reason"
      },
      {
        data: "start_date"
      },
      {
        data: "end_date"
      },
      {
        data: "assignment_submission_status"
      },
      {
        data: "assignment_file",
        render: function(data, type, row) {
          var path = '/../uploads/assignments/';
          var fileLink;
          if (row.File_Type && row.File_Type.toLowerCase() === 'pdf') {
            fileLink = '<a href="' + path + data + '" class="btn btn-danger btn btn-sm" download>Download Assignment</a>';
          } else {
            fileLink = '<a href="' + path + data + '" class="btn btn-primary btn btn-sm" download>Download Assignment</a>';
          }
          return fileLink;
        }
      },
      {
        data: "student_file",
        render: function(data, type, row) {
          var path = '../../uploads/assignments/';
          var file = '';
          if (row.File_Type && row.File_Type.toLowerCase() === 'pdf') {
            file = '<a href="' + path + data + '" class="btn btn-danger btn-sm" download>Download Assignment</a>';
          } else {
            file = '<a href="' + path + data + '" class="btn btn-danger btn-sm" download>Download Assignment</a>';
          }
          return file;
        }
      },
      {
        data: "student_file",
        render: function(data, type, row) {
          var uploadDir = '../../uploads/assignments/';
          var filePath = uploadDir + data;
          var button = '';
          if (row.status !== 'Rejected') {
            if (data && row.file_exists) {
              button += '<a href="' + filePath + '" class="btn btn-danger btn-sm" download>Download Assignment</a>';
            }
            button += '<button class="btn btn-warning btn-sm" data-toggle="modal" onclick=\'openUploadModal("' + row.id + '", "' + row.subject_id + '")\'>Upload Assignment</button>';
          } else {
            if (data && row.file_exists) {
              button += '<a href="' + filePath + '" class="btn btn-danger btn-sm" download>Download Updated Assignment</a>';
            }
            button += '<button class="btn btn-primary btn-sm" data-toggle="modal" onclick=\'openUploadModal("' + row.id + '", "' + row.subject_id + '")\'>Reupload Assignment</button>';
          }
          return button;
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
    "iDisplayLength": 25
  };

  let res = table.dataTable(settings);
  $('#subjects-search-table').keyup(function() {
    table.fnFilter($(this).val());
  });
</script>
<script type="text/javascript">
  function openUploadModal(id, subject_id) {
    $.ajax({
      url: '/app/assignments/student-result-review',
      type: 'GET',
      data: {
        id,
        subject_id
      },
      success: function(data) {
        console.log(data);
        $('#md-modal-content').html(data);
        $('#mdmodal').modal('show');
      }
    });
  }
</script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>