import re

results = 'adsf"class" : "person" asdfasdkf"class" : "dog"'

print(results[42:46])

label = '"class"'
i = results.find(label)
print(i)
classes = []
while i != -1:
    j = i + len(label)
    start = -1
    end = -1
    while j < len(results):
        if (results[j] == '"' and start == -1):
            start = j
            print("start: %d" % start)
        elif (results[j] == '"' and end == -1):
            end = j
            print("end: %d" % end)
            break
        j += 1
        print("start: %d" % start)
        print("end: %d" % end)
        print (results[(start+1):end])
    i = results.find(label, i+1)
    print(i)
print(str(classes))
