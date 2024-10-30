<?php
/* SPDX-License-Identifier: GPL-3.0-or-later */

// ****  DEFAULT VALUES - SET ME **** \\
define("WEBBOT_NAME", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");
define("EMAIL_FROM_NAME", "SERVER");
define("CONFIRMATION_EMAIL", "johnny.appleseed@example.com");
define("SCRIPT_ROOT_DIR", "/home/johnny/public_html");


$emailHeaders = "MIME-Version: 1.0" . "\r\n";
$emailHeaders .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$emailHeaders .= "From: " . EMAIL_FROM_NAME . "\r\n";

$confNumber = $argv[1];
$firstName = $argv[2];
$lastName = $argv[3];


$swReviewApiUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/review";

// check that the passenger check-in data exists before trying to start the check-in process
if (isset($confNumber) && isset($firstName) && isset($lastName))
{
    $reviewData = array("confirmationNumber" => $confNumber, "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest");
    $jsonReviewString = json_encode($reviewData);
}
else
{
    exit();
}


// change to this script's directory before executing Curl
chdir(SCRIPT_ROOT_DIR);

// define Curl options
$curlOptions = array(
    CURLOPT_POST            => TRUE,
    CURLOPT_USERAGENT       => WEBBOT_NAME,
    CURLOPT_RETURNTRANSFER  => TRUE,
    CURLOPT_FOLLOWLOCATION  => TRUE,
    CURLOPT_VERBOSE         => TRUE,
    CURLOPT_ENCODING        => 'identity',
    CURLOPT_HEADER          => FALSE,
    CURLOPT_COOKIEJAR       => 'cookies.txt',
    CURLOPT_COOKIEFILE      => 'cookies.txt'
);


$reviewCurl = curl_init();

if ($reviewCurl)
{
    // apply Curl options
    curl_setopt($reviewCurl, CURLOPT_URL, $swReviewApiUrl);
    curl_setopt($reviewCurl, CURLOPT_POSTFIELDS, $jsonReviewString);
    curl_setopt($reviewCurl, CURLOPT_HTTPHEADER, array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "Content-Type: application/json", "Content-Length: " . strlen($jsonReviewString)));
    curl_setopt($reviewCurl, CURLOPT_REFERER, 'https://www.southwest.com/air/check-in/review.html?confirmationNumber='.$confNumber.'&passengerFirstName='.$firstName.'&passengerLastName='.$lastName);
    curl_setopt_array($reviewCurl, $curlOptions);

    $reviewDataResponse = curl_exec($reviewCurl);
    curl_close($reviewCurl);
}
else
{
    exit();
}

if (isset($reviewDataResponse))
{
    $reviewDecodedData = json_decode($reviewDataResponse, TRUE);
}
else
{
    exit();
}



// define variables for the confirm API call
$swConfirmApiUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";

$confNumber = $reviewDecodedData['data']['searchResults']['reservation']['confirmationNumber'];
$firstName = $reviewDecodedData['data']['searchResults']['reservation']['travelers'][0]['firstName'];
$lastName = $reviewDecodedData['data']['searchResults']['reservation']['travelers'][0]['lastName'];
$token = $reviewDecodedData['data']['searchResults']['token'];

// if a token was obtained from the previous check-in review, then continue check-in process
if (isset($token))
{
    $confirmData = array("token" => $token, "confirmationNumber" => $confNumber, "passengerFirstName" => $firstName, "passengerLastName" => $lastName, "application" => "air-check-in", "site" => "southwest");
    $jsonConfirmString = json_encode($confirmData);

    $confirmCurl = curl_init();

    if ($confirmCurl)
    {
        // apply Curl options
        curl_setopt($confirmCurl, CURLOPT_URL, $swConfirmApiUrl);
        curl_setopt($confirmCurl, CURLOPT_POSTFIELDS, $jsonConfirmString);
        curl_setopt($confirmCurl, CURLOPT_HTTPHEADER, array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null", "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br", "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json", "content-length: " . strlen($jsonConfirmString)));
        curl_setopt($confirmCurl, CURLOPT_REFERER, 'https://www.southwest.com/air/check-in/confirmation.html?drinkCouponSelected=false');
        curl_setopt_array($confirmCurl, $curlOptions);

        $confirmDataResponse = curl_exec($confirmCurl);
        curl_close($confirmCurl);
    }
    else
    {
        exit();
    }
}
else
{
    exit();
}

// check that the confirm response contains data
if (isset($confirmDataResponse))
{
    $confirmDecodedData = json_decode($confirmDataResponse, TRUE);

    $flightBoardingGroup = $confirmDecodedData['data']['searchResults']['travelers'][0]['boardingBounds'][0]['boardingSegments'][0]['boardingGroup'];
    $flightBoardingGroupPos = $confirmDecodedData['data']['searchResults']['travelers'][0]['boardingBounds'][0]['boardingSegments'][0]['boardingGroupPosition'];

    $confirmStatus = $confirmDecodedData['success'];
}
else
{
    exit();
}

// get passenger information from 'review' api call if the information exists
if (isset($reviewDecodedData))
{
    $firstName = $reviewDecodedData['data']['searchResults']['reservation']['travelers'][0]['firstName'];
    $lastName = $reviewDecodedData['data']['searchResults']['reservation']['travelers'][0]['lastName'];
}
else
{
    exit();
}

// if running this software publicly, send a confirmation email to the owner if an auto check-in completed successfully
if (isset($confirmStatus) && $confirmStatus == TRUE && isset($emailHeaders)) {
    mail(CONFIRMATION_EMAIL, "Auto Check-in Completed", $firstName . " " . $lastName, $emailHeaders);
}
else
{
    exit();
}

// if running this software publicly, send a confirmation email to the owner with the passenger's boarding pass information
if (isset($flightBoardingGroup) && isset($flightBoardingGroupPos))
{
    $emailText = "<html><body><br><strong>Boarding Group:</strong> " . $flightBoardingGroup . "<br>";
    $emailText .= "<strong>Boarding Group Position:</strong> " . $flightBoardingGroupPos . "</body></html>";

    if (isset($emailHeaders))
    {
        mail(CONFIRMATION_EMAIL, "Flight Auto Check-in Confirmation", $emailText, $emailHeaders);
    }
    else
    {
        exit();
    }
}
else
{
    exit();
}

