<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/css/tabs.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/new-style.css" />
<link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<style>
    .subject_name {
        font-size: 18px !important;
        font-weight: 600;
    }

    .card.info-box.p-0 {
        box-shadow: unset !important;
        margin-bottom: 14px !important;
    }

    .stu-e-book-style {
        border-radius: 10px !important;
        height: 164px !important;
        width: 100% !important;
    }

    p.picon {
        padding-top: 18px !important;
    }

    .card-box1 {
        display: flex;
        gap: 14px;
    }

    .bg-yellow-gradient {
        background: #f39c12 !important;
    }

    .bg-purple-gradient {
        background: #605ca8 !important;
    }

    .bg-green-gradient {
        background: #00a65a !important;
    }

    .bg-red-gradient {
        background: #dd4b39 !important;
    }

    .bg-aqua-gradient {
        background: #008cd3 !important;
    }

    .bg-maroon-gradient {
        background: #f39c12 !important;
    }

    .bg-teal-gradient {
        background: #d81b60 !important;
    }

    .bg-blue-gradient {
        background: #39cccc !important;
    }

    .text-center.no_record {
        font-weight: 500 !important;
        font-size: 16px !important;
        padding: 11px !important;
    }

    span.bootstrap-switch-handle-on.bootstrap-switch-primary {
        display: none !important;
    }

    span.bootstrap-switch-handle-off.bootstrap-switch-default {
        display: none !important;
    }

    .list-group-item:last-child {
        margin-left: 0px !important;
    }

    .bootstrap-switch-container {
        margin-left: 0 !important;
        width: 0px !important;
    }

    .bootstrap-switch {
        width: 0px !important;
    }

    video {
        width: 100%;
        height: auto;
    }

    iframe {
        width: 100%;
    }
</style>
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
        <?php
        $subject_id = array();
        if (isset($_GET['id'])) {
            $active = '';
            $subject_id[0] = $_GET['id'];
            $ids = $_GET['id'];
            // die();
            $languagetypeSql = '';
            $lectureTypeSql = '';
            if (isset($_GET['languagetype']) && $_GET['languagetype'] != '') {
                $languagetypeSql = "AND video_lectures.Languages_Categories=" . $_GET['languagetype'];
            }
            if (isset($_GET['lectureType']) && $_GET['lectureType'] != '') {

                $lectureTypeSql = " AND video_lectures.Videos_Categories = " . $_GET['lectureType'];
            }
        } else {
            $active = "active";
            $getSubjectQuery = $conn->query("SELECT Subjects.ID as subject_id,Subjects.Name,Program_Grade_ID FROM Subjects WHERE ID IN (SELECT Subject_id FROM `Student_Subjects` WHERE Student_Id = " . $_SESSION['ID'] . ")");
            $subjectData = array();
            while ($row = mysqli_fetch_assoc($getSubjectQuery)) {
                $subject_id[] = $row['subject_id'];
                $subjectData[] = $row;
            }
        }
        $ebook_query = $conn->query("SELECT e_books.id,e_books.title, `Student_Subjects`.`Subject_id`,Subjects.Name AS subject_name FROM e_books LEFT JOIN Student_Subjects ON e_books.Subject_id = Student_Subjects.Subject_id LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.id  WHERE Student_Subjects.Student_ID=" . $_SESSION['ID'] . " AND e_books.subject_id IN ('" . implode("','", $subject_id) . "') AND e_books.Status=1");
        $eBookData = array();
        while ($row = mysqli_fetch_assoc($ebook_query)) {
            $eBookData[] = $row;
        }
        $video_query = $conn->query("SELECT video_lectures.id,video_lectures.video_type, `Student_Subjects`.`Subject_id`,Subjects.Name AS subject_name, video_lectures.unit, video_lectures.video_url FROM video_lectures LEFT JOIN Student_Subjects ON video_lectures.Subject_id = Student_Subjects.Subject_id LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.id  WHERE Student_Subjects.Student_ID=" . $_SESSION['ID'] . " AND video_lectures.subject_id IN ('" . implode("','", $subject_id) . "') AND video_lectures.Status=1 $lectureTypeSql $languagetypeSql");
        $videoData = array();
        while ($row = mysqli_fetch_assoc($video_query)) {
            $videoData[] = $row;
        }

        $loadurl = '';
        if (!empty($videoData[0])) {
            if ($videoData[0]['video_type'] == "url") {
                $loadurl = $videoData[0]['video_url'];
            } else {
                $loadurl = "../../" . $videoData[0]['video_url'];
            }
        }

        $question_bank_query = $conn->query("SELECT question_banks.id,question_banks.title, `Student_Subjects`.`Subject_id`,Subjects.Name AS subject_name FROM question_banks LEFT JOIN Student_Subjects ON question_banks.Subject_id = Student_Subjects.Subject_id LEFT JOIN Subjects ON Student_Subjects.Subject_id = Subjects.id  WHERE Student_Subjects.Student_ID=" . $_SESSION['ID'] . " AND question_banks.subject_id IN ('" . implode("','", $subject_id) . "') AND question_banks.Status=1");
        $question_bankData = array();
        while ($row = mysqli_fetch_assoc($question_bank_query)) {
            $question_bankData[] = $row;
        }

        // echo "<pre>"; 
        // print_r($loadurl); die;
        ?>
        <div class="content">
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs nav-fill customtab2" role="tablist">
                        <li class="nav-item"> <a class="nav-link py-3 <?php echo (!isset($_GET['type']) || $_GET['type'] == '' || $_GET['type'] == 1) ? 'active' : ''; ?>" data-toggle="tab" href="#ebook" role="tab"><span class="hidden-sm-up"><i class="ti-agenda mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Ebooks</span></a></li>
                        <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 2 ? 'active' : ''; ?>" data-toggle="tab" href="#video" role="tab"><span class="hidden-sm-up"><i class=" ti-video-clapper mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Videos</span></a> </li>
                        <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 4 ? 'active' : ''; ?>" data-toggle="tab" href="#notes" role="tab"><span class="hidden-sm-up"><i class="ti-write mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Question Banks</span></a> </li>
                        <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 3 ? 'active' : ''; ?>" data-toggle="tab" href="#assignment" role="tab"><span class="hidden-sm-up"><i class="ti-write mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Assessment</span></a> </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane <?php echo (!isset($_GET['type']) || $_GET['type'] == '' || $_GET['type'] == 1) ? 'active' : ''; ?>" id="ebook" role="tabpanel">
                            <div class="pad-20">
                                <div class="row">
                                    <?php if (count($eBookData) > 0) {
                                        foreach ($eBookData as $eBook) {  ?>
                                            <div class="col-sm-6 col-md-3 mb-3 ">
                                                <div class="card info-box p-0">
                                                    <div class="stu-e-book-style">
                                                        <p class="picon"><i class="icon-book-open e-book-icon"></i></p>
                                                        <p class="subject_name"><span><?php echo isset($eBook['title']) ? $eBook['title'] : $eBook['subject_name']; ?></span></p>
                                                    </div>
                                                </div>
                                                <p class="mt-2 " style="text-align:center;"><a class="btn btn-dark" href="/student/lms/view-e-book?id=<?php echo $eBook['id']; ?>&sub_id=<?= $ids ?>">View</a></p>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="col-md-12" id="student_e_books_not">
                                            <p class="text-center">No EBook Found !</p>
                                        </div>
                                    <?php }  ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 3 ? 'active' : ''; ?>" id="assignment" role="tabpanel">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="text-center no_record">Coming Soon...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 4 ? 'active' : ''; ?>" id="notes" role="tabpanel">
                            <div class="pad-20">
                                <div class="row">
                                    <?php if (count($question_bankData) > 0) {
                                        foreach ($question_bankData as $question_bank) {  ?>
                                            <div class="col-sm-6 col-md-3 mb-3 ">
                                                <div class="card info-box p-0">
                                                    <div class="stu-e-book-style">
                                                        <p class="picon"><i class="icon-book-open e-book-icon"></i></p>
                                                        <p class="subject_name"><span><?php echo isset($question_bank['title']) ? $question_bank['title'] : $question_bank['subject_name']; ?></span></p>
                                                    </div>
                                                </div>
                                                <p class="mt-2 " style="text-align:center;"><a class="btn btn-dark" href="/student/lms/view-question-bank?id=<?php echo $question_bank['id']; ?>&sub_id=<?= $ids ?>">View</a></p>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="col-md-12" id="student_e_books_not">
                                            <p class="text-center">No Question Bank Found !</p>
                                        </div>
                                    <?php }  ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 2 ? 'active' : ''; ?>" id="video" role="tabpanel">
                            <div class="">
                                <div class="row">
                                    <div class="col-md-6" id="eight">
                                        <?php if ($video_query->num_rows > 0) { ?>
                                            <div class="video_box" id="video_box">
                                            </div>

                                        <?php } ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-control" id="lectureType">
                                                    <option value="">Choose Lecture Type</option>
                                                    <option value="1" <?php if (isset($_GET['lectureType']) && $_GET['lectureType'] == 1) echo 'selected'; ?>>Live Lectures</option>
                                                    <option value="2" <?php if (isset($_GET['lectureType']) && $_GET['lectureType'] == 2) echo 'selected'; ?>>Recorded Lectures</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control" id="languagetype">
                                                    <?php
                                                    $selected1 = '';
                                                    $selected2 = '';
                                                    $selected3 = '';
                                                    $selected4 = '';
                                                    if (isset($_GET['languagetype'])) {
                                                        switch ($_GET['languagetype']) {
                                                            case 1:
                                                                $selected1 = "selected";
                                                                break;
                                                            case 2:
                                                                $selected2 = "selected";
                                                                break;
                                                            case 3:
                                                                $selected3 = "selected";
                                                                break;
                                                            case 4:
                                                                $selected4 = "selected";
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                    <option value="">Choose Languages Types</option>
                                                    <option value="1" <?= $selected1 ?>>Malayalam</option>
                                                    <option value="2" <?= $selected2 ?>>Tamil</option>
                                                    <option value="3" <?= $selected3 ?>>Arabic</option>
                                                    <option value="4" <?= $selected4 ?>>Kannada</option>
                                                </select>
                                            </div>
                                        </div>
                                        &nbsp;&nbsp;

                                        <div id="lectureList">
                                            <?php
                                            // if (isset($_GET['languagetype']) || isset($_GET['lectureType'])) {
                                            if (count($videoData) > 0) {
                                                foreach ($videoData as $key => $value) { ?>
                                                    <span onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');" id="videoItem<?= htmlspecialchars($value['id']) ?>" class="active_<?= htmlspecialchars($value['id']) ?> list-group-item list-group-item-action flex-column align-items-start">
                                                        <div class="d-flex w-100 justify-content-between align-items-center" onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');">
                                                            <input type="checkbox" id="videos<?= $key ?>" name="video" style="margin-right: -100px;" onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');">
                                                            <h5 class="mb-0 cursor-pointer" onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');">
                                                                <?= ucwords(htmlspecialchars($value['unit']))  ?>:<?= ucwords(htmlspecialchars($value['subject_name'])) ?>:
                                                            </h5>
                                                            <small><i class="ti-timer mr-1 cursur-pointer" onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');"></i>45min</small>
                                                            <i class="fa fa-play-circle fa-lg" onclick="getDataFunc('<?= htmlspecialchars($value['id']) ?>', '<?= htmlspecialchars($value['video_url']) ?>');"></i>
                                                        </div>
                                                    </span>
                                                <?php }
                                            } else { ?>
                                                <div class="col-md-12" id="student_e_books_not">
                                                    <p class="text-center">No Videos Found !</p>
                                                </div>
                                            <?php    }
                                            ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
        $(function() {
            if (location.pathname.indexOf('/student/lms/subjects') >= 0) {
                $('link[href*="/assets/plugins/bootstrap-switch/bootstrap-switch.css"]').prop('disabled', true);
            }
        });
        var gradientArray = ['bg-yellow-gradient', 'bg-purple-gradient', 'bg-green-gradient', 'bg-red-gradient', 'bg-aqua-gradient',
            'bg-maroon-gradient', 'bg-teal-gradient', 'bg-blue-gradient'
        ];
        $(document).ready(function() {
            $('.card-img-top').each(function(index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })
            $('.stu-e-book-style').each(function(index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })

            $(".video_box1").hide();
            $(".video_box1").show();
            let video_url = '<?= $loadurl ?>';
            // alert(video_url);
            getDataFunc(id = null, video_url);
        });
    </script>
    <script>
        function getDataFunc(id, video_url) {
            if (video_url.includes('youtube.com') || video_url.includes('youtu.be')) {
                let videoId = '';
                if (video_url.includes('youtube.com')) {
                    videoId = new URL(video_url).searchParams.get("v");
                } else if (video_url.includes('youtu.be')) {
                    videoId = video_url.split('/').pop();
                }
                const embedUrl = `https://www.youtube.com/embed/${videoId}`;
                $("#video_box").html('<iframe width="820" height="350" src="' + embedUrl + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
            } else {
                var video = $('<video/>', {
                    id: 'video',
                    src: video_url,
                    type: 'video/mp4',
                    controls: true
                });
                $("#video_box").html(video);
            }
            $(".active_" + id + " .list-group-item").addClass('active');
            $("#video" + id).prop('checked', true);
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkbox = document.getElementById("videos<?= $key ?>");
            const savedState = localStorage.getItem("checkboxState<?= $key ?>");
            if (savedState === "checked") {
                checkbox.checked = true;
                proper.checked = true;
                proper.disabled = false;
            } else {
                checkbox.checked = false;
                proper.checked = false;
                proper.disabled = true;
            }
            checkbox.addEventListener("click", function() {
                if (checkbox.checked) {
                    localStorage.setItem("checkboxState<?= $key ?>", "checked");
                    proper.checked = true;
                    proper.disabled = false;
                } else {
                    localStorage.setItem("checkboxState<?= $key ?>", "unchecked");
                    proper.checked = false;
                    proper.disabled = true;
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#lectureType").change(function() {
                var lectureType = $("#lectureType").val();
                var sub_id = "<?= $ids ?>";
                window.location.href = '/student/lms/subjects?lectureType=' + lectureType + "&id=" + sub_id + "&type=2";
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#languagetype').change(function() {
                var languagetype = $("#languagetype").val();
                var sub_id = "<?= $ids ?>";
                window.location.href = '/student/lms/subjects?languagetype=' + languagetype + "&id=" + sub_id + "&type=2";
            });
        });
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>