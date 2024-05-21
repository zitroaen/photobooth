#!/bin/bash

# Stop on the first sign of trouble
set -e

USERNAME=$USER
WEBSERVER="apache"
SILENT_INSTALL=false
RUNNING_ON_PI=false

DATE=$(date +"%Y%m%d-%H-%M")
IPADDRESS=$(hostname -I | cut -d " " -f 1)
PHOTOBOOTH_TMP_LOG="/tmp/$DATE-photobooth.txt"


SUBFOLDER=true
KIOSK_MODE=false
HIDE_MOUSE=false
USB_SYNC=false
SETUP_CUPS=false


CUPS_REMOTE_ANY=false
WEBBROWSER="unknown"
KIOSK_FLAG="--kiosk http://localhost"
CHROME_FLAGS=false
CHROME_DEFAULT_FLAGS="--noerrdialogs --disable-infobars --disable-features=Translate --no-first-run --check-for-update-interval=31536000 --touch-events=enabled --password-store=basic"
AUTOSTART_FILE=""
WAYLAND_ENV=false
PHP_VERSION="8.3"




COMMON_PACKAGES=(
    'gphoto2'
    "php${PHP_VERSION}-gd"
    "php${PHP_VERSION}-mbstring"
)

EXTRA_PACKAGES=(
)

INSTALL_PACKAGES=()

function info {
    echo -e "\033[0;36m${1}\033[0m"
    echo "${1}" >>"$PHOTOBOOTH_TMP_LOG"
}

function warn {
    echo -e "\033[0;33m${1}\033[0m"
    echo "WARN: ${1}" >>"$PHOTOBOOTH_TMP_LOG"
}

function error {
    echo -e "\033[0;31m${1}\033[0m"
    echo "ERROR: ${1}" >>"$PHOTOBOOTH_TMP_LOG"
}

function print_spaces() {
    echo ""
    info "###########################################################"
    echo ""
}

#Param 1: Question / Param 2: Default / silent answer
function ask_yes_no {
    if [ "$SILENT_INSTALL" = false ]; then
        read -p "${1}: " -n 1 -r
    else
        REPLY=${2}
    fi
}



function check_username() {
    info "[Info]      Checking if user $USERNAME exists..."
    if id "$USERNAME" &>/dev/null; then
        info "[Info]      User $USERNAME found. Installation process continues."
    else
        error "ERROR: An valid OS username is needed! Please re-run the installer."
        view_help
        exit
    fi
}


function common_software() {
    info "### Updating the system"
    apt-get -qq update
    
    info "### Installing Apache Web Server"
    apache_webserver
    
    
    
    
    
    # Additional packages
    INSTALL_PACKAGES+=("${EXTRA_PACKAGES[@]}")
    
    
    # All required packages independend of Raspberry Pi.
    INSTALL_PACKAGES+=("${COMMON_PACKAGES[@]}")
    
    info "### Installing common software:"
    for required in "${INSTALL_PACKAGES[@]}"; do
        info "[Required]  ${required}"
    done
    
    for package in "${INSTALL_PACKAGES[@]}"; do
        if [ "$(dpkg-query -W -f='${Status}' "$package" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
            info "[Package]   ${package} installed already"
        else
            info "[Package]   Installing missing common package: ${package}"
            apt-get -qq install -y "$package"
        fi
    done
    
}

function apache_webserver() {
    info "### Installing Apache Webserver..."
    apt-get -qq install -y apache2 libapache2-mod-php"$PHP_VERSION"
    sudo systemctl enable --now apache2
}



function general_setup() {
    if [ "$SUBFOLDER" = true ]; then
        cd /var/www/html/
        INSTALLFOLDER="photobooth"
        INSTALLFOLDERPATH="/var/www/html/$INSTALLFOLDER"
        URL="http://$IPADDRESS/$INSTALLFOLDER"
    else
        cd /var/www/
        INSTALLFOLDER="html"
        INSTALLFOLDERPATH="/var/www/html"
        URL="http://$IPADDRESS"
    fi
    
    if [ -d "$INSTALLFOLDERPATH" ]; then
        BACKUPFOLDER="html-$DATE"
        info "${INSTALLFOLDERPATH} found. Creating backup as ${BACKUPFOLDER}."
        mv "$INSTALLFOLDER" "$BACKUPFOLDER"
    else
        info "$INSTALLFOLDERPATH not found."
    fi
    
    mkdir -p "$INSTALLFOLDERPATH"
    chown www-data:www-data "$INSTALLFOLDERPATH"
    chown www-data:www-data /var/www
    
    PHOTOBOOTH_LOG="$INSTALLFOLDERPATH/private/install.log"
}




function start_install() {
    info "### Now we are going to install Photobooth."
    cd /var/www/
    sudo -u www-data git clone https://github.com/zitroaen/photobooth photobooth
    sudo mv photobooth/webroot/* html      
}

function detect_browser() {
    if [ "$(dpkg-query -W -f='${Status}' "firefox" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="firefox"
        CHROME_FLAGS=false
        elif [ "$(dpkg-query -W -f='${Status}' "firefox-esr" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="firefox-esr"
        CHROME_FLAGS=false
        elif [ "$(dpkg-query -W -f='${Status}' "chromium-browser" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="chromium-browser"
        CHROME_FLAGS=true
        elif [ "$(dpkg-query -W -f='${Status}' "chromium" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="chromium"
        CHROME_FLAGS=true
        elif [ "$(dpkg-query -W -f='${Status}' "google-chrome" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="google-chrome"
        CHROME_FLAGS=true
        elif [ "$(dpkg-query -W -f='${Status}' "google-chrome-stable" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="google-chrome-stable"
        CHROME_FLAGS=true
        elif [ "$(dpkg-query -W -f='${Status}' "google-chrome-beta" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
        WEBBROWSER="google-chrome-beta"
        CHROME_FLAGS=true
    else
        WEBBROWSER="unknown"
        CHROME_FLAGS=false
    fi
}

function browser_shortcut() {
    if [ "$CHROME_FLAGS" = true ]; then
        if [ "$RUNNING_ON_PI" = true ]; then
            if [ "$WAYLAND_ENV" = true ]; then
                EXTRA_FLAGS="$CHROME_DEFAULT_FLAGS --ozone-platform=wayland --start-maximized"
            else
                EXTRA_FLAGS="$CHROME_DEFAULT_FLAGS --use-gl=egl"
            fi
        else
            EXTRA_FLAGS="$CHROME_DEFAULT_FLAGS"
        fi
    else
        EXTRA_FLAGS=""
    fi
    
    echo "[Desktop Entry]" >"$AUTOSTART_FILE"
    
    {
        echo "Version=1.3"
        echo "Terminal=false"
        echo "Type=Application"
        echo "Name=Photobooth"
    } >>"$AUTOSTART_FILE"
    
    if [ "$SUBFOLDER" = true ]; then
        echo "Exec=$WEBBROWSER $KIOSK_FLAG/$INSTALLFOLDER $EXTRA_FLAGS" >>"$AUTOSTART_FILE"
    else
        echo "Exec=$WEBBROWSER $KIOSK_FLAG $EXTRA_FLAGS" >>"$AUTOSTART_FILE"
    fi
    
    {
        echo "Icon=$INSTALLFOLDERPATH/resources/img/favicon-96x96.png"
        echo "StartupNotify=false"
        echo "Terminal=false"
    } >>"$AUTOSTART_FILE"
}

function browser_desktop_shortcut() {
    if [ -d "/home/$USERNAME/Desktop" ] && [ "$USERNAME" != "" ]; then
        info "### Adding photobooth shortcut to Desktop"
        AUTOSTART_FILE="/home/$USERNAME/Desktop/photobooth.desktop"
        browser_shortcut
        chmod +x /home/"$USERNAME"/Desktop/photobooth.desktop
        chown "$USERNAME:$USERNAME" /home/"$USERNAME"/Desktop/photobooth.desktop
    fi
}

function browser_autostart() {
    AUTOSTART_FILE="/etc/xdg/autostart/photobooth.desktop"
    browser_shortcut
}

function ask_kiosk_mode() {
    echo -e "\033[0;33m### You probably like to start $WEBBROWSER on every start."
    ask_yes_no "### Open $WEBBROWSER in Kiosk Mode at every boot? [y/N] " "Y"
    echo -e "\033[0m"
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        KIOSK_MODE=true
        info "### We will open $WEBBROWSER in Kiosk Mode at every boot."
    else
        KIOSK_MODE=false
        info "### We won't open $WEBBROWSER in Kiosk Mode at every boot."
    fi
}

function ask_hide_mouse() {
    echo -e "\033[0;33m### You probably like hide the mouse cursor on every start."
    ask_yes_no "### Hide the mouse cursor? [y/N] " "Y"
    echo -e "\033[0m"
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        HIDE_MOUSE=true
        if [ "$WAYLAND_ENV" = false ]; then
            EXTRA_PACKAGES+=('unclutter')
        fi
        info "### We will hide the mouse cursor on every start."
    else
        HIDE_MOUSE=false
        info "### We won't hide the mouse cursor on every start."
    fi
}

function ask_usb_sync() {
    echo -e "\033[0;33m### Sync to USB - this feature will automatically copy (sync) new pictures to a USB stick."
    echo -e "### The actual configuration will be done in the admin panel but we need to setup your OS first."
    ask_yes_no "### Would you like to setup your OS to use the USB sync file backup? [y/N] " "Y"
    echo -e "\033[0m"
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        USB_SYNC=true
        info "### We will setup your OS to be able to use the USB sync file backup."
        info "### Note: automount can only be avoided on Pi OS."
    else
        USB_SYNC=false
        info "### We won't setup your OS to use the USB sync file backup."
    fi
}



function general_permissions() {
    info "### Setting permissions."
    chown -R www-data:www-data "$INSTALLFOLDERPATH"/
    chmod g+s "$INSTALLFOLDERPATH/private"
    gpasswd -a www-data plugdev
    gpasswd -a www-data video
    gpasswd -a "$USERNAME" www-data
    
    info "### Fixing permissions on cache folder."
    mkdir -p "/var/www/.cache"
    chown -R www-data:www-data "/var/www/.cache"
    
    info "### Fixing permissions on npm folder."
    mkdir -p "/var/www/.npm"
    chown -R www-data:www-data "/var/www/.npm"
    
    info "### Disabling camera automount."
    chmod -x /usr/lib/gvfs/gvfs-gphoto2-volume-monitor || true
    
    # Add configuration required for www-data to be able to initiate system shutdown / reboot
    info "### Note: In order for the shutdown and reboot button to work we install /etc/sudoers.d/020_www-data-shutdown"
    cat >/etc/sudoers.d/020_www-data-shutdown <<EOF
# Photobooth buttons for www-data to shutdown or reboot the system from admin panel or via remotebuzzer
www-data ALL=(ALL) NOPASSWD: /sbin/shutdown
EOF
    
    if [ "$USB_SYNC" = true ]; then
        info "### Adding polkit rule so www-data can (un)mount drives"
        
        cat >/etc/polkit-1/localauthority/50-local.d/photobooth.pkla <<EOF
[Allow www-data to mount drives with udisks2]
Identity=unix-user:www-data
Action=org.freedesktop.udisks2.filesystem-mount*;org.freedesktop.udisks2.filesystem-unmount*
ResultAny=yes
ResultInactive=yes
ResultActive=yes
EOF
    fi
}

function hide_mouse() {
    if [ "$WAYLAND_ENV" = true ]; then
        if [ -f "/usr/share/icons/PiXflat/cursors/left_ptr" ]; then
            mv /usr/share/icons/PiXflat/cursors/left_ptr /usr/share/icons/PiXflat/cursors/left_ptr.bak
        fi
    else
        if [ -f "/etc/xdg/lxsession/LXDE-pi/autostart" ]; then
            sed -i '/Photobooth/,/Photobooth End/d' /etc/xdg/lxsession/LXDE-pi/autostart
        fi
        
        cat >>/etc/xdg/lxsession/LXDE-pi/autostart <<EOF
# Photobooth
# turn off display power management system
@xset -dpms
# turn off screen blanking
@xset s noblank
# turn off screen saver
@xset s off
# Hide mousecursor
@unclutter -idle 3
# Photobooth End
EOF
        
    fi
}

function cups_setup() {
    info "### Setting printer permissions."
    gpasswd -a www-data lp
    gpasswd -a www-data lpadmin
    if [ "$CUPS_REMOTE_ANY" = true ]; then
        info "### Access to CUPS will be allowed from all devices in your network."
        cupsctl --remote-any
        /etc/init.d/cups restart
    fi
}


############################################################
#                                                          #
# General checks before the installation process can start #
#                                                          #
############################################################

if [ "$UID" != 0 ]; then
    error "ERROR: Only root is allowed to execute the installer. Forgot sudo?"
    exit 1
fi


if [ "$USERNAME" != "" ]; then
    check_username
else
    error "ERROR: An valid OS username is needed! Please re-run the installer."
    exit
fi
print_spaces


info "### Checking internet connection..."
if [ "$(dpkg-query -W -f='${Status}' "wget" 2>/dev/null | grep -c "ok installed")" -eq 1 ]; then
    if wget -q --tries=10 --timeout=20 -O - http://google.com >/dev/null; then
        info "    connected!"
    else
        error "ERROR: No internet connection!"
        error "       Please connect to the internet and rerun the installer."
        exit 1
    fi
else
    warn "Can not check Internet connection, wget missing!"
fi


############################################################
#                                                          #
# Ask all questions before installing Photobooth           #
#                                                          #
############################################################

echo -e "\033[0;33m### Is Photobooth the only website on this system?"
echo -e "### NOTE: If typing y, the whole /var/www/html folder will be renamed"
ask_yes_no "          to /var/www/html-$DATE if exists! [y/N] " "Y"
echo -e "\033[0m"
if [ "$REPLY" != "${REPLY#[Yy]}" ]; then
    info "### We will install Photobooth into /var/www/html."
    SUBFOLDER=false
else
    info "### We will install Photobooth into /var/www/html/$INSTALLFOLDER."
fi

print_spaces

echo -e "\033[0;33m### You probably like to use a printer."
ask_yes_no "### You like to install CUPS and set needing printer permissions? [y/N] " "Y"
echo -e "\033[0m"
if [[ $REPLY =~ ^[Yy]$ ]]; then
    SETUP_CUPS=true
    EXTRA_PACKAGES+=('cups')
    info "### We will install CUPS if not installed already."
    print_spaces
    
    echo -e "\033[0;33m### By default CUPS can only be accessed via localhost."
    ask_yes_no "### You like to allow remote access to CUPS over IP from all devices inside your network? [y/N] " "N"
    echo -e "\033[0m"
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        CUPS_REMOTE_ANY=true
        info "### We will allow remote access to CUPS over IP from all devices inside your network."
    else
        info "### We won't allow remote access to CUPS over IP from all devices inside your network."
    fi
    
    print_spaces
    
    echo -e "\033[0;33m### You might need some additional drivers to use the print function."
    echo -e "### You like to install a collection of free-software printer drivers"
    ask_yes_no "### (Gutenprint for use with UNIX spooling systems, such as CUPS, lpr and LPRng)? [y/N] " "Y"
    echo -e "\033[0m"
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        EXTRA_PACKAGES+=('printer-driver-gutenprint')
        info "### We will install Gutenprint drivers if not installed already."
    else
        info "### We won't install Gutenprint drivers if not installed already."
    fi
fi

print_spaces

detect_browser
if [ -d "/etc/xdg/autostart" ]; then
    if [ "$WEBBROWSER" != "unknown" ]; then
        ask_kiosk_mode
    else
        warn "### No supported webbrowser found!"
    fi
    print_spaces
fi

if [ -d "/etc/polkit-1/localauthority/50-local.d" ]; then
    ask_usb_sync
else
    info "### /etc/polkit-1/localauthority/50-local.d not found!"
    info "### Can not setup your OS to use the USB sync file backup."
fi
print_spaces



############################################################
#                                                          #
# Go through the installation steps of Photobooth          #
#                                                          #
############################################################

print_spaces
info "### Starting installation..."
print_spaces

common_software
general_setup
start_install
general_permissions

if [ "$WEBBROWSER" != "unknown" ]; then
    browser_desktop_shortcut
    if [ "$KIOSK_MODE" = true ]; then
        browser_autostart
    fi
else
    info "### Browser unknown or not installed. Can not add shortcut to Desktop."
fi
if [ "$HIDE_MOUSE" = true ]; then
    hide_mouse
fi
if [ "$SETUP_CUPS" = true ]; then
    cups_setup
fi




info ""
info "### Congratulations you finished the install process."
info "    Photobooth was installed inside:"
info "        $INSTALLFOLDERPATH"
info ""
info "    Used webserver: $WEBSERVER"
info ""
info "    Photobooth can be accessed at:"
info "        $URL"
info ""
if [ "$SETUP_CUPS" = true ]; then
    info "    In order to use the print function,"
    info "    you'll have to setup your printer inside CUPS:"
    info "        http://localhost:631"
    info ""
fi
info "###"
info "### Have fun with your Photobooth, but first restart your device!"

cat "$PHOTOBOOTH_TMP_LOG" >>"$PHOTOBOOTH_LOG" || warn "WARN: failed to add log to ${PHOTOBOOTH_LOG}"

echo -e "\033[0;33m"
ask_yes_no "### Do you like to reboot now? [y/N] " "N"
echo -e "\033[0m"
if [[ $REPLY =~ ^[Yy]$ ]]; then
    info "### Your device will reboot now."
    shutdown -r now
fi