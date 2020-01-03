<?php

class FlightRequest
{
  private $firstName;
  private $lastName;
  private $flightConfirmNum;
  private $flightAirline;
  private $flightTime;
  private $flightDate;

  function __construct($firstName, $lastName, $flightConfirmNum, $flightAirline, $flightTime, $flightDate)
  {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->flightConfirmNum = $flightConfirmNum;
    $this->flightAirline = $flightAirline;
    $this->flightTime = $flightTime;
    $this->flightDate = $flightDate;
  }

  public function getFirstName()
  {
    return $this->firstName;
  }

  public function getLastName()
  {
    return $this->lastName;
  }

  public function getFlightConfirmNum()
  {
    return $this->flightConfirmNum;
  }

  public function getFlightAirline()
  {
    return $this->flightAirline;
  }

  public function getFlightTime()
  {
    return $this->flightTime;
  }

  public function getFlightDate()
  {
    return $this->flightDate;
  }

  public function generateReferenceNum($length = 8)
  {
    $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++)
    {
      $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
  }

  public function calculateCheckinTime($flight_datetime)
  {
    $checkin_datetime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flight_datetime)));
    $checkin_datetime = explode(" ", $checkin_datetime);

    return $checkin_datetime;
  }

  public function buildCheckin()
  {
    $this->checkinReferenceNum = $this->generateReferenceNum();
  }
}
