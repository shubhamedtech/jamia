<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <h5>Export <span class="semi-bold"></span>Documents as</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="modal-body">
    <div class="row text-center">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <i class="cursor-pointer fa fa-file-pdf-o f-70 mb-2" onclick="exportPdf('<?= $id ?>')"></i>
        <p class="mb-0">Export as PDF</p>
      </div>
      <div class="col-sm-6 col-sm-6 col-xs-6 sm-p-t-30">
        <i class="cursor-pointer fa fa-file-zip-o f-70 mb-2" onclick="exportZip('<?= $id ?>')"></i>
        <p class="mb-0">Export as ZIP</p>
      </div>
    </div>
  </div>
<?php } ?>