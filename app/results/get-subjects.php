
<?php
// get courses
if ( isset($_POST['course_id']) && isset($_POST['student_id'])) {

    require '../../includes/db-config.php';
    $course_id = intval($_POST['course_id']);
    $student_id = intval($_POST['student_id']);
    
    
    //$get_subject_query = $conn->query("SELECT m.*, s.ID,s.Name as subject_name FROM Subjects AS s LEFT JOIN marksheets AS m ON m.subject_id =s.ID WHERE m.enrollment_no='$enrollment_no' AND s.Course_ID=$course_id AND s.Sub_Course_ID =$sub_course_id  AND s.Scheme_ID=$scheme_id  AND s.University_ID = $university_id  $sqlQuery AND m.status = 1");
    $html = '';
    // if($get_subject_query->num_rows ==0){
        $get_subject_query = $conn->query("SELECT s.ID, s.Name as subject_name FROM Student_Subjects left join Subjects AS s on Student_Subjects.Subject_id=s.ID  WHERE Student_Subjects.Student_id=$student_id ");
        
    // }else{
    //     $html.= '<input type="hidden" name="enrollment_no" class="form-control" value="'.$enrollment_no.'" >';
    // }
    
    $html .= '<div class="row mt-3 mb-2" >
                <div class="col-md-4">
                    <div class="form-group" style="border:unset">
                        <label style="font-weight:700;">Subject</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group form-group-default required" style="border:unset">
                        <label style="font-weight:700;">Internal Marks </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default required" style="border:unset">
                        <label style="font-weight:700;">External Marks </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default required" style="border:unset">
                        <label style="font-weight:700;">Total </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default required" style="border:unset">
                        <label style="font-weight:700;">Remark </label>
                    </div>
                </div>
                
             </div>';

    if ($get_subject_query->num_rows > 0) {
        while ($row = $get_subject_query->fetch_assoc()) {
            // echo "<pre>";print_r($row);
            $row['max_marks_int'] = isset($row['max_marks_int']) ? $row['max_marks_int'] : NULL;
            $row['max_marks_ext'] = isset($row['max_marks_ext']) ? $row['max_marks_ext'] : NULL;
            $row['obt_marks'] = isset($row['obt_marks']) ? $row['obt_marks'] : NULL;


            $html .= '<div class="row">
                <div class="col-md-4">
                    <div class="form-group" >
                    <label>'.$row['subject_name'].'</label>
                        <input type="hidden" name="subject_id[]" class="form-control" value="'.$row['ID'].'" >
                        
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group form-group-default ">
                        <input type="number" name="max_marks_int[]" value="'.$row['max_marks_int'].'" placeholder="Internal Marks" minimum="0" class="form-control"  onkeyup="getTotal(this)" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default ">
                        <input type="number" name="max_marks_ext[]" value="'.$row['max_marks_ext'].'" placeholder="External Marks" minimum="0" class="form-control"  onkeyup="getTotal(this)" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default ">
                        <input type="number" name="obt_marks[]"  value="'.$row['obt_marks'].'"  placeholder="Total" class="form-control total" readonly>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group form-group-default ">
                        <input type="text" name="remarks[]"    placeholder="Pass" class="form-control " required>
                    </div>
                </div>
             </div>';
        }
    } else {
        $html .= '<div class="row"><div class="col-md-12 "> <div class="form-group form-group-default" style="border:unset"><p style="font-size: 18px;text-align: center;font-weight: 700;">No Suject Found! <p></div></div></div>';
    }
    echo $html;
}
?>
<script>
    function getTotal(input){
        var row = input.closest('.row');
        var intMarks = parseInt(row.querySelector('input[name="max_marks_int[]"]').value) || 0;
        var extMarks = parseInt(row.querySelector('input[name="max_marks_ext[]"]').value) || 0;
        var total = intMarks + extMarks;
        row.querySelector('input[name="obt_marks[]"]').value = total;
    }
</script>