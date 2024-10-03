<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);

  $added_for[] = $id;
  $sub_centers = $conn->query("SELECT Sub_Center FROM Center_SubCenter WHERE Center = '" . $_GET['id'] . "'");
  while ($sub_center = $sub_centers->fetch_assoc()) {
    $added_for[] = $sub_center['Sub_Center'];
  }

  $users = implode(",", array_filter($added_for));

  $already = array();
  $already_ids = array();
  $invoices = $conn->query("SELECT Student_ID, Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND ((Payments.Type = 1 AND Status != 2) OR (Payments.`Type` = 2 AND Payments.Status = 1))");
  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }

  $sessionQuery = "";
  if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
    $admission_session_id = intval($_GET['admission_session_id']);
    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
  }

  $stu_ids_arr = [];
  $stu_ids = "";
  $stu_id_sql = $conn->query("SELECT Student_ID FROM Invoices WHERE Invoices.User_ID IN($users)");
  while ($userdata = $stu_id_sql->fetch_assoc()) {
    $stu_ids_arr[] = $userdata['Student_ID'];
  }

  if (!empty($stu_ids_arr)) {
    $stu_ids = implode(',', $stu_ids_arr);
    $stu_query = "AND Students.ID NOT IN($stu_ids)";
  } else {
    $stu_query = "";
  }

  if ((isset($_GET['sub_center_id']) && !empty($_GET['sub_center_id']))) {
    $sub_center_id = $_GET['sub_center_id'];
    $students = $conn->query("SELECT Students.ID, First_Name, Course_ID, Sub_Course_ID, Middle_Name, Last_Name, Unique_ID, Duration, Added_For, Admission_Sessions.Name as Admission_Session, Users.Role, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Users ON Students.Added_For = Users.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $stu_query  AND Added_By IN($users) AND Step = 4 AND  Users.ID='$sub_center_id'  AND Process_By_Center IS NULL $sessionQuery ORDER BY Students.ID DESC");
  } else {
    if ($_SESSION['Role'] == "Administrator") {
      $users = $users . "," . $id;
      $students = $conn->query("SELECT Students.ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration,Added_By, Course_ID, Sub_Course_ID, Admission_Sessions.Name as Admission_Session, Added_For, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID  LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND  Added_For IN ($users) AND Step = 4  $stu_query AND Process_By_Center IS NULL $sessionQuery ORDER BY Students.ID DESC");
    } else {
      $students = $conn->query("SELECT Students.ID, First_Name, Middle_Name, Last_Name, Unique_ID, Duration, Admission_Sessions.Name as Admission_Session, Added_For, Courses.Name as courseName, Students.Created_At FROM Students LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Added_For IN ($users) AND Step = 4   AND Process_By_Center IS NULL $stu_query $sessionQuery ORDER BY Students.ID DESC");
    }
  }


  if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
      <div class="row m-b-20">
        <div class="col-md-12 d-flex justify-content-end">
          <div>
            <button type="button" class="btn btn-primary" onclick="pay('wallet')"> Pay Wallet</button>
            <!-- <?php if (isset($_SESSION['gateway'])) { ?>
              <button type="button" class="btn btn-primary" onclick="pay('Online')"> Pay Online</button>
            <?php } ?> -->
            <button type="button" class="btn btn-primary" onclick="pay('Offline')">Pay Offline</button>
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th></th>
                <th>Student ID</th>
                <th>Student Name</th>
                <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                  <th>Added By</th>
                <?php } ?>
                <th>Adm. Session</th>
                <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
                  <th>Total Fee</th>
                <?php }
                if ($_SESSION['Role'] != 'Sub-Center') { ?>
                  <th>Payable Fee</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()) {
                // if (in_array($student['ID'], $already_ids) && $student['Duration'] == $already[$student['ID']]) {
                //   continue;
                // }

                if (isset($student['Added_For'])) {
                  $roleQuery = $conn->query("SELECT Name, Code,Role FROM Users Where ID =" . $student['Added_For'] . "");
                  $roleArr = $roleQuery->fetch_assoc();
                  $code = isset($roleArr['Code']) ? $roleArr['Code'] : '';

                  if ($roleArr['Role'] == "Center" && ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Administrator")) {
                    $added_by = "Self";
                  } else if ($_SESSION['Role'] == "Administrator" && $roleArr['Role'] == "Administrator") {

                    $added_by = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                  } else {
                    $user_name = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                    $added_by = $user_name . "(" . $code . ")";
                  }
                }

                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td>
                    <div class="form-check complete" style="margin-bottom: 0px;">
                      <input type="checkbox" class="student-checkbox" id="student-<?= $student['ID'] ?>" name="student_id" value="<?= $student['ID'] ?>">
                      <label for="student-<?= $student['ID'] ?>" class="font-weight-bold"></label>
                    </div>
                  </td>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : sprintf("%'.06d\n", $student['ID']) ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <?php if ($_SESSION['Role'] != "Sub-Center") { ?>
                    <td>
                      <?= isset($added_by) ? $added_by : ''; ?>
                    </td>
                  <?php } ?>
                  <td><?= $student['Admission_Session'] ?></td>
                  <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
                    <td>
                      <?php
                      $balance = 0;
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $debit = $ledger['Type'] == 1 ? $ledger['Fee'] : 0;
                        $credit = $ledger['Type'] == 2 ? $ledger['Fee'] : 0;
                        $balance = ($balance + (int)$credit) - (int)$ledger['Fee'];
                      }
                      echo "&#8377; " . (-1) * $balance;
                      ?>
                    </td>
                  <?php }
                  if ($_SESSION['Role'] != 'Sub-Center') { ?>
                    <td>
                      <?php
                      $balance = 0;
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $debit = $ledger['Type'] == 1 ? $ledger['Fee'] : 0;
                        $credit = $ledger['Type'] == 2 ? $ledger['Fee'] : 0;
                        $balance = ($balance + (int)$credit) - (int)$ledger['Fee'];
                      }

                      $user_sub_centers = $conn->query("SELECT Center FROM Center_SubCenter WHERE Sub_Center = " . $student['Added_For'] . " ");
                      $user_sub_centers = $user_sub_centers->fetch_assoc();

                      $user_can_create_sub_centers = $conn->query("SELECT CanCreateSubCenter FROM Users WHERE ID = " . $student['Added_For'] . " AND Role = 'Center' ");
                      if ($user_can_create_sub_centers->num_rows > 0) {
                        $can_create_sub_centers = $user_can_create_sub_centers->fetch_assoc();
                      } else {
                        $user_can_create_sub_centers = $conn->query("SELECT CanCreateSubCenter FROM Users WHERE ID = " . $user_sub_centers['Center'] . " AND Role = 'Center' ");
                        $can_create_sub_centers = $user_can_create_sub_centers->fetch_assoc();
                      }


                      if ($can_create_sub_centers['CanCreateSubCenter'] == 1) {
                        $deductableAmount = 2000;
                        if (date("Y-m-d", strtotime($student['Created_At'])) >= "2024-03-30") {
                          $deductableAmount =  strpos($student['courseName'], 'adeeb-e-mahir') !== false ? 1800 : 1600;
                        }
                        echo "&#8377; " . (-1) * $balance - $deductableAmount;
                      } else {
                        echo "&#8377; " . (-1) * $balance;
                      }
                      ?>
                    </td>
                </tr>
            <?php }
                } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php if ($_SESSION['Role'] == 'Center') { ?>
      <script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/easebuzz-checkout.js"></script>
      <script type="text/javascript">
        function payOnline(ids, amount, center) {
          $.ajax({
            url: '/app/easebuzz/pay-multiple',
            type: 'post',
            data: {
              ids,
              amount
            },
            dataType: "json",
            success: function(data) {
              if (data.status == 1) {
                $('.modal').modal('hide');
                initiatePayment(data.data, center)
              } else {
                notification('danger', data.error);
              }
            }
          });
        }



        function initiatePayment(data, center) {
          var easebuzzCheckout = new EasebuzzCheckout('<?= $_SESSION['access_key'] ?>', 'prod')
          var options = {
            access_key: data,
            dataType: 'json',
            onResponse: (response) => {
              updatePayment(response, center);
              if (response.status == 'success') {
                Swal.fire({
                  title: 'Thank You!',
                  text: "Your payment is successfull!",
                  icon: 'success',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  if (result.isConfirmed) {
                    getLedger(center);
                  }
                })
              } else {
                Swal.fire(
                  'Payment Failed',
                  'Please try again!',
                  'error'
                )
              }
            },
            theme: "#272B35" // color hex
          }
          easebuzzCheckout.initiatePayment(options);
        }

        function updatePayment(response, center) {
          $.ajax({
            url: '/app/easebuzz/response',
            type: 'POST',
            data: {
              response
            },
            dataType: 'json',
            success: function(response) {
              if (response.status) {
                getLedger(center);
              } else {
                notification('danger', data.message);
              }
            },
            error: function(response) {
              console.error(response);
            }
          })
        }
      </script>
    <?php } ?>
<?php }
} ?>

<script type="text/javascript">
  function pay(by) {
    if ($('.student-checkbox').filter(':checked').length == 0) {
      notification('danger', 'Please select Student');
    } else {
      var center = '<?= $id ?>';
      var ids = [];
      $.each($("input[name='student_id']:checked"), function() {
        ids.push($(this).val());
      });

      $.ajax({
        url: '/app/centers/ledgers/payable-amount',
        type: 'POST',
        data: {
          ids,
          center,
          by
        },
        dataType: 'json',
        success: function(data) {
          if (data.status) {
            if (by == 'Online') {
              payOnline(ids, data.amount, center);
            } else if (by == 'Offline') {
              payOffline(ids, data.amount, center);
            } else if (by == 'wallet') {
              payWallet(ids, data.amount, center);
            }
          } else {
            notification('danger', data.message);
          }
        }
      })
    }
  }

  function payOffline(ids, amount, center) {
    $.ajax({
      url: '/app/offline-payments/create-multiple',
      type: 'post',
      data: {
        ids,
        amount,
        center

      },
      success: function(data) {
        $("#lg-modal-content").html(data);
        $("#lgmodal").modal('show');
      }
    });
  }

  function payWallet(ids, amount, center) {
    var by = 'wallet';
    $.ajax({
      url: '/app/wallet-payments/create-multiple',
      type: 'post',
      data: {
        ids,
        amount,
        center,
        by
      },
      success: function(data) {
        $("#lg-modal-content").html(data);
        $("#lgmodal").modal('show');
      }
    });
  }
</script>