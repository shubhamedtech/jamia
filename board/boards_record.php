<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')); ?>
    <?php include '../includes/db-config.php'; ?>
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
            <?php if (isset($_SESSION['university_id'])) { ?>
                <div class="card card-transparent">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-start">
                            <div class="col-md-1">
                                <div class="form-group">
                                    <select class="form-control" data-init-plugin="select2" id="sessions">
                                        <option value="All">All</option>
                                        <?php
                                        $sql = "SELECT ID, Name FROM admission_sessions";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . $row['ID'] . '">' . $row['Name'] . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No sessions available</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 m-b-10">
                                <div class="form-group">
                                    <input type="text" id="students_id" class="form-control" name="students_id" placeholder="Enter Student ID">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success" onclick="getTable()">Search</button>
                            </div>

                            <div class="row" id="records"></div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php } ?>
                </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <script>
            $("#sessions").select2({
                placeholder: 'Choose Department'
            });
        </script>
        <script type="text/javascript">
            function getTable() {
                var sessions = $('#sessions').val();
                var students_id = $('#students_id').val();
                $.ajax({
                    url: '/app/board_login/adm_record?sessions=' + sessions + '&students_id=' + students_id,
                    type: 'GET',
                    success: function(data) {
                        $('#records').html(data);
                    }
                });
            }

            function removeTable() {
                $('#records').html('');
            }
        </script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>