<?php
session_start();
if (isset($_SESSION["Password"]) || isset($_SESSION["Unique_ID"])) {
  header("Location: /dashboard");
}
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">

  <?php if (!empty($dark_logo)) { ?>
    <div class="text-center">
      <link rel="icon" type="image/png" sizes="16x16" href="<?= $dark_logo ?>" />
    </div>
  <?php } ?>


  <title>Login | <?= $app_title ?></title>

  <link rel="stylesheet" href="./assets/bootstrap/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
  <link rel="stylesheet" href="./assets/css/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="./assets/css/et-line-font/et-line-font.css">
  <link rel="stylesheet" href="./assets/css/themify-icons/themify-icons.css">
  <link href="assets/css/toastr.min.css" rel="stylesheet" type="text/css" />

</head>

<body class=" login-page">
  <div class="login-box">
    <div class="login-box-body">
      <?php if (!empty($dark_logo)) { ?>
        <div class="text-center">
          <img src="<?= $dark_logo ?>" alt="logo" class="img-fluid mb-2" data-src="<?= $dark_logo ?>" data-src-retina="<?= $dark_logo ?>" width="150">
        </div>
      <?php } ?>
      <h3 class="login-box-msg fw-bold">Sign In</h3>
      <form id="form-login" role="form" autocomplete="off" action="app/login/login">
        <div class="form-group has-feedback">
          <input type="text" name="username" class="form-control sty1 text-uppercase" placeholder="Username" required>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control sty1" placeholder="Password" required>
        </div>
        <div>
          <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input type="checkbox">
                Remember Me </label>
              <a href="pages-recover-password.html" class="pull-right"><i class="fa fa-lock"></i> Forgot password?</a>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-xs-4 m-t-1">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
  </div>

  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
  <script src="assets/plugins/liga.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
  <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
  <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
  <script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <!-- END VENDOR JS -->
  <script src="assets/js/toastr.min.js"></script>

  <script>
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "3000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
  </script>

  <script>
    $(function() {
      $('#form-login').validate();
      $("#form-login").on("submit", function(e) {
        if ($('#form-login').valid()) {
          $(':input[type="submit"]').prop('disabled', true);
          var formData = new FormData(this);
          $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
              if (data.status == 200) {
                toastr.success(data.message);
                window.setTimeout(function() {
                  window.location.href = data.url;
                }, 1000);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                toastr.error(data.message);
              }
            }
          });
          e.preventDefault();
        }
      });
    })
  </script>
</body>

</html>