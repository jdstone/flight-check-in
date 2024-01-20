<?php
// SPDX-License-Identifier: GPL-3.0-or-later

/**
 * Create the automatic flight check-in cron job entry.
 *
 * Schedules the flight check-in as a cron job, so that it runs at the
 * correct time and automatically checks in the passenger(s).
 *
 * @author J.D. Stone
 */


// ****  DEFAULT VALUES - SET ME **** \\
define("SERVER_IP_ADDR", "xxx.xxx.xxx.xxx");
define("SCRIPT_ROOT_DIR", "/home/johnny/public_html");
define("SW_AIRLINE_SCRIPT", SCRIPT_ROOT_DIR . "/airlines/southwest.php");


if (isset($_SERVER['HTTP_REFERER']))
{
    $refererFile = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
if ($_SERVER['SERVER_ADDR'] == SERVER_IP_ADDR && isset($refererFile) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
{
    /**
     * createCronTabEntry
     *
     * Creates a formatted crontab entry (cron job) on the Linux host, so it can be inserted into crontab.
     *
     * Format of crontab entry:
     *  # $firstName $lastName with $airline at HH:mm on yyyy-mm-dd
     *  mm HH m d * php -f airline_script.php $confirmationNum $firstName "$lastName"
     *
     * @param string $cronComment  The comment that will appear in crontab on the line before the individual cron job entry.
     * @param string $cronDateTime  The date and time at which the cron job will run.
     * @param string $cronCmd  The cron job command that will be run at executation time.
     * @return int
     */
    function createCronTabEntry($cronComment, $cronDateTime, $cronCmd)
    {
        // preserve existing crontab by retrieving its contents
        exec('crontab -l', $cronContentsRaw);
        $cronContents = implode(PHP_EOL, $cronContentsRaw);

        if (!empty($cronContents))
        {
            // create crontab entry, with existing crontab contents
            file_put_contents('/tmp/crontab', $cronContents.PHP_EOL.$cronComment.PHP_EOL.$cronDateTime." ".$cronCmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exitCode);
            return $exitCode;
        }
        else
        {
            // create crontab entry
            file_put_contents('/tmp/crontab', $cronComment.PHP_EOL.$cronDateTime." ".$cronCmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exitCode);
            return $exitCode;
        }
        return $exitCode;
    }

    /**
     * generateReferenceNum
     *
     * Generate a reference number so the flight cron job entry can be found and either a) modified or b) deleted.
     * This number is shown/provided to the passenger after they have scheduled their flight check-in.
     *
     * @param int $length  Length of reference number to generate. Defaults to 8.
     * @return string
     */
    function generateReferenceNum($length = 8)
    {
        $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

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
    function calculateCheckinTime($flightDateTime)
    {
        $checkinDateTime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flightDateTime)));
        $checkinDateTime = explode(" ", $checkinDateTime);

        return $checkinDateTime;
    }

    /**
     * formatDateTime
     *
     * Take a date and time and prepare it (in the proper crontab format) for use in creating a cron job.
     *
     * @param string $flightDate  The date that the cron job will be set to run at (format: yyyy-mm-dd).
     * @param string $flightTime  The time that the cron job will be set to run at (format: HH:mm).
     * @return string
     */
    function formatDateTime($flightDate, $flightTime)
    {
        $flightDateYear = getYearFromDate($flightDate);
        $flightDateMonth = getMonthFromDate($flightDate);
        $flightDateDay = getDayFromDate($flightDate);


        $flightTimeHours = getHoursFromTime($flightTime);
        $flightTimeMinutes = getMinutesFromTime($flightTime);


        $flightDateMonth = str_replace("0", "", $flightDateMonth);
        $flightDateDay = str_replace("0", "", $flightDateDay);

        if ($flightTimeHours == "00")
        {
            $flightTimeHours = substr($flightTimeHours, 1);
        }
        if ($flightTimeMinutes == "00")
        {
            $flightTimeMinutes = substr($flightTimeMinutes, 1);
        }
        $cronDateTime = $flightTimeMinutes." ".$flightTimeHours." ".$flightDateDay." ".$flightDateMonth." *";

        return $cronDateTime;
    }

    $title = "Schedule a flight check-in";
    $formFunction = $_POST["formFunction"];
    $flightTime = $_POST["inputFlightTime"];
    $flightTimeHours = getHoursFromTime($flightTime);
    $flightTimeMinutes = getMinutesFromTime($flightTime);
    $flightDate = $_POST["inputFlightDate"];
}
else
{
    $title = "NOT AUTHORIZED";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <title><?php echo $title; ?></title>
  </head>
  <body>
<?php
if ($_SERVER['SERVER_ADDR'] == SERVER_IP_ADDR && isset($refererFile) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
{
    $referenceNum = generateReferenceNum();
    // calculate the flight check-in time for the cron job
    $checkinDateTime = calculateCheckinTime($flightDate." ".$flightTime);
    $flightDate = $checkinDateTime[0];
    $flightTime = $checkinDateTime[1];
    // prepare the cron job comment that will be entered above the cron job
    $userCronComment = "# ".$referenceNum." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$flightTime." on ".$flightDate;
    // prepare/format the cron job run time
    $cronDateTime = formatDateTime($flightDate, $flightTime);
    // prepare the command that will be run for this particular cron job
    $cronCmd = "php -f ".SW_AIRLINE_SCRIPT." ".$_POST["inputConfirmationNum"]." ".$_POST["inputFirstName"]." \"".$_POST["inputLastName"]."\"";

    if ($formFunction == "checkin")
    {
        // put all the above info together and create the cron job entry
        if (createCronTabEntry($userCronComment, $cronDateTime, $cronCmd) == 0)
        {
?>
    <form name="confirmForm" action="checkin.php" method="POST">
      <input type="hidden" id="flightCheckinScheduled" name="flightCheckinScheduled" value="true">
      <input type="hidden" id="firstName" name="firstName" value="<?php echo $_POST["inputFirstName"]; ?>">
      <input type="hidden" id="lastName" name="lastName" value="<?php echo $_POST["inputLastName"]; ?>">
      <input type="hidden" id="confirmationNum" name="confirmationNum" value="<?php echo $_POST["inputConfirmationNum"]; ?>">
      <input type="hidden" id="airline" name="airline" value="<?php echo $_POST["formControlSelectAirline"]; ?>">
      <input type="hidden" id="flightTime" name="flightTime" value="<?php echo $_POST["inputFlightTime"]; ?>">
      <input type="hidden" id="flightDate" name="flightDate" value="<?php echo $_POST["inputFlightDate"]; ?>">
      <input type="hidden" id="referenceNum" name="referenceNum" value="<?php echo $referenceNum; ?>">
    </form>
    <script type="text/javascript">document.confirmForm.submit();</script>
<?php
        }
        else
        {
            // redirect if creating the cron job entry failed
            header("Location: services/checkin.php?flightCheckinScheduled=false", true, 302);
        }
    }
}
else
{
    echo "    <h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>\n";
    echo "    <p>If you are not automatically redirected to the home page in 10 seconds, please click <a href=\"checkin.php\">here.</a></p>";
    header('Refresh: 10; URL=checkin.php');
}
?>
  </body>
</html>

