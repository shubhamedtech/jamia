<?php
if (isset($_GET['id'])) {
    require '../../../includes/db-config.php';

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($id));
    $form_query = $conn->query("SELECT form_status FROM Students WHERE ID = $id");
    if ($form_query) {
        $stu = mysqli_fetch_assoc($form_query);
        $formstatus = $stu['form_status'];
    } else {
        echo "Error fetching form Status data.";
        exit();
    }
?>
    <!-- Modal -->
    <div class="modal-header clearfix text-left mb-4">
        <h5>Form Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form role="form" id="form-status" action="/app/applications/formstatus/store" method="POST">
        <div class="modal-body">
            <div class="row clearfix">
                <div class="col-md-12">
                    <div class="form-group form-group-default required">
                        <label>Form Status</label>
                        <select name="formstatus" class="form-control" id="formstatus">
                            <option value="Pending" <?php if ($formstatus == 'Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Send To Board" <?php if ($formstatus == 'Send To Board') echo 'selected'; ?>>Send To Board</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer flex justify-content-between">
            <div class="m-t-10 sm-m-t-10">
                <button type="submit" class="btn btn-primary btn-cons btn-animated from-left">
                    <i class="ti-save-alt mr-2"></i>
                    <span>Update</span>
                </button>
            </div>
        </div>
    </form>
<?php
} else {
    echo "ID not provided.";
}
?>
<script type="text/javascript">
    $(function() {
        $('#form-status').validate({
            rules: {
                formstatus: {
                    required: true
                },
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

    $("#form-status").on("submit", function(e) {
        e.preventDefault();
        if ($('#form-status').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
            var status = $('#formstatus').val();
            formData.append('id', '<?= $id ?>');
            formData.append('formstatus', status);
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
                        $('.table').DataTable().ajax.reload(null, false);
                    } else {
                        $(':input[type="submit"]').prop('disabled', false);
                        notification('danger', data.message);
                    }
                }
            });
        }
    });
</script>