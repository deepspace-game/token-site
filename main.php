<!DOCTYPE html>
<html>
<head>
<title><?php echo $pagetitle; ?></title>
<base target="_blank">
<link rel="shortcut icon" type="image/png" href="<?php echo $favicon; ?>">
</head>
<style>
a {
	color: hotpink;
}

.data {
	color: cyan;
}

h1,h2,h3,p {
	color: white;
	text-align: center;
}

div {
  margin: auto;
  width: 50%;
  border: 0px solid white;
  padding: 10px;
}

th, td {
  border: 0px solid white;
  color: white;
}

table { 
  border-collapse: collapse;
  background-color: black;
 }
  
tr {

	border: solid thin;
	border-color: white;

 }

table {
  width: 100%;
}

</style>
<body style="background-image: url('<?php echo $bgimg; ?>');">
<h1><?php echo $pagetitle; ?></h1>
<?php
$debug_button = "";
if($debug == true) {
	println("<h2>Results Output DEBUG:</h2>", true);
	println("<div id=\"outputdebug\" style=\"width:700px;height:400px;overflow:auto;background-color:white;white-space:nowrap;resize:both;\">", true);
	println("<br/>", true);
	println("$debugOutput", true);
	println("<br/>", true);
	println("JSON Cached Output:", true);
	print_r($storeJSONCachedArray);
	println("<br/>", true);
	println("Lunar Crush Output:", true);
	print_r($storeLunarArray);
	println("<br/>", true);
	println("BSCScan Maximum Supply Output:", true);
	print_r($storeBSCMaximumSupplyArray);
	println("<br/>", true);
	println("BSCScan Burned Output:", true);
	print_r($storeBSCBurnedArray);
	println("<br/>", true);
	println("BSCScan Transactions Output:", true);
	print_r($storeBSCTransactionsArray);
	println("<br/>", true);
	println("Bitquery Contract Info Output:", true);
	print_r($storeBitqueryArray);
	println("<br/>", true);
	println("CoinGecko Contract Output:", true);
	print_r($storeCoinGeckoContractArray);
	println("<br/>", true);
	println("Circulating Supply Exclusion Wallet Output:", true);
	print_r($storeCirculatingSupplyExclusionArray);
	println("<br/>", true);
	println("Providers Output:", true);
	print_r($storeProviders);
	println("<br/>", true);
	println("</div>", true);
	println("<p align=\"center\"><button id=\"button2\" onclick=\"CopyToClipboard('outputdebug')\">Copy Debug Info</button></p>", true);
	println("<br/>", true);
} else {
	if($allow_debug === true) {
		$debug_button = "<button id=\"button5\" onclick=\"location.href='./?action=debug'\">Show Debug</button> ";
	}
}

//assign data providers to attributes
$team = 0;
$lunarcrush = 2;
$bitquery = 5;
$pancakeswap = 3;
$bsc = 1;
$coingecko = 4;

?>
<p><b>Stats are refreshed every <i class="data"><?php echo $refreshJSONCache . " - " . $failSafeTime; ?></i> seconds and possibly are inaccurate at times.<br/>We cannot guarantee the accuracy of this data, pulled from our site providers.</b></p>
<p align="center"><?php echo $debug_button; ?><button id="button3" onclick="location.href='./?action=json'">Get JSON</button></p>
<h2>Results Output:</h2>
<div style="align: center">
<table>
  <tr>
    <th style="color: hotpink;"><b>Attribute Name</b></th>
	<th style="color: hotpink;"><b>Attribute Value</b></th>
    <th style="color: hotpink;"><b>Description</b></th>
	<th style="color: hotpink;"><b>Source</b></th>
  </tr>
  <tr>
    <td><b>Contract Address</b></td>
	<td><a href="https://bscscan.com/token/<?php echo $contract_address; ?>"><?php echo $contract_address; ?></a></td>
    <td>The contract id specified of the asset</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Burn Address</b></td>
	<td><a href="https://bscscan.com/token/<?php echo $contract_address; ?>?a=<?php echo $burn_address; ?>"><?php echo $burn_address; ?></a></td>
    <td>A burn address is a wallet where no money can ever be withdrawn</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Name</b></td>
	<td class="data"><?php echo $name; ?></td>
    <td>The name for the asset</td>
	<td><a href="<?php echo $providers[$lunarcrush]["provider_url"] ?>"><img src="<?php echo $providers[$lunarcrush]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$lunarcrush]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Symbol</b></td>
	<td class="data"><?php echo $symbol; ?></td>
    <td>The symbol for the asset</td>
	<td><a href="<?php echo $providers[$lunarcrush]["provider_url"] ?>"><img src="<?php echo $providers[$lunarcrush]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$lunarcrush]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Logo</b></td>
	<td><a href="<?php echo $token_logo; ?>"><img src="<?php echo $token_logo; ?>" height="50px" width="50px" alt="<?php echo $name; ?> logo" /></a></td>
    <td>The official logo of the asset</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Release Date</b></td>
	<td class="data"><?php echo $token_release_date; ?></td>
    <td>The date the asset was officially created</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Social Score</b></td>
	<td class="data"><?php echo formatTokenStat($social_score, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Social Score is the sum of followers, retweets, likes, reddit karma etc of social posts collected from 48 hours ago to 24 hours ago.</td>
	<td><a href="<?php echo $providers[$lunarcrush]["provider_url"] ?>"><img src="<?php echo $providers[$lunarcrush]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$lunarcrush]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Holders</b></td>
	<td class="data"><?php echo formatTokenStat($holders, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Total number of wallets with asset in it. [Note: evaluating Receivers on Bitquery]</td>
	<td><a href="<?php echo $providers[$bitquery]["provider_url"] ?>"><img src="<?php echo $providers[$bitquery]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$bitquery]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Decimal / Divisor</b></td>
	<td class="data"><?php echo $decimal; ?> -- <?php echo $divisor; ?></td>
    <td>How the token is broken up or setup and the type of asset it is. This is used to convert the various numbers into <?php echo $name; ?>'s assets proper format for calculation and display.</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Price</b></td>
	<td class="data">$<?php echo $price; ?></td>
    <td>The market price of the asset (this can vary across exchanges and may be delayed).(Using <?php echo $price_decimals; ?> decimal places)</td>
	<td><a href="<?php echo $providers[$pancakeswap]["provider_url"] ?>"><img src="<?php echo $providers[$pancakeswap]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$pancakeswap]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Maximum Supply</b></td>
	<td class="data"><?php echo formatTokenStat($maximum_supply, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Number of coins that will ever exist (hard-coded)</td>
	<td><a href="<?php echo $providers[$bsc]["provider_url"] ?>"><img src="<?php echo $providers[$bsc]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$bsc]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Tokens Burned</b></td>
	<td class="data"><?php echo formatTokenStat($burn, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Number of coins sent to the burn address</td>
	<td><a href="<?php echo $providers[$bsc]["provider_url"] ?>"><img src="<?php echo $providers[$bsc]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$bsc]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Total Supply</b></td>
	<td class="data"><?php echo formatTokenStat($total_supply, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Number of coins minted, minus any coins burned</td>
	<td>Calculated Formula (Maximum Supply - Tokens Burned)</td>
  </tr>
  <tr>
    <td><b>Circulating Supply</b></td>
	<td class="data"><?php echo formatTokenStat($circulating_supply, false, 0, ".", $comma_sep = ","); ?></td>
    <td>The amount of coins that are circulating in the market and are in public hands. It is analogous to the flowing shares in the stock market.</td>
	<td>Calculated Formula (Total Supply - Circulating Supply Excluded Wallets' Balance)</td>
  </tr>
  <tr>
    <td><b>Market Cap</b></td>
	<td class="data">$<?php echo formatTokenStat($market_cap, false, 2, ".", $comma_sep = ","); ?></td>
    <td>The total market value of a cryptocurrency's circulating supply.  It is analogous to the free-float capitalization in the stock market.</td>
	<td>Calculated Formula (Price * Circulating Supply)</td>
  </tr>
  <tr>
    <td><b>Total Transactions</b></td>
	<td class="data"><?php echo formatTokenStat($total_transactions, false, 0, ".", $comma_sep = ","); ?></td>
    <td>Total number of transactions that have occured on the contract of the asset</td>
	<td><a href="<?php echo $providers[$bitquery]["provider_url"] ?>"><img src="<?php echo $providers[$bitquery]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$bitquery]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Fees Distributed</b></td>
	<td class="data"><?php echo $fees_distributed; ?></td>
    <td>[under construction]</td>
	<td>[under construction] N/A</td>
  </tr>
  <tr>
    <td><b>Total Volume</b></td>
	<td class="data">$<?php echo formatTokenStat($volume, false, 2, ".", $comma_sep = ","); ?></td>
    <td>A measure of how much of a cryptocurrency was traded in the last 24 hours</td>
	<td><a href="<?php echo $providers[$coingecko]["provider_url"] ?>"><img src="<?php echo $providers[$coingecko]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$coingecko]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Top Holders List</b></td>
	<td><a href="<?php echo $top_holder_list; ?>"><?php echo $top_holder_list; ?></a></td>
    <td>List of top 1000 wallet holders</td>
	<td><a href="<?php echo $providers[$bsc]["provider_url"] ?>"><img src="<?php echo $providers[$bsc]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$bsc]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Tokenomics</b></td>
	<td><a href="<?php echo $tokenomics; ?>"><?php echo $tokenomics; ?></a></td>
    <td>A public document that describes the distribution of the token</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Whitepaper</b></td>
	<td><a href="<?php echo $whitepaper; ?>"><?php echo $whitepaper; ?></a></td>
    <td>[under construction]</td>
	<td><a href="<?php echo $providers[$team]["provider_url"] ?>"><img src="<?php echo $providers[$team]["site_img"] ?>" style="background-color: white;" height="40px" width="150px" alt="<?php echo $providers[$team]["site_name"] ?>" logo" /></a></td>
  </tr>
  <tr>
    <td><b>Last Updated</b></td>
	<td class="data"><?php echo $last_update; ?></td>
    <td>When the site's API was last, successfully, updated</td>
	<td>Calculated</td>
  </tr>
</table>
</div>
<h3>Circulating Supply Excluded Wallets:</h3>
<div style="align: center">
<table>
  <tr>
    <th style="color: hotpink;"><b>Address</b></th>
	<th style="color: hotpink;"><b><?php echo $symbol; ?> Balance</b></th>
    <th style="color: hotpink;"><b>Owner / Description</b></th>
	<th style="color: hotpink;"><b>Status</b></th>
  </tr>
<?php
//loop excluded wallets
$excludedWalletsLength = count($circulating_supply_exclude_wallets);
$i = 0;
while ($i < $excludedWalletsLength) {
	echo "<tr>";
	echo "	<td><a href=\"https://bscscan.com/token/" . $contract_address . "?a=" . $circulating_supply_exclude_wallets[$i]["address"] . "\">" . $circulating_supply_exclude_wallets[$i]["address"] . "</a></td>";
	echo "	<td class=\"data\">" . formatTokenStat($circulating_supply_exclude_wallets[$i]["balance"], false, 0, ".", $comma_sep = ",") . "</td>";
	echo "	<td>" . $circulating_supply_exclude_wallets[$i]["owned_by_desc"] . "</td>";
	echo "	<td>" . $circulating_supply_exclude_wallets[$i]["status"] . "</td>";
	echo "</tr>";
	$i++;
}
?>
</table>
</div>
<h3>Listing Partners:</h3>
<div style="align: center">
<table>
  <tr>
    <th style="color: hotpink;"><b>Listing Partner Name</b></th>
	<th style="color: hotpink;"><b>Listing Partner Image</b></th>
    <th style="color: hotpink;"><b>Listing Partner Description</b></th>
	<th style="color: hotpink;"><b>Listing Partner Link</b></th>
  </tr>
<?php
//loop excluded wallets
$listingsLength = count($listings);
$i = 0;
while ($i < $listingsLength) {
	echo "<tr>";
	echo "	<td>" . $listings[$i]["site_name"] . "</td>";
	echo "	<td><a href=\"" . $listings[$i]["site_img"] . "\"><img src=\"" . $listings[$i]["site_img"] . "\" style=\"background-color: white;\" height=\"40px\" width=\"150px\" alt=\"" . $listings[$i]["site_name"] . " logo\" /></a></td>";
	echo "	<td>" . $listings[$i]["site_desc"] . "</td>";
	echo "	<td><a href=\"" . $listings[$i]["token_listing_url"] . "\">" . $listings[$i]["token_listing_url"] . "</a></td>";
	echo "</tr>";
	$i++;
}
?>
</table>
</div>
<h3>Data Partners:</h3>
<div style="align: center">
<table>
  <tr>
    <th style="color: hotpink;"><b>Data Partner Name</b></th>
	<th style="color: hotpink;"><b>Data Partner Image</b></th>
    <th style="color: hotpink;"><b>Data Partner Description</b></th>
	<th style="color: hotpink;"><b>Data Partner Link</b></th>
  </tr>
<?php
//loop excluded wallets
$providersLength = count($providers);
$i = 0;
while ($i < $providersLength)
{
	echo "<tr>";
	echo "	<td>" . $providers[$i]["site_name"] . "</td>";
	echo "	<td><a href=\"" . $providers[$i]["site_img"] . "\"><img src=\"" . $providers[$i]["site_img"] . "\" style=\"background-color: white;\" height=\"40px\" width=\"150px\" alt=\"" . $providers[$i]["site_name"] . " logo\" /></a></td>";
	echo "	<td>" . $providers[$i]["site_desc"] . "</td>";
	echo "	<td><a href=\"" . $providers[$i]["provider_url"] . "\">" . $providers[$i]["provider_url"] . "</a></td>";
	echo "</tr>";
	$i++;
}
?>
</table>
</div>
<p>Powered by: <a href="https://riskmoon.com">RISKMOON.com</a></p>
<!-- Cloudflare Web Analytics --><script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "7fca63712a4d429089d8eb82faaa34fb"}'></script><!-- End Cloudflare Web Analytics -->
</body>
</html>