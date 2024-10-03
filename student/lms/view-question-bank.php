<?php require '../../includes/db-config.php';  ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    .copyright {
        background: #fff;
        padding: 15px 0;
        border-top: 1px solid #e0e0e0;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php
$id = $_GET['id'];
$sub_id = $_GET['sub_id'];
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
$course_id = $_SESSION['Sub_Course_ID'];
$student_id = $_SESSION['ID'];
$query = "SELECT question_banks.`id`, question_banks.`file_type`, question_banks.`file_path`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, question_banks.`status` FROM question_banks LEFT JOIN Courses ON Courses.ID = question_banks.course_id LEFT JOIN Subjects ON Subjects.ID = question_banks.subject_id WHERE question_banks.id=$id ";
$results = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($results);
?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

    <div class="content-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-header">
                    <b><?= $row['subject_name'] ?> Question Bank</b>
                    <div class="row pull-right">
                        <a class="btn btn-danger p-2 " href="/student/lms/subjects?id=<?= $sub_id; ?>" data-toggle="tooltip"> <i class="circle-arrow-left"></i>Back</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <embed src="<?= $base_url ?><?= $row['file_path'] ?>#toolbar=0&scrollbar=1&&navpanes=0&controls=0" type="application/pdf" width="100%" height="560px" />
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>