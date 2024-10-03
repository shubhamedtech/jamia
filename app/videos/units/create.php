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

        <h1 class="text-capitalize d-inline fw-bold">Create New Unit</h1>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-header">
          <div class="row justify-content-center">
            <div class="col-md-6">
              <form role="form" id="create-unit" foemtarget="_blank" action="/app/videos/units/store" method="POST" enctype="multipart/form-data">
                <input name="syllabi_id" id="syllabi_id" value="<?= base64_decode($_GET['syllabi_id']) ?>" type="hidden" />
                <div class="input-group">
                  <div class="input-group-prepend"><span class="input-group-text">Name:</span></div>
                  <input name="unit_name[]" class="form-control" type="text" />
                  <div class="input-group-append"><button id="submit-button" onclick="submitForm();" class="btn btn-primary"> Add Unit</button></div>
                </div>
                <div id="add_more_unit">
                </div>
                <!-- <div class="col-md-4 m-t-10 sm-m-t-10">
                  <button id="submit-button" onclick="submitForm();" class="btn btn-primary">
                    Add-Unit
                  </button>
                </div> -->
              </form>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Unit</th>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $units = $conn->query("SELECT Units.Unit_Name, Syllabi.Code, Syllabi.Name as sub_name FROM Units LEFT JOIN Syllabi ON  Units.Syllabi_ID = Syllabi.ID WHERE Syllabi_ID = " . base64_decode($_GET['syllabi_id']) . "");
                    while ($row = $units->fetch_assoc()) { ?>
                      <tr>
                        <td><?= $row['Unit_Name'] ?></td>
                        <td><?= $row['Code'] ?></td>
                        <td><?= $row['sub_name'] ?></td>
                        <td><i class="ti-trash text-danger"></i></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
      // $("#unit_btn").click(function() {
      //   myhtml = '<div class="col-md-8 m-t-10 sm-m-t-10" id="add_more_unit1">Name: <input name="unit_name[]" type="text" /> <button name="unit_name" type="button" class="btn btn-primary rounded" id="unit_btn_remove">Remove</button> </div>'
      //   $("#add_more_unit").append(myhtml);
      // });

      // $("#unit_btn_remove").click(function() {
      //   $(this).hide();
      // });
    </script>
    <script>
      function submitForm() {
        $('#submit-button').prop('disabled', false);
        $('#create-unit').submit(function(e) {
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>