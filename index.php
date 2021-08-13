<?php
require 'settings.php';
require 'priceclass.php'; //import price class - special thanks to @john_prime
require 'bitqueryclass.php'; //import bitquery data
require 'tokenfunctions.php';

//preventing cache
header("Cache-Control: max-age=0, private, no-cache"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$debug = false;
$debugOutput = "";
$storeJSONCachedArray = "";
$storeLunarArray = "";
$storeBSCMaximumSupplyArray = "";
$storeProvidersArray = "";
$storeBSCBurnedArray = "";
$storeBSCTransactionsArray = "";
$storeBitqueryArray = "";
$storeBSCTokenArray = "";
$storeCoinGeckoContractArray = "";
$storeCirculatingSupplyExclusionArray = "";
$tokenJSON = new stdClass();
$outputJSON = false;
$action = false;
$filter = "";
$difference_seconds = 0;
if(isset($_GET['action'])){
    $action = htmlentities($_GET['action']);
}

if($action !== false){
   println("Action: " . $action);
   
   switch ($action) {
	case "debug":
		println("debug action requested");
		if($allow_debug == true) {
			error_reporting(-1);
			ini_set('display_errors', 'On');
			$debug = true;
			$storeProviders = $providers;
			println("debug action allowed/enabled");
		} else {
			println("debug action denied");
		}
		break;
	case "json":
		println("generating document as a json only document");
		header('Content-Type: application/json');
		$outputJSON = true;
		break;
	case "circulatingSupply":
		println("generating document as a json only document with a filter on circulatingSupply");
		header('Content-Type: application/json');
		$outputJSON = true;
		$filter = "circulatingSupply";
		break;
	case "update":
		println("checking for updates to the token from the data sources");
		header('Content-Type: application/json');
		$outputJSON = true;
		$allowLive = true;
		break;
    default:
       println("invalid action: " . $action);
	   println("continuing on like no action was asked");
   }
}

//check for cachedjson - to see if results are needed to be updated
checkCachedJSON();

if($allowLive === true) {
	println("allowed to gather live data -- gathering");
	//run token stats
	calcTokenDivisor();
	askPrice();
	getLunarCrushInfo();
	getBSCScanInfoMaximumSupply();
	getBSCScanInfoBurned();
	getBitqueryInfo();
	getCoinGeckoContractInfo();
	calcTotalSupply();
	calcCirculatingSupply();
	calcMarketCap();
} else {
	println("using cached data, too early");
	loadCacheData();
}

//encodeJSON
generateJSON();

if($outputJSON != true) {
	require('main.php');
} else {
	if($filter == "circulatingSupply") {
		$storeJSONCachedArray = json_decode($tokenJSON, true);
		$get_symbol = $storeJSONCachedArray["symbol"];
		$get_contract_address = $storeJSONCachedArray["contractAddress"];
		$get_circulatingSupply = $storeJSONCachedArray["circulatingSupply"];
		$get_lastUpdated = $storeJSONCachedArray["lastUpdated"];
		$tokenJSON = new stdClass();
		$tokenJSON->symbol = $get_symbol;
		$tokenJSON->contractAddress = $get_contract_address;
		$tokenJSON->circulatingSupply = $get_circulatingSupply;
		$tokenJSON->providers = $providers;
		$tokenJSON->lastUpdated = $get_lastUpdated;
		$tokenJSON = json_encode($tokenJSON, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		print_r($tokenJSON);
	} else {
		print_r($tokenJSON);
	}
}

?>