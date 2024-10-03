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
                    <a href="/app/payments/export?type=1" target="_blank" class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download"> <i class="fa fa-download fa-lg"></i></a>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card card-transparent">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-daterange input-group" id="datepicker-range">
                                <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                                <div class="input-group-addon">to</div>
                                <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                            </div>
                        </div>
                        <?php if ($_SESSION['Role'] != 'Sub-Center') { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select class="form-control" data-init-plugin="select2" id="users" onchange="addFilter(this.value, 'users', 1)" data-placeholder="Choose User">

                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <input type="text" id="payments-search-table" class="form-control pull-right" placeholder="Search">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" id="payments-table">
                            <thead>
                               	<tr>
                                    <th>Sub Center Name</th>
                                    <th>Student Name</th>
                                    <th>Transaction ID</th>
                                    <th>Mode</th>
                                    <th>Bank Name</th>
                                    <th>Total Amount</th>
                                    <th>Center Amount</th>
                                    <th>Payment By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript">
        $(function() {
            var role = "<?= $_SESSION['Role'] ?>";
            var showToAdminHeadAccountant = role == 'Center' || role == 'University Head' || role == 'Accountant' ? true : false;
            var table = $('#payments-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/offline-payments/sub-center-payments'
                },
                'columns': [{
                        data: "Center_Name",
                        "render": function(data, type, row) {
                            return '<strong>' + data + ' (' + row.Center_Code + ')</strong>';
                        }
                    },
                    {
                        data: "Added_For"
                    },
                    {
                        data: "Transaction_ID",
                        "render": function(data, type, row) {
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    {
                        data: "Payment_Mode"
                    },
                    {
                        data: "Bank"
                    },
                    {
                        data: "Total_Amount"
                    },
                    {
                        data: "Center_Amount"
                    },
                    {
                        data: "Gateway_ID",
                        "render": function(data, type, row) {
                            return '<strong>' + row.Center_Name + '</strong>';
                        }
                    },
                    {
                        data: "Transaction_Date"
                    },
                    // {
                    //   data: "Center_Name",
                    //   "render": function(data, type, row) {
                    //     return '<strong>' + data + ' (' + row.Center_Code + ')</strong>';
                    //   }
                    // },
                    // {
                    //   data: "File",
                    //   "render": function(data, type, row) {
                    //     var file = row.File_Type != 'pdf' ? '<a href="' + data + '" target="_blank"><img src="' + data + '" height="20"></a>' : '<a href="' + data + '" target="_blank">PDF</a>';
                    //     return file;
                    //   }
                    // },
                    {
                        data: "Status",
                        "render": function(data, type, row) {
                            var label_class = data == 0 ? "warning" : data == 1 ? "success" : "primary";
                            var status = data == 0 ? "Pending" : data == 1 ? "Approved" : "Wainting for paymant";
                            return '<span class="label label-' + label_class + '">' + status + '</span>';
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
                        url: '/app/offline-payments/update-payment-status',
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
            getCenterList('users');
        }

        function addFilter(id, by, page) {
            $.ajax({
                url: '/app/payments/filter',
                type: 'POST',
                data: {
                    id,
                    by,
                    page
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
                    page
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>