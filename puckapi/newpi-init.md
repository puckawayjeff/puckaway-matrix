# Initial setup for a new Pi #
Install latest raspbian, perform apt-get update, dist-upgrade, autoremove  

    sudo apt-get install apache2 -y
    sudo apt-get install php libapache2-mod-php php-curl php-gd -y
    sudo apt-get install acl vsftpd apcupsd fswebcam libav-tools -y
    sudo apt-get remove usbmount --purge
    sudo apt-get install exfat-fuse exfat-utils -y
 
In /etc/vsftpd.conf:  

    write_enable=YES
    local_umask=022
    use_localtime=NO

Then

    sudo adduser pi www-data
    sudo chown www-data:www-data /var/www/*
    sudo a2enmod rewrite
    sudo a2enmod proxy_http

Add this to /etc/apache2/sites-enabled/000-default.conf right after "DocumentRoot /var/www/html"

    <Directory /var/www >
            AllowOverride All
    </Directory>

Add these lines to the bottom of the file for any HTTP redirects for the VPN tunnel (use ports to punch through to other devices on Puckaway network. Add as many as needed.
    
    Listen 19100
    <VirtualHost *:19100>
            ProxyPass / http://192.168.1.100:80/
            ProxyPassReverse / http://192.168.1.100:80/
    </VirtualHost>

Run this any time the config file changes.

    sudo service apache2 restart
    
Mount external usb to /var/www/html/usb
    
    sudo mkdir /var/www/html/usb
    sudo chown -R pi:pi /var/www/html/usb
    sudo chmod -R 775 /var/www/html/usb
    sudo setfacl -Rdm g:pi:rwx /var/www/html/usb
    sudo setfacl -Rm g:pi:rwx /var/www/html/usb

Run this command and verify /dev mount point (should be /dev/sda1):
    
    sudo blkid

Replace /dev/sda1 with result from previous command, if needed:

    sudo mount -o uid=pi,gid=pi /dev/sda1 /var/www/html/usb

Run this command to find UUID for USB drive:
    
    sudo ls -l /dev/disk/by-uuid/

Add this line to /etc/fstab, where XXXX-XXXX is the UUID from the previous command:
    
    UUID=5869-C589  /var/www/html/usb exfat   nofail,uid=pi,gid=pi   0   0

    sudo mount -a
    sudo reboot
    
Troubleshooting instructions at <http://www.htpcguides.com/properly-mount-usb-storage-raspberry-pi/>

Pi user should now have full read/write access to files within the www folder. UPS monitoring over USB, exFAT drive support, PHP Curl and GD Image Library commands, and FTP access are all ready. Raspbian PIXEL will no longer automount USB drives, either.
