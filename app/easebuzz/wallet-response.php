<?php
if (isset($_POST)) {
  require '../../includes/db-config.php';
  if (isset($_POST['response'])) {
    $response = is_array($_POST['response']) ? $_POST['response'] : [];
    if (empty($response)) {
      $conn->close();
      exit(json_encode(['status' => false, 'message' => 'Payment Failed!']));
    }
    if (strcasecmp($response['status'], 'success') == 0) {
      $gateway_id = $_POST['response']['easepayid'];
      $transaction_id = $_POST['response']['txnid'];
      $mode = $_POST['response']['mode'];
      $meta = mysqli_real_escape_string($conn, json_encode(["msg" => $response]));
      $update = $conn->query("UPDATE Wallets SET Gateway_ID = '$gateway_id', Payment_Mode = '$mode', Meta = '$meta', Status = 1 WHERE Transaction_ID = '$transaction_id' AND `Type` = 2");
      if ($update) {
        echo json_encode(['status' => true, 'message' => 'Payment updated!']);
      } else {
        echo json_encode(['status' => false, 'message' => 'Something went wrong!']);
      }
    }
  }
}
