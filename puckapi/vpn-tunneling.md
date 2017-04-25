### Setting up OpenVPN tunneling ###
[https://gist.github.com/superjamie/ac55b6d2c080582a3e64]

Follow these instructions. "VPN Kill Switch" section is unnecessary.

### Actual rules ###
This will push a servce on the local LAN through the tunnel interface. We use port forwarding since everything on the other side of the tunnel sees these services at the Pi's tunnel IP only.

In the example below, we are forwarding an RTSP stream from LAN IP 10.0.3.122, port 554 to the Pi at LAN IP 10.0.3.100, port 19122. To view this service on the other side of the tunnel, visit (tunnel IP):19122. This is for TCP only; UDP works too if you change the protocol in the commands.

    sudo iptables -t nat -A PREROUTING -i tun0 -p tcp --dport 19122 -j DNAT --to-destination 10.0.3.122:554
    sudo iptables -t nat -A POSTROUTING -o eth0 -p tcp --dport 554 -d 10.0.3.122 -j SNAT --to-source 10.0.3.100:19122
    
The changes will take effect immediately but do not persist after restart. To make changes permanent:

    sudo netfilter-persistent save
    
This service was installed as part of the linked instructions above. There's probably no limit to how many of these rules you could add...
