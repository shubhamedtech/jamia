<?php
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $header[] = array('Unique ID','Student Name',  'Course', 'Subject Name', 'Subject Code', 'Obtained mark Internal','Obtained mark Externel', 'Obtained mark total', 'Status', 'Remarks');
  $header[] = array('JUA68690','ANILKUMAR P T',  'adeeb-e-mahir (Science)', 'Mathematics', 'ABC-11', 40,40, 80, 1, 'Pass');
  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('Exam result Sample.xlsx');