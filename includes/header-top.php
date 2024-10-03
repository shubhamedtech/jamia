<?php
function session_error_function()
{
    header("Location: /");
}
set_error_handler('session_error_function');
session_start();
if (!isset($_SESSION['Role'])) {
    header("Location: /");
}
restore_error_handler();
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: text/html; charset=utf-8');

include($_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title> <?= $organization_name ?> | <?php print $_SESSION['Role'] == 'Administrator' ? 'Admin' : $_SESSION['university_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/logo.png" />
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/assets/css/et-line-font/et-line-font.css" />
    <link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
    <link rel="stylesheet" href="/assets/css/simple-lineicon/simple-line-icons.css" />
    <link href="/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/media/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/jquery-datatable/extensions/FixedColumns/css/dataTables.fixedColumns.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/plugins/datatables-responsive/css/datatables.responsive.css" rel="stylesheet" type="text/css" media="screen" />
    <link rel="stylesheet" href="/assets/css/style.css?=1.0" />
    <link href="/assets/plugins/bootstrap-switch/bootstrap-switch.css" rel="stylesheet">

    <script>
        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }
    </script>