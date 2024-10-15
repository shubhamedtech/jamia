<?php
if (isset($_POST['id']) && isset($_POST['by'])) {
  session_start();
  require '../../includes/db-config.php';

  $by = $_POST['by']; // courses
  $role_type = isset($_POST['role']) ? $_POST['role'] : NULL;
  $id = intval($_POST['id']); // 

  $center_name = "";
  $sub_center_name = "";
  $center_sub_center = '';
  if ($by == 'departments') {
    $courseIds = $conn->query("SELECT GROUP_CONCAT(ID) as ID FROM Courses WHERE Department_ID = $id AND University_ID = " . $_SESSION['university_id']);
    if ($courseIds->num_rows > 0) {
      $courseIds = $courseIds->fetch_assoc();
      $courseIds = $courseIds['ID'];
      $_SESSION['filterByDepartment'] = !empty($courseIds) ? " AND Students.Course_ID IN ($courseIds)" : " AND Students.ID IS NULL";
    } else {
      $_SESSION['filterByDepartment'] = " AND Students.ID IS NULL";
    }
  } elseif ($by == 'courses') {
    $_SESSION['filterByCourses'] = " AND Students.Course_ID = $id";
  } elseif ($by == 'sub_courses') {
    $_SESSION['filterBySubCourses'] = " AND Students.Sub_Course_ID = $id";
  } elseif ($by == 'verticals') {
    $vertical_center = $conn->query("SELECT ID, CONCAT(UPPER(Name), ' (', Code, ')') as Name FROM Users WHERE Role = 'Center' And Vertical = $id ORDER BY Code ASC;");
    if ($vertical_center->num_rows > 0) {
      $options = '<option value="">Select</option>';
      $ids = [];
      while ($center = $vertical_center->fetch_assoc()) {
        $options .= '<option value="' . $center['ID'] . '">' . $center['Name'] . '</option>';
        $ids[] = $center['ID'];
      }
      $ids_list = "(" . implode(",", $ids) . ")";
      $_SESSION['filterByVertical'] = " AND Students.Added_For IN $ids_list";
      $center_name = $options;
    }
    $_SESSION['filterByUser'] = '';
  } elseif ($by == 'users') {
    $user = $conn->query("SELECT Role,CanCreateSubCenter FROM Users WHERE ID = $id");
    $user = $user->fetch_assoc();
    $role = $user['Role'];
    $can_create_subcenter = $user['CanCreateSubCenter'];
    $role_query = " AND Students.Added_For = $id";
    // print_r($role_query);
    // die();
    if ($role == 'Counsellor') {
      $center_list = array($id);
      $sub_center_list = array();

      $sub_counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (" . implode(",", $center_list) . ") AND University_ID = " . $_SESSION['university_id']);
      while ($sub_counsellor = $sub_counsellors->fetch_assoc()) {
        $center_list[] = $sub_counsellor['User_ID'];
      }

      $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_Counsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Counsellor_ID = $id");
      if ($get_all_center->num_rows > 0) {
        while ($gac = $get_all_center->fetch_assoc()) {
          $center_list[] = $gac['Code'];
        }
        $center_lists = "(" . implode(",", $center_list) . ")";

        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND Students.Added_For IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND Students.Added_For IN $all_lists";
          } else {
            $role_query = " AND Students.Added_For IN $center_lists";
          }
        }
      }
    } elseif ($role === 'Sub-Counsellor') {
      $center_list = array($id);
      $sub_center_list = array();
      $get_all_center = $conn->query("SELECT DISTINCT(Code) as Code FROM Alloted_Center_To_SubCounsellor WHERE University_ID = '" . $_SESSION['university_id'] . "' AND Sub_Counsellor_ID = $id");
      if ($get_all_center->num_rows > 0) {
        while ($gac = $get_all_center->fetch_assoc()) {
          $center_list[] = $gac['Code'];
        }
        $center_lists = "('" . implode("','", ($center_list)) . "')";
        if ($_SESSION['unique_center'] == 1) {
          $role_query = " AND Students.Added_For IN $center_lists";
        } else {
          $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting IN $center_lists");
          if ($get_sub_center_list->num_rows > 0) {
            while ($gscl = $get_sub_center_list->fetch_assoc()) {
              $sub_center_list[] = $gscl['User_ID'];
            }
            $all_list = array_merge($center_list, $sub_center_list);
            $all_lists = "(" . implode(",", $all_list) . ")";
            $role_query = " AND Students.Added_For IN $all_lists";
          } else {
            $role_query = " AND Students.Added_For IN $center_lists";
          }
        }
      }
    } elseif ($role === 'Center') {
      $center_list[] = $id;
      $get_sub_center_list = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = $id AND University_ID = " . $_SESSION['university_id']);
      if ($get_sub_center_list->num_rows > 0) {
        while ($gscl = $get_sub_center_list->fetch_assoc()) {
          $sub_center_list[] = $gscl['User_ID'];
        }
        $all_list = array_merge($center_list, $sub_center_list);
        $all_lists = "(" . implode(",", ($all_list)) . ")";

        $role_query = " AND Students.Added_For IN $all_lists";
        $centerId = $all_list;
      }


      $subCenter = array();

      $subCenter = $conn->query("SELECT * FROM Center_SubCenter WHERE Center=$id");
      if ($subCenter->num_rows > 0 && $can_create_subcenter == 1) {
        $subCenterArrId = array();
        while ($subCenterArr = $subCenter->fetch_assoc()) {
          $subCenterArrId[] = $subCenterArr['Sub_Center'];
        }
        $subCenter_list = "(" . implode(",", $subCenterArrId) . ")";
        $subcenter_ids = implode(",", $subCenterArrId);

        $sub_centers = $conn->query("SELECT `ID`, `Code`, `Name`, `Role` FROM Users  WHERE ID IN $subCenter_list");
        $sub_center_name .= "<option value=''>Select Sub Center</option>";
        while ($subCenterListArr = $sub_centers->fetch_assoc()) {
          $sub_center_name .= "<option value='" . $subCenterListArr['ID'] . "'>" . $subCenterListArr['Name'] . "(" . $subCenterListArr['Code'] . ")</option>";
        }
        $center_sub_center = $subcenter_ids . ',' . $id;
      } else if ($can_create_subcenter == 0 && $subCenter->num_rows == 0) {
        $center_sub_center = $id;
      } else {
        $sub_center_name = "<option value=''>No Record found!</option>";
      }
    } elseif ($role == 'Sub-Center') {
      $role_query = " AND Students.Added_For = $id AND Students.University_ID = " . $_SESSION['university_id'];
    }
































    //  $_SESSION['search_id']= $id;
    //  $_SESSION['filterByCenter'] = $center_sub_center;
    if ($role_type == 'center') {
      if (!empty($center_sub_center)) {
        $_SESSION['filterByUser'] = " AND Students.Added_For IN ($center_sub_center)";
      } else {
        $_SESSION['filterByUser'] = " AND Students.Added_For IN ('$center_sub_center')";
      }
    } else {
      $_SESSION['filterByUser'] = $role_query;
    }
  } elseif ($by == 'date') {
    $startDate = date("Y-m-d 00:00:00", strtotime($_POST['startDate']));
    $endDate = date("Y-m-d 23:59:59", strtotime($_POST['endDate']));

    $_SESSION['filterByDate'] = " AND Students.Process_By_Center BETWEEN '$startDate' AND '$endDate'";
  } elseif ($by == 'application_status') {
    if ($id == 1) {
      $_SESSION['filterByStatus'] = " AND Document_Verified IS NOT NULL ";
    } elseif ($id == 2) {
      $_SESSION['filterByStatus'] = " AND Payment_Received IS NOT NULL ";
    } elseif ($id == 3) {
      $_SESSION['filterByStatus'] = " AND Document_Verified IS NOT NULL AND Payment_Received IS NOT NULL ";
    }
  }
  // echo json_encode(['status' => true]);
  echo json_encode(['status' => true, 'subCenterName' => $sub_center_name, 'centerName' => $center_name]);
}
