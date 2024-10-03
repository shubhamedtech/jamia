    <?php
    require '../../../includes/db-config.php';
    if (isset($_POST['id']) && isset($_POST['sub_id']) && isset($_POST['unit_id'])) {
        $id = intval($_POST['id']);
        $sub_id = intval($_POST['sub_id']);
        $unit_id = intval($_POST['unit_id']);
        $videos = $conn->query("SELECT * FROM Videos WHERE Unit_ID = " . $unit_id . " AND Subject_ID = " . $sub_id . "");
    ?>
        <div class="modal-header clearfix text-left">
            <h5>Videos in Unit <?= $unit_id ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

            <div class="row">
                <?php while ($video = $videos->fetch_assoc()) { ?>
                    <div class="col-md-6">
                        <video class="video-js" id="vid-<?= $video['ID'] ?>">
                            <source src="/<?= $video['Path'] ?>/<?= $video['File_Name'] ?>" alt="<?= $video['File_Name'] ?>">
                        </video>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    </div>