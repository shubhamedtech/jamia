<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Upload Subjects</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-upload" foemtarget="_blank" action="/app/subjects/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="d-flex justify-content-between">
      <div class="form-inline">
        <input name="file" class="form-control-file" type="file" accept="image/*, .pdf, .csv, .doc, .docx, application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
      </div>
      <button class="btn btn-outline-primary" onclick="window.open('/app/samples/subjects');">
        <i class="fa fa-download"></i><u><span class="ml-1">Sample</span></u>
      </button>
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