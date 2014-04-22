/* @pjs preload= /BloodAxeBooks_800/BXB821-LONPOE-01.jpg; */

PImage pic;
PImage blurPic;
PImage source;
PImage section;
float [] rads ;
PFont font;
int minRad= 50;
int maxRad = 65;
int numPoints = 20;
bool setupDone = false;
float midRad = ( (maxRad-minRad)*0.5)+minRad;
PGraphics pg;

void setup() {  
 //setupEverything();

}
void setupEverything(String url,int pic_width, int pic_height){
    noSmooth();

  size(pic_width, pic_height);
  //String url = "dear.png";
 
  blurPic = loadImage(url);  
  //size(blurPic.width, blurPic.height);
  pg = createGraphics(width, height);
  source = loadImage(url);  

  font = createFont("Serif", 48);
  textFont(font, 48);
  pic =createImage(width, height, ARGB);//loadImage("dear.png");  
  //source.format=ARGB;
  pic.loadPixels();
  source.loadPixels();
  for (int i =0;i<pic.pixels.length;i++) {
    pic.pixels[i] = source.pixels[i];
  }

  pic.updatePixels();
  source.updatePixels();

  //font = loadFont("Serif-12.vlw");
  //textFont(font, 12);
  rads= new float[numPoints];
  for (int i=0;i<rads.length;i++) {
    rads [i]= random(minRad, maxRad);
  }
  blurPic.filter(BLUR, 6);
  //  println (width);
  //  println(indexToXY(width, height, 1003));

setupDone = true;
//  println( getIndexListForBox(5, 5, 5, 5, 100, 100, pic.pixels.length));
}
void drawEllipse(){
  ellipse(50,50,50,50);
  //println("drawn");
}
void draw() {
  background(0);
  if(setupDone){
  int offsetX = 100;
  int offsetY = 30;
  int border =5;
  int offsetX2 = offsetX+border;
  int offsetY2 = offsetY +border;
  section = source.get(mouseX-offsetX,mouseY-offsetY,offsetX,offsetY);
  pic = source.get(mouseX-(border+offsetX),mouseY-(border+offsetY),(2*border)+offsetX,(2*border)+offsetY);
  pic.filter(BLUR,3);
  //draw the blurred background image
  image(blurPic, 0, 0);
  
  fill(0);
  image(pic, mouseX-(border+offsetX),mouseY-(border+offsetY));
  image(section, mouseX-offsetX,mouseY-offsetY);
  }
  
}
PVector indexToXY(int myWidth, int myHeight, int index) {

  int y = index/myWidth;
  int x = index -(y*myWidth);
  return new PVector(x, y);
}
int xyToIndex(int x, int y, int imageWidth) {
  return ((imageWidth*y)+x);
}
int [] getSurroundingPixels(int index, int myWidth, int myHeight) {
  int [] surround = new int [8];
  //indices from top left
  float x = indexToXY(myWidth, myHeight, index).x;
  float y = indexToXY(myWidth, myHeight, index).y;
  if (x>1 && x<width-1 && y>1 && y<height-1) {
    surround[0]= (index - myWidth)-1;
    surround[1]= (index - myWidth);
    surround[2]= (index - myWidth)+1;
    surround[3]= index+1;
    surround[4]= index + myWidth + 1;
    surround[5]= index + myWidth;
    surround[6]= index + myWidth -1;
    surround[7]= index -1;
  }
  return surround;
}
void test() {
  pic.loadPixels();
  color transparent = color(0, 0, 0, 0);
  for (int j =0;j<pic.pixels.length;j++) {
    if (j<100000) {
      pic.pixels[j]=transparent;
    }
  } 
  pic.updatePixels();
}

void getMask(int numLegs, float[] rads, float rad, float xPos, float yPos) {

  pg.beginDraw();
  pg.fill(0);
  pg.rect(0, 0, width, height);
  pg.pushStyle();
  pg.noStroke();
  pg.fill(255, 0, 0);

  float angle = TWO_PI/numLegs;

  pg.beginShape();

  float x;
  float y;

  for (int i=0;i<numLegs;i++) {

    x = rads[i] * sin(angle*i);
    y = rads[i] * cos(angle*i);
    pg.vertex(xPos+x, yPos+ y);

    if (i<numLegs-1) {
      x = midRad * sin((angle*i)+(0.5*angle));
      y = midRad * cos((angle*i)+(0.5*angle));
      pg.vertex(xPos+x, yPos+ y);
    }
  }

  int i =0;

  x = rads[i] * sin((angle*i));
  y = rads[i] * cos((angle*i));

  pg.vertex(xPos+x, yPos+ y);
  pg.vertex(xPos+x, height);
  pg.vertex(0, height);
  pg.vertex(0, 0);
  pg.vertex(width, 0);
  pg.vertex(width, height);
  pg.vertex(xPos+x, height);
  pg.vertex(xPos+x, yPos+y);


  pg.endShape();
  pg.loadPixels();
  pic.loadPixels();

  color black = color(0, 0, 0);
  color transparent = color(0, 0, 0, 0);
  int mygreen;
  int myred;
  int myblue;
  int [] surrounding;

  int baseX = mouseX - maxRad;
  int baseY = mouseY - maxRad;

   int [] indices = getIndexListForBox(baseX, baseY, maxRad*2, maxRad*2, pic.width, pic.height, pic.pixels.length);
 
  for (int j =0;j<indices.length;j++) {
    myred = pg.pixels[indices[j]]  >> 16 & 0xFF;
    mygreen = pg.pixels[indices[j]] >> 8 & 0xFF;
    myblue = pg.pixels[indices[j]] & 0xFF;
    if ( myred >= 250 ) {//&& myblue <15 && mygreen <15) {
      pic.pixels[indices[j]]= transparent;
   
    }
  }
  pic.updatePixels();
  pg.updatePixels();
  pg.endDraw();
}
int [] getIndexListForBox(int x, int y, int w, int h, int imageW, int imageH, int limit) {
  int count = 0;
  int [] aList = new int [w*h];
  for (int i=0;i<w;i++) {
    for (int j=0;j<h;j++) {
      int myx = x+i;
      int myy = (y+j);
      // println(myx+" "+myy);
      int index=xyToIndex(myx, myy, imageW); 
      if(index>=0&&index<limit) {
        aList[count]= index   ;//(myy*imageW)+myx ;
        count++;
      }
    }
  }
  
  return aList;
}
void updateRads() {
  for (int i=0;i<rads.length;i++) {
    rads[i] += random(-2.0, 2.0)  ;//noise(rads[i])*rads[i];
  }
}


