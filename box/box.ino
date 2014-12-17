/* nETSpresso

Programme de contrôle de la machine à expresso «nETSpresso»

*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>
#include "EmonLib.h"
#include <SoftwareSerial.h>
#include <JsonGenerator.h>
//#include <MemoryFree.h>
using namespace ArduinoJson::Generator;
#include <JsonParser.h>
//using namespace ArduinoJson::Parser;

// Aruidno output pins
#define LED_SWITCH 5
#define REL_LOCK 6
#define REL_WARM 7

// Relay states
#define REL_WARM_STATE 0
#define REL_LOCK_STATE 1

// Create a software serial port!
SoftwareSerial lcd = SoftwareSerial(0,2);

// Adresse IP serveur distant:
char server[] = "192.168.1.57";

// Adresse MAC Arduino Ethernet:
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0E, 0xDC, 0x1E };
  
// Adresse IP par défaut de arduino si DHCP indisponible:
IPAddress ip(192,168,1,20);

// Initialisation de la librairie du client Ethernet:
EthernetClient client;

// Capteur de courant
float Irms1;
float Irms2;
float calib = 42.55;
EnergyMonitor emon1;
EnergyMonitor emon2;

int compteur = 0;
int flag = 0;
int temp = 0;
int manual_mode = 0;

int relays_state[] = {0,0};

#define STANDBY 0
#define WARMING 1
#define READY   2
#define COOLING 3
#define LOCKED  4

int state=STANDBY;

#define DO_NOTHING 0
#define WARM_UP    1
#define COOL_DOWN  2
#define LOCK_DOWN  3
#define OVERRIDE   4
#define NET_ERROR  5

int action=DO_NOTHING;

//--------------------------//
// ROUTINE D'INITIALISATION //
//--------------------------//

void setup()
{
  delay(3000);
  // Initialisation communication sérielle et attente de l'ouverture du port:
  Serial.begin(9600);
  Serial.println(F("Memory Début" ));
  
  Serial.println(freeRam() );
  // Initialise switch LED
  pinMode(LED_SWITCH, OUTPUT);
  
  // Initialise les sorties pour les relais
  pinMode(REL_LOCK, OUTPUT);
  pinMode(REL_WARM, OUTPUT);  
  
  lcd.begin(9600);
  
  // set the size of the display if it isn't 16x2 (you only have to do this once)
  lcd.write(0xFE);
  lcd.write(0xD1);
  lcd.write(16); // 16 columns
  lcd.write(2); // 2 rows
  delay(10);
  // we suggest putting delays after each command to make sure the data
  // is sent and the LCD is updated.
   
  // set the contrast
  lcd.write(0xFE);
  lcd.write(0x50);
  lcd.write(220);
  delay(10);
  
  // set the brightness
  lcd.write(0xFE);
  lcd.write(0x99);
  lcd.write(255);
  delay(10);
  
  // turn off cursors
  lcd.write(0xFE);
  lcd.write(0x4B);
  lcd.write(0xFE);
  lcd.write(0x54);
  delay(10);
  
  // clear screen
  lcd.write(0xFE);
  lcd.write(0x58);
  delay(10);
  
  // go 'home'
  lcd.write(0xFE);
  lcd.write(0x48);
  delay(10);

  delay(1000);

  // Message de bienvenue sériel:
  Serial.println(F(" -----------------------"));
  Serial.println(F(" Bienvenue à nETSpresso!"));
  Serial.println(F(" -----------------------"));
  Serial.println();
  Serial.println(F("--> Initialisation, veuillez patienter..."));
  
  // Message de bienvenue sur LCD:
  lcd.print("   nETSpresso");
  
  // Configuration du capteur de courant:  
  emon1.current(5, calib); // (Pin number, calibration value)
  emon2.current(4, calib); // (Pin number, calibration value)
  
  // Configuration de la connection ethernet:  
  if (Ethernet.begin(mac) == 0) // (Mac address)
  {
    Serial.println(F("--> DHCP indisponible"));
    Serial.println(F("--> Tentative de connection avec adresse IP par défaut:"));

    Ethernet.begin(mac, ip); // (Mac address, default arduino ip number)
  }

  /*
  // Délais pour initialisation du Ethernet Shield:
  Serial.println(F("--> Connection..."));
  delay(2000);
  
  // Si la connection est réussie, affichage sur terminal sériel:
  if (client.connect(server, 80)) // (Serveur distant, port)
  {
    Serial.println(F("--> Connecté"));
  }
  
  // Si la connection a échouée
  else
  {
    Serial.println(F("--> Connection impossible!"));
  }
  */
  
  for(int i=0; i<4; i++){
    digitalWrite(LED_SWITCH, HIGH);
    delay(200);
    digitalWrite(LED_SWITCH, LOW);
    delay(400);
  }
  
  set_state(STANDBY);
  Serial.println(F("Memory Fin" ));
  Serial.println(freeRam() );
}

//--------------------//
// TEMPERATURE SENSOR //
//--------------------//
int temperature()
{
  // Initialisation du thermocouple:
  AD595 thermocouple;

  Serial.println(F("TEMPERATURE (°C):"));
  temp = thermocouple.measure(TEMPC);
  Serial.println(temp);
  Serial.println("");
  return temp;
}

//------------------------------//
// MAIN POWER AC CURRENT SENSOR //
//------------------------------//
float ac_power()
{
  Serial.println(F("AC POWER CURRENT SENSOR (A):"));
  Irms1 = emon1.calcIrms(1480);
  Serial.println(Irms1);
  Serial.println("");
  return Irms1;
}
  
//-------------------------------//
// MANUAL MODE AC CURRENT SENSOR //
//-------------------------------//
float ac_manual()
{
  Serial.println(F("AC MANUAL MODE CURRENT SENSOR (A):"));
  Irms2 = emon2.calcIrms(1480);
  Serial.println(Irms2);
  Serial.println("");
  return Irms2;
}

//-------------------------//
// ROUTINE D'AFFICHAGE LCD //
//-------------------------//
void update_lcd_display(int state)
{
  // Couleur
  lcd.write(0xFE);
  lcd.write(0xD0);
  lcd.write(0x1);
  lcd.write(0x255);
  lcd.write(0x1);
  // Efface l'écran
  lcd.write(0xFE);
  lcd.write(0x58);
  // Curseur au début
  lcd.write(0xFE);
  lcd.write(0x48);
  // Affiche température
  lcd.print("Temperature: ");
  lcd.print(temp);
  lcd.write(0xFE);
  lcd.write(0x47);
  lcd.write(1);
  lcd.write(2);
  
  switch(state){
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
void relay_activate(int relay)
{
  digitalWrite(relay,HIGH);
  switch (relay) {
    case REL_WARM:
      relays_state[REL_WARM_STATE] = 1;
      break;
    case REL_LOCK:
      relays_state[REL_LOCK_STATE] = 1;
      break;
  }
  
}

//------------------//
// RELAY DEACTIVATE //
//------------------//
void relay_deactivate(int relay)
{
  digitalWrite(relay,LOW);
    switch (relay) {
    case REL_WARM:
      relays_state[REL_WARM_STATE] = 0;
      break;
    case REL_LOCK:
      relays_state[REL_LOCK_STATE] = 0;
      break;
  }
}

//-----------//
// SET STATE //
//-----------//
void set_state(int new_state)
{
   state = new_state;
   update_lcd_display(state);
}

//-----------//
// GET STATE //
//-----------//
int get_state()
{
  return state;
}

//-----------------//
// GET STATE LABEL //
//-----------------//
// String get_state_label()
// {
//   switch(get_state()){
//       case STANDBY:
//       return "Stand-By";
//       break;
//     
//     case WARMING:
//       return "Warming-Up";
//       break;
//     
//     case READY:
//       return "Ready";
//       break;
//     
//     case COOLING:
//       return "Cooling-Down";
//       break;
//     
//     case LOCKED:
//       return "Locked";
//       break;
//     
//     default:
//       return "Stand-By";
//       break;
//   }
// }

//-------------------//
// CHECK MANUAL MODE //
//-------------------//
void check_manual_mode()
{
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
int manual_cycles()
{
  return manual_mode;
}

//---------------------//
// ROUTINE D'ÉVÉNEMENT //
//---------------------//

int send_event()
{
  Serial.println(F("Memory event debut" ));
  Serial.println(freeRam() );
  //int temp;
  Serial.println(F(" *** Événement ***"));
  Serial.println();
  delay(500);
  
  client.stop();
  if (client.connect(server, 80)) {
    Serial.println(F("--> Connecté!"));
  }
  else
  {
    // Si la connection au serveurs est impossible:
    Serial.println(F("--> Déconnecté du serveur..."));
    int trying = 0;
    do {
      Serial.println(F("--> Tentative de reconnection..."));
      delay(1000);
      if(trying++ > 5) return NET_ERROR;
    } while (!client.connect(server,80));
    Serial.println(F("--> Reconnecté!"));
  } 
  Serial.println(F("Memory connect client fin" ));
  Serial.println(freeRam() ); 
  
  // Capture de la température
  temp = temperature();
  
  // Capture du courant
  Irms1 = ac_power();
  Irms2 = ac_manual();
  
  // Current state
  // char state_label[13] = { '\0' };
  // get_state_label().toCharArray(state_label, 13);
  
  Serial.println(get_state());
  // Affichage sur LCD
  //aff();
/*
  // Controle des relais
  relay_activate(REL_LOCK);
  delay(1000);
  relay_deactivate(REL_LOCK);
  delay(1000);
  relay_activate(REL_WARM);
  delay(1000);
  relay_deactivate(REL_WARM);
  delay(1000);
*/  
  
  // Informations à transférer au serveur:
  //PostData="{\"temperature\":temp,\"unite1\":\"degre_celcius\",\"courant\":Irms,\"unite2\":\"Amperes\"}";
  
  //JsonObject<4> power;
  //power["sensor"] = "netspresso01.ac_power";
  //power["value"] = "110";
  //power["units"] = "Volts";
  //power["adquired"] = "2014-08-09T05:46:06-0400";
  
  //JsonArray<1> metrics;
  //metrics.add(power);
  Serial.println(F("Memory json debut" ));
  Serial.println(freeRam() );
  
  JsonObject<4> sensors;
  
  JsonArray<1> leds;
  JsonArray<2> relays;
  JsonArray<2> current;
  JsonArray<1> temperatures;
  
  leds.add(0);
  relays.add(relays_state[REL_WARM_STATE]);
  relays.add(relays_state[REL_LOCK_STATE]);
  
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

  
  char databuffer[299] = { '\0' };
  //char *databuffer = new char[200];
  data.printTo(databuffer, sizeof(databuffer));
   
  //free(&power);
  //free(&metrics);
  //free(&box);
  //delete(&data);
  
  //String PostData;
  //String PostData="{\"path\":\"netspresso.relay.01\",\"value\":3,\"units\":\"watts\",\"epoch\":\"2014-08-09T05:46:06-0400\"}";
  //String PostData ="{\"box\":{\"name\":\"netspresso01\",\"state\":\"Ready\",\"temperature\":\"30\"}}";
  Serial.print(F("Requete au serveur:"));
  Serial.println(databuffer);
  Serial.println(strlen(databuffer));
  
  // Requête HTTP:
  Serial.println(F("Memory http" ));
  Serial.println(freeRam() );
  client.println(F("POST /netspresso/heartbeat.json HTTP/1.1"));
  client.println(F("Host: 192.168.1.57")); 
  client.println(F("Content-Type: application/json;"));
  client.print(F("Content-Length: "));


  client.println(strlen(databuffer));
  //client.println("Connection: close"); 
  client.println();   
  //client.println(PostData);
  //client.println(strdata);
  client.println(databuffer);
  //data.printTo(client);
  Serial.println(F("Memory http fin" ));
  Serial.println(freeRam() );

  Serial.println("");
  Serial.println(F("Délais d'attente..."));

  delay(5000);  

  while(!client.available()) {
/*  Serial.println(F("--> Déconnecté du client, tentative de reconnection..."));
    //client.stop();
    delay(500);
    if (client.connect(server, 80))
    {
      Serial.println(F("--> Connecté!"));
    }
    else
    {
      // Si la connection au serveurs est impossible:
      Serial.println(F("--> Connection impossible..."));
      //return;
    } 
*/}
  
  Serial.print(F("Réponse du serveur:"));
  
  //char json[99] = { '\0' };
  memset(databuffer, 0, sizeof(databuffer));
  int i = 0;
  
  while(client.available())
  {
    char c = client.read();    
    if((c == '{') or (i > 0)){
      //json[i] = c;
      databuffer[i] = c;
      i = i + 1;
    }
    Serial.print(c);
  }

  //Serial.println(json);
  Serial.println(databuffer);
  Serial.println(F("Memory" ));
  Serial.println(freeRam() );
  // Parse the Json response
  ArduinoJson::Parser::JsonParser<16> parser;
  //char json[99] = "{\"response\":{\"code\":\"002\",\"message\":\"Cold-Down\"}}";
 
  ArduinoJson::Parser::JsonObject root = parser.parse(databuffer);
  if (!root.success())
  {
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
void warm_up()
{
 if (temperature() < 100) {
    relay_activate(REL_WARM);
    set_state(WARMING);
    Serial.println("--> WARMING UP");
 }
}

//-----------//
// COOL DOWN //
//-----------//
void cool_down()
{
 relay_deactivate(REL_WARM);
 set_state(COOLING);
 Serial.println("--> COOLING DOWN");
}

//-----------//
// LOCK DOWN //
//-----------//
void lock_down()
{
 relay_activate(REL_LOCK);
 relay_deactivate(REL_WARM);
 set_state(LOCKED);
 Serial.println("--> MACHINE LOCKED");
}

//----------//
// OVERRIDE //
//----------//
void override()
{
 relay_deactivate(REL_LOCK);
 relay_deactivate(REL_WARM);
 set_state(STANDBY);
 Serial.println("--> STANDBY");
}


void net_error()
{
	
	Serial.println("--> NET_ERROR");
}

//-----------------//
// HOLD STATE (ok) //
//----------------//
void hold_state()
{
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
      //if (temperature() > 120) {
      //  cool_down();
      //}
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

//--------------------//
// ROUTINE PRINCIPALE //
//--------------------//

void loop()
{
  do
  {
    delay(4000);
    Serial.println(F("Memory loop begin (memory disp)" ));
    Serial.println(freeRam() );

    update_lcd_display(get_state());
    
    Serial.println(F("Memory loop end" ));
    Serial.println(freeRam() );

    
    action = send_event();
    
    switch(action){
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
    
    check_manual_mode();
    

  } while(true);  
}


