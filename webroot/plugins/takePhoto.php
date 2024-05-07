<?php
?>


<a class='button' href="#" onclick="startPictureCountdown()">
<img src="<?php echo $photoButtonImage; ?>" alt="">
<div id="photoButton" class="nav">
<?php echo $photoButtonText; ?>
</div>
</a>



<script>



function startPictureCountdown() {
    var countdownTime = <?php echo $countdownTime; ?>; //store countdown time in variable
    var photoFrame = document.getElementById("photoFrame");
    photoFrame.style.display = "block";
    const para = document.createElement("p"); //append a p element that will contain the countdown status
    para.id = 'countdownTimer'          //Set the ID so the text can be set via Javascript
    photoFrame.appendChild(para);
    document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
    const countdown = setInterval(() => {
        countdownTime--;
        document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
        if (countdownTime <= 0) {
            clearInterval(countdown);
            para.remove(); //Remove paragraph displaying the countdown from DOM
            takePicture();
        }

    }, 1000);
}


function takePicture() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            response = JSON.parse(this.responseText)
            console.log(response);

            var photoFrame = document.getElementById("photoFrame");
            if (photoFrame.contains(document.getElementById("photo"))) {
                document.getElementById("photo").remove;
            }

            const pic = document.createElement("img"); //append a img element that will contain the picture 
            pic.alt = 'Well shit - something went wrong'
            pic.id = 'photo'          //Set the ID so the text can be set via Javascript
            photoFrame.appendChild(pic);
            document.getElementById("photo").src = response.path + response.fileName;
            var displayTime = <?php echo $displayTime; ?>;
            const countdown2 = setInterval(() => {
                displayTime--;
                if (displayTime <= 0) {
                    clearInterval(countdown2);
                    photoFrame.style.display = "none";
                }

            }, 1000);




        }
    };
    xhttp.open("GET", "takePhoto.php", true);
    xhttp.send();
}

document.addEventListener("keyup", (evt) => {
    if (evt.keyCode === <?php echo $startPhotoCountdown; ?>) {
        startPictureCountdown();
    }
});


</script>



