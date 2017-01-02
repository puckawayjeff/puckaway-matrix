# /usr/bin/slack #

    #!/bin/bash
    SLACK_URL=https://hooks.slack.com/services/[MY API URL HERE]
    MESSAGE=$1
    USERNAME="PuckaPi"
    CHANNEL="#general"
    ICON=":pi:"
    curl -X POST --data-urlencode "payload={\"channel\": \"$CHANNEL\", \"text\": \"$MESSAGE\", \"username\": \"$USERNAME\", \"icon_emoji\": \"$ICON\"}" -k $SLACK_URL

Simple little script to call to post things to Slack as PuckaPi using curl.
