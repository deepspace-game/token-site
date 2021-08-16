/**
 * Get maximum supply from bscscan
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
  const init = {
    headers: {
      "content-type": "application/json;charset=UTF-8",
    },
  }

  await getMaximumSupply(init)
  await getBurned(init)
  await writeValuesKV()

  return new Response("outputJSON", init)
}

async function getMaximumSupply(init) {
  console.log("Pulling maximum supply...")
  console.log("Provider: bscscan")
  const url = "https://api.bscscan.com/api?module=stats&action=tokensupply&contractaddress=" + CONTRACT + "&apikey=" + BSCSCAN_API
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  maxSupply = JSON.parse(results)["result"]
  console.log("Maximum Supply: " + maxSupply)
}

async function getBurned(init) {
  console.log("Pulling total burned tokens...")
  console.log("Provider: bscscan")
  const url = "https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=" + CONTRACT + "&address=" + BURN_ADDRESS + "&tag=latest&apikey=" + BSCSCAN_API 
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  burned = JSON.parse(results)["result"]
  console.log("Burned: " + burned)
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

  console.log("finished checking KV pairs")
}

addEventListener("fetch", event => {
  return event.respondWith(buildJSON())
})
