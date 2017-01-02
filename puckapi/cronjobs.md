# What to add to crontab #
    # Capture 6 camera images every minute
    * * * * * /var/www/cam1cron.sh >/dev/null 2>&1
    # Poll and output temp data every 5 minutes
    */5 * * * * /var/www/tempcheck.sh >/dev/null 2>&1
    # Run timelapse video script every day at 0230
    30 2 * * * /var/www/lapse.sh >/dev/null 2>&1
