<a id="gallerytrigger" class='button' href="#">
    <img src="<?php echo $galleryButtonImage; ?>" alt="">
    <p class="label">
        <?php echo $galleryButtonText; ?>
        </div>
</a>

<link rel="stylesheet" href="<?php echo $photoswipeCssPath; ?>">



<script type="module">
    import PhotoSwipeLightbox from '<?php echo './' . $photoswipeLightboxPath; ?>'
    const options = {
        gallery: '#my-gallery',
        children: 'a',
        pswpModule: () => import('<?php echo './' . $photoswipeCorePath; ?>'),
        imageClickAction: 'close',
        tapAction: 'close'
    }


    window.lightbox = new PhotoSwipeLightbox(options);
    lightbox.on('uiRegister', function () {
        lightbox.pswp.ui.registerElement({
            name: 'print-button',
            order: 8,
            isButton: true,
            //  tagName: 'a',

            // SVG with outline
            /* html: {
                 isCustomSVG: true,
                 inner: '<path d="M20.5 14.3 17.1 18V10h-2.2v7.9l-3.4-3.6L10 16l6 6.1 6-6.1ZM23 23H9v2h14Z" id="pswp__icn-download"/>',
                 outlineID: 'pswp__icn-download'
             },*/

            // Or provide full svg:
            // html: '<svg width="32" height="32" viewBox="0 0 32 32" aria-hidden="true" class="pswp__icn"><path d="M20.5 14.3 17.1 18V10h-2.2v7.9l-3.4-3.6L10 16l6 6.1 6-6.1ZM23 23H9v2h14Z" /></svg>',
            html: '<p style="color: white"><?php echo $printButtonText; ?></p>',
            // Or provide any other markup:
            // html: '<i class="fa-solid fa-download"></i>' 

            onInit: (el, pswp) => {
                el.setAttribute('download', '');
                el.setAttribute('target', '_blank');
                el.setAttribute('rel', 'noopener');

                pswp.on('change', () => {
                    el.setAttribute('data-id', pswp.currSlide.data.element.getAttribute('data-id'));
                });
            },

            onClick: (event, el) => {
                if (confirm('<?php echo $printConfirmation; ?>')) {
                    printId(el.getAttribute('data-id'));
                }
            }

        });
    });



    lightbox.init();



    function printId(id) {
        var reqUrl = "print.php?id=" + id;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                response = JSON.parse(this.responseText);

                if (response.success == true) { //If everything went well, add success class for 5 seconds
                    //if everything went well
                    console.log(response);
                } else {// if something went wrong, log response and add fail class for 5 seconds
                    //if something went wrong
                    console.log(response);
                }
            }
        };
        xhttp.open("GET", reqUrl, true);
        xhttp.send();
    }



</script>


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

        var galleryFrame = document.getElementById("my-gallery");

        function loadGalleryImages() {
            galleryFrame.innerHTML = '';
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    response = JSON.parse(this.responseText)
                    // console.log(response);
                    response.data.slice().reverse().forEach(element => {
                        const photoAElement = document.createElement("a");
                        photoAElement.setAttribute('data-pswp-src', element.filepath);
                        photoAElement.setAttribute('data-pswp-width', element.width);
                        photoAElement.setAttribute('data-pswp-height', element.height);
                        photoAElement.setAttribute('data-id', element.id);
                        photoAElement.setAttribute('target', '_blank');
                        const thumbnailElement = document.createElement("img");
                        thumbnailElement.setAttribute('src', element.thumbnailPath);
                        photoAElement.appendChild(thumbnailElement);
                        galleryFrame.appendChild(photoAElement);


                    });
                }
            };
            xhttp.open("GET", "getPhotoList.php", true);
            xhttp.send();
        }









        //OLD STUFF ##################################################     


        function loadGalleryImagesOld() {
            galleryFrame.innerHTML = '';
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
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

                printBtn.classList.add("loading");
                var reqUrl = "print.php?fileName=" + targetFile;
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        response = JSON.parse(this.responseText);

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
        if (document.getElementsByClassName('pswp__button--close')[0] != undefined) {
            document.getElementsByClassName('pswp__button--close')[0].click();  //close the gallery lightbox, if it is open
        }
        var galleryModal = document.getElementById("galleryFrame");
        galleryModal.style.display = "none";
        content.style.display = contentDisplay;

    }
</script>