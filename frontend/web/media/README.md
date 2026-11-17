# Static media

Put the tutorial video here as `biosketch-tutorial.mp4` (served at /media/biosketch-tutorial.mp4,
played on /tutorial). Encode as H.264/AAC with the moov atom at the front so it starts
streaming immediately:

    ffmpeg -i input.mp4 -c copy -movflags +faststart biosketch-tutorial.mp4
