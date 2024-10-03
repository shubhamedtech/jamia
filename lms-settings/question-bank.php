<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php 
  $base_url="http://".$_SERVER['HTTP_HOST']."/";
?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">


    <div class="content">
      <div class="card">
        <div class="card-header">
          <?php 
            $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
            for ($i = 1; $i <= count($breadcrumbs); $i++) {
              if (count($breadcrumbs) == $i) : $active = "active";
                $crumb = explode("?", $breadcrumbs[$i]);
                echo '<h4 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h4>';
              endif;
            }
          ?>
           
          <div class="row pull-right">
            <div class="col-xs-7" style="margin-right: 10px;">
              <input type="text" id="courses-search-table" class="form-control pull-right p-2 fw-bold" placeholder="Search">
            </div>
              <div class="col-xs-5" style="margin-right: 10px;">
                <button onclick="add('question-banks', 'lg')" class="btn btn-primary p-2 " aria-label="" title="" data-toggle="tooltip" data-original-title="Add Subjects"> <i class="fa fa-plus-circle fa-lg"> Add</i></button>
              </div>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="question-banks-table">
              <thead>
                <tr>
                  <th>Course</th>
                  <th>Subject</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
        $(function() {
            var role = '<?= $_SESSION['Role'] ?>';
            var show = role == 'Administrator' ? true : false;
            var table = $('#question-banks-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/lms-settings/question-banks/data-list'
                },
                'columns': [{
                        data: "course_name"
                    },
                    {
                        data: "subject_name"
                    },
                    {
                        data: "file_type"
                    },
                    {
                        data: "status",
                        "render": function(data, type, row) {
                            var active = data == 1 ? 'Active' : 'Inactive';
                            var checked = data == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    },
                    {
                        data: "ID",
                        "render": function(data, type, row) {
                            return '<div class="button-list text-end">\
                          <i class="fa fa-trash icon-xs text-danger cursor-pointer" onclick="changeStatus('+"'question_banks'"+', '+data+','+"'status'"+',2)"></i>\
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
            $('#courses-search-table').keyup(function() {
                table.fnFilter($(this).val());
            });
            table.on('draw.dt', function() {
                $('.bs_switch').bootstrapSwitch();
                $('.bs_switch').on('switchChange.bootstrapSwitch', function(event, state) {
                    var rowId = $(this).data('row-id');
                    changeStatus('question_banks', rowId,'status');
                });
            });
          })
    </script>













    <script type="text/javascript">
      function getSemester(id) {
        $.ajax({
          url: '/app/subjects/semester?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#semester").html(data);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function getTable() {
        var course_id = $('#course').val();
        var semester = $('#semester').val();
        if (course_id.length > 0 && semester.length > 0) {
          $.ajax({
            url: '/app/e-books/syllabus?course_id=' + course_id + '&semester=' + semester,
            type: 'GET',
            success: function(data) {
              $('#subjects').html(data);
            }
          })
        } else {
          $.ajax({
            url: '/app/e-books/syllabus',
            type: 'GET',
            success: function(data) {
              $('#subjects').html(data);
            }
          })
        }
      }
      getTable();

      function removeTable() {
        $('#course').html('');
      }
    </script>

    <script type="text/javascript">
      function uploadFile(unit_id, syllabus_id, sem) {
        $.ajax({
          url: '/app/e-books/create?unit_id=' + unit_id + '&syllabus_id=' + syllabus_id + '&sem=' + sem,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      function E_bookList(id, unit_id, sub_id, ) {
        // console.log(id, 'sandip',sub_id,  unit_id);
        $.ajax({
          url: '/app/e-books/show-list',
          type: 'POST',
          data: {
            "id": id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>
     <script>

    </script>
     <script>
      function assessmentList(assessment_id, ebook_id, unit_id, sub_id){
        $.ajax({
          url: '/app/e-books/assessments/show-list',
          type: 'POST',
          data: {
            "assessment_id": assessment_id,
            "ebook_id" : ebook_id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>



    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>