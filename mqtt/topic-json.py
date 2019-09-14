#!/usr/bin/python3
import collections
import json
import paho.mqtt.client as mqttClient
import time

# Found at https://gist.github.com/angstwad/bf22d1822c38a92ec0a9
# Using .update wipes out nested "neighbor" values, so this works great.
# It needed tweaking for python3/3.8 compatibility.
def dict_merge(dct, merge_dct):
    for k, v in merge_dct.items():
        if (k in dct and isinstance(dct[k], dict)
                and isinstance(merge_dct[k], collections.abc.Mapping)):
            dict_merge(dct[k], merge_dct[k])
        else:
            dct[k] = merge_dct[k]

# Modified from https://stackoverflow.com/a/33924987
# I didn't need the multiline input but needed to add the message to the
# deepest (furthest nested) key. Maybe there's a more elegant way to fire
# on that item than this counter, but it works.
def add_dict(topic,message):
    result = dict()
    cur_dict = result
    # simple counter so the for loop can ID the last split of the string
    i = 0
    x = topic.count("/")
    for field in topic.strip("/").split("/"):
        if i == x:
            # sets the value of the message to the deepest key
            cur_dict = cur_dict.setdefault(field, message)
        else:
            # creates empty nested keys from the topic string
            cur_dict = cur_dict.setdefault(field, {})
        i += 1
    return result

def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("Connected to broker")
        global Connected                #Use global variable
        Connected = True                #Signal connection
    else:
        print("Connection failed")

def on_message(client, userdata, message):
     # creates nested array for the detected message
     newmsg = add_dict(message.topic,message.payload.decode('utf8'))
     # merges the new message into the master array; updates existing values
     dict_merge(output, newmsg)
     print("output: ",json.dumps(output, indent=4, sort_keys=True))

Connected = False   #global variable for the state of the connection

output = dict()                            #create output array
newmsg = dict()                            #create newmsg parser array
client = mqttClient.Client()               #create new instance
client.on_connect= on_connect              #attach function to callback
client.on_message= on_message              #attach function to callback
client.connect("localhost")    #connect
client.subscribe([("dht/#", 0), ("ups/#",0)])      #subscribe
client.loop_forever()          #then keep listening forever
