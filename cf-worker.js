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

  getMaximumSupply()
  //writeValuesKV()

  return new Response("outputJSON", init)
}

async function getMaximumSupply() {
  console.log("Pulling maximum supply...")
  console.log("Provider: bscscan")
  const response = await fetch(url, init)
  const results = await gatherResponse(response)
  maxSupply = JSON.parse(results)["result"]
}

async function writeValuesKV() {
  console.log("writing to KV")
  await DEEPSPACETOKEN.put("MAXIMUM_SUPPLY", maxSupply)
  await DEEPSPACETOKEN.put("CONTRACT", CONTRACT)
}

addEventListener("fetch", event => {
  return event.respondWith(buildJSON())
})
