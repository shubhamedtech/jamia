<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="/assets/plugins/formwizard/jquery-steps.css">
<link rel="stylesheet" href="/assets/country-select-js-master/build/css/countrySelect.css">
<style type="text/css">
  input {
    text-transform: uppercase;
  }

  .gap-4 {
    gap: 4px;
  }

  .gap-30 {
    gap: 30px;
  }

  .width-100 {
    width: 100%;
  }

  .card-box {
    width: 30%;
  }

  .shadow {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
  }

  .fw-semi-bold {
    font-weight: 500 !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')); ?>
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
      </div>
    </div>
    <?php
    ini_set('display_errors', 1);
    $vartical_id = '';
    $is_get = 0;
    $id = 0;
    $address = [];
    $centerDesabled = "";
    if (isset($_GET['id'])) {
      $centerDesabled = "disabled";
      $id = mysqli_real_escape_string($conn, $_GET['id']);


      $id = base64_decode($id);
      $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));



      $student = $conn->query("SELECT Students.* , Courses.Name AS Grade_Category, Courses.ID AS Grade_Category_id FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.ID = $id");
      if ($student->num_rows > 0) {
        $is_get = 1;
      } else {
        header("Location: /admissions/applications");
      }
      $student = mysqli_fetch_assoc($student);
      $subcenters = $conn->query("SELECT * FROM Center_SubCenter WHERE Sub_Center = '" . $student['Added_For'] . "'");
      if ($subcenters->num_rows > 0) {
        $subcenter = $subcenters->fetch_assoc();
      }
      if (!empty($student['Unique_ID']) && $_SESSION['crm']) {
        $check_in_leads = $conn->query("SELECT Leads.Email, Leads.Mobile FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.Unique_ID = '" . $student['Unique_ID'] . "'");
        if ($check_in_leads->num_rows > 0) {
          $lead = $check_in_leads->fetch_assoc();
          $student['Email'] = $lead['Email'];
          $student['Contact'] = $lead['Mobile'];
        }
      }

      echo '<script>localStorage.setItem("inserted_id",' . $id . ');</script>';
      $address = !empty($student['Address']) ? json_decode($student['Address'], true) : [];

      $vertical = $conn->query("SELECT Vertical FROM Users WHERE ID = '" . $student['Added_For'] . "'");
      $vertical = mysqli_fetch_assoc($vertical);
      $vartical_id =  $vertical['Vertical'];
    }

    if (isset($_GET['lead_id'])) {
      $lead_id = mysqli_real_escape_string($conn, $_GET['lead_id']);
      $lead_id = base64_decode($lead_id);
      $lead_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $lead_id));
      $lead = $conn->query("SELECT Lead_Status.Admission, Lead_Status.University_ID, Lead_Status.User_ID, Lead_Status.Course_ID,Lead_Status.Sub_Course_ID,Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS University,Courses.Name AS Category,Sub_Courses.Name AS Sub_Category,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID= $lead_id");
      if ($lead->num_rows > 0) {
        $is_get = 1;
      } else {
        header("Location: /leads/lists");
      }
      $lead = $lead->fetch_assoc();
    }
    ?>
    <div class="content">
      <div class="card">
        <div class="card-body">
          <div id="appForm">
            <div class="step-app">
              <ul class="step-steps">
                <li><a href="#tab1"><span class="number">1</span> Basic Details</a></li>
                <li><a href="#tab2"><span class="number">2</span> Personal Details</a></li>
                <li><a href="#tab3"><span class="number">3</span> Academics</a></li>
                <li><a href="#tabSubject"><span class="number">4</span> Subjects</a></li>
                <li><a href="#tab4"><span class="number">5</span> Documents</a></li>
                <li><a href="#tab5"><span class="number">6</span> Application Form</a></li>
              </ul>
              <div class="step-content">
                <div class="step-tab-panel" id="tab1">
                  <form name="step1" id="step1" role="form" autocomplete="off" action="/app/application-form/step-1" enctype="multipart/form-data">
                    <h3 class="my-1">Applying For</h3>
                    <div class="row my-1">
                      <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Center <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="center" id="center" onchange="getCourse()" <?php //echo $centerDesabled; 
                                                                                                        ?> required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div>

                      <!-- Admission Session -->
                      <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Admission Session <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="admission_session" id="admission_session" onchange="getAdmissionType(this.value)" required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div>

                      <!-- Admission Type -->
                      <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Admission Type <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="admission_type" id="admission_type" onchange="getCourse()" required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row my-1">
                      <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Course <sup class="text-danger">*</sup></label>
                          <select class="form-control" data-init-plugin="select2" name="course" id="course" onchange="getSubCourse(); getEligibility();" required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div>
                      <!-- <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Toc <sup class="text-danger">*</sup></label>
                          <select class="form-control" data-init-plugin="select2" name="is_toc" id="is_toc" onchange="" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                          </select>
                        </div>
                      </div>

                       Admission Session -->
                      <!--  <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label>Sub Course <sup class="text-danger">*</sup></label>
                          <select class="form-control" data-init-plugin="select2" name="sub_course" id="sub_course" onchange="getDuration(); getEligibility();" required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div> -->

                      <!-- Admission Type -->
                      <!--   <div class="col-md-4">
                        <div class="form-group form-group-default required">
                          <label id="mode">Mode <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="duration" id="duration" required>
                            <option value="">Select</option>
                          </select>
                        </div>
                      </div> -->
                    </div>
                    <h3 class="my-1">Basic Details</h3>
                    <div class="row my-1">
                      <div class="col-md-4">
                        <label for="">Full Name <sup class="text-danger">*</sup></label>
                        <?php $student_name = !empty($id) ? array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) : [] ?>
                        <input type="text" name="full_name" class="form-control" placeholder="ex: Jhon Doe" value="<?= implode(" ", $student_name) ?><?php print !empty($lead_id) ? $lead['Name'] : "" ?>" required>

                      </div>
                      <div class="col-md-4">
                        <label>Father Name <sup class="text-danger">*</sup></label>
                        <input type="text" name="father_name" class="form-control" value="<?php print !empty($id) ? $student['Father_Name'] : "" ?>" placeholder="" required>
                      </div>
                      <div class="col-md-4">
                        <label>Mother Name <sup class="text-danger">*</sup></label>
                        <input type="text" name="mother_name" value="<?php print !empty($id) ? $student['Mother_Name'] : "" ?>" class="form-control" placeholder="" required>
                      </div>

                    </div>
                    <div class="row m-t-2">
                      <div class="col-md-3">
                        <label>DOB <sup class="text-danger">*</sup></label>
                        <input type="tel" name="dob" class="form-control" value="<?php print !empty($id) ? date('d-m-Y', strtotime($student['DOB'])) : "" ?>" placeholder="dd-mm-yyyy" id="dob" required>
                      </div>
                      <div class="col-md-3">
                        <label>Gender <sup class="text-danger">*</sup></label>
                        <select class="form-control" name="gender" required>
                          <option value="">Select</option>
                          <option value="Male" <?php print !empty($id) ? ($student['Gender'] == 'Male' ? 'selected' : '') : '' ?>>Male</option>
                          <option value="Female" <?php print !empty($id) ? ($student['Gender'] == 'Female' ? 'selected' : '') : '' ?>>Female</option>
                          <option value="Other" <?php print !empty($id) ? ($student['Gender'] == 'Other' ? 'selected' : '') : '' ?>>Other</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Category <sup class="text-danger">*</sup></label>
                        <select class="form-control" name="category" required>
                          <option value="">Select</option>
                          <option value="General" <?php print !empty($id) ? ($student['Category'] == 'General' ? 'selected' : '') : '' ?>>General</option>
                          <option value="OBC" <?php print !empty($id) ? ($student['Category'] == 'OBC' ? 'selected' : '') : '' ?>>OBC</option>
                          <option value="SC" <?php print !empty($id) ? ($student['Category'] == 'SC' ? 'selected' : '') : '' ?>>SC</option>
                          <option value="ST" <?php print !empty($id) ? ($student['Category'] == 'ST' ? 'selected' : '') : '' ?>>ST</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Employment Status <sup class="text-danger">*</sup></label>
                        <select class="form-control" name="employment_status" required>
                          <option value="">Select</option>
                          <option value="Govt Employed" <?php print !empty($id) ? ($student['Employement_Status'] == 'Govt Employed' ? 'selected' : '') : '' ?>>Govt Employee</option>
                          <option value="Employed" <?php print !empty($id) ? ($student['Employement_Status'] == 'Employed' ? 'selected' : '') : '' ?>>Non Govt Employee</option>
                          <option value="Unemployed" <?php print !empty($id) ? ($student['Employement_Status'] == 'Unemployed' ? 'selected' : '') : '' ?>>Unemployed</option>
                          <option value="Others" <?php print !empty($id) ? ($student['Employement_Status'] == 'Others' ? 'selected' : '') : '' ?>>Others</option>
                        </select>
                      </div>
                    </div>
                    <div class="row py-2">
                      <div class="col-md-3">
                        <label>Marital Status <sup class="text-danger">*</sup></label>
                        <select class="form-control" name="marital_status" required>
                          <option value="">Select</option>
                          <option value="Married" <?php print !empty($id) ? ($student['Marital_Status'] == 'Married' ? 'selected' : '') : '' ?>>Married</option>
                          <option value="Unmarried" <?php print !empty($id) ? ($student['Marital_Status'] == 'Unmarried' ? 'selected' : '') : '' ?>>Unmarried</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label>Religion <sup class="text-danger">*</sup></label>
                        <select class="form-control" name="religion" required>
                          <option value="">Select</option>
                          <option value="Hindu" <?php print !empty($id) ? ($student['Religion'] == 'Hindu' ? 'selected' : '') : '' ?>>Hindu</option>
                          <option value="Muslim" <?php print !empty($id) ? ($student['Religion'] == 'Muslim' ? 'selected' : '') : '' ?>>Muslim</option>
                          <option value="Sikh" <?php print !empty($id) ? ($student['Religion'] == 'Sikh' ? 'selected' : '') : '' ?>>Sikh</option>
                          <option value="Christian" <?php print !empty($id) ? ($student['Religion'] == 'Christian' ? 'selected' : '') : '' ?>>Christian</option>
                          <option value="Jain" <?php print !empty($id) ? ($student['Religion'] == 'Jain' ? 'selected' : '') : '' ?>>Jain</option>
                        </select>
                      </div>
                      <div class="col-md-3 national">
                        <label>Aadhar <sup class="text-danger">*</sup></label>
                        <input type="tel" maxlength="14" minlength="14" name="aadhar" value="<?php print !empty($id) ? $student['Aadhar_Number'] : '' ?>" class="form-control" id="aadhar" required>
                      </div>
                      <div class="col-md-4 international">
                        <label>Any Id Proof <sup class="text-danger">*</sup></label>
                        <input type="text" name="id_proof" value="<?= isset($student['Id_Proof']) ? $student['Id_Proof'] : '' ?>" class="form-control" id="id_proof" required>
                      </div>
                      <div class="col-md-2">
                        <label>Nationality <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="country" name="nationality" <?= isset($student['Nationality']) ? $student['Nationality'] : ''; ?>>
                        <!-- <select class="form-control" name="nationality" required>
                          <option value="">Select</option>
                          <option value="Indian" <?php print !empty($id) ? ($student['Nationality'] == 'Indian' ? 'selected' : '') : '' ?>>Indian</option>
                          <option value="NRI" <?php print !empty($id) ? ($student['Nationality'] == 'NRI' ? 'selected' : '') : '' ?>>NRI</option>
                        </select> -->
                      </div>
                    </div>
                  </form>
                </div>
                <div class="step-tab-panel" id="tab2">
                  <form name="step2" id="step2" role="form" autocomplete="off" action="/app/application-form/step-2">
                    <div class="row m-t-2">
                      <div class="col-md-6">
                        <h3>Contact Details</h3>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Email <sup class="text-danger">*</sup></label>
                              <input type="email" name="email" class="form-control" value="<?php print !empty($id) ? $student['Email'] : '' ?> <?php print !empty($lead_id) ? $lead['Email'] : '' ?>" placeholder="ex: jhon@example.com">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Alternate Email</label>
                              <input type="email" name="alternate_email" value="<?php print !empty($id) ? $student['Alternate_Email'] : '' ?><?php print !empty($lead_id) ? $lead['Alternate_Email'] : '' ?>" class="form-control" placeholder="ex: jhondoe@example.com">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Mobile <sup class="text-danger">*</sup></label>
                              <input type="tel" id="contact" name="contact" onkeypress="return isNumberKey(event);" maxlength="10" value="<?php print !empty($id) ? $student['Contact'] : '' ?><?php print !empty($lead_id) ? $lead['Mobile'] : '' ?>" class="form-control" placeholder="ex: 9977886655">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Alternate Mobile</label>
                              <input type="tel" name="alternate_contact" class="form-control" maxlength="10" value="<?php print !empty($id) ? $student['Alternate_Contact'] : '' ?><?php print !empty($lead_id) ? $lead['Alternate_Mobile'] : '' ?>" placeholder="ex: 9988776654">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <h3>Contact Details</h3>
                        <div class="row">
                          <div class="col-md-8">
                            <div class="form-group">
                              <label>Address <sup class="text-danger">*</sup></label>
                              <input type="text" name="address" class="form-control" value="<?php print !empty($id) ? (!empty($address) ? $address['present_address'] : '') : '' ?>" placeholder="ex: 23 Street, California">
                            </div>
                          </div>
                          <div class="col-md-4 national">
                            <div class="form-group">
                              <label>Pincode <sup class="text-danger">*</sup></label>
                              <input type="tel" name="pincode" maxlength="6" class="form-control" placeholder="ex: 123456" value="<?php print !empty($address) ? (array_key_exists('present_pincode', $address) ? $address['present_pincode'] : '') : '' ?>" onkeypress="return isNumberKey(event)" onkeyup="getRegion(this.value);">
                            </div>
                          </div>
                          <div class="col-md-4 international">
                            <div class="form-group">
                              <label>Postal Code <sup class="text-danger">*</sup></label>
                              <input type="tel" name="postal_code" class="form-control" placeholder="Postal Code" value="<?= isset($student['Postal_Code']) ? $student['Postal_Code'] : ''; ?>">
                            </div>
                          </div>
                        </div>
                        <div class="row international">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>City <sup class="text-danger">*</sup></label>
                              <input type="text" name="internatiol_city" class="form-control" placeholder="City" value="<?= isset($student['Internatiol_City']) ? $student['Internatiol_City'] : ''; ?>">
                            </div>
                          </div>
                        </div>
                        <div class="row national">
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>City <sup class="text-danger">*</sup></label>
                              <select class="form-control" name="city" id="city">
                              </select>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>District <sup class="text-danger">*</sup></label>
                              <select class="form-control" name="district" id="district">
                              </select>
                            </div>
                          </div>

                          <div class="col-md-4">
                            <div class="form-group">
                              <label>State <sup class="text-danger">*</sup></label>
                              <input type="text" name="state" class="form-control" placeholder="ex: California" id="state" readonly>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="step-tab-panel" id="tab3">
                  <form name="step3" id="step3" role="form" autocomplete="off" action="/app/application-form/step-3" method="POST" enctype="multipart/form-data">
                    <div class="row m-t-2">
                      <?php
                      $high_school = [];
                      if (!empty($id)) {
                        $high_school = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'High School' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'High School' GROUP BY Student_ID");
                        if ($high_school->num_rows > 0) {
                          $high_school = mysqli_fetch_assoc($high_school);
                          $high_marksheet = !empty($high_school['Location']) ? explode('|', $high_school['Location']) : [];
                        } else {
                          $high_school = [];
                        }
                      }
                      ?>

                      <div class="col-md-6 d-none" id="hight_school_acadimics">
                        <h3>High School</h3>
                        <div class="form-group form-group-default high_school">
                          <label>Subjects <sup class="text-danger">*</sup></label>
                          <input type="text" name="high_subject" id="high_subject" class="form-control" value="<?php print !empty($high_school) ? (array_key_exists('Subject', $high_school) ? $high_school['Subject'] : '') : 'All Subjects' ?>" placeholder="ex: All">
                        </div>
                        <div class="form-group form-group-default high_school">
                          <label>Year <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="high_year" id="high_year">
                            <option value="">Select</option>
                            <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                              <option value="<?= $i ?>" <?php print !empty($high_school) ? ($high_school['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group form-group-default high_school">
                          <label>Board/University <sup class="text-danger">*</sup></label>
                          <input type="text" name="high_board" id="high_board" value="<?php print !empty($high_school) ? $high_school['Board/Institute'] : '' ?>" class="form-control" placeholder="ex: CBSE">
                        </div>
                        <?php if ($_SESSION['university_id'] == 0) { ?>
                          <div class="form-group form-group-default">
                            <label>Marks Obtained <sup class="text-danger">*</sup></label>
                            <input type="text" name="high_obtained" id="high_obtained" value="<?php print !empty($high_school) ? $high_school['Marks_Obtained'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 400">
                          </div>
                          <div class="form-group form-group-default">
                            <label>Max Marks <sup class="text-danger">*</sup></label>
                            <input type="text" name="high_max" id="high_max" value="<?php print !empty($high_school) ? $high_school['Max_Marks'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 600">
                          </div>
                          <div class="form-group form-group-default required">
                            <label>Grade/Percentage <sup class="text-danger">*</sup></label>
                            <input type="text" name="high_total" id="high_total" value="<?php print !empty($high_school) ? $high_school['Total_Marks'] : '' ?>" class="form-control" placeholder="ex: 66%">
                          </div>
                        <?php } else { ?>
                          <div class="form-group form-group-default high_school">
                            <label>Result <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="high_total" id="high_total">
                              <option value="">Select</option>
                              <option value="Passed" <?php print !empty($high_school) && $high_school['Total_Marks'] == 'PASSED' ? 'selected' : '' ?>>Passed</option>
                              <option value="Fail" <?php print !empty($high_school) && $high_school['Total_Marks'] == 'FAIL' ? 'selected' : '' ?>>Fail</option>
                              <option value="Discontinued" <?php print !empty($high_school) && $high_school['Total_Marks'] == 'DISCONTINUED' ? 'selected' : '' ?>>Discontinued</option>
                            </select>
                          </div>
                        <?php } ?>
                        <div class="form-group form-group-default high_school">
                          <label>Marksheet <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('high_marksheet');" id="high_marksheet" name="high_marksheet[]" multiple="multiple" class="form-control mt-1">
                          <dt><?php print !empty($high_marksheet) ?  count($high_marksheet) . " Marksheet(s) Uploaded" : ''; ?></dt>
                          <?php if (!empty($high_marksheet)) {
                            foreach ($high_marksheet as $hm) { ?>
                              <img src="<?= $hm ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $hm ?>')" width="40" height="40" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      $intermediate = [];
                      if (!empty($id)) {
                        $intermediate = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Intermediate' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'Intermediate'");
                        if ($intermediate->num_rows > 0) {
                          $intermediate = mysqli_fetch_assoc($intermediate);
                          $inter_marksheet = !empty($intermediate['Location']) ? explode('|', $intermediate['Location']) : [];
                        } else {
                          $intermediate = [];
                        }
                      }
                      ?>
                      <!-- <div class="col-md-6">
                        <h3>Intermediate</h3>
                        <div class="form-group form-group-default intermediate">
                          <label>Subjects <sup class="text-danger">*</sup></label>
                          <input type="text" name="inter_subject" class="form-control" value="<?php print !empty($intermediate) ? (array_key_exists('Subject', $intermediate) ? $intermediate['Subject'] : '') : '' ?>" id="inter_subject" placeholder="ex: PCM">
                        </div>
                        <div class="form-group form-group-default intermediate">
                          <label>Year <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="inter_year" id="inter_year">
                            <option value="">Select</option>
                            <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                              <option value="<?= $i ?>" <?php print !empty($intermediate) ? ($intermediate['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group form-group-default intermediate">
                          <label>Board/University <sup class="text-danger">*</sup></label>
                          <input type="text" name="inter_board" id="inter_board" value="<?php print !empty($intermediate) ? (array_key_exists('Board/Institute', $intermediate) ? $intermediate['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: CBSE">
                        </div>
                        <?php if ($_SESSION['university_id'] == 0) { ?>
                          <div class="form-group form-group-default">
                            <label>Marks Obtained <sup class="text-danger">*</sup></label>
                            <input type="text" name="inter_obtained" id="inter_obtained" class="form-control" onblur="checkInterMarks();" value="<?php print !empty($intermediate) ? (array_key_exists('Marks_Obtained', $intermediate) ? $intermediate['Marks_Obtained'] : '') : '' ?>" placeholder="ex: 400">
                          </div>
                          <div class="form-group form-group-default">
                            <label>Max Marks <sup class="text-danger">*</sup></label>
                            <input type="text" name="inter_max" id="inter_max" class="form-control" value="<?php print !empty($intermediate) ? (array_key_exists('Max_Marks', $intermediate) ? $intermediate['Max_Marks'] : '') : '' ?>" onblur="checkInterMarks();" placeholder="ex: 600">
                          </div>
                          <div class="form-group form-group-default intermediate">
                            <label>Grade/Percentage <sup class="text-danger">*</sup></label>
                            <input type="text" name="inter_total" id="inter_total" value="<?php print !empty($intermediate) ? (array_key_exists('Total_Marks', $intermediate) ? $intermediate['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                          </div>
                        <?php } else { ?>
                          <div class="form-group form-group-default intermediate">
                            <label>Result <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="inter_total" id="inter_total">
                              <option value="">Select</option>
                              <option value="Passed" <?php print !empty($intermediate) && $intermediate['Total_Marks'] == 'PASSED' ? 'selected' : '' ?>>Passed</option>
                              <option value="Fail" <?php print !empty($intermediate) && $intermediate['Total_Marks'] == 'FAIL' ? 'selected' : '' ?>>Fail</option>
                              <option value="Discontinued" <?php print !empty($intermediate) && $intermediate['Total_Marks'] == 'DISCONTINUED' ? 'selected' : '' ?>>Discontinued</option>
                            </select>
                          </div>
                        <?php } ?>
                        <div class="form-group form-group-default intermediate">
                          <label>Marksheet <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('inter_marksheet');" id="inter_marksheet" name="inter_marksheet[]" multiple="multiple" class="form-control mt-1">
                          <dt><?php print !empty($inter_marksheet) ? count($inter_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                          <?php if (!empty($inter_marksheet)) {
                            foreach ($inter_marksheet as $im) { ?>
                              <img src="<?= $im ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $im ?>')" width="40" height="40" />
                          <?php }
                          } ?>
                        </div>
                      </div> -->
                      <?php
                      $ug = [];
                      if (!empty($id)) {
                        $ug = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'UG' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'UG'");
                        if ($ug->num_rows > 0) {
                          $ug = mysqli_fetch_assoc($ug);
                          $ug_marksheet = !empty($ug['Location']) ? explode('|', $ug['Location']) : [];
                        } else {
                          $ug = [];
                        }
                      }
                      ?>
                      <div class="col-md-6" id="ug_column" style="display:none">
                        <h3>UG</h3>
                        <div class="form-group form-group-default ug-program ">
                          <label>Subjects <sup class="text-danger">*</sup></label>
                          <input type="text" name="ug_subject" id="ug_subject" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Subject', $ug) ? $ug['Subject'] : '') : '' ?>" placeholder="ex: BBA">
                        </div>
                        <div class="form-group form-group-default ug-program ">
                          <label>Year <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="ug_year" id="ug_year">
                            <option value="">Select</option>
                            <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                              <option value="<?= $i ?>" <?php print !empty($ug) ? ($ug['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group form-group-default ug-program ">
                          <label>Board/University <sup class="text-danger">*</sup></label>
                          <input type="text" name="ug_board" id="ug_board" value="<?php print !empty($ug) ? (array_key_exists('Board/Institute', $ug) ? $ug['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: DU">
                        </div>
                        <?php if ($_SESSION['university_id'] == 0) { ?>
                          <div class="form-group form-group-default">
                            <label>Marks Obtained <sup class="text-danger">*</sup></label>
                            <input type="text" name="ug_obtained" id="ug_obtained" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Marks_Obtained', $ug) ? $ug['Marks_Obtained'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 400">
                          </div>
                          <div class="form-group form-group-default">
                            <label>Max Marks <sup class="text-danger">*</sup></label>
                            <input type="text" name="ug_max" id="ug_max" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Max_Marks', $ug) ? $ug['Max_Marks'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 600">
                          </div>
                          <div class="form-group form-group-default ug-program ">
                            <label>Grade/Percentage <sup class="text-danger">*</sup></label>
                            <input type="text" name="ug_total" value="<?php print !empty($ug) ? (array_key_exists('Total_Marks', $ug) ? $ug['Total_Marks'] : '') : '' ?>" id="ug_total" class="form-control" placeholder="ex: 66%">
                          </div>
                        <?php } else { ?>
                          <div class="form-group form-group-default ug-program ">
                            <label>Result <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="ug_total" id="ug_total">
                              <option value="">Select</option>
                              <option value="Passed" <?php print !empty($ug) && $ug['Total_Marks'] == 'PASSED' ? 'selected' : '' ?>>Passed</option>
                              <option value="Fail" <?php print !empty($ug) && $ug['Total_Marks'] == 'FAIL' ? 'selected' : '' ?>>Fail</option>
                              <option value="Discontinued" <?php print !empty($ug) && $ug['Total_Marks'] == 'DISCONTINUED' ? 'selected' : '' ?>>Discontinued</option>
                            </select>
                          </div>
                        <?php } ?>
                        <div class="form-group form-group-default ug-program ">
                          <label>Marksheet <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('ug_marksheet');" id="ug_marksheet" name="ug_marksheet[]" multiple="multiple" class="form-control mt-1">
                          <dt><?php print !empty($ug_marksheet) ? count($ug_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                          <?php if (!empty($ug_marksheet)) {
                            foreach ($ug_marksheet as $um) { ?>
                              <img src="<?= $um ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $um ?>')" width="40" height="40" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      $pg = [];
                      if (!empty($id)) {
                        $pg = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'PG' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'PG'");
                        if ($pg->num_rows > 0) {
                          $pg = mysqli_fetch_assoc($pg);
                          $pg_marksheet = !empty($pg['Location']) ? explode('|', $pg['Location']) : [];
                        } else {
                          $pg = [];
                        }
                      }
                      ?>
                      <div class="col-md-6" id="pg_column" style="display:none">
                        <h3>PG</h3>
                        <div class="form-group form-group-default pg-program ">
                          <label>Subjects <sup class="text-danger">*</sup></label>
                          <input type="text" name="pg_subject" id="pg_subject" value="<?php print !empty($pg) ? (array_key_exists('Subject', $pg) ? $pg['Subject'] : '') : '' ?>" class="form-control" placeholder="ex: MBA">
                        </div>
                        <div class="form-group form-group-default pg-program ">
                          <label>Year <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="pg_year" id="pg_year">
                            <option value="">Select</option>
                            <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                              <option value="<?= $i ?>" <?php print !empty($pg) ? ($pg['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group form-group-default pg-program ">
                          <label>Board/University <sup class="text-danger">*</sup></label>
                          <input type="text" name="pg_board" value="<?php print !empty($pg) ? (array_key_exists('Board/Institute', $pg) ? $pg['Board/Institute'] : '') : '' ?>" id="pg_board" class="form-control" placeholder="ex: DU">
                        </div>
                        <?php if ($_SESSION['university_id'] == 0) { ?>
                          <div class="form-group form-group-default">
                            <label>Marks Obtained <sup class="text-danger">*</sup></label>
                            <input type="text" name="pg_obtained" id="pg_obtained" value="<?php print !empty($pg) ? (array_key_exists('Marks_Obtained', $pg) ? $pg['Marks_Obtained'] : '') : '' ?>" class="form-control" placeholder="ex: 400">
                          </div>
                          <div class="form-group form-group-default">
                            <label>Max Marks <sup class="text-danger">*</sup></label>
                            <input type="text" name="pg_max" id="pg_max" value="<?php print !empty($pg) ? (array_key_exists('Max_Marks', $pg) ? $pg['Max_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 600">
                          </div>
                          <div class="form-group form-group-default pg-program ">
                            <label>Grade/Percentage <sup class="text-danger">*</sup></label>
                            <input type="text" name="pg_total" id="pg_total" value="<?php print !empty($pg) ? (array_key_exists('Total_Marks', $pg) ? $pg['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                          </div>
                        <?php } else { ?>
                          <div class="form-group form-group-default pg-program ">
                            <label>Result <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="pg_total" id="pg_total">
                              <option value="">Select</option>
                              <option value="Passed" <?php print !empty($pg) && $pg['Total_Marks'] == 'PASSED' ? 'selected' : '' ?>>Passed</option>
                              <option value="Fail" <?php print !empty($pg) && $pg['Total_Marks'] == 'FAIL' ? 'selected' : '' ?>>Fail</option>
                              <option value="Discontinued" <?php print !empty($pg) && $pg['Total_Marks'] == 'DISCONTINUED' ? 'selected' : '' ?>>Discontinued</option>
                            </select>
                          </div>
                        <?php } ?>
                        <div class="form-group form-group-default pg-program ">
                          <label>Marksheet <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('pg_marksheet');" name="pg_marksheet[]" id="pg_marksheet" multiple="multiple" class="form-control mt-1">
                          <dt><?php print !empty($pg_marksheet) ? count($pg_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                          <?php if (!empty($pg_marksheet)) {
                            foreach ($pg_marksheet as $pm) { ?>
                              <img src="<?= $pm ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $pm ?>')" width="40" height="40" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      $other = [];
                      if (!empty($id)) {
                        $other = $conn->query("SELECT Student_Academics.*, Student_Documents.Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Other' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'Other'");
                        if ($other->num_rows > 0) {
                          $other = mysqli_fetch_assoc($other);
                          $other_marksheet = !empty($other['Location']) ? explode('|', $other['Location']) : [];
                        } else {
                          $other = [];
                        }
                      }
                      ?>
                      <div class="col-md-6" id="other_column" style="display:none">
                        <h3>Other</h3>
                        <div class="form-group form-group-default other-program ">
                          <label>Subjects <sup class="text-danger">*</sup></label>
                          <input type="text" name="other_subject" id="other_subject" class="form-control" value="<?php print !empty($other) ? (array_key_exists('Subject', $other) ? $other['Subject'] : '') : '' ?>" placeholder="ex: Diploma">
                        </div>
                        <div class="form-group form-group-default other-program ">
                          <label>Year <sup class="text-danger">*</sup></label>
                          <select class="form-control" name="other_year" id="other_year">
                            <option value="">Select</option>
                            <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                              <option value="<?= $i ?>" <?php print !empty($other) ? ($other['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="form-group form-group-default other-program ">
                          <label>Board/University <sup class="text-danger">*</sup></label>
                          <input type="text" name="other_board" id="other_board" value="<?php print !empty($other) ? (array_key_exists('Board/Institute', $other) ? $other['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: DU">
                        </div>
                        <?php if ($_SESSION['university_id'] == 0) { ?>
                          <div class="form-group form-group-default">
                            <label>Marks Obtained <sup class="text-danger">*</sup></label>
                            <input type="text" name="other_obtained" id="other_obtained" value="<?php print !empty($other) ? (array_key_exists('Marks_Obtained', $other) ? $other['Marks_Obtained'] : '') : '' ?>" class="form-control" placeholder="ex: 400">
                          </div>
                          <div class="form-group form-group-default">
                            <label>Max Marks <sup class="text-danger">*</sup></label>
                            <input type="text" name="other_max" id="other_max" value="<?php print !empty($other) ? (array_key_exists('Max_Marks', $other) ? $other['Max_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 600">
                          </div>
                          <div class="form-group form-group-default other-program ">
                            <label>Grade/Percentage <sup class="text-danger">*</sup></label>
                            <input type="text" name="other_total" id="other_total" value="<?php print !empty($other) ? (array_key_exists('Total_Marks', $other) ? $other['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                          </div>
                        <?php } else { ?>
                          <div class="form-group form-group-default other-program ">
                            <label>Result <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="other_total" id="other_total">
                              <option value="">Select</option>
                              <option value="Passed" <?php print !empty($other) && $other['Total_Marks'] == 'PASSED' ? 'selected' : '' ?>>Passed</option>
                              <option value="Fail" <?php print !empty($other) && $other['Total_Marks'] == 'FAIL' ? 'selected' : '' ?>>Fail</option>
                              <option value="Discontinued" <?php print !empty($other) && $other['Total_Marks'] == 'DISCONTINUED' ? 'selected' : '' ?>>Discontinued</option>
                            </select>
                          </div>
                        <?php } ?>
                        <div class="form-group form-group-default other-program ">
                          <label>Marksheet <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('other_marksheet');" id="other_marksheet" name="other_marksheet[]" multiple="multiple" class="form-control mt-1">
                          <dt><?php print !empty($other_marksheet) ? count($other_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                          <?php if (!empty($other_marksheet)) {
                            foreach ($other_marksheet as $om) { ?>
                              <img src="<?= $om ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $om ?>')" width="40" height="40" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- New tab -->
                <div class="step-tab-panel" id="tabSubject">
                  <div class="d-flex flex-column gap-30">
                    <div class="border-bottom">
                      <h4 class="fw-bold text-danger">Notes -</h4>
                      <ul class="fw-semi-bold">
                        <li>(P) denotes that Subject includes pratical.</li>
                        <li>You can select Minimum 1 & Maximum 2 in Language Subjects.</li>
                        <li>You can select Minimum 2 & Maximum 4 in Non-Language Subjects.</li>
                        <li>You can select Maximum 2 in Vocational Subjects.</li>
                      </ul>
                    </div>
                    <form class="d-flex flex-column" name="stepSubject" id="stepSubject" role="form" action="/app/application-form/step-subject" enctype="multipart/form-data">
                      <div id="subjects_secondary">
                      </div>
                    </form>
                  </div>
                </div>
                <div class="step-tab-panel" id="tab4">
                  <form name="step4" id="step4" role="form" action="/app/application-form/step-4" enctype="multipart/form-data">
                    <?php
                    if (!empty($id)) {
                      $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
                      $photo = mysqli_fetch_array($photo);
                    }
                    ?>
                    <div class="row m-t-2">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Photo <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('photo');" id="photo" name="photo" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($photo)) { ?>
                            <img src="<?php print !empty($id) ? $photo['Location'] : '' ?>" height="100" />
                          <?php } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $aadhaars = array();
                        $aadhaar = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Aadhar'");
                        if ($aadhaar->num_rows > 0) {
                          $aadhaar = mysqli_fetch_array($aadhaar);
                          $aadhaars = explode("|", $aadhaar['Location']);
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default required">
                          <label class="national">Aadhaar<sup class="text-danger">*</sup></label>
                          <label class="international"> Other Documents <sup class="text-danger">*</sup></label>

                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('aadhar');" id="aadhar" name="aadhar[]" multiple="multiple" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($aadhaars)) {
                            foreach ($aadhaars as $aadhar) { ?>
                              <img src="<?php print !empty($id) ? $aadhar : '' ?>" height="80" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $students_signature = "";
                        $student_signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Student Signature'");
                        if ($student_signature->num_rows > 0) {
                          $student_signature = mysqli_fetch_array($student_signature);
                          $students_signature = $student_signature['Location'];
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default">
                          <label>Student's Signature <sup class="text-danger">*</sup></label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('student_signature');" id="student_signature" name="student_signature" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($students_signature)) { ?>
                            <img src="<?php print !empty($id) ? $students_signature : '' ?>" height="100" />
                          <?php } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $parents_signature = "";
                        $parent_signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Parent Signature'");
                        if ($parent_signature->num_rows > 0) {
                          $parent_signature = mysqli_fetch_array($parent_signature);
                          $parents_signature = $parent_signature['Location'];
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default">
                          <label>Parent's Signature</label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('parent_signature');" id="parent_signature" name="parent_signature" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($parents_signature)) { ?>
                            <img src="<?php print !empty($id) ? $parents_signature : '' ?>" height="100" />
                          <?php } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $migrations = array();
                        $migration = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Migration'");
                        if ($migration->num_rows > 0) {
                          $migration = mysqli_fetch_array($migration);
                          $migrations = explode("|", $migration['Location']);
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default">
                          <label>Migration Certificate</label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('migration');" id="migration" name="migration[]" multiple="multiple" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($migrations)) {
                            foreach ($migrations as $migration) { ?>
                              <img src="<?php print !empty($id) ? $migration : '' ?>" height="80" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $affidavits = array();
                        $affidavit = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Affidavit'");
                        if ($affidavit->num_rows > 0) {
                          $affidavit = mysqli_fetch_array($affidavit);
                          $affidavits = explode("|", $affidavit['Location']);
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default">
                          <label>Affidavit</label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('affidavit');" id="affidavit" name="affidavit[]" multiple="multiple" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($affidavits)) {
                            foreach ($affidavits as $affidavit) { ?>
                              <img src="<?php print !empty($id) ? $affidavit : '' ?>" height="80" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                      <?php
                      if (!empty($id)) {
                        $other_certificates = array();
                        $other_certificate = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Other Certificate'");
                        if ($other_certificate->num_rows > 0) {
                          $other_certificate = mysqli_fetch_array($other_certificate);
                          $other_certificates = explode("|", $other_certificate['Location']);
                        }
                      }
                      ?>
                      <div class="col-md-3">
                        <div class="form-group form-group-default">
                          <label>Other Certificates</label>
                          <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('other_certificate');" id="other_certificate" name="other_certificate[]" multiple="multiple" class="form-control mt-1">
                          <?php if (!empty($id) && !empty($other_certificates)) {
                            foreach ($other_certificates as $other_certificate) { ?>
                              <img src="<?php print !empty($id) ? $other_certificate : '' ?>" height="80" />
                          <?php }
                          } ?>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="step-tab-panel" id="tab5">
                  <h3>Review</h3>
                  <div class="row m-t-2">
                    <div class="col-md-12 text-center">
                      <h1>Thank you for providing the requested information.<h1>
                          <h3>Please use the links below to print the pre-filled application form or proceed to applications.</h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="step-footer">
                <button data-direction="prev" class="btn btn-light">Previous</button>
                <button data-direction="next" class="btn btn-primary">Next</button>
                <button data-direction="finish" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Main row -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/assets/plugins/formwizard/jquery-steps.js"></script>
  <script src="/assets/plugins/jquery-validate/jquery.validate.min.js"></script>
  <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>

  <?php if (isset($_GET['lead_id']) && $lead['Admission'] == 1) { ?>
    <script>
      Swal.fire({
        position: 'center',
        icon: 'success',
        title: 'Application saved successfully! To Proceed again go to Applications.',
        showConfirmButton: false,
        allowEscapeKey: false,
        allowOutsideClick: false,
        timer: 5000
      }).then((result) => {
        window.location.href = "/leads/lists"
      })
    </script>
  <?php } ?>

  <?php if ($_SESSION['crm'] > 0 && !$is_get) { ?>
    <script>
      Swal.fire({
        position: 'center',
        icon: 'error',
        title: 'To apply New Application, Please add lead first!',
        showConfirmButton: false,
        allowEscapeKey: false,
        allowOutsideClick: false,
        timer: 3000
      }).then((result) => {
        window.location.href = "/leads/generate"
      })
    </script>
  <?php } ?>

  <?php if (!isset($_GET['id'])) { ?>
    <script>
      $(function() {
        if (localStorage.getItem('inserted_id') !== null) {
          console.log();
          localStorage.removeItem('inserted_id');
          Swal.fire({
            icon: 'success',
            title: 'Previous Application is saved!',
            text: 'Please go to Applications > Edit if you want to proceed further!',
          });
        }
      });
    </script>
  <?php } ?>

  <script>
    $(function() {
      localStorage.removeItem('print_id');
      $("#dob").mask("99-99-9999")
      $("#aadhar").mask("9999-9999-9999")
      $('#dob').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: '-15y'
      });
    });
  </script>

  <script>
    function checkInterMarks() {
      var obtained = parseInt($('#inter_obtained').val());
      var max = parseInt($("#inter_max").val());
      var alerted = localStorage.getItem('alertedInter') || '';
      if (obtained > max) {
        if (alerted != 'yes') {
          alert("Obtained marks can not be higher than Maximum marks");
          $(':input[type="submit"]').prop('disabled', true);
          localStorage.setItem('alertedInter', 'yes');
        }
      } else {
        localStorage.setItem('alertedInter', 'no');
        $(':input[type="submit"]').prop('disabled', false);
        if ($('#inter_obtained').val().length > 0) {
          var percentage = (obtained / max) * 100;
          $('#inter_total').val(percentage.toFixed(2));
          $("#inter_total").prop("readonly", true);
        } else if ($('#inter_obtained').val().length == 0) {
          $("#inter_total").prop("readonly", false);
          $('#inter_total').val('');
        }
      }
    }

    // function generateInterYear(val) {
    //   var value = parseInt(val) + 1;
    //   var current = new Date().getFullYear();
    //   $('#inter_year').html('<option value="">Select</option>');
    //   for ($i = current; $i > value; $i--) {
    //     $('#inter_year').append('<option value=' + $i + '>' + $i + '</option>');
    <?php // if (!empty($id)) { 
    ?>
    // $('#inter_year').val('<?php // print !empty($intermediate) ? (array_key_exists('Year', $intermediate) ? $intermediate['Year'] : '') : '' 
                              ?>');
    <?php // } 
    ?>
    //   }
    // }

    <?php // if (!empty($intermediate)) {
    // echo 'generateInterYear(' . $high_school['Year'] . ')';
    // } 
    ?>
  </script>

  <script>
    function checkUGMarks() {
      var obtained = parseInt($('#ug_obtained').val());
      var max = parseInt($("#ug_max").val());
      var alerted = localStorage.getItem('alertedUG') || '';
      if (obtained > max) {
        if (alerted != 'yes') {
          alert("Obtained marks can not be higher than Maximum marks");
          $(':input[type="submit"]').prop('disabled', true);
          localStorage.setItem('alertedUG', 'yes');
        }
      } else {
        localStorage.setItem('alertedUG', 'no');
        $(':input[type="submit"]').prop('disabled', false);
        if ($('#ug_obtained').val().length > 0) {
          var percentage = (obtained / max) * 100;
          $('#ug_total').val(percentage.toFixed(2));
          $("#ug_total").prop("readonly", true);
        } else if ($('#ug_obtained').val().length == 0) {
          $("#ug_total").prop("readonly", false);
          $('#ug_total').val('');
        }
      }
    }

    // function generateUGYear(val) {
    //   var value = parseInt(val) + 2;
    //   var current = new Date().getFullYear();
    //   $('#ug_year').html('<option value="">Select</option>');
    //   for ($i = current; $i >= value; $i--) {
    //     $('#ug_year').append('<option value=' + $i + '>' + $i + '</option>');
    <?php // if (!empty($id)) { 
    ?>
    // $('#ug_year').val('<?php // print !empty($ug) ? (array_key_exists('Year', $ug) ? $ug['Year'] : '') : '' 
                          ?>');
    <?php // } 
    ?>
    //   }
    // }

    <?php // if (!empty($intermediate)) {
    // echo 'generateUGYear(' . $intermediate['Year'] . ')';
    // } 
    ?>
  </script>

  <script>
    function checkPGMarks() {
      var obtained = parseInt($('#pg_obtained').val());
      var max = parseInt($("#pg_max").val());
      var alerted = localStorage.getItem('alertedPG') || '';
      if (obtained > max) {
        if (alerted != 'yes') {
          alert("Obtained marks can not be higher than Maximum marks");
          $(':input[type="submit"]').prop('disabled', true);
          localStorage.setItem('alertedPG', 'yes');
        }
      } else {
        localStorage.setItem('alertedPG', 'no');
        $(':input[type="submit"]').prop('disabled', false);
        if ($('#pg_obtained').val().length > 0) {
          var percentage = (obtained / max) * 100;
          $('#pg_total').val(percentage.toFixed(2));
          $("#pg_total").prop("readonly", true);
        } else if ($('#pg_obtained').val().length == 0) {
          $("#pg_total").prop("readonly", false);
          $('#pg_total').val('');
        }
      }
    }

    // function generatePGYear(val) {
    //   var value = parseInt(val) + 2;
    //   var current = new Date().getFullYear();
    //   $('#pg_year').html('<option value="">Select</option>');
    //   for ($i = current; $i > value; $i--) {
    //     $('#pg_year').append('<option value="' + $i + '">' + $i + '</option>');
    <?php // if (!empty($id)) { 
    ?>
    // $('#pg_year').val('<?php // print !empty($pg) ? (array_key_exists('Year', $pg) ? $pg['Year'] : '') : '' 
                          ?>');
    <?php // } 
    ?>
    //   }
    // }

    <?php // if (!empty($ug)) {
    // echo 'generatePGYear(' . $ug['Year'] . ')';
    // } 
    ?>
  </script>

  <script>
    function getRegion(pincode) {
      if (pincode.length == 6) {
        $.ajax({
          url: '/app/regions/cities?pincode=' + pincode,
          type: 'GET',
          success: function(data) {
            $('#city').html(data);
            <?php if (!empty($id) && !empty($address)) { ?>
              $('#city').val('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_city', $address) ? $address['present_city'] : '') : '' ?>');
            <?php } ?>
          }
        });

        $.ajax({
          url: '/app/regions/districts?pincode=' + pincode,
          type: 'GET',
          success: function(data) {
            $('#district').html(data);
            <?php if (!empty($id) && !empty($address)) { ?>
              $('#district').val('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_district', $address) ? $address['present_district'] : '') : '' ?>');
            <?php } ?>
          }
        });

        $.ajax({
          url: '/app/regions/state?pincode=' + pincode,
          type: 'GET',
          success: function(data) {
            $('#state').val(data);
          }
        })
      }
    }

    <?php if (!empty($id)) { ?>
      getRegion('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_pincode', $address) ? $address['present_pincode'] : '') : '' ?>');
    <?php } ?>
  </script>

  <script type="text/javascript">
    function getCenter(university_id) {
      $.ajax({
        url: '/app/application-form/center?university_id=' + university_id,
        type: 'GET',
        success: function(data) {
          $('#center').html(data);
          <?php if (!empty($id)) { ?>
            $('#center').val('<?= $student['Added_For'] ?>');
          <?php } elseif (isset($_GET['lead_id'])) { ?>
            $('#center').val('<?= $lead['User_ID'] ?>');
          <?php } ?>
          //   $('#center').html(data);
          //   $('#center').val(<?php
                                //     echo !empty($id) ? (
                                //         isset($subcenter['Center']) ? $subcenter['Center'] : (
                                //             !empty($student['Added_For']) ? $student['Added_For'] : ''
                                //         )
                                //     ) : (isset($_GET['lead_id']) ? $lead['User_ID'] : '');
                                //     
                                ?>
          //     );
        }
      })
    }

    function getAdmissionSession(university_id) {
      $.ajax({
        url: '/app/application-form/admission-session?university_id=' + university_id + '&form=<?php print !empty($id) ? 1 : "" ?>',
        type: 'GET',
        success: function(data) {
          $('#admission_session').html(data);
          $('#admission_session').val(<?php print !empty($id) ? $student['Admission_Session_ID'] : '' ?>);
          getAdmissionType($('#admission_session').val());

        }
      })
    }

    function getAdmissionType(session_id) {
      const university_id = '<?= $_SESSION['university_id'] ?>';
      $.ajax({
        url: '/app/application-form/admission-type?university_id=' + university_id + '&session_id=' + session_id,
        type: 'GET',
        success: function(data) {
          $('#admission_type').html(data);
          $('#admission_type').val(<?php print !empty($id) ? $student['Admission_Type_ID'] : '' ?>);
          getCourse();
        }
      })
    }

    function getCourse() {
      var center = $('#center').val();
      const university_id = '<?= $_SESSION['university_id'] ?>';
      const session_id = $('#admission_session').val();
      const admission_type_id = $('#admission_type').val();
      $.ajax({
        url: '/app/application-form/course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&form=<?php print !empty($id) || !empty($lead_id) ? 1 : "" ?>',
        type: 'GET',
        success: function(data) {
          $('#course').val(<?php print !empty($id) ? $student['Course_ID'] : (isset($_GET['lead_id']) ? $lead['Course_ID'] : '') ?>);
          <?php if (!empty($id)) { ?>
            $("#course").html('<option value="<?= $student["Grade_Category_id"] ?>"><?= $student["Grade_Category"] ?></option>');
            $('#course').append(data);
          <?php } else { ?>
            $('#course').html(data);
          <?php } ?>
          getSubCourse();
          getEligibility();
          getitproof(center);
        }
      })
    }

    function highDetailsRequired() {
      $('.high_school').addClass('required');
      $('#high_subject').validate();
      $('#high_subject').rules('add', {
        required: true
      });
      $('#high_year').validate();
      $('#high_year').rules('add', {
        required: true
      });
      $('#high_board').validate();
      $('#high_board').rules('add', {
        required: true
      });
      $('#high_total').validate();
      $('#high_total').rules('add', {
        required: true
      });
      <?php if (empty($id)) { ?>
        $('#high_marksheet').validate();
        $('#high_marksheet').rules('add', {
          required: true
        });
      <?php } ?>

      <?php if (!empty($id) && empty($high_marksheet)) { ?>
        $('#high_marksheet').validate();
        $('#high_marksheet').rules('add', {
          required: true
        });
      <?php } ?>
    }

    function highDetailsNotRequired() {
      $('.high_school').removeClass('required');
      $('#high_subject').rules('remove', 'required');
      $('#high_year').rules('remove', 'required');
      $('#high_board').rules('remove', 'required');
      $('#high_total').rules('remove', 'required');
      $('#high_marksheet').rules('remove', 'required');
    }

    function interDetailsRequired() {
      $('.intermediate').addClass('required');
      $('#inter_subject').validate();
      $('#inter_subject').rules('add', {
        required: true
      });
      $('#inter_year').validate();
      $('#inter_year').rules('add', {
        required: true
      });
      $('#inter_board').validate();
      $('#inter_board').rules('add', {
        required: true
      });
      $('#inter_total').validate();
      $('#inter_total').rules('add', {
        required: true
      });
      <?php if (empty($id)) { ?>
        $('#inter_marksheet').validate();
        $('#inter_marksheet').rules('add', {
          required: true
        });
      <?php } ?>

      <?php if (!empty($id) && empty($inter_marksheet)) { ?>
        $('#inter_marksheet').validate();
        $('#inter_marksheet').rules('add', {
          required: true
        });
      <?php } ?>
    }

    function interDetailsNotRequired() {
      $('.intermediate').removeClass('required');
      $('#inter_subject').rules('remove', 'required');
      $('#inter_year').rules('remove', 'required');
      $('#inter_board').rules('remove', 'required');
      $('#inter_total').rules('remove', 'required');
      $('#inter_marksheet').rules('remove', 'required');
    }

    function ugDetailsRequired() {
      $('.ug-program').addClass('required');
      $('#ug_subject').validate();
      $('#ug_subject').rules('add', {
        required: true
      });
      $('#ug_year').validate();
      $('#ug_year').rules('add', {
        required: true
      });
      $('#ug_board').validate();
      $('#ug_board').rules('add', {
        required: true
      });
      $('#ug_total').validate();
      $('#ug_total').rules('add', {
        required: true
      });
      <?php if (empty($id)) { ?>
        $('#ug_marksheet').validate();
        $('#ug_marksheet').rules('add', {
          required: true
        });
      <?php } ?>

      <?php if (!empty($id) && empty($ug_marksheet)) { ?>
        $('#ug_marksheet').validate();
        $('#ug_marksheet').rules('add', {
          required: true
        });
      <?php } ?>
    }

    function ugDetailsNotRequired() {
      $('.ug-program').removeClass('required');
      $('#ug_subject').rules('remove', 'required');
      $('#ug_year').rules('remove', 'required');
      $('#ug_board').rules('remove', 'required');
      $('#ug_total').rules('remove', 'required');
      $('#ug_marksheet').rules('remove', 'required');
    }

    function pgDetailsRequired() {
      $('.pg-program').addClass('required');
      $('#pg_subject').validate();
      $('#pg_subject').rules('add', {
        required: true
      });
      $('#pg_year').validate();
      $('#pg_year').rules('add', {
        required: true
      });
      $('#pg_board').validate();
      $('#pg_board').rules('add', {
        required: true
      });
      $('#pg_total').validate();
      $('#pg_total').rules('add', {
        required: true
      });
      <?php if (empty($id)) { ?>
        $('#pg_marksheet').validate();
        $('#pg_marksheet').rules('add', {
          required: true
        });
      <?php } ?>

      <?php if (!empty($id) && empty($pg_marksheet)) { ?>
        $('#pg_marksheet').validate();
        $('#pg_marksheet').rules('add', {
          required: true
        });
      <?php } ?>
    }

    function pgDetailsNotRequired() {
      $('.pg-program').removeClass('required');
      $('#pg_subject').rules('remove', 'required');
      $('#pg_year').rules('remove', 'required');
      $('#pg_board').rules('remove', 'required');
      $('#pg_total').rules('remove', 'required');
      $('#pg_marksheet').rules('remove', 'required');
    }

    function otherDetailsRequired() {
      $('.other-program').addClass('required');
      $('#other_subject').validate();
    }

    function otherDetailsNotRequired() {
      $('.other-program').removeClass('required');
      $('#other_subject').rules('remove', 'required');
      $('#other_year').rules('remove', 'required');
      $('#other_board').rules('remove', 'required');
      $('#other_total').rules('remove', 'required');
      $('#other_marksheet').rules('remove', 'required');
    }

    var selectedGrade = $('#course').val();

    function getSubCourse() {
      // selectedGrade = $('#course').val();
      //var course_id = $('#course').find('option:selected').text();
      // if (course_id == '10th' || course_id == 'Secondary' || course_id == 'adeeb') {
      // 	$("#hight_school_acadimics").addClass("d-none");
      //   $("#subjects_secondary").show()
      //   $("#subjects_senior_secondary").hide()
      // }else if (course_id == 'adeeb-e-mahir (Commerce)' || course_id == 'adeeb-e-mahir (Art)' || course_id == 'adeeb-e-mahir (Science)'){
      //   $("#hight_school_acadimics").removeClass("d-none");
      //   $("#subjects_secondary").hide();
      //   $("#subjects_senior_secondary").show();
      // }
      var center = $('#center').val();
      const university_id = '<?= $_SESSION['university_id'] ?>';
      const session_id = $('#admission_session').val();
      const admission_type_id = $('#admission_type').val();
      const course_id = $('#course').find('option:selected').text();
      const course_val = $('#course').val();
      var student_id_12 = '';
      <?php if (!empty($id)) { ?>
        var student_id_12 = <?= !empty($id) ?> ? '<?= $id ?>' : '';
      <?php } ?>
      $.ajax({
        url: '/app/application-form/sub-course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&course_id=' + course_val + '&student_id=' + student_id_12,
        type: 'GET',
        success: function(data) {
          if (course_id == 'adeeb') {
            $('#subjects_secondary').html('');
            $("#hight_school_acadimics").addClass("d-none");
            $("#subjects_secondary").show()
            $('#subjects_secondary').html(data);

          } else if (course_id == 'adeeb-e-mahir (Commerce)' || course_id == 'adeeb-e-mahir (Arts)' || course_id == 'adeeb-e-mahir (Science)') {
            $('#subjects_secondary').html('');
            $("#hight_school_acadimics").removeClass("d-none");
            $("#subjects_secondary").show();
            $('#subjects_secondary').html(data);
          }
          if ($('.ele_sub div').length == 1) {
            $('#subjects_elective').prop('checked', true);
          }
        }
      });
    }

    function getMode() {
      const sub_course_id = $('#sub_course').val();
      $.ajax({
        url: '/app/application-form/mode?sub_course_id=' + sub_course_id,
        type: 'GET',
        success: function(data) {
          $('#mode').html(data);
          getDuration();
          getEligibility();
        }
      })
    }

    function getDuration() {
      const admission_type_id = $('#admission_type').val();
      const sub_course_id = $('#sub_course').val();
      $.ajax({
        url: '/app/application-form/duration?admission_type_id=' + admission_type_id + '&sub_course_id=' + sub_course_id,
        type: 'GET',
        success: function(data) {
          $('#duration').html(data);
          $('#duration').val(<?php print !empty($id) ? $student['Duration'] : '' ?>)
        }
      })
    }

    function getEligibility() {
      var course_id = $('#course').find('option:selected').text();
      console.log(course_id, "elegjfhjfhg");
      if (course_id == 'adeeb') {
        otherDetailsRequired();
        $("#other_column").css('display', 'block');
        $("#other_column").addClass('col-md-5');
      } else {
        otherDetailsNotRequired();
      }

      if (course_id == 'adeeb-e-mahir (Commerce)' || course_id == 'adeeb-e-mahir (Arts)' || course_id == 'adeeb-e-mahir (Science)') {
        highDetailsRequired();
        $("#high_school_column").css('display', 'block');
        $("#high_school_column").addClass('col-md-5');
        $("#other_column").css('display', 'block');
        $("#other_column").addClass('col-md-5');
      } else {
        highDetailsNotRequired();
        // $("#high_school_column").css('display', 'none');
      }
    }

    getCenter('<?= $_SESSION['university_id'] ?>');
    getAdmissionSession('<?= $_SESSION['university_id'] ?>');

    function fileValidation(id) {
      var fi = document.getElementById(id);
      if (fi.files.length > 0) {
        for (var i = 0; i <= fi.files.length - 1; i++) {
          var fsize = fi.files.item(i).size;
          var file = Math.round((fsize / 1024));
          // The size of the file.
          if (file >= 500) {
            $('#' + id).val('');
            alert("File too Big, each file should be less than or equal to 500KB");
          }
        }
      }
    }
  </script>
  <script>
    function onlyOne(el) {
      var checkboxes = document.getElementsByName('subjects_elective')
      var checkboxesList = document.getElementsByClassName("checkoption");
      for (var i = 0; i < checkboxesList.length; i++) {
        checkboxesList.item(i).checked = false; // Uncheck all checkboxes
      }
      el.checked = true;
    }
  </script>
  <script>
    var step1 = $('#step1');
    var step1Validator = step1.validate({
      rules: {
        center: {
          required: true
        },
        admission_session: {
          required: true
        },
        admission_type: {
          required: true
        },
        course: {
          required: true
        },
        sub_course: {
          required: true
        },
        duration: {
          required: true
        },
        full_name: {
          required: true
        },
        first_name: {
          required: true
        },
        last_name: {
          required: true
        },
        father_name: {
          required: true
        },
        mother_name: {
          required: true
        },
        dob: {
          required: true
        },
        gender: {
          required: true
        },
        category: {
          required: true
        },
        employment_status: {
          required: true
        },
        aadhar: {
          required: true
        },
        nationality: {
          required: true
        },
      },
      highlight: function(element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });

    var step2 = $('#step2');
    var step2Validator = step2.validate({
      rules: {
        email: {
          required: true
        },
        contact: {
          required: true
        },
        address: {
          required: true
        },
        pincode: {
          required: true
        },
        city: {
          required: true
        },
        district: {
          required: true
        },
        state: {
          required: true
        },
      },
      highlight: function(element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });

    var step3 = $('#step3');
    var step3Validator = step3.validate();

    var stepSubject = $('#stepSubject');
    var stepSubjectValidator = stepSubject.validate();

    var step4 = $('#step4');
    var step4Validator = step4.validate({
      rules: {
        <?php print (!empty($id) && empty($photo)) ? "photo: {required:true}," : "" ?>
        <?php print empty($id) ? "photo: {required:true}," : "" ?>
        <?php print (!empty($id) && empty($student_signature)) ? "student_signature: {required:true}," : "" ?>
        <?php print empty($id) ? "student_signature: {required:true}," : "" ?>
        <?php print (!empty($id) && empty($aadhaars)) ? "'aadhar[]': {required:true}," : "" ?>
        <?php print empty($id) ? "'aadhar[]': {required:true}," : "" ?>
      },
      highlight: function(element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
    // var step5 = $('#step5');
    // var step5Validator = step5.validate();

    $('#appForm').steps({
      onChange: function(currentIndex, newIndex, stepDirection) {
        console.log('onChange', currentIndex, newIndex, stepDirection);
        // tab1
        if (currentIndex === 0) {
          if (stepDirection === 'forward') {
            var valid = step1.valid();
            if (!valid) {
              return false;
            } else {
              $('#step1').submit();
            }
            return valid;
          }
          if (stepDirection === 'backward') {
            step1Validator.resetForm();
          }
        }

        // tab2
        if (currentIndex === 1) {
          if (stepDirection === 'forward') {
            var valid = step2.valid();
            if (!valid) {
              return false;
            } else {
              $('#step2').submit();

            }
            return valid;
          }
          if (stepDirection === 'backward') {
            step2Validator.resetForm();
          }
        }

        // tab3
        if (currentIndex === 2) {
          if (stepDirection === 'forward') {
            var valid = step3.valid();
            if (!valid) {
              return false;
            } else {
              $('#step3').submit();
            }
            return valid;
          }
          if (stepDirection === 'backward') {
            step3Validator.resetForm();
          }
        }

        // tab4
        if (currentIndex === 3) {
          if (stepDirection === 'forward') {
            var valid = stepSubject.valid();
            if (!valid) {
              return false;
            } else {
              var languageSubjects = $('input[name="language_subjects[]"]:checked').length;
              var selectedSubjects = $('input[name="language_subjects[]"]:checked').length + $('input[name="subjects[]"]:checked').length + $('input[name="subjects_elective[]"]:checked').length + $('input[name="subjects_optional[]"]:checked').length;
              if (languageSubjects === 3 && selectedSubjects == 6) {
                $('#stepSubject').submit();
              } else {
                setTimeout(function() {
                  notification('error', "Language Subjects should be 3 and Total Subjects should be 6!");
                }, 100)
                return false;
              }
            }
            return valid;
          }
          if (stepDirection === 'backward') {
            stepSubjectValidator.resetForm();
          }
        }

        if (currentIndex === 4) {
          $('#appForm .step-footer .btn[data-direction="next"]').text('Submit');
          if (stepDirection === 'forward') {
            var valid = step4.valid();
            if (!valid) {
              return false;
            } else {
              $('#step4').submit();
            }
            return valid;
          }
          if (stepDirection === 'backward') {
            step4Validator.resetForm();
          }
        }

        if (currentIndex === 5) {
          $('#appForm .step-footer .btn[data-direction="finish"]').text('See Applications').click(function() {
            location.href = "/admissions/applications"
          });
          $('#appForm .step-footer .btn[data-direction="prev"]').html('<i class="fa fa-print mr-2"></i>Print Form').click(function() {
            printForm()
          });

        }

        // return submitForm(currentIndex);
      },
      onFinish: function() {
        console.log('Form Submiited Completed');
      }
    });

    // function submitForm(index) {
    //   var $valid = $("#step_" + index).valid();
    //   if (!$valid) {
    //     return false;
    //   } else {
    //     $('#step_' + index).submit();
    //   }
    // }
    $('#step1').submit(function(e) {
      var formData = new FormData(this);
      formData.append('inserted_id', localStorage.getItem('inserted_id'));
      formData.append('lead_id', '<?php echo isset($_GET['lead_id']) ? $lead_id : 0 ?>');
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
     
          if (data.status == 200) {
            notification('success', data.message);
            localStorage.setItem('inserted_id', data.id);
          } else {
            notification('danger', data.message);
            $('#previous-button').click();
          }
        },
        error: function(data) {
          notification('danger', 'Server is not responding. Please try again later');
          $('#previous-button').click();
          console.log(data);
        }
      });
    });

    $('#step2').submit(function(e) {
      var formData = new FormData(this);
      formData.append('inserted_id', localStorage.getItem('inserted_id'));
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            notification('success', data.message);
          } else {
            notification('danger', data.message);
            $('#previous-button').click();
          }
        },
        error: function(data) {
          notification('danger', 'Server is not responding. Please try again later');
          $('#previous-button').click();
          console.log(data);
        }
      });
    });

    $('#step3').submit(function(e) {
      var formData = new FormData(this);
      formData.append('inserted_id', localStorage.getItem('inserted_id'));
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            notification('success', data.message);
          } else {
            notification('danger', data.message);
            $('#previous-button').click();
          }
        },
        error: function(data) {
          notification('danger', 'Server is not responding. Please try again later');
          $('#previous-button').click();
          console.log(data);
        }
      });
    });

    $('#step4').submit(function(e) {
      var formData = new FormData(this);
      formData.append('inserted_id', localStorage.getItem('inserted_id'));
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            localStorage.removeItem('inserted_id');
            localStorage.setItem('print_id', data.print_id);
            notification('success', data.message);
          } else {
            notification('danger', data.message);
          }
        },
        error: function(data) {
          notification('danger', 'Server is not responding. Please try again later');
          console.log(data);
        }
      });
    });

    $('#stepSubject').submit(function(e) {
      // Count the number of checked checkboxes with the same name
      var countCheckboxes = $('input[name="language_subjects[]"]:checked');
      var formData = new FormData(this);
      formData.append('inserted_id', localStorage.getItem('inserted_id'));
      formData.append('lead_id', '<?php echo isset($_GET['lead_id']) ? $lead_id : 0 ?>');
      e.preventDefault();
      // If more than three checkboxes are checked, uncheck the last one
      if (countCheckboxes.length === 3) {
        $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message)
            } else {
              notification('danger', data.message)
              $('#previous-button').click();
            }
          },
          error: function(data) {}
        });
      } else {
        notification('danger', "Language Subjects can no be grater OR Less than three!");
        $('#previous-button').click();
      }
    })

    function printForm() {
      window.open('/forms/48/index.php/?student_id=' + localStorage.getItem('print_id'));
    }

    // remove Swicth type input box from Subject UI
    //$(document).ready(() => {
    //$("#stepSubject input[type=checkbox]").bootstrapSwitch('destroy'); 
    // getSubCourse();
    //})
  </script>
  <script>
    $(document).on('change', '.language_subject', function() {
      // Count the number of checked checkboxes with the same name
      var checkedCheckboxes = $('input[name="language_subjects[]"]:checked');
      // If more than three checkboxes are checked, uncheck the last one
      if (checkedCheckboxes.length > 3) {
        $(this).prop('checked', false);
      }
    });

    $(document).ready(function() {
      var vartical_id = '<?= $vartical_id ?>';

      if (vartical_id == '3' && !empty(vartical_id)) {
        $(".international").show();
        $(".national").hide();
        $("#contact").attr("maxlength", "15");
        $("#contact").attr("placeholder", "ex: +123456789012");
      } else {
        $(".national").show();
        $(".international").hide();
        $("#contact").attr("maxlength", "10");
        $("#contact").attr("placeholder", "ex: 9977886655");
      }
      getitproof();
      $("#center").select2({
        placeholder: "Choose Center",
      })
    })



    function getitproof(center = null) {
      var center = $("#center").val();
      $.ajax({
        url: "/app/application-form/get-Vertical",
        data: {
          center: center
        },
        type: "POST",
        success: function(response) {
          if (response == 3) {
            $(".international").show();
            $(".national").hide();
            $("#contact").attr("maxlength", "15");
            $("#contact").attr("placeholder", "ex: +123456789012");
          } else {
            $(".national").show();
            $(".international").hide();
            $("#contact").attr("maxlength", "10");
            $("#contact").attr("placeholder", "ex: 9977886655");
          }
        }

      })

    }
  </script>
  <script src="/assets/country-select-js-master/build/js/countrySelect.min.js"></script>
  <script>
    $(document).ready(function() {
      var selectedCountry = '<?= isset($student['Nationality']) ? $student['Nationality'] : '' ?>';

      var options = {
        responsiveDropdown: true
      };
      if (!selectedCountry) {
        options.defaultCountry = "in";
      }
      $("#country").countrySelect(options);
      if (selectedCountry) {
        $("#country").countrySelect("setCountry", selectedCountry);
      }
    });
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>