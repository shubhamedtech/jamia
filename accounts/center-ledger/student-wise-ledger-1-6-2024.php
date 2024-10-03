<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex justify-content-between">
        <h1 class="text-capitalize d-inline fw-bold">Center Ledger</h1>
      </div>
    </div>

    <div class="content">
      <?php if (($_SESSION['Role'] == 'Administrator') || ($_SESSION['Role'] == 'University Head')) { ?>
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group form-group-default required">
                  <label>Centers</label>
                  <select class="form-control" data-init-plugin="select2" id="center" onchange="getLedger()">
                    <option value="">Select</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-group-default required">
                  <label>Sub Centers</label>
                  <select class="form-control sub_center" data-init-plugin="select2" id="sub_center" onchange="getLedger()">
                    <option value="">Select</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group form-group-default required">
                  <label>Adm Session</label>
                  <select class="form-control" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                    <option value="">Select</option>
                    <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = " . $_SESSION['university_id']);
                    while ($session = $sessions->fetch_assoc()) {
                      echo '<option value="' . $session['ID'] . '">' . $session['Name'] . '</option>';
                    }
                    ini_set('display_errors', 1);
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        <?php } else {?>
          <input type="hidden" id="centers_id" value="<?= $_SESSION['ID'] ?>">


          <div class="card">
            <div class="card-header">
              <div class="row d-flex justify-content-center">
             
                <?php if ($_SESSION['CanCreateSubCenter'] == "1" && $_SESSION['Role'] == "Center") { ?>
                  <div class="col-md-4">
                    <div class="form-group form-group-default required">
                      <label>Sub Centers</label>
                      <select class="form-control sub_center" data-init-plugin="select2" id="sub_center" onchange="getLedger()">
                        <?php  $sub_center_query = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Center_SubCenter LEFT JOIN Users ON Users.ID = Center_SubCenter.Sub_Center  WHERE Center_SubCenter.Center='".$_SESSION['ID']."' AND Users.Role='Sub-Center'");
                          while($subCenterArr = $sub_center_query->fetch_assoc()){ ?>
                          <option value="">Choose Sub Center</option>
                          <option value="<?= $subCenterArr['ID'] ?>"><?= $subCenterArr['Name']."(".$subCenterArr['Code'].")"  ?></option>
                        <?php } ?>  
                      </select>
                    </div>
                  </div>
                <?php } ?>


                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Session</label>
                    <select class="form-control" data-init-plugin="select2" id="admission_session_id" onchange="getLedger()">
                      <option value="">Select</option>
                      <?php $sessions = $conn->query("SELECT ID, Name FROM Admission_Sessions WHERE University_ID = " . $_SESSION['university_id']);
                      while ($session = $sessions->fetch_assoc()) {
                        echo '<option value="' . $session['ID'] . '">' . $session['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
          <div class="row m-t-20">
            <div class="col-lg-12">
              <div class="card-body tab-style1">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="all-counter">
                  <li class="nav-item" id="counter_student">
                    <?php
                    $counter = array();
                    if ($_SESSION['ID']) {
                      $center_id = 'AND Students.Added_For = ' . $_SESSION['ID'] . '';
                    } else {
                      $center_id = '';
                    }
                    $students_count_punch = $conn->query("SELECT Students.ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration,Added_For, Course_ID, Sub_Course_ID, Admission_Sessions.Name as Admission_Session FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $center_id AND Step = 4 AND Process_By_Center IS NULL ORDER BY Students.ID DESC");

                    ?>
                    <a class="active nav-link" data-toggle="tab" data-target="#students" href="#"><span>Students</span>-<span id="applied_student_count"><?= $students_count_punch->num_rows ?></span></a>
                  </li>
                  <li class="nav-item" id="counter_pending">
                    <?php
                    $pending_counter = array();
                    $id = $_SESSION['ID'];
                    $added_for[] = $id;

                    $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
                    while ($downline = $downlines->fetch_assoc()) {
                      $added_for[] = $downline['User_ID'];
                    }

                    $users = implode(",", array_filter($added_for));

                    $already = array();
                    $already_ids = array();
                    $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID  WHERE Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2 AND Payments.Type = 1 ");
                    //$invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID AND Payments.Type = 1 WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status != 2");
                    while ($invoice = $invoices->fetch_assoc()) {
                      $already[$invoice['Student_ID']] = $invoice['Duration'];
                      $already_ids[] = $invoice['Student_ID'];
                    }

                    $query = empty($already_ids) ? " AND ID IS NULL" : " AND ID IN (" . implode(',', $already_ids) . ")";

                    $sessionQuery = "";
                    if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
                      $admission_session_id = intval($_GET['admission_session_id']);
                      $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
                    }
                    //  $students_count = $conn->query("SELECT ID FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");
                    // //$students_count = $conn->query("SELECT ID FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 $sessionQuery AND Process_By_Center IS NOT NULL $query");
                    // while ($student = $students_count->fetch_assoc()) {
                    //   $pending_counter[] = $student;
                    // }

                    $students_count = $conn->query("SELECT ID FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");
                    if ($students_count->num_rows == 0) {
                      $students_count = $conn->query("SELECT ID FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_By = $id AND Step = 4 $sessionQuery AND Process_By_Center IS NULL $query");
                    }
                    while ($student = $students_count->fetch_assoc()) {
                      $pending_counter[] = $student;
                    }
                    ?>
                    <a class="nav-link" data-toggle="tab" data-target="#pending" href="#"><span>Pending</span>-<span id="pending_student_count"><?= count($pending_counter) ?></span></a>
                  </li>
                  <li class="nav-item" id="counter_processed">
                    <?php
                    $processed_countrer = array();
                    if ($_SESSION['ID']) {
                      $center_id = 'AND Students.Added_For = ' . $_SESSION['ID'] . '';
                    } else {
                      $center_id = '';
                    }

                    $students_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Step = 4 AND Process_By_Center IS NOT NULL AND Payment_Received IS NULL $center_id");
                    if ($students_count->num_rows == 0) {
                      $students_count = $conn->query("SELECT ID FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Step = 4 AND Process_By_Center IS NOT NULL AND Payment_Received IS NULL AND Added_For = " . $_SESSION['ID'] . " ");
                    }
                    while ($student = $students_count->fetch_assoc()) {
                      $processed_countrer[] = $student;
                    }
                    ?>
                    <a class="nav-link" data-toggle="tab" data-target="#processed" href="#"><span>Processed</span>-<span id="processed_student_count"><?= count($processed_countrer) ?></span></a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="students">
                    <div class="row">
                      <div class="col-md-12 text-center">
                        Please select center!
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="pending">
                    <div class="row">
                      <div class="col-md-12 text-center">
                        Please select center!
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="processed">
                    <div class="row">
                      <div class="col-md-12 text-center">
                        Please select center!
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script>
    $("#center").select2({
      placeholder: 'Choose Center'
    })
      $("#sub_center").select2({
      placeholder: 'Choose Sub Center'
    })
    $("#admission_session_id").select2({
      placeholder: 'Choose Admission Session'
    })

    function getLedger() {
      $("#all-counter").html('');
      var session_role = '<?= $_SESSION['Role'] ?>';
      if(session_role =="Center" || session_role =="Sub-Center"){
        var id = <?= $_SESSION['ID'] ?>;
      }else{
      var id = $("#center").val();
      }
      
      var sub_center_id = $("#sub_center").val();
      if (sub_center_id == undefined) {
        sub_center_id = '';
      }
      var admission_session_id = $("#admission_session_id").val();
      getStudentList(id, admission_session_id, sub_center_id);
      getPendingList(id, admission_session_id, sub_center_id);
      getProcessedList(id, admission_session_id, sub_center_id);
      getCounter(id, 'all-count', admission_session_id, sub_center_id);
      if (sub_center_id == '') {
        addFilter(id, 'users');
      }
    }

    function getStudentList(id, admission_session_id, sub_center_id) {
      $.ajax({
        url: '/app/centers/ledgers/student-wise/students?id=' + id + '&admission_session_id=' + admission_session_id + '&sub_center_id=' + sub_center_id,
        type: 'GET',
        success: function(data) {
          $("#students").html(data);
        }
      })
    }

    function getCounter(id, status, admission_session_id, sub_center_id) {
      $.ajax({
        url: '/app/centers/ledgers/student-wise/students-counter?id=' + id + '&count_status=' + status + '&admission_session_id=' + admission_session_id + '&sub_center_id=' + sub_center_id,
        type: 'GET',
        success: function(data) {
          $("#all-counter").html(data);
        }
      })
    }

    function getPendingList(id, admission_session_id, sub_center_id) {
      $.ajax({
        url: '/app/centers/ledgers/student-wise/pending?id=' + id + '&admission_session_id=' + admission_session_id + '&sub_center_id=' + sub_center_id,
        type: 'GET',
        success: function(data) {
          $("#pending").html(data);
        }
      })
    }

    function getProcessedList(id, admission_session_id, sub_center_id) {
      $.ajax({
        url: '/app/centers/ledgers/student-wise/processed?id=' + id + '&admission_session_id=' + admission_session_id + '&sub_center_id=' + sub_center_id,
        type: 'GET',
        success: function(data) {
          $("#processed").html(data);
        }
      })
    }

    getCenterList('center');

    <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
      echo 'getLedger()';
    } ?>
  </script>
  <script>
    
    function addFilter(id, by) {
      $.ajax({
        url: '/app/applications/filter',
        type: 'POST',
        data: {
          id,
          by
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $(".sub_center").html(data.subCenterName);
          }
        }
      })
    }
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>