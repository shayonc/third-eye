# Import the module
import subprocess

keepMaking = true
videoNumber = 0

echo("Press q to quit process.")
#this is the command that we wish to run on the command
#raspivid -o video.h264 -t 10000
while(keepMaking){
	# Set up the echo command and direct the output to a pipe
	p1 = subprocess.Popen(['raspivid', '-o', 'video%s.h264' % str(videoNumber), '-t', '2000'], stdout=subprocess.PIPE)
	videoNumber++
	#output = p1.communicate()[0]
	#print output
	#host = raw_input(.)
	if(host == 'q'){
		keepMaking = false
	}
	sleep(10000)
}
