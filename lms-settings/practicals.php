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
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>Course</label>
                <select class="form-control" id="course" onchange="getSemester(this.value); removeTable()">
                  <option value="">Choose</option>
                  <?php
                  $condition = "";
                  if (in_array($_SESSION['Role'], ['Center', 'Sub-Center'])) {
                    $ids = array();
                    $sub_course_ids = $conn->query("SELECT Sub_Course_ID FROM Center_Sub_Courses WHERE `User_ID` = " . $_SESSION['ID'] . "");
                    while ($sub_course_id = $sub_course_ids->fetch_assoc()) {
                      $ids[] = $sub_course_id['Sub_Course_ID'];
                    }
                    $condition = " AND Sub_Courses.ID IN (" . implode(",", $ids) . ")";
                  }
                  $sub_courses = $conn->query("SELECT CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course, Sub_Courses.ID FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Sub_Courses.University_ID = " . $_SESSION['university_id'] . " AND Paper_Type = 'Practical' $condition ORDER BY Sub_Courses.Name ASC");
                  while ($sub_course = $sub_courses->fetch_assoc()) {
                    echo '<option value="' . $sub_course['ID'] . '">' . $sub_course['Sub_Course'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>Semester</label>
                <select class="form-control" id="semester" onchange="getTable()">
                  <option value="">Choose</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row" id="practicals"></div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script type="text/javascript">
    function getSemester(id) {
      $.ajax({
        url: '/app/practicals/semester?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#semester").html(data);
        }
      })
    }
  </script>

  <script type="text/javascript">
    function getTable() {
      var course_id = $('#course').val();
      var semester = $('#semester').val();
      if (course_id.length > 0 && semester.length > 0) {
        $.ajax({
          url: '/app/practicals/syllabus?sub_course_id=' + sub_course_id + '&semester=' + semester,
          type: 'GET',
          success: function(data) {
            $('#practicals').html(data);
          }
        })
      } else {
        $('#practicals').html('');
      }
    }

    function removeTable() {
      $('#practicals').html('');
    }
  </script>

  <script type="text/javascript">
    function uploadFile(table, column, id) {
      $.ajax({
        url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
        type: 'GET',
        success: function(data) {
          $("#md-modal-content").html(data);
          $("#mdmodal").modal('show');
        }
      })
    }
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>