<?php
#Load Settings
include ('config/config.php');
include ('photoManager.php'); #Get Photo Manager Functions


$dir = $photoPath; # get path for photo storage
$extension = $fileExtension; #also get extension from settings



$ImagesArray = [];
$success = 0;
foreach (getAllPhotos() as $file) {
    ## $ImagesArray[] = ['filepath' => $dir . $file, 'fileName' => $file];

    $ImagesArray[] = [
        'id' => $file['id'],
        'filepath' => $file['originalRelativePath'],
        'fileName' => $file['fileName'], 
        'thumbnailAvailable' => $file['thumbnailGenerated'],
        'thumbnailPath' => $file['thumbnailRelativePath'],
        'width' => $file['width'],
        'height'=> $file['height']

    ];
    //  echo $file['originalPath'];

}
$success = 1;
$data = ['data' => $ImagesArray, 'success' => $success];



header("Content-Type: application/json");
echo json_encode($data);


?>