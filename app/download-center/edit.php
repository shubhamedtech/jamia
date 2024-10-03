<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';

$files = [];
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM Download_Centers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $files = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<!-- modal -->
<div class="modal-body">
    <div class="modal-header">
        <h5 class="mb-0">Edit Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form id="editfiles" method="post" action="/app/download-center/files_update.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($files['id'] ?? '') ?>" id="id">
        <div class="row">
            <!-- Course Type -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="course_type">Course Type</label>
                    <select class="form-control" id="course_type" name="course_type" onchange="getSubjects(this.value);" required>
                        <option value="">Select Course Type</option>
                        <?php
                        $sql = "SELECT ID, Name FROM Courses";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $courseName = htmlspecialchars($row["Name"]);
                                $courseId = intval($row["ID"]);
                                $selected = ($files && $files['course_id'] == $courseId) ? 'selected' : '';
                                echo '<option value="' . $courseId . '" ' . $selected . '>' . $courseName . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Subject -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="subject">Subjects</label>
                    <select class="form-control" id="subject_id" name="subject_name" required>
                        <option value="">Select Subject</option>
                        <?php
                        if (isset($files['course_id'])) {
                            $course_id = intval($files['course_id']);
                            $subject_sql = "SELECT ID, Name FROM Subjects WHERE Program_Grade_ID = $course_id";
                            $subject_result = $conn->query($subject_sql);
                            if ($subject_result->num_rows > 0) {
                                while ($subject_row = $subject_result->fetch_assoc()) {
                                    $subjectName = htmlspecialchars($subject_row["Name"]);
                                    $subjectId = intval($subject_row["ID"]);
                                    $selected = (isset($files['subjects_id']) && $files['subjects_id'] == $subjectId) ? 'selected' : '';
                                    echo '<option value="' . $subjectId . '" ' . $selected . '>' . $subjectName . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="filesname">File Name</label>
                    <input type="text" class="form-control" id="filesname" value="<?= htmlspecialchars($files['file_reason'] ?? '') ?>" placeholder="Files Name" required name="filesname">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label for="files">File</label>
                    <input type="file" class="form-control" id="files" name="files" accept=".pdf, .jpeg, .jpg, .png, .gif, .mp4, .avi, .mov, .xls, .xlsx">
                    <?php if (isset($files['file_name']) && $files['file_name'] != '') : ?>
                        <hr>
                        <a href="../../uploads/<?= htmlspecialchars($files['file_name']) ?>" target="_blank">View File</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close Files</button>
            <button type="submit" class="btn btn-sm btn-primary">Update Files</button>
        </div>
    </form>
</div>

<script>
    function getSubjects(course_type) {
        $.ajax({
            type: 'POST',
            url: '/app/download-center/get_subject.php',
            data: {
                course_type: course_type
            },
            success: function(response) {
                $('#subject_id').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>

<script>
    $(function() {
        $('#editfiles').validate({
            rules: {
                course_type: {
                    required: true
                },
                subject_name: {
                    required: true
                },
                filesname: {
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

        $("#editfiles").on("submit", function(e) {
            e.preventDefault();
            if ($('#editfiles').valid()) {
                $(':input[type="submit"]').prop('disabled', true);
                var formData = new FormData(this);
                formData.append('id', '<?= $id ?>');
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
                            $('#subjects-table').DataTable().ajax.reload(null, false);
                        } else {
                            $(':input[type="submit"]').prop('disabled', false);
                            notification('danger', data.message);
                        }
                    }
                });
            }
        });
    });
</script>