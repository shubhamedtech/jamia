<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../../includes/db-config.php';
if (isset($_POST['lectureType'])) {
    $lectureType = $_POST['lectureType'];
    // Prepare the SQL query with placeholders
    $query = "SELECT * FROM video_lectures 
              WHERE status = 1 
              AND (
                (video_type = 'url' AND (Videos_Categories = ? OR Videos_Categories = ?)) 
                OR 
                (video_type = 'mp4' AND (Videos_Categories = ? OR Videos_Categories = ?))
              )";
    $stmt = $conn->prepare($query);
    // Bind the lectureType parameter to both placeholders
    $stmt->bind_param("iiii", $lectureType, $lectureType, $lectureType, $lectureType);

    $stmt->execute();
    $result = $stmt->get_result();

    $videoData = [];
    while ($row = $result->fetch_assoc()) {
        $videoData[] = $row;
    }

    $stmt->close();
    $conn->close();
?>
    <?php foreach ($videoData as $key => $value) { ?>
        <span id="videoItem<?= $value['id'] ?>" class="active_<?= $value['id'] ?> list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <input type="checkbox" id="video<?= $key ?>" name="video" onclick="uncheckOthers('video<?= $key ?>');">
                <h5 class="mb-0 cursor-pointer" onclick="getDataFunc('<?= $value['id'] ?>', '<?= $value['video_url'] ?>');"><?= ucwords($value['unit']) ?>:</h5>
                <small><i class="ti-timer mr-1"></i>45min</small>
                <i class="fa fa-play-circle fa-lg"></i>
                <div id="video_box"></div>
            </div>
        </span>
    <?php } ?>
<?php
}
?>