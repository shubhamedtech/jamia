<!-- modal -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
?>
<div class="modal-body">
    <div class="modal-header">
        <!-- <h5 class="mb-0">Create Assignment</h5> -->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <form id="centerform" method="post" action="/app/download-center/store" enctype="multipart/form-data">
        <div class="row">
            <!-- Course Type -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="coursetype">Course Type</label>
                    <select class="form-control" id="coursetype" name="coursetype" onchange="getSubjects(this.value)">
                        <option value="">Select Course Type</option>
                        <?php
                        require '../../includes/db-config.php';
                        $sql = "SELECT ID, Name FROM Courses";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $courseName = $row["Name"];
                                $courseId = $row["ID"];
                                echo '<option value="' . $courseId . '">' . $courseName . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group form-group-default required">
                    <label>Subject *</label>
                    <select class="form-control" id="subject_id" name="subject_id">
                        <option value="">Select</option>
                        <?php
                        $subjects = $conn->query("SELECT ID,name FROM Subjects");
                        while ($subject = $subjects->fetch_assoc()) { ?>
                            <option value="<?= $subject['ID'] ?>">
                                <?= $subject['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Subject -->

            <!-- Assignment Name -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="filename">File Name</label>
                    <input type="text" class="form-control" id="filename" placeholder="File Name" required name="filename">
                </div>
            </div>

            <!-- Total Assignment Marks -->
            <!-- Assignment File -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="files">File</label>
                    <input type="file" class="form-control" id="files" required name="files" accept=".pdf, .jpeg, .jpg, .png, .gif, .mp4, .avi, .mov, .xls, .xlsx">
                </div>
            </div>

            <!-- Start Date -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close Files</button>
            <button type="submit" class="btn btn-sm btn-primary">Save Files</button>
        </div>
    </form>
</div>
</div>
<script>
    $(function() {
        $('#centerform').validate({
            rules: {
                coursetype: {
                    required: true
                },
                subject_id: {
                    required: true
                },
                filename: {
                    required: true
                },
                files: {
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
    })
    $("#centerform").on("submit", function(e) {
        if ($('#centerform').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
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
<script>
    function getSubjects(course_type) {
        $.ajax({
            url: '/app/download-center/get_subject',
            type: 'POST',
            dataType: 'text',
            data: {
                'course_type': course_type
            },
            success: function(result) {
                $('#subject_id').html(result);
            }
        })
    }
</script>
<script>

</script>