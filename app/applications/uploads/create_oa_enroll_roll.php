<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>OA Number, Enrollment No and Roll No</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-upload" foemtarget="_blank" action="/app/applications/uploads/update_oa_enroll_roll" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row justify-content-between">
      <div class="col-md-6">
        <input name="file" class="form-control-file" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
      </div>
      <div class="col-md-6 text-right cursor-pointer" onclick="window.open('/app/samples/oa_enroll_roll');">
        <i class="fa fa-file-excel-o text-primary"></i><u><span class="text-primary ml-2">Sample</span></u>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <i class="fa fa-upload mr-2"></i>
        <span>Upload</span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function() {
    $('#form-upload').validate({
      rules: {
        file: {
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

  $('#submit-button').click(function() {
    $('.modal').modal('hide');
  });
</script>