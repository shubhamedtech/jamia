<?php
require '../../../includes/db-config.php';
session_start();
if (isset($_GET['course_id']) && isset($_GET['semester'])) {
  $sub_course_id = intval($_GET['course_id']);
  $semester = explode("|", $_GET['semester']);
  $scheme = $semester[0];
  $semester = $semester[1];

  $syllabus = $conn->query("SELECT * FROM Syllabi WHERE Sub_Course_ID = $sub_course_id AND Scheme_ID = $scheme AND Semester = $semester");
?>
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Videos</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $syllabus->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['Name'] ?></td>
              <td>
                <?php if (!is_null($row['Syllabus']) && !empty($row['Syllabus'])) {
                  $files = explode("|", $row['Syllabus']);
                  foreach ($files as $file) { ?>
                    <a href="<?= $file ?>" target="_blank" download="<?= $row['Code'] ?>">Download</a>
                <?php }
                } ?>
                <?php if (in_array($_SESSION['Role'], ['Student'])) {
                  $videos_id = 0;
                  $videos_unit_id = 0;
                  $videos_subject_id = 0;
                  $units = $conn->query("SELECT Units.Unit_Name, Units.ID, Syllabi_ID FROM Units WHERE Syllabi_ID = " . $row['ID'] . ""); ?>
                  <ul class="list-unstyled text-left">

                    <?php while ($unit = $units->fetch_assoc()) {
                      $videos = $conn->query("SELECT ID, Subject_ID, Unit_ID FROM Videos WHERE Unit_ID = " . $unit['ID'] . " AND Subject_ID = " . $unit['Syllabi_ID'] . "");
                      while ($video = $videos->fetch_assoc()) {
                        $videos_id = $video['ID'];
                        $videos_unit_id = $video['Unit_ID'];
                        $videos_subject_id = $video['Subject_ID'];
                      }
                      $assessment_id = 0;
                      $assessment = $conn->query("SELECT Video_Assessments.ID FROM Video_Assessments WHERE Video_ID = $videos_id AND Video_Assessments.Unit_ID = " . $unit['ID'] . " AND Video_Assessments.Subject_ID = " . $unit['Syllabi_ID'] . "");
                      if ($assessment->num_rows > 0) {
                        $assessment_id = mysqli_fetch_assoc($assessment);
                        $assessment_id = $assessment_id['ID'];
                      }

                      $student_assessments = $conn->query("SELECT ID FROM Students_Assessments WHERE Assessment_ID = $assessment_id AND Student_ID = ".$_SESSION['ID']." AND Video_ID = $videos_id AND Unit_ID = " . $unit['ID'] . " AND Subject_ID = " . $unit['Syllabi_ID'] . "");
                    ?>
                      
                      <button class="btn btn-primary cursor-pointer"><?= $unit['Unit_Name'] ?></button>
                      
                      <?php if($student_assessments->num_rows){ ?>
                        <button class="btn btn-success cursor-pointer" onclick="videoList(<?= $videos_id ?>, <?= $videos_unit_id ?>,<?= $videos_subject_id ?>);"><?= $videos->num_rows ?> <i class="fa fa-video-camera "></i></button>
                        <span class="btn btn-sm btn-success cursor-pointer m-2" onclick="assessmentList(<?= $assessment_id ?>, <?= $videos_id ?>, <?= $videos_unit_id ?>, <?= $videos_subject_id ?>);"><?= $assessment->num_rows ?> <i class="fa-pencil-square-o fa"></i></span>
                      <?php } else { ?>
                        <button class="btn btn-secondary cursor-pointer" onclick="videoList(<?= $videos_id ?>, <?= $videos_unit_id ?>,<?= $videos_subject_id ?>);"><?= $videos->num_rows ?> <i class="fa fa-video-camera "></i></button>
                        <span class="btn btn-sm btn-warning cursor-pointer m-2" onclick="assessmentList(<?= $assessment_id ?>, <?= $videos_id ?>, <?= $videos_unit_id ?>, <?= $videos_subject_id ?>);"><?= $assessment->num_rows ?> <i class="fa-pencil-square-o fa"></i></span>
                  <?php } }
                  } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<?php } ?>