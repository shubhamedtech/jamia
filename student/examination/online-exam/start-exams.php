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
            <div class="row">
                <div class="col-md-12" id="exam_data">
                    <?php
                    $start_exam = false;
                    $syllabus_id = 0;
                    $date_sheet_id = 0;
                    $exam_sessions = $conn->query("SELECT ID FROM Exam_Sessions WHERE JSON_KEYS(Admission_Session) LIKE '%" . $_SESSION['Admission_Session_ID'] . "%'");
                    // print_r($exam_sessions->fetch_assoc());
                    // exit;
                    if ($exam_sessions->num_rows > 0) {
                        while ($exam_session = $exam_sessions->fetch_assoc()) {
                            $exam_session_id = $exam_session['ID'];
                            $date_sheet = $conn->query("SELECT Date_Sheets.ID, Syllabus_ID FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID WHERE Exam_Session_ID = " . $exam_session_id . " AND Exam_Date = '" . date('Y-m-d') . "' AND Start_Time <= '" . date('H:i:s') . "' AND End_Time >= '" . date('H:i:s') . "' AND Syllabi.Sub_Course_ID = " . $_SESSION['Sub_Course_ID'] . " ORDER BY Date_Sheets.ID DESC LIMIT 1");
                        }

                        if ($date_sheet->num_rows > 0) {
                            $start_exam = true;
                            $date_sheet = $date_sheet->fetch_assoc();
                            $syllabus_id = $date_sheet['Syllabus_ID'];
                            $date_sheet_id = $date_sheet['ID'];
                    ?>
                            <div class="card">
                                <div class="card-header seperator">
                                    <h4>Instructions</h4>
                                </div>
                                <div class="card-body">
                                    <ul>
                                        <li>All questions are mandatory.</li>
                                        <li>Proctoring is enable through your webcam and microphone.</li>
                                        <li>No cell phones or other secondary devices in the room or test area.</li>
                                        <li>Your desk/table must be clear or any materials except your test-taking device.</li>
                                        <li>No one else can be in the room with you.</li>
                                        <li>The testing room must be well-lit and you must be clearly visible.</li>
                                        <li>No dual screens/monitors.</li>
                                        <li>Do not leave the camera.</li>
                                        <li>No use of additional applications.</li>
                                    </ul>
                                    <br>
                                    <center>
                                        <h3 class="text-danger">Note</h3>
                                        <h4>Before exam start please ready with ID card to verification</h4>
                                        <h4>Good Luck with your Examination!</h4>
                                    </center>
                                </div>
                                <div class="row d-flex justify-content-center mb-4">
                                    <button class="btn btn-primary" onclick="openCamera()">Start</button>
                                </div>
                            </div>
                    <?php
                        } else {
                            echo '<center><h3>No Exams for today!</h3></center>';
                        }
                    } else {
                        echo '<center><h3>No Exams for today!</h3></center>';
                    }
                    ?>
                </div>
            </div>
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <?php if ($start_exam) { ?>
            <script type="text/javascript">
                function openCamera() {
                    $("#exam_data").html('');
                    $.ajax({
                        url: '/app/exams/openwebcam?date_sheet=<?= $date_sheet_id ?>&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>,
                        type: 'GET',
                        success: function(data) {
                            $("#exam_data").html(data);
                        }
                    });
                }

                function startExam() {
                    $.ajax({
                        url: '/app/exams/start?date_sheet=<?= $date_sheet_id ?>&syllabus_id=<?= $syllabus_id ?>&id=' + <?= $_SESSION['ID'] ?>,
                        type: 'GET',
                        success: function(data) {
                            $("#exam_data").html(data);
                        }
                    });
                }

                function updateOverview() {
                    var checked = $('input[type=radio]:checked').size();
                    console.log(checked);
                }
            </script>

        <?php
            // $check = $conn->query("SELECT ID FROM Exam_Attempts WHERE Student_ID = " . $_SESSION['ID'] . " AND Date_Sheet_ID = " . $date_sheet_id . "");
            // if ($check->num_rows > 0) {
            //   echo '<script>startExam()</script>';
            // }
        } ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>