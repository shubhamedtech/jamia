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
        <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Notification" onclick="add('notifications','lg')"><i class="fa fa-plus-circle fa-lg"></i></button>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>Select Notification Heading</label>
                <select class="form-control" id="heading" onchange="getTable()">
                  <option value="">Choose</option>
                  <option value="Fee">Fee</option>
                  <option value="Admisssion">Admission</option>
                  <option value="Exam">Exam</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group form-group-default required">
                <label>Notification by user</label>
                <select class="form-control" id="send_to" onchange="getTable()">
                  <option value="">Choose</option>
                  <option value="student">Student</option>
                  <option value="center">Center</option>
                  <option value="all">All</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row" id="notifications"></div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      function getSemester(id) {
        $.ajax({
          url: '/app/subjects/semester?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#semester").html(data);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function getTable() {
        var heading = $('#heading').val();
        var send_to = $('#send_to').val();
        $.ajax({
          url: '/app/notifications/server?heading=' + heading + '&send_to=' + send_to,
          type: 'GET',
          success: function(data) {
            $('#notifications').html(data);
          }
        })
      }

      function removeTable() {
        $('#notifications').html('');
      }
    </script>

    <script type="text/javascript">
      function uploadFile(table, column, id) {
        $.ajax({
          url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>