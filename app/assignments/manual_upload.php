<?php
if (isset($_GET['id'], $_GET['subjectId'], $_GET['assignmentId'])) {
    $student_id = $_GET['id'];
    $subject_id = $_GET['subjectId'];
    $assignment_id = $_GET['assignmentId'];
}
?>
<div class="modal-header">
    <h5 class="modal-title">Upload Assignment Solution</h5>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <form id="uploadForm" enctype="multipart/form-data" action="/app/assignments/teacher_upload_assignment" method="post">
        <div class="form-group">
            <label for="teacher_upload_assignment">Select File</label>
            <input type="file" class="form-control-file" id="teacher_upload_assignment" name="teacher_upload_assignment" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-sm">Upload</button>
        <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Close</button>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#uploadForm').validate({
            rules: {
                teacher_upload_assignment: {
                    required: true
                }
            },
            highlight: function(element) {
                $(element).addClass('error');
                $(element).closest('.form-control').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).removeClass('error');
                $(element).closest('.form-control').removeClass('has-error');
            }
        });
        $("#uploadForm").on("submit", function(e) {
            if ($('#uploadForm').valid()) {
                $(':input[type="submit"]').prop('disabled', true);
                var formData = new FormData(this);
                formData.append('student_id', '<?= $student_id ?>');
                formData.append('type', 'Manual');
                formData.append('subject_id', '<?= $subject_id ?>');
                formData.append('assignment_id', '<?= $assignment_id ?>');
                $.ajax({
                    url: this.action,
                    type: 'post',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(data) {
                        if (data.status == 200) {
                            $('.modal').modal('hide');
                            notification('success', data.message);
                            $('#students_table').DataTable().ajax.reload(null, false);
                        } else {
                            $(':input[type="submit"]').prop('disabled', false);
                            notification('danger', data.message);
                        }
                    }
                });
                e.preventDefault();
            }
        });
    });
</script>