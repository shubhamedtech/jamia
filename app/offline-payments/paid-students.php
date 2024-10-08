<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>All <span class="semi-bold">Students</span> <p id="xportxlsx" class="xport"><input type="submit" value="Export to XLSX!" onclick="doit('xlsx');"></p></h5>
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
                        <th>Center-Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        include '../../includes/db-config.php';
                        session_start();
                        $ids = $_GET['ids'];
                        $newStr = explode(",", $ids);
                        $students = $conn->query("SELECT CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name)), ' (', IF(Students.Unique_ID='' OR Students.Unique_ID IS NULL, RIGHT(CONCAT('000000', Students.ID), 6), Students.Unique_ID), ')') as Student_Name , Students.ID as std_ID, Invoices.Amount as amounts, Invoices.Invoice_No as transaction_id, Users.Code as center_name FROM Invoices LEFT JOIN Students ON Invoices.Student_ID = Students.ID LEFT JOIN Users ON Users.ID = Invoices.User_ID WHERE Students.ID IN ($ids) AND Students.University_ID = ".$_SESSION['university_id']." GROUP BY Students.ID");    
                    if($students->num_rows > 0){
                    while ($student = mysqli_fetch_assoc($students)) { ?>
                    <tr>
                        <td><?=$student['Student_Name']?></td>
                        <td><?=$student['amounts']?></td>
                        <td><?=$student['transaction_id']?></td>
                        <td><?=$student['center_name']?></td>
                    </tr>
                    <?php }}else{ ?>
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
    var wb = XLSX.utils.table_to_book(elt, {sheet:"Sheet JS"});
    return dl ?
        XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
        XLSX.writeFile(wb, fn || ('test.' + (type || 'xlsx')));
}


function tableau(pid, iid, fmt, ofile) {
    if(typeof Downloadify !== 'undefined') Downloadify.create(pid,{
            swf: 'downloadify.swf',
            downloadImage: 'download.png',
            width: 100,
            height: 30,
            filename: ofile, data: function() { return doit(fmt, ofile, true); },
            transparent: false,
            append: false,
            dataType: 'base64',
            onComplete: function(){ alert('Your File Has Been Saved!'); },
            onCancel: function(){ alert('You have cancelled the saving of this file.'); },
            onError: function(){ alert('You must put something in the File Contents or there will be nothing to save!'); }
    });
}
tableau('xlsxbtn',  'xportxlsx',  'xlsx',  'test.xlsx');

</script>