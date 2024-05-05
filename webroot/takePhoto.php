<?php
#Load Settings
include('config/config.php');





#Construct System Call
$fileName = date('mdY_His', time()); 
$command = sprintf($takePhotoCommand, $fileName);


#Take Photo#####################
$output=null;
$retval=null;
    chdir($photoPath);

    exec($command , $output, $retval); #execute the "take Photo" command
    $data = ['returnValue'=> $retval,'output' => $output, 'path' => getcwd(), 'fileName' => $fileName ];  #construct the response


if ($retval!=0){ #something went wrong!a
    $data = ['returnValue'=> $retval, 'output' => $output];  #construct the response
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);

#echo "Returned with status $retval and output:\n";
#print_r($output);

?>