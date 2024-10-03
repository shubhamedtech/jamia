<?php

use setasign\Fpdi\Fpdi;

require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

if (isset($_GET['id'])) {
    require '../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $pdf = new Fpdi();

    $pdf->SetTitle('Export Documents for ' . $id);
    $file_extensions = array('.png', '.jpg', '.jpeg');
    $documents = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type NOT IN ('Photo', 'Student Signature', 'Parent Signature')");
    while ($document = $documents->fetch_assoc()) {
        $files = explode("|", $document['Location']);
        foreach ($files as $file) {
            $pdf->AddPage();
            $pdf->SetMargins(10, 10, 10);
            // Width & Height
            list($file_width, $file_height) = getimagesize("../.." . $file);

            $encoded_file = base64_encode(file_get_contents("../.." . $file));

            // Recreate
            $i = 0;
            $end = 3;
            $new_file = $id . uniqid();
            while ($i < $end) {
                $decoded_file = base64_decode($encoded_file);
                $file_with_extension[] = $new_file . $file_extensions[$i];
                file_put_contents($new_file . $file_extensions[$i], $decoded_file);
                $i++;
            }

            $width = ($file_width / 2.02) > 190 ? 190 : $file_width / 2.02;
            $height = ($file_height / 2.02) > 270 ? 270 : $file_height / 2.02;

            try {
                $filename = $new_file . $file_extensions[0];
                $pdf->Image($filename, 10, 10, $width, $height);
            } catch (Exception $e) {
                try {
                    $filename = $new_file . $file_extensions[1];
                    $pdf->Image($filename, 10, 10, $width, $height);
                } catch (Exception $e) {
                    try {
                        $filename = $new_file . $file_extensions[2];
                        $pdf->Image($filename, 10, 10, $width, $height);
                    } catch (Exception $e) {
                    }
                }
            }

            foreach ($file_with_extension as $file_ext) {
                if (file_exists($file_ext)) {
                    unlink($file_ext);
                }
            }
            $file_with_extension = array();
        }
    }

    $pdf->Output('I', $id . '_Documents.pdf');
}
