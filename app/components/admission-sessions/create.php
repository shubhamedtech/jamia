<?php if(isset($_GET['university_id'])){
  require '../../../includes/db-config.php';
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <h6>Add <span class="semi-bold">Admission Session</span></h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <form role="form" id="form-add-admission-sessions" action="/app/components/admission-sessions/store" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Exam Session</label>
            <input type="text" name="exam_session" class="form-control" placeholder="ex: July-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Scheme</label>
            <select class="form-control" name="scheme">
              <option value="">Choose</option>
              <?php
                $schemes = $conn->query("SELECT ID, Schemes.Name FROM Schemes WHERE Schemes.Status = 1 AND University_ID = ".$_GET['university_id']."");
                while($scheme = $schemes->fetch_assoc()) { ?>
                  <option value="<?=$scheme['ID']?>"><?=$scheme['Name']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <i class="ti-save mr-2"></i>
          <span>Save</span>
        </button>
      </div>
    </div>
  </form>

  <script>
    $(function(){
      $('#form-add-admission-sessions').validate({
        rules: {
          name: {required:true},
          exam_session: {required:true},
          scheme: {required:true},
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

    $("#form-add-admission-sessions").on("submit", function(e){
      if($('#form-add-admission-sessions').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$_GET['university_id']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status==200){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
            }else{
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php } ?>
