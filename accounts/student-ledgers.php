<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .profile_img {
    width: 150px;
    height: 150px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" integrity="sha512-qZvrmS2ekKPF2mSznTQsxqPgnpkI4DNTlrdUmTzrDgektczlKNRRhy5X5AAOnx5S09ydFYWWNSfcEqDTTHgtNA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
        <div class="row d-flex ">
          <div class="col-md-6">
            <div class="card-header">
              <div class="form-group form-group-default required">
                <label>Students</label>
                <select class="form-control" data-init-plugin="select2" id="student" onchange="getLedger(this.value)">
                  <option value="">Select</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div id="ledger">
        </div>
      </div>
      <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
<script>
     $("#student").select2({
      placeholder: 'Choose Students'
    })
</script>
      <script type="text/javascript">
        function getLedger(id) {
          $.ajax({
            url: '/app/students/ledger?id=' + id,
            type: 'GET',
            success: function(data) {
              $("#ledger").html(data);
            }
          })
        }

        getStudentList('student');
      </script>

      <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>