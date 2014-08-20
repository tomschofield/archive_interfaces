# -*- coding: utf-8 -*-
import codecs
import sys
import nltk
import string
import os
import unicodedata
from sklearn.feature_extraction.text import TfidfVectorizer
from nltk.stem.porter import PorterStemmer
from nltk.corpus import stopwords
import chardet

path = '/Users/cmdadmin/Dropbox/PYTHON/Ahren_entity_extraction/PDF_text/'
#path = '/Users/cmdadmin/Dropbox/PYTHON/NLTK/tfidf/texts/'

def stem_tokens(tokens, stemmer):
    stemmed = []
    for item in tokens:
        stemmed.append(stemmer.stem(item))
    return stemmed

def tokenize(text):
    tokens = nltk.word_tokenize(text)
    stems = stem_tokens(tokens, stemmer)
    return stems

gText = ""

validPOS = [
'FW',
'JJ',
'JJR',
'JJS',
'NN',
'NNP',
'NNPS',
'NNS',
'RB',
'RBR']
# UTF8Writer = codecs.getwriter('utf8')
# sys.stdout = UTF8Writer(sys.stdout)

def filterPos(text):
	print nltk.pos_tag(text)

#print filterPos(tokenize('the quick brown fox jumped over the lazy dog'))

for subdir, dirs, files in os.walk(path):
    for file in files:
    	token_dict = {}
    	stemmer = PorterStemmer()

    	print '*****************************',file
    	if file!='.DS_Store':
			file_path = subdir + os.path.sep + file
			shakes = open(file_path, 'r')
			# unicode('\x80abc', errors='replace')
			# unicode('\xCBabc', errors='replace')

			text = unicode('\xFF'+shakes.read(), errors='replace') #unicode(shakes.read())
			#print type(text)
			print file
			sText = unicodedata.normalize('NFKD', text).encode('ascii','ignore')
			gText = sText
			lowers = sText.lower()
			print text #filterPos(tokenize(text))
			no_punctuation = lowers.translate(None, string.punctuation)
			#print no_punctuation
			exploded = no_punctuation.split(' ')
			filtered = [w for w in exploded if not w in stopwords.words('english')]
			no_stop_words = ""
			#print type(filtered)
			for item in filtered:
				
				no_stop_words+=item
				no_stop_words+=" "
			#print no_stop_words

			token_dict[file] = no_stop_words #no_stop_words
			#print token_dict.values()
			#this can take some time
			tfidf = TfidfVectorizer(tokenizer=tokenize, stop_words='english')

			tfs = tfidf.fit_transform(token_dict.values())
			feature_names = tfidf.get_feature_names()
			unsortedList = []
			for col in tfs.nonzero()[1]:
				#print feature_names[col],' ',feature_names[col].__len__(), ' - ', tfs[0, col]
				inVar  = feature_names[col]
				
				singleItem = (inVar.encode('utf-8'),tfs[0, col])
				unsortedList.append(singleItem)
			#print unsortedList
				sorted_list = sorted(unsortedList, key=lambda item: item[1], reverse=True)
				log = open('/Users/cmdadmin/Dropbox/PYTHON/NLTK/tfidf/results/tfidf_results_of_'+file, 'w+')

				for line in sorted_list:
					#print line
					word = str(line[0])
					#print word.__len__()
					if word.__len__()>7:
						log.write(str(line[0]))
						log.write(", ")
						log.write(str(line[1]))
						log.write("\n")

				log.close()
# #print tfs
# str = gText
# response = tfidf.transform([str])



# try:
# 				    inVar.decode('UTF-8')
# 				    print "string is UTF-81, length %d bytes" % len(inVar)
# 				except UnicodeError:
# 				    print "string is not UTF-81"
# 				try:
# 				    inVar.decode('ascii')
# 				    print "string is ascii, length %d bytes" % len(inVar)
# 				except UnicodeError:
# 				    print "string is not ascii"
# 				try:
# 				    inVar.decode('utf-16')
# 				    print "string is UTF-16, length %d bytes" % len(inVar)
# 				except UnicodeError:
# 				    print "string is not UTF-816"

