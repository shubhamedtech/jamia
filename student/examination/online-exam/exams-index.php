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
                <div class="card-header separator">
                    <h5>Date Sheets</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <?php
                                $syllabus_ids = array();
                                $codes = $conn->query("SELECT ID FROM Syllabi WHERE Course_ID = " . $_SESSION['Course_ID'] . " AND Sub_Course_ID = " . $_SESSION['Sub_Course_ID'] . " AND Semester = " . $_SESSION['Duration'] . "");
                                if ($codes->num_rows > 0) {
                                    while ($row = $codes->fetch_assoc()) {
                                        $syllabus_ids[] = $row['ID'];
                                    }

                                    $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session, Syllabi.Name, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Syllabus_ID IN (" . implode(",", $syllabus_ids) . ") ORDER BY Exam_Date ASC");
                                    if ($date_sheets->num_rows == 0) {
                                        echo '<center><h1>Date Sheet Not Available</h1></center>';
                                    } else {
                                ?>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Paper Code</th>
                                                        <th>Paper Name</th>
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <!-- <th>Exam</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($date_sheet = $date_sheets->fetch_assoc()) { ?>
                                                        <tr>
                                                            <td><?= $date_sheet['Code'] ?></td>
                                                            <td><?= $date_sheet['Name'] ?></td>
                                                            <td><?= date("l, dS M, Y", strtotime($date_sheet['Exam_Date'])) ?></td>
                                                            <td><?= date("h:i A", strtotime($date_sheet['Start_Time'])) . " to " . date("h:i A", strtotime($date_sheet['End_Time'])) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                <?php }
                                } else {
                                    // No Date Sheet Available
                                    echo '<center><h1>Date Sheet Not Available</h1></center>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-header separator">
                                <h5>Today's Exams</h5>
                            </div>
                            <div class="card">
                                <?php
                                $syllabus_ids = array();
                                $codes = $conn->query("SELECT ID FROM Syllabi WHERE Course_ID = " . $_SESSION['Course_ID'] . " AND Sub_Course_ID = " . $_SESSION['Sub_Course_ID'] . " AND Semester = " . $_SESSION['Duration'] . "");
                                if ($codes->num_rows > 0) {
                                    while ($row = $codes->fetch_assoc()) {
                                        $syllabus_ids[] = $row['ID'];
                                    }

                                    $date_sheets = $conn->query("SELECT Date_Sheets.*, Exam_Sessions.Name as Exam_Session,Syllabi.ID as Syllab_id, Syllabi.Name, Syllabi.Code FROM Date_Sheets LEFT JOIN Syllabi ON Date_Sheets.Syllabus_ID = Syllabi.ID LEFT JOIN Exam_Sessions ON Date_Sheets.Exam_Session_ID = Exam_Sessions.ID WHERE Syllabus_ID IN (" . implode(",", $syllabus_ids) . ") AND Exam_Date = '" . date("Y-m-d") . "' ORDER BY Exam_Date ASC");
                                    if ($date_sheets->num_rows == 0) {
                                        echo '<div class="card-body"><h4 class="fw-bold text-center">NO Exam Scheduled for Today</h4></div>';
                                    } else {
                                ?>
                                        <div class="card-body table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <!-- <th>Exam Session</th> -->
                                                        <th>Paper Code</th>
                                                        <th>Paper Name</th>
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <th>Action</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($date_sheet = $date_sheets->fetch_assoc()) {
                                                        $status = '';
                                                        $action = '';
                                                        if (date('H:i') <= date("H:i", strtotime($date_sheet['End_Time'])) && date('H:i') >= date("H:i", strtotime($date_sheet['Start_Time']))) {
                                                            $action = "Start";
                                                            $status = 'Started';
                                                        } else if (date('H:i') <= date("H:i", strtotime($date_sheet['Start_Time']))) {
                                                            $action = "Starting Soon";
                                                            $status = 'Not started yet';
                                                        } else if (date('H:i') >= date("H:i", strtotime($date_sheet['End_Time']))) {
                                                            $action = "Finished";
                                                            $status = 'Completed';
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td><?= $date_sheet['Code'] ?></td>
                                                            <td><?= $date_sheet['Name'] ?></td>
                                                            <td><?= date("l, dS M, Y", strtotime($date_sheet['Exam_Date'])) ?></td>
                                                            <td><?= date("h:i A", strtotime($date_sheet['Start_Time'])) . " to " . date("h:i A", strtotime($date_sheet['End_Time'])) ?></td>
                                                            <td>
                                                                <?php if ($action == "Start") {
                                                                    $_SESSION['Today_Exam_ID'] = $date_sheet['Exam_Session_ID'];
                                                                    $check_web_pic = $conn->query("SELECT * FROM Exam_Students_Final_Submit WHERE Student_ID = " . $_SESSION['ID'] . " AND 	Syllabus_ID = " . $date_sheet['Syllab_id'] . " AND Date_Sheet_ID = " . $date_sheet['ID'] . "");
                                                                    if ($check_web_pic->num_rows > 0) {
                                                                        $action = "Submited";
                                                                    }
                                                                    if ($action == "Submited") {
                                                                ?>
                                                                        <button class="btn btn-disabled" disabled><span class="title"><?= $action ?></span></button>
                                                                    <?php } else { ?>
                                                                        <a class="btn btn-success" href="/student/examination/online-exam/start-exams"><span class="title"><?= $action ?></span></a>
                                                                    <?php }
                                                                } else { ?>
                                                                    <button class="btn btn-disabled" disabled><span class="title"><?= $action ?></span></button>
                                                                <?php } ?>
                                                            </td>
                                                            <td><?= $status ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                <?php }
                                } else {
                                    // No Date Sheet Available
                                    echo '<center><h1>Date Sheet Not Available</h1></center>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>