/* nETSpresso

Programme de contrôle de la machine à expresso «nETSpresso»

*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>
#include "EmonLib.h"
#include <SoftwareSerial.h>

#define RELAY1 6
#define RELAY2 7

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
float Irms1;
float Irms2;
float calib = 42.55;
EnergyMonitor emon1;
EnergyMonitor emon2;


int compteur = 0;
int flag = 0;
int temp = 0;

//--------------------------//
// ROUTINE D'INITIALISATION //
//--------------------------//

void setup()
{
  // Initialise les sorties pour les relais
  pinMode(RELAY1, OUTPUT);
  pinMode(RELAY2, OUTPUT);  
  
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

//-----------------------------------//
// ROUTINE DU CAPTEUR DE TEMPÉRATURE //
//-----------------------------------//
int temperature()
{
  //int temp;
  Serial.println("Température:");
  temp = thermocouple.measure(TEMPC);
  Serial.println(temp);
  Serial.println("");
  return temp;
}

//---------------------------------//
// ROUTINE DU CAPTEUR DE COURANT 1 //
//---------------------------------//
float capt1()
{
  Serial.println("Courant RMS Pin 5:");
  Irms1 = emon1.calcIrms(1480);
  Serial.println(Irms1);
  Serial.println("");
  return Irms1;
}
  
//---------------------------------//
// ROUTINE DU CAPTEUR DE COURANT 2 //
//---------------------------------//
float capt2()
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
void aff()
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
}

//---------------------//
// ROUTINE DU RELAIS 1 //
//---------------------//
void rel1()
{
  digitalWrite(RELAY1,HIGH);
  delay(1000);
  digitalWrite(RELAY1,LOW);
}

//---------------------//
// ROUTINE DU RELAIS 2 //
//---------------------//
void rel2()
{
  digitalWrite(RELAY2,HIGH);
  delay(1000);
  digitalWrite(RELAY2,LOW);
}

//---------------------//
// ROUTINE D'ÉVÉNEMENT //
//---------------------//

void event()
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
  Irms1 = capt1();
  Irms2 = capt2();
  
  // Affichage sur LCD
  aff();

  // Controle des relais
  rel1();
  rel2();
  
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
    event();
  } while(true);  
}
