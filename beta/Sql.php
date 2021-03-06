<?php

class SQL extends SQLite3
{
  function __construct()
  {
    try
    {
      $this->enableExceptions(true);
      $this->open('db/sqlite.db');
    }
    catch (Exception $e)
    {
      echo "Couldn't create SQLite connection.\n";
      echo $this->lastErrorCode()."\n";
    }
  }

  public function createTables()
  {
    $commands = [
      'CREATE TABLE IF NOT EXISTS flights
      (
        reference_num       TEXT PRIMARY KEY,
        first_name          TEXT NOT NULL,
        last_name           TEXT NOT NULL,
        flight_conf_num     TEXT NOT NULL,
        airline             TEXT NOT NULL,
        flight_time         TEXT NOT NULL,
        flight_date         TEXT NOT NULL,
        flight_checkin_time TEXT NOT NULL,
        flight_checkin_date TEXT NOT NULL,
        email_updates       INTEGER NOT NULL
      )'
    ];

    foreach ($commands as $command)
    {
      $this->exec($command);
    }
  }

  public function getRecord($sql)
  {
    $result = $this->query($sql);

    $row = $result->fetchArray();
    if ($row = 0)
    {
      return;
    }
    return $result;
  }

  public function getFirstName($reference_num)
  {    
    $sql = "SELECT first_name FROM flights WHERE reference_num='".$reference_num."'";

    $result = $this->querySingle($sql);
  }

}
