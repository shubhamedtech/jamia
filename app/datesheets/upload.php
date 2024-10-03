<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Upload Date-Sheet</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-upload" formtarget="_blank"  method="post" enctype="multipart/form-data">
  <div class="modal-body">

    <div class="row">
      <div class="col-md-12 text-end cursor-pointer" onclick="window.open('/app/samples/datesheet');">
        <i class="fa fa-file-download"></i><u><span class="text-primary ml-1">Sample</span></u>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <input name="file" type="file" accept=".csv" />
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
  });

  $("#submit-button").on('click', function(e) {
    var formdata = new FormData($("#form-upload")[0]);
    e.preventDefault();
    $('.modal').modal('hide');
    $.ajax({
      url: '/app/datesheets/upload_store',
      type: "post",
      processData: false, // important
      contentType: false, // important
      data: formdata,
      success: function(data) {
        var blob=new Blob([data]);
        var link=document.createElement('a');
        link.href=window.URL.createObjectURL(blob);
        link.download="sample_status.csv";
        link.click();
        $("#datesheets").dataTable(settings());
      }
    })
  });
</script>