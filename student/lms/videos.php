<?php require '../../includes/db-config.php';  ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video-js.min.css" rel="stylesheet">
<style>
  .stu-e-book-style {
    width: 300px;
    height: 150px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #838383;
  }

  .video-icon {
    font-size: 55px;
    text-align: center;
    color: #fff;
    position: absolute;
    /*color: currentcolor;*/
    top: 45px;
    left: 125px;
    display: none;
    cursor: pointer;
  }
  .subject_name{
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

.thumbnail{
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
$base_url="http://".$_SERVER['HTTP_HOST']."/";
$course_id=$_SESSION['Course_ID'];
$student_id=$_SESSION['ID'];

$studentSubjects = "SELECT Student_Subjects.`Subject_id` as subject_id,Subjects.Name from Student_Subjects LEFT JOIN Subjects ON Subjects.ID = Student_Subjects.Subject_id  WHERE Student_Subjects.Student_id = $student_id ";
$Subjects = mysqli_query($conn, $studentSubjects);
$mySubjects=array();
$subjectData=array();
while ($row = mysqli_fetch_assoc($Subjects)) {
  $mySubjects[]= $row['subject_id'];
  $subjectData[]=$row;
}

$result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.subject_id IN ('" . implode("','", $mySubjects) . "')  AND video_lectures.status =1  AND video_lectures.course_id=$course_id ";
$results = mysqli_query($conn, $result_record);
$videoData=array();
while ($row = mysqli_fetch_assoc($results)) {
  $videoData[]= $row;
}

?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>


    <div class="content-wrapper">
        <!-- <div class="content-header sty-one">
            <div class="d-flex align-items-center justify-content-between">

                <?php 
                  // $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                  // for ($i = 1; $i <= count($breadcrumbs); $i++) {
                  //     if (count($breadcrumbs) == $i) : $active = "active";
                  //         $crumb = explode("?", $breadcrumbs[$i]);
                  //         echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
                  //     endif;
                  // }
                ?>
            </div>
        </div> -->


        <div class="content">
            <div class="card">
              <div class="card-header">
                <?php 
                  $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                  for ($i = 1; $i <= count($breadcrumbs); $i++) {
                    if (count($breadcrumbs) == $i) : $active = "active";
                      $crumb = explode("?", $breadcrumbs[$i]);
                      echo '<h4 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h4>';
                    endif;
                  }
                ?>
                
                <div class="row pull-right">

                <div class="col-xs-7 " style="margin-right: 10px;">
                        <div class="form-group  required">
                            <select class="form-control"  onchange="subjectFilter(this.value)" style="width: 200px;">
                                <option value="">Subject</option>
                                <?php foreach($subjectData as $subj){ ?>
                                    <option value="<?=$subj['subject_id']?>"><?=$subj['Name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                  <!-- <div class="col-xs-7" style="margin-right: 10px;">
                    <input type="text" id="Courses-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
                  </div> -->
                </div>
                <div class="clearfix"></div>
              </div>

                <div class="card-body">
                      
                      <div class="row" id="data_list">
                      <?php  foreach($videoData as $video){  ?>
                        <div class="col-sm-6 col-md-3 mb-3 " >
                         <a  href="/student/lms/video-player?id=<?php echo $video['id']; ?>" >
                          <div class="stu-e-book-style" ><img class="thumbnail" src="<?=$base_url?><?=$video['thumbnail_url']?> "><p><i class="fa fa-play-circle video-icon" ></i></p>
                          </div>
                        </a>
                          <h5 class="mt-2 text-center mb-1"><b><?=ucwords($video['subject_name'])?> : </b><?=ucwords($video['unit'])?></h5>
                          <p class="video-description text-center"><?=ucwords($video['description']) ?></p>
                        </div>
                        <!--<div class="col-sm-6 col-md-3 mb-3">-->
                        <!--  <div>-->
                        <!--    <video style="width: 100%;" controls="controls" src="<?=$base_url?><?=$video['video_url']?> " type="video/"<?=$video['video_type']?>></video>-->
                        <!--  </div>-->
                        <!--  <h5 class="mt-2"><?=$video['unit']?></h5>-->
                        <!--  <p class="video-description"><?=$video['description'] ?></p>-->
                        <!--</div>-->
                        <?php } ?>

                        
                      </div>

                </div>
            </div>
        </div>
    </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

</div>

<script type="text/javascript">
  
    function subjectFilter(subject_id){
      $.ajax({
          url: '/app/videos/students/show-list',
          type: 'POST',
          dataType:'text',
          data: {
            "subject_id": subject_id,
            'course_id':"<?=$course_id?>"
          },
          success: function(result) {
            if(result!=0){
                $('#data_list').html(result);
            } else{
                $('#data_list').html('<div class="col-md-12"><h3 style="text-align: center;">Data not available!</h3></div>');
            }
          }
        })
    }







    /*###############################$$$$$$$$$$$$$$$$$$$$$*/

      function videoList(id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/students/show-list',
          type: 'POST',
          data: {
            "id": id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          },
          complete: function() {
            $('.video-js').each(function() {
              videojs(this, {
                width: 200,
                height: 100,
                controls: true,
                preload: "auto",
              });
            });
          }
        })
      }

      
    function getTable() {
        $('#student_videos_not').hide();
        var course_id = '<?= $_SESSION['Sub_Course_ID'] ?>';
        var semester = $('#semester').val();
        if (course_id.length > 0 && semester.length > 0) {
            $.ajax({
                url: '/app/videos/students/syllabus?course_id=' + course_id + '&semester=' + semester,
                type: 'GET',
                success: function(data) {
                    $('#student_videos').html(data);
                }
            })
        } else {
            $('#student_videos').html('');
        }
    }

    function removeTable() {
        $('#assignments').html('');
    }
    </script>
    
<script src="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video.min.js"></script>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>

    