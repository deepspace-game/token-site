/**
 * Get maximum supply from bscscan
 */

const url = "https://api.bscscan.com/api?module=stats&action=tokensupply&contractaddress=" + CONTRACT + "&apikey=" + BSCSCAN_API

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
  await writeValuesKV()

  return new Response("outputJSON", init)
}

async function getMaximumSupply(init) {
  console.log("Pulling maximum supply...")
  console.log("Provider: bscscan")
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  maxSupply = JSON.parse(results)["result"]
}

async function writeValuesKV() {
  console.log("checking KV pairs")

  //checking max supply
  const KV_MAX_SUPPLY = await DEEPSPACETOKEN.get("MAXIMUM_SUPPLY")
  
  if (KV_MAX_SUPPLY === null) {
    console.log("Failed to pull value: MAXIMUM_SUPPLY - 404 ERROR")
  } else if (KV_MAX_SUPPLY == maxSupply) {
    console.log("No update needed, value matches")
  } else {
      console.log("Updating max supply to " + maxSupply)
      await DEEPSPACETOKEN.put("MAXIMUM_SUPPLY", maxSupply)
  }
  

  //checking contract address
  const KV_CONTRACT = await DEEPSPACETOKEN.get("CONTRACT")
  
  if (KV_CONTRACT === null) {
    console.log("Failed to pull value: CONTRACT - 404 ERROR")
  } else if (KV_CONTRACT == CONTRACT) {
    console.log("No update needed, value matches")
  } else {
      console.log("Updating contract address to " + CONTRACT)
      await DEEPSPACETOKEN.put("CONTRACT", CONTRACT)
  }

  console.log("finished checking KV pairs")
}

addEventListener("fetch", event => {
  return event.respondWith(buildJSON())
})
