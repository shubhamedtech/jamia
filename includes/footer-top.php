<div class="modal fade slide-up" id="mdmodal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content-wrapper">
            <div class="modal-content" id="md-modal-content">
            </div>
        </div>
    </div>
</div>

<div class="modal fade slide-up" id="lgmodal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content-wrapper">
            <div class="modal-content" id="lg-modal-content">
            </div>
        </div>
    </div>
</div>

<div class="modal fade fill-in" id="fullmodal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-x-circle"></i>
    </button>
    <div class="modal-dialog" style="min-width: 100% !important">
        <div class="modal-content" style="display: inline-block" id="full-modal-content">

        </div>
    </div>
</div>
<!-- Modal End -->
<footer class="bg-white rounded-0 shadow-top text-center w-100">
    Copyright &copy; <?php echo (date('Y')) ?>. All rights reserved by
    <?= $app_title ?>
</footer>
</div>
<script src="/assets/js/jquery.min.js"></script>
<script src="/assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/js/adminkit.js"></script>
<script src="/assets/plugins/jquery-validate/jquery.validate.min.js"></script>
<script src="/assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/media/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/TableTools/js/dataTables.tableTools.min.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/media/js/dataTables.bootstrap.js" type="text/javascript"></script>
<script src="/assets/plugins/jquery-datatable/extensions/Bootstrap/jquery-datatable-bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/datatables.responsive.js"></script>
<script type="text/javascript" src="/assets/plugins/datatables-responsive/js/lodash.min.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-autonumeric/autoNumeric.js"></script>
<script src="/assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/assets/plugins/bootstrap-switch/bootstrap-switch.js"></script>
<script src="/assets/plugins/bootstrap-switch/highlight.js"></script>
<script src="/assets/plugins/bootstrap-switch/main.js"></script>


<script type="text/javascript">
    $(function() {
        $('.autonumeric').autoNumeric('init');
    })
</script>

<script type="text/javascript">
    function add(url, modal) {
        $.ajax({
            url: '/app/' + url + '/create',
            type: 'GET',
            success: function(data) {
                $('#' + modal + '-modal-content').html(data);
                $('#' + modal + 'modal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function excelImport(url, modal) {
        $.ajax({
            url: '/app/' + url + '/excel-import',
            type: 'GET',
            success: function(data) {
                $('#' + modal + '-modal-content').html(data);
                $('#' + modal + 'modal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function upload(url, modal) {
        $.ajax({
            url: '/app/' + url + '/upload',
            type: 'GET',
            success: function(data) {
                $('#' + modal + '-modal-content').html(data);
                $('#' + modal + 'modal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function edit(url, id, modal) {
        $.ajax({
            url: '/app/' + url + '/edit?id=' + id,
            type: 'GET',
            success: function(data) {
                $('#' + modal + '-modal-content').html(data);
                $('#' + modal + 'modal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function changeStatus(table, id, column = null,status=null) {
        if(status!=null){
            $(".modal").modal('hide');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/app/status/update',
                        type: 'post',
                        data: {
                            table,
                            id,
                            column,
                            status
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 200) {
                                notification('success', data.message);
                                //var datatable = table == 'Students' ? 'application' : table.toLowerCase();
                                $('#' + table + '-table').DataTable().ajax.reload(null, false);
                            } else {
                                notification('danger', data.message);
                                $('#' + table + '-table').DataTable().ajax.reload(null, false);
                            }
                        }
                    });

                }
            });
        }else{
            $.ajax({
                url: '/app/status/update',
                type: 'post',
                data: {
                    table,
                    id,
                    column,
                    status
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 200) {
                        notification('success', data.message);
                        //var datatable = table == 'Students' ? 'application' : table.toLowerCase();
                        $('#' + table + '-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                        $('#' + table + '-table').DataTable().ajax.reload(null, false);
                    }
                }
            });
        }
    }
</script>

<script type="text/javascript">
    function changeStatus_old(table, id, column = null) {
        $.ajax({
            url: '/app/status/update',
            type: 'post',
            data: {
                table,
                id,
                column
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 200) {
                    notification('success', data.message);
                    var datatable = table == 'Students' ? 'application' : table.toLowerCase();
                    $('#' + datatable + '-table').DataTable().ajax.reload(null, false);;
                } else {
                    notification('danger', data.message);
                    $('#' + table + '-table').DataTable().ajax.reload(null, false);;
                }
            }
        });
    }
</script>

<script type="text/javascript">
    function destroy(url, id) {
        $(".modal").modal('hide');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/app/" + url + "/destroy?id=" + id,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            notification('success', data.message);
                            $('.table').DataTable().ajax.reload(null, false);;
                        } else {
                            notification('danger', data.message);
                        }
                    }
                });
            }
        })
    }
</script>

<script type="text/javascript">
    function notification(type, message) {
        Swal.fire({
            title: message,
            icon: type,
            position: 'top-end',
            toast: true,
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false,

        })
    }
</script>

<script type="text/javascript">
    function changeUniversity(id) {
        $.ajax({
            url: '/app/login/change-university',
            type: 'POST',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    window.location.reload();
                } else {
                    notification('danger', data.message);
                }
            }
        })
    }
</script>

<script type="text/javascript">
    function changeUniversity() {
        $.ajax({
            url: '/app/alloted-universities/universities',
            type: 'GET',
            success: function(data) {
                $('#lg-modal-content').html(data);
                $('#lgmodal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function changePassword() {
        $.ajax({
            url: '/app/password/edit',
            type: 'GET',
            success: function(data) {
                $('#md-modal-content').html(data);
                $('#mdmodal').modal('show');
            }
        })
    }
</script>

<script type="text/javascript">
    function getStudentList(id) {
        $.ajax({
            url: '/app/students/student-list',
            type: 'GET',
            success: function(data) {
                $("#" + id).html(data);
            }
        })
    }

    function getCenterList(id) {
        $.ajax({
            url: '/app/students/center-list',
            type: 'GET',
            success: function(data) {
                $("#" + id).html(data);
            }
        })
    }
</script>

<?php if ($_SESSION['crm'] != 0) { ?>
    <script type="text/javascript">
        function addQuickLead() {
            $.ajax({
                url: '/app/leads/create_quick',
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }
    </script>

    <script type="text/javascript">
        function checkEmail(value, error_id) {
            const university_id = $('#quick_university_id').val();
            if (isEmail(value)) {
                $.ajax({
                    url: '/app/leads/check_email?email=' + value + '&university_id=' + university_id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(data) {
                        if (data.status == 302) {
                            $('#' + error_id).html(data.message);
                            $(':input[type="submit"]').prop('disabled', true);
                        } else {
                            $(':input[type="submit"]').prop('disabled', false);
                            $('#' + error_id).html('');
                        }
                    }
                })
            } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#' + error_id).html('');
            }
        }

        function checkMobile(value, error_id) {
            const university_id = $('#quick_university_id').val();
            if (isMobile(value)) {
                $.ajax({
                    url: '/app/leads/check_mobile?mobile=' + value + '&university_id=' + university_id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(data) {
                        if (data.status == 302) {
                            $('#' + error_id).html(data.message);
                            $(':input[type="submit"]').prop('disabled', true);
                        } else {
                            $(':input[type="submit"]').prop('disabled', false);
                            $('#' + error_id).html('');
                        }
                    }
                })
            } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#' + error_id).html('');
            }
        }

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if (regex.test(email)) {
                return true;
            } else {
                return false;
            }
        }

        function isMobile(mobile) {
            var regex = /[1-9]{1}[0-9]{9}/;
            if (regex.test(mobile)) {
                return true;
            } else {
                return false;
            }
        }
    </script>
<?php } ?>