<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

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
        <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="upload('datesheets', 'md')"><i class="fa fa-upload fa-lg"></i></button>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-body">
          <div class="row justify-content-between">
            <div class="col-md-3">
              <div class="form-group form-group-default required">
                <select class="form-control"  id="courses" data-init-plugin ="select2" data-placeholder = "Choose Courses" >
                  <option value="">Choose courses</option>
                  <option value="0">All</option>
                  <?php $courses = $conn->query("SELECT `ID`,`Name` from `Courses`");
                  while ($course = $courses->fetch_assoc()) {
                    echo '<option value="' . $course['ID'] . '">' . $course['Name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group form-group-default required">
                <select class="form-control"  id="session" data-init-plugin ="select2" data-placeholder = "Choose Session" >
                  <option value="">Choose session</option>
                  <option value="0">All</option>
                  <?php $courses = $conn->query("SELECT `ID`,`Name` FROM `Exam_Sessions`");
                  while ($course = $courses->fetch_assoc()) {
                    echo '<option value="' . $course['ID'] . '">' . $course['Name'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-3 mt-1">
              <input type="text" id="subject-search-table" class="form-control" placeholder="Search">
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover nowrap" id = "datesheets" >
              <thead>
                <tr>
                  <th>Exam Session</th>
                  <th>Course Name</th>
                  <th>Paper Name</th>
                  <th>Exam Date</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                </tr>
              </thead>
              <tbody id = "tbl"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      
      $("#courses").select2({
        placeholder: 'Choose Courses',
      });

      $("#session").select2({
        placeholder: 'Choose Session',
      });

      function settings() {
        var course_id = $('#courses').val();
        var session_id = $("#session").val();
        var setting = {
          'processing': true,
          'serverSide': true,
          "pageLength": 20,
          lengthMenu: [ [5,10, 15, 25, 50, 100, -1], [5,10,15, 25, 50, 100, "All"] ],
          'serverMethod': 'post',
          'ajax': {
              'url': '/app/datesheets/syllabus' ,
              "data": function(d) {
                d.course_id = course_id;
                d.session_id = session_id;
                return d;
            }
          },
          'columns' : [{
              'data' : "Exam session"
            },{
              'data' : "Course Name",
            },{
              'data' : "Paper Name", 
            },{
              'data' : "Exam Date",
            },{
              'data' : "Start Time",
            },{
              'data' : "End Time",
            }
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
              "sLengthMenu": "_MENU_ ",
              "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 20,
          "drawCallback": function() {
              $('[data-toggle="tooltip"]').tooltip();
          }
        };
        return setting;
      } 


      $('#subject-search-table').keyup(function() {
        $("#datesheets").dataTable(settings()).fnFilter($(this).val());
      });

      $(function(){        
        $("#datesheets").dataTable(settings());
      });

      $("#courses,#session").on('change',function(){
        $("#datesheets").dataTable(settings());
      });

    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>