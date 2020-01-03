<?php

class FlightResponse
{
  private $firstName;
  private $lastName;
  private $flightConfirmNum;
  private $flightBoardingGrp;
  private $flightBoardingNum;
    

  function __construct(string $firstName, string $lastName, string $flightConfirmNum, string $flightBoardingGrp = "", string $flightBoardingNum = "")
  {
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->flightConfirmNum = $flightConfirmNum;
    $this->flightBoardingGrp = $flightBoardingGrp;
    $this->flightBoardingNum = $flighflightBoardingNum;
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

  public function getFlightBoardingGrp()
  {
    return $this->flightBoardingGrp;
  }

  public function getFlightBoardingNum()
  {
    return $this->flightBoardingNum;
  }
}
