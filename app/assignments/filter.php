<?php
if (isset($_POST['id']) && isset($_POST['by'])) {
    session_start();
    require '../../includes/db-config.php';

    $by = $_POST['by'];
    $role_type = isset($_POST['role']) ? $_POST['role'] : NULL;
    $id = intval($_POST['id']);
    $sub_center_name = "";
    $center_sub_center = '';
    $role_query = "";
    $center_option = '';

    if ($by == 'courses') {
        $_SESSION['filterByCourses'] = " AND Students.Course_ID = $id";
    } elseif ($by == 'sub_courses') {
        $courses = isset($_SESSION['filterByCourses']) ? $_SESSION['filterByCourses'] : '';
        $_SESSION['filterBySubCourses'] = " AND Students.Sub_Course_ID = $id";
    } elseif ($by == 'users') {
        $user_result = $conn->query("SELECT Role, CanCreateSubCenter FROM Users WHERE ID = $id");
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $role = $user['Role'];
            $can_create_subcenter = $user['CanCreateSubCenter'];
            $role_query = " AND Students.Added_For = $id";

            if ($role == 'Sub-Center') {
                $role_query .= " AND Students.University_ID = " . $_SESSION['university_id'];
            } elseif ($role == 'Center') {
                $center_list = [$id];
                $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = $id AND University_ID = " . $_SESSION['university_id']);

                if ($get_sub_center_list && $get_sub_center_list->num_rows > 0) {
                    $sub_center_list = [];
                    while ($gscl = $get_sub_center_list->fetch_assoc()) {
                        $sub_center_list[] = $gscl['User_ID'];
                    }
                    $all_list = array_merge($center_list, $sub_center_list);
                    $all_lists = "(" . implode(",", $all_list) . ")";
                    $role_query = " AND Students.Added_For IN $all_lists";
                }

                $subCenter = $conn->query("SELECT * FROM Center_SubCenter WHERE Center=$id");
                if ($subCenter && $subCenter->num_rows > 0 && $can_create_subcenter == 1) {
                    $subCenterArrId = [];
                    while ($subCenterArr = $subCenter->fetch_assoc()) {
                        $subCenterArrId[] = $subCenterArr['Sub_Center'];
                    }
                    $subCenter_list = "(" . implode(",", $subCenterArrId) . ")";
                    $subcenter_ids = implode(",", $subCenterArrId);

                    $sub_centers = $conn->query("SELECT `ID`, `Code`, `Name`, `Role` FROM Users WHERE ID IN $subCenter_list");
                    $sub_center_name .= "<option value=''>Select Sub Center</option>";
                    while ($subCenterListArr = $sub_centers->fetch_assoc()) {
                        $sub_center_name .= "<option value='" . $subCenterListArr['ID'] . "'>" . $subCenterListArr['Name'] . " (" . $subCenterListArr['Code'] . ")</option>";
                    }
                    $center_sub_center = $subcenter_ids . ',' . $id;
                } elseif ($can_create_subcenter == 0 && $subCenter->num_rows == 0) {
                    $center_sub_center = $id;
                } else {
                    $sub_center_name = "<option value=''>No Record found!</option>";
                }
            }
            $_SESSION['filterByUser'] = $role_query;
        }
    } elseif ($by == 'subjects') {
        $_SESSION['filterBySubjects'] = " AND Subjects.ID = $id";
        $_SESSION['filterSubjectsID'] = $id;
    } elseif ($by == 'assignment_status') {
        if (isset($_SESSION['filterSubjectsID'])) {
            $subject_id = $_SESSION['filterSubjectsID'];
            $students_ids = [];
            if ($id == 1) {
                $submit_sql = " AND id IS NOT NULL";
            } else {
                $submit_sql = "";
            }
            $submitted_count_query = $conn->query("SELECT * FROM Student_Submitted_Assignment WHERE subject_id = $subject_id $submit_sql");
            while ($submitted_count_query && $get_students = $submitted_count_query->fetch_assoc()) {
                $students_ids[] = $get_students['student_id'];
            }
            $stu_ids = implode(',', $students_ids);
            if ($id == 1) {
                $_SESSION['submitted_students'] = ' AND Student_Submitted_Assignment.student_id IN ' . '(' . $stu_ids . ')';
            } else {
                $_SESSION['submitted_students'] = ' AND Students.ID NOT IN ' . '(' . $stu_ids . ')';
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'No subject selected']);
            exit;
        }
    } elseif ($by == 'vertical_type') {
        $users_query = $conn->query("SELECT ID, Name FROM Users WHERE Role='Center' AND Vertical=$id");
        if ($users_query && $users_query->num_rows > 0) {
            $center_option = '<option value="">Select User</option>';
            while ($user = $users_query->fetch_assoc()) {
                $center_option .= '<option value="' . $user['ID'] . '">' . $user['Name'] . '</option>';
            }
        } else {
            $center_option = '<option value="">No Record found</option>';
        }
        $_SESSION['filterByVerticalType'] = " AND Users.Role='Center' AND Users.Vertical = $id";
    }
    echo json_encode(['status' => true, 'subCenterName' => $sub_center_name, 'CenterName' => $center_option]);
}
