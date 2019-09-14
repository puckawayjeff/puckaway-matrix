#!/usr/bin/python3

# for getting APC status
from apcaccess import status as apc
# for getting smartplug status
from pyHS100 import SmartPlug
# for getting tun0 ip address
import netifaces as ni
# for pretty output
import json

# set some vars
output = { }
apcs = { 'pole-barn': '10.0.3.112', \
         'server-closet': 'localhost', \
#         'made-up-apc': '10.0.3.99', \
         'shack': '10.0.3.45' }
smartplugs = { 'keep-couch-lamp': '10.0.3.20', \
               'keep-water-pump': '10.0.3.21', \
#               'deck-flood-lights': '10.0.3.22', \
               'pole-barn-workbench': '10.0.3.24', \
               'server-closet-heater': '10.0.3.25', \
               'tiki-totem': '10.0.3.26', \
#               'spare-emeter': '10.0.3.27', \
               'keep-owl-sign': '10.0.3.28' }

# gets tun0 ip address
output['vpnip'] = ni.ifaddresses('tun0')[ni.AF_INET][0]['addr']

# gets apc status
output['apc'] = { }
for apcname, apchost in apcs.items():
    apcdata = { }
    try:
        apcdata = {k.lower(): v for k, v in apc.parse(apc.get(host=apchost)).items()}
    except OSError:
        apcdata['status'] = 'offline'
        apcdata['last-ip'] = apchost
    output['apc'][apcname] = apcdata

# gets smartplug status
output['smartplug'] = { }
for plugname, plugip in smartplugs.items():
    plugdata = { }

    try:
        plug = SmartPlug(plugip)
        plugdata['status'] = plug.state.lower()
        if plug.is_on:
            plugdata['on-since'] = plug.on_since.strftime("%Y-%m-%d %H:%M:%S")
        if plug.has_emeter:
            plugdata['emeter'] = plug.get_emeter_realtime()
    except Exception as ex:
        if type(ex).__name__ == 'SmartDeviceException':
            plugdata['status'] = 'offline'
            plugdata['last-ip'] = plugip

    output['smartplug'][plugname] = plugdata

# output the JSON file
print(json.dumps(output, indent=4, sort_keys=True))
