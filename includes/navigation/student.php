<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); ?>
<aside class="main-sidebar">
    <div class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?php print $breadcrumbs[1] == 'dashboard' ? 'open active' : '' ?>">
                <a href="/dashboard">
                    <i class="icon-home"></i> <span>Dashboard</span>
                </a>
            </li>

            <?php if (in_array('Profile', $_SESSION['LMS_Permissions'])) { ?>
                <li class="<?php print $breadcrumbs[2] == 'profile' ? 'open active' : '' ?>">
                    <a href="/student/profile">
                        <i class="icon-user"></i> <span>My Profile</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (in_array('Notifications', $_SESSION['LMS_Permissions'])) { ?>
                <li class="<?php print $breadcrumbs[2] == 'notifications' ? 'open active' : '' ?>">
                    <?php
                    $new_notification = $conn->query("SELECT * FROM Notifications_Generated WHERE Status <> 1 AND Send_To = 'student' OR Send_To = '" . 'all' . "' ORDER BY Notifications_Generated.ID DESC LIMIT 1");
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
                    <a href="/student/notifications">
                        <?php if ($record_count != '') { ?>
                            <div class="d-inline-block">
                                <i class="fa fa-bell-o"></i>
                                <div class="notify">
                                    <span class="heartbit"></span> <span class="point"></span>
                                </div>
                            </div>
                        <?php } else { ?>
                            <i class="icon-bell"></i>
                        <?php } ?>
                        <span>Notifications</span>
                        <small class="mb-0 ml-3 d-block text-info">
                            <?php if ($record_count != '') {
                                echo $record_count . " New Notification";
                            } ?>
                        </small>
                    </a>
                </li>
            <?php } ?>

            <?php if (in_array('Syllabus', $_SESSION['LMS_Permissions'])) { ?>
                <li class="<?php print $breadcrumbs[2] == 'syllabus' ? 'open active' : '' ?>">
                    <a href="/student/syllabus">
                        <i class="icon-book-open"></i> <span>My Syllabus</span>
                    </a>
                </li>
            <?php } ?>

            <?php if (in_array('ID Card', $_SESSION['LMS_Permissions'])) { ?>
                <li class="<?php print $breadcrumbs[2] == 'id-card' ? 'open active' : '' ?>">
                    <a href="/student/id-card">
                        <i class="ti-id-badge"></i> <span>ID Card</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (!in_array('Download Center', $_SESSION['LMS_Permissions'])) { ?>
                <li class="<?php print $breadcrumbs[2] == 'Download Center' ? 'open active' : '' ?>">
                    <a href="/student/download_center">
                        <i class="ti-id-badge"></i> <span>Download Center</span>
                    </a>
                </li>
            <?php } ?>

            <?php
            if (
                in_array('E-Books', $_SESSION['LMS_Permissions']) ||
                in_array('Assignments', $_SESSION['LMS_Permissions']) ||
                in_array('Practicals', $_SESSION['LMS_Permissions']) ||
                in_array('Projects', $_SESSION['LMS_Permissions']) ||
                in_array('Work Books', $_SESSION['LMS_Permissions']) ||
                in_array('Videos', $_SESSION['LMS_Permissions'])
            ) {
            ?>
               <li class="<?php print $breadcrumbs[2] == 'lms' ? 'open active' : '' ?>">
                    <a href="/student/lms/lms">
                        <i class="icon-graduation"></i> <span>LMS</span>
                    </a>
                </li>
                <li class="treeview <?php print array_key_exists(2, $breadcrumbs) && $breadcrumbs[2] == 'lmsss' ? 'open active' : '' ?>">
                    <a href="#">
                        <i class="icon-graduation"></i> <span>Assessments</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (in_array('Assignments', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'assignments' ? 'open active' : '' ?>">
                                <a href="/student/lms/assignments"><i class="fa fa-angle-right"></i>Assignments</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('Practicals', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'practicals' ? 'open active' : '' ?>">
                                <a href="/student/lms/practicals"><i class="fa fa-angle-right"></i>Practicals</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('Projects', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'projects' ? 'open active' : '' ?>">
                                <a href="/student/lms/projects"><i class="fa fa-angle-right"></i>Projects</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('Work Books', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'work-books' ? 'open active' : '' ?>">
                                <a href="/student/lms/work-books"><i class="fa fa-angle-right"></i>Work-Books</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <?php
            if (
                in_array('Date Sheets', $_SESSION['LMS_Permissions']) ||
                in_array('Admit Card', $_SESSION['LMS_Permissions']) ||
                in_array('Mock Tests', $_SESSION['LMS_Permissions']) ||
                in_array('Exams', $_SESSION['LMS_Permissions']) ||
                in_array('Results', $_SESSION['LMS_Permissions'])
            ) {
            ?>
                <li class="treeview <?php print array_key_exists(2, $breadcrumbs) && $breadcrumbs[2] == 'examination' ? 'open active' : '' ?>">
                    <a href="#">
                        <i class="icon-graduation"></i> <span>Examination</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (in_array('Date Sheets', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'date-sheets' ? 'open active' : '' ?>">
                                <a href="/student/examination/date-sheets"><i class="fa fa-angle-right"></i>Date Sheet</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('Admit Card', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'admit-card' ? 'open active' : '' ?>">
                                <a href="/student/examination/admit-card"><i class="fa fa-angle-right"></i>Admit Cards</a>
                            </li>
                        <?php } ?>
                        <?php if (
                            in_array('Mock Tests', $_SESSION['LMS_Permissions']) ||
                            in_array('Exams', $_SESSION['LMS_Permissions'])
                        ) {
                        ?>
                            <li class="treeview <?php print array_key_exists(3, $breadcrumbs) && $breadcrumbs[3] == 'online-exam' ? 'open active' : '' ?>">
                                <a href="/academics/specializations"><i class="fa fa-angle-right"></i>Online Exam
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span></a>
                                <ul class="treeview-menu">
                                    <?php if (in_array('Mock Tests', $_SESSION['LMS_Permissions'])) { ?>
                                        <li class="<?php print $breadcrumbs[4] == 'mock-tests' ? 'open active' : '' ?>">
                                            <a href="/student/examination/online-exam/mock-tests"><i class="fa fa-angle-right"></i>Mock Test</a>
                                        </li>
                                    <?php } ?>
                                    <?php if (in_array('Exams', $_SESSION['LMS_Permissions'])) { ?>
                                        <li class="<?php print $breadcrumbs[4] == 'specializations' ? 'open active' : '' ?>">
                                            <a href="/student/examination/online-exam/exams-index"><i class="fa fa-angle-right"></i>Exam</a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                        <?php if (in_array('Results', $_SESSION['LMS_Permissions'])) { ?>
                            <li class="<?php print $breadcrumbs[3] == 'departments' ? 'open active' : '' ?>">
                                <a href="/student/examination/results"><i class="fa fa-angle-right"></i>Results</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </div>
</aside>