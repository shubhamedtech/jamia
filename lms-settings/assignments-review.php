<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php');
include '../includes/db-config.php';
?>
<div class="wrapper boxed-wrapper">
  <!-- Topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- Menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
  <?php
  unset($_SESSION['filterByCourses']);
  unset($_SESSION['filterBySubCourses']);
  unset($_SESSION['filterByUser']);
  unset($_SESSION['submitted_students']);
  unset($_SESSION['filterByAssignment_status']);
  unset($_SESSION['filterByVerticalType']);
  unset($_SESSION['filterBySubjects']);
  unset($_SESSION['filterSubjectsID']);
  ?>
  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">
      </div>
    </div>
    <div class="content">
      <div class="card card-transparent">
        <div class="card-header pb-0">
          <div class="d-flex justify-content-start">
            <div class="col-md-2 m-b-10">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="vertical_type" onchange="addFilter(this.value, 'vertical_type')" data-placeholder="Choose Verticals">
                  <option value="">Choose Vertical</option>
                  <option value="1">IITS</option>
                  <option value="2">Edtech</option>
                </select>
              </div>
            </div>

            <?php if ($_SESSION['CanCreateSubCenter'] == "1" && $_SESSION['Role'] == "Center") { ?>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control sub_center" data-init-plugin="select2" id="center_sub_center" onchange="addFilter(this.value, 'users')" data-placeholder="Choose Sub Center">
                    <?php $sub_center_query = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Center_SubCenter LEFT JOIN Users ON Users.ID = Center_SubCenter.Sub_Center  WHERE Center_SubCenter.Center='" . $_SESSION['ID'] . "' AND Users.Role='Sub-Center'");
                    while ($subCenterArr = $sub_center_query->fetch_assoc()) { ?>
                      <option value="">Choose Sub Center</option>
                      <option value="<?= $subCenterArr['ID'] ?>"><?= $subCenterArr['Name'] . "(" . $subCenterArr['Code'] . ")"  ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            <?php } ?>
            <?php if ($_SESSION['Role'] == 'Administrator') { ?>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users','center')" data-placeholder="Choose User">

                  </select>
                </div>
              </div>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control sub_center" data-init-plugin="select2" id="sub_center" onchange="addSubCenterFilter(this.value, 'users','subcenter')" data-placeholder="Choose Sub Center">
                  </select>
                </div>
              </div>
            <?php } ?>
            <div class="col-md-2 m-b-10">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="sub_courses" onchange="addFilter(this.value,'courses')" data-placeholder="Choose Courses">
                  <option value="">Choose Courses</option>
                  <?php
                  $programs = $conn->query("SELECT Courses.ID, CONCAT(Courses.Name) as Name FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.University_ID =  " . $_SESSION['university_id'] . " $role_query GROUP BY Students.Course_ID;");
                  while ($program = $programs->fetch_assoc()) {
                    echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="subjects" onchange="addFilter(this.value, 'subjects')" data-placeholder="Choose Subjects">
                  <option value="All">Choose Subjects</option>
                </select>
              </div>
            </div>


            <div class="col-md-2">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="assignment_status" onchange="addFilter(this.value, 'assignment_status')">
                  <option value="All">Choose Status</option>
                  <option value="1">SUBMITTED</option>
                  <option value="2">NOT SUBMITTED</option>
                </select>
              </div>
            </div>

          </div>
          <div class="clearfix"></div>
        </div>

        <div class="card-header">
          <div class="pull-left">
            <div class="col-xs-12">
              <button class="btn btn-sm btn btn-primary" aria-label="Downloads Zip" data-toggle="tooltip" data-placement="top" title="Downloads Zip" onclick="add('zip_bulk_downloads','md')">Bulk Downloads</button>
            </div>
          </div>




          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="subject-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap w-100" id="subjects-table">
              <thead>
                <tr>
                  <th>Board Name</th>
                  <th>Based Vertical</th>
                  <th>Center/Sub-Center Name</th>
                  <th>Center/Sub-Center Code</th>
                  <th>Student Name</th>
                  <th>Enrollment No</th>
                  <th>Unique ID</th>
                  <th>DOB</th>
                  <th>Subject Name</th>
                  <th>Course Name</th>
                  <th>Course Short Name</th>
                  <th>Assignment Submission Date</th>
                  <th>Assignment Name</th>
                  <th>Total Mark</th>
                  <th>Obtained Mark</th>
                  <th>Remark</th>
                  <th>Student Status</th>
                  <th>Uploaded Type</th>
                  <th>Assignment Status</th>
                  <th>Evaluation Status</th>
                  <th>Download Assignments</th>
                  <th data-orderable="false">Assignment Upload</th>
                  <th data-orderable="false">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
  <script>
    var role = '<?= $_SESSION['Role'] ?>';
    var table = $('#subjects-table');
    var settings = {
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'All']
      ],
      'ajax': {
        'url': '/app/assignments/server'
      },
      'columns': [{
          data: "universityname"
        },
        {
          data: "verticaltype"
        },
        {
          data: "center_name"
        },
        {
          data: "center_code"
        },
        {
          data: "student_name"
        },
        {
          data: "Enrollment_No"
        },
        {
          data: "stu_unique_id"
        },
        {
          data: "dateofbirth"
        },
        {
          data: "subject_name"
        },
        {
          data: "course_name"
        },
        {
          data: "Short_Name"
        },
        {
          data: "created_date"
        },
        {
          data: "taskname"
        },
        {
          data: "total_marks"
        },
        {
          data: "obt_mark"
        },
        {
          data: "reason"
        },
        {
          data: "assignment_submission_status"
        },
        {
          data: "uploaded_type"
        },
        {
          data: "assignment_status"
        },
        {
          data: "eva_status"
        },
        {
          data: "pdffile",
          render: function(data, type, row) {
            var fileLinks = "";
            var path = '../../uploads/assignments/';
            if (row.assignment_status && row.assignment_status !== 'NOT CREATED') {
              if (row.uploaded_type == 'Manual' || row.uploaded_type == 'Online') {
                var files = data.split(',').map(file => encodeURIComponent(file.trim())).join(',');
                var zipLink = '/app/assignments/zip_files.php?files=' + files + '&stu_unique_id=' + encodeURIComponent(row.stu_unique_id) + '&student_name=' + encodeURIComponent(row.student_name) + '&taskname=' + encodeURIComponent(row.taskname);
                fileLinks += '<a href="' + zipLink + '" class="btn btn-danger btn-sm" download>Download Assignments</a> ';
              }
            }
            return fileLinks;
          }
        },
        {
          data: 'idd',
          render: function(data, type, full, meta) {
            if (full.assignment_status && full.assignment_status === 'CREATED') {
              if (!(full.assignment_status === 'CREATED' && full.uploaded_type === 'Manual' || full.uploaded_type === 'Online')) {
                var buttonHtml = '<button class="btn btn-primary btn-sm" onclick="OpenSolution(\'' + full.student_id + '\', \'' + full.subject_id + '\', \'' + full.assignment_id + '\')">Manual Upload</button>';
                return buttonHtml;
              }
            }
            return '';
          }
        },
        {
          data: "id",
          render: function(data, type, row) {
            var buttonHtml = '<div class="button-list text-end">';
            if (row.assignment_status == 'CREATED' || row.assignment_status == 'NOT CREATED') {
              if (row.uploaded_type == 'Manual' || row.uploaded_type == 'Online') {
                if (
                  row.eva_status === "Rejected" ||
                  row.eva_status === "Approved" ||
                  row.eva_status === "Submitted" ||
                  row.eva_status === "Not Submitted"
                ) {
                  var sub_id = row.subject_id;
                  // print_r(sub_id);
                  buttonHtml += '<i class="btn btn-warning btn-sm" onclick="openEditModal(\'' + data + '\',\'' + sub_id + '\')">Edit Result</i>';
                } else {
                  var stu_id = row.subject_id;
                  buttonHtml += '<i class="btn btn-primary btn-sm" onclick="openModal(\'' + data + '\',\'' + stu_id + '\')">Set Result</i>';
                }
              }
            }
            buttonHtml += '</div>';
            return buttonHtml;
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
      "iDisplayLength":25
    };
    let res = table.dataTable(settings);
    $('#subject-search-table').keyup(function() {
      table.fnFilter($(this).val());
    });
  </script>
  <script type="text/javascript">
    function openModal(id, stu_id) {
      $.ajax({
        url: '/app/assignments/setresult',
        type: 'GET',
        data: {
          assignment_id: id,
          stu_id: stu_id
        },
        success: function(data) {
          console.log(data);
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      });
    }

    function openEditModal(id, sub_id) {
      $.ajax({
        url: '/app/assignments/assignment-existing-result',
        type: 'POST',
        data: {
          assignment_id: id,
          sub_id: sub_id
        },
        success: function(response) {
          console.log(response);
          $('#md-modal-content').html(response);
          $('#mdmodal').modal('show');
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', error);
        }
      });
    }
  </script>
  <script type="text/javascript">
    function OpenSolution(id, subjectId, assignmentId) {
      $.ajax({
        url: '/app/assignments/manual_upload',
        type: 'GET',
        data: {
          id,
          subjectId,
          assignmentId
        },
        success: function(data) {
          console.log(data);
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      });
    }
  </script>
  <script>
    if ($("#users").length > 0) {
      $("#users").select2({
        placeholder: 'Choose Center'
      })
    }
    $("#sub_courses").select2({
      placeholder: 'Choose Department'
    })
    $("#vertical_type").select2({
      placeholder: "Choose Vertical"
    })
    $("#center_sub_center").select2({
      placeholder: 'Choose Sub Center'
    })
    $("#sub_center").select2({
      placeholder: 'Choose Sub Center'
    })
    $("#subjects").select2({
      placeholder: "choose Courses"
    })
    $("#assinment_status").select2({
      placeholder: "assinment_status"
    })

    function addFilter(id, by, role = null) {
      $.ajax({
        url: '/app/assignments/filter',
        type: 'POST',
        data: {
          id,
          by,
          role
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            if ('<?= $_SESSION['Role'] ?>' == 'Administrator') {
              if (by == 'vertical_type') {
                $("#users").html(data.CenterName);
              } else if (by == 'users') {
                $("#sub_center").html(data.subCenterName);
              }
            }
          }
        }
      })
    }

    function addSubCenterFilter(id, by, role = null) {
      $.ajax({
        url: '/app/assignments/filter',
        type: 'POST',
        data: {
          id,
          by,
          role
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        }
      })
    }
  </script>
  <!-- <script>
    function getsubjects(sub_courses) {
      $.ajax({
        url: '/app/assignments/assignment_get_subjects',
        type: 'POST',
        dataType: 'text',
        data: {
          'sub_courses': sub_courses
        },
        success: function(result) {
          $('#subjects').html(result);
        }
      })

    }
  </script> -->
  <script>
    $(document).ready(function() {
      $("#sub_courses").change(function() {
        var courseId = $(this).val();
        if (courseId) {
          $.ajax({
            type: 'POST',
            url: '/app/assignments/assignment_get_subjects',
            data: {
              sub_courses: courseId
            },
            success: function(response) {
              $("#subjects").html(response);
            }
          });
        } else {
          $("#subjects").html('<option value="All">Choose Subjects</option>');
        }
      });
    });
  </script>

  // </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>