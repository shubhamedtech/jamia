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
            </div>
        </div>

        <div class="content">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header seperator d-flex justify-content-between">
                            <h5 class="fw-bold mb-0">Latest Notification</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $current_notification_id = 0;
                            $latest_notification = $conn->query("SELECT * FROM Notifications_Generated WHERE Send_To = 'center'  OR Send_To = '" . 'all' . "' ORDER BY Notifications_Generated.ID DESC LIMIT 1");
                            if ($latest_notification && $latest_notification->num_rows > 0) {
                                while ($row = $latest_notification->fetch_assoc()) {
                                    $current_notification_id = $row['ID'];
                            ?>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span><span class="fw-bold">Regarding : </span><?= $row['Heading'] ?></span>
                                        <span class="me-auto"><span class="fw-bold">Date : </span><?= $row['Noticefication_Created_on'] ?></span>
                                    </div>
                                    <p><span class="fw-bold">Message: </span><?= $row['Content'] ?></p>
                                    <?php if (!empty($row['Attachment'])) { ?>
                                        <a href="<?= $row['Attachment'] ?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                                    <?php } ?>
                                <?php }
                            } else { ?>
                                <h4 class="">No Notifications</h4>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card custom-card info-box">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title mb-1">Notifications</h6>
                            </div>
                            <div class="table-responsive" style="max-height: 300px;">
                                <table class="table table-bordered text-nowrap mb-0 overflow-auto">
                                    <thead>
                                        <tr>
                                            <th>Regarding</th>
                                            <th>Content</th>
                                            <th>Sent To</th>
                                            <th>Date</th>
                                            <th>Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $result_record = $conn->query("SELECT * FROM Notifications_Generated WHERE Send_To = 'center' OR Send_To = 'all' ORDER BY Noticefication_Created_on DESC");
                                        if ($result_record && $result_record->num_rows > 0) {
                                            while ($row = $result_record->fetch_assoc()) { ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['Heading']) ?></td>
                                                    <td><button type="button" class="btn btn-link py-0" onclick="view_content('<?= htmlspecialchars($row['ID']) ?>');"><i class="fa fa-eye"></i></button></td>
                                                    <td><?= htmlspecialchars($row['Send_To']) ?></td>
                                                    <td><?= date('d-m-Y', strtotime($row['Noticefication_Created_on'])) ?></td>
                                                    <td>
                                                        <?php if (!empty($row['Attachment'])) { ?>
                                                            <a href="<?= htmlspecialchars($row['Attachment']) ?>" target="_blank" download="<?= htmlspecialchars($row['Heading']) ?>">Download</a>
                                                        <?php } else { ?>
                                                            <p>No Attachment</p>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5">
                                                    <h5>No Notifications</h5>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            if (<?= $current_notification_id ?> != 0) {
                $.ajax({
                    url: '/app/notifications/center-read-notification?id=' + <?= $current_notification_id ?>,
                    type: 'GET',
                    success: function(data) {}
                })
            };
        });

        function view_content(id) {
            $.ajax({
                url: '/app/notifications/contents?id=' + id,
                type: 'GET',
                success: function(data) {
                    $("#md-modal-content").html(data);
                    $("#mdmodal").modal('show');
                }
            })
        }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>