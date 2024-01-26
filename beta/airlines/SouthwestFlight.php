<?php
require_once("HttpRequest.php");
require_once("FlightRequest.php");
require_once("FlightResponse.php");
// require 'vendor/autoload.php';

/* if (PHP_SAPI != "cli")
{
    echo "<html>\n  <head>\n    ";
    echo "<title>Not Authorized</title>\n";
    echo "  <body>\n";
    echo "    <h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>\n";
    echo "  </body>\n</html>";
    exit;
} */


// change to script directory before executing
chdir('/home/johnny/public_html/beta');

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
    private $swApiToken;

    private $reviewApiUrl;
    private $reviewApiData;
    // private $reviewApiRequest;
    private $reviewApiResponse;
    private $reviewApiRequestEncodedJsonData;
    private $reviewApiRefererData;
    private $reviewApiHeaderData;
    private $reviewApiResponseDecodedJsonData;

    private $confirmationApiUrl;
    private $confirmationApiData;
    // private $confirmationApiRequest;
    private $confirmationApiResponse;
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
        $this->reviewApiEncodingData = 'identity';


        $this->confirmationApiUrl = "https://www.southwest.com/api/air-checkin/v1/air-checkin/page/air/check-in/confirmation";
        /* $this->confirmationApiData = array("confirmationNumber" => $this->confirmationNumber, "passengerFirstName" => $this->firstName,
          "passengerLastName" => $this->lastName, "application" => "air-check-in", "site" => "southwest"); */
        /* $this->confirmationApiData = array("token" => $this->swApiToken, "confirmationNumber" => $this->confirmationNumber,
          "passengerFirstName" => $this->firstName, "passengerLastName" => $this->lastName, "application" => "air-check-in", "site" => "southwest"); */
        // $this->confirmationApiRequestEncodedJsonData = json_encode($this->confirmationApiData);
        $this->confirmationApiRefererData = 'https://www.southwest.com/air/check-in/confirmation.html';
        // $this->confirmationApiHeaderData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null",
          // "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br",
          // "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json",
          // "content-length: " . strlen($this->confirmationApiRequestEncodedJsonData));
        $this->confirmationApiEncodingData = 'identity';
    }

    public function setSwApiToken($token)
    {
        $this->swApiToken = $token;
    }

    public function MakeReviewRequest()
    {
        /* $this->reviewApiRequest = new HttpReq($this->testApiUrl, TRUE, $this->reviewApiRequestEncodedJsonData, FALSE, TRUE,
          $this->reviewApiRefererData, TRUE, $this->reviewApiHeaderData, TRUE, 'identity'); */
        $this->reviewApiResponse = new HttpReq($this->reviewApiUrl, $this->reviewApiRequestEncodedJsonData, $this->reviewApiRefererData,
          $this->reviewApiHeaderData, $this->reviewApiEncodingData);

        return $this->reviewApiResponse;
    }

    public function MakeConfirmationRequest()
    {
        /* $this->confirmationApiRequest = new HttpReq($this->confirmationApiUrl, TRUE, $this->confirmationApiRequestEncodedJsonData, TRUE, TRUE,
          $this->confirmationApiRefererData, TRUE, $this->confirmationApiHeaderData, TRUE, 'identity'); */

        $this->confirmationApiData = array("token" => $this->swApiToken, "confirmationNumber" => $this->confirmationNumber,
          "passengerFirstName" => $this->firstName, "passengerLastName" => $this->lastName, "application" => "air-check-in", "site" => "southwest");
        $this->confirmationApiRequestEncodedJsonData = json_encode($this->confirmationApiData);
        $this->confirmationApiHeaderData = array("authorization: null null", "origin: https://www.southwest.com", "x-api-idtoken: null",
          "x-api-key: l7xx944d175ea25f4b9c903a583ea82a1c4c", "x-channel-id: southwest", "accept-encoding: gzip, deflate, br",
          "accept: application/json, text/javascript, */*; q=0.01", "accept-language: en-US,en;q=0.9", "content-type: application/json",
          "content-length: " . strlen($this->confirmationApiRequestEncodedJsonData));

        $this->confirmationApiResponse = new HttpReq($this->confirmationApiUrl, $this->confirmationApiRequestEncodedJsonData, $this->confirmationApiRefererData,
          $this->confirmationApiHeaderData, $this->confirmationApiEncodingData);

        return $this->confirmationApiResponse;
    }
}
?>
