<?php
#Load Settings
include('config/config.php');





#Construct System Call
$fileName = date('mdY_His', time()) . "." .$fileExtension; 
$command = sprintf($takePhotoCommand, $fileName);

if (!file_exists($photoPath)) {
    mkdir($photoPath, 0777, true);
}
#Take Photo#####################
$output=null;
$retval=null;
    if (chdir(realpath($photoPath))){
       exec($command , $output, $retval); #execute the "take Photo" command
       $data = ['returnValue'=> $retval,'output' => $output, 'path' => $photoPath, 'fileName' => $fileName ];  #construct the response

        if ($retval!=0){ #something went wrong!a
            $data = ['returnValue'=> $retval, 'output' => $output];  #construct the response
        } 
    }else {
        $data = ['returnValue'=> '-1', 'output' => 'could not chdir to desired path'];  #construct the response
    }

    

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

#echo "Returned with status $retval and output:\n";
#print_r($output);

?>