# Import the module
import subprocess
import time

keepMaking = True
videoNumber = 0

print("Press q to quit process.")
#this is the command that we wish to run on the command
#raspivid -o video.h264 -t 10000
while(keepMaking):
	# Set up the echo command and direct the output to a pipe
	p1 = subprocess.Popen(['raspivid', '-o', 'video%s.h264' % str(videoNumber), '-t', '2000'], stdout=subprocess.PIPE)
	videoNumber += 1
	#output = p1.communicate()[0]
	#print output
	#host = raw_input(.)
        #if(host == 'q'):
		#keepMaking = False
	time.sleep(10000)
