import csv
list1=[]
list2=[]

writefile = open("box_log_paired.json", "w")

def stripNull(s):
	toReturn=''
	for letter in s:
		if ord(letter)!=0:
			toReturn+=letter
	return toReturn

def hasNull(s):
	toReturn=0
	for letter in s:
		if ord(letter)==0:
			toReturn=1
	return toReturn
def getASCIICodes(s):
	toReturn=''
	for letter in s:
		toReturn+=str(ord(letter))
		toReturn+=','
	return toReturn

class AuthorEntry(object):
    def __init__(self, name, index_and_count=None):
        if index_and_count is None:
            self.index_and_count = []
        else:
             self.index_and_count = index_and_count 
        self.name = name
        
        

with open('box_log_both.csv', 'rU') as csvfile:
	reader = csv.reader(csvfile, delimiter=',', quotechar='|')
	for index1, row1 in enumerate(reader):
		list1.append(row1)
		#list2.append(row1)


print list1