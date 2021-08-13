<?php

error_reporting(-1);
ini_set('display_errors', 'On');

require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;

// Create Client object to contact the GraphQL endpoint
$client = new Client(
    'https://graphql.bitquery.io',
    ['X-API-KEY' => 'BQYaPqmqKMOyTsiDqxGmXh24FNO9i6P3']  // Replace with array of extra headers to be sent with request for auth or other purposes
);


// Create the GraphQL query
$gql = <<<QUERY
query {
    ethereum(network:bsc) {
        transfers(currency: {is: "0xa96f3414334f5a0a529ff5d9d8ea95f42147b8c9"}) {
            receiver_count: count(uniq: receivers)
        }
    }
}
QUERY;


// Run query to get results
try {
    //$results = $client->runQuery($gql);
	$results = $client->runRawQuery($gql);
}
catch (QueryError $exception) {
    // Catch query error and desplay error details
	print_r($exception->getErrorDetails());
}
//print_r($results);
$receiver_count = new stdClass();

$data = json_decode(json_encode($results->getData()), true);
print_r($data);

$receiver_count = $data['ethereum']['transfers'][0]['receiver_count'];

echo $receiver_count;