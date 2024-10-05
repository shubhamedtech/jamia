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
            echo '<h1 class="text-capitalize d-inline fw-bold">' . str_replace('_',' ',$crumb[0]) . '</h1>';
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
                  <?php  $sub_center_query = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Center_SubCenter LEFT JOIN Users ON Users.ID = Center_SubCenter.Sub_Center  WHERE Center_SubCenter.Center='".$_SESSION['ID']."' AND Users.Role='Sub-Center'");
                    while($subCenterArr = $sub_center_query->fetch_assoc()){ ?>
                    <option value="">Choose Sub Center</option>
                    <option value="<?= $subCenterArr['ID'] ?>"><?= $subCenterArr['Name']."(".$subCenterArr['Code'].")"  ?></option>
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
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Enrollment No.</th>
                            <th>Father Name</th>
                            <th>Mother Name</th>
                            <th>DOB</th>
                            <th>Subjects</th>
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
  <div class="modal fade slide-up" id="correctionmodal" style="z-index:9999" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="correction_modal_content">
        
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
        var url = role=='Administrator'?"/app/applications/application-correction-admin-server":"/app/applications/application-correction-server";
      var applicationSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': url,
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [
          {
            data: "Unique_ID",
          },
          {
            data: "First_Name",
          },
          {
            data: "Enrollment_No",
          },
          {
            data: "Father_Name",
          },
          {
            data: "Mother_Name",
          },
          {
            data: "DOB",
          },
          {
            data:"Subjects",
            render:function(data,type,full)
            {
                return full['Subjects']??"";
            }
          },
          {
            data:"Action",
            render:function(data,type,full)
            {
                var role = '<?php echo $_SESSION["Role"]?>';
                if(role=='Center' || role=='Sub-Center')
                {
                    if(full['correctionFormStatus']==0)
                    {
                        button = "<button class='btn btn-secondary' onclick='markCorrection(&#39;"+full['ID']+"&#39;)'>Correction Submitted</button>";
                    }
                    else if(full['correctionFormStatus']==1)
                    {
                        button = "<button class='btn btn-success'>Approved</button>";
                    }
                    else if(full['correctionFormStatus']==2)
                    {
                        button = "<button class='btn btn-danger'>Rejected</button>";
                    }
                    else
                    {
                        button = "<button class='btn btn-secondary' onclick='markCorrection(&#39;"+full['ID']+"&#39;)'>Edit Application</button>";
                    }
                }
                else
                {
                    if(full['correctionFormStatus']==0)
                    {
                        button = "<button class='btn btn-secondary' onclick='checkCorrectoin(&#39;"+full['ID']+"&#39;)'>Review Application</button>";
                    }
                    else if(full['correctionFormStatus']==1)
                    {
                        button = "<button class='btn btn-success'>Approved</button>";
                    }
                    else if(full['correctionFormStatus']==2)
                    {
                        button = "<button class='btn btn-danger'>Rejected</button>";
                    }
                    else
                    {
                        button = "";
                    }
                }
                
                return button;
            }
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

     
      applicationTable.dataTable(applicationSettings);

      // search box for table
      $('#application-search-table').keyup(function() {
        applicationTable.fnFilter($(this).val());
      });
   
      
      

      applicationTable.on('draw.dt', function() {
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
      console.log(url,"url");
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
  $(document).ready(function(){
     var center_id = '<?= $_SESSION['ID'] ?>';
     var role = '<?= $_SESSION['Role'] ?>';
  })
</script>
  <script>
    $("#users").select2({
      placeholder: 'Choose Center'
    })

    $("#verticals").select2({
      placeholder:"Choose verticals"
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
    

    function addFilter(id, by, role=null) {
      console.log(id,'id');
      console.log(by,'by');
      console.log(role,'role');
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
              if('<?= $_SESSION['Role'] ?>'==='Administrator'){
                $(".sub_center").html(data.subCenterName);
              } 
            //$(".sub_center").html(data.subCenterName);
          }
        }
      })
    }

    function addCenterFilter(id, by, role=null) {
      console.log(id,'id');
      console.log(by,'by');
      console.log(role,'role');
      $.ajax({
        url: '/app/applications/filter',
        type: 'POST',
        data: { id, by, role },
        dataType: 'json',
        success: function(data) {
          console.log(data);
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
            if('<?= $_SESSION['Role'] ?>'==='Administrator'){
              $("#users").html(data.centerName);
            }
          }
        }
      })
    }
    
    function addSubCenterFilter(id, by, role=null) {
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
      var endDate   = $("#endDateFilter").val();
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

    function checkCorrectoin(studentsId)
    {
        $.ajax({
            url:"/app/applications/check_correction.php?id="+studentsId,
            type:'get',
            success:function(res)
            {
                $('#correction_modal_content').html(res);
                $('#correctionmodal').modal('show');
            }
        })
    }

    function markCorrection(studentsId)
    {
        $.ajax({
            url:"/app/applications/mark_correction.php?id="+studentsId,
            type:'get',
            success:function(res)
            {
                $('#correction_modal_content').html(res);
                $('#correctionmodal').modal('show');

            }
        })
    }
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>