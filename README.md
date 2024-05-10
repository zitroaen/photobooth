# Photobooth

This is my quick and dirty implementation of a photobox. This runs in a  Webserver and is accessed via a browser.

**Attention! This has not been designed secure! Only run this on isolated devices!**

## Features

The Photobooth supports the following features: 
- taking photos with a DSLR connected via USB
- "Hotkey" support to trigger a photo
- Gallery (currently there is no thumbnail creation implemented. This is currently designed to run on the same machine where it is displayed)
- Overlaying a Frame/Watermark for printing

## Installation
- Install CUPS
- install libgphoto2
- install a PHP-Server of choice
- Throw the content of the folder "Webroot" of this repository into the server's root directory
- adjust settings in the config/config.php file if needed

## Open points
- Printing
- Live upload to Server
