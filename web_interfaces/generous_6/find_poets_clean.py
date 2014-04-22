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
        
        


def jsonifyPairList(pairList):
	#open the object
	myAuthors =[]
	
	for apair in pairList:
		explodedPair = apair.split(':')
		exists = 0
		for anAuthor in myAuthors:
			if anAuthor.name == explodedPair[0]:
				exists=1
				indexExists=0
				indexPos =0
				for anindex, indexPair in enumerate(anAuthor.index_and_count):
					broken = indexPair.split(':')
					if broken[0] == explodedPair[1]:
						indexExists =1
						indexPos=anindex
				if indexExists == 0:
					newPair = explodedPair[1]
					newPair+=':'
					newPair+='1'
					anAuthor.index_and_count.append(newPair)
				else:
					splitPair = anAuthor.index_and_count[indexPos].split(':')
					lastCount = int(splitPair[1])
					#print 'lastCount ',lastCount
					lastCount+=1
					newPair = splitPair[0]
					newPair+=':'
					newPair+=str(lastCount)
					anAuthor.index_and_count[indexPos] = newPair
					#print anAuthor.name,' ',newPair


		if exists==0:
			tempArray =[]
			newPair = explodedPair[1]
			newPair+=':'
			newPair+='1'
			tempArray.append(newPair)
			myAuthors.append(AuthorEntry(explodedPair[0], tempArray))

		#check if it exists in the json
		#print 'test'
	myjson='['
	for firstIndex, a in enumerate(myAuthors):
		if firstIndex>0:
			myjson+=','
		myjson+='{'
		myjson+='"'
		myjson+=a.name
		myjson+='"'
		myjson+=':'
		myjson+='['
		for anIndex, thisPair in enumerate(a.index_and_count):
			if(anIndex>0):
				myjson+=','
			myjson+='{'
			asplitPair = thisPair.split(':')
			myjson+='"'
			myjson+=asplitPair[0]
			myjson+='"'
			myjson+=':'
			myjson+='"'
			myjson+=asplitPair[1]
			myjson+='"'


			#myjson+=thisPair
			myjson+='}'
		myjson+=']'
		myjson+='}'
		
	myjson+=']'
		#print a.name,' ',a.index_and_count[0]
	print myjson
	return myjson

with open('box_log_names.csv', 'rU') as csvfile:
	reader = csv.reader(csvfile, delimiter=',', quotechar='|')
	for index1, row1 in enumerate(reader):
		list1.append(row1)
		list2.append(row1)
		
for index1, row1 in enumerate(list1):
	list3=[]
	list3 [:]= []
	entry=''
	if index1>0:
		writefile.write('[{"boxref":')
		try:
			writefile.write('"')
			writefile.write(row1[0])
			writefile.write('"')
		except:
			writefile.write('"nodata"')

		writefile.write('},')

		writefile.write('{"labels":')
		try:
			writefile.write('"')
			writefile.write(row1[1])
			writefile.write('"')
		except:
			writefile.write('"nodata"')

		writefile.write('},')
		
		writefile.write('{"accessed_by":')
		try:
			writefile.write('"')
			writefile.write(row1[2])
			writefile.write('"')
		except:
			writefile.write('"nodata"')

		writefile.write('},')


		writefile.write('{"summary_contents":')
		try:
			writefile.write('"')
			writefile.write(row1[3])
			writefile.write('"')
		except:
			writefile.write('"nodata"')

		writefile.write('},')

		for index2, row2 in enumerate(list2):
			exploded1 = row1 [3].split(';')
			exploded2 = row2 [3].split(';')
			
			#print index2
			for word1 in exploded1:
				for word2 in exploded2:
					exploded3 = word1.split(' ')
					exploded4 = word2.split(' ')
					lastWord1 = exploded3[len(exploded3)-1].strip().lower()
					lastWord2 = exploded4[len(exploded4)-1].strip().lower()
					if("".join(word1.split()).__len__()>3):
						if word1==word2 and index1!=index2 and lastWord1.__len__()>3 and lastWord2.__len__()>3:
							#list3.append(word1)
							#list3.append(':')
							#list3.append(word1+':'+str(index2))
							#list3.append(';')
							entry+=word1.strip()
							entry+=':'
							entry+=str(index2)
							entry+=';'
						elif lastWord1==lastWord2 and lastWord1.__len__()>3 and lastWord2.__len__()>3 and lastWord1!='poems' and lastWord1!='poets' and lastWord2!='poets' and lastWord1!='sheets' and lastWord2!='poems' and lastWord1!='poetry' and lastWord2!='poetry' and lastWord2!='sheets' and index1!=index2:
							#print "match at ",index1,' and ', index2, ' word is ',word1,' compared to ',word2
							#list3.append(word1)
							#list3.append(word1+':'+str(index2))
							#list3.append(':')
							#list3.append(str(index2))
							#list3.append(';')
							entry+=word1.strip()
							entry+=':'
							entry+=str(index2)
							entry+=';'
		
		list3 = entry.split(';')
		list4=[]
		list5=[]
		unrepeated = ''
		for index3, mypair1 in enumerate(list3):
			#print ' pair at ', index1,':',index3,' is ',mypair1
			if mypair1.__len__()>4:
				list4.append(mypair1)
				list5.append(mypair1)
			#else:
				#print 'dead pair at ', index1,':',index3,' is ',mypair1
		#print index1, '  ',len(list3), '  ',len(list5)
		list6=[]
		list4.sort()
		list5.sort()
		#run through list and check if item exists to remove duplicates
		print 'writing list ',index1
		
		

		
		#print unrepeated
		writefile.write(jsonifyPairList(list4))
		writefile.write(',')
		writefile.write('{"dates_covered":')
		try:
			writefile.write('"')
			writefile.write(row1[4])
			writefile.write('"')
		except:
			writefile.write('"nodata"')


		writefile.write('}]\n')

writefile.close()
print 'file written'