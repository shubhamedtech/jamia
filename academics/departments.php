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
                <button class="btn btn-link" aria-label="Add Department" data-toggle="tooltip" data-placement="top" title="Add Department" onclick="add('departments','md')"><i class="fa fa-plus-circle fa-lg"></i></button>
            </div>
        </div>

        <div class="content">
            <div class="card card-transparent">
                <div class="card-header">
                    <div class="pull-right">
                        <div class="col-xs-12">
                            <input type="text" id="departments-search-table" class="form-control pull-right" placeholder="Search">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" id="departments-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th data-orderable="false">Board</th>
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
            var role = '<?= $_SESSION['Role'] ?>';
            var show = role == 'Administrator' ? true : false;
            var table = $('#departments-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/departments/server'
                },
                'columns': [{
                        data: "Name"
                    },
                    {
                        data: "University",
                        visible: show
                    },
                    {
                        data: "Status",
                        "render": function(data, type, row) {
                            var checked = data == 1 ? 'checked' : '';
                            var switchId = 'status-switch-' + row.ID;
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "ID",
                        "render": function(data, type, row) {
                            return '<div class="d-flex align-items-center text-center">\
                            <i class="fa fa-edit text-warning icon-xs cursor-pointer mr-2" onclick="edit(&#39;departments&#39;, &#39;' + data + '&#39, &#39;md&#39;)"></i>\
                            <i class="fa fa-trash text-danger icon-xs cursor-pointer " onclick="destroy(&#39;departments&#39;, &#39;' + data + '&#39)"></i>\
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
            };

            table.dataTable(settings);

            // search box for table
            $('#departments-search-table').keyup(function() {
                table.fnFilter($(this).val());
            });
            table.on('draw.dt', function() {
                $('.bs_switch').bootstrapSwitch();
                $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var rowId = $(this).data('row-id');
                    changeStatus('Departments', rowId);
                });
            });
        });
    </script>

    <script type="text/javascript">
        function changeColumnStatus(id, column) {
            $.ajax({
                url: '/app/departments/status',
                type: 'post',
                data: {
                    id: id,
                    column: column
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        notification('success', data.message);
                        $('#departments-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                        $('#departments-table').DataTable().ajax.reload(null, false);
                    }
                }
            })
        }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>