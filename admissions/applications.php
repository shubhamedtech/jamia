<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>

<style>
  .tooltip-inner {
    white-space: pre-wrap;
    max-width: 100% !important;
    text-align: left !important;
  }
</style>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php'));
  unset($_SESSION['current_session']);
  unset($_SESSION['current_session']);
  unset($_SESSION['filterByDepartment']);
  unset($_SESSION['filterByUser']);
  unset($_SESSION['filterByDate']);
  unset($_SESSION['filterBySubCourses']);
  unset($_SESSION['filterByCourses']);
  unset($_SESSION['filterByVertical']);
  unset($_SESSION['filterByStatus']); ?>

  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">
        <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        for ($i = 1; $i <= count($breadcrumbs); $i++) {
          if (count($breadcrumbs) == $i) : $active = "active";
            $crumb = explode("?", $breadcrumbs[$i]);
            echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
          endif;
        }
        ?>
        <div>
          <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') { ?>
            <button class="btn btn-link px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload OA, Enrollment AND Roll No." onclick="uploadOAEnrollRoll()"> <i class="fa fa-lg fa-upload"></i></button>
            <button class="btn btn-link px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload Pendency" onclick="uploadMultiplePendency()"> <i class="fa fa-lg fa-exclamation-triangle"></i></button>
          <?php } ?>
          <button class="btn btn-link px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Excel" onclick="exportData()"> <i class="fa fa-lg fa-download"></i></button>
          <button class="btn btn-link px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Documents" onclick="exportSelectedDocument()"> <i class="fa fa-lg fa-arrow-down"></i></button>
          <button class="btn btn-link px-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Student" onclick="window.open('/admissions/application-form');"> <i class="fa fa-lg fa-plus-circle"></i></button>
        </div>
      </div>
    </div>

    <div class="content">
      <?php if (isset($_SESSION['university_id'])) { ?>
        <div class="card card-transparent">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-start">
              <div class="col-md-1">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="sessions" onchange="changeSession(this.value)">
                    <option value="All">All</option>
                    <?php
                    $role_query = "";
                    if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                      $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                      $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                    }
                    $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                    while ($session = mysqli_fetch_assoc($sessions)) { ?>
                      <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>><?= $session['Name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 m-b-10" style="display: none;">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="departments" onchange="addFilter(this.value, 'departments');">
                    <option value="">Choose Types</option>
                    <?php //$departments = $conn->query("SELECT ID, Name FROM Course_Types WHERE University_ID = " . $_SESSION['university_id']);
                    $departments = $conn->query("SELECT ID, Name FROM Departments WHERE University_ID = " . $_SESSION['university_id']);

                    while ($department = $departments->fetch_assoc()) {
                      echo '<option value="' . $department['ID'] . '">' . $department['Name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="sub_courses" onchange="addFilter(this.value, 'courses')" data-placeholder="Choose Program">
                    <option value="">Choose Program</option>
                    <?php $programs = $conn->query("SELECT Courses.ID, CONCAT(Courses.Name) as Name FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.University_ID =  " . $_SESSION['university_id'] . " $role_query GROUP BY Students.Course_ID;");
                    while ($program = $programs->fetch_assoc()) {
                      echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 m-b-10">
                <div class="input-daterange input-group" id="datepicker-range">
                  <input type="text" class="form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                  <div class="input-group-addon">to</div>
                  <input type="text" class="form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                </div>
              </div>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="application_status" onchange="addFilter(this.value, 'application_status')" data-placeholder="Choose App. Status">
                    <option value="">Application Status</option>
                    <option value="1">Document Verified</option>
                    <option value="2">Payment Verified</option>
                    <option value="3">Both Verified</option>
                  </select>
                </div>
              </div>
              <div class="col-md-1 m-b-10">
                <div class="form-group">
                  <select class="form-control" data-init-plugin="select2" id="verticals" onchange="addCenterFilter(this.value, 'verticals')" data-placeholder="Verticals">
                    <option value="">All</option>
                    <option value="1">Edtech Innovate</option>
                    <option value="2">IITS</option>
                    <option value="3">International</option>
                  </select>
                </div>
              </div>
              <?php if ($_SESSION['Role'] == 'Administrator') { ?>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="form-control" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users','center')" data-placeholder="Choose User">
                    </select>
                  </div>
                </div>
                <!-- </div>
            <div class="d-flex justify-content-start"> -->
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="form-control sub_center" data-init-plugin="select2" id="sub_center" onchange="addSubCenterFilter(this.value, 'users','subcenter')" data-placeholder="Choose Sub Center">
                    </select>
                  </div>
                </div>
            </div>
          <?php } ?>
          <?php if ($_SESSION['CanCreateSubCenter'] == "1" && $_SESSION['Role'] == "Center") { ?>
            <div class="col-md-2 m-b-10">
              <div class="form-group">
                <select class="form-control sub_center" data-init-plugin="select2" id="center_sub_center" onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">
                  <?php $sub_center_query = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Center_SubCenter LEFT JOIN Users ON Users.ID = Center_SubCenter.Sub_Center  WHERE Center_SubCenter.Center='" . $_SESSION['ID'] . "' AND Users.Role='Sub-Center'");
                  while ($subCenterArr = $sub_center_query->fetch_assoc()) { ?>
                    <option value="">Choose Sub Center</option>
                    <option value="<?= $subCenterArr['ID'] ?>"><?= $subCenterArr['Name'] . "(" . $subCenterArr['Code'] . ")"  ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          <?php } ?>

          <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="card tab-style1">
              <!-- Nav tabs -->
              <ul class="nav nav-tabs">
                <li class="nav-item">
                  <a class="nav-link active" data-toggle="tab" data-target="#applications" href="#"><span>All Applications - <span id="application_count">0</span></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" data-target="#not_processed" href="#"><span>Not Processed - <span id="not_processed_count">0</span></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" data-target="#ready_for_verification" href="#"><span>Ready for Verification - <span id="ready_for_verification_count">0</span></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" data-target="#verified" href="#"><span>Verified - <span id="verified_count">0</span></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" data-target="#proccessed_to_university" href="#"><span>Processed to Board - <span id="processed_to_university_count">0</span></span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-toggle="tab" data-target="#enrolled" href="#"><span>Enrolled - <span id="enrolled_count">0</span></span></a>
                </li>
              </ul>
              <!-- Tab panes -->
              <div class="tab-content">
                <div class="tab-pane active" id="applications">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2 d-flex justify-content-start">
                      <input type="text" id="application-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="application-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Enrollment No.</th>
                          <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'OA Number') ?></th>
                          <th>Form Status</th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center(Sub-Center)Details</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane" id="not_processed">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2">
                      <input type="text" id="not-processed-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="not-processed-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center Details</th>
                          <th>Form Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane" id="ready_for_verification">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2">
                      <input type="text" id="ready-for-verification-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="ready-for-verification-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Enrollment No.</th>
                          <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'OA Number') ?></th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center Details</th>
                          <th>Form Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane" id="verified">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2">
                      <input type="text" id="verified-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="verified-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Enrollment No.</th>
                          <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'OA Number') ?></th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center Details</th>
                          <th>Form Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane" id="proccessed_to_university">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2">
                      <input type="text" id="proccessed-to-university-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="proccessed-to-university-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Enrollment No.</th>
                          <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'OA Number') ?></th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center Details</th>
                          <th>Form Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="tab-pane" id="enrolled">
                  <div class="row d-flex justify-content-end">
                    <div class="col-md-2">
                      <input type="text" id="enrolled-search-table" class="form-control pull-right" placeholder="Search">
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover nowrap" id="enrolled-table">
                      <thead>
                        <tr>
                          <th data-orderable="false">Actions</th>
                          <th>Photo</th>
                          <th>Student</th>
                          <th>Status</th>
                          <th>Enrollment No.</th>
                          <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'OA Number') ?></th>
                          <th>Admission Details</th>
                          <th>Pendency</th>
                          <th>Student Name</th>
                          <th>Permissions</th>
                          <th>Center Details</th>
                          <th>Form Status</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>

  <div class="modal fade slide-up" id="reportmodal" style="z-index:9999" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
    <div class="modal-dialog modal-md">
      <div class="modal-content-wrapper">
        <div class="modal-content" id="report-modal-content">
        </div>
      </div>
    </div>
  </div>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
  <script>
    $('#datepicker-range').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      endDate: '0d'
    });
  </script>

  <?php if ($_SESSION['Role'] == 'Administrator' && !isset($_SESSION['university_id'])) { ?>
    <script type="text/javascript">
      changeUniversity();
    </script>
  <?php } ?>

  <script type="text/javascript">
    $(function() {
      var role = '<?php echo $_SESSION['Role']; ?>';
      var notProcessedTable = $('#not-processed-table');
      var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
      var is_accountant = ['Accountant', 'Administrator'].includes(role) ? true : false;
      var is_university_head = ['University Head', 'Administrator'].includes(role) ? true : false;
      var is_operations = role == 'Operations' ? true : false;
      var hasStudentLogin = '<?php echo $_SESSION['has_lms'] == 1 ? true : false; ?>';
      var applicationTable = $('#application-table');
      var notProcessedTable = $('#not-processed-table');
      var readyForVerificationTable = $('#ready-for-verification-table');
      var verifiedTable = $('#verified-table');
      var processedToUniversityTable = $('#proccessed-to-university-table');
      var enrolledTable = $('#enrolled-table');

      var applicationSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/application-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              //var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var edit = '';
              if (role == 'Administrator') {
                edit = '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>';
              } else {
                edit = (row.Process_By_Center == '1' && (showInhouse || row.Step < 4)) ?
                  '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              }
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              } else {
                var renderMsg = statusMsg;
                var pbcMsg = '';
              }
              // DOC VERFICATION
              if (row.Pendency_Status == 2) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger"><strong>In Review</strong></span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="text-danger cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Re-Review</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else if (row.Pendency != 0) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger fw-bold" onclick="uploadPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="cursor-pointer fw-bold text-danger" onclick="reportPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pendency<span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else {
                if (row.Document_Verified == 1) {
                  var docMsg = (is_operations || is_university_head) && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="cursor-pointer text-primary fw-bold" onclick="verifyDocument(&#39;' + row.ID + '&#39;)">Review</span></p>' : row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pending</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verified On:<span class="text-success fw-bold">' + row.Document_Verified + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              }
              // Payment Recieved
              if (row.Payment_Received == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                var paymentMsg = is_accountant ? '<p class="mb-0">Payment Status: <span class="cursor-pointer fw-bold text-primary" onclick="verifyPayment(&#39;' + row.ID + '&#39;)">Review</span></p>' : '<center><span class="label label-primary">Pending</span></center>';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else if (row.Process_By_Center != 1) {
                var paymentMsg = row.Step == 4 ? '<p class="mb-0">Payment Verified On: <span class="text-success fw-bold">' + row.Payment_Received + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else {
                var paymentMsg = "";
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              }

              // Processed to University
              if (row.Processed_To_University == 1) {
                var uniMsg = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed by University</label>\
                </div>' : "";
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              } else {
                var uniMsg = data == 4 ? '<p class="mb-0">Processed to University on: <span class="text-success fw-bold">' + row.Processed_To_University + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              }
              return renderMsg;
            }
          },
          {
            data: "Enrollment_No",
            "render": function(data, type, row) {
              var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "OA_Number",
            "render": function(data, type, row) {
              var edit = showInhouse ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add OA Number" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          },
          {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var Sub_Center_Name = row.Sub_Center_Name.length > 0 ? '(' + row.Sub_Center_Name + ')' : '';
              var rm = row.RM;
              return '<p class="mb-0">' + name + Sub_Center_Name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var notProcessedSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/not-processed-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#not_processed_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              } else {
                renderMsg = '<p></p>';
              }
              return renderMsg;
            }
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              // console.log(row);
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          },
          {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var rm = row.RM;
              return '<p class="mb-0">Center Name: ' + name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          }

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var readyForVerificationSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/ready-for-verification-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#ready_for_verification_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="process-by-center-' + row.ID + '" onclick="processByCenter(&#39;' + row.ID + '&#39;)">\
                <label for="process-by-center-' + row.ID + '">Mark as Processed by Center</label>\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              }
              // DOC VERFICATION
              if (row.Pendency_Status == 2) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger"><strong>In Review</strong></span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="text-danger cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Re-Review</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else if (row.Pendency != 0) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger fw-bold" onclick="uploadPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="cursor-pointer fw-bold text-danger" onclick="reportPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pendency<span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else {
                if (row.Document_Verified == 1) {
                  var docMsg = (is_operations || is_university_head) && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="cursor-pointer text-primary fw-bold" onclick="verifyDocument(&#39;' + row.ID + '&#39;)">Review</span></p>' : row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pending</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verified On:<span class="text-success fw-bold">' + row.Document_Verified + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              }
              // Payment Recieved
              if (row.Payment_Received == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                var paymentMsg = is_accountant ? '<p class="mb-0">Payment Status: <span class="cursor-pointer fw-bold text-primary" onclick="verifyPayment(&#39;' + row.ID + '&#39;)">Review</span></p>' : '<center><span class="label label-primary">Pending</span></center>';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else if (row.Process_By_Center != 1) {
                var paymentMsg = row.Step == 4 ? '<p class="mb-0">Payment Verified On: <span class="text-success fw-bold">' + row.Payment_Received + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else {
                var paymentMsg = "";
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              }

              // Processed to University
              if (row.Processed_To_University == 1) {
                var uniMsg = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed by University</label>\
                </div>' : "";
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              } else {
                var uniMsg = data == 4 ? '<p class="mb-0">Processed to University on: <span class="text-success fw-bold">' + row.Processed_To_University + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              }
              return renderMsg;
            }
          },
          {
            data: "Enrollment_No",
            "render": function(data, type, row) {
              var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "OA_Number",
            "render": function(data, type, row) {
              var edit = showInhouse ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add OA Number" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          },
          {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var rm = row.RM;
              return '<p class="mb-0">Center Name: ' + name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          }

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var verifiedSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/verified-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#verified_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="process-by-center-' + row.ID + '" onclick="processByCenter(&#39;' + row.ID + '&#39;)">\
                <label for="process-by-center-' + row.ID + '">Mark as Processed by Center</label>\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              }
              // DOC VERFICATION
              if (row.Pendency_Status == 2) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger"><strong>In Review</strong></span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="text-danger cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Re-Review</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else if (row.Pendency != 0) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger fw-bold" onclick="uploadPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="cursor-pointer fw-bold text-danger" onclick="reportPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pendency<span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else {
                if (row.Document_Verified == 1) {
                  var docMsg = (is_operations || is_university_head) && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="cursor-pointer text-primary fw-bold" onclick="verifyDocument(&#39;' + row.ID + '&#39;)">Review</span></p>' : row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pending</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verified On:<span class="text-success fw-bold">' + row.Document_Verified + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              }
              // Payment Recieved
              if (row.Payment_Received == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                var paymentMsg = is_accountant ? '<p class="mb-0">Payment Status: <span class="cursor-pointer fw-bold text-primary" onclick="verifyPayment(&#39;' + row.ID + '&#39;)">Review</span></p>' : '<center><span class="label label-primary">Pending</span></center>';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else if (row.Process_By_Center != 1) {
                var paymentMsg = row.Step == 4 ? '<p class="mb-0">Payment Verified On: <span class="text-success fw-bold">' + row.Payment_Received + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else {
                var paymentMsg = "";
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              }

              // Processed to University
              if (row.Processed_To_University == 1) {
                var uniMsg = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed by University</label>\
                </div>' : "";
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              } else {
                var uniMsg = data == 4 ? '<p class="mb-0">Processed to University on: <span class="text-success fw-bold">' + row.Processed_To_University + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              }
              return renderMsg;
            }
          },
          {
            data: "Enrollment_No",
            "render": function(data, type, row) {
              var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "OA_Number",
            "render": function(data, type, row) {
              var edit = showInhouse ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add OA Number" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          }, {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var rm = row.RM;
              return '<p class="mb-0">Center Name: ' + name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          }

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var processedToUniversitySettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/processed-to-university-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#processed_to_university_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="process-by-center-' + row.ID + '" onclick="processByCenter(&#39;' + row.ID + '&#39;)">\
                <label for="process-by-center-' + row.ID + '">Mark as Processed by Center</label>\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              }
              // DOC VERFICATION
              if (row.Pendency_Status == 2) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger"><strong>In Review</strong></span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="text-danger cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Re-Review</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else if (row.Pendency != 0) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger fw-bold" onclick="uploadPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="cursor-pointer fw-bold text-danger" onclick="reportPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pendency<span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else {
                if (row.Document_Verified == 1) {
                  var docMsg = (is_operations || is_university_head) && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="cursor-pointer text-primary fw-bold" onclick="verifyDocument(&#39;' + row.ID + '&#39;)">Review</span></p>' : row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pending</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verified On:<span class="text-success fw-bold">' + row.Document_Verified + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              }
              // Payment Recieved
              if (row.Payment_Received == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                var paymentMsg = is_accountant ? '<p class="mb-0">Payment Status: <span class="cursor-pointer fw-bold text-primary" onclick="verifyPayment(&#39;' + row.ID + '&#39;)">Review</span></p>' : '<center><span class="label label-primary">Pending</span></center>';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else if (row.Process_By_Center != 1) {
                var paymentMsg = row.Step == 4 ? '<p class="mb-0">Payment Verified On: <span class="text-success fw-bold">' + row.Payment_Received + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else {
                var paymentMsg = "";
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              }

              // Processed to University
              if (row.Processed_To_University == 1) {
                var uniMsg = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed by University</label>\
                </div>' : "";
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              } else {
                var uniMsg = data == 4 ? '<p class="mb-0">Processed to University on: <span class="text-success fw-bold">' + row.Processed_To_University + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              }
              return renderMsg;
            }
          },
          {
            data: "Enrollment_No",
            "render": function(data, type, row) {
              var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "OA_Number",
            "render": function(data, type, row) {
              var edit = showInhouse ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add OA Number" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          }, {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var rm = row.RM;
              return '<p class="mb-0">Center Name: ' + name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          }

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": []
      };

      var enrolledSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/applications/enrolled-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#enrolled_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "ID",
            "render": function(data, type, row) {
              var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="fa fa-edit mr-1 text-warning" title="Edit Application Form"></i></a>' : '';
              var deleted = showInhouse ? '<i class="fa fa-trash mr-1 cursor-pointer text-danger" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
              var print = row.Step == 4 ? '<i class="fa fa-print text-success mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
              var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
              var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
              var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
              var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
              var info = row.Step == 4 ? '<i class="fa fa-info-circle cursor-pointer text-info" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
              return print + edit + deleted + info;
            }
          },
          {
            data: "Photo",
            "render": function(data, type, row) {
              return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32" class="rounded-circle">\
            </span>';
            }
          },
          {
            data: "Unique_ID",
            "render": function(data, type, row) {
              var dob = row.DOB;
              var contact = row.Contact;
              var father = row.Father_Name;
              return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>\
              <p class="mb-0">DOB: ' + dob + ' </p>\
              <p class="mb-0">Contact: ' + contact + ' </p>\
              <p class="mb-0">Father\'s Name: ' + father + ' </p>\
              ';
            }
          },
          {
            data: "Step",
            "render": function(data, type, row) {
              var pbc = row.Process_By_Center;
              var label_class = data < 4 ? 'text-info' : 'text-success';
              var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
              if (data < 4) {
                return '<p class="mb-0">Status: <span class="fw-bold text-warning"> In Draft @ Step ' + data + '</span></p>';
              } else {
                var statusMsg = '<p class="mb-0">Status: <span class="fw-bold text-success">Completed</span></p>';
              }
              // PBC
              if (role != 'Sub-Center') {
                if (data == 4 && pbc == 1) {
                  var pbcMsg = !showInhouse ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="process-by-center-' + row.ID + '" onclick="processByCenter(&#39;' + row.ID + '&#39;)">\
                <label for="process-by-center-' + row.ID + '">Mark as Processed by Center</label>\
                </div>' : '<p class="mb-0">Processed on: <span class="fw-bold text-danger">Not Processed</span></p>';
                  var renderMsg = statusMsg + pbcMsg;
                } else {
                  var pbcMsg = data == 4 ? '<p class="mb-0">Processed by Center on: <span class="fw-bold text-success">' + pbc + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg;
                }
              }
              // DOC VERFICATION
              if (row.Pendency_Status == 2) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger"><strong>In Review</strong></span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="text-danger cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Re-Review</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else if (row.Pendency != 0) {
                if (!showInhouse) {
                  var docMsg = '<p class="mb-0">Document Verification: <span class="cursor-pointer text-danger fw-bold" onclick="uploadPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = is_operations || is_university_head ? '<p class="mb-0">Document Verification: <span class="cursor-pointer fw-bold text-danger" onclick="reportPendency(&#39;' + row.ID + '&#39;)">Pendency</span></p>' : '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pendency<span></p>';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              } else {
                if (row.Document_Verified == 1) {
                  var docMsg = (is_operations || is_university_head) && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="cursor-pointer text-primary fw-bold" onclick="verifyDocument(&#39;' + row.ID + '&#39;)">Review</span></p>' : row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verification: <span class="text-danger fw-bold">Pending</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                } else {
                  var docMsg = row.Step == 4 && row.Process_By_Center != 1 ? '<p class="mb-0">Document Verified On:<span class="text-success fw-bold">' + row.Document_Verified + '</span></p>' : '';
                  var renderMsg = statusMsg + pbcMsg + docMsg;
                }
              }
              // Payment Recieved
              if (row.Payment_Received == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                var paymentMsg = is_accountant ? '<p class="mb-0">Payment Status: <span class="cursor-pointer fw-bold text-primary" onclick="verifyPayment(&#39;' + row.ID + '&#39;)">Review</span></p>' : '<center><span class="label label-primary">Pending</span></center>';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else if (row.Process_By_Center != 1) {
                var paymentMsg = row.Step == 4 ? '<p class="mb-0">Payment Verified On: <span class="text-success fw-bold">' + row.Payment_Received + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              } else {
                var paymentMsg = "";
                var renderMsg = statusMsg + pbcMsg + docMsg + paymentMsg;
              }

              // Processed to University
              if (row.Processed_To_University == 1) {
                var uniMsg = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed by University</label>\
                </div>' : "";
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              } else {
                var uniMsg = data == 4 ? '<p class="mb-0">Processed to University on: <span class="text-success fw-bold">' + row.Processed_To_University + '</span></p>' : '';
                var renderMsg = statusMsg + pbcMsg + docMsg + uniMsg;
              }
              return renderMsg;
            }
          },
          {
            data: "Enrollment_No",
            "render": function(data, type, row) {
              var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "OA_Number",
            "render": function(data, type, row) {
              var edit = showInhouse ? '<i class="fa fa-edit ml-2 cursor-pointer" title="Add OA Number" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
              return data + edit;
            }
          },
          {
            data: "Adm_Session",
            "render": function(data, type, row) {
              var type = row.Adm_Type;
              var prog = row.Short_Name;
              var duration = row.Duration;

              return '<p class="mb-0">Session: ' + data + '</p>\
              <p class="mb-0">Type: ' + type + '</p>\
              <p class="mb-0">Program: ' + prog + '</p>\
              ';
            }
          },
          {
            data: "Adm_Type",
            "render": function(data, type, row) {
              return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
            },
            visible: false,
          },
          {
            data: "First_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            },
            visible: false,
          }, {
            data: "Status",
            "render": function(data, type, row) {
              var switches = "";
              // Login
              var loginActive = data == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse) {
                var loginChecked = data == 1 ? 'checked' : '';
                var loginSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="Login" data-off-text="Login"  type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + loginChecked + '></div>';
                switches = loginSwitch
              } else {
                var loginSwitch = '<p class="mb-1">Login: Inactive</p>';
                switches = loginSwitch
              }
              // ID
              var idActive = row.ID_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var idChecked = row.ID_Card == 1 ? 'checked' : '';
                var idSwitch = '<div class="mb-2"><input class="bs_switch" data-on-text="ID Card" data-off-text="ID Card" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="ID_Card" data-row-id="' + row.ID + '" ' + idChecked + '></div>';
                switches += idSwitch;
              } else {
                var idSwitch = '<p class="mb-1">Id: Inactive</p>';
                switches += idSwitch;
              }
              // Admit Card
              var admitActive = row.Admit_Card == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var admitChecked = row.Admit_Card == 1 ? 'checked' : '';
                var admitSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Admit Card" data-off-text="Admit Card" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Admit_Card" data-row-id="' + row.ID + '" ' + admitChecked + '></div>';
                switches += admitSwitch;

              } else {
                var admitSwitch = '<p class="mb-1">Admit Card: Inactive</p>';
                switches += admitSwitch;

              }
              // Exam
              var examActive = row.Exam == 1 ? 'Active' : 'Inactive';
              if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                var examChecked = row.Exam == 1 ? 'checked' : '';
                var examSwitch = '<div class="mb-2"><input class="bs_switch" type="checkbox" data-on-text="Exam" data-off-text="Exam" data-size="small" data-on-color="success" data-off-color="danger" data-col-name="Exam" data-row-id="' + row.ID + '" ' + examChecked + '></div>';
                switches += examSwitch;

              } else {
                var examSwitch = '<p class="mb-1">Exam: Inactive</p>';
                switches += examSwitch;

              }
              return switches
            },
            visible: hasStudentLogin
          },
          {
            data: "Center_Code",
            "render": function(data, type, row) {
              var name = row.Center_Name;
              var prog = row.Short_Name;
              var rm = row.RM;
              return '<p class="mb-0">Center Name: ' + name + '</p>\
              <p class="mb-0">Center Code: ' + data + '</p>\
              <p class="mb-0">RM: ' + rm + '</p>\
              ';
            },
            visible: role == 'Sub-Center' ? false : true
          },
          {
            data: "form_status",
            "render": function(data, type, row) {
              var abc = '<i class="fa fa-edit ml-2 cursor-pointer" title="Add Form Status" onclick="addFormStatus(\'' + row.ID + '\')"></i>';
              var retuenValue = role == 'Administrator' ? data + abc : data;
              return retuenValue;
            },
            visible: (role != 'Sub-Center' && role != 'Center')
          }

        ],
        "sDom": "l<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        drawCallback: function(settings, json) {
          $('[data-toggle="tooltip"]').tooltip();
        },
        "aaSorting": [],
        "initComplete": function() {
          $('.bs_switch').bootstrapSwitch();
        },
      };

      applicationTable.dataTable(applicationSettings);
      notProcessedTable.dataTable(notProcessedSettings);
      readyForVerificationTable.dataTable(readyForVerificationSettings);
      verifiedTable.dataTable(verifiedSettings);
      processedToUniversityTable.dataTable(processedToUniversitySettings);
      enrolledTable.dataTable(enrolledSettings);

      // search box for table
      $('#application-search-table').keyup(function() {
        applicationTable.fnFilter($(this).val());
      });


      $('#not-processed-search-table').keyup(function() {
        notProcessedTable.fnFilter($(this).val());
      });

      $('#ready-for-verification-search-table').keyup(function() {
        readyForVerificationTable.fnFilter($(this).val());
      });

      $('#document-verified-search-table').keyup(function() {
        verifiedTable.fnFilter($(this).val());
      });

      $('#processed-to-university-search-table').keyup(function() {
        processedToUniversityTable.fnFilter($(this).val());
      });

      $('#enrolled-search-table').keyup(function() {
        enrolledTable.fnFilter($(this).val());
      });

      applicationTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
      notProcessedTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
      readyForVerificationTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
      verifiedTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
      processedToUniversityTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
      enrolledTable.on('draw.dt', function() {
        $('.bs_switch', this).bootstrapSwitch();
        $('.bs_switch', this).on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          var colName = $(this).data('col-name');
          changeStatus('Students', rowId, colName);
        });
      });
    })
  </script>

  <script type="text/javascript">
    function changeSession(value) {
      $('input[type=search]').val('');
      updateSession();
    }

    function updateSession() {
      var session_id = $('#sessions').val();
      $.ajax({
        url: '/app/applications/change-session',
        data: {
          session_id: session_id
        },
        type: 'POST',
        success: function(data) {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }
  </script>

  <script type="text/javascript">
    function addEnrollment(id) {
      $.ajax({
        url: '/app/applications/enrollment/create?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    function addOANumber(id) {
      $.ajax({
        url: '/app/applications/oa-number/create?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>

  <script type="text/javascript">
    function exportData() {

      var search = $('#application-search-table').val();
      var steps_found = $('.nav-tabs').find('li a.active').attr('data-target');
      steps_found = steps_found.substring(1, steps_found.length);
      var url = search.length > 0 ? "?steps_found=" + steps_found + "&search=" + search : "?steps_found=" + steps_found;
      console.log(url, "url");
      //var url = search.length > 0 ? "?search=" + search : "";
      //window.open('/app/applications/export' + url);

      window.open('/app/applications/export' + url);
    }

    function exportDocuments(id) {
      $.ajax({
        url: '/app/applications/document?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    function exportZip(id) {
      window.open('/app/applications/zip?id=' + id);
    }

    function exportPdf(id) {
      window.open('/app/applications/pdf?id=' + id);
    }

    function exportSelectedDocument() {
      var search = $('#application-search-table').val();
      var searchQuery = search.length > 0 ? "?search=" + search : "";
      $.ajax({
        url: '/app/applications/documents/create' + searchQuery,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>

  <script type="text/javascript">
    function uploadOAEnrollRoll() {
      $.ajax({
        url: '/app/applications/uploads/create_oa_enroll_roll',
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }
  </script>

  <script type="text/javascript">
    function printForm(id) {
      window.open('/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id, '_blank');
      // window.location.href = '/forms/<?= $_SESSION['university_id'] ?>/index.php?student_id=' + id;
    }
  </script>

  <script type="text/javascript">
    function processByCenter(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Process'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/app/applications/process-by-center",
            type: 'POST',
            dataType: 'json',
            data: {
              id: id
            },
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              }
            }
          });
        } else {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }

    function processedToUniversity(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Process.'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "/app/applications/processed-to-university",
            type: 'POST',
            dataType: 'json',
            data: {
              id: id
            },
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
                $('.table').DataTable().ajax.reload(null, false);
              }
            }
          });
        } else {
          $('.table').DataTable().ajax.reload(null, false);
        }
      })
    }

    function verifyPayment(id) {
      $.ajax({
        url: '/app/applications/review-payment?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#lg-modal-content").html(data);
          $("#lgmodal").modal('show');
        }
      })
    }

    function verifyDocument(id) {
      $.ajax({
        url: '/app/applications/review-documents?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#full-modal-content').html(data);
          $('#fullmodal').modal('show');
        }
      })
    }

    function reportPendency(id) {
      $.ajax({
        url: '/app/pendencies/create?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#report-modal-content').html(data);
          $('#reportmodal').modal('show');
        }
      })
    }

    function uploadPendency(id) {
      $(".modal").modal('hide');
      $.ajax({
        url: '/app/pendencies/edit?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#lg-modal-content").html(data);
          $("#lgmodal").modal('show');
        }
      })
    }

    function uploadMultiplePendency() {
      $(".modal").modal('hide');
      $.ajax({
        url: '/app/pendencies/upload',
        type: 'GET',
        success: function(data) {
          $("#lg-modal-content").html(data);
          $("#lgmodal").modal('show');
        }
      })
    }
  </script>
  <script>
    $(document).ready(function() {
      var center_id = '<?= $_SESSION['ID'] ?>';
      var role = '<?= $_SESSION['Role'] ?>';
    })
  </script>
  <script>
    $("#users").select2({
      placeholder: 'Choose Center'
    })

    $("#verticals").select2({
      placeholder: "Choose verticals"
    })

    $("#departments").select2({
      placeholder: 'Choose Department'
    })

    $("#sessions").select2({
      placeholder: 'Choose Department'
    })

    $("#sub_courses").select2({
      placeholder: 'Choose Department'
    })
    $("#application_status").select2({
      placeholder: 'Choose Department'
    })
    $("#center_sub_center").select2({
      placeholder: 'Choose Sub Center'
    })
    $("#sub_center").select2({
      placeholder: 'Choose Sub Center'
    })


    function addFilter(id, by, role = null) {
      console.log(id, 'id');
      console.log(by, 'by');
      console.log(role, 'role');
      $.ajax({
        url: '/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by,
          role
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            if ('<?= $_SESSION['Role'] ?>' === 'Administrator') {
              $(".sub_center").html(data.subCenterName);
            }
            //$(".sub_center").html(data.subCenterName);
          }
        }
      })
    }

    function addCenterFilter(id, by, role = null) {
      console.log(id, 'id');
      console.log(by, 'by');
      console.log(role, 'role');
      $.ajax({
        url: '/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by,
          role
        },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            if ('<?= $_SESSION['Role'] ?>' === 'Administrator') {
              $("#users").html(data.centerName);
              $("#sub_center").html('');
            }
          }
        }
      })
    }

    function addSubCenterFilter(id, by, role = null) {
      $.ajax({
        url: '/app/applications/filter',
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

    function addDateFilter() {
      var startDate = $("#startDateFilter").val();
      var endDate = $("#endDateFilter").val();
      if (startDate.length == 0 || endDate == 0) {
        return
      }
      var id = 0;
      var by = 'date';
      $.ajax({
        url: '/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by,
          startDate,
          endDate
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        }
      })
    }

    function getCourses(id) {
      $.ajax({
        url: '/app/courses/department-courses',
        type: 'POST',
        data: {
          id
        },
        success: function(data) {
          $("#sub_courses").html(data);
        }
      })
    }
  </script>
  <script>
    function addFormStatus(id) {
      $.ajax({
        url: '/app/applications/formstatus/create?id=' + id,
        type: 'GET',
        success: function(data) {
          console.log(data);
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      });
    }
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>