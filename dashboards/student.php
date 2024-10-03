<style>
  .profile_img {
    width: 100px;
    height: 100px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }

  table,
  tr,
  th,
  th {
    border: none !important;
  }

  .profile_table td {
    padding: .5rem !important;
    border: none !important;

  }
  .tile-progress .tile-footer {
    padding: 9px 20px !important;
}
.mbottom{
  margin-bottom: 4.5rem !important;
}
</style>
<div class="row mb-3">
  <div class="col-md-6">
    <div class="card border-primary shadow">
      <div class="card-header bg-primary text-white separator">
        <h5 class="fw-bold"><i class="ti-user mr-2"></i> Profile</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-5">
            <img class="img-fluid rounded border mb-3" width="100" src="<?= $_SESSION['Photo'] ?>" alt="">
            <h6 class="fw-semibold">Name: <?= $_SESSION['Name'] ?></h6>
            <h6 class="fw-semibold">Student ID: <?= $_SESSION['Unique_ID'] ?></h6>
            <h6 class="fw-semibold">Phone: <?= $_SESSION['Contact'] ?></h6>
            <h6 class="fw-semibold">Email: <?= $_SESSION['Email'] ?></h6>
          </div>
          <div class="col-md-7 border-left">
            <h6 class="fw-semibold">Academic Details:</h6>
            <div class="table-responsive">
              <table class="table mb-0 profile_table">
                <tr>
                  <td class="fw-normal" width="30%">Adm. Session </td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= $_SESSION['Admission_Session'] ?></td>
                </tr>
                <!--<tr>-->
                <!--  <td class="fw-normal" width="30%">Adm. Sem</td>-->
                <!--  <td class="fw-normal" width="2%">:</td>-->
                <!--  <td class="fw-normal"><?= $_SESSION['Duration'] ?></td>-->
                <!--</tr>-->
                <tr>
                  <td class="fw-normal" width="30%">Enrollment No</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?php echo empty($_SESSION['Enrollment_No']) ? 'Document under verification' : $_SESSION['Enrollment_No'] ?></td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Course</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= $_SESSION['Course'] ?></td>
                </tr>
                <tr>
                  <td class="fw-normal" width="30%">Specialization</td>
                  <td class="fw-normal" width="2%">:</td>
                  <td class="fw-normal"><?= $_SESSION['Sub_Course'] ?></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow border-success">
      <div class="card-header bg-success text-white separator">
        <h5 class="fw-bold"> <i class=" ti-ruler-pencil mr-2"></i> Academic Details</h5>
      </div>
      <div class="card-body m-t-10">
        <div class="row">
          <div class="col-md-6">
            <div class="tile-progress tile-pink">
              <div class="tile-header">
                <h4 class="mb-0">Subjects</h4>
                <?php

                $getSyllabi = $conn->query("SELECT Subjects.ID as subject_id,Subjects.Name,Program_Grade_ID FROM Subjects WHERE ID IN (SELECT Subject_id FROM `Student_Subjects` WHERE Student_Id = ".$_SESSION['ID'].")");
                 ?>
                <h4 class="mb-0"><?= $getSyllabi->num_rows; ?></h4>
                
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"><a href="/student/lms/lms" class="text-white">View Details <i class="ti-arrow-right ml-2"></i></a></h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-red">
              <div class="tile-header">
                <h4 class="mb-0">Assignments</h4>
                <!-- <h4 class="mb-0"> 4/10 Submitted</h4> -->
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"><a href="/student/lms/assignments" class="text-white">See Assignments <i class="ti-arrow-right ml-2"></i></a></h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-cyan">
              <div class="tile-header">
                <h4 class="mb-0">Exams</h4>
                <h4 class="mb-0"><?= $_SESSION['Admission_Session'] ?></h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"> <a href="/student/datesheet" class="text-white">See Datesheet <i class="ti-arrow-right ml-2"></i></a> </h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tile-progress tile-aqua">
              <div class="tile-header">
                <?php 
                   //$result_date = "Coming Soon";
  					$result_date = "";
                ?>
                <h4 class="mb-0">Results</h4>
                <h4 class="mb-0"><?= $result_date;  ?></h4>
              </div>
              <div class="tile-footer">
                <h5 class="mb-0"> <a href="/student/examination/results" class="text-white">See Results <i class="ti-arrow-right ml-2"></i></a> </h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row mb-2 mbottom">
  <div class="col-md-6">
    <div class="card border-info shadow">
      <div class="card-header bg-info text-white separator">
        <h5 class="fw-bold"><i class="ti-agenda mr-2"></i> Subject Overview</h5>
      </div>
      <div class="card-body">
      <div class="table-responsive">
          <?php
          if ($getSyllabi->num_rows > 0) { ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Subject Name</th>
                  <!-- <th>Credits</th> -->
                  <th>Ebooks</th>
                  <th>Video</th>
                  <th>Assessments</th>
                </tr>
              </thead>
              
              <tbody>
                <?php
                $rowArr = array();
                while ($rowArr = mysqli_fetch_assoc($getSyllabi)) {
                  $query = $conn->query("SELECT count(e_books.id) as total_ebook FROM e_books WHERE e_books.subject_id = '" . $rowArr['subject_id'] . "' AND e_books.status =1 AND e_books.course_id='" . $rowArr['Program_Grade_ID'] . "'");
                  $e_bookArr = $query->fetch_assoc();
                  $video_query = $conn->query("SELECT count(video_lectures.id) as total_vedio FROM video_lectures WHERE video_lectures.subject_id = '" . $rowArr['subject_id'] . "' AND video_lectures.status =1 AND video_lectures.course_id='" . $rowArr['Program_Grade_ID'] . "'");
                  $videoArr = $video_query->fetch_assoc();
                  $assesmentArr['total_assesment'] = 0;
                ?>
                  <tr>
                    <td><?= $rowArr['Name'] ?></td>
                    <td><a href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=1"><?= $e_bookArr['total_ebook'] ?></a></td>
                    <td><a href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=2"><?= $videoArr['total_vedio'] ?></a></td>
                    <td><a href="/student/lms/subjects?id=<?= $rowArr['subject_id'] ?>&type=3"><?= $assesmentArr['total_assesment'] ?></a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          <?php  } else { ?>
            <tr>
              <h1 class="text-center" style="font-size: 20px;font-weight: 600;">No Record Found!</h1>
            </tr>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border border-danger shadow">
      <div class="card-header bg-danger text-white separator">
        <h5 class="fw-bold"><i class="ti-bell mr-2"></i> Notifications</h5>
      </div>
      <div class="card-body m-t-10">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Regarding</th>
                <th>Sent To</th>
                <th>Sent On</th>
                <th>Content</th>
                <th>Attachment</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $current_notification_id = 0;
              $session = $_SESSION['Admission_Session'];
              list($monthText, $year) = explode('-', $session);
              $monthNumber = date('m', strtotime($monthText));
              $date = $year . '-' . $monthNumber . '-01';
              $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE (Send_To = '" . 'student' . "' OR Send_To = '" . 'all' . "') AND Noticefication_Created_on >= '$date'  ORDER BY Notifications_Generated.ID DESC  ");
              $data = array();
              while ($row = $result_record->fetch_assoc()) { ?>
                <tr>
                  <td><?= $row['Heading'] ?></td>
                  <td><?= $row['Send_To'] ?></td>
                  <td><?= $row['Noticefication_Created_on'] ?></td>
                  <td class="text-center"><a type="btn btn-link" class="text-primary" onclick="view_content('<?= $row['ID'] ?>');"><i class="fa fa-eye"></i></a></td>
                  <td>
                    <?php if (!empty($row['Attachment'])) { ?>
                      <a href="<?= $row['Attachment'] ?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                    <?php } else { ?>
                      <p>No Attachment</p>
                    <?php } ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <a href="" class="btn btn-danger float-right">See All Notifications</a>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function changeNotificationStatus(id) {
    $.ajax({
      url: '/app/notifications/current-notification?id=' + id,
      type: 'GET',
      success: function(data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal('show');
      }
    })
  }

  function view_content(id) {
    $.ajax({
      url: '/app/notifications/contents?id=' + id,
      type: 'GET',
      success: function(data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal('show');
      }
    })
  }
</script>