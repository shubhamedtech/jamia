<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
    td {
        white-space: normal !important;
        word-wrap: break-word;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
    <!-- topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

    <div class="content-wrapper">
        <div class="content-header sty-one d-flex justify-content-between">
            <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                    $crumb = explode("?", $breadcrumbs[$i]);
                    echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
                endif;
            }
            ?>
            <div class="text-end">
                <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="fa fa-download fa-lg"></i></button>
                <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add" onclick="add('center-master','lg')"> <i class="fa fa-plus-circle fa-lg"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="card card-transparent">
                <div class="card-header">
                    <div class="pull-right">
                        <div class="col-xs-12">
                            <input type="text" id="users-search-table" class="form-control pull-right" placeholder="Search">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" id="users-table">
                            <thead>
                                <tr>
                                    <th data-orderable="false"></th>
                                    <th>Name</th>
                                    <th data-orderable="false">Password</th>
                                  	<th>Joining Date</th>
                                    <th>No Of Addmission</th>
                                    <th>Current Balance</th>
                                    <th data-orderable="false">Can Create Sub-Center?</th>
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
        $(function() {

            var table = $('#users-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'pageLength': 5,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/center-master/server'
                },
                'columns': [{
                        data: "Photo",
                        "render": function(data, type, row) {
                            return '<span class="thumbnail-wrapper d48 circular inline">\
      					<img src="' + data + '" alt="" data-src="' + data + '"\
      						data-src-retina="' + data + '" width="32" height="32">\
      				</span>';
                        }
                    },
                    {
                        data: "Name",
                        "render": function(data, type, row) {
                            var code = row.Code;
                            var email = row.Email;
                            return '<strong>' + data + '</strong>\
                            <p class="mb-0"> Code: ' + code + '</p>\
                            <p class="mb-0"> Email: ' + email + '</p>';
                        }
                    },
                    {
                        data: "Password",
                        "render": function(data, type, row) {
                            return '<div class="row" style="width:250px !important;">\
                <div class="col-md-10 px-0">\
                  <input type="password" class="form-control" disabled="" style="border: 0ch;" value="' + data + '" id="myInput' + row.ID + '">\
                </div>\
                <div class="col-md-2">\
                  <i class="fa fa-eye pt-2 cursor-pointer" onclick="showPassword(' + row.ID + ')"></i>\
                </div>\
              </div>';
                        }
                    },
                    {
                        data: "Created_At",
                    },
                    {
                        data: "Admission",
                    },
                    {
                        data: "totalamount",
                    },
                    {
                        data: "CanCreateSubCenter",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Yes' : 'No';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-func="changeSubCenterStatus" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "Status",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Active' : 'Inactive';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
                        },
                        visible: false
                    },
                    {
                        data: "ID",
                        "render": function(data, type, row) {
                            return '<div class="button-list text-end">\
                <i class="fa fa-plus-circle icon-xs text-success cursor-pointer" title="Allot Board" onclick="allot(&#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="fa fa-edit text-warning icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;center-master&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="fa fa-trash text-danger icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;center-master&#39;, &#39;' + data + '&#39)"></i>\
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
                "initComplete": function() {
                    $('.bs_switch').bootstrapSwitch();
                },
            };

            table.dataTable(settings);

            // search box for table
            $('#users-search-table').keyup(function() {
                table.fnFilter($(this).val());
            });

            table.on('draw.dt', function() {
                $('.bs_switch').bootstrapSwitch();
                $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var rowId = $(this).data('row-id');
                    var funcName = $(this).data('func');
                    if (funcName) {
                        changeSubCenterStatus(rowId);
                    } else {
                        changeStatus('Users', rowId);
                    }
                });
            });
        })
    </script>

    <script>
        function allot(id, modal) {
            $.ajax({
                url: '/app/center-master/allot-universities?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#' + modal + '-modal-content').html(data);
                    $('#' + modal + 'modal').modal('show');
                }
            });
        }

        function changeSubCenterStatus(id) {
            $.ajax({
                url: '/app/center-master/sub-center-access',
                type: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        notification('success', data.message);
                        $('#users-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                    }
                }
            });
        }
    </script>

    <script>
        function showPassword(id) {
            var x = document.getElementById("myInput".concat(id));
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>

    <script type="text/javascript">
        function exportData() {
            var search = $('#users-search-table').val();
            var url = search.length > 0 ? "?search=" + search : "";
            window.open('/app/center-master/export' + url);
        }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>