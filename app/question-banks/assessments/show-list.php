<!-- Modal -->
<?php
require '../../../includes/db-config.php';
if (isset($_POST['assessment_id']) && isset($_POST['ebook_id']) && isset($_POST['sub_id']) && isset($_POST['unit_id'])) {
    $id = intval($_POST['assessment_id']);
    $ebook_id = intval($_POST['ebook_id']);
    $sub_id = intval($_POST['sub_id']);
    $unit_id = intval($_POST['unit_id']);
    $e_book_Assessments = $conn->query("SELECT * FROM E_Book_Assessments WHERE E_Book_ID = $ebook_id AND Unit_ID = " . $unit_id . " AND Subject_ID = " . $sub_id . "");
    $video_id = array();
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
                        while ($row = $e_book_Assessments->fetch_assoc()) { 
                            $assessments = $conn->query("SELECT * FROM E_Book_Assessment_Questions WHERE Assessment_ID = ".$row['ID']." AND E_Book_ID = ".$row['E_Book_ID']." AND Unit_ID = $unit_id AND Syllabus_ID = $sub_id");
                        ?>
                            <tr>
                                <td><?=$i++?></td>
                                <td><?=$assessments->num_rows?></td>
                                <td><?= $row['Created_at'] ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>    
<?php } ?>
