/**
 * BSC Token Stat API  Provider
 * 
 * Combines Stats from the following sources and provides a JSON of all the stats
 * 
 * Providers:
 * --bscscan
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

  await getMaximumSupply()
  await getBurned()
  await getTotalSupply()
  await getCirculatingSupply()
  await writeValuesKV()

  const data = {
    contract: CONTRACT,
    burn_address: BURN_ADDRESS,
    burned: burned,
    maximum_supply: maxSupply,
    total_supply: totalSupply,
    circulating_supply: circulatingSupply
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
  maxSupply = JSON.parse(results)["result"]
  console.log("Maximum Supply: " + maxSupply)
}

async function getBurned() {
  console.log("Pulling total burned tokens...")
  console.log("Provider: bscscan")
  const url = "https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" + CONTRACT + "&address=" + BURN_ADDRESS + "&tag=latest&apikey=" + BSCSCAN_API 
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  burned = JSON.parse(results)["result"]
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

  const forLoop = async _ => {
    console.log('Start')

    for (let index = 0; index < excludedSupply.length; index++) {
      exclusion = excludedSupply[index]
      console.log(exclusion);
      console.log("Total Supply is currently: " + circulatingSupply)

      console.log("Pulling total tokens for wallet: " + exclusion)
      console.log("Provider: bscscan")
      const url = "https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" + CONTRACT + "&address=" + exclusion + "&tag=latest&apikey=" + BSCSCAN_API 
      const response = await fetch(url, init)
      const results = await gatherResponse(response)
      tokenAmount = JSON.parse(results)["result"]
      console.log("Token Amount: " + tokenAmount)

      circulatingSupply = circulatingSupply - tokenAmount
      await sleep(200)
    }

    console.log('End')
  }

  await forLoop()

  console.log("finished calculating circulating supply: " + circulatingSupply)
}

function sleep(ms) {
      return new Promise(resolve => setTimeout(resolve, ms));
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

  console.log("finished checking KV pairs")
}

addEventListener("fetch", event => {
  return event.respondWith(buildJSON())
})
