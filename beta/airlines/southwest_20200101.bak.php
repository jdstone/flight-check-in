<?php
// namespace Karriere\JsonDecoder;

if (PHP_SAPI != "cli")
{
    exit;
}

// change to script directory before executing
chdir('/home/johnny/public_html/beta');

// define("WEBBOT_NAME", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
require_once("HttpRequest.php");
require_once("FlightRequest.php");
require_once("FlightResponse.php");
/* require_once '../vendor/autoload.php';

$jsonDecoder = new JsonDecoder();
// $jsonData = '{"id": 1, "name": "John Doe"}';

$person = $jsonDecoder->decode($jsonData, Person::class); */

$confNumber = $argv[1];
$firstName = $argv[2];
$lastName = $argv[3];

$reviewUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/review";

print "\n\nTRYING $reviewUrl\n\n";

$reviewDataRequest = array("confirmationNumber" => $confNumber, "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest");
$jsonReviewRequestString = json_encode($reviewDataRequest);
$refererData = 'https://www.southwest.com/air/check-in/review.html?confirmationNumber='.$confNumber.'&passengerFirstName='.$firstName.'&passengerLastName='.$lastName;
$headerData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json", "content-length: " . strlen($jsonReviewRequestString));
// curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, 'identity');  // "CURLOPT_ACCEPT_ENCODING" is invalid -- leaving for reference.
// curl_setopt($curl, CURLOPT_ENCODING, 'identity');
$encodingData = 'identity';

// create HTTP Request.
$reviewRequest = new HttpReq($reviewUrl, TRUE, $jsonReviewRequestString, TRUE, TRUE, $refererData, TRUE, $headerData, TRUE, $encodingData);
// send HTTP Request.
// echo "\n\nHTTP STATUS CODE: ".$reviewRequest->getHttpCode()."OMG\n\n";
$reviewDataResponse = $reviewRequest->request();

// echo out request response.
echo "\n\nHTTP STATUS CODE: ".$reviewRequest->getHttpCode()."\n\n";
echo "\nTHIS IS THE REQUEST:\n$reviewDataResponse";
/* $flightResponse = $jsonDecoder->decode($reviewDataResponse, FlightResponse::class);
print "\nTYPE: ".gettype($flightResponse); */

$reviewDecodedData = json_decode($reviewDataResponse, TRUE);

/* echo "<br><br>";
echo "token: " . $reviewDecodedData['data']['searchResults']['token'];
echo "<br><br> -- break --  <br><br>"; */


// confirm check-in request
// $confirmUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";

print "\n\nTRYING $confirmUrl\n\n";

$confirmDataRequest = array("token" => $reviewDecodedData['data']['searchResults']['token'], "confirmationNumber" => $confNumber, "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest");
$jsonConfirmRequestString = json_encode($confirmDataRequest);
$refererData = 'https://www.southwest.com/air/check-in/confirmation.html?drinkCouponSelected=false';
$headerData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json", "content-length: " . strlen($jsonConfirmRequestString));
// curl_setopt($confCurl, CURLOPT_ACCEPT_ENCODING, 'identity');  // "CURLOPT_ACCEPT_ENCODING" is invalid -- leaving for reference.
// curl_setopt($confCurl, CURLOPT_ENCODING, 'identity');
$encodingData = 'identity';

// create HTTP Request.
// $confirmRequest = new HttpReq($confirmUrl, TRUE, $jsonConfirmRequestString, TRUE, TRUE, $refererData, TRUE, $headerData, TRUE, $encodingData);
// send HTTP Request.
// $confirmDataResponse = $confirmRequest->request();

// echo out request response.
echo $confirmDataResponse;


/* $emailHeaders = "MIME-Version: 1.0" . "\r\n";
$emailHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$emailHeaders .= 'From: SERVER' . "\r\n";
$emailText = $confirmDataRequest;
mail("johnny.5@example.com","Flight Check-in API",$emailText,$emailHeaders); */
?>

