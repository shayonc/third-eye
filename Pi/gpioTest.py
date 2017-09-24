#import RPi.GPIO library as GPIO
import RPi.GPIO as GPIO
#Set GPIO numbering scheme to pinnumber
GPIO.setmode(GPIO.BOARD)
#setup pin 18 as an output
GPIO.setup(18,GPIO.OUT)
#lights off
GPIO.output(18, GPIO.HIGH)
print(str(GPIO.input(18)))  # returns 1

GPIO.output(18, GPIO.LOW)
print(str(GPIO.input(18)))  # returns 0
