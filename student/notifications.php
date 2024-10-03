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
      <div class="row">
        <div class="col-md-6">
          <div class="card custom-card">
            <div class="card-header seperator d-flex justify-content-between">
              <h5 class="fw-bold mb-0">Notifications</h5>
            </div>
            <div class="card-body dash1">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Regarding</th>
                      <th>Sent To</th>
                      <th>Sent On</th>
                      <th>Content</th>
                      <th>Attachment</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $current_notification_id = 0;
                    $session = $_SESSION['Admission_Session'];
                    list($monthText, $year) = explode('-', $session);
                    $monthNumber = date('m', strtotime($monthText));
                    $date = $year . '-' . $monthNumber . '-01';
                    $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE (Send_To = '" . 'student' . "' OR Send_To = '" . 'all' . "') AND Noticefication_Created_on >= '$date'  ORDER BY Notifications_Generated.ID DESC  ");
                    $data = array();
                    while ($row = $result_record->fetch_assoc()) { ?>
                      <tr>
                        <td><?= ucfirst($row['Heading']) ?></td>
                        <td><?= ucfirst($row['Send_To']) ?></td>
                        <td><?= $row['Noticefication_Created_on'] ?></td>
                        <td class="text-center"><a type="btn-link" class="text-primary" onclick="view_content('<?= $row['ID'] ?>');"><i class="fa fa-eye"></i></a></td>
                        <td>
                          <?php if (!empty($row['Attachment'])) { ?>
                            <a href="<?= $row['Attachment'] ?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                          <?php } else { ?>
                            <p>No Attachment</p>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header seperator d-flex justify-content-between">
              <h5 class="fw-bold mb-0">View Notification</h5>
            </div>
            <div class="card-body">
              <?php
              $latest_notification = $conn->query("SELECT * FROM Notifications_Generated WHERE (Send_To = 'student' OR Send_To = '" . 'all' . "') AND Noticefication_Created_on >= '$date' ORDER BY Notifications_Generated.ID DESC LIMIT 1");
              if ($latest_notification->num_rows > 0) {
                while ($row = $latest_notification->fetch_assoc()) {
                  $current_notification_id = $row['ID'];
              ?>
                <div class="d-flex justify-content-between mb-2" id="show-notification">
                  <span><span class="fw-bold">Regarding : </span> <?= $row['Heading'] ?></span>
                  <span class="me-auto"><span class="fw-bold">Date :</span> <?= $row['Noticefication_Created_on'] ?></span>
                </div>
                <p><span class="fw-bold">Message: </span><?= $row['Content'] ?></p>
                <?php if (!empty($row['Attachment'])) { ?>
                  <a href="<?= $row['Attachment'] ?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                <?php } ?>
            </div>
          <?php } }?>
          </div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $(document).ready(function() {
        if (<?= $current_notification_id ?> != 0) {
          $.ajax({
            url: '/app/notifications/student-read-notification?id=' + <?= $current_notification_id ?>,
            type: 'GET',
            success: function(data) {}
          })
        }
      });

      function view_content(id) {
        $.ajax({
          url: '/app/notifications/contents?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>