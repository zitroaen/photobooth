<?php
?>


<a id='photoTrigger' ,class='button' href="#">
    <img src="<?php echo $photoButtonImage; ?>" alt="">
    <p class="label">
        <?php echo $photoButtonText; ?>
    </p>
</a>



<style>


</style>



<script>






    window.onload = (event) => {
        var modal = document.getElementById("photoFrame");
        // Get the button that opens the modal
        var btn = document.getElementById("photoTrigger");
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        //get the content and save display mode that is defined in css so we can go back afterwards
        var content = document.getElementsByClassName("content")[0];
        var contentDisplay = content.style.display;

        // When the user clicks on the button, open the modal
        btn.onclick = function () {
            modal.style.display = "flex";
            content.style.display = "none";
            startPictureCountdown();
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function () {
            closePhotoModal()
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                closePhotoModal()
            }
        }


    function closePhotoModal() {
        modal.style.display = "none";
        content.style.display = contentDisplay;
    }



    var photoFrame = document.getElementById("photoFrame").getElementsByClassName('modal-frame')[0].getElementsByClassName('modal-content')[0];


function startPictureCountdown() {
        var countdownTime = <?php echo $countdownTime; ?>; //store countdown time in variable
        
       // photoFrame.style.display = "block";
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
                        closePhotoModal();
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









    };






    


</script>