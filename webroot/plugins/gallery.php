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

        const printLabel = document.createElement("p");
        printLabel.classList.add("label");
        printLabel.appendChild(document.createTextNode("<?php echo $printButtonText ?>"));
        const printLogo = document.createElement("img");
        printLogo.src = "<?php echo $printButtonImage ?>";
        printBtn.appendChild(printLogo);
        printBtn.appendChild(printLabel);
        buttonWrapper.appendChild(printBtn);
        parentElement.appendChild(buttonWrapper);

        printBtn.addEventListener("click", function () {
            if (!printBtn.classList.contains("loading")) { //Only execute if it's not already running
                printBtn.classList.remove("error");
                printBtn.classList.remove("success");
                console.log(printBtn);
                printBtn.classList.add("loading");
                var reqUrl = "print.php?fileName=" + targetFile;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        response = JSON.parse(this.responseText);
                        console.log(response);
                        printBtn.classList.remove("loading");
                        if (response.success == true) { //If everything went well, add success class for 5 seconds
                            printBtn.classList.add("success");
                            setInterval(function () { printBtn.classList.remove("success"); }, 5000);
                        } else {// if something went wrong, log response and add fail class for 5 seconds
                            printBtn.classList.add("error");
                            setInterval(function () { printBtn.classList.remove("error"); }, 5000);
                            console.log(response);
                        }
                    }
                };
                xhttp.open("GET", reqUrl, true);
                xhttp.send();
            }
        });
    }




    // When the user clicks on an image
    window.addEventListener('click', function (event) {
        if (event.target.classList.contains("zoomable")) {
            const target = event.target;
            target.classList.toggle('zoomed');
            target.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        }
    });


    var content = document.getElementsByClassName("content")[0];
    var contentDisplay = content.style.display;
    function closeGalleryModal() {
        var galleryModal = document.getElementById("galleryFrame");
        galleryModal.style.display = "none";
        content.style.display = contentDisplay;
    }
</script>