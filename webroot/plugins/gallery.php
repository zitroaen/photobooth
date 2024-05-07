<a id="gallerytrigger" class='button' href="#">
    <img src="<?php echo $galleryButtonImage; ?>" alt="">
    <p class="label">
        <?php echo $galleryButtonText; ?>
        </div>
</a>

<script>


    window.addEventListener("load", function (event) {

        var galleryModal = document.getElementById("galleryFrame");
        // Get the button that opens the modal
        var galleryBtn = document.getElementById("gallerytrigger");
        // Get the <span> element that closes the modal
        var gallerySpan = galleryModal.getElementsByClassName("close")[0];
        //get the content and save display mode that is defined in css so we can go back afterwards
        var content = document.getElementsByClassName("content")[0];
        var contentDisplay = content.style.display;

        // When the user clicks on the button, open the modal
        galleryBtn.onclick = function () {
            console.log("gallery click");
            openGalleryModal();
        }

        // When the user clicks on <span> (x), close the modal
        gallerySpan.onclick = function () {
            closeGalleryModal()
        }


        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener('click', function (event) {
            if (event.target == galleryModal) {
                closeGalleryModal()
            }
        });

        function openGalleryModal() {
            galleryModal.style.display = "flex";
            content.style.display = "none";
            loadGalleryImages();
        }

        function closeGalleryModal() {
            galleryModal.style.display = "none";
            content.style.display = contentDisplay;
        }

        var galleryFrame = document.getElementById("galleryFrame").getElementsByClassName('modal-frame')[0].getElementsByClassName('modal-content')[0];


        function loadGalleryImages() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText)
                    console.log(response);
                    response.data.forEach(element => {
                        const pic = document.createElement("img"); //append a img element that will contain the picture 
                        pic.src = element.filepath;
                        pic.classList.add("zoomable");
                        galleryFrame.appendChild(pic);
                    });



                }
            };
            xhttp.open("GET", "getPhotoList.php", true);
            xhttp.send();
        }


    }, false);




    // When the user clicks on an image
    window.addEventListener('click', function (event) {
        if (event.target.classList.contains("zoomable")) {
            const target = event.target;
            target.classList.toggle('zoomed');
        }
    });

</script>