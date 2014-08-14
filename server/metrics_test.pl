#!/usr/bin/perl -w

use LWP::UserAgent;
 
my $ua = LWP::UserAgent->new;
 
my $server_endpoint = "http://netspresso.cedille.club/netspresso/metric.json";
 
# set custom HTTP request header fields
my $req = HTTP::Request->new(POST => $server_endpoint);
$req->header('content-type' => 'application/json');
$req->header('x-auth-token' => 'tr9D96HJtlcH');
 
# add POST data to HTTP request body
my $post_data = '{"path":"netspresso.relay.01","value":38.400,"units":"volts","epoch":"2014-08-09T05:46:06-0400"}';
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

