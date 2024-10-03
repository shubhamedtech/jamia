<?php 
  ini_set('display_errors', 1); 

## Database configuration
require '../../includes/db-config.php';;
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Subjects.ID ASC";
}

$searchQuery = "";

if($searchValue != ""){
    $searchQuery = " AND CONCAT(Subjects.Name, Subjects.Program_Grade_ID, Subjects.Subject_Fee, Subjects.Exam_Fee, Subjects.Toc_Fee, Subjects.Practical_Fee) LIKE '%$searchValue%'";
}

$sql_query = "SELECT Subjects.ID, Subjects.Name,  Subjects.Type, Courses.Name as Grade, Subjects.Mode, Subjects.Category, Subjects.Subject_Fee, Subjects.Exam_Fee, Subjects.Toc_Fee, Subjects.Practical_Fee, Subjects.Registration_Fee, Subjects.Total_Fee FROM Subjects LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID WHERE Subjects.ID IS NOT NULL  AND Courses.University_ID = ".$_SESSION['university_id']." $searchQuery $orderby LIMIT $row, $rowperpage";
// print_r($sql_query);
$results = mysqli_query($conn, $sql_query);
$data = [];
while($row = mysqli_fetch_assoc($results)) {
    $data[] = array(
        "Name" => $row["Name"],
        "Grade" => $row["Grade"],
        "Mode" => $row["Mode"],
      	"Type" => $row["Type"] == 1 ? "Default" : 'Optional',
        "Category" => $row["Category"],
        "Subject_Fee" => $row["Subject_Fee"],
        "Exam_Fee" => $row["Exam_Fee"],
        "Toc_Fee" => $row["Toc_Fee"],
        "Practical_Fee" => $row["Practical_Fee"],
      	"Registration_Fee" => $row["Registration_Fee"],
        "Total_Fee" => $row["Total_Fee"],
        "ID" => $row["ID"],

    );
}

$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => count($data),
    "iTotalDisplayRecords" => count($data),
    "aaData" => $data
);

echo json_encode($response);