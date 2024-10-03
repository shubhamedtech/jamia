<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<link href="/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" media="screen">
<?php
unset($_SESSION['filterByUser']);
unset($_SESSION['filterByDate']);
?>
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
        <div>
          <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
            <a href="/accounts/center-ledger/student-wise-ledger" target="" class="btn btn-default" style="padding: 7px 13px;" aria-label="" title="" data-toggle="tooltip" data-original-title="Go On Ladger"> <i class="fa fa-arrow-left "></i></a>
            <a class="btn btn-success" aria-label="" title="" data-toggle="tooltip" style="padding: 7px 13px;" data-original-title="Add Amount" onclick="add_wallet();"> <i class=" fa fa-plus "></i></a>
          <?php } ?>
        </div>
      </div>
    </div>
    <!-- END JUMBOTRON -->
    <!-- START CONTAINER FLUID -->
    <div class="content">
      <!-- BEGIN PlACE PAGE CONTENT HERE -->
      <div class="card card-transparent">
        <div class="card-header">
          <div class="row">
            <!-- <div class="col-md-12 d-flex justify-content-start align-items-center"> -->
            <div class="col-md-2 m-b-10">
              <div class="input-daterange input-group" id="datepicker-range">
                <input type="text" class="form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                <div class="input-group-addon">to</div>
                <input type="text" class="form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
              </div>
            </div>
            <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
              $d_none = "display:none";
            } else {
              $d_none = "display:block";
            } ?>
            <div class="col-md-2 m-b-10" style="<?= $d_none ?>">
              <div class="form-group">
                <select class="form-control" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users','center')" data-placeholder="Choose User">

                </select>
              </div>
            </div>
            <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
              <div class="col-md-2 m-b-10">
                <div class="form-group">
                  <select class="form-control sub_center" data-init-plugin="select2" id="sub_center" onchange="addSubCenterFilter(this.value, 'users','subcenter')" data-placeholder="Choose Sub Center">
                  </select>
                </div>
              </div>
            <?php } ?>
            <div class="col-md-3 m-b-10">
              <a class="btn btn-primary" href="/app/wallet-payments/admission-history">Admission History</a>
            </div>
            <!-- </div> -->
          </div>
          <div class="pull-right">
            <div class="col-xs-12">
              <input type="text" id="payments-search-table" class="form-control pull-right" placeholder="Search">
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="payments-table">
              <thead>
                <tr>
                  <th>File</th>
                  <th>Transaction ID</th>
                  <th>Gateway ID</th>
                  <th>Mode</th>
                  <th>Bank Name</th>
                  <th>Amount</th>
                  <th>Type</th>
                  <th>Payment By</th>
                  <th>Date</th>
                  <th>Status</th>

                  <th data-orderable="false">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
      <!-- END PLACE PAGE CONTENT HERE -->
    </div>
    <!-- END CONTAINER FLUID -->
  </div>
  <!-- END PAGE CONTENT -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
  <script type="text/javascript">
    $(function() {
      var role = "<?= $_SESSION['Role'] ?>";
      var showToAdminHeadAccountant = role == 'Administrator' || role == 'University Head' || role == 'Accountant' ? true : false;
      var table = $('#payments-table');

      var settings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/wallet-payments/server'
        },
        'columns': [{
            data: "File",
            "render": function(data, type, row) {
              var file = row.File_Type != 'pdf' ? '<a href="' + data + '" target="_blank"><img src="' + data + '" height="20"></a>' : '<a href="' + data + '" target="_blank">PDF</a>';
              return file;
            }
          },
          {
            data: "Transaction_ID",
            "render": function(data, type, row) {
              return '<strong>' + data + '</strong>';
            }
          },
          {
            data: "Gateway_ID"
          },
          {
            data: "Payment_Mode"
          },
          {
            data: "Bank"
          },
          {
            data: "Amount"
          }, {
            data: "Type"
          },
          {
            data: "Center_Name",
            "render": function(data, type, row) {
              return '<strong>' + data + ' (' + row.Center_Code + ')</strong>';
            }
          },
          {
            data: "Transaction_Date"
          },
          {
            data: "Status",
            "render": function(data, type, row) {
              var status = '';
              var label_class = '';
              if (row.Type == "Online") {
                status = data == 0 ? "Failled" : data == 1 ? "Added" : "Rejected";
                label_class = data == 0 ? "danger" : data == 1 ? "success" : "danger";
              } else {
                status = data == 0 ? "Pending" : data == 1 ? "Added" : "Rejected";
                label_class = data == 0 ? "warning" : data == 1 ? "success" : "danger";
              }
              return '<span class="label label-' + label_class + '">' + status + '</span>';
            }
          },
          {
            data: "ID",
            "render": function(data, type, row) {
              var status_button = '';
              var action_button = '';
              if ((role == 'Accountant' || role == 'Administrator')) {
                if (row.Status == 0 && row.Type == 'Offline' && row.Status != 2 && row.status != 1) {
                  status_button = '<i class="fa fa-check-circle text-success px-1 cursor-pointer" data-toggle="tooltip" data-original-title="Approve" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;1&#39;)"></i><i class="fa fa-times-circle text-danger px-1 cursor-pointer" data-toggle="tooltip" data-original-title="Reject" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;2&#39;)"></i>';
                } else if (row.Status != 0 && row.Type == 'Online' && row.Status != 2 && row.status == 1) {
                  status_button = '<i class="fa fa-check-circle text-success px-1 cursor-pointer" data-toggle="tooltip" data-original-title="Approve" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;1&#39;)"></i><i class="fa fa-times-circle text-danger px-1 cursor-pointer" data-toggle="tooltip" data-original-title="Reject" title="" onclick="updatePaymentStatus(&#39;' + data + '&#39, &#39;2&#39;)"></i>';
                }
              }
              if (role == 'Accountant' || role == 'Administrator' || role == 'University Head') {
                if (row.Status != 2 && row.status != 1 && ((row.Status == 0 && row.Type == 'Offline') || (row.Status != 0 && row.Type == 'Online'))) {
                  action_button = (role == 'Accountant' || role == 'Administrator' || role == 'University Head') && row.Status != 1 ? '<i class="fa fa-edit cursor-pointer text-warning px-1" title="Edit" onclick="edit(&#39;offline-payments&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i><i class="fa fa-trash cursor-pointer text-danger px-1" title="Delete" onclick="destroy(&#39;offline-payments&#39;, &#39;' + data + '&#39)"></i>' : '';
                }
              }
              return '<div class="button-list text-end">\
              ' + status_button + action_button + '\
              </div>';
            },
            visible: showToAdminHeadAccountant
          }
        ],
        "sDom": "<t><'row'<p i>>",
        "destroy": true,
        "scrollCollapse": true,
        "oLanguage": {
          "sLengthMenu": "_MENU_ ",
          "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        "aaSorting": [],
        "iDisplayLength": 25,
        "drawCallback": function() {
          $('[data-toggle="tooltip"]').tooltip();
        }
      };

      table.dataTable(settings);

      // search box for table
      $('#payments-search-table').keyup(function() {
        table.fnFilter($(this).val());
      });
    })
  </script>

  <script type="text/javascript">
    function updatePaymentStatus(id, value) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '/app/wallet-payments/update-payment-status',
            type: 'POST',
            data: {
              id,
              value
            },
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('#payments-table').DataTable().ajax.reload(null, false);
              } else {
                notification('danger', data.message);
              }
            }
          })
        }
      })
    }
  </script>
  <script>
    $('#datepicker-range').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      endDate: '0d'
    });
  </script>
  <script>
    if ($("#users").length > 0) {
      $("#users").select2({
        placeholder: 'Choose Center'
      })
      $("#sub_center").select2({
        placeholder: 'Choose Sub-Center'
      })
      getCenterList('users');
    }

    function addFilter(id, by, page) {
      var wallet_payments = "wallet-payments";
      $.ajax({
        url: '/app/payments/filter',
        type: 'POST',
        data: {
          id,
          by,
          page,
          wallet_payments
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {

            $('.table').DataTable().ajax.reload(null, false);
            $("#sub_center").html(data.subCenterName);

          }
        }
      })
    }
  </script>
  <?php if ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Sub-Center") { ?>
    <script>
      $(document).ready(function() {
        addFilter('<?= $_SESSION['ID'] ?>', 'users', 2);
      })
    </script>
  <?php } ?>
  <script>
    function addSubCenterFilter(id, by) {

      var wallet_payments = "wallet-payments";

      var page = 2;
      $.ajax({
        url: '/app/payments/filter',
        type: 'POST',
        data: {
          id,
          by,
          page,
          wallet_payments
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
          }

        }
      })
    }


    function addDateFilter() {
      var startDate = $("#startDateFilter").val();
      var endDate = $("#endDateFilter").val();
      var wallet_payments = "wallet-payments";
      if (startDate.length == 0 || endDate == 0) {
        return
      }
      var id = 0;
      var by = 'date';
      page = 2;
      $.ajax({
        url: '/app/payments/filter',
        type: 'POST',
        data: {
          id,
          by,
          startDate,
          endDate,
          page,
          wallet_payments

        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        }
      })
    }
  </script>
  <script>
    function show_students(transaction_id) {
      console.log(transaction_id);
      modal = 'md';
      var sdsds = $('#sdsds_' + transaction_id).attr('data-value');
      $.ajax({
        url: '/app/online-payments/paid-students?ids=' + sdsds,
        type: 'GET',
        success: function(data) {
          $('#' + modal + '-modal-content').html(data);
          $('#' + modal + 'modal').modal('show');
        }
      })

    }
  </script>
  <script>
    function add_wallet() {
      modal = 'md';
      $.ajax({
        url: '/app/wallet-payments/create',
        type: 'GET',
        success: function(data) {
          $('#' + modal + '-modal-content').html(data);
          $('#' + modal + 'modal').modal('show');
        }
      })

    }
  </script>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>