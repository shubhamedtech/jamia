<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); 
$disallowAccess = array('');
?>
<aside class="main-sidebar">
    <div class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?php print $breadcrumbs[1] == 'dashboard' ? 'open active' : '' ?>">
                <a href="/dashboard">
                    <i class="icon-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php print $breadcrumbs[2] == 'bank-details' ? 'open active' : '' ?>">
                <a href="/accounts/bank-details">
                    <i class="ti-agenda"></i> <span>Bank Details</span>
                    <small class="mb-0 ml-3 d-block">For Offline Payments</small>
                </a>
            </li>
            <!-- <li class="<?php print $breadcrumbs[2] == 'payment-gateways' ? 'open active' : '' ?>">
                <a href="/accounts/payment-gateways">
                    <i class="icon-wallet"></i> <span>Payment Gateway</span>
                    <small class="mb-0 ml-3 d-block">For Online Payments</small>

                </a>
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
                    <!-- <li class="<?php print $breadcrumbs[2] == 're-registrations' ? 'open active' : '' ?>">
                        <a href="/admissions/re-registrations"><i class="fa fa-angle-right"></i>Re-Registration</a>
                    </li> -->
                    <!-- <li class="<?php print $breadcrumbs[2] == 'back-papers' ? 'open active' : '' ?>">
                        <a href="/admissions/back-papers"><i class="fa fa-angle-right"></i>Back Paper</a>
                    </li> -->
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
                        <?php } }?>

                    </ul>
                </li>
            <?php } ?>
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