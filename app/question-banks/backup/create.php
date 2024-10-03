<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Upload E-Book</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

</div>
<form role="form" id="form-upload" foemtarget="_blank" action="/app/e-books/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    
    <!-- <div class="row">
      <div class="col-md-12 text-end cursor-pointer" onclick="window.open('/app/samples/subjects');">
        <i class="uil uil-file-download-alt"></i><u><span class="text-primary ml-1">Sample</span></u>
      </div>
    </div> -->
    <?php 
    $unit_id = intval($_GET['unit_id']);
    $syllabus_id = intval($_GET['syllabus_id']);
    $sem = intval($_GET['sem']);
    ?>

    <div class="row">
      <div class="col-md-12">
        <input name="unit_id" type="hidden" value="<?=$unit_id?>" id="unit_id"/>
        <input name="syllabus_id" type="hidden" value="<?=$syllabus_id?>" id="syllabus_id"/>
        <input name="sem" type="hidden" value="<?=$sem?>" id="sem"/>

        <input name="file" type="file" accept="application/pdf" />
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

  $(function(){
    $('#form-upload').validate({
      rules: {
        file: {required:true},
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $('#submit-button').click(function() {
    $('.modal').modal('hide');
        $('#submit-button').prop('disabled', false);
        $('#form-upload').submit(function(e) {
          $('#submit-button').prop('disabled', true);
          var formData = new FormData(this);
          // formData.append('inserted_id', localStorage.getItem('inserted_id'));
          // formData.append('lead_id', '<?php echo isset($_GET['lead_id']) ? $lead_id : 0 ?>');
          e.preventDefault();
          $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                localStorage.setItem('inserted_id', data.id);
              } else {
                notification('danger', data.message);
                $('#previous-button').click();
              }
            },
            error: function(data) {
              notification('danger', 'Server is not responding. Please try again later');
              $('#previous-button').click();
              console.log(data);
            }
          });
        });
  });
      
</script>
