<?php
if (isset($_POST['id']) && isset($_POST['university_id'])) {
  require '../../includes/db-config.php';
  session_start();
  //ini_set('display_errors', 1);
  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $counsellor_id = '';
  $sub_counsellor_id = '';
  $durations = '';
  $alloted_counsellor_id = '';
  $alloted_sub_counsellor_id = '';
   $course_types = array();
  $university_is_vocational = 0;
  $is_vocational = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Is_Vocational = 1");
  if ($is_vocational->num_rows > 0) {
    $university_is_vocational = 1;
  }
  $center_subcenter = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = $id");
   
  if ($center_subcenter->num_rows > 0) {
    $center = mysqli_fetch_assoc($center_subcenter);
    $center_id = $center['Center'];
    
  }
  
  $alloted_counsellor = $conn->query("SELECT Counsellor_ID , Course_type FROM Alloted_Center_To_Counsellor WHERE `Code` = $center_id AND University_ID = $university_id");
  
  if ($alloted_counsellor->num_rows > 0) {
    $alloted_counsellor = mysqli_fetch_assoc($alloted_counsellor);
    $alloted_counsellor_id = $alloted_counsellor['Counsellor_ID'];
   
  }

  $alloted_course_types = $conn->query("SELECT Course_type FROM Alloted_Center_To_Counsellor WHERE `Code` = $id AND University_ID = $university_id");
  
  if ($alloted_course_types->num_rows > 0) {
    $alloted_course_type = mysqli_fetch_assoc($alloted_course_types);
    $course_types = json_decode($alloted_course_type['Course_type']);
    
  }
  $alloted_sub_counsellor = $conn->query("SELECT Sub_Counsellor_ID FROM Alloted_Center_To_SubCounsellor WHERE `Code` = $id AND University_ID = $university_id");
  if ($alloted_sub_counsellor->num_rows > 0) {
    $alloted_sub_counsellor = mysqli_fetch_assoc($alloted_sub_counsellor);
    $alloted_sub_counsellor_id = $alloted_sub_counsellor['Sub_Counsellor_ID'];
  }
?>
  <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <!-- Modal -->
   <div class="modal-header clearfix text-left">
    <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'Center') { ?>
      <!-- Back Button -->
      <span class="pull-left link text-color cursor-pointer" onclick="allot(<?= $id ?>, 'lg')"><i class="fa fa-arrow-left"></i> Back</span>
    <?php } ?>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

    <!-- Delete e:\Programs\xampp\htdocs\admissionui\app\sub-centers\vocational-course-type.phpButton -->

  </div>
  <form role="form" id="form-allot-center-master" action="/app/sub-centers/allot<?php print $university_is_vocational == 1 ? '-vocational' : '' ?>" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="d-flex justify-content-end mb-2">
        <?php if (!empty($alloted_counsellor_id)) { ?>
          <button class="btn-danger btn" onclick="removeAllotment()"><i class="fa fa-trash mr-1"></i> Remove</button>
        <?php } ?>
      </div>
     <input class="form-control" type="hidden" name="counsellor" id="counsellor" value="<?php echo $alloted_counsellor_id; ?>">
     <input class="form-control" type="hidden" name="sub_counsellor" id="sub_counsellor" value="<?php echo $alloted_sub_counsellor_id; ?>">
    <div class="row">
      <div class="col-md-12">
          <div class="form-group form-group-default form-group-default-select2 required">
          <label>Course Type</label>
          <select class="form-control" style="width: 100% !important;" id="course_type" name="course_type[]" multiple>
                <option value="1" <?php print (is_array($course_types) && in_array(1, $course_types)) ? 'selected' : ''; ?>>10th</option>
                <option value="2" <?php print (is_array($course_types) && in_array(2, $course_types)) ? 'selected' : ''; ?>>12th</option>
          </select>
        </div>
      </div>
  </div>

      <!---<div id="fee">
      </div>-->
    </div>

    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Save</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>
  <script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript">
      $('#course_type').select2({
        placeholder: 'Course Type'
      }).change();
    </script>
  <script>
    function getSubCounsellor() {
      getFeeSructures();
      var counsellor = $('#counsellor').val();
      $.ajax({
        url: '/app/center-master/sub-counsellor?id=' + counsellor,
        type: 'GET',
        success: function(data) {
          $('#sub_counsellor').html(data);
          <?php if (!empty($alloted_sub_counsellor_id)) { ?>
            $('#sub_counsellor').val('<?= $alloted_sub_counsellor_id ?>');
          <?php } ?>
        }
      })
    }

    <?php if (!empty($alloted_counsellor_id)) { ?>
      getSubCounsellor();
    <?php } ?>

    function getFeeSructures() {
      const university_id = '<?= $university_id ?>';
      const id = '<?= $id ?>';
      $.ajax({
        url: '/app/sub-centers/<?php print $university_is_vocational == 1 ? 'vocational-course-type' : 'fee-structures' ?>?university_id=' + university_id + '&id=' + id,
        type: 'GET',
        success: function(data) {
          $('#fee').html(data);
        }
      });
    }

    function removeAllotment() {
      const id = '<?= $id ?>';
      const university_id = '<?= $university_id ?>';
      $.ajax({
        url: '/app/center-master/remove-allotment',
        type: 'POST',
        data: {
          id: id,
          university_id: university_id
        },
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
          } else {
            notification('danger', data.message);
          }
        }
      })
    }

    $(function() {
      $('#form-allot-center-master').validate({
        rules: {
          counsellor: {
            required: true
          },
          'fee[]': {
            required: true
          }
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

    $("#form-allot-center-master").on("submit", function(e) {
      if ($('#form-allot-center-master').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        formData.append('university_id', '<?= $university_id ?>');
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
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
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