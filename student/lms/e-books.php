<?php require '../../includes/db-config.php';  ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .stu-e-book-style {
    width: 300px;
    height: 150px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #F3B95F;
  }

  .e-book-icon {
    margin-top:15px;
    font-size: 80px;
    text-align: center;
    color: white;
  }
  .subject_name{
    font-size: 18px !important;
    font-weight: 600;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php 

$base_url="http://".$_SERVER['HTTP_HOST']."/";
$course_id=$_SESSION['Course_ID'];
$student_id=$_SESSION['ID'];

$studentSubjects = "SELECT Student_Subjects.`Subject_id` as subject_id,Subjects.Name from Student_Subjects LEFT JOIN Subjects ON Subjects.ID = Student_Subjects.Subject_id  WHERE Student_Subjects.Student_id = $student_id ";
$Subjects = mysqli_query($conn, $studentSubjects);
$mySubjects=array();
$subjectData=array();
while ($row = mysqli_fetch_assoc($Subjects)) {
  $mySubjects[]= $row['subject_id'];
  $subjectData[]=$row;
}

$query="SELECT e_books.`id`, e_books.`file_type`, e_books.`file_path`, Courses.`Name` as course_name, Courses.`Short_Name` as course_short_name, Subjects.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Courses ON Courses.ID = e_books.course_id LEFT JOIN Subjects ON Subjects.ID = e_books.subject_id WHERE e_books.subject_id IN ('" . implode("','", $mySubjects) . "')  AND e_books.status=1 AND e_books.course_id=$course_id ";

$results = mysqli_query($conn, $query);
$eBookData=array();
while ($row = mysqli_fetch_assoc($results)) {
  $eBookData[]= $row;
}

?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

    <div class="content-wrapper">
        <!-- <div class="content-header sty-one">
            <div class="d-flex align-items-center justify-content-between">

                <?php 
                // $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                // for ($i = 1; $i <= count($breadcrumbs); $i++) {
                //     if (count($breadcrumbs) == $i) : $active = "active";
                //         $crumb = explode("?", $breadcrumbs[$i]);
                //         echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
                //     endif;
                // }
                ?>
            </div>
        </div> -->


        <div class="content">
            <div class="card">
            <div class="card-header">
                <?php 
                  $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                  for ($i = 1; $i <= count($breadcrumbs); $i++) {
                    if (count($breadcrumbs) == $i) : $active = "active";
                      $crumb = explode("?", $breadcrumbs[$i]);
                      echo '<h4 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h4>';
                    endif;
                  }
                ?>
                
                <div class="row pull-right">
                    <div class="col-xs-7 " style="margin-right: 10px;">
                        <div class="form-group  required">
                            <select class="form-control"  onchange="subjectFilter(this.value)" style="width: 200px;">
                                <option value="">Subject</option>
                                <?php foreach($subjectData as $subj){ ?>
                                    <option value="<?=$subj['subject_id']?>"><?=$subj['Name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                   
                  <!-- <div class="col-xs-7" style="margin-right: 10px;">
                    <input type="text" id="Courses-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
                  </div> -->
                </div>
                <div class="clearfix"></div>
              </div>

                <div class="card-body">
                  <div class="row" id="data_list">
                    <?php  foreach($eBookData as $eBook){  ?>
                      <div class="col-sm-6 col-md-3 mb-3 " >
                        <div class="stu-e-book-style" ><p><i class="icon-book-open e-book-icon" ></i></p>
                        <p class="subject_name"><span ><?php echo $eBook['subject_name']; ?></span></p>
                        </div>
                        <p class="mt-2 " style="text-align:center;"><a class="btn btn-dark" href="/student/lms/view-e-book?id=<?php echo $eBook['id']; ?>" >View </a></p>
                      </div>
                      <?php } ?>
                  </div> 
                </div>
              
            </div>
        </div>
    </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>

<script type="text/javascript">

    function subjectFilter(subject_id){
        
        $.ajax({
          url: '/app/e-books/students/show-list',
          type: 'POST',
          dataType:'text',
          data: {
            "subject_id": subject_id,
            'course_id':"<?=$course_id?>"
          },
          success: function(result) {
            if(result!=false){
                $('#data_list').html(result);
            } else{
                $('#data_list').html('<div class="col-md-12"><h3 style="text-align: center;">Data not available!</h3></div>');
            }
          }
        })
    }
    
    function E_bookList(id, unit_id, sub_id, ) {
    
    $.ajax({
        url: '/app/e-books/students/show-list',
        type: 'POST',
        data: {
        "id": id,
        "sub_id": sub_id,
        "unit_id": unit_id
        },
        success: function(data) {
        $("#lg-modal-content").html(data);
        $("#lgmodal").modal('show');
        }
    })
    }
    </script>
    