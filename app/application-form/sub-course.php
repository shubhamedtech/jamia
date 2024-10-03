<?php
if (isset($_GET['university_id']) && isset($_GET['session_id']) && isset($_GET['admission_type_id']) && isset($_GET['course_id']) && isset($_GET['center'])) {
  require '../../includes/db-config.php';
  session_start();

  $university_id = intval($_GET['university_id']);
  $session_id = intval($_GET['session_id']);
  $admission_type_id = intval($_GET['admission_type_id']);
  $course_id = intval($_GET['course_id']);
  $user_id = intval($_GET['center']);
  $student_id = intval($_GET['student_id']);
  
  $sql_query = "SELECT Subjects.*, Courses.Name as Grade FROM Subjects LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID WHERE Subjects.Program_Grade_ID = $course_id";
  $results = $conn->query($sql_query);
  $lag_subjects = array();
  $other_subjects = array();
  $elective_subjects = array();
  $elective_count = array();
  $optional_subjects = array();
  $lag_type = array();
  $other_type = array();
  $elective_type = array();
  $optional_type = array();
  while ($result = $results->fetch_assoc()) { 
    if ($result['Category'] == 'Language') {
      $lag_subjects[$result['ID']] =  $result['Name'];
      $lag_type[$result['ID']] =  $result['Type'];
    } elseif ($result['Category'] == 'Others'){
      $other_subjects[$result['ID']] = $result['Name'];
      $other_type[$result['ID']] = $result['Type'];
    }elseif ($result['Category'] == 'Elective'){
      $elective_subjects[$result['ID']] = $result['Name'];
      $elective_type[$result['ID']] = $result['Type'];
      //$elective_count['Name'] = ($result['Category'] == "Elective") ? $result['Category'] : '';
    }elseif ($result['Category'] == 'optional'){
      $optional_subjects[$result['ID']] = $result['Name'];
      $optional_type[$result['ID']] = $result['Type'];
    }
  } 
  ?>

  <div class="d-flex flex-row justify-content-between">
  <div class='card-box'>
    <p class='fw-bold'>Language Subjects</p>
    <div class='card shadow p-2 pr-6 d-flex flex-column gap-4'>
      <?php if(empty($lag_subjects)){ ?>
        <div>
          <span>Subject not available!</span>
        </div>
        <?php }else{
        foreach($lag_subjects as $index => $sub){ 
          	$check_assign = $conn->query("SELECT * FROM Student_Subjects WHERE  Subject_id = $index AND Student_Id = $student_id");
            $checked_value = ($lag_type[$index] == 1 ||  $check_assign->num_rows > 0) ? 'checked' : '';
           	$disabled = $lag_type[$index] == 1 ? 'false' : 'true';
      	?>
        <div>
          <input type='checkbox' name='language_subjects[]' id='language_subject' class="language_subject" onclick="return <?=$disabled?>;" value='<?=$index?>' <?=$checked_value?> />
          <span for='subject'><?=$sub?></span>
        </div>
        <?php }
      } ?>
    </div>
  </div>
  <div class='card-box'>
    <p class='fw-bold'>Others Subjects</p>
    <div class='card shadow p-2 pr-6 d-flex flex-column gap-4'>
      <?php if(empty($other_subjects)){ ?>
        <div>
          <span>Subject not available!</span>
        </div>
        <?php }else{
        foreach($other_subjects as $index => $sub){ 
          $check_assign = $conn->query("SELECT * FROM Student_Subjects WHERE  Subject_id = $index AND Student_Id = $student_id");
            $checked_value = ($other_type[$index] == 1 ||  $check_assign->num_rows > 0) ? 'checked' : '';  
          	$disabled = $other_type[$index] == 1 ? 'false' : 'true';
      	?>
          <div>
          <input type="checkbox" name="subjects[]" id="subject<?=$index?>" onclick="return <?=$disabled?>;" onchange="updateSubjectStatus(<?=$index?>)" value="<?=$index?>" <?=$checked_value?> />
          <label for="subject<?=$index?>"><?=$sub?></label>
        </div>
        <?php }
      } ?>
    </div>
  </div>
  <div class='card-box'>
    <p class='fw-bold'>Elective Subjects</p>
    <div class='card shadow p-2 pr-6 d-flex flex-column gap-4 ele_sub'>
      <?php if(empty($elective_subjects)){ ?>
        <div>
          <span>Subject not available!</span>
        </div>
        <?php }else{
        	foreach($elective_subjects as $index => $sub){ 
          	$check_assign = $conn->query("SELECT * FROM Student_Subjects WHERE  Subject_id = $index AND Student_Id = $student_id");
            $checked_value = ($elective_type[$index] == 1 && $check_assign->num_rows > 0) ? 'checked' : '';
        ?>
        <div>
            <input type='checkbox' name='subjects_elective[]' class="checkoption" onclick="onlyOne(this);" id='subjects_elective' value='<?=$index?>' <?=$checked_value?> required/>
            <span for='subjects_elective'><?=$sub?></span>
        </div>
        <?php }
      } ?>
    </div>
  </div>
    <div class='card-box'>
    <p class='fw-bold'>Optional Subjects</p>
    <div class='card shadow p-2 pr-6 d-flex flex-column gap-4'>
      <?php if(empty($optional_subjects)){ ?>
        <div>
          <span>Subject not available!</span>
        </div>
        <?php }else{
        foreach($optional_subjects as $index => $sub){ 
          	$check_assign = $conn->query("SELECT * FROM Student_Subjects WHERE  Subject_id = $index AND Student_Id = $student_id");
            $checked_value = $check_assign->num_rows > 0 ? 'checked' : '';
          ?>
          <div>
          <input type='checkbox' name='subjects_optional[]' id='subjects_optional' value='<?=$index?>' <?=$checked_value?>/>
          <span for='subjects_optional'><?=$sub?></span>
        </div>
        <?php }
      } ?>
    </div>
  </div>
</div>

<script>
  function updateSubjectStatus(id){
    const labelText = $("#subject"+id).next('label').text().trim().toLowerCase();
    const rules = {
      'history' : ['Geography'],
      'geography' : ['History'],
      'political science' : ['Sociology'],
      'sociology' : ['Political Science'],
      'biology':['Mathematics'],
      'mathematics':['Biology','Home Science'],
      //'mathematics':['Home Science'],
      'home science':['Mathematics'],
    }

    if(rules.hasOwnProperty(labelText)){
      $.each(rules[labelText], function(index, disabledLabelText){
        const checkbox = $('label:contains("' + disabledLabelText + '")').prev('input[type="checkbox"]');
        checkbox.prop('disabled', $("#subject"+id).prop('checked'))  
      })
    } 
  }
</script>
<?php }
