<?php
// ****  DEFAULT VARIABLES - SET ME **** \\
$airline_script = "";


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer_file = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
}
if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false)) {
    function createCronEntry($cron_comment, $cron_datetime, $cron_cmd) {
        // Preserve existing crontab by retrieving its contents.
        exec('crontab -l', $cron_contents_raw);
        $cron_contents = implode(PHP_EOL, $cron_contents_raw);

        if (!empty($cron_contents)) {
            file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.$cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);
            return $exit_code;
        } else {
            file_put_contents('/tmp/crontab', $cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);
            return $exit_code;
        }
        return $exit_code;
    }

    function generateReferenceNum($length = 8) {
        $characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
        $characters_length = strlen($characters);
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, $characters_length - 1)];
        }
        return $random_string;
    }

    // Given YYYY-MM-DD, extract YYYY.
    function getYearFromDate($date) {
        $date = explode("-", $date);
        $date_year = $date[0];
        return $date_year;
    }

    // Given YYYY-MM-DD, extract MM.
    function getMonthFromDate($date) {
        $date = explode("-", $date);
        $date_month = $date[1];
        return $date_month;
    }

    // Given YYYY-MM-DD, extract DD.
    function getDayFromDate($date) {
        $date = explode("-", $date);
        $date_day = $date[2];
        return $date_day;
    }

    // Given HH:mm, extract HH.
    function getHoursFromTime($time) {
        $time = explode(":", $time);
        $time_hour = $time[0];
        return $time_hour;
    }

    // Given HH:mm, extract mm.
    function getMinutesFromTime($time) {
        $time = explode(":", $time);
        $time_minutes = $time[1];
        return $time_minutes;
    }

    function calculateCheckinTime($flight_datetime) {
        $checkin_datetime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flight_datetime)));
        $checkin_datetime = explode(" ", $checkin_datetime);

        return $checkin_datetime;
    }

    function formatDateTime($flight_date, $flight_time) {
        $flight_date_year = getYearFromDate($flight_date);
        $flight_date_month = getMonthFromDate($flight_date);
        $flight_date_day = getDayFromDate($flight_date);

        $flight_time_hours = getHoursFromTime($flight_time);
        $flight_time_minutes = getMinutesFromTime($flight_time);

        $flight_date_month = str_replace("0", "", $flight_date_month);
        $flight_date_day = str_replace("0", "", $flight_date_day);

        if ($flight_time_hours == "00") {
            $flight_time_hours = substr($flight_time_hours, 1);
        }

        if ($flight_time_minutes == "00") {
            $flight_time_minutes = substr($flight_time_minutes, 1);
        }

        $cron_datetime = $flight_time_minutes." ".$flight_time_hours." ".$flight_date_day." ".$flight_date_month." *";

        return $cron_datetime;
    }

    $title = "Schedule a flight check-in";
    $formFunction = $_POST["form-function"];
    $flight_time = $_POST["inputFlightTime"];
    $flight_time_hours = getHoursFromTime($flight_time);
    $flight_time_minutes = getMinutesFromTime($flight_time);
    $flight_date = $_POST["inputFlightDate"];
    $flight_date_year = getYearFromDate($flight_date);
    $flight_date_month = getMonthFromDate($flight_date);
    $flight_date_day = getDayFromDate($flight_date);
} else {
    $title = "NOT AUTHORIZED";
    echo "<h1>YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE</h1>\n";
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
<body>
<?php

if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false)) {
    $reference_num = generateReferenceNum();

    $checkin_datetime = calculateCheckinTime($flight_date." ".$flight_time);

    $user_cron_comment = "# ".$reference_num." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with "
        .$_POST["formControlSelectAirline"]." at ".$checkin_datetime[1]." on ".$checkin_datetime[0];
    $cron_datetime = formatDateTime($checkin_datetime[0], $checkin_datetime[1]);
    $cron_cmd = "php -f ".$airline_script." ".$_POST["inputConfirmationNum"]." ".$_POST["inputFirstName"]." \"".$_POST["inputLastName"]."\"";

    if ($formFunction == "checkin") {
        if (createCronEntry($user_cron_comment, $cron_datetime, $cron_cmd) == 0) {
            ?>
            <form name="post_form" action="checkin.php" method="POST">
              <input type="hidden" id="confirm" name="confirm" value="checkin">
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
        } else {
            header("Location: services/checkin.php?scheduled=false", true, 301);
        }
    } else if ($formFunction == "modify") {
        $flight = searchForCronEntry($user_cron_comment);
        modifyCronEntry();
    } else if ($formFunction == "delete") {
        deleteCronEntry($user_cron_comment);
    }
}

?>

</body>
</html>