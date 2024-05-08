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
            galleryFrame.innerHTML = '';
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText)
                    response.data.forEach(element => {
                        const photoDiv = document.createElement("div");
                        photoDiv.classList.add("photoWrapper");
                        const pic = document.createElement("img"); //append a img element that will contain the picture 
                        pic.src = element.filepath;
                        pic.classList.add("zoomable");
                        photoDiv.appendChild(pic);
                        galleryFrame.appendChild(photoDiv);
                        addPrintButton(photoDiv, element.fileName)
                    });



                }
            };
            xhttp.open("GET", "getPhotoList.php", true);
            xhttp.send();
        }


    }, false);



    function addPrintButton(parentElement, targetFile) {
        const buttonWrapper = document.createElement('div');
        buttonWrapper.classList.add("buttonWrapper");
        const printBtn = document.createElement('a');
        printBtn.href = "#";
        printBtn.classList.add("printButton");
        printBtn.setAttribute('data-path', targetFile);
        const printLabel = document.createElement("p");
        printLabel.classList.add("label");
        printLabel.appendChild(document.createTextNode("<?php echo $printButtonText ?>"));
        const printLogo = document.createElement("img");
        printLogo.src = "<?php echo $printButtonImage ?>";
        printBtn.appendChild(printLogo);
        printBtn.appendChild(printLabel);
        buttonWrapper.appendChild(printBtn);
        parentElement.appendChild(buttonWrapper);
    }


    // When the user clicks on a print button
    window.addEventListener('click', function (event) {
        if (event.target.parentElement.classList.contains("printButton")) {
            console.log("print clicked");
            var reqUrl = "print.php?fileName=" + event.target.parentElement.getAttribute('data-path');
          //  reqUrl.concat(event.target.parentElement.getAttribute('data-path'));
            console.log(reqUrl);
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText);
                    console.log(response);
                    console.log(event.target.parentElement.getAttribute('data-path'));
                }
            };
            xhttp.open("GET", reqUrl , true);
            xhttp.send();




        }
    });




    // When the user clicks on an image
    window.addEventListener('click', function (event) {
        if (event.target.classList.contains("zoomable")) {
            const target = event.target;
            target.classList.toggle('zoomed');
            target.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
    });

</script>