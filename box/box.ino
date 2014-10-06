/* nETSpresso

Programme de contrôle de la machine à expresso «nETSpresso»

*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>
#include "EmonLib.h"
#include <SoftwareSerial.h>

#define REL_LOCK 6
#define REL_WARM 7

// Create a software serial port!
SoftwareSerial lcd = SoftwareSerial(0,2); 

// Adresse MAC Arduino Ethernet:
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0E, 0xDC, 0x1E };

// Adresse IP serveur distant:
char server[] = "192.168.0.104";

// Adresse IP par défaut de arduino si DHCP indisponible:
IPAddress ip(192,168,0,177);

// Initialisation de la librairie du client Ethernet:
EthernetClient client;

// Initialisation du thermocouple:
AD595 thermocouple;

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

#define STANDBY 0
#define WARMING 1
#define READY 2
#define COOLING 3
#define LOCKED 4

int state=STANDBY;

#define DO_NOTHING 0
#define WARM_UP 1
#define COOL_DOWN 2
#define LOCK_DOWN 3
#define OVERRIDE 4

int action=DO_NOTHING;

//--------------------------//
// ROUTINE D'INITIALISATION //
//--------------------------//

void setup()
{
  // Initialise les sorties pour les relais
  pinMode(REL_LOCK, OUTPUT);
  pinMode(REL_WARM, OUTPUT);  
  
  // Initialisation communication sérielle et attente de l'ouverture du port:
  Serial.begin(9600);
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
  lcd.write(200);
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

  delay(5000);

  // Message de bienvenue sériel:
  Serial.println(" -----------------------");
  Serial.println(" Bienvenue à nETSpresso!");
  Serial.println(" -----------------------");
  Serial.println("");
  Serial.println("--> Initialisation, veuillez patienter...");
  
  // Message de bienvenue sur LCD:
  lcd.print("   nETSpresso");
  
  // Configuration du capteur de courant:  
  emon1.current(5, calib); // (Pin number, calibration value)
  emon2.current(4, calib); // (Pin number, calibration value)
  /*
  // Configuration de la connection ethernet:
  if (Ethernet.begin(mac) == 0) // (Mac address)
  {
    Serial.println("--> DHCP indisponible");
    Serial.println("--> Tentative de connection avec adresse IP par défaut:");
    Ethernet.begin(mac, ip); // (Mac address, default arduino ip number)
  }
  
  // Délais pour initialisation du Ethernet Shield:
  Serial.println("--> Connection...");
  delay(2000);
  
  // Si la connection est réussie, affichage sur terminal sériel:
  if (client.connect(server, 80)) // (Serveur distant, port)
  {
    Serial.println("--> Connecté");
  }
  
  // Si la connection a échouée
  else
  {
    Serial.println("--> Connection impossible!");
  }*/
}

//--------------------//
// TEMPERATURE SENSOR //
//--------------------//
int temperature()
{
  //int temp;
  Serial.println("Température:");
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
  Serial.println("Courant RMS Pin 5:");
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
  Serial.println("Courant RMS Pin 4:");
  Irms2 = emon2.calcIrms(1480);
  Serial.println(Irms2);
  Serial.println("");
  return Irms2;
}

//-------------------------//
// ROUTINE D'AFFICHAGE LCD //
//-------------------------//
void update_lcd_display()
{
  // Couleur rouge
  lcd.write(0xFE);
  lcd.write(0xD0);
  lcd.write(0x255);
  lcd.write(0x255);
  lcd.write(0x255);
  // Efface l'écran
  lcd.write(0xFE);
  lcd.write(0x58);
  // Curseur au début
  lcd.write(0xFE);
  lcd.write(0x48);
  // Affiche température
  lcd.print("Temperature= ");
  lcd.print(temp);
  
  
  switch(get_state()){
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
}

//------------------//
// RELAY DEACTIVATE //
//------------------//
void relay_deactivate(int relay)
{
  digitalWrite(relay,LOW);
}

//-----------//
// SET STATE //
//-----------//
void set_state(int new_state)
{
   state = new_state;
   update_lcd_display();
   Serial.println("--> WARMING UP");
}

//-----------//
// GET STATE //
//-----------//
int get_state()
{
  return state;
}


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
  String PostData;
  //String PostData="{\"path\":\"netspresso.relay.01\",\"value\":3,\"units\":\"watts\",\"epoch\":\"2014-08-09T05:46:06-0400\"}";
  //int temp;
  Serial.println("Event!");
  Serial.println("");
  delay(500);
  
  // Capture de la température
  temp = temperature();
  
  // Capture du courant
  Irms1 = ac_power();
  Irms2 = ac_manual();
  
  // Affichage sur LCD
  //aff();

  // Controle des relais
  relay_activate(REL_LOCK);
  delay(1000);
  relay_deactivate(REL_LOCK);
  delay(1000);
  relay_activate(REL_WARM);
  delay(1000);
  relay_deactivate(REL_WARM);
  delay(1000);
  
  /*
  // Informations à transférer au serveur:
  PostData="{\"temperature\":temp,\"unite1\":\"degre_celcius\",\"courant\":Irms,\"unite2\":\"Amperes\"}";
  Serial.println(PostData);
  
  // Requête HTTP:
  client.println("POST /netspresso/metric.json HTTP/1.1");
  client.println("Host: 192.168.0.104"); 
  client.println("Connection: close");
  client.println("Content-Type: application/json;");
  client.print("Content-Length: ");
  client.println(PostData.length());
  client.println();   
  client.println(PostData);

  Serial.println("Délais d'attente...");  
  delay(1000);

  Serial.print("Réponse du serveur:");
  while(client.available())
  {
    char c = client.read();
    Serial.print(c);
  }
  Serial.println("");*/
  
  return DO_NOTHING;
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

//-----------------//
// HOLD STATE (ok) //
//----------------//
void hold_state()
{
  switch(get_state()) {
    case STANDBY:
      if ((temperature() > 100) && (manual_cycles() > 90)) {
        set_state(READY);
      }
      break;
    
    case WARMING:
      if (temperature() > 100) {
        set_state(READY);
      }
      break;
    
    case READY:
      if (temperature() > 120) {
        cool_down();
      }
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
  
 set_state(STANDBY);
 Serial.println("--> STANDBY");
}

//--------------------//
// ROUTINE PRINCIPALE //
//--------------------//

void loop()
{
  do
  {
    delay(1000);
    //delay(5000);
    /*
    // if the server's disconnected, stop the client:
    if (!client.connected())
    {
      Serial.println();
      Serial.println("--> Déconnecté...");
      Serial.println("");
      client.stop();
      
      if (client.connect(server, 80))
      {
        Serial.println("--> Connecté");
      }
      else
      {
        // Si la connection au serveurs est impossible:
        Serial.println("--> Connection impossible!");
        //return;
      }  
    }*/
    
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
      default:
        hold_state();
    }
    
    check_manual_mode();
    

  } while(true);  
}
