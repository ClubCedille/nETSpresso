
var text = '{"date":"2015-02-01T11:52:26-0500","status":"Stand-By","temperature":"25"}';

var xmlhttp = new XMLHttpRequest();
var url = "/mobile/status.json?box=netspresso01";

xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var box = JSON.parse(xmlhttp.responseText);
        update_status(box.status);
        update_temperature(box.temperature);
    }
}
xmlhttp.open("GET", url, true);
xmlhttp.send();

function update_status(status) {

	if(status == "Stand-By") {
	
		document.getElementById("status").innerHTML = "Status: " + status;
	}
}
  
function update_temperature(temp) {

	document.getElementById("temperature").innerHTML = "Temperature: " + temp + " °C";

}

