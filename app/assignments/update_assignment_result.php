<?php
if (isset($_POST['id'])) {
   // require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
    require '../../includes/db-config.php';
    error_reporting(-1); 
    $sub_id = intval($_POST['sub_id']);
    $id = intval($_POST['id']);
    $reason = $_POST['reason'];
    $status = $_POST['status'];
    $marks = intval($_POST['marks']);
  
    

    $get_marks_sql = "SELECT sa.marks FROM Student_Assignment AS sa WHERE sa.subject_id = ?";
    // echo "select sa.marks from student_assignment as sa  where sa.subject_id=?";
    if ($stmt = $conn->prepare($get_marks_sql)) {

        $stmt->bind_param("i", $sub_id);
        $stmt->execute();
        $stmt->bind_result($allotted_marks);
        $stmt->fetch();
        $stmt->close();
        // die();
        if ($marks > $allotted_marks) {
            echo json_encode(['status' => 400, 'message' => 'Marks should be less than or equal to allotted marks']);
            header('location:/../lms-settings/assignments-review');
            exit;
        }
        $sql = "UPDATE Student_Assignment_Result SET status=?, obtained_mark=?, remark=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(['status' => 0, 'message' => 'Database error: unable to prepare statement']);
            exit;
        }
        
        $stmt->bind_param("sisi", $status, $marks, $reason, $id);
        $stmt->execute();

        
        if ($stmt->execute()) {
          
            $assignentId = $conn->query("SELECT assignment_id FROM Student_Assignment_Result WHERE id = $id");
            $assignentId = $assignentId->fetch_assoc();
            $assignentId = $assignentId['assignment_id'];
            $update = $conn->query("UPDATE Student_Submitted_Assignment SET reuploaded = 0 WHERE id = $assignentId");
            echo json_encode(['status' => 200, 'message' => 'Result Updated Successfully!']); die;
            // header('location:/../lms-settings/assignments-review');
            // die;
        } else {
            echo json_encode(['status' => 0, 'message' => 'Something went wrong while updating the result in the database.']);
        }
        $stmt->close();
        $conn->close();
    }
}
