<?php
if (isset($_GET['subject_id']) && isset($_GET['id'])) {
?>
    <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel">Upload Assignment AnswerSheet</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form id="uploadForm" action="/app/assignments/student-upload-assignment" method="post" enctype="multipart/form-data">
        <input type="hidden" name="uploaded_type" value="Online">
        <input type="hidden" name="subject_id" value="<?= $_GET['subject_id'] ?>" id="subject_id">
        <input type="hidden" name="assignment_id" value="<?= $_GET['id'] ?>" id="assignment_id">
        <div class="modal-body">
            <div class="form-group">
                <label for="assignmentFile">Select File (PDF, JPEG, JPG, PNG, GIF): AND Less Then 20 MB</label>
                <input type="file" class="form-control-file" id="assignmentFile" name="assignment_files[]" accept=".pdf, .jpeg, .jpg, .png, .gif" multiple required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            <button type="submit" name="upload_assignment" class="btn btn-sm btn-primary">Upload</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('#uploadForm').validate({
                rules: {
                    'assignment_files[]': {
                        required: true,
                        accept: "application/pdf,image/jpeg,image/jpg,image/png,image/gif"
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
                e.preventDefault();
                if ($('#uploadForm').valid()) {
                    $(':input[type="submit"]').prop('disabled', true);
                    var formData = new FormData(this);
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'post',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: "json",
                        success: function(data) {
                            console.log(data);
                            if (data.status === 200) {
                                $('.modal').modal('hide');
                                notification('success', data.message);
                                $('#students_table').DataTable().ajax.reload(null, false);
                            } else {
                                $(':input[type="submit"]').prop('disabled', false);
                                notification('danger', data.message);
                            }
                        },
                        error: function() {
                            $(':input[type="submit"]').prop('disabled', false);
                            notification('danger', 'Error occurred during file upload.');
                        }
                    });
                }
            });
        });
    </script>
<?php
}
?>