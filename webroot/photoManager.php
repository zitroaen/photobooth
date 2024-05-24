<?php
#Load Settings
include ($_SERVER['DOCUMENT_ROOT'] . '/config/config.php');

$initialSetup = false;
if (file_exists($dbPath . $dbFilename) == false) {
    $initialSetup = true;
}

$db = new SQLite3($dbPath . $dbFilename);

if ($initialSetup == true) {
    initDb();
    addAllPictures($photoPath, $fileExtension);
}




#addAllPictures($photoPath, $fileExtension);
#echo $db->exec("INSERT INTO photos(originalPath, originalRelativePath, fileName) VALUES ('Test','Test','Test')");
#echo addPhotoEntry("../resources/photos/original/pic.jpg");
#echo setThumbnail(33, 'C:\Users\Elle\Documents\GitHub\photobooth\webroot\resources\photos\print\pic.jpg');
#$info = getPhotoInfo(33);
#print_r(getPhotoInfo(33)['printCounter']);


#$info = getAllPhotos();
#header("Content-Type: application/json");
#echo json_encode(getAllPhotos());


#print_r(var_dump($info));



######################
#FUNCTION DEFINITIONS#
######################
function initDb()
{
    $db = $GLOBALS['db'];
    $db->exec("CREATE TABLE IF NOT EXISTS photos(
        id INTEGER PRIMARY KEY AUTOINCREMENT, 
        originalPath TEXT UNIQUE,
        originalRelativePath TEXT NOT NULL,
        fileName TEXT NOT NULL,
        width INTEGER NOT NULL,
        height INTEGER NOT NULL, 
        printFileGenerated INTEGER NOT NULL DEFAULT '0',
        printFilePath TEXT,
        printCounter INTEGER NOT NULL DEFAULT '0',
        copiedToUsb INTEGER NOT NULL DEFAULT '0',
        usbPath TEXT,
        uploadedToWeb INTEGER NOT NULL DEFAULT '0',
        webUrl TEXT,
        thumbnailGenerated INTEGER NOT NULL DEFAULT '0',
        thumbnailRelativePath TEXT,
        thumbnailAbsolutePath TEXT)");
    if ($db->lastErrorCode() != 0) { #Log Errors and return false if something went wrong
        error_log("SQLite Error Code: " . $db->lastErrorCode() . " Error Info: " . $db->lastErrorMsg());
        return false;
    }
    return true;

}




function addAllPictures($dir, $extension)
{
    $db = $GLOBALS['db'];
    $file_display = [$extension];
    if (file_exists($dir) == false) { #check if the directory exists
    } else {
        $dir_contents = scandir($dir);  #scan the directory into an array
        foreach ($dir_contents as $file) {  #sort the array
            $files[$file] = filemtime($dir . '/' . $file);

        }
        asort($files);
        $sortedFiles = array_keys($files);

        foreach ($sortedFiles as $file) { #Process each file
            $file_type = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($file_type, $file_display) == true) {
                if (addPhotoEntry($dir . $file) == false) {
                    return false;
                }

            }

        }
    }
    return true;
}



function addPhotoEntry($photoEntry)
{  #function adds an entry to the photoDatabase
    $db = $GLOBALS['db'];
    if (file_exists($photoEntry)) {
        $photoFileName = basename("$photoEntry");
        $photoAbsolutePath = realpath("$photoEntry");
        $photoWebPath = getRelativePath($photoAbsolutePath);
    } else {
        return false;
    }

    $imageSize = getimagesize($photoAbsolutePath);
    if ($imageSize === false) {
        error_log("Could not determine Size of the following image: " . $photoAbsolutePath);
        return false;
    }

    $db->exec("INSERT INTO photos (
        originalPath,
        originalRelativePath,
        fileName,
        width,
        height
        ) VALUES (
            '$photoAbsolutePath',
            '$photoWebPath',
            '$photoFileName',
            '$imageSize[0]',
            '$imageSize[1]'
        )
    ");

    if ($db->lastErrorCode() != 0 && $db->lastErrorCode() != 19) { #Log Errors and return false if something went wrong. ErrorCode 19 means that the file already exists in DB
        error_log("SQLite Error Code: " . $db->lastErrorCode() . " Error Info: " . $db->lastErrorMsg());
        return false;
    }
    return true;
}


function idExists($id)
{
    $db = $GLOBALS['db'];
    if (is_numeric($id) && $id >= 0) {
        if ($db->querySingle("SELECT * FROM photos WHERE id=$id", true) === false) {
            return false;
        } else {
            return true;
        }
    }
}


function setPrintFile($id, $printFilePath)
{
    $db = $GLOBALS['db'];
    if (file_exists(realpath($printFilePath))) {
        $realPrintFilePath = realpath($printFilePath);
        $db->exec("UPDATE photos SET printFileGenerated = 1 WHERE id = $id");
        $db->exec("UPDATE photos SET printFilePath = '$realPrintFilePath' WHERE id = $id");
        if ($db->lastErrorCode() != 0) { #Log Errors and return false if something went wrong. 
            error_log("SQLite Error Code: " . $db->lastErrorCode() . " Error Info: " . $db->lastErrorMsg());
            return false;
        }
    } else {
        error_log("Could not set print file: File not found " . $printFilePath);
    }
    return true;
}


function increasePrintCounter($id)
{
    $db = $GLOBALS['db'];
    $db->exec("UPDATE photos SET printCounter = printCounter + 1 WHERE id = $id");
    if ($db->lastErrorCode() != 0) { #Log Errors and return false if something went wrong. 
        error_log("SQLite Error Code: " . $db->lastErrorCode() . " Error Info: " . $db->lastErrorMsg());
        return false;
    }
    return true;
}

function setThumbnail($id, $thumbnailPath)
{
    $db = $GLOBALS['db'];
    if (file_exists(realpath($thumbnailPath))) {
        $thumbnaiAbsolutelPath = realpath($thumbnailPath);
        $thumnailRelativePath = getRelativePath($thumbnaiAbsolutelPath);
        $db->exec("UPDATE photos SET thumbnailGenerated = 1 WHERE id = $id");
        $db->exec("UPDATE photos SET thumbnailAbsolutePath = '$thumbnaiAbsolutelPath' WHERE id = $id");
        $db->exec("UPDATE photos SET thumbnailRelativePath = '$thumnailRelativePath' WHERE id = $id");
        if ($db->lastErrorCode() != 0) { #Log Errors and return false if something went wrong.
            error_log("SQLite Error Code: " . $db->lastErrorCode() . " Error Info: " . $db->lastErrorMsg());
            return false;
        }
    } else {
        error_log("Could not set print file: File not found " . $thumbnailPath);
    }
    return true;
}


function getPhotoInfo($id)
{
    $db = $GLOBALS['db'];
    if (is_numeric($id) && $id >= 0) {
        return $db->querySingle("SELECT * FROM photos WHERE id=$id", true);
    } else {
        error_log("Given photo ID is not numeric: " . $id);
        return false;

    }
}

function getAllPhotos()
{
    $db = $GLOBALS['db'];
    $result = $db->query('SELECT * FROM photos');
    $data = array();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        array_push($data, $row);
    }

    return $data;



    // return $db->query("SELECT * FROM photos");

}


function getRelativePath($path)
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT']) . DIRECTORY_SEPARATOR; //Absolute path to the root of the Server with Directory Separator in the end to ensure that the relative path does not start with a slash
    $path = realpath($path); //Ensure that input is also an absolute path
    if (insideDocumentRoot($path)) {
        $relativePath = str_replace($documentRoot, '', $path); //Crop the document Root off the relative Path
        return $relativePath;
    } else {
        error_log('The path given is outside of document root. This cannot be resolved into a relative path Path: ' . realpath($path) . ' Root: ' . strpos(realpath($_SERVER['DOCUMENT_ROOT']), realpath($path)));
        return false;
    }
}


function insideDocumentRoot($path)
{  //Returns true, if a given absolute path is within Document root
    if (strpos(realpath($path), realpath($_SERVER['DOCUMENT_ROOT'])) === false) { #if the document root cannot be found within the given path...
        return false;
    } else {
        return true;
    }
}



function generateThumbnail($id, $width, $thumbnailBasePath)
{ #generates the Thumbnail for a given photo ID
    $originalPath = realpath(getPhotoInfo($id)['originalPath']);
    $fileName = getPhotoInfo($id)['fileName'];
    $outputPath = realpath($thumbnailBasePath);
    $outputFilePath = $outputPath . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($originalPath)) { #Check if the original file exists
        $img = imagecreatefromjpeg($originalPath); #Load Image
        $img = imagescale($img, $width, -1, IMG_BILINEAR_FIXED);
        if ($img === false) {
            error_log('Could not scale given image');
            return false;
        }
        imagejpeg($img, $outputFilePath, 80); #Try to write to disk
        imagedestroy($img); #Free up memory
        if (file_exists($outputFilePath)) {#check if thumbnail was created
            setThumbnail($id, $outputFilePath);

        } else {
            error_log('Could not save thumbnail to ' . $outputFilePath);
            return false;
        }

    } else {
        error_log('Could not create thumbnail. The following file does not exist: ');
        return false;
    }
    return true;
}



?>