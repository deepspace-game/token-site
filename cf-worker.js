/**
 * BSC Token Stat API  Provider
 * 
 * Combines Stats from the following sources and provides a JSON of all the stats
 * 
 * Providers:
 * --bscscan
 * --LunarCRUSH
 * --BitQuery
 * --Token Team
 * --Calculations
 * 
 */

async function gatherResponse(response) {
  const { headers } = response
  const contentType = headers.get("content-type") || ""
  if (contentType.includes("application/json")) {
    return JSON.stringify(await response.json())
  }
  else if (contentType.includes("application/text")) {
    return response.text()
  }
  else if (contentType.includes("text/html")) {
    return response.text()
  }
  else {
    return response.text()
  }
}

async function buildJSON() {
  console.log("Building JSON")
  init = {
    headers: {
      "content-type": "application/json;charset=UTF-8",
    },
  }

  await calcTokenDecimal()
  await getMaximumSupply()
  await getBurned()
  await getTotalSupply()
  await getCirculatingSupply()
  await getLunarCrushInfo()
  await getTokenTransactionsHoldersInfo()
  await writeValuesKV()

  const data = {
    contract: CONTRACT,
    name: tokenName,
    symbol: SYMBOL,
    divisor: DIVISOR,
    burn_address: BURN_ADDRESS,
    burned: burned,
    maximum_supply: maxSupply,
    total_supply: totalSupply,
    circulating_supply: circulatingSupply,
    exclude_circulating_supply: JSON.parse(excludedSupplyList),
    social_score: socialScore,
    total_transactions: transactionsCount,
    holders: holderCount
  }

  const json = JSON.stringify(data, null, 2)
  return new Response(json, init)
}

async function getMaximumSupply() {
  console.log("Pulling maximum supply...")
  console.log("Provider: bscscan")
  const url = "https://api.bscscan.com/api?module=stats&action=tokensupply&contractaddress=" + CONTRACT + "&apikey=" + BSCSCAN_API
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  maxSupply = formatToken(JSON.parse(results)["result"])
  console.log("Maximum Supply: " + maxSupply)
}

async function getBurned() {
  console.log("Pulling total burned tokens...")
  console.log("Provider: bscscan")
  const url = "https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" + CONTRACT + "&address=" + BURN_ADDRESS + "&tag=latest&apikey=" + BSCSCAN_API 
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  burned = formatToken(JSON.parse(results)["result"])
  console.log("Burned: " + burned)
}

async function getTotalSupply() {
  console.log("Calculating total supply...")
  console.log("Provider: calculation")
  console.log("TOTAL SUPPLY = NUM OF COINS MINTED, MINUS ANY COINS BURNED")
  console.log("Formula: Total Supply = Maximum Supply - Burn")
  
  totalSupply = maxSupply - burned
}

async function getCirculatingSupply() {
  console.log("Calculating circulating supply...")
  console.log("Provider: calculation/team")
  console.log("CIRCULATING SUPPLY = NUMBER of coins circulating in the market/general public's hands, not controlled by team")
  console.log("Formula: Circulating Supply = total supply - teamWallets - lockedLPWallets - etc")
  
  excludedSupply = EXCLUDE_CIRCULATING_SUPPLY.split(', ')
  circulatingSupply = totalSupply
  excludedSupplyList = []

  const forLoop = async _ => {
    console.log('Start')

    for (let index = 0; index < excludedSupply.length; index++) {
      exclusion = excludedSupply[index]
      console.log("Exclusion: " + exclusion)

      exclusionExtract = exclusion.split(':')

      excludeWallet = exclusionExtract[0]
      excludeTag = exclusionExtract[1]

      console.log("Wallet Address: " + excludeWallet)
      console.log("Wallet Tag: " + excludeTag)
      console.log("Total Supply is currently: " + circulatingSupply)

      console.log("Pulling total tokens for wallet: " + excludeWallet)
      console.log("Provider: bscscan")
      const url = "https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" + CONTRACT + "&address=" + excludeWallet + "&tag=latest&apikey=" + BSCSCAN_API 
      const response = await fetch(url, init)
      const results = await gatherResponse(response)
      tokenAmount = formatToken(JSON.parse(results)["result"])
      console.log("Token Amount: " + tokenAmount)

      let excludeItem = {
        "address" : excludeWallet,
        "tag" : excludeTag,
        "balance" : tokenAmount
      }

      excludedSupplyList.push(excludeItem);

      circulatingSupply = circulatingSupply - tokenAmount
      await sleep(300)
    }

    console.log(excludedSupplyList)
    excludedSupplyList = JSON.stringify(excludedSupplyList)
    console.log('End')
  }

  await forLoop()

  console.log("finished calculating circulating supply: " + circulatingSupply)
}

function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
}

async function getLunarCrushInfo() {
  console.log("Gathering token data from LunarCRUSH...")
  console.log("Provider: LunarCRUSH")
  console.log("social_score_calc_24h_previous //Sum of followers, retweets, likes, reddit karma etc of social posts collected from 48 hours ago to 24 hours ago")
  console.log("name //The full name of the asset")
  const url = "https://api.lunarcrush.com/v2?data=assets&key=" + LUNARCRUSH_API  + "&symbol=" + SYMBOL
  const response = await fetch(url, init)
  const results = JSON.parse(await gatherResponse(response))

  console.log(results)

  tokenName = results["data"]["0"]["name"]
  socialScore = results["data"]["0"]["social_score_calc_24h_previous"]

  console.log("Token Name: " + tokenName)
  console.log("Social Score: " + socialScore)
}

async function getTokenTransactionsHoldersInfo() {
  console.log("Gathering token transactions and holder information...")
  console.log("Provider: BitQuery")
  console.log("transfers-receiver_count -- unique accounts that have received the token throughout the life of the contract")
  console.log("transfers-count -- total network transactions count")

  query = `
  query {
      ethereum(network:bsc) {
          transfers(currency: {is: "[CONTRACT]"}) {
        count
              receiver_count: count(uniq: receivers)
          }
      }
  }
  `
  query = query.replace("[CONTRACT]", CONTRACT);

  const url = "https://graphql.bitquery.io/";
  const opts = {
      method: "POST",
      headers: {
          "Content-Type": "application/json",
          "X-API-KEY": BITQUERY_API
      },
      body: JSON.stringify({
          query
      })
  }

  const response = await fetch(url, opts)
  const results = JSON.parse(await gatherResponse(response))

  console.log(results)

  transactionsCount = results["data"]["ethereum"]["transfers"][0]["count"]
  holderCount = results["data"]["ethereum"]["transfers"][0]["receiver_count"]

  console.log("Total Transactions: " + transactionsCount)
  console.log("Total Holders: "  + holderCount)
}

async function writeValuesKV() {
  console.log("checking KV pairs")

  //checking max supply
  const KV_MAX_SUPPLY = await DEEPSPACETOKEN.get("MAXIMUM_SUPPLY")
  
  if (KV_MAX_SUPPLY === null) {
    console.log("Failed to pull value: MAXIMUM_SUPPLY - 404 ERROR")
  } else if (KV_MAX_SUPPLY == maxSupply) {
    console.log("Max Supply: No update needed, value matches")
  } else {
      console.log("Updating max supply to " + maxSupply)
      await DEEPSPACETOKEN.put("MAXIMUM_SUPPLY", maxSupply)
  }
  

  //checking contract address
  const KV_CONTRACT = await DEEPSPACETOKEN.get("CONTRACT")
  
  if (KV_CONTRACT === null) {
    console.log("Failed to pull value: CONTRACT - 404 ERROR")
  } else if (KV_CONTRACT == CONTRACT) {
    console.log("Contract: No update needed, value matches")
  } else {
      console.log("Updating contract address to " + CONTRACT)
      await DEEPSPACETOKEN.put("CONTRACT", CONTRACT)
  }


  //checking burn address
  const KV_BURN_ADDRESS = await DEEPSPACETOKEN.get("BURN_ADDRESS")
  
  if (KV_BURN_ADDRESS === null) {
    console.log("Failed to pull value: BURN_ADDRESS - 404 ERROR")
  } else if (KV_BURN_ADDRESS == BURN_ADDRESS) {
    console.log("Burn Address: No update needed, value matches")
  } else {
      console.log("Updating burn address to " + BURN_ADDRESS)
      await DEEPSPACETOKEN.put("BURN_ADDRESS", BURN_ADDRESS)
  }

  //checking burned
  const KV_BURNED = await DEEPSPACETOKEN.get("BURNED")
  
  if (KV_BURNED === null) {
    console.log("Failed to pull value: BURNED - 404 ERROR")
  } else if (KV_BURNED == burned) {
    console.log("Burned: No update needed, value matches")
  } else {
      console.log("Updating burned to " + burned)
      await DEEPSPACETOKEN.put("BURNED", burned)
  }

  //checking total_supply
  const KV_TOTAL_SUPPLY = await DEEPSPACETOKEN.get("TOTAL_SUPPLY")
  
  if (KV_TOTAL_SUPPLY === null) {
    console.log("Failed to pull value: TOTAL_SUPPLY - 404 ERROR")
  } else if (KV_TOTAL_SUPPLY == totalSupply) {
    console.log("Total Supply: No update needed, value matches")
  } else {
      console.log("Updating total_supply to " + totalSupply)
      await DEEPSPACETOKEN.put("TOTAL_SUPPLY", totalSupply)
  }

  //checking circulating_supply
  const KV_CIRCULATING_SUPPLY = await DEEPSPACETOKEN.get("CIRCULATING_SUPPLY")
  
  if (KV_CIRCULATING_SUPPLY === null) {
    console.log("Failed to pull value: CIRCULATING_SUPPLY - 404 ERROR")
  } else if (KV_CIRCULATING_SUPPLY == circulatingSupply) {
    console.log("Circulating Supply: No update needed, value matches")
  } else {
      console.log("Updating circulating_supply to " + circulatingSupply)
      await DEEPSPACETOKEN.put("CIRCULATING_SUPPLY", circulatingSupply)
  }

  //checking exclude_circulating_supply
  const KV_EXCLUDE_CIRCULATING_SUPPLY = await DEEPSPACETOKEN.get("EXCLUDE_CIRCULATING_SUPPLY")
  
  if (KV_EXCLUDE_CIRCULATING_SUPPLY === null) {
    console.log("Failed to pull value: EXCLUDE_CIRCULATING_SUPPLY - 404 ERROR")
  } else if (KV_EXCLUDE_CIRCULATING_SUPPLY == excludedSupplyList) {
    console.log("Exclude Circulating Supply: No update needed, value matches")
  } else {
      console.log("Updating exclude_circulating_supply with this JSON:")
      console.log(excludedSupplyList)
      await DEEPSPACETOKEN.put("EXCLUDE_CIRCULATING_SUPPLY", excludedSupplyList)
  }

  //checking token_name
  const KV_TOKEN_NAME = await DEEPSPACETOKEN.get("TOKEN_NAME")
  
  if (KV_TOKEN_NAME === null) {
    console.log("Failed to pull value: TOKEN_NAME - 404 ERROR")
  } else if (KV_TOKEN_NAME == tokenName) {
    console.log("Token Name: No update needed, value matches")
  } else {
      console.log("Updating token_name with this JSON:")
      console.log(tokenName)
      await DEEPSPACETOKEN.put("TOKEN_NAME", tokenName)
  }

  //checking symbol
  const KV_SYMBOL = await DEEPSPACETOKEN.get("SYMBOL")
  
  if (KV_SYMBOL === null) {
    console.log("Failed to pull value: SYMBOL - 404 ERROR")
  } else if (KV_SYMBOL == SYMBOL) {
    console.log("Symbol: No update needed, value matches")
  } else {
      console.log("Updating symbol with this JSON:")
      console.log(SYMBOL)
      await DEEPSPACETOKEN.put("SYMBOL", SYMBOL)
  }

  //checking social_score
  const KV_SOCIAL_SCORE = await DEEPSPACETOKEN.get("SOCIAL_SCORE")
  
  if (KV_SOCIAL_SCORE === null) {
    console.log("Failed to pull value: SOCIAL_SCORE - 404 ERROR")
  } else if (KV_SOCIAL_SCORE == socialScore) {
    console.log("Social Score: No update needed, value matches")
  } else {
      console.log("Updating social_score with this JSON:")
      console.log(socialScore)
      await DEEPSPACETOKEN.put("SOCIAL_SCORE", socialScore)
  }

  //checking transactions_count
  const KV_TRANSACTIONS_COUNT = await DEEPSPACETOKEN.get("TRANSACTIONS_COUNT")
  
  if (KV_TRANSACTIONS_COUNT === null) {
    console.log("Failed to pull value: TRANSACTIONS_COUNT - 404 ERROR")
  } else if (KV_TRANSACTIONS_COUNT == transactionsCount) {
    console.log("Transactions Count: No update needed, value matches")
  } else {
      console.log("Updating transactions_count with this JSON:")
      console.log(transactionsCount)
      await DEEPSPACETOKEN.put("TRANSACTIONS_COUNT", transactionsCount)
  }

  //checking holders
  const KV_HOLDERS = await DEEPSPACETOKEN.get("HOLDERS")
  
  if (KV_HOLDERS === null) {
    console.log("Failed to pull value: HOLDERS - 404 ERROR")
  } else if (KV_HOLDERS == holderCount) {
    console.log("Holders: No update needed, value matches")
  } else {
      console.log("Updating holders with this JSON:")
      console.log(holderCount)
      await DEEPSPACETOKEN.put("HOLDERS", holderCount)
  }

  console.log("finished checking KV pairs")
}

async function calcTokenDecimal() {
  console.log("Calculating Token Decimal...")
  console.log("Divisor: " + DIVISOR)

  decimal = "1"

  console.log("Decimal is currently: " + decimal)

  const forLoop = async _ => {
    console.log('Start')

    for (let index = 0; index < DIVISOR; index++) {
      decimal += "0"
      console.log("Decimal is currently: " + decimal)
    }

    console.log('End')
  }

  await forLoop()

  console.log("finished with decimal: " + decimal)
}

function formatToken(value) {

  console.log  ("value: " + value)
  console.log ("decimal:" + decimal)

  value = value / decimal

  console.log  ("new value: " + value)

  return value
}

addEventListener("fetch", event => {
  return event.respondWith(buildJSON())
})
