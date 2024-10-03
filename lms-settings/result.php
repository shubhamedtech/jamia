<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php 
  $base_url="http://".$_SERVER['HTTP_HOST']."/";
?>

<div class="wrapper boxed-wrapper">
  
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  
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
              <div class="col-xs-3" style="margin-right: 10px;">
                <button onclick="add('results', 'lg')" class="btn btn-primary p-2 " aria-label="" title="" data-toggle="tooltip" data-original-title="Add Subjects"> <i class="fa fa-plus-circle fa-lg"> Add</i></button>
              </div>
              <div class="col-xs-2" style="margin-right: 10px;">
                <button onclick="excelImport('results', 'lg')" class="btn btn-primary p-2 " aria-label="" title="" data-toggle="tooltip" data-original-title="import"> <i class="fa fa-lg fa-upload"> </i></button>
              </div>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="results-table">
              <thead>
                <tr>
                  <th>Student Name</th>
                  <th>Student ID</th>
                  <th>Enrollment No</th>
                  <th>Course Name</th>
                  <th>Published By</th>
                  <th>Published At</th>
                  <th>Status</th>
                  <!-- <th>Action</th> -->
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
            var table = $('#results-table');

            var settings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/results/server'
                },
                'columns': [
                    {
                        data: "student_name"
                    },
                    {
                        data: "unique_id"
                    },
                    {
                        data: "enrollment_no"
                    },
                    
                    {
                        data: "course_name"
                    },
                    {
                        data: "published_by"
                    },
                    {
                        data: "published_on"
                    },
                   
                    {
                        data: "status",
                        "render": function(data, type, row) {
                            var active = row.status == 1 ? 'Active' : 'Inactive';
                            var checked = row.status == 1 ? 'checked' : '';
                            return '<input class="bs_switch" type="checkbox" data-size="small" data-on-color="success" data-off-color="danger" data-row-id="' + row.ID + '" ' + checked + '>';
                        }
                    }
                    // {
                    //     data: "ID",
                    //     "render": function(data, type, row) {
                    //         return '<div class="button-list text-end">\
                    //       <i class="fa fa-trash icon-xs text-danger cursor-pointer" onclick="changeStatus('+"'e_books'"+', '+data+','+"'status'"+',2)"></i>\
                    //     </div>'
                    //     }
                    // },
                ],
                "sDom": "<t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "aaSorting": [],
                "iDisplayLength": 10,
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
                    
                    changeStatus('results', rowId,'status');
                });
            });
          })

    </script>



    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>