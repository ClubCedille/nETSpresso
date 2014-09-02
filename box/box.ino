/* nETSpresso

Programme de contrôle de la machine à expresso «nETSpresso»
par le club étudiant CEDILLE de l'École de Technologie Supérieure

Fonctionnement: TODO
*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>
#include "EmonLib.h"                   // Include Emon Library

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
float calib = 4.65;
EnergyMonitor emon1;

int compteur = 0;
int flag = 0;

//--------------------------//
// ROUTINE D'INITIALISATION //
//--------------------------//

void setup()
{
  // Initialisation communication sérielle et attente de l'ouverture du port:
  Serial.begin(9600);
  delay(5000);

  // Message de bienvenue:
  Serial.println(" -----------------------");
  Serial.println(" Bienvenue à nETSpresso!");
  Serial.println(" -----------------------");
  Serial.println("");
  Serial.println("--> Initialisation, veuillez patienter...");
  
  // Configuration du capteur de courant:  
  emon1.current(5, calib); // (Pin number, calibration value)
  
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
  }
}

//---------------------//
// ROUTINE D'ÉVÉNEMENT //
//---------------------//

void event()
{
  String PostData;
  //String PostData="{\"path\":\"netspresso.relay.01\",\"value\":3,\"units\":\"watts\",\"epoch\":\"2014-08-09T05:46:06-0400\"}";
  int temp;
  double Irms;
  Serial.println("Event!");
  Serial.println("");
  delay(500);
  
  // Capture de la température
  Serial.println("Température:");
  temp = thermocouple.measure(TEMPC);
  Serial.println(temp);
  Serial.println("");
  
  // Capture du courant
  Serial.println("Courant RMS:");
  Irms = emon1.calcIrms(1480);
  Serial.println(Irms);
  Serial.println("");
  
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
  Serial.println("");
}

//--------------------//
// ROUTINE PRINCIPALE //
//--------------------//

void loop()
{
  do
  {
    delay(5000);
    
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
    }
    event();
  } while(true);  
}
