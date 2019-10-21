<?php
require_once("Cron.php");
require_once("FlightRequest.php");

if (isset($_SERVER['HTTP_REFERER']))
{
    $referer_file = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
/* if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php")
  !== false)) */
if ($_SERVER['SERVER_ADDR'] == "10.0.1.9")
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title><?php echo $title; ?></title>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
<body>
<?php

/* if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php")
  !== false)) */
if ($_SERVER['SERVER_ADDR'] == "10.0.1.9")
{
    $reference_num = $flight->generateReferenceNum();

    $checkinDateTime = $flight->calculateCheckinTime($flight->getFlightDate()." ".$flight->getFlightTime());

    $checkinDate = $checkinDateTime[0];
    $checkinTime = $checkinDateTime[1];

    $userCronComment = "# ".$reference_num." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$checkinTime." on ".$checkinDate;
    $cronDateTime = $cronEntry->formatDateTime($checkinDateTime[0], $checkinDateTime[1]);
    $cronEntry->buildCronCmd($_POST["inputConfirmationNum"], $_POST["inputFirstName"], $_POST["inputLastName"]);
    $formFunction = $_POST["form-function"];

    //echo "Ref #: ".generateReferenceNum()."<br>";

    if (isset($formFunction) && $formFunction == "checkin")
    {
        if ($cronEntry->createCronEntry($userCronComment, $cronDateTime, $cronEntry->getCronCmd()) == 0)
        {
            ?>
            <form name="post_form" action="checkin.php" method="POST">
              <input type="hidden" id="confirm-checkin" name="confirm-checkin" value="true">
              <!-- <input type="hidden" id="form_function" name="form_function" value="confirm-checkin"> -->
              <input type="hidden" id="firstName" name="firstName" value="<?php echo $_POST["inputFirstName"]; ?>">
              <input type="hidden" id="lastName" name="lastName" value="<?php echo $_POST["inputLastName"]; ?>">
              <input type="hidden" id="confirmationNum" name="confirmationNum" value="<?php echo $_POST["inputConfirmationNum"]; ?>">
              <input type="hidden" id="airline" name="airline" value="<?php echo $_POST["formControlSelectAirline"]; ?>">
              <input type="hidden" id="flightTime" name="flightTime" value="<?php echo $_POST["inputFlightTime"]; ?>">
              <input type="hidden" id="flightDate" name="flightDate" value="<?php echo $_POST["inputFlightDate"]; ?>">
              <input type="hidden" id="referenceNum" name="referenceNum" value="<?php echo $reference_num; ?>">
            </form>
            <script type="text/javascript">
              document.post_form.submit();
            </script>
            <?php
        }
        else
        {
            ?>
            <form name="post_form" action="checkin.php" method="POST">
              <input type="hidden" id="confirm-checkin" name="confirm-checkin" value="false">
            <?php
            header("Location: services/flight/checkin.php?scheduled=false", true, 301);
            //echo "<h1>Something went wrong and your flight has NOT been scheduled.</h1>";
        }
    }
    else if (isset($form_function) && $form_function == "modify")
    {
        $flight = searchForCronEntry($user_cron_comment);
        modifyCronEntry();
    }
    else if (isset($form_function) && $form_function == "delete")
    {
        //$flight = searchForCronEntry($user_cron_comment);
        echo "HERE2<br>";
        deleteCronEntry($user_cron_comment);
    }
}
else
{
    echo "  <h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>";
}



?>

</body>
</html>