<!-- Modal -->
<div class="modal-header clearfix text-left">
    <?php
    require '../../../includes/db-config.php';
    if (isset($_POST['id']) && isset($_POST['sub_id']) && isset($_POST['unit_id'])) {
        $id = intval($_POST['id']);
        $sub_id = intval($_POST['sub_id']);
        $unit_id = intval($_POST['unit_id']);
        $videos = $conn->query("SELECT * FROM e_books WHERE Unit_ID = " . $unit_id . " AND Subject_ID = " . $sub_id . "");
    ?>
        <h5>Ebooks in Unit <?= $unit_id ?> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="row">
        <?php while ($video = $videos->fetch_assoc()) { ?>
            <div class="col-md-4">
                <a href="/<?= $video['Path'] ?>/<?= $video['File_Name'] ?>" class="text-black">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fa-file-pdf-o fa f-70"></i>
                        </div>
                        <div class="card-footer">
                            <p><?= $video['File_Name'] ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
<?php } ?>
</div>