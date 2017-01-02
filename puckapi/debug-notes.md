

### Random notes for debugging ###
    tail -f /var/log/apache2/error.log
This is a good way to watch what's getting fucked up in real time.  
   
   
   
    sudo service apache2 restart
Does what it says on the tin. Sub in your service name for apache2
   
   
   
    sudo apt-get install dos2unix
    dos2unix /your/file/here
Fun little tool to fix $'\r': command not found errors when copying files from Windows-based shares.
