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

    /**
     * calculateCheckinTime
     *
     * Calculate the flight check-in time for the cron job.
     * The check-in time must be set to 23 hours, 59 minutes prior to departure.
     * This is becuase check-in is allowed 24 hours in advance. Due to crontab's
     * inability to schedule something down to a specific second, check-in must
     * occur 23 hrs, 59 min to allow for any time differences between this and
     * airline servers (check-in systems).
     *
     * @param string $flightDateTime  The date and time for cron job to run, separated by a space
     *               (format: yyyy-mm-dd HH:mm, ex: 2023-01-01 13:30).
     * @return array  Date format: yyyy-mm-dd, Time format: HH:mm (24-hour).
     */
    /* public function calculateCheckinTime($flight_datetime)
    {
        $checkin_datetime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flight_datetime)));
        $checkin_datetime = explode(" ", $checkin_datetime);

        return $checkin_datetime;
    } */

    /**
     * generateReferenceNum
     *
     * Generate a reference number so the flight cron job entry can be found and either a) modified or b) deleted.
     * This number is shown/provided to the passenger after they have scheduled their flight check-in.
     *
     * @param int $length  Length of reference number to generate. Defaults to 8.
     * @return string
     */
    /* public function generateReferenceNum($length = 8)
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

