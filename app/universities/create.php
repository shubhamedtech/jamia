<link rel="stylesheet" href="../../assets/plugins/dropify/dropify.min.css">
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Add Board</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<form role="form" id="form-add-university" action="/app/universities/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Board Dealing with</label>
          <select class="form-control" name="university_type" id="university_type">
            <option value="0">Outsourced Partners</option>
            <option value="1">Inhouse i.e. Students</option>
            <option value="2">Both</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: XYZ University" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: XYZU" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Vertical</label>
          <input type="text" name="vertical" class="form-control" placeholder="ex: Technical" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Address</label>
          <textarea name="address" class="form-control" rows="2" placeholder="ex: 23 Street, California, USA 681971" required></textarea>
        </div>
      </div>
    </div>

    <div class="row mb-2">
      <div class="col-md-12">
        <label>Logo*</label>
        <input type="file" name="logo" class="dropify" accept="image/png, image/jpg, image/jpeg, image/svg" required>
      </div>
    </div>

    <div class="row justify-content-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary">
          <i class="ti-save mr-2"></i> Save</button>
      </div>
    </div>
</form>
<script src="../../assets/plugins/dropify/dropify.min.js"></script>
<script>
  $('.dropify').dropify();
</script>
<script>
  $(function() {
    $('#form-add-university').validate({
      rules: {
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        vertical: {
          required: true
        },
        address: {
          required: true
        },
        logo: {
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

  $("#form-add-university").on("submit", function(e) {
    if ($('#form-add-university').valid()) {
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
            $('#universities-table').DataTable().ajax.reload(null, false);
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