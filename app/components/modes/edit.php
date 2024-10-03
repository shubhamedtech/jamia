<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $mode = $conn->query("SELECT ID, Name, University_ID FROM Modes WHERE ID = $id");
  if($mode->num_rows>0){
    $mode = mysqli_fetch_assoc($mode);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <h6>Edit <span class="semi-bold">Mode</span></h6>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <form role="form" id="form-edit-modes" action="/app/components/modes/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $mode['Name'] ?>" placeholder="ex: Sem">
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <i class="ti-save-alt mr-2"></i>
          <span>Update</span>
        </button>
      </div>
    </div>
  </form>

  <script>
    $(function(){
      $('#form-edit-modes').validate({
        rules: {
          name: {required:true},
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

    $("#form-edit-modes").on("submit", function(e){
      if($('#form-edit-modes').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$mode['University_ID']?>');
        formData.append('id', '<?=$mode['ID']?>');
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
              $('#tableModes').DataTable().ajax.reload(null, false);
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
<?php }} ?>
