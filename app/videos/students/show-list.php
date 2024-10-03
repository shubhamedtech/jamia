<?php
    require '../../../includes/db-config.php';
    $base_url="http://".$_SERVER['HTTP_HOST']."/";
    $course_id=intval($_POST['course_id']);
    if (isset($_POST['subject_id']) ) {
        $subject_id = intval($_POST['subject_id']);

        $result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, video_lectures.`status` FROM video_lectures LEFT JOIN Courses ON Courses.ID = video_lectures.course_id LEFT JOIN Subjects ON Subjects.ID = video_lectures.subject_id WHERE video_lectures.subject_id =$subject_id  AND video_lectures.status !=2  AND video_lectures.course_id=$course_id ";

        $results = mysqli_query($conn, $result_record);
        $videoData=array();
        $htmlReturnData="";
        while ($row = mysqli_fetch_assoc($results)) {
            $videoData[]= $row;
            $htmlReturnData .= '<div class="col-sm-6 col-md-3 mb-3"><a href="/student/lms/video-player?id='.$row['id'].'"><div class="stu-e-book-style"><img class="thumbnail" src="'.$base_url.$row['thumbnail_url'].'"><p><i class="fa fa-play-circle video-icon"></i></p></div></a><h5 class="mt-2 text-center mb-1"><b>'.ucwords($row['subject_name']).':</b>'.ucwords($row['unit']).'</h5><p class="video-description text-center">'.ucwords($row['description']).'</p></div>';
            //$htmlReturnData = $htmlReturnData . '<div class="col-sm-6 col-md-3 mb-3"><div><video style="width:100%" controls src="'.$base_url.$row['video_url'].'" type="video/'.$row['video_type'].'" ></video></div><h5 class="mt-2">'.$row['unit'].'</h5><p class="video-description">'.$row['description'] .'</p></div>';
        }

        if(count($videoData)>0){
            echo $htmlReturnData; die;
        }else{
            echo 0; die;
        }

    }
        
    ?>









        