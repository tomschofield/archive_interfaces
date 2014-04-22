import sys
import os
from os import walk
import os.path

print 'path is : ',sys.argv[1]
from email.mime.text import MIMEText
from datetime import date
import smtplib
import tweepy
import socket
import random
import urllib2
import datetime
from lxml import etree
from lxml import objectify
import time

HOST = "10.67.33.160"
PORT = 80

s = socket.socket( socket.AF_INET, socket.SOCK_STREAM )
s.connect((HOST, PORT))

consumer_key="x"
consumer_secret="x"
access_key = "x-x"
access_secret = "x" 

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_key, access_secret)
api = tweepy.API(auth)

#fname = sys.argv[1]
SMTP_SERVER = "smtp.gmail.com"
SMTP_PORT = 587
SMTP_USERNAME = "tomschofieldart@gmail.com"
SMTP_PASSWORD = "x"

EMAIL_TO = ["tom.schofield@ncl.ac.uk"]
EMAIL_FROM = "tomschofieldart@gmail.com"
EMAIL_SUBJECT = "new files in archive : "

DATE_FORMAT = "%d/%m/%Y"
EMAIL_SPACE = ", "
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
#   c03 is author
#   c04 is file of stuff
#   c05 is an item in a file (scan level)

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
                    #print '    ',folder.find('did').find('unittitle').text," ",folder.find('did').find('unitid').text," ",folder.find('did').find('unitdate').text
                    c05 = folder.findall('c05')
                    if collect == 1:
                        aFile = {
                            'title': folder.find('did').find('unittitle').text, 
                            'id': folder.find('did').find('unitid').text,
                            'date': folder.find('did').find('unitdate').text
                        }
                        thisFileList.append(aFile)

                    #for item in c05:
                        #print '        ',item.find('did').find('unittitle').text," ",item.find('did').find('unitid').text," ",item.find('did').find('unitdate').text
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
                    #print '    ',folder.find('did').find('unittitle').text," ",folder.find('did').find('unitid').text," ",folder.find('did').find('unitdate').text
                    c05 = folder.findall('c05')
                    for item in c05:
                        #print '        ',item.find('did').find('unittitle').text," ",item.find('did').find('unitid').text," ",item.find('did').find('unitdate').text
                        if item.find('did').find('unitid').text == itemID:
                            authorName = author.find('did').find('unittitle').text
    return authorName

def send_email(DATA):
    msg = MIMEText(DATA)
    msg['Subject'] = EMAIL_SUBJECT + " %s" % (date.today().strftime(DATE_FORMAT))
    msg['To'] = EMAIL_SPACE.join(EMAIL_TO)
    msg['From'] = EMAIL_FROM
    mail = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)
    mail.starttls()
    mail.login(SMTP_USERNAME, SMTP_PASSWORD)
    mail.sendmail(EMAIL_FROM, EMAIL_TO, msg.as_string())
    mail.quit()

def getSavedListOffiles(fname):
    f = open(fname, 'r+')
    files = []
    for line in f:
        #print line,
        exploded  =  line.split('.')
        if len(exploded)>0:
            if exploded[len(exploded)-1]=='jpg\n' or exploded[len(exploded)-1]=='tif\n' or exploded[len(exploded)-1]=='pdf\n':
                #print 'file ',fname
                files.append(line.rstrip('\n'))
    return files

def listfiles(mypath):
    f = []
    #filenames = []
    for (dirpath, dirnames, filenames) in walk(mypath):
        f.extend(filenames)
        for dirname in dirnames:
            print "checking in ", mypath+"/"+dirname
            for (dirpath, dirnames, filenames) in walk(mypath+"/"+dirname):
                print "found the following filenames ", filenames
                f.extend(filenames)
                print '////////////len(f)///////////// ',len(f)
        
        
        break
    print 'filenames ',f
    files = []
    for fname in f:
        exploded  =  fname.split('.')
        if len(exploded)>0:
            if exploded[len(exploded)-1]=='jpg' or exploded[len(exploded)-1]=='tif' or exploded[len(exploded)-1]=='pdf':
            #print fname
                files.append(fname)
    
    return files

def getFileNamesAndDates(mypath):
    f = []
    #filenames = []
    for (dirpath, dirnames, filenames) in walk(mypath):
        
        for aFile in filenames:
            print 'afile' ,mypath+"/"+aFile
            print time.ctime(os.path.getctime(mypath+"/"+aFile))
            file_and_info = []
            file_and_info.append(aFile)
            file_and_info.append(time.ctime(os.path.getctime(mypath+"/"+aFile)))
            f.append (file_and_info)
        for dirname in dirnames:
            print "checking in ", mypath+"/"+dirname
            for (dirpath, dirnames, filenames) in walk(mypath+"/"+dirname):
                print "found the following filenames ", filenames
                for aFile in filenames:
                    print 'afile' ,dirname+"/"+aFile
                    print time.ctime(os.path.getctime(dirname+"/"+aFile))
                    file_and_info = []
                    file_and_info.append(aFile)
                    file_and_info.append(time.ctime(os.path.getctime(dirname+"/"+aFile)))
                    f.append (file_and_info)
                   
        break
    print 'filenames ',f
    files = []
    for fname in f:
        exploded  =  fname[0].split('.')
        if len(exploded)>0:
            if exploded[len(exploded)-1]=='jpg' or exploded[len(exploded)-1]=='tif' or exploded[len(exploded)-1]=='pdf':
            #print fname
                files.append(fname)
    
    return files

def writeListOffiles(files, fname):
    f = open(fname, 'r+')
    f.seek(0)
    for s in files:
        f.write(s)
        f.write('\n')
    f.truncate()
    f.close()

def getNewItems(list1, list2):
    new_items = []
    if list1 != list2:
        print 'lists are different'
        for item in list1:
            try:
                list2.index(item)
            except:
                print item ,' is not in list'
                new_items.append(item)
    else:
        print 'no change to directory'
    return new_items

def checkFile(path):
    new_items = getNewItems(listfiles(path),getSavedListOffiles('file_list.txt') )
    print 'new_items ',new_items
    #print listfiles(path)
    if len(new_items)==0:
        print 'do nothing'
        msg = 'no new items at '
        msg += str(datetime.datetime.now())
        msg+= '#'
        #for letter in msg:
            #s.send(letter)
    else:
        writeListOffiles(listfiles(path),'file_list.txt' )
        updateMyStatus(new_items)
                

def getItemRefFromFileName(itemRef):
    parts = itemRef.split('.')
    parts[0].replace('-','/')
    return parts[0]

def updateMyStatus(rawMsgList):

    msgList = []
    for item in rawMsgList:
        ref = getItemRefFromFileName(item)

        if getAuthorForItem(ref) !='no author found':
            msgList.append(item)

    if msgList>0:
        num_new_files = len(msgList)
        if num_new_files ==1 :
            msg = 'There\'s a new item by'
        else:
            msg = 'There are '
            msg+= str(num_new_files)
            msg+= ' new items in the Bloodaxe archive. They are by '

        authorsList = []
        for item in msgList:
            ref = getItemRefFromFileName(item)
            author = getAuthorForItem(ref)
            hasBeenSaid = 0
            for a in authorsList:
                if author == a:
                    hasBeenSaid = 1
            if hasBeenSaid == 0:
                msg +='; '
                msg += author
                authorsList.append(author)
        
        if msg.__len__()>140:
            print 'long message '
            part =''
            count = 0
            msgList = []
            for index, char in enumerate (msg):
                if msg.__len__()-1 == index:
                    msgList.append(part)
                elif count <136:
                    part += char
                    count+=1
                else:
                    part += '...'
                    msgList.append(part)
                    
                    part=''
                    part += char
                    count = 0
            #print msgList
            for x in reversed(msgList):
                api.update_status(x)
        else:
            api.update_status(msg)
            #now email it 
            send_email(msg)
            #now send to ardunio
            msg+='#'
            for letter in msg:
                s.send(letter)

print getFileNamesAndDates(sys.argv[1])



