<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Student Assignment Result</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="resultForm" action="/app/assignments/update_result" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="status">Evaluation Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Not Submitted">Not Submitted</option>
                <option value="Submitted">Submitted</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
        <input type="hidden" name="stu_id" value="<?= $_GET['stu_id'] ?>">
        <div class="form-group">
            <label for="marks">Enter Marks</label>
            <input type="number" class="form-control" id="marks" name="marks" placeholder="Enter Assignment Marks" required>
        </div>
        <div class="form-group">
            <label for="reason">Enter Reason (Comment)</label>
            <input type="text" class="form-control" id="reason" name="reason" placeholder="Enter Reason/Remark" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
    </form>
</div>
<script>
    $(function() {
        $('#resultForm').validate({
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
    $("#resultForm").on("submit", function(e) {
        if ($('#resultForm').valid()) {
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