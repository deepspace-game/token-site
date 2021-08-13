<?php

//attempts to gather json information from site
function println($string_message, $production=false) {
	GLOBAL $debugOutput;
	if($production == false) {
		$debugOutput .= "$string_message<br />";
	}else {
		$_SERVER['SERVER_PROTOCOL'] ? print "$string_message<br />" : print "$string_message\n";
	}
}

//formats numbers as a token stats or into proper display numbers
function formatTokenStat($value, $tokenAdjust = true, $points = 0, $decimal_sep = ".", $comma_sep = "") {
	GLOBAL $divisor;
	
	if($tokenAdjust == true) {
		//format for token decimal/divisor
		$value = $value / $divisor;
	}
	
	//format_number -> scientific notation fix
	$value = number_format($value, $points, $decimal_sep, $comma_sep);
	
	return $value;
}

//check decimals and calculate divisor - which will be used remainder of script - output will be process with this divisor format
function calcTokenDivisor() {
	GLOBAL $decimal;
	GLOBAL $divisor;
	
	println("Calculating Divisor:");
	println("Decimal: " . $decimal);

	//check to make sure these are populated
	if($decimal > 0) {
		println("calculation -> processing divisor");

		$i = "1";
		for ($x = 0; $x < $decimal; $x++) {
			$i .= "0";
			println("Divisor is now: " . $i);
		}
		
		$divisort = $i;
		
		if($divisort > 0) {
			println("calculation -> divisor update success");
			$divisor = $divisort;
		} else {
			println("calculation -> divisor update failed");
			$divisor = $divisor;
		}
	} else {
		println("calculation -> divisor update failed");
		$divisor = $divisor;
	}
}

function checkCachedJSON() {
	GLOBAL $jsonFile;
	GLOBAL $storeJSONCachedArray;
	GLOBAL $refreshJSONCache;
	GLOBAL $difference_seconds;
	GLOBAL $allowLive;
	GLOBAL $failSafeTime;
	
	if (file_exists($jsonFile)) {
		println("The cached JSON file " . $jsonFile . " exists, checking when last updated");
		
		$strJsonFileContents = file_get_contents($jsonFile);
		$storeJSONCachedArray = json_decode($strJsonFileContents, true);
			
		$get_lastUpdated = $storeJSONCachedArray["lastUpdated"];
		
		$currentDateTime = new DateTime();
		$difference_seconds = strtotime($currentDateTime->format('Y-m-d H:i:s')) - strtotime($get_lastUpdated);

		println("Local JSON Last Updated: " . $get_lastUpdated);
		println("checkCachedJSON -> difference in seconds: " . $difference_seconds);
		println("checking if refreshed in last: " . $refreshJSONCache . " seconds");
		
		$overrideUpdateTime = $refreshJSONCache + $failSafeTime;
		
		if($difference_seconds >= $overrideUpdateTime) {
			println("checkCachedJSON -> time to update JSON, cache expired - pulling live data (override occured)");
			$allowLive = true;
			$difference_seconds = $refreshJSONCache;
		} elseif($difference_seconds >= $refreshJSONCache) {
			println("checkCachedJSON -> time to update JSON, cache expired - pulling live data - if update action");
			$difference_seconds = $refreshJSONCache;
		} else {
			println("checkCachedJSON -> too early, using cached data");
			$allowLive = false;
			$difference_seconds = $refreshJSONCache - $difference_seconds;
		}
	} else {
		println("The cached JSON file " . $jsonFile . " does not exists, pulling live data (override occured)");
		$allowLive = true;
	}
}

function getLunarCrushInfo() {
	GLOBAL $social_score;
	GLOBAL $name;
	GLOBAL $symbol;
	GLOBAL $lunarcrush_api;
	GLOBAL $storeLunarArray;
	
	//using lunarcrush.com -> social_score_calc_24h_previous //Sum of followers, retweets, likes, reddit karma etc of social posts collected from 48 hours ago to 24 hours ago
	$json = @file_get_contents("https://api.lunarcrush.com/v2?data=assets&key=" . $lunarcrush_api . "&symbol=" . $symbol);

	if($json === false) {
		println("lunarcrush -> something failed");
	} else {
		println("lunarcrush -> success");
		$data = json_decode($json,true);
	
		$storeLunarArray = $data;

		$namet = $data["data"]["0"]["name"];
		$symbolt = $data["data"]["0"]["symbol"];
		$social_scoret = $data["data"]["0"]["social_score_calc_24h_previous"];
		
		if($namet != "") {
			println("lunarcrush -> name update success");
			$name = $namet;
		} else {
			println("lunarcrush -> name update failed");
			$name = $name;
		}
		
		if($symbolt != "") {
			println("lunarcrush -> symbol update success");
			$symbol = $symbolt;
		} else {
			println("lunarcrush -> symbol update failed");
			$symbol = $symbol;
		}
		
		if($social_scoret > 0) {
			println("lunarcrush -> social_score update success");
			$social_score = $social_scoret;
		} else {
			println("lunarcrush -> social_score update failed");
			$social_score = $social_score;
		}
	}
}

function getBSCScanInfoMaximumSupply() {
	GLOBAL $maximum_supply;
	GLOBAL $contract_address;
	GLOBAL $storeBSCMaximumSupplyArray;
	GLOBAL $bscscan_api;
	
	//using bscscan.com -> tokensupply (api key not required, but using to prevent rate limit)
	$json = @file_get_contents("https://api.bscscan.com/api?module=stats&action=tokensupply&contractaddress=" . $contract_address . "&apikey=" . $bscscan_api);

	if($json === false) {
		println("bscscan -> something failed");
	} else {
		println("bscscan -> success");
		$data = json_decode($json,true);
	
		$storeBSCMaximumSupplyArray = $data;
		
		$status = $data["status"];
		$message = $data["message"];
		$result = $data["result"];
		
		println("bscscan result status: " . $status);
		
		if($status != 1) {
			println("bscscan -> message: " . $message);
			println("bscscan -> result: " . $result);
		} else {
			println("bscscan -> result success");
			
			$maximum_supplyt = $result;
			
			if($maximum_supplyt > 0) {
				println("bscscan -> maximum_supply update success");
				$maximum_supply = $maximum_supplyt;
			} else {
				println("bscscan -> maximum_supply update failed");
				$maximum_supply = $maximum_supply;
			}
			
			$maximum_supply = formatTokenStat($maximum_supply);
		}
	}
}

function getBSCScanInfoBurned() {
	GLOBAL $burn_address;
	GLOBAL $contract_address;
	GLOBAL $burn;
	GLOBAL $bscscan_api;
	GLOBAL $storeBSCBurnedArray;
	
	//using bscscan.com -> tokenbalance -> burn address
	$json = @file_get_contents("https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" . $contract_address . "&address=" . $burn_address . "&tag=latest&apikey=" . $bscscan_api);

	if($json === false) {
		println("bscscan -> something failed");
	} else {
		println("bscscan -> success");
		$data = json_decode($json,true);
	
		$storeBSCBurnedArray = $data;
		
		$status = $data["status"];
		$message = $data["message"];
		$result = $data["result"];
		
		println("bscscan result status: " . $status);
		
		if($status != 1) {
			println("bscscan -> message: " . $message);
			println("bscscan -> result: " . $result);
		} else {
			println("bscscan -> result success");
			
			$burn_balancet = $result;
			
			if($burn_balancet > 0) {
				println("bscscan -> burn update success");
				$burn = $burn_balancet;
				
				$burn = formatTokenStat($burn);
			} else {
				println("bscscan -> burn update failed");
				$burn = $burn;
			}
		}
	}
}

function getCoinGeckoContractInfo() {
	//total_volume
	GLOBAL $contract_address;
	GLOBAL $volume;
	GLOBAL $storeCoinGeckoContractArray;
	
	//using coingecko -> pulling contract info -> free
	$json = @file_get_contents("https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/". $contract_address);

	if($json === false) {
		println("coingecko -> something failed");
	} else {
		println("coingecko -> success");
		$data = json_decode($json,true);
	
		$storeCoinGeckoContractArray = $data;
		$volumet = $data["market_data"]["total_volume"]["usd"];
		println("coingecko -> volume pulled: " . $volumet);
		
		if($volumet > 0) {
			println("coingecko -> volume update success");
			$volume = $volumet;
		} else {
			println("coingecko -> volume update failed");
			$volume = $volume;
		}
	}
}

//TOTAL SUPPLY = NUM OF COINS MINTED, MINUS ANY COINS BURNED
//TOTAL SUPPLY = $maximum_supply - $burn
function calcTotalSupply() {
	GLOBAL $total_supply;
	GLOBAL $maximum_supply;
	GLOBAL $burn;
	
	println("Calculating Total Supply:");
	println("Maximum Supply: " . $maximum_supply);
	println("Tokens Burned: " . $burn);
	
	//check to make sure these are populated
	if($maximum_supply > 0 && $burn > 0) {
		println("calculation -> total supply update success");
		$total_supply = $maximum_supply - $burn;
	} else {
		println("calculation -> total supply update failed");
		$total_supply = $total_supply;
	}	
}

//CIRCULATING SUPPLY = NUMBER of coins circulating in the market/general public's hands, not controlled by team
//CIRCULATING SUPPLY = $maximum_supply - $burn - teamLockedWallets-> Not yet implemented
function calcCirculatingSupply() {
	GLOBAL $maximum_supply;
	GLOBAL $burn;
	GLOBAL $circulating_supply;
	GLOBAL $circulating_supply_exclude_wallets;
	GLOBAL $contract_address;
	GLOBAL $bscscan_api;
	GLOBAL $storeCirculatingSupplyExclusionArray;
		
	println("Calculating Circulating Supply:");
	println("Maximum Supply: " . $maximum_supply);
	println("Burn: " . $burn);
	
	println("Checking for exlcusion wallets and calculating wallets");
	$storeCirculatingSupplyExclusionArray = $circulating_supply_exclude_wallets;
	
	$x = count($circulating_supply_exclude_wallets);
	$y = 0;
	$exclude_supplyt = 0;
	$exclude_supply = 0;
	if($x > 0) {
		println("circulating supply -> exclusion addresses exist " . $x);
		
		while($y < $x) {
			println("circulating supply -> scanning exclusion wallets: " . $y);
			$wallet_addresst = $circulating_supply_exclude_wallets[$y]["address"];
			
			//using bscscan.com -> tokenbalance -> exclusion address
			$json = @file_get_contents("https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" . $contract_address . "&address=" . $wallet_addresst . "&tag=latest&apikey=" . $bscscan_api);

			if($json === false) {
				println("bscscan -> something failed");
			} else {
				println("bscscan -> success");
				$data = json_decode($json,true);
			
				$storeBSCBurnedArray = $data;
				
				$status = $data["status"];
				$message = $data["message"];
				$result = $data["result"];
				
				println("bscscan result status: " . $status);
				
				if($status != 1) {
					println("bscscan -> message: " . $message);
					println("bscscan -> result: " . $result);
				} else {
					println("bscscan -> result success");
					
					$exclude_supplyt = formatTokenStat($result);
					
					if($exclude_supplyt > 0) {
						println("bscscan -> exclusion supply update success");
						$exclude_supply = $exclude_supply + $exclude_supplyt;
						
						$circulating_supply_exclude_wallets[$y]["balance"] = $exclude_supplyt;
						
						println("Circulating Exclusion Supply Running Total: " . $exclude_supplyt);					
					} else {
						println("bscscan -> exclusion supply update failed");
						$exclude_supply = $exclude_supply;
					}
				}
			}
			$y++;
		}
	} else {
		println("circulating supply -> no exclusions exists " . $x);
	}
	
	//check to make sure these are populated
	if($maximum_supply > 0 && $burn > 0) {
		println("calculation -> circulating supply update success");
		$circulating_supply = $maximum_supply - $burn - $exclude_supply;		
	} else {
		println("calculation -> circulating supply update failed");
		$circulating_supply = $circulating_supply;
	}
}

//MARKET CAP = PRICE MULTIPLIED BY CIRCULATING SUPPLY
//MARKET CAP = $price * $circulating_supply
function calcMarketCap() {
	GLOBAL $circulating_supply;
	GLOBAL $price;
	GLOBAL $market_cap;
	
	println("Calculating Market Cap:");
	println("Circulating Supply: " . $circulating_supply);
	println("Price: " . $price);
	
	//check to make sure these are populated
	if($circulating_supply > 0 && $price > 0) {
		println("calculation -> market cap update success");
		$market_cap = $circulating_supply * $price;
		
		$market_cap = formatTokenStat($market_cap, false, 2);		
	} else {
		println("calculation -> market cap update failed");
		$market_cap = $market_cap;
	}
}

function loadCacheData() {
	GLOBAL $tokenJSON;
	GLOBAL $name;
	GLOBAL $contract_address;
	GLOBAL $burn_address;
	GLOBAL $symbol;
	GLOBAL $token_logo;
	GLOBAL $token_release_date;
	GLOBAL $decimal;
	GLOBAL $divisor;
	GLOBAL $price;
	GLOBAL $social_score;
	GLOBAL $maximum_supply;
	GLOBAL $burn;
	GLOBAL $total_transactions;
	GLOBAL $holders;
	GLOBAL $total_supply;
	GLOBAL $circulating_supply;
	GLOBAL $market_cap;
	GLOBAL $volume;
	GLOBAL $circulating_supply_exclude_wallets;
	GLOBAL $storeJSONCachedArray;
	GLOBAL $fees_distributed;
	GLOBAL $last_update;
	GLOBAL $top_holder_list;
	GLOBAL $tokenomics;
	GLOBAL $whitepaper;

	//load up the values from cached data
	$name = $storeJSONCachedArray["name"];
	$symbol = $storeJSONCachedArray["symbol"];
	$token_logo = $storeJSONCachedArray["logo"];
	$contract_address = $storeJSONCachedArray["contractAddress"];
	$burn_address = $storeJSONCachedArray["burnAddress"];
	$token_release_date = $storeJSONCachedArray["releaseDate"];
	$decimal = $storeJSONCachedArray["decimal"];
	$divisor = $storeJSONCachedArray["divisor"];
	$price = $storeJSONCachedArray["price"];
	$social_score = $storeJSONCachedArray["socialScore"];
	$maximum_supply = $storeJSONCachedArray["maximumSupply"];
	$burn = $storeJSONCachedArray["burnBalance"];
	$total_transactions = $storeJSONCachedArray["totalTransactions"];
	$holders = $storeJSONCachedArray["holders"];
	$total_supply = $storeJSONCachedArray["totalSupply"];
	$circulating_supply = $storeJSONCachedArray["circulatingSupply"];
	$market_cap = $storeJSONCachedArray["marketCap"];
	$volume = $storeJSONCachedArray["volume"];
	$circulating_supply_exclude_wallets = $storeJSONCachedArray["excludedCirculationWallets"];
	$fees_distributed = $storeJSONCachedArray["feesDistributed"];
	$last_update = $storeJSONCachedArray["lastUpdated"];
	$top_holder_list = $storeJSONCachedArray["topHoldersList"];
	$tokenomics = $storeJSONCachedArray["tokenomics"];
	$whitepaper = $storeJSONCachedArray["whitepaper"];
	
	$tokenJSON = $storeJSONCachedArray;
}

function generateJSON() {
	GLOBAL $tokenJSON;
	GLOBAL $name;
	GLOBAL $contract_address;
	GLOBAL $burn_address;
	GLOBAL $symbol;
	GLOBAL $token_logo;
	GLOBAL $decimal;
	GLOBAL $divisor;
	GLOBAL $price;
	GLOBAL $token_release_date;
	GLOBAL $social_score;
	GLOBAL $maximum_supply;
	GLOBAL $burn;
	GLOBAL $total_transactions;
	GLOBAL $holders;
	GLOBAL $total_supply;
	GLOBAL $circulating_supply;
	GLOBAL $market_cap;
	GLOBAL $volume;
	GLOBAL $jsonFile;
	GLOBAL $allowLive;
	GLOBAL $providers;
	GLOBAL $circulating_supply_exclude_wallets;
	GLOBAL $fees_distributed;
	GLOBAL $last_update;
	GLOBAL $listings;
	GLOBAL $top_holder_list;
	GLOBAL $tokenomics;
	GLOBAL $whitepaper;

	if($allowLive === true) {
		println("generatejson -> attempting to write fresh json");
		//load up JSON
		$tokenJSON->name = $name;
		$tokenJSON->symbol = $symbol;
		$tokenJSON->logo = $token_logo;

		$token_release_date = new DateTime($token_release_date);
		$token_release_date = $token_release_date->format('F j, Y');
		$tokenJSON->releaseDate = $token_release_date;
		
		$tokenJSON->contractAddress = $contract_address;
		$tokenJSON->burnAddress = $burn_address;
		$tokenJSON->burnBalance = $burn;
		$tokenJSON->excludedCirculationWallets = $circulating_supply_exclude_wallets;

		
		$tokenJSON->decimal = $decimal;
		$tokenJSON->divisor = $divisor;
		
		$tokenJSON->price = $price;
		
		$tokenJSON->socialScore = $social_score;
		
		$tokenJSON->holders = $holders;
		
		$tokenJSON->maximumSupply = $maximum_supply;
		$tokenJSON->totalSupply = $total_supply;
		$tokenJSON->circulatingSupply = $circulating_supply;
		$tokenJSON->marketCap = $market_cap;
		
		$tokenJSON->totalTransactions = $total_transactions;
		$tokenJSON->feesDistributed = $fees_distributed;
		$tokenJSON->volume = $volume;
		
		$tokenJSON->topHoldersList = $top_holder_list;
		$tokenJSON->tokenomics = $tokenomics;
		$tokenJSON->whitepaper = $whitepaper;
		$tokenJSON->listingPartners = $listings;
		$tokenJSON->dataPartners = $providers;
		
		$last_update = new DateTime();
		$last_update = $last_update->format('Y-m-d H:i:s');
		$tokenJSON->lastUpdated = $last_update;

		$tokenJSON = json_encode($tokenJSON, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
		
	   if (json_decode($tokenJSON) != null) {
			$file = fopen($jsonFile,'w+');
			fwrite($file, $tokenJSON);
			fclose($file);
			println("generating json -> json cached");
	   } else {
			println("generating json -> something went wrong");
	   }
	} else {
		println("generatejson -> will not generate cache json -> using cached value");
		$tokenJSON = json_encode($tokenJSON, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
	}
}

?>