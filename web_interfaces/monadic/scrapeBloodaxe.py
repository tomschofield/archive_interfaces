
# -*- coding: UTF-8 -*-
import json
import httplib2
import urllib2
import re
from bs4 import BeautifulSoup
#all possible subjets
sourceSubjectList = ["21st Century Poets" , "Academic" , "African Poets" , "American Poets" , "Animal Poetry" , "Anthologies" , "Arab Poets" , "Art/History Interest" , "Asian Poets" , "Australian Poets" , "Avantgarde/Experimental" , "Bengali" , "Bilingual Editions" , "Caribbean/Black British" , "Catalan" , "Children's Poetry" , "Chinese" , "Classical Literature" , "Czech" , "Danish" , "Death and Bereavement" , "Drama" , "Fiction" , "Finland Swedish" , "Finnish" , "First Collections" , "French" , "Gay and Lesbian Interest" , "German" , "Greek (Ancient)" , "Humorous" , "Hungarian" , "International Anthology" , "Irish Poets" , "Italian" , "Latin (Classical)" , "Latin (Medieval)" , "Literary Criticism" , "Love Poetry" , "Mainstream" , "Modern Classic" , "Modernist Poetry" , "Mythology Interest" , "Nature/Environment" , "Old Babylionian" , "Performance Poetry" , "Philosophical Interest" , "Poets of the 1980/90s" , "Polish" , "Political/Social" , "Religious/Spiritual" , "Romanian" , "Russian" , "Science Interest" , "Scottish Poets" , "Serbo-Croat/Bosnian" , "Spanish" , "Spoken Word" , "Swedish" , "Translations" , "Welsh" , "Welsh Poets" , "Women"]
#all possible authors
authorList = []
authorList.append("Robert+Adamson")
authorList.append("Fleur+Adcock")
authorList.append("John+Agard")
authorList.append("Moniza+Alvi")
authorList.append("Elizabeth+Alexander")
authorList.append("Taha+Muhammad+Ali")
authorList.append("Gillian+Allnutt")
authorList.append("Maram+al-Massri")
authorList.append("Simon+Armitage")
authorList.append("Neil+Astley")
authorList.append("Tiffany+Atkinson")
authorList.append("Attila+the+Stockbroker")
authorList.append("Annemarie+Austin")
authorList.append("Josephine+Balmer")
authorList.append("Elizabeth+Bartlett")
authorList.append("Paul+Batchelor")
authorList.append("Suzanne+Batty")
authorList.append("Martin+Bell")
authorList.append("Connie+Bensley")
authorList.append("Sara+Berkeley")
authorList.append("James+Berry")
authorList.append("Elizabeth+Bishop")
authorList.append("Robyn+Bolam")
authorList.append("Martin+Booth")
authorList.append("Karin+Boye")
authorList.append("Patrick+Brandon")
authorList.append("Kamau+Brathwaite")
authorList.append("Fran+Brearton")
authorList.append("Jean+'Binta'+Breeze")
authorList.append("Zoë+Brigley")
authorList.append("Eleanor+Brown")
authorList.append("Basil+Bunting")
authorList.append("Matthew+Caley")
authorList.append("Ciaran+Carson")
authorList.append("Martin+Carter")
authorList.append("Dan+Chiasson")
authorList.append("Polly+Clark")
authorList.append("Brendan+Cleary")
authorList.append("Jack+Clemo")
authorList.append("Harry+Clifton")
authorList.append("Stewart+Conn")
authorList.append("David+Constantine")
authorList.append("Jane+Cooper")
authorList.append("Julia+Copus")
authorList.append("Jeni+Couzyn")
authorList.append("Hart+Crane")
authorList.append("Fred+D'+Aguiar")
authorList.append("Amanda+Dalton")
authorList.append("Ailbhe+Darcy")
authorList.append("Imtiaz+Dharker")
authorList.append("Peter+Didsbury")
authorList.append("Stephen+Dobyns")
authorList.append("Katie+Donovan")
authorList.append("Maura+Dooley")
authorList.append("Tishani+Doshi")
authorList.append("Freda+Downie")
authorList.append("Nick+Drake")
authorList.append("Ian+Duhig")
authorList.append("Helen+Dunmore")
authorList.append("Douglas+Dunn")
authorList.append("G.+F.+Dutton")
authorList.append("Lauris+Edmond")
authorList.append("Menna+Elfyn")
authorList.append("Ruth+Fainlight")
authorList.append("Helen+Farish")
authorList.append("Gillian+Ferguson")
authorList.append("Roy+Fisher")
authorList.append("Tony+Flynn")
authorList.append("Cheryl+Follon")
authorList.append("Carolyn+Forché")
authorList.append("Janet+Frame")
authorList.append("Linda+France")
authorList.append("Tess+Gallagher")
authorList.append("Miriam+Gamble")
authorList.append("Roger+Garfitt")
authorList.append("Elizabeth+Garrett")
authorList.append("Deborah+Garrison")
authorList.append("Jack+Gilbert")
authorList.append("Pamela+Gillilan")
authorList.append("Chris+Greenhalgh")
authorList.append("John+Greening")
authorList.append("Andrew+Greig")
authorList.append("Jane+Griffiths")
authorList.append("Philip+Gross")
authorList.append("Jen+Hadfield")
authorList.append("Maggie+Hannan")
authorList.append("Choman+Hardi")
authorList.append("Kerry+Hardie")
authorList.append("Jackie+Hardy")
authorList.append("Tony+Harrison")
authorList.append("J.+S.+Harry")
authorList.append("Kevin+Hart")
authorList.append("Robert+Hass")
authorList.append("Geoff+Hattersley")
authorList.append("Adrian+Henri")
authorList.append("W.+N.+Herbert")
authorList.append("Tracey+Herd")
authorList.append("Dorothy+Hewett")
authorList.append("Rita+Ann+Higgins")
authorList.append("Selima+Hill")
authorList.append("Ellen+Hinsey")
authorList.append("Jane+Hirshfield")
authorList.append("Tony+Hoagland")
authorList.append("Jane+Holland")
authorList.append("Matthew+Hollis")
authorList.append("Frances+Horovitz")
authorList.append("John+Hughes")
authorList.append("Frieda+Hughes")
authorList.append("Paul+Hyland")
authorList.append("Helen+Ivory")
authorList.append("Sarah+Jackson")
authorList.append("Kathleen+Jamie")
authorList.append("Linton+Kwesi+Johnson")
authorList.append("Jenny+Joseph")
authorList.append("Sylvia+Kantaris")
authorList.append("Kapka+Kassabova")
authorList.append("Jackie+Kay")
authorList.append("Garrison+Keillor")
authorList.append("Brendan+Kennelly")
authorList.append("Jane+Kenyon")
authorList.append("Galway+Kinnell")
authorList.append("John+Kinsella")
authorList.append("Helen+Kitson")
authorList.append("Stephen+Knight")
authorList.append("Arun+Kolatkar")
authorList.append("Jean+Hanff+Korelitz")
authorList.append("Li-Young+Lee")
authorList.append("Denise+Levertov")
authorList.append("Philip+Levine")
authorList.append("Gwyneth+Lewis")
authorList.append("Yang+Lian")
authorList.append("Joanne+Limburg")
authorList.append("Jackie+Litherland")
authorList.append("Marion+Lomax")
authorList.append("Edna+Longley")
authorList.append("Hannah+Lowe")
authorList.append("Roddy+Lumsden")
authorList.append("Mairi+MacInnes")
authorList.append("Kona+Macphee")
authorList.append("Barry+MacSweeney")
authorList.append("Jennifer+Maiden")
authorList.append("Maitreyabandhu")
authorList.append("Gerald+Mangan")
authorList.append("Jack+Mapanje")
authorList.append("Don+Marquis")
authorList.append("William+Martin")
authorList.append("Harry+Martinson")
authorList.append("Jill+Maughan")
authorList.append("Glyn+Maxwell")
authorList.append("Medbh+McGuckian")
authorList.append("W.+S.+Merwin")
authorList.append("Adrian+Mitchell")
authorList.append("John+Montague")
authorList.append("Esther+Morgan")
authorList.append("Vincent+Morrison")
authorList.append("Richard+Murphy")
authorList.append("Grace+Nichols")
authorList.append("Stephanie+Norgate")
authorList.append("Henry+Normal")
authorList.append("Alden+Nowlan")
authorList.append("Naomi+Shihab+Nye")
authorList.append("Sean+O'Brien")
authorList.append("Julie+O’Callaghan")
authorList.append("Caitríona+O’Reilly")
authorList.append("Micheal+O’Siadhail")
authorList.append("Leanne+O'+Sullivan")
authorList.append("John+Oldham")
authorList.append("Douglas+Oliver")
authorList.append("Mary+Oliver")
authorList.append("Ottó+Orbán")
authorList.append("Ruth+Padel")
authorList.append("Heather+Phillipson")
authorList.append("Tom+Pickard")
authorList.append("Clare+Pollard")
authorList.append("Katrina+Porteous")
authorList.append("Jem+Poster")
authorList.append("Kate+Potts")
authorList.append("Tom+Pow")
authorList.append("J.+H.+Prynne")
authorList.append("Deborah+Randall")
authorList.append("Sally+Read")
authorList.append("Peter+Reading")
authorList.append("Anne+Rouse")
authorList.append("Carol+Rumens")
authorList.append("Gig+Ryan")
authorList.append("Tracy+Ryan")
authorList.append("Lawrence+Sail")
authorList.append("Eva+Salzman")
authorList.append("Jacob+Sam-La+Rose")
authorList.append("Fiona+Sampson")
authorList.append("Ann+Sansom")
authorList.append("Carole+Satyamurti")
authorList.append("Gjertrud+Schnackenberg")
authorList.append("David+Scott")
authorList.append("John+Sears")
authorList.append("Olive+Senior")
authorList.append("Jo+Shapcott")
authorList.append("Clare+Shaw")
authorList.append("Penelope+Shuttle")
authorList.append("James+Simmons")
authorList.append("Matt+Simpson")
authorList.append("Louis+Simpson")
authorList.append("Lemn+Sissay")
authorList.append("Ken+Smith")
authorList.append("Esta+Spalding")
authorList.append("Bernard+Spencer")
authorList.append("Pauline+Stainer")
authorList.append("Anne+Stevenson")
authorList.append("Ruth+Stone")
authorList.append("Arundhathi+Subramaniam")
authorList.append("Matthew+Sweeney")
authorList.append("George+Szirtes")
authorList.append("A.+S.+J.+Tessimond")
authorList.append("D.+M.+Thomas")
authorList.append("Edward+Thomas")
authorList.append("R.+S.+Thomas")
authorList.append("Brian+Turner")
authorList.append("Chase+Twichell")
authorList.append("Priscila+Uppal")
authorList.append("Fred+Voss")
authorList.append("Sarah+Wardle")
authorList.append("Ahren+Warner")
authorList.append("Alan+Wearne")
authorList.append("Nigel+Wells")
authorList.append("Christiana+Whitehead")
authorList.append("Susan+Wicks")
authorList.append("C.+K.+Williams")
authorList.append("John+Hartley+Williams")
authorList.append("Heidi+Williamson")
authorList.append("C.+D.+Wright")
authorList.append("James+Wright")
authorList.append("Robert+Wrigley")
authorList.append("Benjamin+Zephaniah")



authorAndSubjects = {}
#for each author
for authorname in authorList:
	print authorname 
	try:
		base_url='http://www.bloodaxebooks.com/personpage.asp?author='
		#query site with this custom url
		full_query=base_url+authorname

		
		http = httplib2.Http()
		status, response = http.request(full_query)
		#make the soup
		soup = BeautifulSoup(response)
		#print soup
		count = 0

		subjectList = []
		#subjects exist in a class called blurb
		for blurbItem in soup.findAll("a", "blurb"):
			#print "item ",blurbItem
			href = blurbItem['href']
			exploded = href.split("=")
			
			if exploded[0]=='subjectpage.asp?subject':
				subjectList.append(str(blurbItem.contents[0]))
		#print subjectList
		authorAndSubjects[authorname]=subjectList
	except:
		print 'couldnt get page for ',authorname 

#make a long list of all the subjects
subjectObjects = []
for anIndex, subject in enumerate(sourceSubjectList):
	#print subject
	indices = []
	#check for each author if they have this subject and add the index of that author if they do
	for index, author in enumerate(authorList):
		for authorsSubject in authorAndSubjects[author]:
			#print authorsSubject
			if subject==authorsSubject:
				print author, ' has ', subject, ' at ', index
				indices.append(index+10000)
	subjectObject = {}
	subjectObject["id"]=30000+anIndex;
	subjectObject["type"]=2;
	subjectObject["title"]=subject;
	subjectObject["text"]="";
	subjectObject["links"]=indices;
	subjectObjects.append(subjectObject)

#print json.dumps(subjectObjects)
myjson = json.dumps(subjectObjects)
f = open('monadic_python.json', 'w')
f.write(myjson)
f.close()



