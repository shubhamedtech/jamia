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
        <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Exam" onclick="add('exams', 'lg')"><i class="fa fa-plus-circle fa-lg"></i></button>
      </div>
    </div>

    <div class="content">
      <div class="card card-transparent">
        <div class="card-header">
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="exams-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="exams-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Name</th>
                  <th>Exam Session</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th data-orderable="false">Actions</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      $(function() {
        var table = $('#exams-table');
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/exams/server'
          },
          'columns': [{
              data: "Exam_Type",
              "render": function(data, type, row) {
                return data == 1 ? 'MCQs' : data == 2 ? 'File Upload' : '';
              }
            },
            {
              data: "Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Exam_Session_ID"
            },
            {
              data: "Start_Date",
              "render": function(data, type, row) {
                return data + " to " + row.End_Date
              }
            },
            {
              data: "Start_Time",
              "render": function(data, type, row) {
                return data + ' - ' + row.End_Time
              }
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
                <i class="fa fa-edit text-warning icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;exams&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="fa fa-trash text-danger icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;exams&#39;, &#39;' + data + '&#39)"></i>\
              </div>'
              }
            },
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 5,
          "drawCallback": function(settings) {
            $('[data-toggle="tooltip"]').tooltip();
          },
        };

        table.dataTable(settings);

        // search box for table
        $('#users-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });

      })
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>