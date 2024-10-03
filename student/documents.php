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
                <?php $documents = $conn->query("SELECT * FROM Student_Documents WHERE Student_ID = " . $_SESSION['ID'] . "");
                while ($document = $documents->fetch_assoc()) {
                    $images = explode("|", $document['Location']);
                    foreach ($images as $image) {
                        $id = uniqid();
                ?>
                        <div class="col-sm-3 m-b-10" onclick="viewImage('<?= $id ?>')">
                            <div class="ar-1-1">
                                <div class="widget-2 card no-margin">
                                    <div class="card-body">
                                        <img src="<?= $image ?>" alt="<?= $document['Type'] ?>" class="cursor-pointer" width="100%" height="100%" style="object-fit:fill" id="<?= $id ?>">
                                        <div class="pull-bottom bottom-left bottom-right padding-25">
                                            <span class="label font-montserrat fs-11"><?= $document['Type'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php }
                }
                ?>
            </div>
        </div>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
        <script>
            function viewImage(id) {
                var viewer = new Viewer(document.getElementById(id), {
                    inline: false,
                    toolbar: false,
                    viewed() {
                        viewer.zoomTo(0.6);
                    },
                });
                var viewer = new Viewer(document.getElementById(id), {
                    inline: false,
                    toolbar: false,
                    viewed() {
                        viewer.zoomTo(0.6);
                    },
                });
            }
        </script>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>