<?php
// ****  DEFAULT VARIABLES - SET ME **** \\
$airline_script = "";


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer_file = substr($_SERVER['HTTP_REFERER'], (strrpos($_SERVER['HTTP_REFERER'], "/") + 1));
    /* echo $referer_file."<br>";
    echo strpos($referer_file, "checkin.html");
    echo strpos($_SERVER['HTTP_REFERER'], "checkin.html"); */
}
if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false)) {
    function createCronEntry($cron_comment, $cron_datetime, $cron_cmd) {
        // Preserve existing crontab by retrieving its contents.
        exec('crontab -l', $cron_contents_raw);
        $cron_contents = implode(PHP_EOL, $cron_contents_raw);
        // Grab for debugging purposes.
        /* $cron_contents_html = implode("<br>", $cron_contents_raw);
        echo $cron_contents_html; */
        if (!empty($cron_contents)) {
            //file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.'#* * * * * touch /tmp/Jerry'.PHP_EOL);
            //file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.$cron_datetime.PHP_EOL);
            // # $firstName $lastName with $airline at HH:mm on YYYY-MM-DD
            // mm HH M D * php -f script.php $confirmationNum $firstName $lastName
            file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.$cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);
            //echo $exit_code;
            return $exit_code;
        } else {
            // Echo for debugging purposes.
            //echo "crontab IS empty.<br>";
            //file_put_contents('/tmp/crontab', $cron_entry.PHP_EOL);
            file_put_contents('/tmp/crontab', $cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);
            return $exit_code;
        }
        return $exit_code;
    }

    function searchForCronEntry($search_term) {
        // This will search for an existing cron entry (using the comment and subsequent line).
        //echo "Search for Cron Entry<br>";
        exec('crontab -l', $cron_contents_raw);
        $cron_contents = implode(PHP_EOL, $cron_contents_raw);
        $cron_contents_html = implode("<br>", $cron_contents_raw);
        //echo $cron_contents_raw[0]."<br>";
        //echo $cron_contents_raw[1]."<br>";
        echo "foreach loop<br>";
        foreach ($cron_contents_raw as $cron_line) {
            echo $cron_line."<br>";
        }
        echo "for loop<br>";
        /* for ($x = 0; $x < count($cron_contents_raw); $x++) {
            echo "[Line ".$x."] ".$cron_contents_raw[$x]."<br>";
        } */
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++) {
            echo "[Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            if (strpos($cron_contents_raw[$x], $search_term)) {
                $found_line = $x;
            }
        }
        //echo $cron_contents."<br>";
        //echo $cron_contents_html."<br>";
        return $found_line;
    }

    function deleteCronEntry($search_term) {
        // Use searchForCronEntry() to find entry and then delete it.
        echo "Delete Cron Entry<br>";
        echo "Search term: ".$search_term."<br>";
        exec('crontab -l', $cron_contents_raw);
        echo "1Size of array: ".sizeof($cron_contents_raw)."<br>";
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++) {
            echo "[DELETE1 Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            //echo strpos($cron_contents_raw[$x], $search_term)."<br>";
            $pos = strpos($cron_contents_raw[$x], $search_term);
            if ($pos !== false) {
                echo "HERE1<br>";
                unset($cron_contents_raw[$x]);
                unset($cron_contents_raw[$x + 1]);
                //array_values($cron_contents_raw);
                //return true;
            }/*  else {
                return false;
            } */
        }
        $cron_contents_raw = array_values($cron_contents_raw);
        echo "2Size of array: ".sizeof($cron_contents_raw)."<br>";
        unlink('/tmp/crontab');
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++) {
            echo "[DELETE2 Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            file_put_contents('/tmp/crontab', $cron_contents_raw[$x].PHP_EOL, FILE_APPEND);
        }
        //file_put_contents('/tmp/crontab', $cron_contents_raw[$x]);
        echo "Print contents:<br>";
        echo exec('cat /tmp/crontab', $o, $exit_code);
        echo exec('crontab /tmp/crontab', $o, $exit_code);
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

    function modifyCronEntry($search_term) {
        // Use searchForCronEntry() to find entry and then modify it.
        echo "Modify Cron Entry<br>";
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
        //$checkin_datetime = date("Y-m-d H:i", strtotime('-1 minutes', strtotime($flight_datetime)));
        $checkin_datetime = date("Y-m-d H:i", strtotime('-1439 minutes', strtotime($flight_datetime)));
        $checkin_datetime = explode(" ", $checkin_datetime);
        /* echo "ALERT ALERT ".$checkin_datetime[0]."<br>";
        echo "ALERT ALERT ".$checkin_datetime[1]; */
        ////$checkin_datetime[0]; // date
        ////$checkin_datetime[1]; // time
        ////date("Y-m-d H:i", strtotime('-1 minutes', strtotime($flight_datetime)));

        /* $flight_datetime = explode(" ", $flight_datetime);
        $flight_date = $flight_datetime[0];
        $flight_time = $flight_datetime[1];

        $flight_date_year = getYearFromDate($flight_date);
        $flight_date_month = getMonthFromDate($flight_date);
        $flight_date_day = getDayFromDate($flight_date);

        // Find check-in day (and maybe check-in month, if initial day was "01").
        if ($flight_date_day == "01") {
            if ($flight_date_month == "01") {
                $flight_date_year = $flight_date_year - 1;
                $flight_date_month = "12";
            } else {
                $flight_date_month = $flight_date_month - 1;
            }
            $adjusted_flight_date = $flight_date_year."-".$flight_date_month."-".$flight_date_day;
            //echo "adjusted_flight_date: ".$adjusted_flight_date."<br>";
            $checkin_date = date("Y-m-t", strtotime($adjusted_flight_date));
            //echo "checkin_date: ".$checkin_date."<br>";
            //echo "checkin_date_month: ".getMonthFromDate($checkin_date)."<br>";
            //echo "checkin_date_day: ".getDayFromDate($checkin_date)."<br>";
        } else {
            $flight_date_day = $flight_date_day - 1;
            $adjusted_flight_date = $flight_date_year."-".$flight_date_month."-".$flight_date_day;
            $checkin_date = date("Y-m-d", strtotime($adjusted_flight_date));
            //echo "checkin_date: ".$checkin_date."<br>";
        }

        // Find check-in time.
        $checkin_time = strtotime('-1 minutes', strtotime($flight_time));

        return $checkin_date." ".date("H:i", $checkin_time); */
        return $checkin_datetime;
    }

    //function formatDateTime($flight_date_month, $flight_date_day, $flight_time_hours, $flight_time_minutes) {
    //function formatDateTime($flight_datetime) {
    function formatDateTime($flight_date, $flight_time) {
        /* $flight_datetime = explode(" ", $flight_datetime);
        $flight_date = $flight_datetime[0];
        $flight_time = $flight_datetime[1]; */

        $flight_date_year = getYearFromDate($flight_date);
        $flight_date_month = getMonthFromDate($flight_date);
        $flight_date_day = getDayFromDate($flight_date);

        $flight_time_hours = getHoursFromTime($flight_time);
        $flight_time_minutes = getMinutesFromTime($flight_time);


        $flight_date_month = str_replace("0", "", $flight_date_month);
        $flight_date_day = str_replace("0", "", $flight_date_day);

        if ($flight_time_hours == "00") {
            $flight_time_hours = substr($flight_time_hours, 1);
        }/*  else {
            $flight_time_hours = str_replace("0", "", $flight_time_hours);
        } */

        if ($flight_time_minutes == "00") {
            $flight_time_minutes = substr($flight_time_minutes, 1);
        }/*  else {
            $flight_time_minutes = str_replace("0", "", $flight_time_minutes);
        } */

        $cron_datetime = $flight_time_minutes." ".$flight_time_hours." ".$flight_date_day." ".$flight_date_month." *";

        return $cron_datetime;
    }

    function echoDebugInfo($formFunction, $time_hours, $time_minutes, $date_year, $date_month, $date_day) {
        echo "<strong>Form Function:</strong> ".$formFunction."<br>";
        echo "<strong>First Name:</strong> ".$_POST["inputFirstName"]."<br>";
        echo "<strong>Last Name:</strong> ".$_POST["inputLastName"]."<br>";
        echo "<strong>Confirmation #:</strong> ".$_POST["inputConfirmationNum"]."<br>";
        echo "<strong>Airline:</strong> ".$_POST["formControlSelectAirline"]."<br>";
        echo "<strong>Flight Time:</strong> ".$_POST["inputFlightTime"]."<br>";
        echo "<strong>Flight Time (without colon):</strong> ". str_replace(":", "", $_POST["inputFlightTime"])."<br>";
        echo "<strong>Flight Time Hours:</strong> ".$time_hours."<br>";

        if ($time_hours == "00") {
            echo "<strong>Flight Time Hours (without ZERO):</strong> ".substr($time_hours, 1)."<br>";
        } else {
            echo "<strong>Flight Time Hours (without ZERO):</strong> ".str_replace("0", "", $time_hours)."<br>";
        }

        echo "<strong>Flight Time Minutes:</strong> ".$time_minutes."<br>";

        if ($time_minutes == "00") {
            echo "<strong>Flight Time Minutes (without ZERO):</strong> ".substr($time_minutes, 1)."<br>";
        } else {
            echo "<strong>Flight Time Minutes (without ZERO):</strong> ".str_replace("0", "", $time_minutes)."<br>";
        }

        echo "<strong>Flight Date:</strong> ".$_POST["inputFlightDate"]."<br>";
        echo "<strong>Flight Date (without dashes):</strong> ". str_replace("-", "", $_POST["inputFlightDate"])."<br>";
        echo "<strong>Flight Date Year:</strong> ".$date_year."<br>";
        echo "<strong>Flight Date Month:</strong> ".$date_month."<br>";
        echo "<strong>Flight Date Month (without ZERO):</strong> ".str_replace("0", "", $date_month)."<br>";
        echo "<strong>Flight Date Day:</strong> ".$date_day."<br>";
        echo "<strong>Flight Date Day (without ZERO):</strong> ".str_replace("0", "", $date_day)."<br>";
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
//$_POST['text'] = 'another value';
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

if ($_SERVER['SERVER_ADDR'] == "10.0.1.9" && isset($referer_file) && (strpos($_SERVER['HTTP_REFERER'], "checkin.php") !== false)) {
    $reference_num = generateReferenceNum();
    //echoDebugInfo($formFunction, $flight_time_hours, $flight_time_minutes, $flight_date_year, $flight_date_month, $flight_date_day);
    echo "Reference #: ".$reference_num."<br>";

    $checkin_datetime = calculateCheckinTime($flight_date." ".$flight_time);
    echo $checkin_datetime[0]."<br>";
    echo $checkin_datetime[1]."<br>";
    //echo "<strong>Check-in Time:</strong> ".$checkin_datetime."<br>";

    //$user_cron_comment = "# ".$reference_num." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$_POST["inputFlightTime"]." on ".$_POST["inputFlightDate"];
    $user_cron_comment = "# ".$reference_num." ".$_POST["inputFirstName"]." ".$_POST["inputLastName"]." with ".$_POST["formControlSelectAirline"]." at ".$checkin_datetime[1]." on ".$checkin_datetime[0];
    //$flight_date_time = ltrim($flight_time_minutes, "0")." ".ltrim($flight_time_hours, "0")." ".ltrim($flight_date_day, "0")." ".ltrim($flight_date_month, "0")." *";
    //$flight_date_time = substr($flight_time_minutes, 1)." ".substr($flight_time_hours, 1)." ".substr($flight_date_day, 1)." ".substr($flight_date_month, 1)." *";
    //$cron_datetime = formatDateTime($flight_date_month, $flight_date_day, $flight_time_hours, $flight_time_minutes);
    //$cron_datetime = formatDateTime($flight_date, $flight_time);
    //$cron_datetime = formatDateTime($checkin_datetime);
    $cron_datetime = formatDateTime($checkin_datetime[0], $checkin_datetime[1]);
    //echo "<strong>Check-in Time (Cron):</strong> ".$cron_datetime."<br>";
    $cron_cmd = "php -f ".$airline_script." ".$_POST["inputConfirmationNum"]." ".$_POST["inputFirstName"]." \"".$_POST["inputLastName"]."\"";

    //echo "Ref #: ".generateReferenceNum()."<br>";

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
            <!-- <script>
              $(document).ready(function() {
                $('#exampleModal').modal('show');
              });
            </script>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
              Launch demo modal
            </button>
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div> -->
            <?php
            /* echo "<h1>Your flight has been scheduled.</h1>";
            echo "<strong>Reference #:</strong> ".$reference_num."<br>"; */
        } else {
            header("Location: services/checkin.php?scheduled=false", true, 301);
            //echo "<h1>Something went wrong and your flight has NOT been scheduled.</h1>";
        }
    } else if ($formFunction == "modify") {
        $flight = searchForCronEntry($user_cron_comment);
        modifyCronEntry();
    } else if ($formFunction == "delete") {
        //$flight = searchForCronEntry($user_cron_comment);
        echo "HERE2<br>";
        deleteCronEntry($user_cron_comment);
    }
}



?>

</body>
</html>