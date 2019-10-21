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

    /* public function calculateCheckinTime($flight_datetime)
    {
        $checkin_datetime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flight_datetime)));
        $checkin_datetime = explode(" ", $checkin_datetime);

        return $checkin_datetime;
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

    public function buildCheckin()
    {
        $this->checkinReferenceNum = $this->generateReferenceNum();
    } */
}

