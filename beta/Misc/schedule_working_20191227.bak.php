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

if (isset($_SERVER['HTTP_REFERER']))
{
    $referer_file = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
// if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
if ($_SERVER['SERVER_ADDR'] == "xxx.xxx.xxx.xxx")
{
    $cronEntry = new CronEntry();
    $flight = new FlightRequest($_POST['inputFirstName'], $_POST['inputLastName'], $_POST['inputConfirmationNum'],
      $_POST['formControlSelectAirline'], $_POST['inputFlightTime'], $_POST['inputFlightDate']);

    $title = "Schedule a flight check-in (beta)";
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
    $reference_num = $flight->generateReferenceNum();

    $checkinDateTime = $flight->calculateCheckinTime($flight->getFlightDate()." ".$flight->getFlightTime());

    $checkinDate = $checkinDateTime[0];
    $checkinTime = $checkinDateTime[1];

    $userCronComment = "# ".$reference_num." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$checkinTime." on ".$checkinDate;
    $cronDateTime = $cronEntry->formatDateTime($checkinDateTime[0], $checkinDateTime[1]);
    $cronEntry->buildCronCmd($_POST["inputConfirmationNum"], $_POST["inputFirstName"], $_POST["inputLastName"]);
    $formFunction = $_POST["form-function"];

    // echo "Ref #: ".generateReferenceNum()."<br>";

    if (isset($formFunction) && $formFunction == "checkin")
    {
        if ($cronEntry->createCronEntry($userCronComment, $cronDateTime, $cronEntry->getCronCmd()) == 0)
        {
?>
      <form name="confirmForm" action="checkin.php" method="POST">
        <input type="hidden" name="flightCheckinScheduled" id="flightCheckinScheduled" value="true">
        <input type="hidden" name="firstName" id="firstName" value="<?php echo $_POST["inputFirstName"]; ?>">
        <input type="hidden" name="lastName" id="lastName" value="<?php echo $_POST["inputLastName"]; ?>">
        <input type="hidden" name="confirmationNum" id="confirmationNum" value="<?php echo $_POST["inputConfirmationNum"]; ?>">
        <input type="hidden" name="airline" id="airline" value="<?php echo $_POST["formControlSelectAirline"]; ?>">
        <input type="hidden" name="flightTime" id="flightTime" value="<?php echo $_POST["inputFlightTime"]; ?>">
        <input type="hidden" name="flightDate" id="flightDate" value="<?php echo $_POST["inputFlightDate"]; ?>">
        <input type="hidden" name="referenceNum" id="referenceNum" value="<?php echo $reference_num; ?>">
      </form>
      <script type="text/javascript">document.post_form.submit();</script>
<?php
      }
      else
      {
?>
      <!-- <form name="confirmForm" action="checkin.php" method="POST">
        <input type="hidden" name="flightCheckinScheduled" id="flightCheckinScheduled" value="false"> -->
<?php
        // redirect if creating the cron job entry failed
        header("Location: checkin.php?scheduled=false", true, 301);
        }
    }
    else if (isset($form_function) && $form_function == "modify")
    {
        $flight = searchForCronEntry($user_cron_comment);
        modifyCronEntry();
    }
    else if (isset($form_function) && $form_function == "delete")
    {
        // $flight = searchForCronEntry($user_cron_comment);
        echo "HERE2<br>";
        deleteCronEntry($user_cron_comment);
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

