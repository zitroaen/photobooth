<?php
#Load Settings
include ('config/config.php');



$dir = $photoPath; # get path for photo storage
$extension = $fileExtension; #also get extension from settings


$ImagesA = Get_ImagesToFolder($dir, $fileExtension);
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ImagesA);


function Get_ImagesToFolder($dir , $extension)
{
    $ImagesArray = [];
    $success = 0;
    $file_display = [$extension];

    if (file_exists($dir) == false) {
        $success = 0;
    } else {
        $dir_contents = scandir($dir);
        foreach ($dir_contents as $file) {
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($file_type, $file_display) == true) {
                $ImagesArray[] = ['filepath' => $dir . $file, 'fileName' => $file];
            }

        }

        $success = 1;
    }
    $data = ['data' => $ImagesArray, 'success' => $success];
    return $data;
}




?>