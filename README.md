nÉTSpresso
==========

## Table des matières
1. nÉTSpresso Box
2. nÉTSpresso Server
3. Group Office Module


## nÉTSpresso Box
La cafetière nÉTSpresso est surmontée d'une boîte appellée 'Box'. La Box est composée d'un Arduino Ethernet ainsi que des composantes qui contrôllent le fonctionnement de la  cafetière. Elle est connectée au réseau via un câble Ethernet.

Lorsque la Box démarre, elle fait la calibration de ses instruments. Une fois la phase de calibrage terminée, elle tente de se connecter au serveur (hébergé par le Critias). Si elle n'y arrive pas, elle entre en mode NETWORK_ERROR et tente de se reconnecter périodiquement.

Lorsqu'elle est connectée au réseau, la Box envoie une chaîne de caractères JSON au serveur contenant la valeur des différentes sondes de la cafetière:
```JSON
{
  "box": {
    "n": "netspresso01",
    "s": 0,
    "t": 24
  },
  "sensors": {
    "led": [
      0
    ],
    "relay": [
      0,
      0
    ],
    "current": [
      {
        "u": "A",
        "v": 0.09
      },
      {
        "u": "A",
        "v": 0.09
      }
    ],
    "temperature": [
      {
        "u": "C",
        "v": 24
      }
    ]
  }
}
```

Le serveur reçoit la chaîne de caractères et renvoie une chaîne JSON comportant la commande à exécuter par la cafetière:
```JSON
{
  "response": {
    "code": "0",
    "message": "Ok"
  }
}
```
La commande à exécuter est la valeur numérique contenue dans l'index 'code'. Les différentes commandes possibles sont:
```
DO_NOTHING 0   # Ne rien faire
WARM_UP    1   # Réchauffer l'eau
COOL_DOWN  2   # Refroidif l'eau
LOCK_DOWN  3   # ??
OVERRIDE   4   # Contrôle manuel? (On sait que ça remet la machine en mode STANDBY)
```

Tout dépendant des commandes envoyées, la Box se met dans différents 'états':

 - `STANDBY`
  - Le mode par défaut. La Box ne fait rien.
 - `WARMING`
  - La Box commence à réchauffer l'eau de la cafetière jusqu'à ce que la temoérature excède 100 degrés Celcius, après quoi la Box se met en mode `READY`.
 - `READY`
  - La Box entre dans ce mode seulement quand l'eau est suffisament chaude.
 - `COOLING`
  - La Box se met dans ce mode lorsque le serveur lui indique (possiblement lorsque la période où le café doit être préparé se termine, nous pourrons confirmer en se connectant sur le serveur du CRITIAS).
 - `LOCKED`
  - ?

## nÉTSpresso Server


## nÉTSpresso Group Office Module
