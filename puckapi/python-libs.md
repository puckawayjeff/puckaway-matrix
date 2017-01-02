# Python Libraries #
We need to import some scripts and install some things to talk to the DHT22/AM2032 sensor and do other cool tricks.

    mkdir /var/www/python
    cd /var/www/python
    git clone https://github.com/adafruit/Adafruit_Python_DHT.git
    cd Adafruit_Python_DHT
    sudo apt-get install build-essential python-dev -y
    sudo python setup.py install
    cd ..
    rm -r Adafruit_Python_DHT
