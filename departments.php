<?php include($_SERVER['DOCUMENT_ROOT'] . ('./header-top.php')) ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . ('./header-bottom.php')) ?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('./topbar.php')) ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('./admin-menu.php')) ?>

    <div class="content-wrapper">
        <div class="content-header sty-one">
                <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                for ($i = 1; $i <= count($breadcrumbs); $i++) {
                    if (count($breadcrumbs) == $i) : $active = "active";
                        $crumb = explode("?", $breadcrumbs[$i]);
                        echo '<h1 class="fw-bold">' . $crumb[0] . '</h1';
                    endif;
                }
                ?>
        </div>

        <div class="content">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-xs-12 m-b-3">
                    <div class="card">
                        <div class="card-body box-rounded box-gradient">
                            <span class="info-box-icon bg-transparent"><i class="icon-home text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Centers</h6>
                                <?php
                                $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'Center' ");
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
                            <span class="info-box-icon bg-transparent"><i class="icon-people text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Students</h6>
                                <?php
                                $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Students WHERE University_ID = " . $_SESSION['university_id'] . " ");
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
                        <div class="card-body box-rounded box-gradient-2">
                            <span class="info-box-icon bg-transparent"><i class="ti-book text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Programs</h6>
                                <?php
                                $all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Sub_Courses WHERE University_ID = " . $_SESSION['university_id'] . " ");
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
                        <div class="card-body box-rounded box-gradient-3">
                            <span class="info-box-icon bg-transparent"><i class="ti-money text-white"></i></span>
                            <div class="info-box-content">
                                <h6 class="info-box-text text-white">Total Revenue</h6>
                                <?php
                                $all_counts = $conn->query("SELECT SUM(Amount) as totalamount FROM Payments WHERE Status = 1 ");
                                $records = mysqli_fetch_assoc($all_counts);
                                $totalRecords = $records['totalamount'];
                                ?>
                                <h1 class="text-white">&#8377; <?= $totalRecords ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="card custom-card info-box">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Recently Added Centers</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Center Name</th>
                                            <th>Code</th>
                                            <th>Created AT</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $centers = $conn->query("SELECT * FROM Users WHERE Role = 'Center' ORDER BY Users.ID DESC LIMIT 10");
                                        if ($centers->num_rows > 0) {
                                            while ($row = $centers->fetch_assoc()) {
                                        ?>
                                                <tr>
                                                    <td><?= $row['Name'] ?></td>
                                                    <td><?= $row['Code'] ?></td>
                                                    <td><?= date('M d, Y',strtotime($row['Created_At'])) ?></td>
                                                    <td><?php if ($row['Status'] == 1) {  ?> <span class="badge badge-success">Active</span>
                                                        <?php  } else {  ?> <span class="badge badge-danger">Inactive</span>
                                                        <?php  } ?>
                                                    </td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card custom-card info-box">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Recently Added Students</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>DOB</th>
                                            <th>Created At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $centers = $conn->query("SELECT * FROM Students WHERE Students.University_ID = " . $_SESSION['university_id'] . " ORDER BY Students.ID DESC LIMIT 15");
                                        if ($centers->num_rows > 0) {
                                            while ($row = $centers->fetch_assoc()) {
                                        ?>
                                                <tr>
                                                    <td><?= $row['First_Name'] ?></td>
                                                    <td><?= $row['DOB'] ?></td>
                                                    <td><?= date('M d, Y',strtotime($row['Created_At'])) ?></td>
                                                    <td><?php if ($row['Status'] == 1) {  ?> <span class="badge badge-success">Active</span>
                                                        <?php  } else {  ?> <span class="badge badge-danger">Inactive</span>
                                                        <?php  } ?>
                                                    </td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('./footer-top.php')) ?>

    <?php include($_SERVER['DOCUMENT_ROOT'] . ('./footer-bottom.php')) ?>