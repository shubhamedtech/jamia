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
                    <button class="btn btn-link p-2" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Program" onclick="add('universities','lg')"> <i class="fa fa-plus-circle fa-lg"></i></button>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card card-transparent">
                <div class="card-header">
                    <div class="pull-right">
                        <div class="col-xs-12">
                            <input type="text" id="universities-search-table" class="form-control pull-right" placeholder="Search">
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap" id="universities-table">
                            <thead>
                                <tr>
                                    <th data-orderable="false">Logo</th>
                                    <th>Name</th>
                                    <th>Vertical</th>
                                    <th data-orderable="false">Status</th>
                                    <th data-orderable="false">Type</th>
                                    <th data-orderable="false">Options</th>
                                    <th data-orderable="false">LMS</th>
                                    <th data-orderable="false">Unique Center Code</th>
                                    <th data-orderable="false">Unique Student ID</th>
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

            var table = $('#universities-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/universities/server'
                },
                'columns': [{
                        data: "Logo",
                        "render": function(data, type, row) {
                            return '<img src="' + data + '" width="60px" />'
                        }
                    },
                    {
                        data: "Short_Name"
                    },
                    {
                        data: "Vertical"
                    },
                    {
                        data: "Status",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Active' : 'Inactive';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-fn="status" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "Is_B2C",
                        "render": function(data, type, row) {
                            var type = data == 1 ? 'University is dealing<br>with Students.' : data == 2 ? 'Board is dealing<br>with both Outsourced Partners and Students.' : 'Board is dealing<br>with Outsourced Partners.';
                            return type;
                        }
                    },
                    {
                        data: "Is_Vocational",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Has Vocational Courses' : 'Don\'t have Vocational Courses';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-fn="columnStatus" data-property="Is_Vocational" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "Has_LMS",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Has LMS' : 'Don\'t have LMS';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-fn="columnStatus" data-property="Has_LMS" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "Has_Unique_Center",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Has Unique Center Code' : 'Don\'t have Unique Center Code';
                            var checked = data == 1 ? 'checked' : '';
                            var character = 'XXXX';
                            var centerCode = row.Center_Suffix != '' ? '<span>Center Code: <b>' + row.Center_Suffix + character + '</b></span>' : '<span>Please create Center Code</span>';
                            var edit = data == 1 ? '<span><i class="fa fa-cog icon-xs cursor-pointer" onclick="addCenterCode(' + row.ID + ')"></i></span>' : '';
                            var generator = data == 1 ? centerCode + edit : edit;
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-fn="columnStatus" data-property="Has_Unique_Center" data-row-id="' + row.ID + '" ' + checked + '>\
                            </div><br><p>' + generator + '</p>';
                        }
                    },
                    {
                        data: "Has_Unique_StudentID",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Has unique Student ID' : 'Don\'t have a unique Student ID';
                            var checked = data == 1 ? 'checked' : '';
                            var studentID = row.Max_Character != '' ? '<span>Student ID: <b>' + row.ID_Suffix + row.Max_Character + '</b></span>' : '<span>Please create Student ID</span>';
                            var edit = data == 1 ? '<span><i class="fa fa-cog icon-xs cursor-pointer" onclick="addStudentID(' + row.ID + ')"></i></span>' : '';
                            var generator = data == 1 ? studentID + edit : edit;
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-fn="columnStatus" data-property="Has_Unique_StudentID" data-row-id="' + row.ID + '" ' + checked + '>\
                      </div><br><p>' + generator + '</p>';
                        }
                    },
                    {
                        data: "ID",
                        "render": function(data, type, row) {
                            return '<div class="button-list text-end">\
                <i class="fa fa-edit icon-xs text-warning cursor-pointer" onclick="edit(&#39;universities&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                <i class="fa fa-trash icon-xs text-danger cursor-pointer" onclick="destroy(&#39;universities&#39;, &#39;' + data + '&#39)"></i>\
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
            $('#universities-search-table').keyup(function() {
                table.fnFilter($(this).val());
            });
            table.on('draw.dt', function() {
                $('.bs_switch').bootstrapSwitch();
                $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var rowId = $(this).data('row-id');
                    var fn = $(this).data('fn');
                    if (fn == 'status') {
                        changeStatus('Universities', rowId);
                    } else if (fn == 'columnStatus') {
                        var property = $(this).data('property');
                        changeColumnStatus(rowId, property)
                    }
                });
            });
        })
    </script>

    <script type="text/javascript">
        function changeColumnStatus(id, column) {
            $.ajax({
                url: '/app/universities/status',
                type: 'post',
                data: {
                    id: id,
                    column: column
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        notification('success', data.message);
                        $('#universities-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                        $('#universities-table').DataTable().ajax.reload(null, false);
                    }
                }
            })
        }

        function addStudentID(id) {
            $.ajax({
                url: '/app/universities/student-id?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            })
        }

        function addCenterCode(id) {
            $.ajax({
                url: '/app/universities/center-code?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            })
        }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>