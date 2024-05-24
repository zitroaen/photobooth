<?php
# No toucy!#############
$prevCwd = getcwd();
chdir($_SERVER["DOCUMENT_ROOT"]); #Change to the servers root directory so all relations work, regardless from where they were called


########Appearence ############
#Style
$baseStyle = 'styles/baseStyle.css';
$phpStyle = 'styles/phpStyle.php';

#Texts
$title = 'websiteTitlePhotobox';
$headline = 'Überschrift';
$printConfirmation = 'Möchtest du das Foto drucken? Das Drucken dauert etwa 50 Sekunden.'; #Test that is being displayed to confirm printing
$subHeadline = 'Name & Name';
$fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

#Buttons
$galleryButtonText = "Galerie";
$galleryButtonImage = "resources/img/galleryButtonImage.svg";

$photoButtonText = "Foto";
$photoButtonImage = "resources/img/photoButtonImage.svg";

$printButtonText = "Drucken";
$printButtonImage = "resources/img/printButtonImage.svg";

#Images
$logoPath = 'resources/img/Logo.svg';
$backgroundImage = 'resources/img/background1.jpg';

##########Photos
$framePath = 'resources/frames/frame.png';
$printWithFrame = 1;



#Keycodes
$startPhotoCountdown = 13; #Keycode for starting the Photo Countdown (13=Enter)

#############################################################
#Other stuff that probably noone will ever need to touch
#############################################################

#Take Photo
$countdownTime = 3; #Time in seconds for the countdown
$displayTime = 10; #How long the picture should be showed after taking it

#
date_default_timezone_set('Europe/Berlin'); # Timezone for the picture filename
$photoPath ='resources/photos/original/';  #Path where photos should be stored. Must end in a "/"
$fileExtension = 'jpg';


if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { #detect OS of Server for testing purposes
    #Using Windows
    $takePhotoCommand = '"C:\Program Files (x86)\digiCamControl\CameraControlCmd.exe" /capture /filename ' . realpath($photoPath) . '\%s'; #%s is the placeholder for the filename that the script will automatically genrate 
} else {
    #Not using windows 
    $takePhotoCommand = 'gphoto2 --capture-image-and-download --filename=%s'; #%s is the placeholder for the filename that the script will automatically genrate 
}

#thumbnails
$thumbnailWidth = 600; #Width of the thumbnail in Pixels --> Height will be automatically dependent on the aspect ratio
$thumbnailPath = 'resources/photos/thumbs'; #Path where thumbnails should be stored. Without a trailing "/"

#gallery
$photoswipeCorePath ='scripts/photoswipe/photoswipe.esm.js';
$photoswipeLightboxPath = 'scripts/photoswipe/photoswipe-lightbox.esm.js';
$photoswipeCssPath =  'scripts/photoswipe/photoswipe.css';



#printing
$printingPath = 'resources/photos/print/'; #Folder where prints are generated to and printed from. Must end in a "/"
$printTargetWidth = 6000; #width of the document to be printed
$printTargetHeight = 4000; #height of the document to be printed
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { #detect OS of Server for testing purposes
    #Using Windows
    $printCommand = 'echo this command is in for testing mspaint /pt %s'; #%s is the placeholder for the filename
} else {
    #Not using windows 
    $printCommand = 'lp -o landscape -o fit-to-page %s'; #%s is the placeholder for the filename
    #
}


#photoManager
$dbFilename = "photos.db"; #Filename for the Database that stores the Info regarding the photo Libary
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/db/"; #Folder where the photo database is stored. Must end in a "/"

chdir($prevCwd);
?>