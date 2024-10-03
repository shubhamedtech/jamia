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
                  <th>Assignment</th>
                  <!-- <th>Student Assignment</th> -->
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
          if (row.uploadingIsActive === 0) {
            return "Not Downloads";
          }
          if (row.File_Type && row.File_Type.toLowerCase() === 'pdf' && row.uploadingIsActive == 1) {
            fileLink = '<a href="' + path + data + '" class="btn btn-success btn btn-sm" download>Download</a>';
          } else {
            fileLink = '<a href="' + path + data + '" class="btn btn-success btn btn-sm" download>Download</a>';
          }
          return fileLink;
        }
      },
      {
        data: "student_file",
        render: function(data, type, row) {
          var button = '';
          var fileLinks = '';
          if (row.uploadingIsActive === 0) {
            return "Date Over";
          } else if (row.assignment_submission_status == "NOT RESUBMITTED") {
            button = '<button class="btn btn-secondary btn-sm" data-toggle="modal" onclick=\'openUploadModal("' + row.id + '", "' + row.subject_id + '")\'>ReUpload Assignment</button>';
          } else if (row.assignment_submission_status == "RESUBMITTED" || row.assignment_submission_status.trim() == "SUBMITTED") {
            var files = data.split(',').map(file => encodeURIComponent(file.trim())).join(',');
            var zipLink = '/app/assignments/stu_zip_files.php?files=' + files + '&subject_name=' + encodeURIComponent(row.subject_name);
            fileLinks += '<a href="' + zipLink + '" class="btn btn-danger btn-sm" download>Download Assignments</a> ';
          } else if (row.assignment_submission_status.trim() == "NOT SUBMITTED" && row.uploadingIsActive == 1) {
            button = '<button class="btn btn-primary btn-sm" data-toggle="modal" onclick=\'openUploadModal("' + row.id + '", "' + row.subject_id + '")\'>Upload Assignment</button>';
          }
          return button + fileLinks;
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