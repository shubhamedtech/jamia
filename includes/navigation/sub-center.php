<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); ?>
<aside class="main-sidebar">
  <div class="sidebar">
    <ul class="sidebar-menu" data-widget="tree">
      <li class="<?php print $breadcrumbs[2] == 'admin-dashboard' ? 'open active' : '' ?>">
        <a href="/dashboard">
          <i class="icon-home"></i> <span>Dashboard</span>
        </a>
      </li>
      <?php if ($_SESSION['crm'] != 0) { ?>
        <li class="treeview <?php print $breadcrumbs[1] == 'leads' ? 'open active' : '' ?>">
          <a href="javascript:void(0);">
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
            <?php while ($page = $pages->fetch_assoc()) { ?>

              <li class="<?php print $breadcrumbs[2] == $page['Slug'] ? 'open active' : '' ?>">
                <a href="/accounts/<?= $page['Slug'] ?>"><i class="fa fa-angle-right"></i><?= $page['Name'] ?></a>
              </li>
            <?php } ?>

          </ul>
        </li>
      <?php } ?>
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
    </ul>
  </div>
</aside>