<!-- Modal -->
<style>
    div#md-modal-content {
        width: 700px;
    }

    tr th {
        text-wrap: nowrap !important;
    }

    tr td {
        text-wrap: nowrap !important;
    }

    .modal-header {
        border-bottom: unset;
    }
</style>
<div class="modal-header clearfix text-left" style="display: block;">
    <div class="row">
        <div class="col-md-4">
            <h5>All <span class="semi-bold">Students</span></h5>
        </div>
        <div class="col-md-6">
            <p id="xportxlsx" style="text-align: right;" class="xport"><input type="submit" value="Export to XLSX!" onclick="doit('xlsx');"></p>
        </div>
        <div class="col-md-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    </div>
</div>
<form role="form" id="form-add-department" action="/app/departments/store" method="POST" enctype="multipart/form-data">
    <div class="modal-body">

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap" id="departments-table">
                        <thead>
                            <tr>
                                <th>Student-Name</th>
                                <th>Fee</th>
                                <th>Transaction-ID</th>
                                <th>Payment By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include '../../includes/db-config.php';
                            session_start();
                            $ids = $_GET['ids'];
                            $type = isset($_GET['type']) ? $_GET['type'] : '';
                            if (isset($type) && $type == "3") {
                                $students = $conn->query("SELECT Students.ID, CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID, Wallet_Invoices.Amount as amounts, Wallet_Invoices.Invoice_No as transaction_id, Users.Name as center_name, Students.Course_ID, Students.Sub_Course_ID, Wallet_Invoices.User_ID  FROM Wallet_Invoices LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID LEFT JOIN Users ON Users.ID = Wallet_Invoices.User_ID WHERE Students.ID IN ($ids) AND Students.University_ID = " . $_SESSION['university_id'] . " GROUP BY Students.ID");
                            } else {
                                $students = $conn->query("SELECT Students.ID, CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID, Invoices.Amount as amounts, Invoices.Invoice_No as transaction_id, Users.Name as center_name, Students.Course_ID, Students.Sub_Course_ID, Invoices.User_ID FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID LEFT JOIN Users ON Users.ID = Invoices.User_ID WHERE Students.ID IN ($ids) AND Students.University_ID = " . $_SESSION['university_id'] . " GROUP BY Students.ID");
                            }

                            $newStr = explode(",", $ids);
                            $studentsCount = count($newStr);
                            if ($students->num_rows > 0) {
                                while ($student = mysqli_fetch_assoc($students)) {
                            ?>
                                    <tr>
                                        <td><?= $student['Student_Name'] ?></td>
                                        <?php
                                        $userTypeId = $student['User_ID'];
                                        $centerArr = array();
                                        $roleQuery = $conn->query("SELECT Role FROM `Users` WHERE `ID` = $userTypeId");
                                        $roleArr = $roleQuery->fetch_assoc();
                                        if ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Administrator") {
                                            $center_fee_Query = $conn->query("SELECT wp.amount AS Fee FROM `Wallet_Invoices` AS wi LEFT JOIN Wallet_Payments  AS wp on  wi.Invoice_No = wp.Transaction_ID  WHERE  `User_ID` = $userTypeId  AND wi.Student_ID = '" . $student['ID'] . "'  AND wi.University_ID=" . $_SESSION['university_id'] . "");
                                            $centerArr = $center_fee_Query->fetch_assoc(); ?>
                                            <td><?= "&#8377; " . number_format($centerArr['Fee']/$studentsCount , 2); ?></td>
                                        <?php } else { ?>
                                            <td><?= "&#8377; " . number_format($student['amounts']/$studentsCount , 2) ?></td>
                                        <?php } ?>
                                        <td><?= $student['transaction_id'] ?></td>
                                        <td><?= $student['center_name'] ?></td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td>No data found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript" src="//unpkg.com/xlsx/dist/shim.min.js"></script>
<script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<script type="text/javascript" src="//unpkg.com/blob.js@1.0.1/Blob.js"></script>
<script type="text/javascript" src="//unpkg.com/file-saver@1.3.3/FileSaver.js"></script>

<script type="text/javascript">
    function doit(type, fn, dl) {
        var elt = document.getElementById('departments-table');
        var wb = XLSX.utils.table_to_book(elt, {
            sheet: "Sheet JS"
        });
        return dl ?
            XLSX.write(wb, {
                bookType: type,
                bookSST: true,
                type: 'base64'
            }) :
            XLSX.writeFile(wb, fn || ('test.' + (type || 'xlsx')));
    }


    function tableau(pid, iid, fmt, ofile) {
        if (typeof Downloadify !== 'undefined') Downloadify.create(pid, {
            swf: 'downloadify.swf',
            downloadImage: 'download.png',
            width: 100,
            height: 30,
            filename: ofile,
            data: function() {
                return doit(fmt, ofile, true);
            },
            transparent: false,
            append: false,
            dataType: 'base64',
            onComplete: function() {
                alert('Your File Has Been Saved!');
            },
            onCancel: function() {
                alert('You have cancelled the saving of this file.');
            },
            onError: function() {
                alert('You must put something in the File Contents or there will be nothing to save!');
            }
        });
    }
    tableau('xlsxbtn', 'xportxlsx', 'xlsx', 'test.xlsx');
</script>