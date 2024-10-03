<!-- Modal -->

<?php
require '../../../includes/db-config.php';
if (isset($_POST['id']) && isset($_POST['video_id']) && isset($_POST['sub_id']) && isset($_POST['unit_id'])) {
    $id = intval($_POST['id']);
    $videos_id = intval($_POST['video_id']);
    $sub_id = intval($_POST['sub_id']);
    $unit_id = intval($_POST['unit_id']);
    $video_assessments = $conn->query("SELECT * FROM Video_Assessments WHERE Video_ID = $videos_id AND Unit_ID = " . $unit_id . " AND Subject_ID = " . $sub_id . "");
    $assessments = $conn->query("SELECT * FROM Video_Assessment_Questions WHERE Assessment_ID = $id AND Unit_ID = $unit_id AND Video_id = $videos_id AND Syllabus_ID = $sub_id");
?>
    <div class="modal-header">
        <h5>uploaded Assessments</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>Assesssment No.</th>
                                <th>Number of Questions</th>
                                <th>Create AT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i = 1;
                                while ($row = $video_assessments->fetch_assoc()) { ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><?=$assessments->num_rows?></td>
                                    <td><?=$row['Created_at'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>