<!-- Modal -->

<?php
require '../../../../includes/db-config.php';

if (isset($_POST['assessment_id']) && isset($_POST['ebook_id']) && isset($_POST['sub_id']) && isset($_POST['unit_id'])) {
    $id = intval($_POST['assessment_id']);
    $ebook_id = intval($_POST['ebook_id']);
    $sub_id = intval($_POST['sub_id']);
    $unit_id = intval($_POST['unit_id']);
    $video_assessments = $conn->query("SELECT * FROM E_Book_Assessments WHERE E_Book_ID = $ebook_id AND Unit_ID = " . $unit_id . " AND Subject_ID = " . $sub_id . "");
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
                                <th>Action</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $i = 1;
                                while ($row = $video_assessments->fetch_assoc()) { ?>
                                <tr>
                                    <td><?=$i++?></td>
                                    <td><button class="btn btn-secondary cursor-pointer" onclick="startAssessment(<?=$row['ID']?>,<?=$row['E_Book_ID']?>,<?=$row['Unit_ID']?>,<?=$row['Subject_ID']?>);">Start</button></td>
                                    <td><button class="btn btn-secondary cursor-pointer m-2" >Created On <?=$row['Created_at'] ?></button>
                                        <button class="btn btn-secondary cursor-pointer" >Closed On <?=$row['Created_at'] ?></button>
                                    </td>
                                    <td><button class="btn btn-secondary cursor-pointer" >Not Attempt</button></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>