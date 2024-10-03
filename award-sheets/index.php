<?php
require '../includes/db-config.php';

$path = uniqid();
if(!file_exists($path)){
    mkdir($path, 0777, true);
}

$centers = $conn->query("SELECT Center_Code FROM Award_Sheet_Records GROUP BY Center_Code");
while($center = $centers->fetch_assoc()){
    $centerPath = $path.'/'.$center['Center_Code'];
    if(!file_exists($centerPath)){
        mkdir($centerPath, 0777, true);
    }
    
    // Courses
    $courses = $conn->query("SELECT Course FROM Award_Sheet_Records WHERE Center_Code = '".$center['Center_Code']."' GROUP BY Course");
    while($course = $courses->fetch_assoc()){
        $coursePath = $centerPath.'/'.$course['Course'];
        if(!file_exists($coursePath)){
           mkdir($coursePath, 0777, true);
        }
        
        $subjects = $conn->query("SELECT Subject FROM Award_Sheet_Records WHERE Center_Code = '".$center['Center_Code']."' AND Course = '".$course['Course']."' GROUP BY Subject");
        while($subject = $subjects->fetch_assoc()){
          	$fileName = $subject['Subject'].'.pdf';
          
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://board.juaonline.in/award-sheets/file.php',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => array('path'=>$coursePath, 'fileName'=>$fileName, 'center'=>$center['Center_Code'], 'course'=>$course['Course'], 'subject'=>$subject['Subject']),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
        }
    }
}