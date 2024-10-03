<!-- modal -->
<?php
session_start();
require '../../includes/db-config.php';
?>
<div class="modal-body">
    <div class="modal-header">
        <h5 class="mb-0">Downloads Zip Behalf With Selected Filter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <form id="downloadzipbulkform" method="post" action="/app/zip_bulk_downloads/create_zip_stru" enctype="multipart/form-data">
        <div class="row">
            <!-- Center Name -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="center">Center Name</label>
                    <select class="form-control" id="center" name="center">
                        <option value="">Choose Center</option>
                        <?php
                        $sql = "SELECT ID, Name, Code FROM Users WHERE Role='Center' OR Role='Sub-Center' ORDER BY Name";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $centername = $row["Name"];
                                $code = $row['Code'];
                                $centerId = $row["ID"];
                                echo '<option value="' . $centerId . '">' . $centername . '(' . $code . ')' . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Course Type -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="coursetype">Course Type</label>
                    <select class="form-control" id="coursetype" name="coursetype" onchange="getSubjects(this.value);">
                        <option value="">Select Course Type</option>
                        <?php
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

            <!-- Subject -->
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="subject">Subjects</label>
                    <select class="form-control" id="subject_id" name="subject">
                        <option value="">Select Subject</option>
                    </select>
                </div>
            </div>

            <!-- Submit button -->
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary">Download Zip</button>
            </div>
        </div>
    </form>
</div>
</div>
<script>
    function getSubjects(courseId) {
        $.ajax({
            type: 'POST',
            url: '/app/zip_bulk_downloads/get_subject',
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
</script>