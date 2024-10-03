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
                <div class="card-header">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group form-group-default required">
                                <label>Semester</label>
                                <select class="form-control" id="semester" onchange="getTable()">
                                    <option value="">Choose</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12" id="student_videos">
                    </div>
                    <div class="col-md-12" id="student_videos_not">
                        <p class="text-center">Please Select Semesters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

</div>
<script type="text/javascript">
    function getSemester(id) {
        $.ajax({
            url: '/app/videos/students/semester?id=' + id,
            type: 'GET',
            success: function(data) {
                $("#semester").html(data);
            }
        })
    }

    getSemester(<?= $_SESSION['Sub_Course_ID'] ?>);
</script>

<script type="text/javascript">
    function getTable() {
        $('#student_videos_not').hide();
        var course_id = '<?= $_SESSION['Sub_Course_ID'] ?>';
        var semester = $('#semester').val();
        if (course_id.length > 0 && semester.length > 0) {
            $.ajax({
                url: '/app/videos/students/syllabus?course_id=' + course_id + '&semester=' + semester,
                type: 'GET',
                success: function(data) {
                    $('#student_videos').html(data);
                }
            })
        } else {
            $('#student_videos').html('');
        }
    }

    function removeTable() {
        $('#assignments').html('');
    }
</script>
<script type="text/javascript">
      function uploadFile(unit_id, syllabus_id, sem) {
        $.ajax({
          url: '/app/videos/create?unit_id=' + unit_id + '&syllabus_id=' + syllabus_id + '&sem=' + sem,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      function videoList(id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/students/show-list',
          type: 'POST',
          data: {
            "id": id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          },
          complete: function() {
            $('.video-js').each(function() {
              videojs(this, {
                width: 200,
                height: 100,
                controls: true,
                preload: "auto",
              });
            });
          }
        })
      }
    </script>
    <script>
      function addAssessment(id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/students/assessments/create?unit_id=' + unit_id + '&syllabus_id=' + sub_id + '&video_id=' + id,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>
    <script>
      function assessmentList(id, video_id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/students/assessments/show-list',
          type: 'POST',
          data: {
            "id": id,
            "video_id": video_id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <script>
        function startAssessment(assessment_id, video_id, unit_id, suject_id){
            $('.modal').modal('hide');
            $.ajax({
                url: '/app/videos/students/assessments/store',
                type: 'POST',
                data: {
                    "student_id": <?= $_SESSION['ID'] ?>,
                    "assessment_id": assessment_id,
                    "video_id": video_id,
                    "suject_id": suject_id,
                    "unit_id": unit_id
                },
                success: function(data) {
                    // $("#md-modal-content").html(data);
                    // $("#mdmodal").modal('show');
                    if (data.status == 200) {
                        notification('success', data.message);
                        localStorage.setItem('inserted_id', data.id);
                    } else {
                        notification('danger', data.message);
                        $('#previous-button').click();
                    }
                }
            });
        }
    </script>
<script src="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video.min.js"></script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>

    