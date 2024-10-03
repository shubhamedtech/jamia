<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  $id = intval($_GET['id']);
  $course = $conn->query("SELECT * FROM Courses WHERE ID = $id");
  $course = $course->fetch_assoc();
}
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5>Edit Program</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </button>
</div>
<form role="form" id="form-edit-course" action="/app/courses/update" method="POST" enctype="multipart/form-data">
  <div class="modal-body">

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Board</label>
          <select class="form-control" name="university_id" onchange="getCourseType(this.value); getDepartments(this.value);">
            <option value="">Choose</option>
            <?php
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE ID IS NOT NULL " . $_SESSION['UniversityQuery']);
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>" <?php print $university['ID'] == $course['University_ID'] ? 'selected' : '' ?>><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Department</label>
          <select class="form-control" id="department" name="department">

          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Program Type</label>
          <select class="form-control" id="course_type" name="course_type">

          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Bachelor of Technology" value="<?= $course['Name'] ?>" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: B.Tech" value="<?= $course['Short_Name'] ?>" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Exam Fee</label>
          <input type="text" name="exam_fee" class="form-control" placeholder="ex: 200" value="<?= $course['Exam_Fee'] ?>" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Registration Fee</label>
          <input type="text" name="registration_fee" class="form-control" value="<?= $course['Registration_Fee'] ?>" placeholder="ex: 200" required>
        </div>
      </div>
    </div>
  </div>
  <div class=" modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <i class="ti-save-alt mr-2"></i> Update</button>
      </button>
    </div>
  </div>
</form>

<script>
  function getCourseType(id) {
    $.ajax({
      url: '/app/courses/course-types?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#course_type').html(data);
        $('#course_type').val('<?= $course['Course_Type_ID'] ?>');
      }
    });
  }

  function getDepartments(id) {
    $.ajax({
      url: '/app/courses/departments?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#department').html(data);
        $('#department').val('<?= $course['Department_ID'] ?>');
      }
    });
  }

  getCourseType(<?= $course['University_ID'] ?>);
  getDepartments(<?= $course['University_ID'] ?>);

  $(function() {
    $('#form-edit-course').validate({
      rules: {
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        university_id: {
          required: true
        },
        department: {
          required: true
        },
        course_type: {
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

  $("#form-edit-course").on("submit", function(e) {
    if ($('#form-edit-course').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      formData.append('id', '<?= $id ?>');
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
            $('#courses-table').DataTable().ajax.reload(null, false);
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