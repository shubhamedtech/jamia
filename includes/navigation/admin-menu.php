<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$disallowAccess = array('');

?>
<aside class="main-sidebar">
    <div class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?php print $breadcrumbs[2] == 'admin-dashboard' ? 'open active' : '' ?>">
                <a href="/dashboards/admin-dashboard">
                    <i class="icon-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview <?php print $breadcrumbs[1] == 'academics' ? 'open active' : '' ?>">
                <a href="javascript:void(0);">
                    <i class="icon-graduation"></i> <span>Academics</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li>
                        <a href="/academics/boards"><i class="fa fa-angle-right"></i>Boards</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'departments' ? 'open active' : '' ?>">
                        <a href="/academics/departments"><i class="fa fa-angle-right"></i>Departments</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'programs' ? 'open active' : '' ?>">
                        <a href="/academics/programs"><i class="fa fa-angle-right"></i>Programs</a>
                    </li>
                    <!-- <li class="<?php print $breadcrumbs[2] == 'specializations' ? 'open active' : '' ?>">
                        <a href="/academics/specializations"><i class="fa fa-angle-right"></i>Specializations</a>
                    </li> -->
                    <li class="<?php print $breadcrumbs[2] == 'specializations' ? 'open active' : '' ?>">
                        <a href="/academics/subjects"><i class="fa fa-angle-right"></i>Subjects</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'syllabus' ? 'open active' : '' ?>">
                        <a href="/academics/syllabus"><i class="fa fa-angle-right"></i>Syllabus</a>
                    </li>
                </ul>
            </li>
            <!-- <li class="treeview <?php print $breadcrumbs[1] == 'exam-students' ? 'open active' : '' ?>">
                <a href="#">
                    <i class="icon-user"></i> <span>Exam Students</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php print $breadcrumbs[2] == 'student-list' ? 'open active' : '' ?>">
                        <a href="/exam-students/student-list"><i class="fa fa-angle-right"></i>Student List</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'add-student' ? 'open active' : '' ?>">
                        <a href="/exam-students/add-student"><i class="fa fa-angle-right"></i>Add New Student</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'view-results' ? 'open active' : '' ?>">
                        <a href="/exam-students/view-results"><i class="fa fa-angle-right"></i>View Results</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'add-date-sheet' ? 'open active' : '' ?>">
                        <a href="/exam-students/add-date-sheet"><i class="fa fa-angle-right"></i>Add Datesheet</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'exams' ? 'open active' : '' ?>">
                        <a href="/exam-students/exams"><i class="fa fa-angle-right"></i>Exam</a>
                    </li>
                </ul>
            </li> -->
            <li class="treeview <?php print $breadcrumbs[1] == 'admissions' ? 'open active' : '' ?>">
                <a href="#">
                    <i class="icon-doc"></i> <span>Admissions</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php print $breadcrumbs[2] == 'applications' ? 'open active' : '' ?>">
                        <a href="/admissions/applications" class="active"><i class="fa fa-angle-right"></i>Applications</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'application-form' ? 'open active' : '' ?>">
                        <a href="/admissions/application-form"><i class="fa fa-angle-right"></i>Apply Fresh</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 're-registrations' ? 'open active' : '' ?>">
                        <a href="/admissions/re-registrations"><i class="fa fa-angle-right"></i>Re-Registration</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'back-papers' ? 'open active' : '' ?>">
                        <a href="/admissions/back-papers"><i class="fa fa-angle-right"></i>Back Paper</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'results' ? 'open active' : '' ?>">
                        <a href="/admissions/results"><i class="fa fa-angle-right"></i>Results</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'exam-schedules' ? 'open active' : '' ?>">
                        <a href="/admissions/exam-schedules"><i class="fa fa-angle-right"></i>Exam Schedule</a>
                    </li>
                </ul>
            </li>
            <?php
            $pages = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID WHERE Pages.`Type` = 'Accounts' GROUP BY Pages.Name");
            if ($pages->num_rows > 0) {
            ?>
                <li class="treeview <?php print $breadcrumbs[1] == 'accounts' ? 'open active' : '' ?>">
                    <a href="#">
                        <i class="icon-folder-alt"></i> <span>Accounts</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php while ($page = $pages->fetch_assoc()) {
                            if (!in_array($page["Name"], $disallowAccess)) {
                        ?>
                                <li class="<?php print $breadcrumbs[2] == $page['Slug'] ? 'open active' : '' ?>">
                                    <a href="/accounts/<?= $page['Slug'] ?>"><i class="fa fa-angle-right"></i><?= $page['Name'] ?></a>
                                </li>
                        <?php }
                        } ?>

                    </ul>
                </li>
            <?php } ?>
            <?php
            $pages = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID WHERE Pages.`Type` = 'Download' GROUP BY Pages.Name");
            if ($pages->num_rows > 0) {
            ?>
               <!-- <li class="treeview <?php print $breadcrumbs[1] == 'downloads' ? 'open active' : '' ?>">
                    <a href="#">
                        <i class="ti-save"></i> <span>Download</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php while ($page = $pages->fetch_assoc()) { ?>
                            <li class="<?php print $breadcrumbs[2] == $page['Slug'] ? 'open active' : '' ?>">
                                <a href="/downloads/<?= $page['Slug'] ?>"><i class="fa fa-angle-right"></i><?= $page['Name'] ?></a>
                            </li>
                        <?php } ?>

                    </ul>
                </li> -->
            <?php } ?>

            <li class="treeview <?php print $breadcrumbs[1] == 'users' ? 'open active' : '' ?>">
                <a href="#">
                    <i class="icon-people"></i> <span>Users</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php print $breadcrumbs[2] == 'borad-managers' ? 'open active' : '' ?>">
                        <a href="/users/board-managers"><i class="fa fa-angle-right"></i>Board Managers</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'operations' ? 'open active' : '' ?>">
                        <a href="/users/operations"><i class="fa fa-angle-right"></i>Operations</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'counsellors' ? 'open active' : '' ?>">
                        <a href="/users/counsellors"><i class="fa fa-angle-right"></i>Counsellor</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'sub-counsellors' ? 'open active' : '' ?>">
                        <a href="/users/sub-counsellors"><i class="fa fa-angle-right"></i>Sub-Counsellor</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'center-master' ? 'open active' : '' ?>">
                        <a href="/users/center-master"><i class="fa fa-angle-right"></i>Center Masters</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'centers' ? 'open active' : '' ?>">
                        <a href="/users/centers"><i class="fa fa-angle-right"></i>Centers</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'sub-centers' ? 'open active' : '' ?>">
                        <a href="/users/sub-centers"><i class="fa fa-angle-right"></i>Sub-Centers</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'accountants' ? 'open active' : '' ?>">
                        <a href="/users/accountants"><i class="fa fa-angle-right"></i>Accountants</a>
                    </li>
                </ul>
            </li>
            <li class="<?php print $breadcrumbs[1] == 'settings' ? 'open active' : '' ?>">
                <a href="/settings/admission"><i class="icon-settings"></i> <span>Settings</span></a>
            </li>
            <li class="<?php print $breadcrumbs[1] == 'notifications' ? 'open active' : '' ?>">
                <a href="/notifications"><i class="icon-bell"></i> <span>Notifications</span></a>
            </li>
            <li class="treeview <?php print $breadcrumbs[1] == 'lms-settings' ? 'open active' : '' ?>">
                <a href="#">
                    <i class="icon-docs"></i> <span>LMS Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="<?php print $breadcrumbs[2] == 'subjects' ? 'open active' : '' ?>">
                        <a href="/lms-settings/subjects"><i class="fa fa-angle-right"></i>Subjects</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'datesheets' ? 'open active' : '' ?>">
                        <a href="/lms-settings/datesheets"><i class="fa fa-angle-right"></i>Date Sheets</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'assignments' ? 'open active' : '' ?>">
                        <a href="/lms-settings/assignments"><i class="fa fa-angle-right"></i>Assignments</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'new_assignments' ? 'open active' : '' ?>">
                        <a href="/lms-settings/assignments-review"><i class="fa fa-angle-right"></i>Assignments Review</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'practicals' ? 'open active' : '' ?>">
                        <a href="/lms-settings/practicals"><i class="fa fa-angle-right"></i>Practicals</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'mock-tests' ? 'open active' : '' ?>">
                        <a href="/lms-settings/mock-tests"><i class="fa fa-angle-right"></i>Mock Test</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'exams' ? 'open active' : '' ?>">
                        <a href="/lms-settings/exams"><i class="fa fa-angle-right"></i>Exam</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'result' ? 'open active' : '' ?>">
                        <a href="/lms-settings/result"><i class="fa fa-angle-right"></i>Results</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'queries-&-feedback' ? 'open active' : '' ?>">
                        <a href="/lms-settings/queries-&-feedback"><i class="fa fa-angle-right"></i>Queries & Feedback</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'e-books' ? 'open active' : '' ?>">
                        <a href="/lms-settings/e-books"><i class="fa fa-angle-right"></i>E-Books</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'videos' ? 'open active' : '' ?>">
                        <a href="/lms-settings/videos"><i class="fa fa-angle-right"></i>Videos</a>
                    </li>
                     <li class="<?php print $breadcrumbs[2] == 'question-bank' ? 'open active' : '' ?>">
                        <a href="/lms-settings/question-bank"><i class="fa fa-angle-right"></i>Question Banks</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'dispatch' ? 'open active' : '' ?>">
                        <a href="/lms-settings/dispatch"><i class="fa fa-angle-right"></i>Dispatch</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'documents' ? 'open active' : '' ?>">
                        <a href="/lms-settings/documents"><i class="fa fa-angle-right"></i>Documents</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'contact-us' ? 'open active' : '' ?>">
                        <a href="/lms-settings/contact-us"><i class="fa fa-angle-right"></i>Contact Us</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'download_center' ? 'open active' : '' ?>">
                        <a href="/lms-settings/download_center"><i class="fa fa-angle-right"></i>Download Center</a>
                    </li>
                    <li class="<?php print $breadcrumbs[2] == 'application_correction' ? 'open active' : '' ?>">
                        <a href="/lms-settings/application_correction"><i class="fa fa-angle-right"></i>Application Correction</a>
                    </li>
                </ul>
            </li>
            <!-- <li class="">
                <a href="#">
                    <i class="icon-phone"></i> <span>Support</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li> -->
        </ul>
    </div>
</aside>