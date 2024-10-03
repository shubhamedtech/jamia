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
        <div class="">
          <?php if ($_SESSION['Role'] == 'University Head' || $_SESSION['Role'] == 'Administrator') { ?>
            <button class="btn btn-link" aria-label=""  data-toggle="tooltip" data-original-title="Add" onclick="add('downloads/e-books', 'md')"> <i class="fa fa-plus-circle fa-lg"></i></button>
          <?php } ?>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="card card-transparent">
        <div class="card-header">
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="ebooks-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="ebooks-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Board</th>
                  <th data-orderable="false"></th>
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
        var table = $('#ebooks-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var showToAdmin = role == 'Administrator' ? true : false;
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/downloads/e-books/server'
          },
          'columns': [{
              data: "Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "University"
            },
            {
              data: "ID",
              "render": function(data, type, row) {
                return '<div class="button-list text-end">\
              <a href="' + row.File + '" download="' + row.Name + '.' + row.Extension + '"><i class="fa fa-download icon-xs cursor-pointer" title="Download"></i></a>\
              <i class="fa fa-trash text-danger icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;downloads/e-books&#39;,&#39;' + data + '&#39)"></i>\
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
          "iDisplayLength": 5
        };

        table.dataTable(settings);

        // search box for table
        $('#ebooks-search-table').keyup(function() {
          table.fnFilter($(this).val());
        });
      })
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>