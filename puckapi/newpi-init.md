# Initial setup for a new Pi #
Install latest raspbian, perform apt-get update, dist-upgrade, autoremove  

    sudo apt-get install apache2 -y
    sudo apt-get install php5 libapache2-mod-php5 php5-curl php5-gd -y
    sudo apt-get install vsftpd -y
    sudo apt-get install apcupsd -y
    sudo apt-get remove usbmount --purge
    sudo apt-get install exfat-tools -y
 
In /etc/vsftpd.conf:  

    write_enable=YES
    local_umask=022
    use_localtime=YES

Then

    sudo adduser pi www-data
    sudo chown www-data:www-data /var/www/*
    sudo a2enmod rewrite

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
    


Mount external usb to /var/www/html/usb per instructions at <http://www.htpcguides.com/properly-mount-usb-storage-raspberry-pi/>

Pi user should now have full read/write access to files within the www folder. UPS monitoring over USB, exFAT drive support, PHP Curl and GD Image Library commands, and FTP access are all ready. Raspbian PIXEL will no longer automount USB drives, either.
