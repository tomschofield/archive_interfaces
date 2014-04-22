import sys
import os
from os import walk
print 'path is : ',sys.argv[1]
from email.mime.text import MIMEText
from datetime import date
import smtplib
import tweepy
import socket
import random
import urllib2
import datetime
HOST = "10.67.33.160"
PORT = 80

s = socket.socket( socket.AF_INET, socket.SOCK_STREAM )
s.connect((HOST, PORT))

consumer_key="xxx"
consumer_secret="x"
access_key = "x"
access_secret = "x" 

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_key, access_secret)
api = tweepy.API(auth)

#fname = sys.argv[1]
SMTP_SERVER = "smtp.gmail.com"
SMTP_PORT = 587
SMTP_USERNAME = "tomschofieldart@gmail.com"
SMTP_PASSWORD = "xxxx"

EMAIL_TO = ["tom.schofield@ncl.ac.uk"]
EMAIL_FROM = "tomschofieldart@gmail.com"
EMAIL_SUBJECT = "new files in archive : "

DATE_FORMAT = "%d/%m/%Y"
EMAIL_SPACE = ", "

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
        msg = ''
        for item in new_items:
            msg += item
            msg += '\n'
            
        print 'sending eamil'
        send_email(msg)

def updateMyStatus(msgList):

    num_new_files = len(msgList)
    if num_new_files ==1 :
        msg = 'There is a new image in the Bloodaxe archive. It is'
    else:
        msg = 'There are '
        msg+= str(num_new_files)
        msg+= ' new images in the Bloodaxe archive. They are'

    for item in msgList:
        msg +='; '
        msg += item
    
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
        msg+='#'
        for letter in msg:
            s.send(letter)

checkFile(sys.argv[1])