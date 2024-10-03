<?php
    require '../../includes/db-config.php';
    session_start();
    
    $syllabus = $conn->query("SELECT ' - ' as Code, Name, Mode as Paper_Type, NULL as Syllabus FROM Subjects WHERE ID IN (SELECT Subject_id FROM `Student_Subjects` WHERE Student_Id = ".$_SESSION['ID'].")");
?>
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Paper Type</th>
            <th>Syllabus</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $syllabus->fetch_assoc()) { ?>
            <tr>
              <td><?=$row['Code']?></td>
              <td><?=$row['Name']?></td>
              <td><?=$row['Paper_Type']?></td>
              <td>
                <?php if(!is_null($row['Syllabus']) && !empty($row['Syllabus'])){ 
                  $files = explode("|", $row['Syllabus']);
                  foreach($files as $file){?>
                    <a href="<?=$file?>" target="_blank" download="<?=$row['Code']?>">Download</a>
                <?php }}
                if(is_null($row['Syllabus']) && empty($row['Syllabus']) && $_SESSION['Role']=="Student"){
                  echo "NA";
                } 
                ?>
                <?php if(in_array($_SESSION['Role'], ['Administrator', 'University Head', 'Academic'])){ ?><button class="btn btn-link" onclick="uploadFile('Syllabi', 'Syllabus', <?=$row['ID']?>)">Upload</button><?php } ?>
              </td>
            </tr>
            <?php } ?>
        </tbody>
      </table>
    </div>
  </div>