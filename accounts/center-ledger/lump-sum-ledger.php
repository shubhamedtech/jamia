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
  </div>

  <div class="content">
    <div class="row d-flex justify-content-center">
      <div class="card">
        <div class="card-body">
          <div class="col-md-8">
            <div class="form-group form-group-default required">
              <label>Centers</label>
              <select class="form-control"  data-init-plugin="select2" id="center" onchange="getLedger(this.value)">
                <option value="">Select</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row m-t-20">
      <div class="card card-transparent">
        <div class="col-lg-12">
          <div class="card-header tab-style1">
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="active nav-link" data-toggle="tab" data-target="#students" href="#"><span>Students</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" data-target="#invoices" href="#"><span>Invoices</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" data-target="#ledger" href="#"><span>Ledger</span></a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">
              <div class="tab-pane active" id="students">
                <div class="row">
                  <div class="col-md-12 text-center">
                    Please select center!
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="invoices">
                <div class="row">
                  <div class="col-md-12 text-center">
                    Please select center!
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="ledger">
                <div class="row">
                  <div class="col-md-12 text-center">
                    Please select center!
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script>
    function getLedger(id) {
      getStudentList(id);
      getInvoiceList(id);
      getCenterLedger(id);
    }

    function getStudentList(id) {
      $.ajax({
        url: '/app/centers/ledgers/lump-sum/students?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#students").html(data);
        }
      })
    }

    function getInvoiceList(id) {
      $.ajax({
        url: '/app/centers/ledgers/lump-sum/invoices?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#invoices").html(data);
        }
      })
    }

    function getCenterLedger(id) {
      $.ajax({
        url: '/app/centers/ledgers/lump-sum/ledger?id=' + id,
        type: 'GET',
        success: function(data) {
          $("#ledger").html(data);
        }
      })
    }

    function showStudents(id) {
      $.ajax({
        url: '/app/centers/ledgers/lump-sum/list?id=' + id,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $('#mdmodal').modal('show');
        }
      })
    }

    getCenterList('center');
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>