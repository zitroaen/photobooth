<?php
#Load Settings
include ('config/config.php');
include ('photoManager.php'); #Get Photo Manager Functions





#Construct System Call
$fileName = date('Ymd_His', time()) . "." . $fileExtension;
$command = sprintf($takePhotoCommand, $fileName);

if (!file_exists($photoPath)) {
    mkdir($photoPath, 0777, true);
}
#Take Photo#####################
$output = null;
$retval = null;
if (chdir(realpath($photoPath))) {
    exec($command, $output, $retval); #execute the "take Photo" command
    $data = ['returnValue' => $retval, 'output' => $output, 'path' => $photoPath, 'fileName' => $fileName, 'command' => $command];  #construct the response

    if ($retval != 0) { #something went wrong!a
        $data = ['returnValue' => $retval, 'output' => $output];  #construct the response
    }
} else {
    $data = ['returnValue' => '-1', 'output' => 'could not chdir to desired path'];  #construct the response
}



header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

if ($retval == 0) {
    chdir($_SERVER["DOCUMENT_ROOT"]);
    $db = $GLOBALS['db'];
    if(addPhotoEntry(realpath($photoPath . $fileName))){#update the photo database
        $photoId = $db->lastInsertRowID(); #Get the ID of the picture that was just inserted
    ###Now trigger all Jobs to generate stuff
    generateThumbnail($photoId, $thumbnailWidth, $thumbnailPath);
    } else {
        error_log('Could not add photo to database');
    }
    

}




#echo "Returned with status $retval and output:\n";
#print_r($output);

?>