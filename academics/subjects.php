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

        <?php 
  		$breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        for ($i = 1; $i <= count($breadcrumbs); $i++) {
          if (count($breadcrumbs) == $i) : $active = "active";
            $crumb = explode("?", $breadcrumbs[$i]);
            echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
          endif;
        }
        ?>
        <div>
          <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="fa fa-download fa-lg"></i></button>
          <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Program" onclick="add('grade-subjects','lg')"> <i class="fa fa-plus-circle fa-lg"></i></button>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="card card-transparent">
        <div class="card-header">
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="sub-courses-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap w-100" id="subjects-table" >
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Grade</th>
                  <th>Mode</th>
                  <th>Type</th>
                  <th>Category</th>
                  <th>Subject Fee</th>
                  <th>TOC Fee</th>
                  <th>Practical Fee</th>
                  <th>Total</th>
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

</div>

<script>
  var role = '<?=$_SESSION['Role']?>';
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
      'url': '/app/subjects/server'
    },
    'columns': [{
        data: "Name"
      },
      {
        data: "Grade"
      },
      {
        data: "Mode"
      },
      {
        data: "Type"
      },
      {
        data: "Category"
      },
      {
        data: "Subject_Fee"
      },
      {
        data: "Toc_Fee"
      },
      {
        data: "Practical_Fee"
      },
      {
        data: "Total_Fee"
      },
      {
          data: "ID",
          "render": function(data, type, row) {
          return '<div class="button-list text-end">\
          <i class="fa fa-edit text-warning icon-xs cursor-pointer" onclick="edit(&#39;subjects&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
          <i class="fa fa-trash text-danger icon-xs cursor-pointer" onclick="destroy(&#39;subjects&#39;, &#39;' + data + '&#39)"></i>\
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
  // search box for table
  $('#sub-courses-search-table').keyup(function() {
    table.fnFilter($(this).val());
  });
</script>