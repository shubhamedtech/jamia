<!-- Modal -->
<div class="modal-header clearfix text-left">
    <h5>Add Subject</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>

    <div class="modal-body">
      <form role="form" id="create_subjects" foemtarget="_blank" action="/app/grade-subjects/store" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-5">
                <div class="form-group form-group-default required">
                    <label>Grade</label>
                    <select class="form-control" name="grade" id="subject_type">
                        <?php 
                         require '../../includes/db-config.php';
                         session_start();
                         $courses = $conn->query("SELECT * FROM Courses WHERE University_ID = ".$_SESSION['university_id']."");
                         while($course = mysqli_fetch_assoc($courses)) {
                        ?>
                        <option value="<?=$course['ID']?>"><?=$course['Name']?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group form-group-default required">
                    <label>Subject Category</label>
                    <select class="form-control" name="subject_catagory" id="subject_catagory" required>
                        <option value="">Select</option>
                        <option value="Language">Language Subject</option>
                        <option value="Others">Others</option>
                        <option value="Elective">Elective</option>
                        <option value="optional">Optional</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group form-group-default required">
                    <label>Type</label>
                    <select class="form-control" name="subject_type" id="subject_type">
                      	<option value="">Choose</option>
                        <option value="Default">Default</option>
                        <option value="optional">Optional</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="ex: Hindi" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Subject Mode</label>
                    <select class="form-control" name="subject_mode" id="subject_mode" >
                        <option value="Theory">Theory</option>
                        <option value="Practicle">Practical</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Subject Fee</label>
                    <input type="number" name="subject_fee" id="subject_fee" class="form-control" placeholder="ex: 5000">
                </div>
            </div>
           <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>TOC Fee</label>
                    <input type="number" name="toc_fee"  id="toc_fee" class="form-control" placeholder="ex: 200">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" id="practical_fee_div">
                <div class="form-group form-group-default required">
                    <label>Practical Fee</label>
                    <input type="number" name="practical_fee" id="practical_fee" class="form-control" placeholder="ex: 200">
                </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-md-4 m-t-10 sm-m-t-10">
                <button aria-label="" type="submit" id="submit_button" onclick="submitForm();" class="btn btn-primary">
                    <i class="ti-save mr-2"></i> Save</button>
            </div>
        </div>
      </form>
      </div>
<script>
  $('#practical_fee_div').hide();
  $('#subject_mode').change(function(){
     let mode = $('#subject_mode').val();
    if(mode == "Practicle"){
      $('#practical_fee_div').show();
    }else {
      $('#practical_fee_div').hide();
    } 
  });
</script>
<script>
  function submitForm() {
        $('#submit-button').prop('disabled', false);
        $('#create_subjects').submit(function(e) {
          $('#submit-button').prop('disabled', true);
          var formData = new FormData(this);
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
              } else {
                notification('danger', data.message);
              }
            },
            error: function(data) {
              notification('danger', data.message);
              console.log(data);
            },
            complete: function() {
             setTimeout(function() {
                location.reload();
              }, 1500)
           }
          });
        });
  }
</script>