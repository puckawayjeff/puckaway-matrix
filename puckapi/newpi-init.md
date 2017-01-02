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

Then

    sudo adduser pi www-data
    sudo chown www-data:www-data /var/www/*

Mount external usb to /var/www/html/usb per instructions at <http://www.htpcguides.com/properly-mount-usb-storage-raspberry-pi/>

Pi user should now have full read/write access to files within the www folder.

### Random note for debugging ###
    tail -f /var/log/apache2/error.log
This is a good way to watch what's getting fucked up in real time.  

    sudo service apache2 restart
Does what it says on the tin.
