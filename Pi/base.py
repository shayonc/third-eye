import subprocess
import thread
import RPi.GPIO as GPIO
import time
import requests
import signal
import sys
import os
import json
import re
sys.path.append(os.path.join(os.getcwd(),'..'))
import watson_developer_cloud
from watson_developer_cloud import VisualRecognitionV3
import watson_developer_cloud.natural_language_understanding.features.v1 as features


GPIO.setmode(GPIO.BCM)

# set GPIO Pins
pinTrigger = 18
pinEcho = 24


def closeGPIO(signal, frame):
    GPIO.cleanup() 
    sys.exit(0)


def getDistance():
    signal.signal(signal.SIGINT, closeGPIO)

    # set GPIO input and output channels
    GPIO.setup(pinTrigger, GPIO.OUT)
    GPIO.setup(pinEcho, GPIO.IN)

    # set Trigger to HIGH
    GPIO.output(pinTrigger, True)
    # set Trigger after 0.01ms to LOW
    time.sleep(0.00001)
    GPIO.output(pinTrigger, False)

    startTime = time.time()
    stopTime = time.time()

    # save start time
    while 0 == GPIO.input(pinEcho):
        startTime = time.time()

    # save time of arrival
    while 1 == GPIO.input(pinEcho):
        stopTime = time.time()

    # time difference between start and arrival
    TimeElapsed = stopTime - startTime
    # multiply with the sonic speed (34300 cm/s)
    # and divide by 2, because there and back
    distance = (TimeElapsed * 34300) / 2

    # print ("Distance: %.1f cm" % distance)
    return distance
    

def getVideo():
    p1 = subprocess.Popen(['raspivid', '-o', 'liveframe.h264', '-t', '2000'], stdout=subprocess.PIPE)
    p1.wait()
    p2 = subprocess.Popen(['MP4Box', '-add', 'liveframe.h264', 'liveframe.mp4'], stdout=subprocess.PIPE)
    p2.wait()


def getStill():
    p1 = subprocess.Popen(['raspistill', '-w', '1280', '-h', '720', '-o', 'stillframe.jpg'], stdout=subprocess.PIPE)
    p1.wait()


def getTopObject(objects):
    return objects[0]


def say(words):
    tempfile = "temp.wav"
    devnull = open("/dev/null","w")
    subprocess.call(["pico2wave", "-w", tempfile, words],stderr=devnull)
    subprocess.call(["aplay", tempfile],stderr=devnull)
    #os.remove(tempfile)


if __name__ == "__main__":
    while True:
        initDistance = getDistance()
        print ("Distance: %.1f cm" % initDistance)

        if (initDistance <= 100000):
            getStill()

            # call web API for image recognition
            visual_recognition = VisualRecognitionV3('2016-05-20', api_key='c2d666595f0b356d897ae80347d1d6977aa5072d')
            results = None
            with open('stillframe.jpg', 'rb') as images_file:
                json_obj = visual_recognition.classify(images_file=images_file)
                results = str(json.dumps(json_obj, indent=2))

            # print(results)
            label = '"class"'
            i = results.find(label)
            classes = []
            while i != -1:
                j = i + len(label)
                start = -1
                end = -1
                while j < len(results):
                    if (results[j] == '"' and start == -1):
                        start = j
                    elif (results[j] == '"' and end == -1):
                        end = j
                        classes += [results[start+1:end]]
                        break
                    j += 1
                i = results.find(label, i+1)

            # print(classes)
            s = ""
            for k in classes:
                nlu = watson_developer_cloud.NaturalLanguageUnderstandingV1(version='2017-02-27',
                                                                username='2360a5c2-5be8-4338-8635-5e93049503f4',
                                                                password='hvHuCBrve2iT')
                h = nlu.analyze(text=k, features=[features.Entities()])

            names = []
            if "person" in classes:
                face_results = None
                with open('stillframe.jpg', 'rb') as images_file2:
                    json_obj2 = visual_recognition.detect_faces(images_file=images_file2)
                    face_results = str(json.dumps(json_obj2, indent=2))
                    
                    name = '"name"'
                    i2 = results.find(name)
                    while i2 != -1:
                        j2 = i2 + len(label)
                        start2 = -1
                        end2 = -1
                        while j2 < len(face_results):
                            if (face_results[j2] == '"' and start2 == -1):
                                start2 = j2
                            elif (face_results[j2] == '"' and end2 == -1):
                                end2 = j2
                                names += [face_results[start2+1:end2]]
                                break
                            j2 += 1
                        i2 = face_results.find(name, i2+1)
            
            
            topObject = classes[0]
            # prioritized classes
            if ("person" in classes):
                topObject = "person"
            elif ("orange" in classes):
                topObject = "orange"
            elif ("chair" in classes):
                topObject = "chair"
            elif ("table" in classes):
                topObject = "table"
            elif ("wall" in classes):
                topObject = "wall"
            elif ("curtains" in classes):
                topObject = "curtains"


            # output text-to-speech
            topObject = classes[0]
            distance = getDistance()
            if (distance < 100):
                if len(names) > 1:
                    topObject = names[0]
                    message = "%s is around %d centimeters away" % (topObject, distance)
                else:
                    message = "A %s is around %d centimeters away" % (topObject, distance)
            else:
                if len(names) > 1:
                    topObject = names[0]
                    message = "%s is around %d meters away" % (topObject, distance/100)
                else:
                    message = "A %s is around %d meters away" % (topObject, distance/100)
            
            say(message)
            print(message)
        #time.sleep(1)



