<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../vendor/autoload.php';

class Email
{
    private $fullName;
    private $firstName;
    private $lastName;
    private $toEmail;
    private $subject;

    function __construct(string $firstName, string $lastName, string $toEmail, string $subject)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->toEmail = $toEmail;
        $this->subject = $subject;

        $this->fullName = $this->firstName." ".$this->lastName;
        $this->mail = new PHPMailer(true);
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getToEmail()
    {
        return $this->toEmail;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}

