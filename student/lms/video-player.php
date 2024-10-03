<?php require '../../includes/db-config.php';  ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video-js.min.css" rel="stylesheet">
<style>
  .copyright {
    background: #fff;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
  }

  .stu-e-book-style {
    width: 140px;
    height: 80px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #838383;
  }

  .video-icon {
    font-size: 30px;
    text-align: center;
    color: #fff;
    position: absolute;
    /*color: currentcolor;*/
    top: 22px;
    left: 65px;
    display: none;
    cursor: pointer;

  }

  .subject_name {
    font-size: 18px !important;
    font-weight: 600;
  }

  .container-play-btn {
    position: relative;
    width: 400px;
    height: 200px;
  }

  .play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* display: none; */
    font-size: 40px;
  }

  .thumbnail {
    height: inherit;
    width: inherit;
    border-radius: 10px;
    cursor: pointer;
  }

  .stu-e-book-style:hover .video-icon {
    display: block;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php
//$base_url=$_SERVER['HTTP_HOST'];
$id = $_GET['id'];
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
// $course_id = $_SESSION['Course_ID'];
$student_id = $_SESSION['ID'];

$studentSubjects = "SELECT Student_Subjects.`Subject_id` as subject_id,Subjects.Name from Student_Subjects LEFT JOIN Subjects ON Subjects.ID = Student_Subjects.Subject_id  WHERE Student_Subjects.Student_id = $student_id ";
$Subjects = mysqli_query($conn, $studentSubjects);
$mySubjects = array();
$subjectData = array();
while ($row = mysqli_fetch_assoc($Subjects)) {
  $mySubjects[] = $row['subject_id'];
  $subjectData[] = $row;
}
$result_record = "SELECT video_lectures.`id`,video_lectures.`subject_id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.id=$id ";
$results = mysqli_query($conn, $result_record);
$row = mysqli_fetch_assoc($results);

$result_record2 = "SELECT video_lectures.`id`,video_lectures.`subject_id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.status=1 AND video_lectures.subject_id=" . $row['subject_id'];

$results2 = mysqli_query($conn, $result_record2);
$videoData = array();
while ($row2 = mysqli_fetch_assoc($results2)) {
  $videoData[] = $row2;
}
?>
<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">

    <div class="content">
      <div class="card">
        <section class="my-5">
          <div class="container">
            <div class="row">
              <div class="col-md-8 border-top" style="padding: 0px;">
                <?php
                $video_url = $row['video_url'];
                $base_url = '';
                if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                  if (strpos($video_url, 'youtube.com') !== false) {
                    parse_str(parse_url($video_url, PHP_URL_QUERY), $query_params);
                    $video_id = $query_params['v'];
                  } else {
                    $video_id = substr(parse_url($video_url, PHP_URL_PATH), 1);
                  }
                  echo '<iframe width="760" height="350" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                } else {
                  echo '<video width="760" height="350" controls autoplay controlsList="nodownload" src="' . $base_url . $row['video_url'] . '" type="video/' . $row['video_type'] . '"></video>';
                }
                if (!empty($row['subject_name']) && !empty($row['unit']) && !empty($row['description'])) {
                  echo '<h5 class="mt-2 mb-1"><b>' . ucwords($row['subject_name']) . ' : </b>' . ucwords($row['unit']) . '</h5>';
                  echo '<p class="video-description">' . ucfirst($row['description']) . '</p>';
                }
                ?>
              </div>
              <div class="col-md-4 ">
                <?php foreach ($videoData as $video) {  ?>
                  <div class="row border m-1">
                    <div class="col-sm-6 mt-2 mb-2">
                      <a href="/student/lms/video-player?id=<?php echo $video['id']; ?>">
                        <div class="stu-e-book-style"><img class="thumbnail" src="<?= $base_url ?><?= $video['thumbnail_url'] ?> ">
                          <p><i class="fa fa-play-circle video-icon"></i></p>
                        </div>
                      </a>
                    </div>
                    <div class="col-sm-6 mt-2 mb-2 ">
                      <p><b><?= ucwords($video['unit']) ?></b></p>
                      <p><?= ucwords($video['description']) ?></p>
                    </div>
                  </div>
                <?php } ?>

              </div>
            </div>
          </div>
        </section>

      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video.min.js"></script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>