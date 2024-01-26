<?php
require_once("Cron.php");
require_once("FlightRequest.php");
require_once("Sql.php");

// ****  DEFAULT VALUES - SET ME **** \\
define("SERVER_IP_ADDR", "xxx.xxx.xxx.xxx");
define("SCRIPT_ROOT_DIR", "/home/johnny/public_html/beta");
define("SW_AIRLINE_SCRIPT", SCRIPT_ROOT_DIR . "/airlines/southwest.php");


$firstName = $_POST['inputFirstName'];
$lastName = $_POST['inputLastName'];
if (isset($_POST['inputEmail']) && $_POST['inputEmail'] != "")
{
    $email = $_POST['inputEmail'];
    $emailUpdates = 1;
}
else
{
    $emailUpdates = 0;
}
$flightConfNum = $_POST['inputConfirmationNum'];
$airline = $_POST['formControlSelectAirline'];
$flightTime = $_POST['inputFlightTime'];
$flightDate = $_POST['inputFlightDate'];


if (isset($_SERVER['HTTP_REFERER']))
{
    $refererFile = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
if ($_SERVER['SERVER_ADDR'] == SERVER_IP_ADDR && isset($refererFile) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php")
  !== false))
{
    $db = new SQL();
    if ($db == null)
    {
        echo 'Whoops! Could not open database.';
    }

    $db->createTables();

    $cronEntry = new CronEntry();
    $flight = new FlightRequest($firstName, $lastName, $flightConfNum, $airline, $flightTime, $flightDate);

    $title = "Flight Check-in";
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
if ($_SERVER['SERVER_ADDR'] == SERVER_IP_ADDR && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false))
{
    $referenceNum = $flight->generateReferenceNum();

    $checkinDateTime = $flight->calculateCheckinTime($flight->getFlightDate()." ".$flight->getFlightTime());

    $checkinDate = $checkinDateTime[0];
    $checkinTime = $checkinDateTime[1];

    $userCronComment = "# ".$referenceNum." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at "
      .$checkinTime." on ".$checkinDate;
    $cronDateTime = $cronEntry->formatDateTime($checkinDateTime[0], $checkinDateTime[1]);
    $cronEntry->buildCronCmd($_POST["inputConfirmationNum"], $_POST["inputFirstName"], $_POST["inputLastName"]);
    $formFunction = $_POST["form-function"];

    $db->exec("INSERT INTO `flights` (`reference_num`, `first_name`, `last_name`, `flight_conf_num`, `airline`, `flight_time`, `flight_date`,
              `flight_checkin_time`, `flight_checkin_date`, `email_updates`) VALUES('$referenceNum', '$firstName', '$lastName', '$flightConfNum', '$airline',
              '$flightTime', '$flightDate', '$checkinTime', '$checkinDate', '$emailUpdates')");
    $db->close();

    if ($cronEntry->createCronEntry($userCronComment, $cronDateTime, $cronEntry->getCronCmd()) == 0)
    {
?>
        <form name="post_form" id="post_form" method="post" action="confirm.php">
          <input type="hidden" name="confirm-checkin" id="confirm-checkin" value="true">
          <input type="hidden" name="firstName" id="firstName" value="<?php echo $firstName; ?>">
          <input type="hidden" name="lastName" id="lastName" value="<?php echo $lastName; ?>">
<?php if (isset($_POST['inputEmail']) && $_POST['inputEmail'] != "") { ?>
          <input type="hidden" name="email" id="email" value="<?php echo $email;?>">
<?php } ?>
          <input type="hidden" name="confirmationNum" id="confirmationNum" value="<?php echo $flightConfNum; ?>">
          <input type="hidden" name="airline" id="airline" value="<?php echo $airline; ?>">
          <input type="hidden" name="flightTime" id="flightTime" value="<?php echo $flightTime; ?>">
          <input type="hidden" name="flightDate" id="flightDate" value="<?php echo $flightDate; ?>">
          <input type="hidden" name="referenceNum" id="referenceNum" value="<?php echo $referenceNum; ?>">
        </form>
        <script type="text/javascript">document.post_form.submit();</script>
        <?php
      }
      else
      {
          // redirect if creating the cron job entry failed
          header("Location: checkin.php?flightCheckinScheduled=false", true, 302);
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

