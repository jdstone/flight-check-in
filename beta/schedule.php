<?php
  require_once("Cron.php");
  require_once("FlightRequest.php");
  require_once("Sql.php");


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
  if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($refererFile) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php")
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title><?php echo $title; ?></title>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
<body>
<?php
  if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php")
    !== false))
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
        <form id="post_form" name="post_form" method="post" action="confirm.php">
          <input type="hidden" id="confirm-checkin" name="confirm-checkin" value="true">
          <input type="hidden" id="firstName" name="firstName" value="<?php echo $firstName; ?>">
          <input type="hidden" id="lastName" name="lastName" value="<?php echo $lastName; ?>">
          <?php if (isset($_POST['inputEmail']) && $_POST['inputEmail'] != "") { ?>
            <input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
          <?php } ?>
          <input type="hidden" id="confirmationNum" name="confirmationNum" value="<?php echo $flightConfNum; ?>">
          <input type="hidden" id="airline" name="airline" value="<?php echo $airline; ?>">
          <input type="hidden" id="flightTime" name="flightTime" value="<?php echo $flightTime; ?>">
          <input type="hidden" id="flightDate" name="flightDate" value="<?php echo $flightDate; ?>">
          <input type="hidden" id="referenceNum" name="referenceNum" value="<?php echo $referenceNum; ?>">
        </form>
        <script type="text/javascript">
          document.post_form.submit();
        </script>
        <?php
      }
  }
  else
  {
    echo "  <h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>";
  }
?>

</body>
</html>