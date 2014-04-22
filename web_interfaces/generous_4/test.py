import os
fname = 'BloodAxeDB.csv'

#lines = [line.strip() for line in open('BloodAxeDB.csv')]

with open(fname) as f:
    lines = f.readlines()

numEntries = len(lines)-1;

#for x in lines:
#	print '/////////////////////a line : ',x

print 'numEntries : ',numEntries

path = os.getcwd()

path+='/BloodAxeBooks'

print path

images = os.listdir(path)
print len(images)

def checkImageExists(imageName, listOfImageNames):
	return imageName in listOfImageNames

print checkImageExists('BXBaaa821-ADCVIR-01.jpg', images)

def countMostInOneYear():
	for y in lines:
		exploded = y.split(',')
		#print exploded[20]

#def countMostInOneYear():
