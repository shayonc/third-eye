import json
from os.path import join, dirname
from watson_developer_cloud import VisualRecognitionV3


visual_recognition = VisualRecognitionV3('2016-05-20', api_key='key')

# test_url = 'https://www.ibm.com/ibm/ginni/images' \
#            '/ginni_bio_780x981_v4_03162016.jpg'

# url_result = visual_recognition.classify(images_url=test_url)
# print(json.dumps(url_result, indent=2))

with open('prez.jpg', 'rb') as images_file:
    results = visual_recognition.detect_faces(images_file=images_file)
    print(json.dumps(results, indent=2))
