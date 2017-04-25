### Tools for Network Monitoring ###
    sudo apt-get install pktstat
    sudo pktstat -i eth0 -nt
    
Realtime monitoring for all traffic over the ethernet port. Switch "eth0" to "tun0" to monitor the VPN connection only, otherwise just pay attention to UDP traffic on port 1194.

    sudo apt-get install vnstat
    service vnstat status
    vnstat
    
Logs all bandwidth use over time. Good monthly summary tool.
