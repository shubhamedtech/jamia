<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['assignment_id']) && !empty($_POST['assignment_id'])) {
    require $_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php';
    $assignment_id = intval($_POST['assignment_id']);
    $sql = "SELECT * FROM Student_Assignment_Result WHERE assignment_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $response = [
            'status' => 0,
            'message' => 'Database error: unable to prepare statement'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    $stmt->bind_param('i', $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $response = [
            'status' => 1,
            'message' => 'Data loaded successfully',
            'data' => $data
        ];
?>
        <div class="modal-header">
            <h5 class="modal-title" id="myModalLabel">Student Assignment Update Result</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="editassignmentform" action="/app/assignments/update_assignment_result" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($data['id']); ?>">
                <div class="form-group">
                    <label for="status">Evaluation Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Not Submitted" <?php if ($data['status'] == 'Not Submitted') echo 'selected'; ?>>Not Submitted</option>
                        <option value="Submitted" <?php if ($data['status'] == 'Submitted') echo 'selected'; ?>>Submitted</option>
                        <option value="Approved" <?php if ($data['status'] == 'Approved') echo 'selected'; ?>>Approved</option>
                        <option value="Rejected" <?php if ($data['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="marks">Enter Marks</label>
                    <input type="number" class="form-control" id="marks" name="marks" placeholder="Enter Assignment Marks" value="<?php echo htmlspecialchars($data['obtained_mark']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="reason">Enter Reason</label>
                    <input type="text" class="form-control" id="reason" name="reason" placeholder="Enter Reason/Remark" value="<?php echo htmlspecialchars($data['remark']); ?>" required>
                </div>
                <button type=" submit" id="update" class="btn btn-primary btn-sm">Update</button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </form>
        </div>
<?php
    } else {
        $response = [
            'status' => 0,
            'message' => 'Assignment not found'
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    $stmt->close();
} else {
    $response = [
        'status' => 0,
        'message' => 'Invalid assignment ID'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<script>
    $(function() {
        $('#editassignmentform').validate({
            rules: {
                status: {
                    required: true
                },
                marks: {
                    required: true
                },
                reason: {
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
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#editassignmentform").on("submit", function(e) {
            if ($('#editassignmentform').valid()) {
                $(':input[type="submit"]').prop('disabled', true);
                var formData = new FormData(this);
                formData.append('assignment_id', '<?= $_GET['assignment_id']; ?>');
                $.ajax({
                    url: this.action,
                    type: 'post',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
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
    });
</script>