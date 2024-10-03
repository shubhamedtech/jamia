<?php
  if(isset($_GET['ids']) && isset($_GET['user_id']) && isset($_GET['university_id'])){
    require '../../includes/db-config.php';
    //ini_set('display_errors', 1);
    $user_id = intval($_GET['user_id']);
    
    $university_id = intval($_GET['university_id']);
    $type_ids = mysqli_real_escape_string($conn, $_GET['ids']);

    if(empty($type_ids)){
      exit;
    }

    $fees = [];
    if ($university_id == 48) {
      $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID FROM Center_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");
    while($alloted_fee = $alloted_fees->fetch_assoc()){
      $fees[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Fee'];
    }

    $center_subCenters = $conn->query("SELECT Created_By FROM Users WHERE `ID` = $user_id");
    while($center_subCenter = $center_subCenters->fetch_assoc()){
      $user_id = $center_subCenter['Created_By'];
    }

    $sub_courses = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course, Sub_Courses.Min_Duration as duration FROM Center_Sub_Courses LEFT JOIN Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Center_Sub_Courses.Course_ID = Courses.ID WHERE Center_Sub_Courses.User_ID =  $user_id AND Center_Sub_Courses.University_ID = $university_id GROUP BY Sub_Courses.ID"); 
    ?>

    <div class="row">
      <div class="col-md-3">
        <P>Subject Name</P>
      </div>
      <div class="col-md-3">
        3<sup>rd</sup> Month fee 
      </div>
      <div class="col-md-3">
        6<sup>th</sup> Month fee
      </div>
      <div class="col-md-3">
        11<sup>th</sup> Month fee
      </div>
    </div>
     <?php while($sub_course = $sub_courses->fetch_assoc()){ ?>
      
      <div class="row pb-2">
        <div class="col-md-3">
          <dt class="pt-1"><?=$sub_course['Sub_Course']?></dt>
        </div>
        <?php if(count(json_decode($sub_course['duration'])) > 0){ 
          // for($i=0; $i < count(json_decode($sub_course['durections'])); $i++ )
          foreach(json_decode($sub_course['duration']) as $indx=> $drf)
          {?>
          <div class="col-md-3">
            <input type="number" min="0" step="500" placeholder="Fee" name="fee[<?=$sub_course['ID']?>]" value="<?php echo array_key_exists($sub_course['ID'], $fees) ? $fees[$sub_course['ID']] : '' ?>" class="form-control" />
          </div>
          <?php }}else{
            echo '<div class="col-md-3">';
            echo 'No Fee Set Yet.';
            echo '</div>';
            }
            ?>
      </div>
  <?php }
    } else {
      $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID FROM Sub_Center_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");
    while($alloted_fee = $alloted_fees->fetch_assoc()){
      $fees[$alloted_fee['Sub_Course_ID']] = $alloted_fee['Fee'];
    }

    $center_subCenters = $conn->query("SELECT Created_By FROM Users WHERE `ID` = $user_id");
    while($center_subCenter = $center_subCenters->fetch_assoc()){
      $user_id = $center_subCenter['Created_By'];
    }

    $sub_courses = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Sub_Course FROM Center_Sub_Courses LEFT JOIN Sub_Courses ON Center_Sub_Courses.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Center_Sub_Courses.Course_ID = Courses.ID WHERE Center_Sub_Courses.User_ID =  $user_id AND Center_Sub_Courses.University_ID = $university_id");
    while($sub_course = $sub_courses->fetch_assoc()){ ?>
      <div class="row pb-2">
        <div class="col-md-3">
          <dt class="pt-1"><?=$sub_course['Sub_Course']?></dt>
        </div>
        <div class="col-md-3">
          <input type="number" min="0" step="500" placeholder="Fee" name="fee[<?=$sub_course['ID']?>]" value="<?php echo array_key_exists($sub_course['ID'], $fees) ? $fees[$sub_course['ID']] : '' ?>" class="form-control" />
        </div>
      </div>
  <?php }
    }
    
  }
