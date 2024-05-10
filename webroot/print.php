<?php

#Load Settings
include ('config/config.php');



$dir = $printingPath; # get path for photo storage
$frame = $framePath;


$response = ['success' => false, 'error' => ''];
if (!isset($_GET["fileName"]) or empty($_GET["fileName"])) {
    header('Content-Type: application/json; charset=utf-8');
    $response['error'] = 'No target specified';
    echo json_encode($response);
    exit;
} else {
    $fileName = htmlspecialchars($_GET["fileName"]);
}

$sourcePhotoPath = $photoPath . $fileName;

//$sourcePhotoPath = htmlspecialchars($_GET["fileName"]);


$targetWidth = $printTargetWidth;
$targetHeight = $printTargetHeight;




if (file_exists($framePath) and file_exists($sourcePhotoPath)) {

    ini_set('memory_limit', '512M');


    // Create image instances
    $dest = imagecreatefromjpeg($sourcePhotoPath);
    $src = imagecreatefrompng($frame);


    //generate needed variables
    $resizedDest = imagecreatetruecolor($targetWidth, $targetHeight); //create empty destination picture for resized photo
//source will be $dest
    $dst_x = 0; //Destination of picture will be top left corner
    $dst_y = 0; //Destination of picture will be top left corner

    $src_x = 0; //not 0 for pictures that are wider than target
    $src_y = 0; //not 0 for pictures that are taller than target

    $dst_width = $targetWidth;
    $dst_height = $targetHeight;


    $src_width = 0;
    $src_height = 0;


    //calculate aspectRatios
    $targetAspectRatio = $targetWidth / $targetHeight;
    $photoAspectRatio = imagesx($dest) / imagesy($dest);
    $scalingFactor = 0;


    if ($photoAspectRatio >= $targetAspectRatio) { //image is wider than (or equal to) the target. 
        //we want to use the full height of the image
        $src_height = imagesy($dest);
        //calculate the needed width via target aspect ratio
        $src_width = $src_height * $targetAspectRatio;

        //calculate the scaling factor needed for calculating offset to center image in frame
        $scalingFactor = $targetHeight / imagesy($dest);
        //find X offset for image
        $src_x = 0.5 * (imagesx($dest) - ($targetWidth / $scalingFactor));


    } else { //image is narrower than the target. 
        //we want to use the full width of the images
        $src_width = imagesx($dest);
        //calculate the needed height via target aspect ratio
        $src_height = $src_width / $targetAspectRatio;

        //calculate the scaling factor neededfor calculating offset to center image in frame
        $scalingFactor = $targetWidth / imagesx($dest);
        //find Y offset for image
        $src_y = 0.5 * (imagesy($dest) - ($targetHeight / $scalingFactor));
    }

    //Generate resized Image
    imagecopyresampled($resizedDest, $dest, $dst_x, $dst_y, $src_x, $src_y, $dst_width, $dst_height, $src_width, $src_height);
    //Add frame
    if ($printWithFrame) {
        imagecopyresized($resizedDest, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($src), imagesy($src));
    }

    imagejpeg($resizedDest, $dir . $fileName, 100);


    //check whether file has been created successfully
    if (file_exists($dir . $fileName)) {
        $response['path'] = $dir . $fileName;
    } else {
        $response['success'] = false;
        $response['error'] = 'Prepared Photo could not be created in the filesystem';
    }


} else {
    //files dont exist
    if (!file_exists($framePath)) {
        $response['success'] = false;
        $response['error'] = 'Selected frame does not exist';
        $response['selectedPhoto'] = $sourcePhotoPath;
        $response['selectedFrame'] = $framePath;
    }
    if (!file_exists($sourcePhotoPath)) {
        $response['success'] = false;
        $response['error'] = 'Selected photo does not exist';
        $response['selectedPhoto'] = $sourcePhotoPath;
        $response['selectedFrame'] = $framePath;
    }
}


// print image

if (file_exists($dir . $fileName)) {
    chdir($dir);
    $cmd = sprintf($printCommand, $fileName);
    $cmd .= ' 2>&1';
    exec($cmd, $output, $returnValue);
    $response['printingResponse'] = $output;
    $response['printingReturnValue'] = $returnValue;
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = 'Photo could not be found for print command';
}








header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
//free Memory
imagedestroy($resizedDest);
imagedestroy($dest);
imagedestroy($src);







?>