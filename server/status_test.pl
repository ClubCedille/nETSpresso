#!/usr/bin/perl -w

use LWP::UserAgent;
 
my $ua = LWP::UserAgent->new;
 
my $server_endpoint = "http://critias.etsmtl.ca:8080/go/status.json?box=netspresso01";
 
# set custom HTTP request header fields
my $req = HTTP::Request->new(GET => $server_endpoint);
#$req->header('content-type' => 'application/json');
#$req->header('x-auth-token' => 'tr9D96HJtlcH');
 
# add POST data to HTTP request body
# my $post_data = '
# {
#     "box": {
#         "name" : "netspresso01"
#     }
# }';
# $req->content($post_data);
 
my $resp = $ua->request($req);
if ($resp->is_success) {
    print "-----Received hearders-----\n", $resp->headers()->as_string, "\n";
    print "-----Received body-----\n", $resp->decoded_content, "\n\n";
}
else {
    print "HTTP GET error code: ", $resp->code, "\n";
    print "HTTP GET error message: ", $resp->message, "\n\n";
}

