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
        <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Payment Gateway" onclick="add('payment-gateways', 'lg')"> <i class="fa fa-plus-circle fa-lg"></i></button>
      </div>
    </div>

    <div class="content">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="payment-gateway-table">
                <thead>
                  <tr>
                    <th>University</th>
                    <th>Type</th>
                    <th>Access Key</th>
                    <th>Salt/Secret Key</th>
                    <th data-orderable="false">Status</th>
                    <th data-orderable="false">Actions</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
  <script>
    $(function() {
      $("#payment-gateway-table").dataTable({
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/payment-gateways/server'
        },
        'columns': [{
            data: "Type"
          },
          {
            data: "University_ID"
          },
          {
            data: "Access_Key"
          },
          {
            data: "Secret_Key"
          },
          {
            data: "Status",
            "render": function(data, type, row) {
              var active = data == 1 ? 'Active' : 'Inactive';
              var checked = data == 1 ? 'checked' : '';
              return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '"' + checked + '> ';
            }
          },
          {
            data: "ID",
            "render": function(data, type, row) {
              return '<div class="button-list text-end">\
                <i class="fa fa-edit text-warning icon-xs cursor-pointer" onclick="edit(&#39;payment-gateways&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="fa fa-trash text-danger icon-xs cursor-pointer" onclick="destroy(&#39;payment-gateways&#39;, &#39;' + data + '&#39)"></i>\
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
        "initComplete": function() {
          $('.bs_switch').bootstrapSwitch();
        },
      });
      $("#payment-gateway-table").on('draw.dt', function() {
        $('.bs_switch').bootstrapSwitch();
        $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
          var rowId = $(this).data('row-id');
          changeStatus('Payment_Gateways', rowId);
        });
      });
    })
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>