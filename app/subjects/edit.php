<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_GET['id']);
  $subjects = $conn->query("SELECT Subjects.*, Courses.Name as Grade, Courses.ID as course_id FROM Subjects LEFT JOIN Courses ON Subjects.Program_Grade_ID = Courses.ID WHERE Subjects.ID = $id");
  $subjects = mysqli_fetch_assoc($subjects);
?>
  <link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <h5>Edit Subjects</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <form role="form" id="form-edit-subject" foemtarget="_blank" action="/app/subjects/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-5">
          <div class="form-group form-group-default required">
            <label>Grade</label>
            <select class="form-control" name="grade" id="subject_type">
            <?php if($subjects['Grade']){ ?>
              <option value="<?=$subjects['course_id']?>"><?=$subjects['Grade']?></option>
            <?php
              $grades = $conn->query("SELECT Courses.Name as Grade, Courses.ID as course_id FROM Courses"); 
              while ($grade = $grades->fetch_array()) { ?>
              <option value="<?=$grade['course_id']?>"><?=$grade['Grade']?></option>
            <?php } } ?>
            </select>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group form-group-default required">
            <label>Subject type</label>
            <select class="form-control" name="subject_catagory" id="subject_catagory" required>
              <?php if($subjects['Category']){ ?>
              	<option value="<?=$subjects['Category']?>"><?=$subjects['Category']?></option>
            	<?php } ?>
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
              <select class="form-control" name="subject_type" id="subject_type" required>
                <?php if($subjects['Type'] == 1){ ?>
              		<option value="Default">Default</option>
                	<option value="optional">Optional</option>
            	<?php } else { ?>
                    <option value="optional">Optional</option>
              		<option value="Default">Default</option>
                <?php } ?>
              </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="hidden" name="subject_id" value="<?=$subjects['ID']?>"/>
            <input type="text" name="name" class="form-control" value="<?=$subjects['Name']?>"/>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Subject Mode</label>
            <select class="form-control" name="subject_mode" id="subject_mode">
              <option value="Theory">Theory</option>
              <option value="Practicle">Practicle</option>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Subject Fee</label>
            <input type="number" name="subject_fee" id="subject_fee" class="form-control" value="<?=$subjects['Subject_Fee']?>">
          </div>
        </div>
         <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>TOC Fee</label>
            <input type="number" name="toc_fee" id="toc_fee" class="form-control" value="<?=$subjects['Toc_Fee']?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6" id="practical_fee_div">
          <div class="form-group form-group-default required">
            <label>Practical Fee</label>
            <input type="number" name="practical_fee" id="practical_fee" class="form-control" value="<?=$subjects['Practical_Fee']?>">
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" id="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <i class="ti-save-alt mr-2"></i>Update</button>
      </div>
    </div>
  </form>
  <script type="text/javascript" src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script>

    $(function() {
      $('#form-edit-subject').validate({
        rules: {
          name: {
            required: true
          },
          grade: {
            required: true
          },
          subject_fee: {
            required: true
          },
          subject_mode: {
            required: true
          },
          subject_type: {
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

    $("#form-edit-subject").on("submit", function(e) {
      if ($('#form-edit-subject').valid()) {
        $('#submit').prop('disabled', true);
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
              $('#subjects-table').DataTable().ajax.reload(null, false);
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
<?php } ?>