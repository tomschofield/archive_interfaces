
#include <AccelStepper.h>
#include <SoftwareSerial.h>
#include <Makeblock.h>
#include <Wire.h>
//port 1411
// Define a stepper and the pins it will use
AccelStepper stepperX(AccelStepper::DRIVER, 13, 12); // 13-PUL, 12-DIR PORT 3
AccelStepper stepperY(AccelStepper::DRIVER, 2, 8); // 2-PUL, 8-DIR, PORT 4
//MeBluetooth bluetooth(PORT_6);
int motorDrc = 4; //M1
int motorPwm = 5; //M1

int limitSW_X = A0; //PORT 7
int limitSW_Y = A1; //PORT 8



float maxSpeed = 100;
String inputString = "";         // a string to hold incoming data
boolean stringComplete = false;  // whether the string is complete
boolean penIsDown = false;
boolean sentThisPoint = true;
int pReadingX=0;
int pReadingY=0;
boolean started = false;
boolean moving = false;
boolean pmoving;
boolean lastTimeWasZero = false;
float speedX = 300;
float speedY = 300;
float readingx=0.0;
float readingy=0.0;
String targetX ="0";
String targetY="0";
int timeOut =0;
int timeOutThreshold = 500;
int notMovingCounter =0;
int thresh = 40;

void setup(){  
  Serial.begin(9600);
  Wire.begin(); // join i2c bus (address optional for master)
  // Wire.onReceive(receiveEvent);
  //this holds our input message
  inputString.reserve(200);
  pinMode(limitSW_X, INPUT);
  pinMode(limitSW_Y, INPUT);

  pinMode(motorDrc, OUTPUT);
  digitalWrite(motorDrc, HIGH);
  pinMode(motorPwm, OUTPUT);
  analogWrite(motorPwm, 0);

  delay(2000);
  initMotor();


}

void loop() {

  if(penIsDown){
    analogWrite(motorPwm, 200); 
  }
  else{
    analogWrite(motorPwm, 0); 
  }

  stepperX.runSpeedToPosition();
  stepperY.runSpeedToPosition();

  String distanceX = String((int)stepperX.distanceToGo());
  String distanceY = String((int)stepperY.distanceToGo());
  String sent = String(sentThisPoint);


  if(stepperX.distanceToGo()==0 && stepperY.distanceToGo() ==0 ){//&& readingx!=0 && readingy!=0){
    moving = false;
  }
  else{
    moving = true; 
  }
  //if it was moving and now isn't then ask for a new point
  if(moving!=pmoving && moving == false){
    String hasArrived = "0";
    if(stepperX.distanceToGo()==0 && stepperX.distanceToGo()==0){
      hasArrived="1";
    }

    Serial.println("request_new_point:"+targetX+":"+targetY+":"+hasArrived+":"+String((int)speedX)+":"+String((int)speedY));
  }
  pmoving = moving;

  //now lets check the message
  if (stringComplete) {
    //is it a pen up or pen down?
    if(inputString.charAt(0)=='U'){//.equals("togglepen")){
      penIsDown=false; 
      inputString = "";
      stringComplete = false;
    }
    else if(inputString.charAt(0)=='D'){
      penIsDown=true; 
      inputString = "";
      stringComplete = false;
    }
    //respond to request for distance
    else if(inputString.charAt(0)=='A'){
      String distanceX = String((int)stepperX.distanceToGo());
      String distanceY = String((int)stepperY.distanceToGo());
      String hasArrived = "0";
      if(stepperX.distanceToGo()==0 && stepperY.distanceToGo()==0){
        hasArrived="1";
      }
      inputString = "";
      stringComplete = false;
      Serial.println("dist:"+distanceX+":"+distanceY+":"+hasArrived+":"+String((int)speedX)+":"+String((int)speedY));

    }
    //reset speed
    else if(inputString.charAt(0)=='S'){
      Serial.println("last:"+String((int)pReadingX)+":"+String((int)pReadingY));
      inputString = "";
      stringComplete = false;


    }
    //transmit via wire to other arduino
    else if(inputString.charAt(0)=='P'){
      Serial.println("advancing stepper");
      transmitWire("P#");


    }
    //if it's none of the above, lets assume it's a 'go to this point' message
    else{
      int firstcomma = inputString.indexOf(',');
      if(inputString.charAt(inputString.length()-1) =='\n'){
        // Serial.println("inputString reads "+inputString);

        String ypos = inputString.substring(0,firstcomma);
        //surely this should break
        String xpos = inputString.substring(firstcomma+1,inputString.length());

        String secondSection = inputString.substring(firstcomma+1,inputString.length());

        firstcomma = secondSection.indexOf(',');

        String thirdSection = secondSection.substring(firstcomma+1,secondSection.length());
        firstcomma = thirdSection.indexOf(',');

        String xposlast = thirdSection.substring(0,firstcomma);
        String yposlast = thirdSection.substring(firstcomma+1,thirdSection.length());


        readingx =(float) xpos.toInt();
        readingy =(float) ypos.toInt();

        targetX = String((int)readingx);
        targetY = String((int)readingy);

        //this is intentional backwards x - y
        pReadingX = (float)xposlast.toInt();
        pReadingY = (float)yposlast.toInt();



        float mult = 1.0;//map(max(readingx,readingy),0,2000,0,500);

        float rawSpeedX = abs(mult * (readingx-pReadingX));
        float rawSpeedY = abs(mult * (readingy-pReadingY));
        //Serial.println("distance:"+String((int)readingx-pReadingX)+" "+String((int)readingy-pReadingY));

        float maxSpeed =150.0;


        speedX = rawSpeedX;//map(rawSpeedX, 0, max((rawSpeedX),(rawSpeedY)),0,maxSpeed);
        speedY = rawSpeedY;//map(rawSpeedY, 0, max((rawSpeedX),(rawSpeedY)),0,maxSpeed);


        stepperX.moveTo(readingx);
        stepperY.moveTo(readingy);

        stepperX.setSpeed((speedX));
        stepperY.setSpeed((speedY));

        sentThisPoint = false;

        inputString = "";
        stringComplete = false;




      }
    }
  }


}
void serialEvent() {
  while (Serial.available()) {
    // get the new byte:
    char inChar = (char)Serial.read(); 
    // add it to the inputString:
    inputString += inChar;
    // if the incoming character is a newline, set a flag
    // so the main loop can do something about it:
    if (inChar == '\n') {
      stringComplete = true;
    } 
  }
}


void initMotor(){
  Serial.println("setting max speed");
  stepperX.setMaxSpeed(1000);
  stepperX.setAcceleration(6000); // set X stepper speed and acceleration
  stepperY.setMaxSpeed(1000);
  stepperY.setAcceleration(6000); // set Y stepper speed and acceleration

  Serial.println("moving to -4000");

  stepperX.moveTo(-4000);
  stepperY.moveTo(-4000);// move XY to origin

  Serial.println("running ");
  stepperX.setSpeed(600);
  stepperY.setSpeed(600);

  while(digitalRead(limitSW_X))stepperX.runSpeedToPosition();
  while(digitalRead(limitSW_Y))stepperY.runSpeedToPosition();// scanning stepper motor

  Serial.println("finished run, reseting current pos");
  stepperX.setCurrentPosition(0);
  stepperY.setCurrentPosition(0); // reset XY position
  stepperX.setMaxSpeed(1000);
  stepperY.setMaxSpeed(1000);// set XY working speed
}




void transmitWire(String message){
  char charBuf[message.length()+1]; 
  // Serial.println("t_wire");
  Wire.beginTransmission(42); // transmit to device #4
  //Wire.write("writing to me base shield is ");        // sends five bytes
  message.toCharArray(charBuf, message.length()+1);
  // Serial.println("starting to write tranmsission");  // sends one byte  
  Wire.write(charBuf) ;      
  // Serial.println("written tranmsission");  // sends one byte  
  Wire.endTransmission();    
  // Serial.println("ended tranmsission");

}






