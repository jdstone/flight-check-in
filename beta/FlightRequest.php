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

/*     // Given YYYY-MM-DD, extract YYYY.
    private function getYearFromDate($date)
    {
        $date = explode("-", $date);
        $date_year = $date[0];
        return $date_year;
    }

    // Given YYYY-MM-DD, extract MM.
    private function getMonthFromDate($date)
    {
        $date = explode("-", $date);
        $date_month = $date[1];
        return $date_month;
    }

    // Given YYYY-MM-DD, extract DD.
    private function getDayFromDate($date)
    {
        $date = explode("-", $date);
        $date_day = $date[2];
        return $date_day;
    }

    // Given HH:mm, extract HH.
    private function getHoursFromTime($time)
    {
        $time = explode(":", $time);
        $time_hour = $time[0];
        return $time_hour;
    }

    // Given HH:mm, extract mm.
    private function getMinutesFromTime($time)
    {
        $time = explode(":", $time);
        $time_minutes = $time[1];
        return $time_minutes;
    } */

    public function calculateCheckinTime($flight_datetime)
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
    }
}

