import scala.concurrent.duration._

import io.gatling.core.Predef._
import io.gatling.http.Predef._
import io.gatling.jdbc.Predef._

class beige extends Simulation {

	val httpProtocol = http
		.baseUrl("https://studienprojekt2020.elements.live")

	val searchHeader = Map(
		"accept" -> "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
		"accept-encoding" -> "gzip, deflate, br",
		"accept-language" -> "de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7",
		"sec-fetch-dest" -> "document",
		"sec-fetch-mode" -> "navigate",
		"sec-fetch-site" -> "same-origin",
		"sec-fetch-user" -> "?1",
		"upgrade-insecure-requests" -> "1",
		"user-agent" -> "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36")

	val loginHeader = Map(
		"accept" -> "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
		"accept-encoding" -> "gzip, deflate, br",
		"accept-language" -> "de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7",
		"cache-control" -> "max-age=0",
		"origin" -> "https://studienprojekt2020.elements.live",
		"sec-fetch-dest" -> "document",
		"sec-fetch-mode" -> "navigate",
		"sec-fetch-site" -> "same-origin",
		"sec-fetch-user" -> "?1",
		"upgrade-insecure-requests" -> "1",
		"user-agent" -> "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36")

	val requestScn = scenario("beige")
		.exec(http("login")
			.post("/en/account/login")
			.headers(loginHeader)
			.formParam("_username", "bamm@modern.age")
			.formParam("_password", "elements")
			.formParam("_csrf_token", "mOzG7dBEGM9lY_XupFmo3pokT1LoW00JlrXDWhv--YI")
			.formParam("_submit", "")
			.formParam("_target_path", "")
			.formParam("_token", "LZ24oXFsmCp-eYqJflI_0f31etBBL_mg4dbeRANDz20"))
		.exec(http("tenant")
			.get("/en/More-Stuff/Developers-Corner/Tenant-Switch?change-assortment-tenant=ElasticSearch")
			.headers(searchHeader))
		.exec(http("search")
			.get("/en/search?term=beige")
			.headers(searchHeader))

	setUp(requestScn.inject(atOnceUsers(150))).protocols(httpProtocol)
}