<?php
  if(isset($_POST['center'])){
    require '../../includes/db-config.php';
    session_start();

    $center = intval($_POST['center']);

    if(empty($center)){
      echo 'Center';
      exit();
    }
    $vertical = $conn->query("SELECT Vertical FROM Users WHERE ID = $center");
    $vertical = mysqli_fetch_assoc($vertical);
    echo $vertical['Vertical'];
    
  }
