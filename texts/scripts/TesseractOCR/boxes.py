#creates a character box image for all files in current directory and saves to a subdir called boxy_images uses https://code.google.com/p/python-tesseract/

import tesseract
import cv2
import cv2.cv as cv
import ctypes
import os
from os import walk
from PIL import Image, ImageDraw

_fname ="BXB-1-1-HAT-8-8 7-8_1.jpg"


api = tesseract.TessBaseAPI()

def listfiles(mypath):
    f = []
    #filenames = []
    for (dirpath, dirnames, filenames) in walk(mypath):
        f.extend(filenames)
        for dirname in dirnames:
            #print "checking in ", mypath+"/"+dirname
            for (dirpath, dirnames, filenames) in walk(mypath+"/"+dirname):
                #print "found the following filenames ", filenames
                f.extend(filenames)
                #print '////////////len(f)///////////// ',len(f)
        
        
        break
    #print 'filenames ',f
    files = []
    for fname in f:
        exploded  =  fname.split('.')
        if len(exploded)>2:
        	print exploded
        if len(exploded)>0:
            if exploded[len(exploded)-1]=='jpg' or exploded[len(exploded)-1]=='tif' or exploded[len(exploded)-1]=='pdf':
            #print fname
            	#print 'adding ', exploded[0]
                files.append(fname)
    
    return files
def processImage(fname):
	print 'processing ',fname
	image0=cv2.imread(fname)

#leptonica = ctypes.cdll.LoadLibrary(leptlib)
#### you may need to thicken the border in order to make tesseract feel happy to ocr your image #####
	offset=20
	height,width,channel = image0.shape
	image1=cv2.copyMakeBorder(image0,offset,offset,offset,offset,cv2.BORDER_CONSTANT,value=(255,255,255))

	
	api.Init(".","eng",tesseract.OEM_DEFAULT)
	api.SetPageSegMode(tesseract.PSM_AUTO)
	height1,width1,channel1=image1.shape
	print(image1.shape)
	print(image1.dtype.itemsize)
	width_step = width*image1.dtype.itemsize
	print(width_step)

	iplimage = cv.CreateImageHeader((width1,height1), cv.IPL_DEPTH_8U, channel1)
	cv.SetData(iplimage, image1.tostring(),image1.dtype.itemsize * channel1 * (width1))
	tesseract.SetCvImage(iplimage,api)
	api.Recognize(None)
	ri=api.GetIterator()
	level=tesseract.RIL_WORD
	count=0
	x1 =0
	y1=0
	x2=0
	y2=0

	boxa = tesseract.TessBaseAPIGetComponentImages(api, tesseract.RIL_TEXTLINE, 1,None, None)
	boxes = api.GetBoxText(0)
	#print boxes
	#print type(boxes)
	lines = boxes.split('\n')
	#print len(lines)
	_im = Image.open(fname)
	pixels = list(_im.getdata())
	w = _im.size[0]
	h = _im.size[1]

	writeImage = Image.new("RGB", (w, h))

	draw = ImageDraw.Draw(writeImage)
	draw.rectangle([0,0,w,h],fill="rgb(255,255,255)")

	for index, box in enumerate(lines):
		#print index," ", box
		items = box.split(' ')
		if len(items)>4:
			#print index, " ",[items[1],items[2],items[3],items[4]]
			coords = []
			coords.append(int(items[1]))
			coords.append(h-int(items[2]))
			coords.append(int(items[3]))
			coords.append(h-int(items[4]))

			draw.rectangle(coords,fill="rgb(0,0,0)")
	#writeImage.transpose(Image.FLIP_TOP_BOTTOM)
	brokenPath = fname.split("/")
	writeImage.save('boxy_images/boxes_of_'+brokenPath[len(brokenPath)-1])

adir  = '/Users/cmdadmin/Documents/PDF/jpegs_r'
files = listfiles(adir)
for afile in files:
	checkPath = os.getcwd()+'/boxy_images/boxes_of_'+afile
	processImage(adir+"/"+afile)
	# if os.path.isfile(checkPath):
	# 	#print os.path.isfile(checkPath)
	# 	print 'processing ', afile
	# 	processImage(adir+"/"+afile)
	# else:
	# 	print 'already have ',afile
# processImage(_fname)
# iplimage=None
api.End()


