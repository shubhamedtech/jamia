<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
$disallowAccess = array('');
?>
<aside class="main-sidebar">
    <div class="sidebar">
        <ul class="sidebar-menu" data-widget="tree">
            <li class="<?php print $breadcrumbs[2] == 'User_dashboard' ? 'open active' : '' ?>">
                <a href="/dashboards/User_dashboard">
                    <i class="icon-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/board/boards_record"><i class="fa fa-angle-right"></i>Students Record</a>
            </li>
        </ul>
    </div>
</aside>