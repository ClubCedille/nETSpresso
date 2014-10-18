import QtQuick 2.2
//import QHttp

Rectangle {
    id: mainPanel
    width: 360
    height: 360
    color: "#000000"
    Component.onCompleted:{
        sendToServer();
    }


    function sendToServer() {

          var now = new Date().toISOString();
          messages.text = "sending request";

          var http = new XMLHttpRequest();
          var url = "http://netspresso.cedille.club/netspresso/heartbeat.json";
          var params = '{
            "box": {
                "name" : "netspresso01",
                "state" : "'+ lbl_display.text + '",
                "temperature" : "' + txt_temperature.text + '"
            },
            "network": {
                "mac": "08:00:27:fe:28:45",
                "ip": "192.168.10.188",
                "dns": "4.4.4.4",
                "gateway": "192.168.10.188",
                "subnet": "255.255.255.0"
            },
            "board": {
                "memory-total": "160Kb",
                "memory-free": "120Kb",
                "cpu": "10Hz",
                "poe": "Ok"
            },
            "metrics": [
                {"sensor": "netspresso01.led.01", "value": "1", "units" : "none", "adquired" : "' + now + '" },
                {"sensor": "netspresso01.relay.01", "value": "1", "units": "Volts","adquired": "' + now + '" },
                {"sensor": "netspresso01.relay.02", "value": "0", "units" :"Volts", "adquired" : "' + now + '" },
                {"sensor": "netspresso01.temperature.01", "value": "'+ txt_temperature.text +'", "units" :"Celsius degrees", "adquired" : "' + now + '" },
                {"sensor": "netspresso01.power.01", "value": "' + btn_power.metric + '", "units" :"Volts", "adquired" : "' + now + '" },
                {"sensor": "netspresso01.power.02", "value": "' + btn_coffee.metric + '", "units" :"Volts", "adquired" : "' + now + '" }
            ]
          }';

          http.open("POST", url, true);

          // Send the proper header information along with the request
          http.setRequestHeader("Content-type", "application/json");
          http.setRequestHeader("Content-length", params.length);
          http.setRequestHeader("Connection", "close");

          http.onreadystatechange = function() { // Call a function when the state changes.
                      messages.text += ".";
                      if (http.readyState == 4) {
                          if (http.status == 200) {
                              messages.text = "Ok";
                              console.log("ok: " +  http.responseText);

                              // translate response into object
                              var json = eval('new Object(' + http.responseText + ')');
                              console.log("message: " +  json.response.message);
                              messages.text = json.response.message;

                              switch (json.response.message){
                              case "Ok":
                                  break;
                              case "Warm-Up":
                                  lbl_display.text = "Warming-Up";
                                  btn_power_img.color = "#bcb61e";
                                  btn_power.state = "auto";
                                  btn_power.metric = "120.0";
                                  break;
                              case "Cold-Down":
                                  lbl_display.text = "Cooling-Down";
                                  break;
                              case "Lock-Down":
                                  lbl_display.text = "Locked";
                                  break;
                              case "Override":
                                  lbl_display.text = "Stand-By";
                                  break;
                              default:
                                  break;
                              }
                          } else {
                              messages.text = "Error";
                              console.log("error: " + http.status);
                          }
                      }
                  }
          http.send(params);
    }

    MouseArea {
        id: btn_power
        anchors.bottomMargin: 229
        anchors.leftMargin: 24
        anchors.rightMargin: 272
        anchors.topMargin: 36
        anchors.fill: parent
        property var state: "off"
        property var metric : "0.0";
        onClicked: {
            switch(btn_power.state){
            case "off":
                btn_power_img.color = "#146b00";
                btn_power.state = "on";
                btn_power.metric = "120.0";
                lbl_display.text = "Warming-Up";
                //sendToServer();
                break;
            case "on":
                btn_power_img.color = "#b80000";
                btn_power.state = "off";
                btn_power.metric = "0.0";
                lbl_display.text = "Cooling-Down";
                //sendToServer();
                break;
            case "auto":
                break;
            default:
                break;
            }
        }

        Rectangle {
            id: btn_power_img
            x: 7
            y: 9
            width: 50
            height: 77
            color: "#b80000"
        }
    }

    Text {
        id: lbl_display
        color: "#ffffff"
        anchors.centerIn: parent
        text: "Stand-By"
        z: 1
        style: Text.Raised
        font.bold: false
        font.family: "Verdana"
        font.pointSize: 14
        anchors.verticalCenterOffset: -164
        anchors.horizontalCenterOffset: -11
    }

    MouseArea {
        id: btn_coffee
        x: 30
        y: 8
        anchors.rightMargin: 182
        anchors.leftMargin: 114
        anchors.topMargin: 36
        anchors.bottomMargin: 229
        anchors.fill: parent
        property var state: "off"
        property var metric: "0.0"
        onClicked: {
            //Qt.quit();
            if((btn_power.state == "auto" && lbl_display.text == "Ready") ||
               (btn_power.state == "on")){

                if(btn_coffee.state == "off") {
                    btn_coffee_img.color = "#146b00";
                    btn_coffee.state = "on";
                    btn_coffee.metric = "120.0";
                } else{
                    btn_coffee_img.color = "#b80000";
                    btn_coffee.state = "off";
                    btn_coffee.metric = "0.0";
                }
                sendToServer();
            }
        }

        Rectangle {
            id: btn_coffee_img
            x: 7
            y: 9
            width: 50
            height: 77
            color: "#b80000"
        }
    }

    Text {
        id: lbl_power
        x: -8
        y: 3
        color: "#ffffff"
        text: "Power"
        anchors.centerIn: parent
        anchors.horizontalCenterOffset: -123
        style: Text.Raised
        font.family: "Verdana"
        font.pointSize: 14
        anchors.verticalCenterOffset: -34
        font.bold: false
    }

    Text {
        id: lbl_coffee
        x: -2
        y: -5
        color: "#ffffff"
        text: "Coffee"
        anchors.centerIn: parent
        anchors.horizontalCenterOffset: -34
        style: Text.Raised
        font.family: "Verdana"
        font.pointSize: 14
        anchors.verticalCenterOffset: -34
        font.bold: false
    }

    Text {
        id: messages
        x: 24
        y: 303
        width: 295
        height: 18
        color: "#ffffff"
        text: qsTr("...")
        font.pixelSize: 12
    }

    Timer {
        interval: 1000; running: true; repeat: true
        onTriggered: {

            lbl_time.text = Date().toString();

            if (lbl_display.text === "Warming-Up" && txt_temperature.text < 100.0) {
                txt_temperature.text = parseFloat(txt_temperature.text) + 2;
            }
            if (lbl_display.text === "Warming-Up" && txt_temperature.text >= 100.0) {
                txt_temperature.text = parseFloat(txt_temperature.text) + 1;
                lbl_display.text = "Ready";
            }
            if (lbl_display.text === "Ready" && txt_temperature.text > 100.0) {
                txt_temperature.text = parseFloat(txt_temperature.text) + 1;
            }
            if (lbl_display.text === "Ready" && txt_temperature.text > 120.0) {
                lbl_display.text = "Cooling-Down";
            }
            if (lbl_display.text === "Cooling-Down" && txt_temperature.text > 30.0) {
                txt_temperature.text = parseFloat(txt_temperature.text) - 1;
            }
            if (lbl_display.text === "Cooling-Down" && txt_temperature.text <= 30.0) {
                lbl_display.text = "Stand-By";
                btn_power.state = "off";
                btn_power_img.color = "#b80000";
            }
        }
    }

    Timer {
        interval: 1000; running: true; repeat: true
        onTriggered: {

            lbl_time.text = Date().toString();
        }
    }

    Timer {
        interval: 60000; running: true; repeat: true
        onTriggered: {
            sendToServer();
        }
    }

    Text { id: lbl_time ; x: 24; y: 327; width: 79; height: 9;color: "#ffffff" }

    Rectangle {
        id: temperature
        x: 216
        y: 58
        width: 91
        height: 33
        color: "#e1b033"
        z: 2

        Text {
            id: lbl_temperature
            x: 38
            y: 42
            color: "#ffffff"
            text: qsTr("Temperature")
            anchors.horizontalCenter: parent.horizontalCenter
            anchors.bottom: parent.bottom
            anchors.bottomMargin: -20
            font.family: "Verdana"
            font.pixelSize: 14
        }

        Text {
            id: lbl_celsius
            x: 118
            y: 54
            color: "#ffffff"
            text: qsTr("°C")
            anchors.verticalCenter: parent.verticalCenter
            anchors.right: parent.right
            anchors.rightMargin: 4
            font.family: "Verdana"
            font.pixelSize: 14
        }

        TextInput {
            id: txt_temperature
            y: 54
            height: 20
            color: "#ffffff"
            text: qsTr("30.0")
            anchors.verticalCenter: parent.verticalCenter
            anchors.left: parent.left
            anchors.leftMargin: 10
            font.family: "Verdana"
            activeFocusOnPress: true
            autoScroll: true
            inputMask: qsTr("")
            selectionColor: "#ffffff"
            cursorVisible: true
            horizontalAlignment: Text.AlignHCenter
            font.pixelSize: 14
            //validator: DoubleValidator{bottom: 0.0; top: 140.0;}
            //Keys.onReturnPressed:
            onAccepted:{
                //messages.text = txt_temperature.text;
                //if (lbl_display.text == "Warming-Up" && txt_temperature.text > 100.0) {
                //    lbl_display.text = "Ready";
                //}
                //sendToServer();
            }
            //textChanged:{
            //}
        }
    }
}

