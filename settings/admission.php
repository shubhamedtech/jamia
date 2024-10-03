<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>

<div class="wrapper boxed-wrapper">
  <!-- topbar -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- menu -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . ('/includes/menu.php')) ?>

  <div class="content-wrapper">
    <div class="content-header sty-one">
      <div class="d-flex justify-content-between">
        <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
        for ($i = 1; $i <= count($breadcrumbs); $i++) {
          if (count($breadcrumbs) == $i) : $active = "active";
            $crumb = explode("?", $breadcrumbs[$i]);
            echo '<h1 class="text-capitalize d-inline fw-bold">' . $crumb[0] . '</h1>';
          endif;
        }
        ?>
      </div>
    </div>

    <div class="content" id="main-content">
      <div class="row">
        <?php if ($_SESSION['Role'] == 'Administrator') {
          $universities = $conn->query("SELECT ID, Short_Name, Vertical, Logo FROM Universities");
          if ($universities->num_rows > 0) {
            while ($university = $universities->fetch_assoc()) { ?>
              <div class="col-lg-3 sm-no-padding">
                <div class="card card-transparent">
                  <div class="card-body no-padding">
                    <div onclick="getComponents('<?php echo base64_encode($university['ID']) ?>')" class="card card-default">
                      <div class="card-header">
                        <div class="card-title bold">
                          <?php echo $university['Short_Name'] . " (" . $university['Vertical'] . ")" ?>
                        </div>
                      </div>
                      <div class="card-body" style="min-height: 150px !important">
                        <div class="row m-t-20">
                          <div class="d-flex justify-content-center col-md-12 cursor-pointer">
                            <img src="<?php echo $university['Logo'] ?>" style="max-width:100% !important" height="100px">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php }
          } else { ?>
            <div class="container mt-3">
              <div class="d-flex justify-content-center mb-3">
                <div class="card">
                  <div class="card-body text-center">
                    <h5 class="semi-bold">Please add <a href="/academics/universities"><span class="text-primary"><u>University</u></span></a></h5>
                  </div>
                </div>
              </div>
            </div>
        <?php }
        }
        ?>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script type="text/javascript">
    function getComponents(id) {
      $.ajax({
        url: '/app/components/main?id=' + id,
        type: "GET",
        success: function(data) {
          $("#main-content").html(data);
        }
      })
    }

    <?php if ($_SESSION['Role'] == 'University Head') { ?>
      getComponents('<?php echo base64_encode($_SESSION['university_id']) ?>');
    <?php } ?>

    function addComponents(url, modal, university_id) {
      $.ajax({
        url: '/app/components/' + url + '/create?university_id=' + university_id,
        type: 'GET',
        success: function(data) {
          $('#' + modal + '-modal-content').html(data);
          $('#' + modal + 'modal').modal('show');
        }
      })
    }

    function editComponents(url, id, modal) {
      $.ajax({
        url: '/app/components/' + url + '/edit?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#' + modal + '-modal-content').html(data);
          $('#' + modal + 'modal').modal('show');
        }
      })
    }

    function changeComponentStatus(table, datatable, id) {
      $.ajax({
        url: '/app/status/update',
        type: 'post',
        data: {
          "table": table,
          "id": id
        },
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            notification('success', data.message);
            $('#table' + datatable).DataTable().ajax.reload(null, false);;
          } else {
            notification('danger', data.message);
            $('#table' + datatable).DataTable().ajax.reload(null, false);;
          }
        }
      });
    }

    function destroyComponents(url, table, id) {
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
            url: "/app/components/" + url + "/destroy?id=" + id,
            type: 'DELETE',
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                $('#table' + table).DataTable().ajax.reload(null, false);;
              } else {
                notification('danger', data.message);
              }
            }
          });
        }
      })
    }
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>