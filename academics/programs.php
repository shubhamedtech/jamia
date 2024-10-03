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
                <div>
                    <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="fa fa-download fa-lg"></i></button>
                    <!-- <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Program" onclick="add('courses','md')"> <i class="fa fa-plus-circle fa-lg"></i></button> -->
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card card-transparent">
                <div class="card-header">
                    <div class="pull-right">
                        <div class="col-xs-12">
                            <input type="text" id="courses-search-table" class="form-control pull-right" placeholder="Search">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" id="courses-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Type</th>
                                    <th>Department</th>
                                    <th data-orderable="false">Board</th>
                                  	<th data-orderable="false">Exam Fee</th>
                                  	<th data-orderable="false">Registration Fee</th>
                                    <th data-orderable="false">Status</th>
                                    <!--  <th data-orderable="false">Actions</th> -->
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
            var table = $('#courses-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/courses/server'
                },
                'columns': [{
                        data: "Name"
                    },
                    {
                        data: "Short_Name"
                    },
                    {
                        data: "Type"
                    },
                    {
                        data: "Department_ID"
                    },
                    {
                        data: "University",
                        visible: show
                    },
                    {
                        data: "Exam_Fee",
                        visible: show
                    },
                    {
                        data: "Registration_Fee",
                        visible: show
                    },
                    {
                        data: "Status",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Active' : 'Inactive';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
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
            $('#courses-search-table').keyup(function() {
                table.fnFilter($(this).val());
            });
            table.on('draw.dt', function() {
                $('.bs_switch').bootstrapSwitch();
                $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var rowId = $(this).data('row-id');
                    changeStatus('Courses', rowId);
                });
            });
        })
    </script>

    <script type="text/javascript">
        function changeColumnStatus(id, column) {
            $.ajax({
                url: '/app/courses/status',
                type: 'post',
                data: {
                    id: id,
                    column: column
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        notification('success', data.message);
                        $('#courses-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                        $('#courses-table').DataTable().ajax.reload(null, false);
                    }
                }
            })
        }

        function addStudentID(id) {
            $.ajax({
                url: '/app/courses/student-id?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            })
        }

        function addCenterCode(id) {
            $.ajax({
                url: '/app/courses/center-code?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            })
        }
    </script>

    <script type="text/javascript">
        function exportData() {
            var search = $('#courses-search-table').val();
            var url = search.length > 0 ? "?search=" + search : "";
            window.open('/app/courses/export' + url);
        }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>