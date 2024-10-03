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
                    <div class="col-md-12" id="student_e_books">
                    </div>
                    <div class="col-md-12" id="student_e_books_not">
                        <p class="text-center">Please Select Semesters</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>

<script type="text/javascript">
    function getSemester(id) {
        $.ajax({
            url: '/app/e-books/semester?id=' + id,
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
        $('#student_e_books_not').hide();
        var course_id = '<?= $_SESSION['Sub_Course_ID'] ?>';
        var semester = $('#semester').val();
        if (course_id.length > 0 && semester.length > 0) {
            $.ajax({
                url: '/app/e-books/students/syllabus?course_id=' + course_id + '&semester=' + semester,
                type: 'GET',
                success: function(data) {
                    $('#student_e_books').html(data);
                }
            })
        } else {
            $('#student_e_books').html('');
        }
    }

    function removeTable() {
        $('#student_e_books').html('');
    }
</script>
<script>
      function E_bookList(id, unit_id, sub_id, ) {
        // console.log(id, 'sandip',sub_id,  unit_id);
        $.ajax({
          url: '/app/e-books/students/show-list',
          type: 'POST',
          data: {
            "id": id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>
     <script>
      function addAssessment(id, unit_id, sub_id){
        $.ajax({
          url: '/app/e-books/assessments/create?unit_id=' + unit_id + '&syllabus_id=' + sub_id + '&ebook_id=' + id,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>
     <script>
      function assessmentList(assessment_id, ebook_id, unit_id, sub_id){
        $.ajax({
          url: '/app/e-books/students/assessments/show-list',
          type: 'POST',
          data: {
            "assessment_id": assessment_id,
            "ebook_id" : ebook_id,
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
    function startAssessment(assessment_id, e_book_id, unit_id, suject_id){
        $('.modal').modal('hide');
        $.ajax({
            url: '/app/e-books/students/assessments/store',
            type: 'POST',
            data: {
                "student_id": <?= $_SESSION['ID'] ?>,
                "assessment_id": assessment_id,
                "e_book_id": e_book_id,
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