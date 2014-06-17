
//this one Thursday
#include <Wire.h>
#include <SoftwareSerial.h>
#include <Stepper.h>

//port1421
const int transistorPin = 7;    // connected to the base of the transistor
String fromWire="";
boolean penDown = false;
boolean goStepper = false;


int enA  = 3;  // Enable pin 1 on Motor Control Shield   
int enB  = 11;  // Enable pin 2 on Motor Control Shield   
int dirA = 12;  // Direction pin dirA on Motor Control Shield
int dirB = 13;  // Direction pin dirB on Motor Control Shield

const float stepsPerRevolution = 200;  // Change this to fit the number of steps per revolution
Stepper myStepper(stepsPerRevolution, dirA, dirB);
float roll_length =  -1;//-6.64;
void setup()

{
  Wire.begin(42);                // join i2c bus with address #4
  Wire.onReceive(receiveEvent); // register event
  myStepper.setSpeed(20);
  pinMode(enA, OUTPUT);
  digitalWrite (enA, HIGH);

  pinMode(enB, OUTPUT);
  digitalWrite (enB, HIGH);

}

void loop()
{

  if (goStepper) {
    Serial.println("start stepping");
    myStepper.step(roll_length * stepsPerRevolution);
    goStepper=false;
  }
}

// function that executes whenever data is received from master
// this function is registered as an event, see setup()
void receiveEvent(int howMany)
{
  while(1 < Wire.available()) // loop through all but the last
  {
    char c = Wire.read(); // receive byte as a character
    fromWire+=c;
    if (c=='P'){
      Serial.print("fromWire ");
      Serial.println(fromWire);
      goStepper = true;
      fromWire="";  
    }
    // Serial.print(" c ");
    // Serial.print(c);         // print the character
  }
  int x = Wire.read();    // receive byte as an integer
  //Serial.print(" x " ); 
  //Serial.println(x);         // print the integer
}

