import  sys
from lxml import etree
from lxml import objectify
import time


def loadXML():
	tree = etree.parse('edit.xml')
	root = tree.getroot()

	print(root.tag)

	archdesc = tree.findall('archdesc')
	for elem in archdesc:
		dsc = elem.findall('dsc')
		#print len(dsc)
		for a in dsc:
			c01 = a.findall('c01')
			b = c01[0]
			c02 = b.findall('c02')
			c03 = c02[0].findall('c03')
			for item in c03:
				#print item.tag
				print item.find('did').find('unittitle').text," ",item.find('did').find('unitid').text," ",item.find('did').find('unitdate').text

# /*
# c02 is sub of editorial eg published poetry by aythor, anthologies, critisim etc
# (in c02) 
# 	c03 is author
# 	c04 is file of stuff
# 	c05 is an item in a file (scan level)

# confusingly both c03 and c04 are 'file' level
# */
def getInfoForAuthor(searchAuthor):
	tree = etree.parse('edit.xml')
	root = tree.getroot()

	thisAuthor =''
	thisBio =''
	thisId = ''
	thisDate = ''
	thisScopecontent = ''
	thisFileList=[]
	collect = 0
	authorName ='no author found'
	archdesc = tree.findall('archdesc')
	for elem in archdesc:
		dsc = elem.findall('dsc')
		#print len(dsc)
		for a in dsc:
			c01 = a.findall('c01')
			b = c01[0]
			c02 = b.findall('c02')
			c03 = c02[0].findall('c03')
			for author in c03:
				#we are now at author level
				#print author.find('did').find('unittitle').text," ",author.find('did').find('unitid').text," ",author.find('did').find('unitdate').text
				if author.find('did').find('unittitle').text == searchAuthor:
					collect = 1
					thisAuthor = author.find('did').find('unittitle').text
				else:
					collect = 0
				c04 = author.findall('c04')
				if collect == 1:
					try:
						thisId = author.find('did').find('unitid').text
						thisDate = author.find('did').find('unitdate').text
						thisBio =  author.find('bioghist').find('p').text
						thisScopecontent =  author.find('scopecontent').find('p').text
					except:
						print 'problem'

				for folder in c04:
					#we are now at file level - called folder because 'file' is reserved
					#print '	',folder.find('did').find('unittitle').text," ",folder.find('did').find('unitid').text," ",folder.find('did').find('unitdate').text
					c05 = folder.findall('c05')
					if collect == 1:
						aFile = {
							'title': folder.find('did').find('unittitle').text, 
							'id': folder.find('did').find('unitid').text,
							'date': folder.find('did').find('unitdate').text
						}
						thisFileList.append(aFile)

					#for item in c05:
						#print '		',item.find('did').find('unittitle').text," ",item.find('did').find('unitid').text," ",item.find('did').find('unitdate').text
						#if item.find('did').find('unitid').text == itemID:
	aRecord = {
		'author': thisAuthor, 
		'bio': thisBio,
		'id': thisId,
		'date': thisDate,
		'scopecontent': thisScopecontent,
		'fileList': thisFileList
	}

	return aRecord

def getAuthorForItem(itemID):
	tree = etree.parse('edit.xml')
	root = tree.getroot()

	#print(root.tag)
	authorName ='no author found'
	archdesc = tree.findall('archdesc')
	for elem in archdesc:
		dsc = elem.findall('dsc')
		#print len(dsc)
		for a in dsc:
			c01 = a.findall('c01')
			b = c01[0]
			c02 = b.findall('c02')
			c03 = c02[0].findall('c03')
			for author in c03:
				#we are now at author level
				#print author.find('did').find('unittitle').text," ",author.find('did').find('unitid').text," ",author.find('did').find('unitdate').text
				c04 = author.findall('c04')

				for folder in c04:
					#we are now at file level - called folder because 'file' is reserved
					#print '	',folder.find('did').find('unittitle').text," ",folder.find('did').find('unitid').text," ",folder.find('did').find('unitdate').text
					c05 = folder.findall('c05')
					for item in c05:
						#print '		',item.find('did').find('unittitle').text," ",item.find('did').find('unitid').text," ",item.find('did').find('unitdate').text
						if item.find('did').find('unitid').text == itemID:
							authorName = author.find('did').find('unittitle').text
	return authorName

def getItemRefFromFileName(itemRef):
	parts = itemRef.split('.')
	ref = parts[0].replace('-','/')
	return ref
id = 'BXB-1-1-ADC-1-2.pdf'
#print getAuthorForItem(id);
#loadXML()
print getItemRefFromFileName(id)
print getAuthorForItem('BXB/1/1/ADC/1/2');
#print getInfoForAuthor('Fleur Adcock')


