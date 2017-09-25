# Third-Eye
Third-Eye helps visually impaired people be better informed about their surroundings.
This project won the Hack the North 2017 IBM Watson API prize for using Watson's computer vision and natural language underestanding APIs. Here is our devpost page: https://devpost.com/software/third-eye-1jgh52 

## Inspiration
One in every 37 people in Canada have visual impairments of some kind. We want to help them overcome their visual barriers when they interact with their surroundings.  

## Functionalities 
- Third-Eye captures images and recognizes things in them using the Watson Visual Recognition API. 
- If there is a person within an image, facial recognition is performed to recognize any known people. For now, only faces of famous people that are contained in Watson's database can be recognized.
- For the list of things that are recognized within the images, the Watson Natural Language understanding API is used to find out which of these things are "entities". Entities are physical objects that the user would want to know about.
- Using a distance sensor, Third-Eye can detect how far these physical objects are from the user.
- Third-Eye tells the user using a text-to-speech application about the objects/people in front of him/her, and how far away it is. For people, Third-Eye says their names to the user.

## How we built it
We use the Raspberry Pi, a Pi Camera module, a distance sensor, and wireless microphone for the purpose of feeding live video feed for computer vision analysis, object classification, distance/potential obstacle analysis, and facial recognition. Watson's Visual Recognition and Natural Language Understanding APIs were used.

## What's next
We want to incorporate more learning algorithms and training to continue to improve recognition of objects and facial features of people.
