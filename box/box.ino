/* nETSpresso

Programme de contrôle de la machine à expresso «nETSpresso»
par le club étudiant CEDILLE de l'École de Technologie Supérieure

Fonctionnement: TODO
*/

#include <SPI.h>
#include <Ethernet.h>
#include <AD595.h>

// Adresse MAC (tel qu'indiqué sous le Arduino):
byte mac[] = { 0x90, 0xA2, 0xDA, 0x0E, 0xDC, 0x1E };
// Adresse IP du serveur:
char server[] = "192.168.0.104";
// Adresse IP par défaut si DHCP est indisponible:
IPAddress ip(192,168,0,177);
// Initialisation de la librairie du client Ethernet:
EthernetClient client;
// Initialisation de la librairie du thermocouple:
AD595 thermocouple;
int compteur = 0;
int flag = 0;

//--------------------------//
// ROUTINE D'INITIALISATION //
//--------------------------//

void setup()
{
  // Open serial communications and wait for port to open:
  Serial.begin(9600);
  // Délais pour démarrer la communication sérielle manuellement (Facultatif):
  delay(5000);
  // Message de bienvenue:
  Serial.println(" -----------------------");
  Serial.println(" Bienvenue à nETSpresso!");
  Serial.println(" -----------------------");
  Serial.println("");
  Serial.println("--> Initialisation, veuillez patienter...");
  // Démarrage de la connection Ethernet:
    if (Ethernet.begin(mac) == 0) {
    Serial.println("--> DHCP indisponible");
    Serial.println("--> Tentative de connection avec adresse IP par défaut:");
    Ethernet.begin(mac, ip);
  }
  // Délais pour initialisation du Ethernet Shield:
  delay(1000);
  Serial.println("--> Connection...");
  // Si la connection est réussie, affichage sur terminal sériel:
  if (client.connect(server, 80))
  {
    Serial.println("--> Connecté");
  }
  else
  {
    // Si la connection au serveurs est impossible:
    Serial.println("--> Connection impossible!");
  }  

  // Configuration des interruptions
  cli(); //stop interrupts
  //set timer1 interrupt at 1Hz
  TCCR1A = 0;// set entire TCCR1A register to 0
  TCCR1B = 0;// same for TCCR1B
  TCNT1  = 0;//initialize counter value to 0
  // set compare match register for 1hz increments
  OCR1A = 15624;// = (16*10^6) / (1*1024) - 1 (must be <65536)
  // turn on CTC mode
  TCCR1B |= (1 << WGM12);
  // Set CS10 and CS12 bits for 1024 prescaler
  TCCR1B |= (1 << CS12) | (1 << CS10);  
  // enable timer compare interrupt
  TIMSK1 |= (1 << OCIE1A);  
  sei();//allow interrupts
}

//------------------------//
// ROUTINE D'INTERRUPTION //
//------------------------//

ISR(TIMER1_COMPA_vect){
  if (compteur >= 5)
  {
    flag = 1;
    compteur = 0;
  }
  else
  {
    compteur++;
    Serial.println(compteur);
  }  
}

//---------------------//
// ROUTINE D'ÉVÉNEMENT //
//---------------------//

void event()
{
  String PostData="{\"path\":\"netspresso.relay.01\",\"value\":3,\"units\":\"watts\",\"epoch\":\"2014-08-09T05:46:06-0400\"}";
  int temp;
  temp = thermocouple.measure(TEMPC);
  Serial.println(temp);
  Serial.println("");

  // Requête HTTP:
  client.println("POST /netspresso/metric.json HTTP/1.1");
  client.println("Host: 192.168.0.104"); 
  client.println("Connection: close");
  client.println("Content-Type: application/json;");
  client.print("Content-Length: ");
  client.println(PostData.length());
  client.println();   
}

//--------------------//
// ROUTINE PRINCIPALE //
//--------------------//

void loop()
{
  do
  {
    if (flag)
    {
      cli();
      flag = 0;
      if (client.connect(server, 80))
      {
        Serial.println("--> Connecté");
        event();
      }
      else
      {
        // Si la connection au serveurs est impossible:
        Serial.println("--> Connection impossible!");
      }
      sei();
    }

    // if there are incoming bytes available
    // from the server, read them and print them:
/*    if (client.available())
    {
      char c = client.read();
      Serial.print(c);
    }
  
    // if the server's disconnected, stop the client:
    if (!client.connected())
    {
      Serial.println();
      Serial.println("--> Déconnecté...");
      Serial.println("");
      client.stop();
    }
*/ 
    delay(200);
  } while(true);  
}
