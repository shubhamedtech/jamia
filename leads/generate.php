<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">

        <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        for ($i = 1; $i <= count($breadcrumbs); $i++) {
          if (count($breadcrumbs) == $i) : $active = "active";
            $crumb = explode("?", $breadcrumbs[$i]);
            echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
          endif;
        }
        ?>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body" id="replaceable">
                  <form action="/app/leads/store-generate" id="lead-form">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Owner</label>
                          <select class="form-control"  name="user" id="user" required>
                            <option value="">Choose</option>
                            <?php
                            $role_query = str_replace("{{ table }}", "Users", $_SESSION['RoleQuery']);
                            $role_query = str_replace("{{ column }}", "ID", $role_query);

                            $centers = $conn->query("SELECT ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users WHERE Role IN ('Center', 'Sub-Center') $role_query");
                            while ($center = $centers->fetch_assoc()) {
                              echo '<option value="' . $center['ID'] . '">' . $center['Name'] . '</option>';
                            }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Full Name</label>
                          <input type="text" style="text-transform: uppercase" class="form-control" autocomplete="off" name="name" placeholder="Jhon Doe" required />
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Email</label>
                          <input type="email" class="form-control" style="text-transform: lowercase" autocomplete="off" id="email" name="email" placeholder="jhon@example.com" onkeyup="checkLeadEmail(this.value, 'leadEmailError')" required>
                        </div>
                        <p class="text-danger font-weight-bold" id="leadEmailError"></p>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Mobile</label>
                          <input type="tel" class="form-control" autocomplete="off" maxlength="10" id="mobile" name="mobile" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" required />
                        </div>
                        <p class="text-danger font-weight-bold" id="leadMobileError"></p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Course</label>
                          <select class="form-control"  name="course" id="course" onchange="getSubCourse(this.value)" required>
                            <option value="">Choose</option>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group form-group-default required">
                          <label>Sub-Course</label>
                          <select class="form-control"  name="sub_course" id="sub_course" required>
                            <option value="">Choose</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer clearfix text-end mt-2">
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $("#mobile").keyup(function() {
        var error_id = "leadMobileError";
        var mobile = $(this).val();
        if (mobile.length == 10) {
          var university = '<?= $_SESSION['university_id'] ?>';
          $.ajax({
            url: '/app/leads/check_lead_mobile?mobile=' + mobile + '&university=' + university,
            type: 'GET',
            dataType: 'JSON',
            success: function(data) {
              if (data.status == 302) {
                $('#leadMobileError').html(data.message);
                $(':input[type="submit"]').prop('disabled', true);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#leadMobileError').html('');
              }
            }
          })
        } else {
          $(':input[type="submit"]').prop('disabled', false);
          $('#' + error_id).html('');
        }
      });

      function checkLeadEmail(value, error_id) {
        if (isEmail(value)) {
          var university = '<?= $_SESSION['university_id'] ?>';
          $.ajax({
            url: '/app/leads/check_lead_email?email=' + value + '&university=' + university,
            type: 'GET',
            dataType: 'JSON',
            success: function(data) {
              if (data.status == 302) {
                $('#' + error_id).html(data.message);
                $(':input[type="submit"]').prop('disabled', true);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#' + error_id).html('');
              }
            }
          })
        } else {
          $(':input[type="submit"]').prop('disabled', false);
          $('#' + error_id).html('');
        }
      }

      function getCourse(id) {
        $.ajax({
          url: '/app/leads/courses?university_id=' + id,
          type: 'GET',
          success: function(data) {
            $('#course').html(data);
            getSubCourse($('#course').val());
          }
        })
      }

      getCourse(<?= $_SESSION['university_id'] ?>);

      function getSubCourse(id) {
        $.ajax({
          url: '/app/leads/course_sub_courses?course_id=' + id,
          type: 'GET',
          success: function(data) {
            $('#sub_course').html(data);
          }
        })
      }

      $(function() {
        $("#lead-form").on("submit", function(e) {
          var formData = new FormData(this);
          $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                $('#lead-form')[0].reset();
                notification('success', data.message);
                if (data.hasOwnProperty('url')) {
                  window.location.href = data.url;
                }
              } else {
                notification('danger', data.message);
              }
            }
          });
          e.preventDefault();
        });
      })
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>