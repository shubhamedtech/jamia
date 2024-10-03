<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    td {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->

    <div class="content-wrapper">
        <div class="content-header sty-one">
            <div class="d-flex align-items-center justify-content-between">

                <h1 class="text-capitalize d-inline fw-bold">Dashboard</h1>;
                <?php
                $new_notification = $conn->query("SELECT * FROM Notifications_Generated WHERE Status <> 1 AND Send_To = 'center' OR Send_To = '" . 'all' . "' ORDER BY Notifications_Generated.ID DESC LIMIT 1");
                $records = mysqli_fetch_assoc($new_notification);
                $record_count = array();
                $viewed_id = array();

                $viewed_notification = $conn->query("SELECT * FROM Notifications_Viewed_By WHERE Reader_ID =  " . $_SESSION['ID'] . " ORDER BY Notifications_Viewed_By.ID DESC LIMIT 1 ");

                if ($viewed_notification->num_rows > 0) {
                    $viewed_records = mysqli_fetch_assoc($viewed_notification);
                    $viewed_id = json_decode($viewed_records['Notification_ID']);
                }
                if (empty($records)) {
                    $record_count = '';
                } else if (in_array($records['ID'], $viewed_id)) {
                    $record_count = '';
                } else {
                    $record_count = 1;
                }
                ?>

                <div id="show-notification">
                    <?php if ($record_count != '') { ?>
                        <a href="#" onclick="show_notification('<?= $records['ID'] ?>')">
                            <div class="d-inline-block mr-5">
                                <i class="fa fa-bell-o" data-toggle="tooltip" title="<?php echo "One New Notification regarding " . $records['Heading']; ?>"></i>
                                <div class="notify">
                                    <span class="heartbit"></span> <span class="point"></span>
                                </div>
                            </div>
                        <?php } else {
                        echo '';
                    } ?>
                        </a>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12 m-b-3">
                    <div class="card">
                        <div class="card-body box-rounded box-gradient">
                            <span class="info-box-icon bg-transparent"><i class="icon-people text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Applications</h6>
                                <?php
                                $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " AND Added_For =  " . $_SESSION['ID'] . " ");
                                $records = mysqli_fetch_assoc($all_count);
                                $totalRecords = $records['allcount'];
                                ?>
                                <h1 class="text-white"><?= $totalRecords ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 m-b-3">
                    <div class="card">
                        <div class="card-body box-rounded box-gradient-4">
                            <span class="info-box-icon bg-transparent"><i class="ti-book text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Programs</h6>
                                <?php
                                // $all_count = $conn->query("SELECT COUNT(Sub_Courses.ID) as allcount FROM Sub_Courses LEFT JOIN Center_Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID WHERE Sub_Courses.University_ID = " . $_SESSION['university_id'] . " ");
                                // $records = mysqli_fetch_assoc($all_count);
                                // $totalRecords = $records['allcount'];
                                $result = $conn->query("SELECT COUNT(ID) AS total_courses FROM Courses");
                                $row = $result->fetch_assoc();
                                $totalCourses = $row['total_courses'];
                                // echo   $totalCourses;

                                ?>
                                <h1 class="text-white"><?= $totalCourses ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 m-b-3">
                    <div class="card">
                        <div class="card-body box-rounded box-gradient-2">
                            <span class="info-box-icon bg-transparent"><i class="icon-user-following text-white"></i></span>
                            <div class="info-box-content">
                                <?php
                                $counsellor = $conn->query("SELECT Alloted_Center_To_Counsellor.Counsellor_ID as counsellor,  University_User.Reporting as head FROM Alloted_Center_To_Counsellor LEFT JOIN University_User ON University_User.User_ID = Alloted_Center_To_Counsellor.Counsellor_ID WHERE Code =  " . $_SESSION['ID'] . " ");
                                $records = mysqli_fetch_assoc($counsellor);
                                $totalRecords = $records['head'];
                                $Head = $conn->query("SELECT * FROM Users WHERE ID = " . $totalRecords . " ");
                                $university_head = mysqli_fetch_assoc($Head);
                                ?>
                                <h3 class="text-white">Board Head</h3>
                                <p class="text-white mb-0">Name: <?= $university_head['Name'] ?></p>
                                <p class="text-white mb-0">Code: <?= $university_head['Code'] ?></p>
                                <p class="text-white mb-0">Email: <?= $university_head['Email'] ?></p>
                                <p class="text-white mb-0">Phone: <?= $university_head['Mobile'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-xs-12 m-b-3">
                    <div class="card">
                        <div class="card-body box-rounded box-gradient-3">
                            <span class="info-box-icon bg-transparent"><i class="icon-user text-white"></i></span>
                            <div class="info-box-content">
                                <?php
                                $counsellor = $conn->query("SELECT Alloted_Center_To_Counsellor.Counsellor_ID as counsellor,  University_User.Reporting as head FROM Alloted_Center_To_Counsellor LEFT JOIN University_User ON University_User.User_ID = Alloted_Center_To_Counsellor.Counsellor_ID WHERE Code =  " . $_SESSION['ID'] . " ");

                                $records = mysqli_fetch_assoc($counsellor);
                                $totalRecords = $records['counsellor'];
                                $Counsellor = $conn->query("SELECT * FROM Users WHERE ID = " . $totalRecords . " ");
                                $university_Counsellor = mysqli_fetch_assoc($Counsellor);
                                ?>
                                <h3 class="text-white"><?= $university_Counsellor['Role'] ?></h3>
                                <p class="text-white mb-0">Name: <?= $university_Counsellor['Name'] ?></p>
                                <p class="text-white mb-0">Code: <?= $university_Counsellor['Code'] ?></p>
                                <p class="text-white mb-0">Email: <?= $university_Counsellor['Email'] ?></p>
                                <p class="text-white mb-0">Phone: <?= $university_Counsellor['Mobile'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card custom-card info-box">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Notifications</h6>
                            </div>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-bordered text-nowrap mb-0 overflow-auto">
                                    <thead>
                                        <tr>
                                            <th>Regarding</th>
                                            <th>Content</th>
                                            <th>Sent To</th>
                                            <th>Date</th>
                                            <th>Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE Send_To = 'center' OR Send_To = 'all' ORDER BY Noticefication_Created_on DESC");
                                        if ($result_record && $result_record->num_rows > 0) {
                                            while ($row = $result_record->fetch_assoc()) { ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Heading']) ?></td>
                                                    <td><button type="button" class="btn btn-link py-0" onclick="view_content('<?= htmlspecialchars($row['ID']) ?>');"><i class="fa fa-eye"></i></button></td>
                                                    <td><?= htmlspecialchars($row['Send_To']) ?></td>
                                                    <td><?= date('d-m-Y', strtotime($row['Noticefication_Created_on'])) ?></td>
                                                    <td>
                                                        <?php if (!empty($row['Attachment'])) { ?>
                                                            <a href="<?= htmlspecialchars($row['Attachment']) ?>" target="_blank" download="<?= htmlspecialchars($row['Heading']) ?>">Download</a>
                                                        <?php } else { ?>
                                                            <p>No Attachment</p>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5">
                                                    <h5>No Notifications</h5>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card custom-card info-box">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Recently Added Students</h6>
                            </div>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-bordered text-nowrap mb-0 overflow-auto">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Student Code</th>
                                            <th>DOB</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $conn->prepare("SELECT First_Name, Unique_ID, DOB, Created_At, Status FROM Students WHERE University_ID = ? AND Added_For = ?");
                                        $stmt->bind_param("ii", $_SESSION['university_id'], $_SESSION['ID']);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                        ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['First_Name']) ?></td>
                                                    <td><?= htmlspecialchars($row['Unique_ID']) ?></td>
                                                    <td><?= date('d-m-Y', strtotime($row['DOB'])) ?></td>
                                                    <td><?= date('d-m-Y H:i:s', strtotime($row['Created_At'])) ?></td>
                                                    <td>
                                                        <?php if ($row['Status'] == 1) { ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php } else { ?>
                                                            <span class="badge badge-danger">Inactive</span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No students found.</td></tr>"; 
                                        }
                                        $stmt->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/footer-top.php')) ?>
    <script type="text/javascript">
        function view_content(id) {
            $.ajax({
                url: '/app/notifications/contents?id=' + id,
                type: 'GET',
                success: function(data) {
                    $("#md-modal-content").html(data);
                    $("#mdmodal").modal('show');
                }
            })
        }

        function show_notification(id) {
            $.ajax({
                url: '/app/notifications/current-notification?id=' + id,
                type: 'GET',
                success: function(data) {
                    $("#md-modal-content").html(data);
                    $("#mdmodal").modal('show');
                }
            })
        }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/footer-bottom.php')) ?>