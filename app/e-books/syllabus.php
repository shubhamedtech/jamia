<?php
require '../../includes/db-config.php';
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
            <th>Code</th>
            <th>E-Books</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $syllabus->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['Name'] ?></td>
              <td><?= $row['Code'] ?></td>
              <td>
                <?php if (!is_null($row['Syllabus']) && !empty($row['Syllabus'])) {
                  $files = explode("|", $row['Syllabus']);
                  foreach ($files as $file) { ?>
                    <a href="<?= $file ?>" target="_blank" download="<?= $row['Code'] ?>">Download</a>
                <?php }
                } ?>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])) {
                  $E_books_id = 0;
                  $E_books_unit_id = 0;
                  $E_books_subject_id = 0;
                  $units = $conn->query("SELECT Units.Unit_Name, Units.ID, Syllabi_ID FROM Units WHERE Syllabi_ID = " . $row['ID'] . ""); ?>
                  <ul class="list-unstyled text-left">
                    <?php while ($unit = $units->fetch_assoc()) {
                      $E_books = $conn->query("SELECT ID, Subject_ID, Unit_ID FROM e_books WHERE Unit_ID = " . $unit['ID'] . " AND Subject_ID = " . $unit['Syllabi_ID'] . "");
                      while ($E_book = $E_books->fetch_assoc()) {
                        $E_books_id = $E_book['ID'];
                        $E_books_unit_id = $E_book['Unit_ID'];
                        $E_books_subject_id = $E_book['Subject_ID'];
                      }

                      $assessments = $conn->query("SELECT ID FROM e_book_assessments WHERE E_Book_ID = $E_books_id AND Unit_ID = ".$unit['ID']." AND Subject_ID = ".$unit['Syllabi_ID']."");
                      if($assessments->num_rows > 0){
                        $assessment_id = mysqli_fetch_assoc($assessments);
                        $assessment_id = $assessment_id['ID'];
                      }

                    ?>
                      <?php if ($E_books->num_rows > 0) { ?>
                        <li class="text-capitalize">
                          <?= $unit['Unit_Name'] ?> : <button class="btn btn-link ml-2 p-0 btn-sm" onclick="uploadFile(<?= $unit['ID'] ?>, <?= $row['ID'] ?>, <?= $row['Semester'] ?>)"><i class="fa fa-upload"></i> E-Book</button><button class="btn btn-link ml-2 p-0 btn-sm" onclick="addAssessment(<?= $E_books_id ?>, <?=$E_books_unit_id ?>, <?= $E_books_subject_id ?>)"><i class="fa fa-upload"></i>Assessment</button>
                          <ul class="list-unstyled">
                            <li class="text-primary cursor-pointer" onclick="E_bookList(<?= $E_books_id ?>, <?= $E_books_unit_id ?>,<?= $E_books_subject_id ?>);"><i class="fa fa-book mr-1"></i><?= $E_books->num_rows ?> Files
                            </li>
                            <li class="text-primary cursor-pointer" onclick="assessmentList(<?=$assessment_id ?>,<?= $E_books_id ?>, <?= $E_books_unit_id ?>,<?= $E_books_subject_id ?>);"><i class="fa fa-file-text-o mr-1"></i> <?=$assessments->num_rows?> Assessments
                            </li>
                          </ul>
                        </li>
                      <?php } else { ?>
                        <li>
                          <?= $unit['Unit_Name'] ?><?= $unit['Unit_Name'] ?> : <button class="btn btn-link ml-2 p-0 btn-sm" onclick="uploadFile(<?= $unit['ID'] ?>, <?= $row['ID'] ?>, <?= $row['Semester'] ?>)"><i class="fa fa-upload"></i> E-Book</button><button class="btn btn-link ml-2 p-0 btn-sm"><i class="fa fa-upload"></i> Assessment</button>
                        </li>
                  <?php }
                    }
                  } ?>
              </td>
              <td>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])) { ?><a href="/app/E_books/units/create?syllabi_id=<?= base64_encode($row['ID']) ?>" class="btn btn-primary btn-sm"><i class="icon-plus mr-1"></i> Add-Unit</a><?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<?php } else {
  $syllabus = $conn->query("SELECT * FROM Syllabi WHERE University_ID = " . $_SESSION['university_id'] . "");
?>
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped text-center">
        <thead>
          <tr>
            <th>Name</th>
            <th>Code</th>
            <th>E-Books</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $syllabus->fetch_assoc()) { ?>
            <tr>
              <td><?= $row['Code'] ?></td>
              <td><?= $row['Name'] ?></td>
              <td>
                <?php if (!is_null($row['Syllabus']) && !empty($row['Syllabus'])) {
                  $files = explode("|", $row['Syllabus']);
                  foreach ($files as $file) { ?>
                    <a href="<?= $file ?>" target="_blank" download="<?= $row['Code'] ?>">Download</a>
                <?php }
                } ?>
                <?php if (in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])) {
                  $E_books_id = 0;
                  $E_books_unit_id = 0;
                  $E_books_subject_id = 0;
                  $units = $conn->query("SELECT Units.Unit_Name, Units.ID, Syllabi_ID FROM Units WHERE Syllabi_ID = " . $row['ID'] . ""); ?>
                  <ul class="list-unstyled text-left">
                    <?php while ($unit = $units->fetch_assoc()) {
                      $E_books = $conn->query("SELECT ID, Subject_ID, Unit_ID FROM e_books WHERE Unit_ID = " . $unit['ID'] . " AND Subject_ID = " . $unit['Syllabi_ID'] . "");
                      while ($E_book = $E_books->fetch_assoc()) {
                        $E_books_id = $E_book['ID'];
                        $E_books_unit_id = $E_book['Unit_ID'];
                        $E_books_subject_id = $E_book['Subject_ID'];
                      }

                      $assessments = $conn->query("SELECT ID FROM e_book_assessments WHERE E_Book_ID = $E_books_id AND Unit_ID = ".$unit['ID']." AND Subject_ID = ".$unit['Syllabi_ID']."");
                      if($assessments->num_rows > 0){
                        $assessment_id = mysqli_fetch_assoc($assessments);
                        $assessment_id = $assessment_id['ID'];
                      }
                    ?>
                      <?php if ($E_books->num_rows > 0) { ?>
                        <li class="text-primary cursor-pointer">
                          <span onclick="E_bookList(<?= $E_books_id ?>, <?= $E_books_unit_id ?>,<?= $E_books_subject_id ?>);"><i class="fa fa-book mr-1"></i><?= $unit['Unit_Name'] ?> : <?= $E_books->num_rows ?> Files </span>
                          <span onclick="assessmentList(<?=$assessment_id ?>,<?= $E_books_id ?>, <?= $E_books_unit_id ?>,<?= $E_books_subject_id ?>);"> <?= $assessments->num_rows ?> Assesssment</span>
                        </li>
                      <?php } else { ?>
                        <li>
                          <i class="fa fa-book mr-1"></i><?= $unit['Unit_Name'] ?>
                        </li>
                  <?php }
                    }
                  } ?>
                  </ul>


              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<?php } ?>