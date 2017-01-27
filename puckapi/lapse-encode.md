# Timelapse Encoding #

Get some good tools:

    sudo apt-get install gstreamer1.0-tools

Use gstreamer to have the hardware encoders do the heavy lifting. This creates the timelapse video in mere minutes.
(Assumes this is run from the actual directory for now)

    gst-launch-1.0 multifilesrc location=camX-%04d.jpg index=1 caps="image/jpeg,framerate=30/1" !\
    jpegdec ! queue ! omxh264enc target-bitrate=3200000 control-rate=variable !\
    video/x-h264,stream-format=byte-stream,width=1280,height=720,framerate=30/1,profile=high !\
    h264parse ! mp4mux faststart=true ! filesink location=output.mp4
    
Breaking down the command:
* **multifilesrc**: location of source JPEG files. Must start with 0000 (indexing is broken?) and will iterate up. In above example, it's camX-0000.jpg. We're going for 30fps here.
* **jpegdec, queue**: decode JPEG. Queue helps the timecode somehow? I dunno...
* **omxh264enc**: hardware encode to x264. So damn fast compared to ffmpeg using the CPU. Bitrate in example creates a file that's 24MB/minute.
* **video/x-h264**: explicitly stating things here to help create a well-formed file that doesn't puke during playback
* **h264parse, mp4mux**: output container is mp4 (streamable because of faststart)
* **filesink**: where we're putting it.
