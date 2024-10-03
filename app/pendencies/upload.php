<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Upload Multiple Pendencies</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-upload" foemtarget="_blank" action="/app/pendencies/upload_store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <input name="file" type="file" accept=".csv" />
      </div>
      <div class="col-md-6 text-right cursor-pointer" onclick="window.open('/app/samples/pendencies');">
        <i class="fa fa-file-excel-o text-primary"></i><u><span class="text-primary ml-2">Sample</span></u>
      </div>
    </div>
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <i class="fa fa-upload mr-2"></i>Upload</button>
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