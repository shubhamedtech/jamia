<!-- Modal -->
<div class="modal-header clearfix text-left">
  <h5><span class="semi-bold"></span>Notification</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12">
      <?php
      if (isset($_GET['id'])) {
        $id = $_GET['id'];
        include '../../includes/db-config.php';
        session_start();
        $content = $conn->query("SELECT * FROM Notifications_Generated WHERE ID = $id");
      }
      while ($row = $content->fetch_assoc()) {
        echo "<p>" . nl2br("$row[Content]") . "</p>";
      }
      ?>
    </div>
  </div>
</div>