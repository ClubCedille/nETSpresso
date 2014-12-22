
/*
      ----------------------
      « nETSpresso » Project
      ----------------------

    Automatic preheat control for CRITIAS' "Mokita café crème" espresso machine
    Built to work using CEDILLE's custom GroupOffice plugin and server
    Designed and built by CEDILLE

    CRITIAS: Chaire de recherche industrielle en technologies intra-auriculaire Sonomax-ÉTS
    CEDILLE: Club d'expérimentation Devops et d'intégration de logiciels libres et embarqués

    École de Technologie Supérieure
*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>
#include "EmonLib.h"
#include <SoftwareSerial.h>
#include <JsonGenerator.h>
using namespace ArduinoJson::Generator;
#include <JsonParser.h>

// Aruidno output pins
#define LED_SWITCH 5
#define REL_LOCK 6
#define REL_WARM 7

// Relay states
#define REL_WARM_STATE 0
#define REL_LOCK_STATE 1

// Software serial port
SoftwareSerial lcd = SoftwareSerial(0,2);

// IP Address of nETSpresso server
char server[] = "192.168.1.57";

// Arduino Ethernet's MAC address
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0E, 0xDC, 0x1E };
  
// Default IP if no DHCP
IPAddress ip(192,168,1,20);

// Initialisation of Ethernet Client Library
EthernetClient client;

// Current sensors
float Irms1;
float Irms2;
float calib = 42.55;
EnergyMonitor emon1;
EnergyMonitor emon2;

// miscellaneous variables
int compteur = 0;
int flag = 0;
int temp = 0;
int manual_mode = 0;
int relays_state[] = {0,0};

// nETSpresso "states"
#define STANDBY 0
#define WARMING 1
#define READY   2
#define COOLING 3
#define LOCKED  4

// Initial state
int state=STANDBY;

// nETSpresso "actions"
#define DO_NOTHING 0
#define WARM_UP    1
#define COOL_DOWN  2
#define LOCK_DOWN  3
#define OVERRIDE   4
#define NET_ERROR  5

// Initial action
int action=DO_NOTHING;

//--------------//
// System setup //
//--------------//

void setup() {
  
  delay(3000);
  
  // Start serial communication
  Serial.begin(9600);

  // Initialize switch LED
  pinMode(LED_SWITCH, OUTPUT);
  
  // Initialize relay outputs
  pinMode(REL_LOCK, OUTPUT);
  pinMode(REL_WARM, OUTPUT);  
  
  // Initialize LCD
  lcd.begin(9600);
  
  // Set Size of LCD display
  lcd.write(0xFE);
  lcd.write(0xD1);
  lcd.write(16); // 16 columns
  lcd.write(2); // 2 rows
  delay(10); // This delay must exist after every commands
   
  // Set the contrast
  lcd.write(0xFE);
  lcd.write(0x50);
  lcd.write(220);
  delay(10);
  
  // Set the brightness
  lcd.write(0xFE);
  lcd.write(0x99);
  lcd.write(255);
  delay(10);
  
  // Turn off cursors
  lcd.write(0xFE);
  lcd.write(0x4B);
  lcd.write(0xFE);
  lcd.write(0x54);
  delay(10);
  
  // Clear screen
  lcd.write(0xFE);
  lcd.write(0x58);
  delay(10);
  
  // Cursor go home
  lcd.write(0xFE);
  lcd.write(0x48);
  delay(10);

  // Display welcome message (serial)
  Serial.println(F(" ------------"));
  Serial.println(F("  nETSpresso"));
  Serial.println(F(" ------------"));
  Serial.println();
  Serial.println(F("--> Initializing..."));
  
  // Display welcome message (LCD)
  lcd.print("   nETSpresso");
  
  // Configuration of current sensors  
  emon1.current(5, calib); // (Pin number, calibration value)
  emon2.current(4, calib); // (Pin number, calibration value)
  
  // Configuration of ethernet connection  
  if (Ethernet.begin(mac) == 0) {
    Serial.println(F("--> No DHCP"));
    Serial.println(F("--> Connecting using default IP..."));
    Ethernet.begin(mac, ip); // (Mac address, default arduino ip number)
  }
  
  // Blink LED after setup
  for(int i=0; i<4; i++) {
    digitalWrite(LED_SWITCH, HIGH);
    delay(200);
    digitalWrite(LED_SWITCH, LOW);
    delay(400);
  }
  
  // Set initial state
  set_state(STANDBY);
}

//--------------------//
// TEMPERATURE SENSOR //
//--------------------//

int temperature() {
  
  // Initialize thermocouple
  AD595 thermocouple;
  
  // Get temperature
  temp = thermocouple.measure(TEMPC);
  
  // Print Temperature (Serial)
  Serial.println(F("TEMPERATURE (°C):"));
  Serial.println(temp);
  Serial.println("");
  
  return temp;
}

//------------------------------//
// MAIN POWER AC CURRENT SENSOR //
//------------------------------//

float ac_power() {

  // Get current for main power
  Irms1 = emon1.calcIrms(1480);
  
  // Print current (Serial output)
  Serial.println(F("AC POWER CURRENT SENSOR (A):"));
  Serial.println(Irms1);
  Serial.println("");
  
  return Irms1;
}
  
//-------------------------------//
// MANUAL MODE AC CURRENT SENSOR //
//-------------------------------//

float ac_manual() {

  // Get current for manual mode
  Irms2 = emon2.calcIrms(1480);

  // Print current (Serial output)
  Serial.println(F("MANUAL CURRENT SENSOR (A): "));
  Serial.println(Irms2);
  Serial.println("");
  
  return Irms2;
}

//---------------------//
// LCD DISPLAY ROUTINE //
//---------------------//

void update_lcd_display(int state) {
  
  // Color
  lcd.write(0xFE);
  lcd.write(0xD0);
  lcd.write(0x1);
  lcd.write(0x255);
  lcd.write(0x1);
  
  // Clear screen
  lcd.write(0xFE);
  lcd.write(0x58);
  
  // Cursor go home
  lcd.write(0xFE);
  lcd.write(0x48);
  
  // Display temperature
  lcd.print("Temperature: ");
  lcd.print(temp);
  lcd.write(0xFE);
  lcd.write(0x47);
  lcd.write(1);
  lcd.write(2);
  
  // Display state
  switch(state) {
    
    case STANDBY:
      lcd.print(" STAND BY");
      //blue
      break;
  
    case WARMING:
      lcd.print(" WARMING UP");
      // orange
      break;
  
    case READY:
      lcd.print(" READY");
      // green
      break;
  
    case COOLING:
      lcd.print(" COOLING");
      if (temperature() > 100) {
      // green
      break;
      }
      if (temperature() > 30) {
      // orange
      break;
      }
      break;
  
    case LOCKED:
      lcd.print(" LOCKED");
      // red
      break;
  
    case NET_ERROR:
      lcd.print(" NETWORK ERROR");
      // red
      break;
  
    default:
      break;
  }
}

//----------------//
// RELAY ACTIVATE //
//----------------//

void relay_activate(int relay) {

  // Activate relay
  digitalWrite(relay,HIGH);
  
  // Set relay state flag
  switch (relay) {
    
    // Flag for warming relay
    case REL_WARM:
      relays_state[REL_WARM_STATE] = 1;
      break;
    
    // Flag for locking relay
    case REL_LOCK:
      relays_state[REL_LOCK_STATE] = 1;
      break;
  }
}

//------------------//
// RELAY DEACTIVATE //
//------------------//

void relay_deactivate(int relay) {
  
  // Deactivate relay
  digitalWrite(relay,LOW);
  
  // Set relay state flag
  switch (relay) {

    // Flag for warming relay
    case REL_WARM:
      relays_state[REL_WARM_STATE] = 0;
      break;

    // Flag for locking relay
    case REL_LOCK:
      relays_state[REL_LOCK_STATE] = 0;
      break;
  }
}

//-----------//
// SET STATE //
//-----------//

void set_state(int new_state) {
  
  // Set a new state
  state = new_state;
  
  // Display new state (LCD)
  update_lcd_display(state);
}

//-----------//
// GET STATE //
//-----------//

int get_state() {
  
  // Return state
  return state;
}

//-------------------//
// CHECK MANUAL MODE //
//-------------------//

void check_manual_mode() {
  
  // Check if machine is in manual mode
  if (ac_manual() > 1) {
    manual_mode++;
  }
  else {
    manual_mode=0;
  }
}

//---------------//
// MANUAL CYCLES //
//---------------//

int manual_cycles() {
  
  // Returns manual mode value
  return manual_mode;
}

//-------//
// EVENT //
//-------//

int send_event() {
  
  // Display start of event (Serial)
  Serial.println();
  Serial.println(F(" *** EVENT ***"));
  Serial.println();
  delay(500);
  
  // Get temperature value
  temp = temperature();
  
  // Get current values
  Irms1 = ac_power();
  Irms2 = ac_manual();

  // Print state (Serial)
  Serial.println(get_state());
  
  // Json Object
  JsonObject<4> sensors;
  
  // Json Arrays
  JsonArray<1> leds;
  JsonArray<2> relays;
  JsonArray<2> current;
  JsonArray<1> temperatures;
  
  // Defining Json arrays
  leds.add(0);
  relays.add(relays_state[REL_WARM_STATE]);
  relays.add(relays_state[REL_LOCK_STATE]);
  
  // Defining Json Objects
  JsonObject<2> current_power;
  current_power["u"] = "A";
  current_power["v"] = Irms1;
  current.add(current_power);
  
  JsonObject<2> current_manual;
  current_manual["u"] = "A";
  current_manual["v"] = Irms2;
  current.add(current_manual);  
  
  JsonObject<1> main_temperature;
  main_temperature["u"] = "C";
  main_temperature["v"] = temp;
  temperatures.add(main_temperature); 
  
  sensors["led"] = leds;
  sensors["relay"] = relays;
  sensors["current"] = current;
  sensors["temperature"] = temperatures;
  
  JsonObject<3> box;
  box["n"] = "netspresso01";
  //box["state"] = "Ready";
  box["s"] = get_state();
  box["t"] = temp;
  
  JsonObject<2> data;
  data["box"] = box;
  data["sensors"] = sensors;

  // Initialize buffer
  char databuffer[299] = { '\0' };

  // Fill the buffer
  data.printTo(databuffer, sizeof(databuffer));

  // Display Json from buffer (Serial)
  Serial.print(F("Server request:"));
  Serial.println(databuffer);
  Serial.println(strlen(databuffer));
  
  // HTTP request:
  client.println(F("POST /netspresso/heartbeat.json HTTP/1.1"));
  client.println(F("Host: 192.168.1.57")); 
  client.println(F("Content-Type: application/json;"));
  client.print(F("Content-Length: "));
  client.println(strlen(databuffer));
  client.println();   
  client.println(databuffer);

  // Delay for server feedback
  Serial.println("");
  Serial.println(F("Delay..."));
  Serial.println("");
  delay(5000);

  while(!client.available())
  
  Serial.print(F("Answer from server:"));
  
  //char json[99] = { '\0' };
  memset(databuffer, 0, sizeof(databuffer));
  int i = 0;
  
  while(client.available()) {
    char c = client.read();    
    if((c == '{') or (i > 0)) {
      databuffer[i] = c;
      i = i + 1;
    }
    Serial.print(c);
  }

  //Serial.println(json);
  Serial.println(databuffer);
//Serial.println(F("Memory" ));
//Serial.println(freeRam() );
  // Parse the Json response
  ArduinoJson::Parser::JsonParser<16> parser;
 
  ArduinoJson::Parser::JsonObject root = parser.parse(databuffer);
  if (!root.success()) {
    Serial.println("JsonParser.parse() failed");
    return DO_NOTHING;
  }
  ArduinoJson::Parser::JsonObject response = root["response"];
  char* code = response["code"];
  
  Serial.print(F("Return code: "));
  Serial.println(atoi(code));
  
  return atoi(code);
}

//---------//
// WARM UP //
//---------//

void warm_up() {
  
  if (temperature() < 100) {
    relay_activate(REL_WARM);
    set_state(WARMING);
    Serial.println("--> WARMING UP");
  }
}

//-----------//
// COOL DOWN //
//-----------//

void cool_down() {
  
 relay_deactivate(REL_WARM);
 set_state(COOLING);
 Serial.println("--> COOLING DOWN");
}

//-----------//
// LOCK DOWN //
//-----------//

void lock_down() {
  
  relay_activate(REL_LOCK);
  relay_deactivate(REL_WARM);
  set_state(LOCKED);
  Serial.println("--> MACHINE LOCKED");
}

//----------//
// OVERRIDE //
//----------//

void override() {
  
  relay_deactivate(REL_LOCK);
  relay_deactivate(REL_WARM);
  set_state(STANDBY);
  Serial.println("--> STANDBY");
}

//-----------//
// NET_ERROR //
//-----------//

void net_error() {
  
  relay_deactivate(REL_WARM);
  set_state(NET_ERROR);
  //update_lcd_display(NET_ERROR);
  Serial.println("--> NET_ERROR");
}

//------------//
// HOLD STATE //
//------------//

void hold_state() {
  
  switch(get_state()) {
    
    case STANDBY:
      if ((temperature() > 100) && (manual_cycles() > 90)) {
        lock_down();
      }
      break;
    
    case WARMING:
      if (temperature() > 100) {
        set_state(READY);
      }
      break;
    
    case READY:
      break;
    
    case COOLING:
      if (temperature() < 30) {
        set_state(STANDBY);
      }
      break;
    
    case LOCKED:
      break;
    
    default:
      break;
  }
}

int freeRam() {
  extern int __heap_start, *__brkval;
  int v;
  return (int) &v - (__brkval == 0 ? (int) &__heap_start : (int) __brkval);
}

//--------------//
// CONNECTIVITÉ //
//--------------//

bool connect() {
  
    client.stop();
    
    if (client.connect(server, 80)) {
      Serial.println(F("--> Connecté!"));
      return true;
    } 
    
    else {
      Serial.println(F("--> Déconnecté..."));
      int trying = 0;
      do {
        Serial.println(F("--> Tentative de reconnection..."));
        delay(1000);
        trying++;
        if (trying >= 5) {
          return false;
        }
      } while (!client.connect(server,80));
    }
    return true;
}

//--------------------//
// ROUTINE PRINCIPALE //
//--------------------//

void loop() {
  
  do {

    if (!connect()) {
      action = NET_ERROR;
    }
    
    else {
      action = send_event();
    }
    
    Serial.println(F(""));
    Serial.println(F(" *** Switch after send_event() ***"));
    switch(action) {
      
      case WARM_UP:
        warm_up();
        break;
        
      case COOL_DOWN:
        cool_down();
        break;
        
      case LOCK_DOWN:
        lock_down();
        break;
        
      case OVERRIDE:
        override();
        break;
        
      case NET_ERROR:
        net_error();
        break;
        
      default:
        hold_state();
    }
    
    update_lcd_display(get_state());
    
    check_manual_mode();
    
  } while(true);  
}
