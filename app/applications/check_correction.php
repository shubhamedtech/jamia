<?php
require '../../includes/db-config.php';
$id = mysqli_real_escape_string($conn, $_GET['id']);
$id = str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($id));

$cehckQuery = 'select *, GROUP_CONCAT(subjects.Name) as subjects from correction_form  left join student_subjects on `student_subjects`.`Student_Id`=correction_form.students_id left join subjects on subjects.ID=student_subjects.Subject_id left join students on students.ID = correction_form.students_id WHERE correction_form.students_id=' . $id . ' group by correction_form.students_id';


$result = $conn->query($cehckQuery);

$studentCorrectionData = mysqli_fetch_array($result);

if (count($studentCorrectionData) > 0) {

?>

    <form action="/app/application/correction/create" method="post" id="correction-form">
        <div class="m-3">
            <div class="mt-3 p-3">
                <h5>Correction For <?php echo implode(' ', [$studentCorrectionData['First_Name'], $studentCorrectionData['Middle_Name'], $studentCorrectionData['Last_Name']]) . ' (' . $studentCorrectionData['Unique_ID'] . ')' ?>
                </h5>
            </div>
            <div class="row g-3">
                <input type="hidden" name="id" value="<?= $id ?> ">
                <div class="col-md-4 form-check complete">
                    <input type="checkbox" id="Student_Name" name="report[]" value="Student Name"
                        onclick="addRemark('Student_Name')">
                    <label for="Student_Name" class="font-weight-bold">Student Name</label>
                </div>
                <div class="col-md-4 form-check complete">
                    <label for="Student_Name" class="font-weight-bold"><?php echo implode(' ', [$studentCorrectionData['First_Name'], $studentCorrectionData['Middle_Name'], $studentCorrectionData['Last_Name']]) ?></label>
                </div>
                <div class="col-lg-4" id="remark_Student_Name">

                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4 form-check complete">
                    <input type="checkbox" id="Father_Name" name="report[]" value="Student Name"
                        onclick="addRemark('Father_Name')">
                    <label for="Father_Name" class="font-weight-bold">Father Name</label>
                </div>
                <div class="col-md-4 form-check complete">
                    <label for="Father_Name" class="font-weight-bold"><?php echo $studentCorrectionData['Father_Name']; ?></label>
                </div>
                <div class="col-lg-4" id="remark_Father_Name">

                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4 form-check complete">
                    <input type="checkbox" id="Mother_Name" name="report[]" value="Student Name"
                        onclick="addRemark('Mother_Name')">
                    <label for="Mother_Name" class="font-weight-bold">Mother Name</label>
                </div>
                <div class="col-md-4 form-check complete">
                    <label for="Mother_Name" class="font-weight-bold"><?php echo $studentCorrectionData['Mother_Name']; ?></label>
                </div>
                <div class="col-lg-4" id="remark_Mother_Name">

                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4 form-check complete">
                    <input type="checkbox" id="DOB" name="report[]" value="DOB"
                        onclick="addRemark('DOB')">
                    <label for="DOB" class="font-weight-bold">DOB</label>
                </div>
                <div class="col-md-4 form-check complete">
                    <label for="DOB" class="font-weight-bold"><?php echo $studentCorrectionData['DOB']; ?></label>
                </div>
                <div class="col-lg-4" id="remark_DOB">

                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-4 form-check complete">
                    <input type="checkbox" id="Subjects" name="report[]" value="Subjects"
                        onclick="addRemark('Subjects')">
                    <label for="Subjects" class="font-weight-bold">Subjects</label>
                </div>
                <div class="col-md-4 form-check complete">
                    <label for="Subjects" class="font-weight-bold" style="    word-wrap: break-word;width: 255px;"><?php echo $studentCorrectionData['subjects'];
                                                                                                                    ?></label>
                </div>
                <div class="col-lg-4" id="remark_Subjects">

                </div>
            </div>
        </div>
        <div class="col-md-4 pull-right mb-3 d-flex flex-row">
            <button type="button" style="margin-left: 3px;margin-right: 13px;" class="btn btn-danger" onclick="submitCoorection('reject')">Reject</button>
            <button type="button" class="btn btn-success" onclick="submitCoorection('approve')">Approve</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            var correctionData = '<?php echo $studentCorrectionData['correction_data']; ?>';
            var correctionArr =JSON.parse(correctionData);
            $.each(correctionArr,function(key,val){
                $('#'+key).prop('checked',true);
                addRemark(key,val);
            })
        });

        function addRemark(id,val="") {
            var inputType = 'input';
            if (id == 'DOB') {
                inputType = 'date';
            }
            var input = '<label>Enter Correct ' + id.replace('_', ' ') + '</label><input type="' + inputType + '" class="form-control" id="remark_for_' + id + '" autocomplete="off" name="remark[' + id + ']" value="'+val+'" placeholder="" required />';
            if ($("#" + id).prop('checked') == true) {
                $("#remark_" + id).html(input);
            } else {
                $("#remark_" + id).html('');
            }
        }

        function submitCoorection(action) {
            var form = $('#correction-form');
            $.ajax({
                url: "/app/applications/correction/approve.php?action="+action,
                type: "post",
                data: form.serialize(),
                dataType: 'json',
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status == 200) {
                        $('.modal').modal('hide');
                        notification('success', data.message);
                        $('.table').DataTable().ajax.reload(null, false);
                    } else {
                        $(':input[type="submit"]').prop('disabled', false);
                        notification('danger', data.message);
                    }
                }
            });
        }
    </script>

<?php
}
?>