<?php
// define("WEBBOT_NAME", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");

include("../lib/LIB_http.php");
include("../lib/LIB_parse.php");

$emailHeaders = "MIME-Version: 1.0" . "\r\n";
$emailHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$emailHeaders .= 'From: SERVER' . "\r\n";

$confNumber = "W63897";
$firstName = "Johnny";
$lastName = "5";

$reviewUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/review";
$confUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";
// $url = "https://www.southwest.com/air/check-in/review.html?confirmationNumber=W63897&passengerFirstName=Johnny&passengerLastName=5";
// $url = "http://www.webbotsspidersscreenscrapers.com/form_analyzer.php";
// $confNumber = $_GET["confNum"];
// $firstName = $_GET["firstName"];
// $lastName = $_GET["lastName"];
// $returnUrl = "https://www.wheeloffortune.com/Widget/SpinTestModal";
// //$data_string2 = json_encode($data2);
$reviewData = array("confirmationNumber" => $confNumber, "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest");
$jsonString = json_encode($reviewData);
// $data = "confirmationNumber=".$confNumber."&passengerFirstName=".$firstName."&passengerLastName=".$lastName;
// $data .= "&LoginEmail=".$email."&LoginPassword=".$pass;

// change to script directory before executing
chdir('/home/johnny/public_html');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $reviewUrl);
curl_setopt($curl, CURLOPT_POST, TRUE);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string2);
curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonString);
curl_setopt($curl, CURLOPT_USERAGENT, WEBBOT_NAME);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($curl, CURLOPT_VERBOSE, true);
// curl_setopt($curl, CURLOPT_REFERER, 'https://www.southwest.com/air/check-in/review.html?confirmationNumber='.$confNumber.'&passengerFirstName=Johnny&passengerLastName=5');
curl_setopt($curl, CURLOPT_REFERER, 'https://www.southwest.com/air/check-in/review.html?confirmationNumber=W63897&passengerFirstName=Johnny&passengerLastName=5');
curl_setopt($curl, CURLOPT_PROXY, '24.245.100.212');
curl_setopt($curl, CURLOPT_PROXYPORT, '48678');
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Host: www.southwest.com'));
// curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string)), 'Cookie: test=cookie');
// curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Content-Length: " . strlen($jsonString), "authorization: null null", "origin: https://www.southwest.com"));
curl_setopt($curl, CURLOPT_HTTPHEADER, array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json", "content-length: " . strlen($jsonString)));
// curl_setopt($curl, CURLOPT_COOKIE, 'JSESSIONID=aaaaaaaaaaaaa; resPurchasePage=AMEuwFKYXL7DMBX3hxZlh5ExpM_W1v6L1qfpm77FycLHEIq7AaArKgui8UesbNJO1P8KlRhkm8nqjQblm5q0yMtuqEJ_Y5sqGbVz_dsuBXY90dZRo6jrk-jz_dVtiH6kvn3QPXPrprb37VO2s0Jk3GvsKETIHt9oDgNvWLjXucHSDnPYTF_eKIs; AccountBarCookie=136047110112112124130123129091110122114047071123130121121057047112121110128128047071047112124122059128132110112124127125059112124122122124123059078112112124130123129079110127080124124120118114099110121130114128047057047112124122125110123134086113047071123130121121057047112129122083118127128129091110122114047071123130121121057047114133125118127114128047071123130121121057047121110128129078112129118131118129134047071123130121121057047121124116118123082127127124127047071123130121121057047121124116118123082127127124127080124113114047071123130121121057047121124134110121129134047071115110121128114057047125124118123129128047071123130121121057047127127078112112124130123129091130122111114127047071123130121121057047129118114127047071123130121121057047129118114127080124113114047071123130121121057047130128114127083130121121091110122114047071123130121121057047133113118116114128129047071123130121121057047133118125110113113127047071047068067059062061063059063063061059063065061057045063064059062070063059062063065059063063057045063064059063062066059062064062059066064057045062068063059063070059063059068065047057047133129118122114124130129047071047061062060062063060063061063069045061062071064062071062065047138; check=true; AMCVS_65D316D751E563EC0A490D4C%40AdobeOrg=1; s_cc=true; s_fid=6AA1FBA472C8DCFD-0A1B56423BAB54B2; s_sq=%5B%5BB%5D%5D; SwaSessionCookie=8D12E2B80AB3499F88778053475DBC6E; SwaRegionCookie=pdc; AMCV_65D316D751E563EC0A490D4C%40AdobeOrg=2096510701%7CMCIDTS%7C17888%7CMCMID%7C46151325161530134038027339241714088501%7CMCAID%7CNONE%7CMCOPTOUT-1545535649s%7CNONE%7CvVersion%7C2.0.0; akavpau_prod_fullsite=1545532756~id=cb9efd8cefac2a53728d10fba2bb4d3f');
// curl_setopt($curl, CURLOPT_COOKIE, 'check=true;');
// curl_setopt($curl, CURLOPT_HTTPHEADER, array('x-api-idtoken: null', 'x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c', 'x-channel-id: southwest', 'x-user-experience-id: 4d58fa96-47d2-49df-9d03-daf09a7ddd43'));
// curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: check=true"));
// curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
// curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "");
curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, 'identity');
// curl_setopt($curl, CURLOPT_HTTPHEADER, array("accept: application/json, text/javascript, */*; q=0.01", "accept-encoding: gzip, deflate, br", "accept-language: en-US,en;q=0.9"));
curl_setopt($curl, CURLOPT_BINARYTRANSFER, TRUE);
$webpage = curl_exec($curl);
curl_close($curl);

// $obj = json_decode($webpage);
// print $data->{'searchResults'}->{'token'};

$data = json_decode($webpage, true);
echo "token: " . $data['data']['searchResults']['token'];
// $webpage1 = gzdecode($webpage);
// json_decode($webpage);
// echo "<br><br> test <br><br>";
// echo $webpage;
?>

