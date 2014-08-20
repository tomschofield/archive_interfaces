

def extractAllPDFsInCurrentDir():
	pdfs = listPDFs(os.getcwd())
	print 'there are ', len(pdfs), ' pdfs to extract'
	for apdf in pdfs:
	    extractPDF(apdf)

def extractPDF(fname):
    exploded  =  fname.split('.')
    path_and_name = exploded[0]
    exploded_by_slash = path_and_name.split('/')
    filename = exploded_by_slash[len(exploded_by_slash)-1]
    #filename = os.getcwd()+"/images_of_"+exploded[0] +"/"+fname

    #directory = os.path.dirname(filename)

    #if not os.path.exists(directory):
    #    os.makedirs(directory)

    pdf = file(fname, "rb").read()

    startmark = "\xff\xd8"
    startfix = 0
    endmark = "\xff\xd9"
    endfix = 2
    i = 0

    njpg = 0
    while True:
        istream = pdf.find("stream", i)
        if istream < 0:
            break
        istart = pdf.find(startmark, istream, istream+20)
        if istart < 0:
            i = istream+20
            continue
        iend = pdf.find("endstream", istart)
        if iend < 0:
            raise Exception("Didn't find end of stream!")
        iend = pdf.find(endmark, iend-20)
        if iend < 0:
            raise Exception("Didn't find end of JPG!")
         
        istart += startfix
        iend += endfix
        print "JPG %d from %d to %d" % (njpg, istart, iend)
        jpg = pdf[istart:iend]
        #jpgfile = file(directory+"/"+fname+"jpg%d.jpg" % njpg, "wb")
        jpgfile = file("jpegs/"+filename+"_%d.jpg" % njpg, "wb")
        jpgfile.write(jpg)
        jpgfile.close()
         
        njpg += 1
        i = iend

extractAllPDFsInCurrentDir()