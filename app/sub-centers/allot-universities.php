<?php
if (isset($_GET['id'])) {
  // ini_set('display_errors', 1);

  require '../../includes/db-config.php';
  $id = intval($_GET['id']);

  $center = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = $id");
  $center = mysqli_fetch_assoc($center);
  $center = $center['Center'];

  $alloted = array();
  $alloted_universities = $conn->query("SELECT University_ID FROM Alloted_Center_To_Counsellor WHERE `Code` = $id");
  //$alloted_universities = $conn->query("SELECT University_ID FROM University_User WHERE `User_ID` = $id");
  while ($alloted_university = $alloted_universities->fetch_assoc()) {
    $alloted[] = $alloted_university['University_ID'];
  }
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <h5>Allot <span class="semi-bold"></span>Boards</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <form role="form" id="form-allot-universities" action="/app/sub-centers/allot" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <?php if (!empty($alloted)) {
        print !empty($alloted) ? '<dt class="text-success mb-2">Alloted Boards</dt>' : '' ?>
        <div class="row">
          <?php
          $alloted_query = !empty($alloted) ? " WHERE ID IN (" . implode(',', $alloted) . ")" : "";
          $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name, Logo FROM Universities $alloted_query");
          while ($university = $universities->fetch_assoc()) { ?>
            <div class="col-md-6 cursor-pointer" onclick="step2('<?= $id ?>', '<?= $university['ID'] ?>', '<?= $university['Name'] ?>');">
              <center>
                <img src="<?= $university['Logo'] ?>" alt="logo" data-src="<?= $university['Logo'] ?>" data-src-retina="<?= $university['Logo'] ?>" width="60%" height="70px">
                <p class="bold mt-2"><?= $university['Name'] ?></p>
              </center>
            </div>
          <?php }
          ?>
        </div>
      <?php } ?>
      <?php $not_alloted_query = !empty($alloted) ? " WHERE ID NOT IN (" . implode(',', $alloted) . ")" : "";
      $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name, Logo FROM Universities $not_alloted_query");
      if ($universities->num_rows > 0) { ?>
        <dt class="text-primary <?php print !empty($alloted) ? 'mt-4' : '' ?> mb-2">Not Alloted Boards</dt>
        <div class="row">
          <?php
          while ($university = $universities->fetch_assoc()) { ?>
            <div class="col-md-6 cursor-pointer" onclick="step2('<?= $id ?>', '<?= $university['ID'] ?>', '<?= $university['Name'] ?>');">
              <center>
                <img src="<?= $university['Logo'] ?>" alt="logo" data-src="<?= $university['Logo'] ?>" data-src-retina="<?= $university['Logo'] ?>" width="60%" height="70px">
                <p class="bold mt-2"><?= $university['Name'] ?></p>
              </center>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <i class="ti-save mr-2"></i>
          <span>Allot</span>
        </button>
      </div>
    </div>
  </form>

  <script>
    function step2(id, university_id, name) {
      var modal = 'md';
      $.ajax({
        url: '/app/sub-centers/step-2',
        type: 'POST',
        data: {
          id: id,
          university_id: university_id,
          name: name
        },
        success: function(data) {
          $('#' + modal + '-modal-content').html(data);
        }
      })
    }
  </script>
<?php } ?>