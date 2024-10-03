<?php require '../../includes/db-config.php';  ?>

<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/css/tabs.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/new-style.css" />
<link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .stu-e-book-style {
        width: 300px;
        height: 150px;
        align-items: center;
        text-align: center;
        border-radius: 10px;
        background-color: #F3B95F;
    }

    .e-book-icon {
        margin-top: 15px;
        font-size: 80px;
        text-align: center;
        color: white;
    }

    .subject_name {
        font-size: 18px !important;
        font-weight: 600;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

    <div class="content-wrapper">
        <div class="content-header sty-one">
            <div class="d-flex align-items-center justify-content-between">

                <?php
                $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
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
                <!-- <div class="card-header">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group form-group-default required">
                                <label>Semester</label>
                                <select class="form-control" id="semester" onchange="getTable()">
                                    <option value="">Choose</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="card-body">
                    <div class="row">
                        <?php
                        $getSubjectQuery = $conn->query("SELECT Subjects.ID as subject_id,Subjects.Name,Program_Grade_ID FROM Subjects WHERE ID IN (SELECT Subject_id FROM `Student_Subjects` WHERE Student_Id = " . $_SESSION['ID'] . ")");
                        if ($getSubjectQuery->num_rows == 0) {  ?>
                            <div class="col-md-12" id="student_e_books_not">
                                <p class="text-center" style="font-weight: 600;">Please Select Semesters</p>
                            </div>
                            <?php } else {
                            while ($subResArr = $getSubjectQuery->fetch_assoc()) { ?>
                                <div class="col-md-3">
                                    <div class="card info-box p-0">
                                        <a href="/student/lms/subjects?id=<?= $subResArr['subject_id'] ?>&type=1">
                                            <div class="card-img-top">
                                                <p class="subject-name"><?= $subResArr['Name'];  ?></p>
                                            </div>
                                        </a>
                                        <div class="card-footer">
                                            <div class="row justify-content-between align-items-center">
                                                <?php
                                                $ebook_query = $conn->query("SELECT id FROM e_books WHERE subject_id = '" . $subResArr['subject_id'] . "' AND course_id='" . $subResArr['Program_Grade_ID'] . "' and Status = 1");
                                                $ebook_count = ($ebook_query->num_rows > 0) ? $ebook_query->num_rows : 0;

                                                $video_query = $conn->query("SELECT id FROM video_lectures WHERE subject_id = '" . $subResArr['subject_id'] . "' AND course_id='" . $subResArr['Program_Grade_ID'] . "' and Status = 1");
                                                $video_count = ($video_query->num_rows > 0) ? $video_query->num_rows : 0;
                                                                                  
                                                 $question_bank_query = $conn->query("SELECT id FROM question_banks WHERE subject_id = '" . $subResArr['subject_id'] . "' AND course_id='" . $subResArr['Program_Grade_ID'] . "' and Status = 1");
                                                 $question_bank_count = ($question_bank_query->num_rows > 0) ? $question_bank_query->num_rows : 0;                                 
                                                ?>
                                                <div class="col-md-4 text-center">
                                                    <a href="/student/lms/subjects?id=<?= $subResArr['subject_id'] ?>&type=1"><i class="ti-book mr-2"></i><span><?= $ebook_count; ?></span></a>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <a href="/student/lms/subjects?id=<?= $subResArr['subject_id'] ?>&type=2"><i class="ti- ti-video-clapper mr-2"></i><span><?= $video_count; ?></span></a>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <a href="/student/lms/subjects?id=<?= $subResArr['subject_id'] ?>&type=4"><i class=" ti-book mr-2"></i><span><?= $question_bank_count; ?></span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                        <div class="col-md-12" id="student_e_books">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
    <script>
        var gradientArray = ['bg-yellow-gradient', 'bg-purple-gradient', 'bg-green-gradient', 'bg-red-gradient', 'bg-aqua-gradient',
            'bg-maroon-gradient', 'bg-teal-gradient', 'bg-blue-gradient'
        ];

        $(document).ready(function() {
            $('.card-img-top').each(function(index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })
        });
    </script>