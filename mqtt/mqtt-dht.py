#!/usr/bin/python
# MQTT DHT Publisher
# ------------------
# Reads data from a temperature/humidity sensor attached via GPIO and publishes it to an MQTT broker
# every 30 seconds. Meant to be installed as a systemd service. Publishes to "dht" topic, specified
# subtopic, then "temperature" and "humidity".
# Uses Fahrenheit butcan be commented out to use Celcius.
#
# Requires Adafruit_DHT and paho-mqtt libraries.

import Adafruit_DHT as dht
import paho.mqtt.publish as publish
import socket
import sys
import time

# set vars
mqtt_broker = "10.0.3.111" # hostname/IP of MQTT broker
area_name = "pole-barn"    # will be used as subtopic of "dht/"
GPIO_pin = 26              # GPIO pin ID
starttime=time.time()      # used for timer

#begin 30 second loop
while True:
    # read data from attached sensor
    humidity, temperature = dht.read_retry(dht.AM2302, GPIO_pin)
    # only publish to MQTT if data is good
    if humidity is not None and temperature is not None:
        # delete or comment out the below line to get Celcius
        temperature = temperature * 9/5.0 + 32
        # compose MQTT messages
        msgs = [("dht/"+area_name+"/temperature", round(temperature,2)),
            ("dht/"+area_name+"/humidity", round(humidity,2)),
            ("dht/"+area_name+"/time", int(time.time()))]
        # publish messages to MQTT broker
        publish.multiple(msgs, hostname=mqtt_broker, client_id=socket.gethostname())
    # chill out until 30 seconds have passed since script started
    time.sleep(30.0 - ((time.time() - starttime ) % 30.0))
