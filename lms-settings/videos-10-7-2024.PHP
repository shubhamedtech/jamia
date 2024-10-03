<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video-js.min.css" rel="stylesheet">
<style>
  .copyright {
    background: #fff;
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
  }

  .stu-e-book-style {
    width: 140px;
    height: 80px;
    align-items: center;
    text-align: center;
    border-radius: 10px;
    background-color: #838383;
  }

  .video-icon {
    font-size: 30px;
    text-align: center;
    color: #fff;
    position: absolute;
    /*color: currentcolor;*/
    top: 22px;
    left: 65px;
    display: none;
    cursor: pointer;

  }

  .subject_name {
    font-size: 18px !important;
    font-weight: 600;
  }

  .container-play-btn {
    position: relative;
    width: 400px;
    height: 200px;
  }

  .play-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* display: none; */
    font-size: 40px;
  }

  .thumbnail {
    height: inherit;
    width: inherit;
    border-radius: 10px;
    cursor: pointer;
  }


  .stu-e-book-style:hover .video-icon {
    display: block;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<?php
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/";
?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">
    <!-- <div class="content-header sty-one">
      <div class="d-flex align-items-center justify-content-between">

        <?php
        // $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        // for ($i = 1; $i <= count($breadcrumbs); $i++) {
        //   if (count($breadcrumbs) == $i) : $active = "active";
        //     $crumb = explode("?", $breadcrumbs[$i]);
        //     echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
        //   endif;
        //}
        ?>

      </div>
    </div> -->

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
              <button onclick="add('videos', 'lg')" class="btn btn-primary p-2 " aria-label="" title="" data-toggle="tooltip" data-original-title="Add Subjects"> <i class="fa fa-plus-circle fa-lg"> Add</i></button>
            </div>
          </div>

          <div class="clearfix"></div>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="video_lectures-table">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Course</th>
                  <th>Semester</th>
                  <th>Unit</th>
                  <th>video</th>
                  <th>Video Categories</th>
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
        var table = $('#video_lectures-table');

        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/lms-settings/video-lectures/data-list'
          },
          'columns': [{
              data: "subject_name"
            },
            {
              data: "course_name"
            },
            {
              data: "semester"
            },
            {
              data: "unit"
            },
            {
              data: "video_url",
              render: function(data, type, row) {
                return '<div class="col-sm-6 mt-2 mb-2"><a href="/student/lms/video-player?id=' + row.ID + '"><div class="stu-e-book-style"><img class="thumbnail" src="<?= $base_url ?>' + row.thumnail_url + '"><p><i class="fa fa-play-circle video-icon"></i></p></div></a></div>';
                //return '<video  width="120px" height="80px" controls="controls"><source src="'+'<?= $base_url ?>'+row.video_url+'" type="video/'+row.video_type+'" /></video>' ;
              }
            },
            {
              data: "video_cat",
              render: function(data, type, row) {
                return data == 1 ? 'Live Lectures' : 'Recorded Lectures';
              }
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
                <i class="fa fa-edit icon-xs text-warning cursor-pointer p-3" onclick="edit(&#39;videos&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                          <i class="fa fa-trash icon-xs text-danger cursor-pointer" onclick="changeStatus(' + "'video_lectures'" + ', ' + data + ',' + "'status'" + ',2)"></i>\
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
            changeStatus('video_lectures', rowId, 'status');
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
            url: '/app/videos/syllabus?course_id=' + course_id + '&semester=' + semester,
            type: 'GET',
            success: function(data) {
              $('#subjects').html(data);
            }
          })
        } else {
          $.ajax({
            url: '/app/videos/syllabus',
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
          url: '/app/videos/create?unit_id=' + unit_id + '&syllabus_id=' + syllabus_id + '&sem=' + sem,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <script>
      function videoList(id, unit_id, sub_id) {
        // console.log(id, 'sandip',sub_id,  unit_id);
        $.ajax({
          url: '/app/videos/show-list',
          type: 'POST',
          data: {
            "id": id,
            "sub_id": sub_id,
            "unit_id": unit_id
          },
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          },
          complete: function() {
            $('.video-js').each(function() {
              videojs(this, {
                width: 300,
                height: 150,
                controls: true,
                preload: "auto",
              });
            });
          }
        })
      }
    </script>
    <script>
      function addAssessment(id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/assessments/create?unit_id=' + unit_id + '&syllabus_id=' + sub_id + '&video_id=' + id,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>
    <script>
      function assessmentList(id, video_id, unit_id, sub_id) {
        $.ajax({
          url: '/app/videos/assessments/show-list',
          type: 'POST',
          data: {
            "id": id,
            "video_id": video_id,
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

    <script src="https://cdn.jsdelivr.net/npm/video.js@8.6.0/dist/video.min.js"></script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>