<?php

//site items
$allow_debug = false; //allow displaying system/debug messages (in url at end type ?action=debug) <- turn off for production use
$allowLive = false; //override and allow live data on each page refresh <- turn off for production use

//APIs
$lunarcrush_api = "3t2ykr1w1dv3qqam7zyi7j"; //api free key to LunarCrush system (need listing on LunarCrush)
$bscscan_api = "JGWY5P5WPYPDPJJZGCEYMC3B9ZFEPZ8J1Y"; //api free key to BSCScan system
$bitquery_api = "BQYaPqmqKMOyTsiDqxGmXh24FNO9i6P3"; //bitquery free key to GraphQL data

$pagetitle = "RiskMoon Token Stats"; //page display name
$favicon = "./images/favicon.png"; //fav icon for display on page
$bgimg = "./images/astronautoriginalv2.jpeg"; //background image on homepage
$jsonFile = "./data/riskmoon.json";
$refreshJSONCache = 60; //60 in seconds -> how often to allow updating the JSON (time delay between updates)
$failSafeTime = 300; //300 in seconds -> fail safe timer that forces an update reagrdless of the action chosen (in case automatic update actions are not done or fail)

//default value for not found stats
$contract_address = "0xa96f3414334F5A0A529ff5d9D8ea95f42147b8C9";
$burn_address = "0x000000000000000000000000000000000000dead";
$name = "Riskmoon";
$symbol = "RISKMOON";
$token_release_date = "2021-03-25";
$token_logo = "https://riskmoon.com/token/images/RISKMOON-token.png";
$decimal = 9;
$divisor = 1000000000;
$price = "N/A";
$social_score = "N/A";
$maximum_supply = "1000000000000000000000000";
$burn = "N/A";
$total_transactions = 0;
$holders = 0;
$fees_distributed = "N/A";
$total_supply = "N/A";
$circulating_supply = "N/A";
$market_cap = "N/A";
$volume = "N/A";
$last_update = "N/A";
$top_holder_list = "https://rsk.mn/bscscan#balances";
$tokenomics = "https://rsk.mn/tokenomics";
$whitepaper = "https://rsk.mn/whitepaper";

$price_decimals = $decimal + 3;//how many price decimals to go

//listingpartners
$listings = array(
	array(
		"site_name" => "CoinGecko",
		"site_img" => "https://static.coingecko.com/s/coingecko-logo-d13d6bcceddbb003f146b33c2f7e8193d72b93bb343d38e392897c3df3e78bdd.png",
		"site_desc" => "CoinGecko provides a fundamental analysis of the crypto market. In addition to tracking price, volume and market capitalization, CoinGecko tracks community growth, open-source code development, major events and on-chain metrics.",
		"token_listing_url" => "https://rsk.mn/coingecko")
);

//addresses to exclude from circulating_supply
//team/development/foundation wallets, airdrop/distribution holdings, marketing funds, vesting funds or any locked up supply.	
//do not add burn address, already excluded			
$circulating_supply_exclude_wallets = array(
	array(
		"address" => "0x55ddddfddab484146db25c42a4e3a4b3c161be8e",
		"balance" => 0,
		"owned_by_desc" => "PancakeSwap: RISKMOON - Liquidity Wallet",
		"status" => "Locked"),
	array(
		"address" => "0x5a79cb55ea1eaaf3ef1af61c9846857c78e1ad48",
		"balance" => 0,
		"owned_by_desc" => "DxSale Contract - DxSale claim at: https://dxsale.com",
		"status" => "Unclaimed"),
	array(
		"address" => "0x89776102137767c38048974a4AEA85A8A43CcC57",
		"balance" => 0,
		"owned_by_desc" => "Marketing Funds - Team",
		"status" => "Open")
);

//datapartners
$providers = array(
	array(
		"site_name" => "RiskMoon Team",
		"site_img" => "https://riskmoon.com/wp-content/uploads/2021/06/RISKMOON-Black-Text.png",
		"site_desc" => "RISKMOON is an evolving platform that leverages risk with dynamic yield generating strategies from NFTs. DEEPSPACE Crypto MMORPG game development underway with an NFT marketplace for loans, minting, and staking! DEEPSPACE property and assets can be loaned to generate revenue, but be careful: assets degrade over time, and value changes based on market demand!",
		"provider_url" => "https://riskmoon.com"),
	array(
		"site_name" => "BscScan",
		"site_img" => "https://bscscan.com/images/logo-bscscan.svg?v=0.0.3",
		"site_desc" => "BscScan is a Block Explorer and Analytics Platform for Binance Smart Chain.",
		"provider_url" => "https://www.bscscan.com/"),
	array(
		"site_name" => "LunarCRUSH",
		"site_img" => "https://lunarcrush.com/assets/img/icons/retro-logo-black-horiz.png",
		"site_desc" => "LunarCRUSH.com is a free tool for the crypto community, designed to provide unique insights and an adaptive, engaging user experience they can't get anywhere else. It helps simplify crypto investing by reducing research time, simplifying social intelligence and providing a more complete view of the market.",
		"provider_url" => "https://lunarcrush.com/"),
	array(
		"site_name" => "PancakeSwap",
		"site_img" => "https://riskmoon.com/token/images/PancakeSwap-Logo.png",
		"site_desc" => "PancakeSwap is an Open Source DeFi protocol designed specially for swapping BEP-20 tokens. PancakeSwap helps you make the most out of your crypto in three ways: Trade, Earn, and Win.",
		"provider_url" => "https://pancakeswap.finance/"),
	array(
		"site_name" => "CoinGecko",
		"site_img" => "https://static.coingecko.com/s/coingecko-logo-d13d6bcceddbb003f146b33c2f7e8193d72b93bb343d38e392897c3df3e78bdd.png",
		"site_desc" => "CoinGecko provides a fundamental analysis of the crypto market. In addition to tracking price, volume and market capitalization, CoinGecko tracks community growth, open-source code development, major events and on-chain metrics.",
		"provider_url" => "https://www.coingecko.com"),
	array(
		"site_name" => "Bitquery",
		"site_img" => "https://bitquery.io/wp-content/uploads/thegem-logos/logo_441e2bb023e0087bb4eea8154a71d246_1x.png",
		"site_desc" => "Bitquery provide blockchain APIs for Bitcoin, Ethereum and Web3 protocols like Uniswap etc. We also provide blockchain money tracing APIs.",
		"provider_url" => "https://bitquery.io")
);

?>