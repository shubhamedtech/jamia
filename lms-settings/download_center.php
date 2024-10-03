<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<div class="wrapper boxed-wrapper">
    <!-- Topbar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- Menu -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
    <style>
        .stu-e-book-style {
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            text-align: center;
            cursor: pointer;
        }
    </style>
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
                    <button class="btn btn-sm btn btn-success" aria-label="Add Files" data-toggle="tooltip" data-placement="top" title="Add Files" onclick="add('download-center','md')">Add Files</button>
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
                        <table class="table table-hover nowrap w-100 display" style="width:100%" id="subjects-table">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Subject Name</th>
                                    <th>File Remark</th>
                                    <th>University Name</th>
                                    <th>Created Date</th>
                                    <th>Updated Date</th>
                                    <th>Files</th>
                                    <th>Status</th>
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
                'url': '/app/download-center/server'
            },
            'columns': [{
                    data: "course_name"
                },
                {
                    data: "subject_name"
                },
                {
                    data: "reason"
                },
                {
                    data: "universityname"
                },
                {
                    data: "created_date"
                },
                {
                    data: "updated_date"
                },
                {
                    data: "files",
                    render: function(data, type, row) {
                        var path = ''; // '/../uploads/download-centers/';
                        var fileName = data;
                        var fileLink = '<a href="' + path + fileName + '" download>' +
                            '<div class="stu-e-book-style">' +
                            '<i class="fa fa-download"></i> Download Files</div></a>';

                        var fileExtension = fileName.split('.').pop().toLowerCase();
                        var fileViewerLink = '';

                        switch (fileExtension) {
                            case 'mp4':
                            case 'webm':
                            case 'ogg':
                                fileViewerLink = '<a href="#" onclick="openModal(\'' + path + fileName + '\', \'' + fileName + '\'); return false;">' +
                                    '<div class="stu-e-book-style">' +
                                    '<i class="fa fa-play-circle video-icon"></i> View File</div></a>';
                                break;
                            case 'jpg':
                            case 'jpeg':
                            case 'png':
                                fileViewerLink = '<a href="#" onclick="openModal(\'' + path + fileName + '\', \'' + fileName + '\'); return false;">' +
                                    '<div class="stu-e-book-style">' +
                                    '<i class="fas fa-image" style="font-size:14px"></i> View File</div></a>';
                                break;
                            case 'pdf':
                                fileViewerLink = '<a href="#" onclick="openModal(\'' + path + fileName + '\', \'' + fileName + '\'); return false;">' +
                                    '<div class="stu-e-book-style">' +
                                    '<i class="fa fa-file-pdf-o" style="font-size:14px"></i> View File</div></a>';
                                break;
                            case 'xls':
                            case 'xlsx':
                                fileViewerLink = '<a href="#" onclick="openModal(\'' + path + fileName + '\', \'' + fileName + '\'); return false;">' +
                                    '<div class="stu-e-book-style">' +
                                    '<i class="fa fa-table"></i> View File</div></a>';
                                break;
                            default:
                                fileViewerLink = '<div class="stu-e-book-style">' +
                                    '<i class="fa fa-file"></i> Unsupported File Type</div>';
                        }

                        return fileViewerLink + '<br>' + fileLink;
                    }
                },
                {
                    data: "status",
                    "render": function(data, type, row) {
                        var checked = data == 1 ? 'checked' : '';
                        var switchId = 'status-switch-' + row.id;
                        return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.id + '" ' + checked + '>';
                    }
                },
                {
                    data: "id",
                    "render": function(data, type, row) {
                        return '<div class="button-list text-end">\
                  <i class="fa fa-edit text-warning icon-xs cursor-pointer" onclick="edit(&#39;download-center&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                  <i class="fa fa-trash text-danger icon-xs cursor-pointer" onclick="destroy(&#39;download-center&#39;, &#39;' + data + '&#39)"></i>\
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
        let res = table.dataTable(settings);
        $('#subject-search-table').keyup(function() {
            table.fnFilter($(this).val());
        });
        table.on('draw.dt', function() {
            $('.bs_switch').bootstrapSwitch();
            $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                var rowId = $(this).data('row-id');
                changeStatus('Download_Centers', rowId);
            });
        });
    </script>

    <div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="fileContent">
                </div>
            </div>
        </div>
    </div>
    <script>
        function openModal(filePath, fileName) {
            var fileContent = '';
            var fileExtension = fileName.split('.').pop().toLowerCase();
            switch (fileExtension) {
                case 'mp4':
                case 'webm':
                case 'ogg':
                    fileContent = '<video controls width="100%"><source src="' + filePath + '" type="video/' + fileExtension + '">Your browser does not support the video tag.</video>';
                    break;
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                    fileContent = '<img src="' + filePath + '" class="img-fluid">';
                    break;
                case 'pdf':
                    fileContent = '<iframe src="' + filePath + '" width="100%" height="500px"></iframe>';
                    break;
                case 'xls':
                case 'xlsx':
                    fileContent = '<iframe src="https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(filePath) + '" width="100%" height="500px"></iframe>';
                    break;
                default:
                    fileContent = 'File type not supported for inline viewing.';
            }
            $('#fileContent').html(fileContent);
            $('#fileModal').modal('show');
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.7/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sheetjs/0.18.7/sheetjs.min.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>