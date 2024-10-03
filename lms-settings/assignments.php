<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<div class="wrapper boxed-wrapper">
  <!-- Topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- Menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">
        <?php
        $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        foreach ($breadcrumbs as $i => $breadcrumb) {
          if ($i + 1 == count($breadcrumbs)) {
            $crumb = explode("?", $breadcrumb);
            echo '<h1 class="text-capitalize fw-bold">' . $crumb[0] . '</h1>';
          }
        }
        ?>
        <div class="d-flex">
          <button class="btn btn-sm btn btn-success" aria-label="Add Assignments" data-toggle="tooltip" data-placement="top" title="Add Assignments" onclick="add('assignments','md')">Create Assignments</button>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="card card-transparent">
        <div class="card-header">
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="subject-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap w-100" id="subjects-table">
              <thead>
                <tr>
                  <th>Course Name</th>
                  <th>Short Name</th>
                  <th>Subject Name</th>
                  <th>Assignments Name</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Marks</th>
                  <th>Created Date</th>
                  <th>Updated Date</th>
                  <th>Created By</th>
                  <th>Download Assignments</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
  <script>
    var role = '<?= $_SESSION['Role'] ?>';
    var table = $('#subjects-table');
    var settings = {
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, 'All']
      ],
      'ajax': {
        'url': '/app/assignments/admin'
      },
      'columns': [{
          data: "Grade"
        },
        {
          data: "short_name"
        },
        {
          data: "subject_name"
        },
        {
          data: "assignment_name"
        },
        {
          data: "start_date"
        },
        {
          data: "end_date"
        },
        {
          data: "marks"
        },
        {
          data: "created_date"
        },
        {
          data: "updated_date"
        },
        {
          data: "created"
        },
        {
          data: "assignment_file",
          render: function(data, type, row) {
            var path = '/../uploads/assignments/';
            var file = '<a href="' + path + data + '" class="btn btn-success btn-sm" download>Download Assignments</a>';
            return file;
          }
        },

        {
          data: "id",
          "render": function(data, type, row) {
            return '<div class="button-list text-end">\
          <i class="fa fa-edit text-warning icon-xs cursor-pointer" onclick="edit(&#39;assignments&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
          <i class="fa fa-trash text-danger icon-xs cursor-pointer" onclick="destroy(&#39;assignments&#39;, &#39;' + data + '&#39)"></i>\
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
      "iDisplayLength": 50
    };
    let res = table.dataTable(settings);
    $('#subject-search-table').keyup(function() {
      table.fnFilter($(this).val());
    });
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>