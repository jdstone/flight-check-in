<?php

class Main
{
    /* function __construct()
    {
        
    }

    private $var; */

    /**
     * getYearFromDate
     *
     * Get just the year (number) from a given full date.
     * Given yyyy-mm-dd, extract yyyy.
     *
     * @param string $date  The full date in yyyy-mm-dd format.
     * @return string
     */
    function getYearFromDate($date)
    {
        $date = explode("-", $date);
        $dateYear = $date[0];
        return $dateYear;
    }

    /**
     * getMonthFromDate
     *
     * Get just the month (number) from a given full date.
     * Given yyyy-mm-dd, extract mm.
     *
     * @param string $date  The full date in yyyy-mm-dd format.
     * @return string
     */
    function getMonthFromDate($date)
    {
        $date = explode("-", $date);
        $dateMonth = $date[1];
        return $dateMonth;
    }

    /**
     * getDayFromDate
     *
     * Get just the day (number) from a given full date.
     * Given yyyy-mm-dd, extract dd.
     *
     * @param string $date  The full date in yyyy-mm-dd format.
     * @return string
     */
    function getDayFromDate($date)
    {
        $date = explode("-", $date);
        $dateDay = $date[2];
        return $dateDay;
    }

    /**
     * getHoursFromTime
     *
     * Get just the hour from a given full time.
     * Given HH:mm, extract HH.
     *
     * @param string $time  The full time in HH:mm format.
     * @return string
     */
    function getHoursFromTime($time)
    {
        $time = explode(":", $time);
        $timeHour = $time[0];
        return $timeHour;
    }

    /**
     * getMinutesFromTime
     *
     * Get just the minutes from a given full time.
     * Given HH:mm, extract mm.
     *
     * @param string $time  The full time in HH:mm format.
     * @return string
     */
    function getMinutesFromTime($time)
    {
        $time = explode(":", $time);
        $timeMinutes = $time[1];
        return $timeMinutes;
    }
}

