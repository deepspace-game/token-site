<?php

require_once __DIR__ . '/vendor/autoload.php';
use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;

function getBitqueryInfo() {
	GLOBAL $contract_address;
	GLOBAL $holders;
	GLOBAL $bitquery_api;
	GLOBAL $storeBitqueryArray;
	GLOBAL $total_transactions;

	//using Bitquery -> pulling various transfers info
	
	// Create Client object to contact the GraphQL endpoint
	$client = new Client(
		'https://graphql.bitquery.io',
		['X-API-KEY' => $bitquery_api]
	);

// Create the GraphQL query
$gql = <<<QUERY
query {
    ethereum(network:bsc) {
        transfers(currency: {is: "0xa96f3414334f5a0a529ff5d9d8ea95f42147b8c9"}) {
			count
            receiver_count: count(uniq: receivers)
        }
    }
}
QUERY;

	$errorFlag = 0;
	// Run query to get results
	try {
		println("bitquery -> running query for contract info");
		$results = $client->runRawQuery($gql);
	}
	catch (QueryError $exception) {
		// Catch query error and desplay error details
		print("bitquery -> error occured: ");
		storeBitqueryArray($exception->getErrorDetails());
		$errorFlag = 1;
	}
		
	if($errorFlag == 0) {
		println("bitquery -> so far so good");
		$receiver_count = new stdClass();
		$data = json_decode(json_encode($results->getData()), true);
		$storeBitqueryArray = $data;

		$transactionst = $data['ethereum']['transfers'][0]['count'];
		$receiver_countt = $data['ethereum']['transfers'][0]['receiver_count'];
		
		println("bitquery -> transactions count: " . $transactionst);
		println("bitquery -> account holder info: " . $receiver_countt);
		
		if($transactionst > 0) {
			println("bitquery -> transactions count update success");
			$total_transactions = $transactionst;
		} else {
			println("bitquery -> transactions count update failed");
			$total_transactions = $total_transactions;
		}
		
		if($receiver_countt > 0) {
			println("bitquery -> holders update success");
			$holders = $receiver_countt;
		} else {
			println("bitquery -> holders update failed");
			$holders = $holders;
		}
	} else {
		println("bitquery -> something went wrong");
	}
}

?>