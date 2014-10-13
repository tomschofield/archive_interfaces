import processing.core.*; 
import processing.data.*; 
import processing.event.*; 
import processing.opengl.*; 

import processing.serial.*; 

import java.util.HashMap; 
import java.util.ArrayList; 
import java.io.File; 
import java.io.BufferedReader; 
import java.io.PrintWriter; 
import java.io.InputStream; 
import java.io.OutputStream; 
import java.io.IOException; 

public class xml_to_machine_v5 extends PApplet {

//TODO
/*
establish limit points of machine
 
 write function checking if scaled points are within this
 
 alter xml writing code to make sure there are never repeated numbers
 
 encapsulate loading into a load function which can be recalled as and when
 
 */


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
int numFiles=29;
int fileCounter = 0;
int xShift = 0;
int yShift = 0;
float xSize;
float ySize;
PVector [] duplicatePoints;
int [] duplicateIndices;
//six minute wait
int waitTime = 360000;
boolean restart = false;
public void setup() {
  size(1400, 800);
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



  fnames[0] = "marginalia_of_BXB-1-1-BEN-1-4 Copyright-February_0_points.xml";
  fnames[1] = "marginalia_of_BXB-1-1-BEN-1-4 Copyright-February_7_points.xml";
  fnames[2] = "marginalia_of_BXB-1-1-BEN-1-4 Seeing Things-Modus Vivendi_2_points.xml";
  fnames[3] = "marginalia_of_BXB-1-1-BEN-1-4 Seeing Things-Modus Vivendi_9_points.xml";
  fnames[4] = "marginalia_of_BXB-1-1-BEN-1-5 Reject Setting-The Innocent_0_points.xml";
  fnames[5] = "marginalia_of_BXB-1-1-BER-1-4 11-52_13_points.xml";
  fnames[6] = "marginalia_of_BXB-1-1-BER-1-4 11-52_22_points.xml";
  fnames[7] = "marginalia_of_BXB-1-1-BER-1-4 11-52_43_points.xml";
  fnames[8] = "marginalia_of_BXB-1-1-CAS-2-3 Contents-Noon in Sante Fe_3_points.xml";
  fnames[9] = "marginalia_of_BXB-1-1-GRG-1-4_2_points.xml";
  fnames[10] = "marginalia_of_BXB-1-1-GRG-2-1 Five Parts in an Adventure-Kenny's Fax_1_points.xml";
  fnames[11] = "marginalia_of_BXB-1-1-GRG-2-1 Five Parts in an Adventure-Kenny's Fax_11_points.xml";
  fnames[12] = "marginalia_of_BXB-1-1-GRJ-1-1_13_points.xml";
  fnames[13] = "marginalia_of_BXB-1-1-HYL-1-1 Cover-IX_4_points.xml";
  fnames[14] = "marginalia_of_BXB-1-1-MOV-1-1_14_points.xml";
  fnames[15] = "marginalia_of_BXB-1-2-TEN-1 Preface-Page 9 Ends_14_points.xml";
  fnames[16] = "marginalia_of_BXB-1-2-TEN-1 Preface-Page 9 Ends_18_points.xml";

  fnames[17] = "marginalia_of_BXB-1-1-BEN-1-4 Copyright-February_6_points.xml";
  fnames[18] = "marginalia_of_BXB-1-1-BEN-1-4 Seeing Things-Modus Vivendi_5_points.xml";
  fnames[19] = "marginalia_of_BXB-1-1-BER-1-4 11-52_24_points.xml";
  fnames[20] = "marginalia_of_BXB-1-1-CAS-2-3 Contents-Noon in Sante Fe_8_points.xml";
  fnames[21] = "marginalia_of_BXB-1-1-COS-2-2 1-8_4_points.xml";
  fnames[22] = "marginalia_of_BXB-1-1-COS-3-4 Acknowledgements-Contents_0_points.xml";
  fnames[23] = "marginalia_of_BXB-1-1-GRG-2-1 Five Parts in an Adventure-Kenny's Fax_1b_points.xml";
  fnames[24] = "marginalia_of_BXB-1-1-GRJ-1-1_29_points.xml";
  fnames[25] = "marginalia_of_BXB-1-1-GRJ-1-3 The Tutankamun Variations-Entr'acte_13_points.xml";
  fnames[26] = "marginalia_of_BXB-1-2-TEN-1 Preface-Page 9 Ends_17_points.xml";
  fnames[27] = "marginalia_of_BXB-1-4-AST-1-4 D M Thomas_1_points.xml";
  fnames[28] = "marginalia_of_BXB-1-4-AST-1-4 David Constantine_2_points.xml";
  //  fnames[17] = "marginalia_of_BXB-1-4-AST-1-4 Alan Plater_0_points.xml";
  //  fnames[18] = "house_points.xml";
  //  fnames[19] = "points.xml";

  scales = new float [numFiles];
  for (int i=0; i<scales.length; i++) {
    scales[i] = 2.0f;
  }
  scales[21] = 3.0f;
  //  scales[0]  = 3.0;
  //  scales[1] = 2.0;
  //  scales[2]  = 2.0;
  //  scales[3] = 2.0;
  //  scales[4]  = 2.0;
  //  scales[5] = 2.0;
  //  scales[6]  = 2.0;
  //  scales[7] = 2.0;
  //  scales[8]  = 2.0;
  //  scales[9] = 2.0;
  //  scales[10]  = 2.0;
  //  scales[11] = 2.0;
  //  scales[12]  = 2.0;
  //  scales[13] = 2.0;
  //  scales[14]  = 2.0;
  //  scales[15] = 2.0;
  //  scales[16]  = 2.0;
  //  //  scales[17] = 1.0;
  //  //  scales[18]  = 2.0;
  //  //  scales[19] = 2.0;



  loadDoc(fnames[fileCounter]);



  background(0);
}

public void draw() {

  if (frameCount>1000) {
    if (!started) {
      sendPoint(sendPoints[count].x, sendPoints[count].y, 0, 0);
      sendPenToggle((int)sendPoints[count].z);
      count++;
      if (count>=sendPoints.length) {
        count= 0;
      }
      started = true;
    }
  }

  scale(1/xSize, 1/ xSize);
  background(100);
  if (!useSerial) {
    if (frameCount%50==0) {
      count++;
    }
  }
  //  fill(0, 200, 0);
  //  for (int i = 0; i<duplicatePoints.length; i++) {
  //    ellipse(duplicatePoints[i].x, duplicatePoints[i].y, 5, 5);
  //  }
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
  pos+="\nWriting "+fnames[fileCounter-1];
  textFont(font, 48);

  text(pos, 10, (ySize* height)-200);

  //to do make this linked to the distance btween this point and the previous
  //change this to request position first!
  if (millis() - timeOut>timeOutThresh && sentFirstPoint) {
    //if we time out then request the distance
    println("timed out: requesting location. thresh is : "+timeOutThresh);
    myPort.write('A');//"H");
    myPort.write(10);

    timeOut = millis();
    timeOutThresh+=1000;
    //if we've really been waiting for ages...like a minute say...
    if (timeOutThresh>30000) {

      //lets log the fail
      String [] log = loadStrings("failure_log.txt");
      PrintWriter output;
      output = createWriter("data/failure_log.txt");
      for (int i=0; i<log.length; i++) {
        output.println(log[i]);
      }
      String failure_message = "";
      failure_message+= str(day());
      failure_message+= ":";
      failure_message+= str(hour());
      failure_message+= ":";
      failure_message+= str(minute());
      failure_message+= " failed on "+fnames[fileCounter-1];


      output.print(failure_message);
      output.flush();
      output.close();

      //and restart
      restart=true;
    }
  }


  if (restart) {
    //send the pen home
    println("sending home");
    myPort.write('U');//"H");
    myPort.write(10);
    delay(1000); 

    myPort.write('I');
    myPort.write(10);

    delay(1500); 
    //send a paper advance message
    myPort.write('P');//"H");
    myPort.write(10);
    //wait for the paper to advance and then sit there for a few minutes allowing the solenoid to cool
    delay(waitTime); 
    //now go for it
    startNewImage();
    restart =false;
  }
}
public void sendPoint(float x, float y, float x1, float y1) {

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
public void sendPenToggle(int penIsDown) {
  if (penIsDown==1) {
    myPort.write('D');//"H");
    myPort.write(10);
  } else {
    myPort.write('U');//"H");
    myPort.write(10);
  }
}


public void keyPressed() {

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

    //fileCounter++;
    if (fileCounter>=scales.length) fileCounter = 0;
    loadDoc(fnames[fileCounter]);
  }
  if (key=='h'||key=='H') {

    //myPort.write('H');//"H");
    //myPort.write(10);
  }
  if (key=='p'||key=='P') {
    myPort.write('P');
    myPort.write(10);
  }
  if (key=='i'||key=='I') {
    myPort.write('I');
    myPort.write(10);
  }
}

public void serialEvent(Serial myPort) {

  serialMessage = myPort.readString();
  if (serialMessage.length()>4) {
    println("from serial "+serialMessage);
  }
  checkMessage(serialMessage);
  serialMessage="";
  //if(recievedMessage.length()>4)  println(recievedMessage);
}


public void checkMessage(String recievedMessage) {
  String recieved = "request_new_point";

  String distanceMsg = "dist";
  if (recievedMessage!=null) {

    if (recievedMessage.length()> distanceMsg.length()+1) {
      if (recievedMessage.substring(0, distanceMsg.length() ).equals(distanceMsg)) { 
        println("got distance message "+recievedMessage);
        String [] exploded =splitTokens(recievedMessage, ":" );

        int distX = PApplet.parseInt(exploded[1].trim());
        int distY = PApplet.parseInt(exploded[2].trim());

        int speedX = PApplet.parseInt(exploded[4].trim());
        int speedY = PApplet.parseInt(exploded[5].trim());


        int hasArrived = PApplet.parseInt(exploded[3].trim());

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

        int lastX = PApplet.parseInt(exploded[1].trim());
        int lastY = PApplet.parseInt(exploded[2].trim());


        int hasArrived = PApplet.parseInt(exploded[3].trim());

        int speedX = PApplet.parseInt(exploded[4].trim());
        int speedY = PApplet.parseInt(exploded[5].trim());


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

public PVector [] getFontPoints(String letter, int xAdjust, int yAdjust) {
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

public PVector [] getPoints( int xAdjust, int yAdjust, float xScale, float yScale, String fname) {
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

public void drawPoints( int xAdjust, int yAdjust, XML xml, float xScale, float yScale) {
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



public void drawDrawnPoints( int xAdjust, int yAdjust, XML xml, float xScale, float yScale) {
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
public float getMinXInPVectorArray(PVector [] vectors) {
  float min = 10000000.0f;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x<min) {
      min = vectors[i].x;
    }
  }
  return min;
}
public float getMinYInPVectorArray(PVector [] vectors) {
  float min = 10000000.0f;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].y<min) {
      min = vectors[i].y;
    }
  }
  return min;
}
public float getMaxXInPVectorArray(PVector [] vectors) {
  float max = 0.0f;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x>max) {
      max = vectors[i].x;
    }
  }
  return max;
}
public float getMaxYInPVectorArray(PVector [] vectors) {
  float max = 0.0f;
  for (int i =0; i<vectors.length; i++) {
    if (vectors[i].x>max) {
      max = vectors[i].y;
    }
  }
  return max;
}

public PVector [] checkForDuplicates(PVector [] vectors) {
  ArrayList duplicates = new ArrayList();
  for (int i =0; i<vectors.length-1; i++) {
    if (vectors[i].x == vectors[i+1].x && vectors[i].y == vectors[i+1].y) {
      //println(i+"has a duplicate"); 
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
public int [] getDuplicateIndices(PVector [] vectors) {
  ArrayList duplicates = new ArrayList();
  PrintWriter output;

  output = createWriter("duplicates.txt");
  for (int i =0; i<vectors.length-1; i++) {
    if (vectors[i].x == vectors[i+1].x && vectors[i].y == vectors[i+1].y) {
      //println(i+"has a duplicate"); 
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

public boolean isARepeat(int [] duplicates, int index) {
  boolean repeat = false;
  for (int i =0; i<duplicates.length; i++) {
    if (duplicates[i] ==  index) {
      repeat = true;
    }
  }
  return repeat;
}


public PVector getLastNonIdenticalPoint(PVector thisPoint, int thisIndex) {
  PVector returnPoint = new PVector();
  for ( int i=thisIndex; i>=0; i--) {
    if (sendPoints[i].x!= thisPoint.x && sendPoints[i].y!=thisPoint.y) {
      returnPoint = sendPoints[i];
      break;
    }
  }
  return returnPoint;
}

public void loadDoc(String fname) {

  timeOut = millis();

  xSize = scales[fileCounter];
  ySize = 1.3f*scales[fileCounter];

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

public void startNewImage() {
  count =0;
  PVector startOver = new PVector(0, 0, 0);
  sendPoint(startOver.x, startOver.y, 0, 0);
  sendPenToggle((int)startOver.z);
  if (fileCounter>=scales.length) fileCounter = 0;
  loadDoc(fnames[fileCounter]);
  timeOut = millis();
}

  static public void main(String[] passedArgs) {
    String[] appletArgs = new String[] { "xml_to_machine_v5" };
    if (passedArgs != null) {
      PApplet.main(concat(appletArgs, passedArgs));
    } else {
      PApplet.main(appletArgs);
    }
  }
}
