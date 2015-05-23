#!/usr/bin/perl -w

use LWP::UserAgent;
 
my $ua = LWP::UserAgent->new;
 
my $server_endpoint = "http://critias.etsmtl.ca:8080/netspresso/heartbeat.json";
 
# set custom HTTP request header fields
my $req = HTTP::Request->new(POST => $server_endpoint);
$req->header('content-type' => 'application/json');
$req->header('x-auth-token' => 'tr9D96HJtlcH');
 
# add POST data to HTTP request body
#my $post_data = '{"status":{"mode":"Cold-Down"},"network":{"mac":"08:00:27:fe:20:4f","ip":"192.168.10.188","dns":"4.4.4.4","gateway":"192.168.10.188","subnet":"255.255.255.0"},"board":{"memory-total":"160Kb","memory-free":"120Kb","cpu":"10Hz","poe":"Ok"},"metrics":[{"sensor":"netspresso.led.01","value":"1","units":"none","adquired":"2014-08-09T05:46:06-0400"},{"sensor":"netspresso.relay.02","value":"30.000","units":"volts","adquired":"2014-08-09T05:46:06-0400"},{"sensor":"netspresso.led.01","value":"1","units":"none","adquired":"2014-08-09T05:46:06-0400"},{"sensor":"netspresso.led.01","value":"1","units":"none","adquired":"2014-08-09T05:46:06-0400"},{"sensor":"netspresso.led.01","value":"1","units":"none","adquired":"2014-08-09T05:46:06-0400"}]}';
# my $post_data = '{
#     "box": {
#         "name" : "netspresso01",
#         "state" : "Cooling-Down",
#         "temperature" : "30.00"
#     },
#     "network": {
#         "mac": "08:00:27:fe:28:45",
#         "ip": "192.168.10.188",
#         "dns": "4.4.4.4",
#         "gateway": "192.168.10.188",
#         "subnet": "255.255.255.0"
#     },
#     "board": {
#         "memory-total": "160Kb",
#         "memory-free": "120Kb",
#         "cpu": "10Hz",
#         "poe": "Ok"
#     },
#     "metrics": [
#         {"sensor": "netspresso01.led.01", "value": "1", "units" : "none", "adquired" : "2014-08-09T05: 46: 06-0400" },
#         {"sensor": "netspresso01.relay.01", "value": "120.000", "units": "Volts","adquired": "2014-08-09T05:46:06-0400" },
#         {"sensor": "netspresso01.relay.02", "value": "0.0", "units" :"Volts", "adquired" : "2014-08-09T05:46:06-0400" },
#         {"sensor": "netspresso01.temperature.01", "value": "70.00", "units" :"Celsius degrees", "adquired" : "2014-08-09T05:46:06-0400" },
#         {"sensor": "netspresso01.power.01", "value": "120.000", "units" :"Volts", "adquired" : "2014-08-09T05:46:06-0400" },
#         {"sensor": "netspresso01.power.02", "value": "0.0", "units" :"Volts", "adquired" : "2014-08-09T05:46:06-0400" }
#     ]
# }';

my $post_data = '{
    "box": {
        "n": "netspresso01",
        "s": 1,
        "t": 30.0
    },
    "sensors": {
        "led": [
            1,
            0
        ],
        "relay": [
			0,
			0
        ],
        "temperature": [
            {
                "v": 70.99,
                "u": "C"
            }
        ],
        "current": [
            {
                "v": 120.99,
                "u": "A"
            },
            {
                "v": 120.99,
                "u": "A"
            }
        ]
    }
}';


$req->content($post_data);
my $resp = $ua->request($req);
if ($resp->is_success) {
    print "-----Received hearders-----\n", $resp->headers()->as_string, "\n";
    print "-----Received body-----\n", $resp->decoded_content, "\n\n";
}
else {
    print "HTTP POST error code: ", $resp->code, "\n";
    print "HTTP POST error message: ", $resp->message, "\n\n";
}

