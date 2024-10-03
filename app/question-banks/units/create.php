<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload" onclick="add('subjects', 'lg')"> <i class="fa fa-export"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class="container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row">
          <div class="col-md-12">
            <form role="form" id="create-unit" foemtarget="_blank" action="/app/videos/units/store" method="POST" enctype="multipart/form-data">
              <input name="syllabi_id" id="syllabi_id" value="<?=base64_decode($_GET['syllabi_id'])?>" type="hidden" />
              <div class="col-md-8 m-t-10 sm-m-t-10">
                Name: <input name="unit_name[]" type="text" />
              <button name="unit_name" type="button" class="btn btn-primary rounded" id="unit_btn">ADD</button>
            </div>
            <div id="add_more_unit">
            </div>
            <div class="col-md-4 m-t-10 sm-m-t-10">
                <button id="submit-button" onclick="submitForm();" class="btn btn-primary">
                  Add-Unit
                </button>
            </div>
            </form>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Unit</th>
                    <th>Code</th>
                    <th>Subject</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $units = $conn->query("SELECT Units.Unit_Name, Syllabi.Code, Syllabi.Name as sub_name FROM Units LEFT JOIN Syllabi ON  Units.Syllabi_ID = Syllabi.ID WHERE Syllabi_ID = ".base64_decode($_GET['syllabi_id'])."");
                    while($row = $units->fetch_assoc()) { ?>
                    <tr>
                      <td><?=$row['Unit_Name']?></td>
                      <td><?=$row['Code']?></td>
                      <td><?=$row['sub_name']?></td>
                    </tr>
                    <?php }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
    <script>
      $("#unit_btn").click(function(){  
        myhtml = '<div class="col-md-8 m-t-10 sm-m-t-10" id="add_more_unit1">Name: <input name="unit_name[]" type="text" /> <button name="unit_name" type="button" class="btn btn-primary rounded" id="unit_btn_remove">Remove</button> </div>'
        $("#add_more_unit").append(myhtml);
      });

      $("#unit_btn_remove").click(function(){  
        $(this).hide();
      });
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
            }
          });
        });
      }
    </script>