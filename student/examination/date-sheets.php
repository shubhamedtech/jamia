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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Paper Name</th>
                                                    <th>Username</th>
                                                    <th>Password</th>
                                                    <th>Login URL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $codes = $conn->query("SELECT Name FROM Student_Subjects LEFT JOIN Subjects ON Student_Subjects.Subject_ID = Subjects.ID WHERE Student_ID = " . $_SESSION['ID']);
                                                while ($code = $codes->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?= $code['Name'] ?></td>
                                                        <td><?= $_SESSION['Unique_ID'] ?></td>
                                                        <td><?= date("d-M-Y", strtotime($_SESSION['DOB'])) ?></td>
                                                        <td><a href="https://jua.exam-portal.in/auth/student" target="_blank">Click Here</a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>