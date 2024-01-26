<?php
require_once("HttpRequest.php");
require_once("FlightRequest.php");
require_once("FlightResponse.php");
// require 'vendor/autoload.php';

if (PHP_SAPI != "cli")
{
    echo "<html>\n  <head>\n    ";
    echo "<title>Not Authorized</title>\n";
    echo "  <body>\n";
    echo "    <h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>\n";
    echo "  </body>\n</html>";
    exit;
}

// change to script directory before executing
chdir('/home/johnny/public_html/beta');

// define("WEBBOT_NAME", "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36");

/* use GuzzleHttp\Client;
$client = new Client
([
    // base URI is used with relative requests
    'base_uri' => 'https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in',
    // you can set any number of default request options.
    'timeout'  => 2.0,
]); */


// class Southwest extends Flight
class SouthwestFlight
{
    private $testApiUrl;

    private $reviewApiUrl;
    private $reviewApiData;
    private $reviewApiRequest;
    private $reviewApiRequestEncodedJsonData;
    private $reviewApiRefererData;
    private $reviewApiHeaderData;
    private $reviewApiResponseDecodedJsonData;

    private $confirmationApiUrl;
    private $confirmationApiData;
    private $confirmationApiRequest;
    private $confirmationApiRequestEncodedJsonData;
    private $confirmationApiRefererData;
    private $confirmationApiHeaderData;
    private $confirmationApiResponseDecodedJsonData;

    private $firstName;
    private $lastName;
    private $confirmationNumber;

    public function __construct($firstName, $lastName, $confirmationNumber)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->confirmationNumber = $confirmationNumber;

        $this->reviewApiUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/review";
        $this->reviewApiData = array("confirmationNumber" => $this->confirmationNumber, "passengerFirstName" => $this->firstName,
          "passengerLastName" => $this->lastName, "application" => "air-check-in", "site" => "southwest");
        $this->reviewApiRequestEncodedJsonData = json_encode($this->reviewApiData);
        $this->reviewApiRefererData = 'https://www.southwest.com/air/check-in/review.html?confirmationNumber='
          .$this->confirmationNumber.'&passengerFirstName='.$this->firstName.'&passengerLastName='.$this->lastName;
        $this->reviewApiHeaderData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null",
          "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br",
          "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json",
          "content-length: " . strlen($this->reviewApiRequestEncodedJsonData));


        $this->confirmationApiUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";
        $this->confirmationApiData = array("confirmationNumber" => $this->confirmationNumber, "passengerFirstName" => $this->firstName,
          "passengerLastName" => $this->lastName, "application" => "air-check-in", "site" => "southwest");
        $this->confirmationApiRequestEncodedJsonData = json_encode($this->confirmationApiData);
        $this->confirmationApiRefererData = 'https://www.southwest.com/air/check-in/confirmation.html';
        $this->confirmationApiHeaderData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null",
          "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br",
          "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json",
          "content-length: " . strlen($this->confirmationApiRequestEncodedJsonData));
    }

    public function MakeReviewRequest()
    {
        $this->reviewApiRequest = new HttpReq($this->reviewApiUrl, TRUE, $this->reviewApiRequestEncodedJsonData, FALSE, TRUE,
          $this->reviewApiRefererData, TRUE, $this->reviewApiHeaderData, TRUE, 'identity');

        return $this->reviewApiRequest;
    }

    public function MakeConfirmationRequest()
    {
        $this->confirmationApiRequest = new HttpReq($this->confirmationApiUrl, TRUE, $this->confirmationApiRequestEncodedJsonData, TRUE, TRUE,
          $this->confirmationApiRefererData, TRUE, $this->confirmationApiHeaderData, TRUE, 'identity');

        return $this->confirmationApiRequest;
    }
}
?>

