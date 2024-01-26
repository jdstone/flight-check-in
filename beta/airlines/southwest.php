<?php
session_start();

/* if (PHP_SAPI != "cli")
{
    exit;
} */

// change to script directory before executing
chdir('/home/johnny/public_html/beta');

// define("WEBBOT_NAME", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
/* require_once("HttpRequest.php");
require_once("FlightRequest.php");
require_once("FlightResponse.php"); */
require_once("SouthwestFlight.php");


$referenceNumber = $argv[1];
$confirmationNumber = $argv[2];
$firstName = $argv[3];
$lastName = $argv[4];

$southwest = new SouthwestFlight($firstName, $lastName, $confirmationNumber);
$southwestReviewResponseData = $southwest->MakeReviewRequest();
$southwestReviewResponseData = $southwestReviewResponseData->request();
echo "Review Response DATA: $southwestReviewResponseData\n";
// $reviewRequest = new HttpReq($reviewUrl, TRUE, $jsonReviewRequestString, TRUE, TRUE, $refererData, TRUE, $headerData, TRUE, $encodingData);
// send HTTP Request.
// echo "\n\nHTTP STATUS CODE: ".$southwest->getHttpCode()."OMG\n\n";
// $reviewDataResponse = $reviewRequest->request();

// echo out request response.
// echo "\n\nHTTP STATUS CODE: ".$reviewRequest->getHttpCode()."\n\n";
// echo "\nTHIS IS THE REQUEST:\n$southwestReviewResponseData";
/* $flightResponse = $jsonDecoder->decode($reviewDataResponse, FlightResponse::class);
print "\nTYPE: ".gettype($flightResponse); */

$reviewDecodedData = json_decode($southwestReviewResponseData, TRUE);
// echo $southwestReviewResponseData;
// echo $reviewDecodedData;
/* echo "<br><br>";
echo "token: " . $reviewDecodedData['data']['searchResults']['token'];
echo "<br><br> -- break --  <br><br>"; */
// echo "<br><br>";
echo "origination airport code: " . $reviewDecodedData['data']['searchResults']['reservation']['bounds'][0]['segments'][0]['originationAirportCode'] . "\n";
echo "TOKEN: " . $reviewDecodedData['data']['searchResults']['token'] . "\n";
echo "destination airport code: " . $reviewDecodedData['data']['searchResults']['reservation']['bounds'][0]['segments'][0]['destinationAirportCode'] . "\n";
// echo "<br><br> -- break --  <br><br>";


// confirm check-in request
// $confirmUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";

$southwestApiToken = $reviewDecodedData['data']['searchResults']['token'];

// print "\n\nTRYING $confirmUrl\n\n";
$southwest->setSwApiToken($southwestApiToken);
$southwestConfirmationResponseData = $southwest->MakeConfirmationRequest();
$southwestConfirmationResponseData = $southwestConfirmationResponseData->request();
$confirmationDecodedData = json_decode($southwestConfirmationResponseData, TRUE);
echo "$southwestConfirmationResponseData\n";
/* $confirmDataRequest = array("token" => $reviewDecodedData['data']['searchResults']['token'], "confirmationNumber" => $confNumber,
  "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest"); */
// $jsonConfirmRequestString = json_encode($confirmDataRequest);
// $refererData = 'https://www.southwest.com/air/check-in/confirmation.html?drinkCouponSelected=false';
// $refererData = 'https://www.southwest.com/air/check-in/confirmation.html';
// $headerData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c",
  // "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9",
  // "content-type: application/json", "content-length: " . strlen($jsonConfirmRequestString));
// $encodingData = 'identity';

// create HTTP Request.
// $confirmRequest = new HttpReq($confirmUrl, TRUE, $jsonConfirmRequestString, TRUE, TRUE, $refererData, TRUE, $headerData, TRUE, $encodingData);
// send HTTP Request.
// $confirmDataResponse = $confirmRequest->request();

// Echo out request response.
// echo $confirmDataResponse;


/* $emailHeaders = "MIME-Version: 1.0" . "\r\n";
$emailHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$emailHeaders .= 'From: SERVER' . "\r\n";
$emailText = $confirmDataRequest;
mail("johnny.5@example.com","Flight Check-in API",$emailText,$emailHeaders); */

// send flight check-in email
// $reviewDecodedDataReservation = $reviewDecodedData['data']['searchResults']['reservation'];
// $confirmationDecodedDataReservation = $confirmationDecodedData['data']['searchResults']['reservation'];
// $flight_datetime = $reviewDecodedDataReservation['bounds'][0]['segments'][0]['departureDate'];
// $flight_boardinggroup = $confirmationDecodedDataReservation['travelers'][0]['boardingBounds'][0]['boardingSegments'][0]['boardingGroup'];
// $flight_boardingnum = $confirmationDecodedDataReservation['travelers'][0]['boardingBounds'][0]['boardingSegments'][0]['boardingGroupPosition'];
// $datetime = "2020-11-06T09:25-08:00";
// $flight_time = date("g:i a",strtotime($flight_datetime));
// $flight_date = date("j-n-Y",strtotime($flight_datetime));
// $host = "localhost";
// $path = "beta/send_email_beta.php";
// $data = "email=johnny.5@example.com&first_name=$firstName&last_name=$lastName&confirmation_num=$confirmationNumber&airline=Southwest";
// $data .= "&flight_time=$flight_time&flight_date=$flight_date&boarding_group=$flight_boardinggroup&boarding_num=$flight_boardingnum";
// $data .= "&subject=Your flight check-in&emailBodyType=checkedIn";
// $data = urlencode($data);

// header("POST $path HTTP/1.1\\r\
// " );
// header("Host: $host\\r\
// " );
// header("Content-type: application/x-www-form-urlencoded\\r\
// " );
// header("Content-length: " . strlen($data) . "\\r\
// " );
// header("Connection: close\\r\
// \\r\
// " );
// header($data);

$_SESSION["favcolor"] = "green";
echo $_SESSION["favcolor"]."\n";
?>

