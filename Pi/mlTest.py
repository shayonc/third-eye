import sys
import os
sys.path.append(os.path.join(os.getcwd(),'..'))
import watson_developer_cloud
import watson_developer_cloud.natural_language_understanding.features.v1 as features


nlu = watson_developer_cloud.NaturalLanguageUnderstandingV1(version='2017-02-27',
                                                            username='username',
                                                            password='password')
h = nlu.analyze(text='apple',
            features=[features.Entities(), features.Keywords()])
print(h)
