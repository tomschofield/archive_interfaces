int bW ;
int bH ;
int numXBlocks=10;
int numYBlocks=15;
boolean [] blockStates;
boolean [][] comparisonBlockStates;
int thresh  =231;
PImage img;
PFont font;
PImage [] srcs;
PImage bestMatch;
void setup() {
  size(1900, 1000); 
  //  numXBlocks = width/bW;
  //  numYBlocks = height/bH;
  font = loadFont("Serif-14.vlw");
  textFont(font, 14);
  int w = 100;
  int h = 150;
  bW = w/numXBlocks;
  bH = h/numYBlocks;
  blockStates = new boolean[numXBlocks*numYBlocks];
  String docFolder = "IMAGES";
  File dir = new File(dataPath(docFolder+"/"));
  ArrayList names = new ArrayList();
    String[] children = dir.list();
    if (children == null) {
      //  println("not there");
      // Either dir does not exist or is not a directory
    } 
    else {
      for (int i=0; i<children.length; i++) {
        println(children[i]);
        String temp[]=splitTokens(children[i],".");
        try {
          if(temp[1].equals("jpg")) {
            names.add(children[i]);
          }
        }
        catch(Exception e) {
        }
      }
    } 
    
  String [] fnames = new String[names.size()];
  for(int i=0;i<names.size();i++){
   fnames[i] = (String) names.get(i); 
  }
//  fnames[0] = "test.jpg";
//  fnames[1] = "BXB-1-1-ADC-5-4 18-22-24.06.2014 17-38-01-page4.jpg";
//  fnames[2] = "BXB-1-1-ATT-1-1 Belgium-Dear Saddam-page6.jpg";
//  fnames[3] = "BXB-1-1-BEL-1-3 51-56-page5.jpg";
//  fnames[4] = "BXB-1-1-BEL-1-3 137-140-page1.jpg";
//  fnames[5] = "BXB-1-1-BEL-1-4-page4.jpg";
//  fnames[6] = "BXB-1-1-BER-1-4 79-88-page1.jpg";
//  fnames[7] = "BXB-1-1-HIL-1-1 44-58-page3.jpg";

  comparisonBlockStates = new boolean [fnames.length][];
  srcs = new PImage[fnames.length];
  for (int i= 0; i<fnames.length; i++) {
    comparisonBlockStates [i] = processImage("IMAGES/"+fnames[i]);
    srcs [i]= loadImage("IMAGES/"+fnames[i]);
  }
}
float compareBinaryImages(boolean [] drawnBlocks, boolean [] imgBlocks) {
  float count = 0;
  if (drawnBlocks.length!=imgBlocks.length) {
    println("lengths do not match");
  }
  int numTextBlocks = 0;
  for (int i=0; i<imgBlocks.length; i++) {
    if (imgBlocks[i]) {
      numTextBlocks++;
    }
  }
  for (int i=0; i<drawnBlocks.length; i++) {
    if (drawnBlocks[i]==imgBlocks[i]){// && imgBlocks[i]) {
      count++; 
      //     println("hit");
    }
  }

  // println("numTextBlocks "+numTextBlocks);
  float percent = count/drawnBlocks.length;
  //   println(percent);
  return percent;///drawnBlocks.length;
}
void draw() {
  background(0);
  //  noFill();
  noStroke();
  //  stroke(255);
  int index = 0;
  
  int indexOfBestImage = 0;
  float bestResult = 0.0f;
  for (int i=0; i<numXBlocks; i++) {
    for (int j=0; j<numYBlocks; j++) {
      if (blockStates[index]==false) {
        fill(255);
      } else {
        fill(0);
      }
      rect(i*bW, j*bH, bW, bH);
      //      if(mousePressed && mouseX>= i*bW && mouseX < (i*bW)+bW && mouseY >=j*bH && mouseY <(j*bH)+bH){
      //        blockStates[index] = !blockStates[index];
      //      }
      index++;
    }
  }
  int xShift = 0;
  int yShift = 160;
  for (int imageIndex = 0; imageIndex <comparisonBlockStates.length; imageIndex++) {
    index =0;
    
    float result = compareBinaryImages(blockStates,comparisonBlockStates[imageIndex]);
    
    if(result>bestResult){
     bestResult = result;
    indexOfBestImage = imageIndex; 
    }
    String percent = str(result );
    if(percent.length()>4){
     percent = percent.substring(0,4); 
    }
    text(percent,xShift,yShift+165);
    for (int i=0; i<numXBlocks; i++) {
      for (int j=0; j<numYBlocks; j++) {
        if (comparisonBlockStates[imageIndex][index]==false) {
          fill(255);
        } else {
          fill(0);
        }
        rect(xShift+(i*bW), yShift+(j*bH), bW, bH);
        //      if(mousePressed && mouseX>= i*bW && mouseX < (i*bW)+bW && mouseY >=j*bH && mouseY <(j*bH)+bH){
        //        blockStates[index] = !blockStates[index];
        //      }
        index++;
      }
    }
    
    image(srcs[imageIndex],xShift,yShift+200,100,150);
    xShift+=120;
    if(xShift+120>width){
     xShift = 0;
    yShift+=400; 
    }
  }
//  xShift = 0;
//  yShift = 540;
//  for (int i = 0; i <srcs.length; i++) {
//    image(srcs[i],xShift,yShift,100,150);
//    xShift+=120;
//  }
  text("BEST MATCHING IMAGE: ",120,50);
  image(srcs[indexOfBestImage], 300,0,100,150);
  fill(255);
  //  text(str(compareBinaryImages(blockStates,imgblockStates) ),10,height-15);
}
void keyPressed(){
  for (int i=0; i<blockStates.length; i++) {
    blockStates[i]=false;
  }
}
void mouseClicked() {

  int index = 0;
  for (int i=0; i<numXBlocks; i++) {
    for (int j=0; j<numYBlocks; j++) {

      if (mouseX>= i*bW && mouseX < (i*bW)+bW && mouseY >=j*bH && mouseY <(j*bH)+bH) {
        blockStates[index] = !blockStates[index];
      }
      index++;
    }
  }
}
void mouseDragged() {

  int index = 0;
  for (int i=0; i<numXBlocks; i++) {
    for (int j=0; j<numYBlocks; j++) {

      if (mouseX>= i*bW && mouseX < (i*bW)+bW && mouseY >=j*bH && mouseY <(j*bH)+bH) {
        blockStates[index] = true;//!blockStates[index];
      }
      index++;
    }
  }
}
boolean [] processImage(String fname) {
  img = loadImage(fname);
  img.loadPixels();
  int pixelsPerBlock=0;
  boolean [] imgblockStates;

  int imgnumXBlocks =numXBlocks;
  int imgnumYBlocks =numYBlocks;
  int imgbW = img.width/imgnumXBlocks;
  int imgbH = img.height/imgnumYBlocks;



  int imgpixelsPerBlock=0;
  imgnumXBlocks = img.width/imgbW;
  imgnumYBlocks = img.height/imgbH;
  //  numXBlocks = 30;
  //  numYBlocks  = 50;

  imgbW = img.width/imgnumXBlocks;
  imgbH = img.height/imgnumYBlocks;

  imgpixelsPerBlock = imgbW*imgbH;

  img.resize(imgnumXBlocks*imgbW, imgnumYBlocks*imgbH);

  imgblockStates = new boolean[imgnumXBlocks*imgnumYBlocks];
  for (int i=0; i<imgblockStates.length; i++) {
    imgblockStates[i]=false;
  }
  println(imgblockStates.length);
  int index = 0;

  for (int i=0; i<img.width; i+=imgbW) {
    for (int j=0; j<img.height; j+=imgbH) {

      float average=0;

      //      println("processing at "+i+" "+j);
      for (int k=i; k<i+imgbW; k++) {
        for (int l=j; l<j+imgbH; l++) {
          average+=brightness(img.get(k, l));
        }
      }
      average/=(imgpixelsPerBlock);
      //      println(average);
      if (index<imgblockStates.length-1) {
        if (average>=thresh ) {
          imgblockStates[index] = false;
        } else {
          imgblockStates[index] = true;
        }
      }
      index++;
    }
  }
  println(imgblockStates.length);
  println(imgnumXBlocks);
  println(imgnumYBlocks);
  return imgblockStates;
}

