<?php

require_once("Main.php");

class CronEntry extends Main
{
    private $baseAirlinePath;

    function __construct()
    {
        $this->baseAirlinePath = "/home/johnny/public_html/beta/airlines";
    }

    /* function __construct($cronComment, $cronDateTime, $cronCmd)
    {
        $this->cronComment = $cronComment;
        $this->cronDateTime = $cronDateTime;
        $this->cronCmd = $cronCmd;
    } */

    private $cronCmd;

    public function buildCronCmd($confirmationNum, $firstName, $lastName)
    {
        $this->cronCmd = "php -f ".$this->baseAirlinePath."/southwest.php ".$confirmationNum." ".$firstName." ".$lastName;
    }

    public function getCronCmd()
    {
        return $this->cronCmd;
    }

    public function createCronEntry($cron_comment, $cron_datetime, $cron_cmd)
    {
        // Preserve existing crontab by retrieving its contents.
        exec('crontab -l', $cron_contents_raw);
        $cron_contents = implode(PHP_EOL, $cron_contents_raw);
        // Grab for debugging purposes.
        /* $cron_contents_html = implode("<br>", $cron_contents_raw);
        echo $cron_contents_html; */
        if (!empty($cron_contents))
        {
            // file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.'#* * * * * touch /tmp/Johnny'.PHP_EOL);
            // file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.$cron_datetime.PHP_EOL);
            // # $firstName $lastName with $airline at HH:mm on YYYY-MM-DD
            // mm HH M D * php -f /home/johnny/public_html/beta/airlines/southwest.php $confirmationNum $firstName $lastName
            file_put_contents('/tmp/crontab', $cron_contents.PHP_EOL.$cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);
            // echo $exit_code;
            return $exit_code;
        }
        else
        {
            // echo for debugging purposes.
            // echo "crontab IS empty.<br>";
            // file_put_contents('/tmp/crontab', $cron_entry.PHP_EOL);
            file_put_contents('/tmp/crontab', $cron_comment.PHP_EOL.$cron_datetime." ".$cron_cmd.PHP_EOL);
            exec('crontab /tmp/crontab', $o, $exit_code);

            return $exit_code;
        }
        return $exit_code;
    }

    public function searchForCronEntry($search_term)
    {
        // this will search for an existing cron entry (using the comment and subsequent line).
        // echo "Search for Cron Entry<br>";
        exec('crontab -l', $cron_contents_raw);
        $cron_contents = implode(PHP_EOL, $cron_contents_raw);
        $cron_contents_html = implode("<br>", $cron_contents_raw);
        // echo $cron_contents_raw[0]."<br>";
        // echo $cron_contents_raw[1]."<br>";
        echo "foreach loop<br>";
        foreach ($cron_contents_raw as $cron_line)
        {
            echo $cron_line."<br>";
        }
        echo "for loop<br>";
        /* for ($x = 0; $x < count($cron_contents_raw); $x++) {
            echo "[Line ".$x."] ".$cron_contents_raw[$x]."<br>";
        } */
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++)
        {
            echo "[Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            if (strpos($cron_contents_raw[$x], $search_term))
            {
                $found_line = $x;
            }
        }
        // echo $cron_contents."<br>";
        // echo $cron_contents_html."<br>";
        return $found_line;
    }

    private function deleteCronEntry($search_term)
    {
        // use searchForCronEntry() to find entry and then delete it.
        echo "Delete Cron Entry<br>";
        echo "Search term: ".$search_term."<br>";
        exec('crontab -l', $cron_contents_raw);
        echo "1Size of array: ".sizeof($cron_contents_raw)."<br>";
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++)
        {
            echo "[DELETE1 Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            // echo strpos($cron_contents_raw[$x], $search_term)."<br>";
            $pos = strpos($cron_contents_raw[$x], $search_term);
            if ($pos !== false)
            {
                echo "HERE1<br>";
                unset($cron_contents_raw[$x]);
                unset($cron_contents_raw[$x + 1]);
                // array_values($cron_contents_raw);
                // return true;
            }
            /* else
            {
                return false;
            } */
        }
        $cron_contents_raw = array_values($cron_contents_raw);
        echo "2Size of array: ".sizeof($cron_contents_raw)."<br>";
        unlink('/tmp/crontab');
        for ($x = 0, $size = count($cron_contents_raw); $x < $size; $x++)
        {
            echo "[DELETE2 Line ".$x."] ".$cron_contents_raw[$x]."<br>";
            file_put_contents('/tmp/crontab', $cron_contents_raw[$x].PHP_EOL, FILE_APPEND);
        }
        // file_put_contents('/tmp/crontab', $cron_contents_raw[$x]);
        echo "Print contents:<br>";
        echo exec('cat /tmp/crontab', $o, $exit_code);
        echo exec('crontab /tmp/crontab', $o, $exit_code);
    }

    private function modifyCronEntry($search_term)
    {
        // use searchForCronEntry() to find entry and then modify it.
        echo "Modify Cron Entry<br>";
    }

    public function formatDateTime($flight_date, $flight_time)
    {
        $flight_date_year = $this->getYearFromDate($flight_date);
        $flight_date_month = $this->getMonthFromDate($flight_date);
        $flight_date_day = $this->getDayFromDate($flight_date);

        $flight_time_hours = $this->getHoursFromTime($flight_time);
        $flight_time_minutes = $this->getMinutesFromTime($flight_time);

        $flight_date_month = str_replace("0", "", $flight_date_month);
        $flight_date_day = str_replace("0", "", $flight_date_day);

        if ($flight_time_hours == "00")
        {
            $flight_time_hours = substr($flight_time_hours, 1);
        }

        if ($flight_time_minutes == "00")
        {
            $flight_time_minutes = substr($flight_time_minutes, 1);
        }

        $cron_datetime = $flight_time_minutes." ".$flight_time_hours." ".$flight_date_day." ".$flight_date_month." *";

        return $cron_datetime;
    }
}

