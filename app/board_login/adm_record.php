<?php
include '../../includes/db-config.php';

$student_id = isset($_GET['students_id']) ?  $_GET['students_id'] : "";
$sessions = isset($_GET['sessions']) ? (int) $_GET['sessions'] : null;

if (!$student_id || !$sessions) {
    echo "Invalid request. Missing student ID or session ID.";
    exit();
}

$sql = "SELECT * FROM students WHERE Unique_ID = ? AND Admission_Session_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $student_id, $sessions);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    // print_r($student);
    // die();
} else {
    echo "Student not found.";
    exit();
}
$answersheetUrl = "https://jua.exam-portal.in/api/answer-sheets/list?uniqueId=$student_id";
$student_answer_sheet = file_get_contents($answersheetUrl);
$answer_sheet_data = json_decode($student_answer_sheet,true);

?>

  
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card</title>
    <link rel="stylesheet" href="path_to_your_css.css">
</head>

<body>
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admit Card</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td><?= htmlspecialchars($student['First_Name'] . " " . $student['Last_Name']); ?></td>
                        <td>
                            <a href="/app/board_login/admit_card.php?student_id=<?= base64_encode($student['ID']); ?>" class="">
                            <i class="fa fa-download fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <h4>Answer Sheet</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Subject Name</th>
                        <th>Date</th>
                        <th>Download Answer Sheet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(!empty($answer_sheet_data['data']))
                        {
                            foreach($answer_sheet_data['data'] as $key=>$value)
                            {
                                $pdf_name = str_replace('/','_',$student['Enrollment_No']).'_'.str_replace(' ','_',$value['AssessmentName']).'.pdf';
                                ?>
                                    <tr>
                                        <td><?=$key+1?></td>
                                        <td><?=$value['AssessmentName']?></td>
                                        <td><?=$value['StartDate']?></td>
                                        <td>
                                            <a href="https://jua.exam-portal.in/api/answer-sheets/pdf?fileName=<?=$pdf_name?>&answerSheetId=<?=$value['AnswerSheetId']?>" Download><i class="fa fa-download fa-lg"></i></a>    
                                        </td>
                                    </tr>
                                <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>