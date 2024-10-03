<?php
require 'includes/db-config.php';
require 'includes/helpers.php';
$students=$conn->query("SELECT ID FROM `Students` WHERE `Added_By` = 1886");
while($student=$students->fetch_assoc()){
generateStudentLedger($conn,$student['ID']);
}