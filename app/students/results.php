<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    @media print {
            .no-print,
            .no-print * {
                display: none !important;
            }
        }
    body {
        font-family: 'Roboto', sans-serif;
        font-size: 14px !important;
        .table-bordered{
            border: 2px solid black !important;
        }

        .tr-bordered{
            border: 2px solid black !important;
        }
    }

    .table-responsive td, .table-responsive th {
        border: 2px solid black !important;
        
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

    
    

    <div class="content-wrapper">
        <div class="content">
            <div class="card card-transparent">
                <div class="card-body" >
                <?php 
                    require '../../includes/db-config.php';
                    $typoArr=["th","st","nd","rd","th","th","th","th","th","th"];
                    $result = $conn->query("SELECT results.id as result_id,Courses.Name as course_name, Courses.Short_Name as course_short_name,Students.*,Admission_Sessions.Name as admission_session, Admission_Sessions.Exam_Session  FROM results left join Courses on Courses.ID = results.course_id  left join Students on Students.ID = results.student_id left join Admission_Sessions on Students.Admission_Session_ID = Admission_Sessions.ID WHERE results.student_id = '".$_SESSION['ID']."' ");
                    if($result->num_rows ==0){
                        echo '<section><div class="container mt-3 mb-3"><div class="row justify-content-center "><h3>Result not published yet! </h3></div></div></section>';
                        die;
                    }else{ 
                        $resultDetail = mysqli_fetch_assoc($result);
                        
                        $results = $conn->query("SELECT Subjects.ID,Subjects.Name,Subjects.Mode,Subjects.Category,Subjects.Type,Subjects.min_marks,Subjects.max_marks,result_marks.obt_marks_ext,result_marks.obt_marks_int,result_marks.obt_marks,result_marks.status,result_marks.remarks FROM Student_Subjects left join result_marks on Student_Subjects.subject_id= result_marks.subject_id left join Subjects on Student_Subjects.subject_id= Subjects.ID WHERE Student_Subjects.student_id = '".$_SESSION['ID']."' AND result_marks.result_id='".$resultDetail['result_id']."' ");
                        
                        
                ?>
                    <section id="content">
                        <div class="container mt-3 mb-3">
                            <div class="row justify-content-center ">
                                <div class="col-lg-10 form-r-border px-5"  style="border:2px solid black !important; height:auto;">
                                    <div class="text-center">
                                        <img src="/uploads/result-logo/form-logo1.jpeg" alt="" width="200" height="150">
                                        <p class="fw-bold">Statement of Marks</p>
                                        <p class="fw-bold"><?=$resultDetail['course_name']?></p>
                                        <p class="fw-bold"><?=$resultDetail['Exam_Session']?></p>
                                    </div>
                                    <div class="row" style="display: flex;justify-content: center;">
                                        <div class="col-md-6" style="width:50% !important;">
                                            <p><span class="fw-bold">Name :</span><span> <?=$resultDetail['First_Name']." ".$resultDetail['Middle_Name']." ".$resultDetail['Last_Name']?></span></p>
                                            <p><span class="fw-bold">Father's Name :</span> <span><?=$resultDetail['Father_Name']?></span></p>
                                            <p><span class="fw-bold">Date of Birth :</span><span><?=date("d-m-Y",strtotime($resultDetail['DOB']))?></span></p>
                                        </div>
                                        <div class="col-md-3 " style="width:25% !important;">
                                            <p><span class="fw-bold">Roll No. :</span><span><?=$resultDetail['OA_Number']?></span></p>   <!--Roll_No-->
                                            <p><span class="fw-bold">Enrollment No. :</span> <span><?=$resultDetail['Enrollment_No']?></span></p>  
                                            
                                        </div>
                                    </div>
                                
                                    <div class="table-responsive">
                                        <table class="table-bordered ">
                                            <thead class="text-center">
                                                <tr class="tr-bordered">
                                                    <th scope="col" colspan="2" style="width:583px !important;height: 50px;">Subject</th>
                                                    <th style="width: 100px;">Max. Marks</th>
                                                    <th style="width: 112px;">Marks Theory</th>
                                                    <th style="width: 103px;">Mark Pract.</th>
                                                    <th style="width: 70px;">Total</th>
                                                    <th style="width: 70px;">Result</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-center ">
                                                <?php 
                                                $total_obtained=0;
                                                $total_max=0;
                                                $obtained_theory=0;
                                                $obtained_practicle=0;
                                                $fail=false;

                                                if($results->num_rows>0){ 
                                                    $i=1;
                                                while($row=mysqli_fetch_assoc($results)){ 
                                                $total_obtained=$total_obtained+$row['obt_marks'];
                                                $total_max= $total_max+$row['max_marks'];
                                                $obtained_theory=$obtained_theory+$row['obt_marks_ext'];
                                                $obtained_practicle=$obtained_practicle+$row['obt_marks_int'];

                                                if($row['status']==0 || $row['remarks']!="Pass"){
                                                    $fail=true;
                                                }
                                                ?>
                                                <tr>
                                                    <td style="width: 126px !important;"><?=$i.$typoArr[$i] ?> Paper</td>
                                                    <td style="width:500px !important;"><?=$row['Name']?></td>
                                                    <td><?=$row['max_marks']?></td>
                                                    <td><?=$row['obt_marks_ext']?></td>
                                                    <td><?=$row['obt_marks_int']?></td>
                                                    <td><?=$row['obt_marks']?></td>
                                                    <td><?=$row['remarks']?></td>

                                                </tr>

                                                <?php $i++;} } 
                                                $percentage=0;
                                                $division="";
                                                if($total_max!=0){ 
                                                    $percentage = ($total_obtained/$total_max) * 100;
                                                }

                                                

                                                if($percentage<33){
                                                    $division="Fail";
                                                }elseif($percentage==33 || $percentage>33 && $percentage<45){
                                                    $division="IIIrd Division";
                                                }elseif($percentage==45 || $percentage>45 && $percentage<60){
                                                    $division="IInd Division";
                                                }elseif($percentage==60 || $percentage>60 && $percentage<75){
                                                    $division="Ist Division";
                                                }else{
                                                    $division="Distinction";
                                                }
                                                
                                                ?>

                                                <tr >
                                                    <td colspan="2">Total</td>
                                                    <td><?=$total_max?></td>
                                                    <td ><?=$obtained_theory?></td>
                                                    <td ><?=$obtained_practicle?></td>
                                                    <td style="border-right:none !important;" colspan="2"><?=$total_obtained?> </td>
                                                </tr>
                                                
                                                <tr >
                                                    <td colspan="2">Grand Total</td>
                                                    <td colspan="3"><?=$total_obtained."/".$total_max?></td>
                                                    <td style="border-right:none !important;"colspan="2"><?=($fail) ? "Fail": $division?> </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                    <!-- <p class="text-justify mb-1 mt-5"><span class="text-dark fw-bold text-justify">Passing Divisions :</span>IIIrd 33% or more; IInd 45% or more; Ist 60% or more ; Distinction 75% or more </p>
                                    <p class="text-dark fw-bold mb-1">Note :</p>
                                    <ul class="list-group-numbered text-justify">
                                        <li>Mark encircled (0) indicate failure in the subject(s) and tick mark indicates distinction in the subject(s.)</li>
                                        <li>The Candidate has to be obtain 33% marks in each paper (all compulsary and optional) and 33% marks in grand total to pass the <span class="ms-3"> exam.</span></li>
                                        <li>RL means regional language recognised by Govt Of India (Assamese 1/Bengali 2/Telgu 3/Kashmiri 4/kannada 5/Marathi <span class="ms-3"> 6/Malyalam  7/Oriya 8/Punjabi 9/Tamil 10/Gujrati 11/Nepali 12/Arabic 13/Persian 14/Sanskrit 15.)</span></li>
                                        <li>Grade description : A - Excellent, B - Very Good, C - Good, D - Satisfactory, E - Unsatisfactory </li>
                                        <li>In case of any error in the marksheet please send it for correction widthin 3 months otherwise the candidate will be reponsible.</li>
                                        <li>Recgnised by the Govt. of India, By the office Memorandum No. 14021/2/28- Estt(D) dated 28, June, 1978 of Ministry of Home <span class="ms-3">Affairs/Home Ministry (Deoartment of Personnel and Administrative Reforms). </span></li>
                                    </ul> -->
                                    <p class="mt-4"><span class="fw-bold"> Disclaimer :</span> The published result is provisional only. Jamia Urdu Aligarh is not responsible for any inadvertent error that may have crept in the data / results being published online. This is being published just for the immediate information to the examinees. The final mark sheet(s) issued by Jamia Urdu Aligarh will only be treated authentic & final in this regard.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php } ?>
                </div>

                <div class="text-center no-print">
                <button type="button" class="btn btn-primary" id="cmd" onclick="printDiv('content')">Download as PDF/Print</button>
                </div>
                <br><br>

            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>

        <script>
            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        </script>