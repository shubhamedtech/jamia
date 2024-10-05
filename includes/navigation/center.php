<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$disallowAccess = array('');
?>
<aside class="main-sidebar">
    <div class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?php print $breadcrumbs[1] == 'dashboard' ? 'open active' : '' ?>">
                <a href="/dashboards/center-dashboard">
                    <i class="icon-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <?php if ($_SESSION['crm'] != 0) { ?>
                <li class="treeview <?php print $breadcrumbs[1] == 'leads' ? 'open active' : '' ?>">
                    <a href="#">
                        <i class="icon-graduation"></i> <span>Leads</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php print $breadcrumbs[2] == 'generate' ? 'open active' : '' ?>">
                            <a href="/leads/generate"><i class="fa fa-angle-right"></i>Generate</a>
                        </li>
                        <li class="<?php print $breadcrumbs[2] == 'lists' ? 'open active' : '' ?>">
                            <a href="/leads/lists"><i class="fa fa-angle-right"></i>Leads</a>
                        </li>
                        <li class="<?php print $breadcrumbs[2] == 'follow-ups' ? 'open active' : '' ?>">
                            <a href="/leads/follow-ups"><i class="fa fa-angle-right"></i>Follow-Ups</a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
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
             $check = $conn->query("SELECT ID FROM Users WHERE Role = 'Center' AND CanCreateSubCenter=1 AND ID=".$_SESSION['ID']);
            if($check->num_rows>0){
                $none = 'display:block';
            }else{
                $none = 'display:none';
            }
            
            $pages = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID AND Page_Access.University_ID = " . $_SESSION['university_id'] . " WHERE Pages.`Type` = 'Accounts' AND Page_Access.Inhouse = 1");
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
                        <li class="<?php print $breadcrumbs[2] == 'sub-center-ledgers' ? 'open active' : '' ?>" style="<?= $none  ?>">
                            <a href="/accounts/sub-center-ledgers" class="active"><i class="fa fa-angle-right"></i>Sub-Center-Ledgers</a>
                        </li>
                    </ul>
                </li>
            <?php } ?>

            <li class="<?php print $breadcrumbs[2] == 'notifications' ? 'open active' : '' ?>">
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
                <a href="/center/notifications">
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

            <?php
            $pages = $conn->query("SELECT Pages.ID, Pages.Name, Pages.Slug FROM Pages LEFT JOIN Page_Access ON Pages.ID = Page_Access.Page_ID WHERE Pages.`Type` = 'Download' GROUP BY Pages.Name");
            if ($pages->num_rows > 0) {
            ?>
                <li class="treeview <?php print $breadcrumbs[1] == 'downloads' ? 'open active' : '' ?>">
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
                </li>
            <?php } ?>
            <?php if ($_SESSION['CanCreateSubCenter'] == 1) { ?>
                <li class="treeview <?php print $breadcrumbs[1] == 'users' ? 'open active' : '' ?>">
                    <a href="javascript:void(0);">
                        <i class="icon-people"></i> <span>Users</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li class="<?php print $breadcrumbs[2] == 'sub-centers' ? 'open active' : '' ?>">
                            <a href="/users/sub-centers" class="active"><i class="fa fa-angle-right"></i>Sub Centers</a>
                        </li>
                    </ul>
                </li>
            <?php } ?>
            <li class="">
                <a href="#">
                    <i class="icon-phone"></i> <span>Support</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>
            <li class="">
                <a href="/lms-settings/application_correction">
                    <i class="icon-phone"></i> <span>Application Correction</span>
                </a>
            </li>
        </ul>
    </div>
</aside>