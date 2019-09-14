#!/usr/bin/python3

# for processing times from logs
import time
import datetime
# for pretty output
import json
# to get file from SSH
import subprocess

# set some vars
output = { 'apc-log': { } }                             # create container array
logfile = '/var/log/apcupsd.events'      # path to log file
apcs = { 'pole-barn': 'pi@10.0.3.112', \
         'server-closet': 'localhost', \
         'shack': 'pi@10.0.3.45' }       # list of UPSes and their addresses

# Main function: reads logs for a given UPS line-by-line and converts them
# to description/timestamp pairs, sorted in reverse order. Includes total
# event counter for each UPS.
def getlogs(file,server):
    # start counter
    i = 0
    # build empty container array
    eventlist = { 'events': { } }

    # reads local log file
    if server == 'localhost':
        lines = reversed(list(open(logfile)))
        # removes newline from end of each line. Doesn't happen with SSH.
        for line in lines:
            line = line[:-1]

    # reads remote log file over SSH; assumes passwordless SSH is set up
    else:
        ssh = subprocess.Popen(['ssh', server, 'cat', logfile], stdout=subprocess.PIPE)
        sso, sse = ssh.communicate()
        lines = reversed(sso.decode('utf-8').splitlines())

    # processes log lines, splitting timestamp and description
    for line in lines:
        title = str(i).zfill(3)
        eventlist['events'][title] = { 'timestamp': str(line[:25]), 'description': str(line[27:]) }
        i += 1

    # returns total number of events for a given UPS
    eventlist['num-events'] = i
    return eventlist

# runs our function for each UPS in the list and builds output
for apcname, server in apcs.items():
    output['apc-log'][apcname] = getlogs(logfile,server)

# output the JSON file
print(json.dumps(output, indent=4, sort_keys=True))
