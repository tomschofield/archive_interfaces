//TODO
/*
establish limit points of machine
 
 write function checking if scaled points are within this
 
 alter xml writing code to make sure there are never repeated numbers
 
 encapsulate loading into a load function which can be recalled as and when
 
 */

import processing.serial.*;
Serial myPort;  // Create object from Serial class

PrintWriter output;
int i=0;
ArrayList points;
PVector [] sendPoints;
PFont smallFont;
int count = 0;
boolean started =false;
String serialMessage="";
String precievedMessage="";
boolean useSerial = true;
PFont font;
float targetX = 0;
float targetY = 0;
boolean printSerialInput = true;
boolean sentFirstPoint = false;
float currentX = 0;
float currentY = 0;
//the ranges on the steppers
float maxX =3000;
float maxY = 3000;
int pPen = 0;
XML drawPs;
long timeOut = 0;
long timeOutThresh = 100;
long timeOutThreshDefault = 100;
String [] fnames;
float [] scales;
int numFiles=2;
int fileCounter = 0;
int xShift = 0;
int yShift = 0;
float xSize;
float ySize;
PVector [] duplicatePoints;
int [] duplicateIndices;
boolean restart = false;
void setup() {
  size(1400, 900);
  String [] alist = Serial.list();
  for (int i =0; i<alist.length; i++) {
    println(i+" "+alist[i]);
  }
  if (useSerial) {
    String portName = Serial.list()[9];
    println(portName);
    myPort = new Serial(this, portName, 9600);
    myPort.bufferUntil('\n');//'#');
  }
  font = loadFont("Serif-48.vlw");
  smallFont = loadFont("Serif-8.vlw");
  textFont(font, 48);
  //  points = new ArrayList();

  fnames = new String[numFiles] ;
  fnames[0] = "house_points.xml";
  fnames[1] = "points.xml";

  scales = new float [numFiles];
  scales[0]  = 3.0;
  scales[1] = 2.0;



  loadDoc(fnames[fileCounter]);



  background(0);
}

void draw() {
  scale(1/xSize, 1/ ySize);
  background(100);
  if (!useSerial) {
    if (frameCount%50==0) {
      count++;
    }
  }
  fill(0, 200, 0);
  for (int i = 0; i<duplicatePoints.length; i++) {
    ellipse(duplicatePoints[i].x, duplicatePoints[i].y, 5, 5);
  }
  noFill();
  stroke(244);
  strokeWeight(1);
  int rad = 3;

  drawPoints(xShift, yShift, drawPs, xSize, ySize );
  drawDrawnPoints(xShift, yShift, drawPs, xSize, ySize );

  noFill();
  stroke(255, 0, 0);
  ellipse(targetX, targetY, 15, 15);

  stroke(0, 255, 0);
  ellipse(currentX, currentY, 10, 10);
  String pos="TargetPos: ";

  //  stroke(0,0,255);
  //  ellipse(sendPoints[count].x, sendPoints[count].y,15,15);
  fill(255);
  pos+=str(targetX);//sendPoints[count].x); 
  pos+=", ";
  pos+=str(targetY);//sendPoints[count].y);
  textFont(font, 48);

  text(pos, 310, 40);

  //to do make this linked to the distance btween this point and the previous
  //change this to request position first!
  if (millis() - timeOut>timeOutThresh && sentFirstPoint) {
    //if we time out then request the distance
    println("timed out: requesting location. thresh is : "+timeOutThresh);
    myPort.write('A');//"H");
    myPort.write(10);

    timeOut = millis();
    timeOutThresh+=1000;
  }


  if (restart) {
    //send a paper advance message
    myPort.write('P');//"H");
    myPort.write(10);
    //wait for the paper to advance
    delay(30000); 
    //now go for it
    startNewImage();
    restart =false;
  }
}
void sendPoint(float x, float y, float x1, float y1) {

  String mx = str((int)x);//map(x, 0, height, 0, maxX));
  String my = str((int)y);//map(y, 0, width, 0, maxY));

  String mx1 = str((int)x1);//map(x, 0, height, 0, maxX));
  String my1 = str((int)y1);//map(y, 0, width, 0, maxY));

  targetX = x;
  targetY = y;

  println("sending target "+mx+" "+my+"with previous target "+mx1+" "+my1);
  //println(mouseX);
  for (int i=0; i<mx.length (); i++) {

    myPort.write(mx.charAt(i));
  }
  myPort.write(',');
  for (int i=0; i<my.length (); i++) {

    myPort.write(my.charAt(i));
  }

  //add the previous point
  myPort.write(',');
  for (int i=0; i<mx1.length (); i++) {

    myPort.write(mx1.charAt(i));
  }
  myPort.write(',');
  for (int i=0; i<my1.length (); i++) {

    myPort.write(my1.charAt(i));
  }

  myPort.write(10);//'\n');
}
void sendPenToggle(int penIsDown) {
  if (penIsDown==1) {
    myPort.write('D');//"H");
    myPort.write(10);
  } else {
    myPort.write('U');//"H");
    myPort.write(10);
  }
}


void keyPressed() {

  if (key=='g') {

    sendPoint(sendPoints[count].x, sendPoints[count].y, 0, 0);
    sendPenToggle((int)sendPoints[count].z);
    count++;
    if (count>=sendPoints.length) {
      count= 0;
    }
    //started=true;
  }
  if (key=='d'||key=='D') {

    myPort.write('D');//"H");
    myPort.write(10);
  }
  if (key=='u'||key=='U') {

    myPort.write('U');//"H");
    myPort.write(10);
  }
  if (key=='s'||key=='S') {

    myPort.write('S');//"H");
    myPort.write(10);
  }
  if (key=='a'||key=='A') {

    myPort.write('A');//"H");
    myPort.write(10);
  }
  if (key=='f'||key=='F') {

    //myPort.write('F');//"H");
    //myPort.write(10);
  }
  if (key=='h'||key=='H') {

    //myPort.write('H');//"H");
    //myPort.write(10);
  }
  if (key=='p'||key=='P') {
    myPort.write('P');
    myPort.write(10);
  }
}

void serialEvent(Serial myPort) {

  serialMessage = myPort.readString();
  if (serialMessage.length()>4) {
    println("from serial "+serialMessage);
  }
  checkMessage(serialMessage);
  serialMessage="";
  //if(recievedMessage.length()>4)  println(recievedMessage);
}


void checkMessage(String recievedMessage) {
  String recieved = "request_new_point";

  String distanceMsg = "dist";
  if (recievedMessage!=null) {

    if (recievedMessage.length()> distanceMsg.length()+1) {
      if (recievedMessage.substring(0, distanceMsg.length() ).equals(distanceMsg)) { 
        println("got distance message "+recievedMessage);
        String [] exploded =splitTokens(recievedMessage, ":" );

        int distX = int(exploded[1].trim());
        int distY = int(exploded[2].trim());

        int speedX = int(exploded[4].trim());
        int speedY = int(exploded[5].trim());


        int hasArrived = int(exploded[3].trim());

        if (speedX<=0 || speedY<=0) {
          println("found negative speed, advance count ");
          hasArrived =1;
        }

        if (hasArrived==0) {
          println("////////////////////////////////////////////resending point at "+count+". is a repeat : "+isARepeat(duplicateIndices, count));
          count--;
          PVector lastNonIdenticalPoint = getLastNonIdenticalPoint(sendPoints[count], count );
          sendPoint(sendPoints[count].x, sendPoints[count].y, lastNonIdenticalPoint.x, lastNonIdenticalPoint.y);
          sendPenToggle((int)sendPoints[count].z);

          if ((int)sendPoints[count].z==1) {
            println("**************pen down******************");
          }

          pPen = (int)sendPoints[count].z;
          count++;
          if (count>=sendPoints.length) {
            restart = true;
          }
        } else {
          timeOutThresh = timeOutThreshDefault;
          println("////////////////////////////////////////////got distance request sending next point at "+count+".  is a repeat : "+isARepeat(duplicateIndices, count));
          PVector lastNonIdenticalPoint = getLastNonIdenticalPoint(sendPoints[count], count );
          sendPoint(sendPoints[count].x, sendPoints[count].y, lastNonIdenticalPoint.x, lastNonIdenticalPoint.y);
          sendPenToggle((int)sendPoints[count].z);
          if ((int)sendPoints[count].z==1) {
            println("**************pen down******************");
          }
          pPen = (int)sendPoints[count].z;
          count++;
          if (count>=sendPoints.length) {
            restart = true;
          }
        }
      }
    }

    if (recievedMessage.length()> recieved.length()+1) {

      if (recievedMessage.substring(0, recieved.length() ).equals(recieved)) { 
        println("sending point"); 
        sentFirstPoint=true;
        timeOut = millis();
        timeOutThresh = timeOutThreshDefault;
        String [] exploded =splitTokens(recievedMessage, ":" );

        int lastX = int(exploded[1].trim());
        int lastY = int(exploded[2].trim());


        int hasArrived = int(exploded[3].trim());

        int speedX = int(exploded[4].trim());
        int speedY = int(exploded[5].trim());


        println("compared points "+sendPoints[count].x+" "+ sendPoints[count].y+" "+lastX+" "+lastY+" has arrived " +hasArrived+" speeds "+speedX+" "+speedY);
        PVector lastNonIdenticalPoint = getLastNonIdenticalPoint(sendPoints[count], count );
        sendPoint(sendPoints[count].x, sendPoints[count].y, lastNonIdenticalPoint.x, lastNonIdenticalPoint.y);
        sendPenToggle((int)sendPoints[count].z);
        if ((int)sendPoints[count].z==1) {
          println("**************pen down******************");
        }
        pPen = (int)sendPoints[count].z;

        count++;
        if (count>=sendPoints.length) {
          restart = true;
        }
      } else {
        println("long message which doesn't match "+recievedMessage); 
        //println("substring "+recievedMessage.substring(0, 4 ));
      }
    } else {
      // println( recievedMessage.length() +" "+recieved.length()+" "+recievedMessage.charAt(0)+" "+recieved.charAt(0));
    }
  } 
  recievedMessage = "";
}

PVector [] getFontPoints(String letter, int xAdjust, int yAdjust) {
  ArrayList myList = new ArrayList();

  XML xml = loadXML("font_points.xml");
  XML[] children = xml.getChildren("letter"); 
  println("children "+children.length);

  for (int i=0; i<children.length; i++) {
    if ( children[i].getString("letter_name").equals("A")) {
      println("found");
      XML letterStroke = children[i];
      XML [] pointData = letterStroke.getChildren("point");
      //this is the first point
      float x = pointData[0].getChild("x").getFloatContent();
      float y = pointData[0].getChild("y").getFloatContent();
      PVector startPos = new PVector(x, y, 0);
      myList.add(startPos);
      //add all the points in the middle
      for (int j=0; j<pointData.length; j++) {
        x = pointData[j].getChild("x").getFloatContent();
        y = pointData[j].getChild("y").getFloatContent();
        PVector pointPos = new PVector(x, y, 1);
        myList.add(pointPos);
      }
      //now pen up at the end
      x = pointData[pointData.length-1].getChild("x").getFloatContent();
      y = pointData[pointData.length-1].getChild("y").getFloatContent();
      PVector endPos = new PVector(x, y, 0);
      myList.add(endPos);
    }
  }
  PVector [] toReturn  = new PVector[myList.size()];
  for (int i=0; i<myList.size (); i++) {
    toReturn[i] = (PVector) myList.get(i);
    toReturn[i].x+=xAdjust;
    toReturn[i].y+=yAdjust;
  }
  return toReturn;
}

PVector [] getPoints( int xAdjust, int yAdjust, float xScale, float yScale, String fname) {
  ArrayList myList = new ArrayList();

  XML xml = loadXML(fname);
  XML[] children = xml.getChildren("contour"); 
  println("children "+children.length);
  float pX = 0;
  float pY = 0;

  for (int i=0; i<children.length; i++) {
    // if ( children[i].getString("letter_name").equals("A")) {
    println("found");
    XML letterStroke = children[i];
    XML [] pointData = letterStroke.getChildren("point");
    //this is the first point
    float x = pointData[0].getChild("x").getFloatContent();
    float y = pointData[0].getChild("y").getFloatContent();

    PVector startPos = new PVector((int)x*xScale, (int)y*yScale, 0);

    myList.add(startPos);
    //add all the points in the middle
    for (int j=0; j<pointData.length; j++) {
      x = pointData[j].getChild("x").getFloatContent();
      y = pointData[j].getChild("y").getFloatContent();

      ///if((int)x!=(int)pX && (int)y!=(int)pY){
      PVector pointPos = new PVector((int)x*xScale, (int) y*yScale, 1);
      myList.add(pointPos);
      pX = x;
      pY = y;
      //      }
      //      else{
      //       println("found duplicate point"); 
      //      }
    }
    x = pointData[0].getChild("x").getFloatContent();
    y = pointData[0].getChild("y").getFloatContent();

    PVector returnToStartPos = new PVector((int)x*xScale, (int)y*yScale, 1);
    myList.add(returnToStartPos);
    returnToStartPos.z=0;
    myList.add(returnToStartPos);
    //    x = pointData[pointData.length-1].getChild("x").getFloatContent();
    //    y = pointData[pointData.length-1].getChild("y").getFloatContent();
    //    PVector endPos = new PVector((int)x*xScale,(int) y*yScale, 0);
    //    myList.add(endPos);

    // }
  }
  PVector [] toReturn  = new PVector[myList.size()];
  for (int i=0; i<myList.size (); i++) {
    toReturn[i] = (PVector) myList.get(i);
    toReturn[i].x+=xAdjust;
    toReturn[i].y+=yAdjust;
  }
  return toReturn;
}

void drawPoints( int xAdjust, int yAdjust, XML xml, float xScale, float yScale) {
  pushStyle();
  stroke(255);
  ArrayList myList = new ArrayList();
  textFont(smallFont, 8);
  XML[] children = xml.getChildren("contour"); 
  // println("children "+children.length);

  for (int i=0; i<children.length; i++) {
    //if ( children[i].getString("letter_name").equals("A")) {
    //println("found");
    XML letterStroke = children[i];

    XML [] pointData = letterStroke.getChildren("point");
    beginShape();
    for (int j=0; j<pointData.length; j++) {
      float x = pointData[j].getChild("x").getFloatContent();
      float y = pointData[j].getChild("y").getFloatContent();
      PVector pointPos = new PVector(x*xScale, y*yScale, 1);
      vertex(xAdjust + (x*xScale), yAdjust +( y*yScale));
      int thresh = 10;
      if (mouseX > (xAdjust + (x*xScale))-thresh && mouseX < (xAdjust + (x*xScale))+thresh && mouseY > (yAdjust +( y*yScale))-thresh &&mouseY < (yAdjust +( y*yScale))+thresh) {
        String t = "contour: ";
        t+=str(j);
        t+="\npoint: ";
        t+=str(i);
        t+="\ncoords: ";
        t+=str(x*xScale);
        t+=" , ";
        t+=str(y*yScale);
        // text(t, xAdjust + (x*xScale), yAdjust +( y*yScale));
      }

      // text(str(j), xAdjust + (x*xScale), yAdjust +( y*yScale));
      //myList.add(pointPos);
    }
    endShape();

    // }
  }
  popStyle();
}



void drawDrawnPoints( int xAdjust, int yAdjust, XML xml, float xScale, float yScale) {
  pushStyle();
  stroke(255, 0, 0);
  ArrayList myList = new ArrayList();
  textFont(smallFont, 8);
  XML[] children = xml.getChildren("contour"); 
  // println("children "+children.length);
  int pointCount = 0;
  for (int i=0; i<children.length; i++) {
    //if ( children[i].getString("letter_name").equals("A")) {
    //println("found");
    XML letterStroke = children[i];

    XML [] pointData = letterStroke.getChildren("point");
    beginShape();
    pointCount++;
    for (int j=0; j<pointData.length; j++) {
      float x = pointData[j].getChild("x").getFloatContent();
      float y = pointData[j].getChild("y").getFloatContent();
      PVector pointPos = new PVector(x*xScale, y*yScale, 1);

      if (pointCount<count) {
        vertex(xAdjust + (x*xScale), yAdjust +( y*yScale));
      }
      pointCount++;
      // text(str(j), xAdjust + (x*xScale), yAdjust +( y*yScale));
      //myList.add(pointPos);
    }
    pointCount++;
    pointCount++;
    endShape();

    // }
  }
  popStyle();
}
float getMinXInPVectorArray(PVector [] vectors) {
  float min = 10000000.0;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x<min) {
      min = vectors[i].x;
    }
  }
  return min;
}
float getMinYInPVectorArray(PVector [] vectors) {
  float min = 10000000.0;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].y<min) {
      min = vectors[i].y;
    }
  }
  return min;
}
float getMaxXInPVectorArray(PVector [] vectors) {
  float max = 0.0;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x>max) {
      max = vectors[i].x;
    }
  }
  return max;
}
float getMaxYInPVectorArray(PVector [] vectors) {
  float max = 0.0;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x>max) {
      max = vectors[i].y;
    }
  }
  return max;
}

PVector [] checkForDuplicates(PVector [] vectors) {
  ArrayList duplicates = new ArrayList();
  for (int i =0; i<vectors.length-1; i++) {
    if (vectors[i].x == vectors[i+1].x && vectors[i].y == vectors[i+1].y) {
      println(i+"has a duplicate"); 
      duplicates.add(vectors[i]);
      duplicates.add(vectors[i+1]);
    }
  }
  PVector [] d = new PVector[duplicates.size()];
  for (int i =0; i<d.length; i++) {
    d[i] = (PVector) duplicates.get(i);
  }
  return d;
}
int [] getDuplicateIndices(PVector [] vectors) {
  ArrayList duplicates = new ArrayList();
  PrintWriter output;

  output = createWriter("duplicates.txt");
  for (int i =0; i<vectors.length-1; i++) {
    if (vectors[i].x == vectors[i+1].x && vectors[i].y == vectors[i+1].y) {
      println(i+"has a duplicate"); 
      duplicates.add(i);
      duplicates.add(i+1);

      output.println(i+","+vectors[i]);
      output.println((i+1)+","+vectors[i+1]);
    }
  }
  int [] d = new int[duplicates.size()];
  for (int i =0; i<d.length; i++) {
    d[i] = (Integer) duplicates.get(i);
  }

  for (int i =0; i<d.length; i++) {
  }
  output.flush();
  output.close();
  return d;
}

boolean isARepeat(int [] duplicates, int index) {
  boolean repeat = false;
  for (int i =0; i<duplicates.length; i++) {
    if (duplicates[i] ==  index) {
      repeat = true;
    }
  }
  return repeat;
}


PVector getLastNonIdenticalPoint(PVector thisPoint, int thisIndex) {
  PVector returnPoint = new PVector();
  for ( int i=thisIndex; i>=0; i--) {
    if (sendPoints[i].x!= thisPoint.x && sendPoints[i].y!=thisPoint.y) {
      returnPoint = sendPoints[i];
      break;
    }
  }
  return returnPoint;
}

void loadDoc(String fname) {
  timeOut = millis();
  xSize = scales[fileCounter];
  ySize = scales[fileCounter];
  XML xml = loadXML("font_points.xml");
  drawPs =loadXML(fname);
  XML[] children = xml.getChildren("alphabet");
  println("children "+children.length);

  sendPoints = getPoints( xShift, yShift, xSize, ySize, fname);//new PVector[4];
  PrintWriter output;
  output = createWriter("log.txt");
  for (int i=0; i<sendPoints.length; i++) {
    output.println(i+ " "+sendPoints[i]);
  }
  output.flush();
  output.close();

  println("max x "+getMaxXInPVectorArray(sendPoints));
  println("max y "+getMaxYInPVectorArray(sendPoints));

  duplicatePoints = checkForDuplicates(sendPoints);
  duplicateIndices = getDuplicateIndices(sendPoints);
  fileCounter++;
}

void startNewImage() {
  count =0;
  PVector startOver = new PVector(0, 0, 0);
  sendPoint(startOver.x, startOver.y, 0, 0);
  sendPenToggle((int)startOver.z);

  loadDoc(fnames[fileCounter]);
}

