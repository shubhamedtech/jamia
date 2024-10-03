<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
$assignment = [];
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM Student_Assignment WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $assignment = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!-- modal -->
<div class="modal-body">
    <div class="modal-header">
        <h5 class="mb-0">Edit Assignment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <form id="editassignmentform" method="post" action="/app/assignments/teacher-update-assignment" enctype="multipart/form-data">
        <div class="row">
            <!-- Course Type -->
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="coursetype">Course Type</label>
                    <select class="form-control" id="coursetype" name="coursetype" onchange="getSubjects(this.value);" required>
                        <option value="">Select Course Type</option>
                        <?php
                        $sql = "SELECT ID, Name FROM Courses";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $courseName = htmlspecialchars($row["Name"]);
                                $courseId = intval($row["ID"]);
                                $selected = ($assignment && $assignment['course_id'] == $courseId) ? 'selected' : '';
                                echo '<option value="' . $courseId . '" ' . $selected . '>' . $courseName . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Subject -->
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="subject">Subjects</label>
                    <select class="form-control" id="subject_id" value="<?= $assignment['subject_id'] ?>" name="subject" required>
                        <option value="">Select Subject</option>
                        <?php
                        if (isset($assignment['course_id'])) {
                            $course_id = intval($assignment['course_id']);
                            $subject_sql = "SELECT ID, Name FROM Subjects WHERE Program_Grade_ID = $course_id";
                            $subject_result = $conn->query($subject_sql);
                            if ($subject_result->num_rows > 0) {
                                while ($subject_row = $subject_result->fetch_assoc()) {
                                    $subjectName = htmlspecialchars($subject_row["Name"]);
                                    $subjectId = intval($subject_row["ID"]);
                                    $selected = ($assignment['subject_id'] == $subjectId) ? 'selected' : '';
                                    echo '<option value="' . $subjectId . '" ' . $selected . '>' . $subjectName . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Assignment Name -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="assignmentname">Assignment Name</label>
                    <input type="text" class="form-control" id="assignmentname" value="<?= $assignment['assignment_name'] ?>" placeholder="Assignment Name" required name="assignmentname">
                </div>
            </div>

            <!-- Total Assignment Marks -->
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="marks">Total Assignment Marks</label>
                    <input type="number" class="form-control" id="marks" value="<?= $assignment['marks'] ?>" placeholder="Marks" required name="marks">
                </div>
            </div>

            <!-- Assignment File -->

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="files">Question Assignment File</label>
                    <input type="file" class="form-control" id="files" name="files" accept=".pdf, .jpeg, .jpg">
                    <?php if (isset($assignment['assignment_file']) && $assignment['assignment_file'] != '') : ?>
                        <p>
                            <a href="../../uploads/<?= htmlspecialchars($assignment['assignment_file']) ?>" target="_blank">View Assignment File</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Start Date -->
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="startdate">Start Date</label>
                    <input type="date" class="form-control" id="startdate" value="<?= $assignment['start_date'] ?>" required name="startdate">
                </div>
            </div>

            <!-- End Date -->
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="enddate">End Date</label>
                    <input type="date" class="form-control" id="enddate" value="<?= $assignment['end_date'] ?>" required name="enddate">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close Assignment</button>
            <button type="submit" class="btn btn-sm btn-primary">Update Assignment</button>
        </div>
    </form>
</div>
</div>

<script>
    function getSubjects(courseId) {
        $.ajax({
            type: 'POST',
            url: '/app/assignments/get_subject',
            data: {
                courseId: courseId
            },
            success: function(response) {
                $('#subject_id').html(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function setMinDate() {
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('startdate').min = today;
        document.getElementById('enddate').min = tomorrow;
        document.getElementById('enddate').addEventListener('change', function() {
            var endDate = document.getElementById('enddate').value;
            document.getElementById('startdate').min = endDate;
        });
    }
    document.addEventListener('DOMContentLoaded', setMinDate);
    $(document).ready(function() {
        $("#startdate").on("change", function() {
            $("#enddate").attr("min", $(this).val());
        });
    });
</script>
<script>
    $(function() {
        $('#editassignmentform').validate({
            rules: {
                coursetype: {
                    required: true
                },
                subject: {
                    required: true
                },
                assignmentname: {
                    required: true
                },
                marks: {
                    required: true
                },
                files: {
                    required: true
                },
                startdate: {
                    required: true
                },
                enddate: {
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
    });
    $("#editassignmentform").on("submit", function(e) {
        if ($('#editassignmentform').valid()) {
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
            e.preventDefault();
        }
    });
</script>