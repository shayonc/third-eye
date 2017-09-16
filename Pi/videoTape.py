# Import the module
import subprocess
import thread
import time

keepMaking = True
videoNumber = 0

print("Press q to quit process.")
#this is the command that we wish to run on the command
#raspivid -o video.h264 -t 10000
while(keepMaking):
	# Set up the echo command and direct the output to a pipe
	p1 = subprocess.Popen(['raspivid', '-o', 'video%s.h264' % str(videoNumber), '-t', '2000'], stdout=subprocess.PIPE)

        print("File : %s", str(videoNumber))

        time.sleep(1000)
        #give time for video file to be created
	#host = raw_input(.)
        #if(host == 'q'):
		#keepMaking = False
	# Wrap the raw video with an MP4 container: 
	p2 = subprocess.Popen(['MP4Box', '-add', 'video%s.h264' % str(videoNumber), 'video%s.mp4' % str(videoNumber)], stdout=subprocess.PIPE)
	p3 = subprocess.Popen(['rm', 'video%s.h264' % str(videoNumber)], stdout=subprocess.PIPE)
	# Remove the source raw file, leaving the remaining pivideo.mp4 file to play
	#rm 'video%s.h264' % str(videoNumber)
	time.sleep(10000)
        videoNumber+=1
