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

require_once("Cron.php");
require_once("FlightRequest.php");
require_once("Sql.php");


$firstName = $_POST['inputFirstName'];
$lastName = $_POST['inputLastName'];
$flightConfNum = $_POST['inputConfirmationNum'];
$airline = $_POST['formControlSelectAirline'];
$flightTime = $_POST['inputFlightTime'];
$flightDate = $_POST['inputFlightDate'];
$referenceNum = $_POST['inputReferenceNum'];

if (isset($_SERVER['HTTP_REFERER']))
{
    $refererFile = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
// if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx" && isset($refererFile) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx")
{
    $db = new SQL();
    if ($db == null)
    {
        echo 'Whoops! Could not open database.';
    }

    $db->createTables();

    $cronEntry = new CronEntry();
    $flight = new FlightRequest($firstName, $lastName, $flightConfNum, $airline, $flightTime, $flightDate);

    $title = "Flight Checkin - Scheduling...";
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
// if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx")
{
    $checkinDateTime = $flight->calculateCheckinTime($flight->getFlightDate()." ".$flight->getFlightTime());

    $checkinDate = $checkinDateTime[0];
    $checkinTime = $checkinDateTime[1];

    $userCronComment = "# ".$referenceNum." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$checkinTime." on ".$checkinDate;
    $cronDateTime = $cronEntry->formatDateTime($checkinDateTime[0], $checkinDateTime[1]);
    $cronEntry->buildCronCmd($_POST["inputConfirmationNum"], $_POST["inputFirstName"], $_POST["inputLastName"]);
    $formFunction = $_POST["form-function"];

    $sql =<<<EOF
      INSERT INTO flights (reference_num, first_name, last_name, flight_conf_num, airline, flight_time, flight_date, flight_checkin_time, flight_checkin_date)
      VALUES("$referenceNum", "$firstName", "$lastName", "$flightConfNum", "$airline", "$flightTime", "$flightDate", "$checkinTime", "$checkinDate");
EOF;

    $db->exec($sql);
    $db->close();

    $cronEntry->createCronEntry($userCronComment, $cronDateTime, $cronEntry->getCronCmd());
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

