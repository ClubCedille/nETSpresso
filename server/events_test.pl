#!/usr/bin/perl -w

use LWP::UserAgent;
 
my $ua = LWP::UserAgent->new;
 
my $server_endpoint = "http://critias.etsmtl.ca:8080/go/event.json";
 
# set custom HTTP request header fields
my $req = HTTP::Request->new(POST => $server_endpoint);
$req->header('content-type' => 'application/json');
$req->header('x-auth-token' => 'tr9D96HJtlcH');
 
# add POST data to HTTP request body
my $post_data = '
{
    "box": {
        "name" : "netspresso01",
        "state" : "Ready"
    },
	"event": {
		"uuid":"d63c8131-dc60-5145-88ef-7f1cbad1e12e",
		"event_id":38,
		"resource_event_id":37,
		"calendar_id":6,
		"user_id":7,
		"username":"Laurin, Patrick",
		"start_time":"2014-08-12 17:30",
		"end_time":"2014-08-12 18:30",
		"subjet":"Test",
		"status":"CONFIRMED"
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

