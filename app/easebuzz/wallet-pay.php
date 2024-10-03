
<?php
if (isset($_POST['online_amount'])) {
  session_start();
  require '../../includes/db-config.php';
  include '../../includes/helpers.php';

  $amount = sprintf("%.1f", $_POST['online_amount']);
  $txnId = strtoupper(strtolower(uniqid()));
  $key = $_SESSION['access_key'];
  $salt = $_SESSION['secret_key'];
  $productInfo = 'Wallet Payment';

  $value = $key . '|' . $txnId . '|' . $amount . '|' . $productInfo . '|' . trim($_SESSION['Name']) . '|' . trim($_SESSION['Email']) . '|||||||||||' . $salt;
  $hash = hash('sha512', $value);

  $conn->query("INSERT INTO Wallets (`Type`, `Amount`, `Transaction_ID`, `Added_By`, `University_ID`) VALUES (2, '$amount', '$txnId', '" . $_SESSION['ID'] . "', '" . $_SESSION['university_id'] . "')");

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://pay.easebuzz.in/payment/initiateLink',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
      'key' => $key,
      'txnid' => $txnId,
      'amount' => $amount,
      'productinfo' => $productInfo,
      'firstname' => trim($_SESSION['Name']),
      'phone' => $_SESSION['Mobile'],
      'email' => trim($_SESSION['Email']),
      'surl' => 'https://board.juaonline.in/accounts/wallet-payments',
      'furl' => 'https://board.juaonline.in/accounts/wallet-payments',
      'hash' => $hash
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
}
