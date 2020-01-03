<?php

class Main
{
  // Given YYYY-MM-DD, extract YYYY.
  protected function getYearFromDate($date)
  {
    $date = explode("-", $date);
    $date_year = $date[0];
    return $date_year;
  }

  // Given YYYY-MM-DD, extract MM.
  protected function getMonthFromDate($date)
  {
    $date = explode("-", $date);
    $date_month = $date[1];
    return $date_month;
  }

  // Given YYYY-MM-DD, extract DD.
  protected function getDayFromDate($date)
  {
    $date = explode("-", $date);
    $date_day = $date[2];
    return $date_day;
  }

  // Given HH:mm, extract HH.
  protected function getHoursFromTime($time)
  {
    $time = explode(":", $time);
    $time_hour = $time[0];
    return $time_hour;
  }

  // Given HH:mm, extract mm.
  protected function getMinutesFromTime($time)
  {
    $time = explode(":", $time);
    $time_minutes = $time[1];
    return $time_minutes;
  }
}
