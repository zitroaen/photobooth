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

    var countdownRunning = false; //Variable that ensures that there is only one countdown running at a time




    window.addEventListener("load", function (event) {
        var photoModal = document.getElementById("photoFrame");
        // Get the button that opens the modal
        var photoBtn = document.getElementById("photoTrigger");
        // Get the <span> element that closes the modal
        var photoSpan = photoModal.getElementsByClassName("close")[0];
        //get the content and save display mode that is defined in css so we can go back afterwards
        var content = document.getElementsByClassName("content")[0];
        var contentDisplay = content.style.display;

        // When the user clicks on the button, open the modal
        photoBtn.onclick = function () {
            startPictureCountdown();
        }

        // When the user clicks on <span> (x), close the modal
        photoSpan.onclick = function () {
            closePhotoModal()
        }

        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener('click', function (event) {
            if (event.target == photoModal) {
                closePhotoModal()
            }
        });



        function closePhotoModal() {
            photoModal.style.display = "none";
            content.style.display = contentDisplay;
            clearInterval(window.photoCountdown);
            clearInterval(window.modalCountdown);
            countdownRunning = false;
        }



        var photoFrame = document.getElementById("photoFrame").getElementsByClassName('modal-frame')[0].getElementsByClassName('modal-content')[0];
        var countdown;

        function startPictureCountdown() {
            if (!countdownRunning) {
                photoFrame.innerHTML = '';
                countdownRunning = true;
                photoModal.style.display = "flex";
                content.style.display = "none";
                var countdownTime = <?php echo $countdownTime; ?>; //store countdown time in variable

                // photoFrame.style.display = "block";
                const para = document.createElement("p"); //append a p element that will contain the countdown status
                para.id = 'countdownTimer'          //Set the ID so the text can be set via Javascript
                photoFrame.appendChild(para);
                document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
                window.photoCountdown = setInterval(() => {
                    countdownTime--;
                    document.getElementById("countdownTimer").textContent = countdownTime; //Set Text to current time
                    if (countdownTime <= 0) {
                        clearInterval(window.photoCountdown);
                        para.remove(); //Remove paragraph displaying the countdown from DOM
                        takePicture();
                    }

                }, 1000);
            }
        }

        function takePicture() {
            var photoHttp = new XMLHttpRequest();
            photoHttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText)
                    //console.log(response);
                    const photoWrapper = document.createElement('div');
                    photoWrapper.classList.add('photoWrapper');
                    photoWrapper.style.backgroundImage = "url('" + response.path + response.fileName+"')";
                    const pic = document.createElement("img"); //append a img element that will contain the picture 
                    pic.alt = 'Something went wrong'
                    pic.id = 'photo'          //Set the ID so the text can be set via Javascript
                   // photoWrapper.appendChild(pic);
                    photoFrame.appendChild(photoWrapper);
                    addPrintButton(photoFrame, response.fileName);
                   // document.getElementById("photo").src = response.path + response.fileName;
                    var displayTime = <?php echo $displayTime; ?>;
                    window.modalCountdown = setInterval(() => {
                        displayTime--;
                        if (displayTime <= 0) {
                            clearInterval(window.modalCountdown);
                            closePhotoModal();
                        }

                    }, 1000);
                }
            };
            photoHttp.open("GET", "takePhoto.php", true);
            photoHttp.send();
        }

        document.addEventListener("keyup", (evt) => {
            if (evt.keyCode === <?php echo $startPhotoCountdown; ?>) {
                startPictureCountdown();
            }
        });









    }, false);









</script>