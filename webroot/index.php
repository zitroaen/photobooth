<?php
#Load Settings
include('config/config.php');
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="<?php echo $baseStyle; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $customizationStyle; ?>" type="text/css">
    <title> <?php echo $title; ?> </title>

</head>

<body>
<?php include 'logo.php'?>


<h1><?php echo $headline; ?></h1>
<h2><?php echo $subHeadline; ?></h2>



<a href="gallery.php">
  <div id="galleryButton" class="nav">
    <?php echo $galleryButtonText; ?>
  </div>
  <img src="<?php echo $galleryButtonImage; ?>" alt="">
</a>

<a href="#" onclick="startPictureCountdown()">
  <div id="photoButton" class="nav">
    <?php echo $photoButtonText; ?>
  </div>
  <img src="<?php echo $photoButtonImage; ?>" alt="">
</a>



<div id="photoFrame">
</div>


<script>



function startPictureCountdown() {
    var countdownTime = <?php echo $countdownTime; ?>; //store countdown time in variable
    var photoFrame = document.getElementById("photoFrame"); 
    const para = document.createElement("p"); //append a p element that will contain the countdown status
    para.id = 'countdownTimer'          //Set the ID so the text can be set via Javascript
    photoFrame.appendChild(para);
    document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
    const countdown = setInterval(() => {
                countdownTime--;
                document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
                if (countdownTime<=0){
            clearInterval(countdown);
            para.remove(); //Remove paragraph displaying the countdown from DOM
            takePicture();    
        }
        
    }, 1000);
}


    function takePicture() {
      var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    // document.getElementById("demo").innerHTML = this.responseText;
    response = JSON.parse(this.responseText)
    console.log(response);

    var photoFrame = document.getElementById("photoFrame"); 
    const pic = document.createElement("img"); //append a img element that will contain the picture 
    pic.alt = 'Well shit - something went wrong' 
    pic.id = 'photo'          //Set the ID so the text can be set via Javascript
    photoFrame.appendChild(pic);
    document.getElementById("photo").src=response.path + response.fileName;
    }
  };
  xhttp.open("GET", "takePhoto.php", true);
  xhttp.send();
}

document.addEventListener ("keyup", (evt) => {
	if (evt.keyCode === <?php echo $startPhotoCountdown; ?>) {
        startPictureCountdown();
	}
});


</script>


</body>
</html>


