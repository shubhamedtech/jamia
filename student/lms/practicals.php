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

                    <div class="row" id="practicals"></div>
                </div>
            </div>
        </div>
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

            getSemester(<?= $_SESSION['Sub_Course_ID'] ?>);
        </script>

        <script type="text/javascript">
            function getTable() {
                var course_id = '<?= $_SESSION['Sub_Course_ID'] ?>';
                var semester = $('#semester').val();
                if (course_id.length > 0 && semester.length > 0) {
                    $.ajax({
                        url: '/app/practicals/syllabus?course_id=' + course_id + '&semester=' + semester,
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
        </script> <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>