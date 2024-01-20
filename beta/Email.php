<?php
// SPDX-License-Identifier: GPL-3.0-or-later

// import PHPMailer classes into the global namespace
// these must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// load Composer's autoloader
require '../vendor/autoload.php';

class Email
{
    private $fullName;
    private $firstName;
    private $lastName;
    private $toEmail;
    private $subject;
    private $fromEmail;
    private $replyToEmail;

    function __construct(string $firstName, string $lastName, string $toEmail, string $subject)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->fullName = "{$firstName} {$lastName}";

        $this->mail = new PHPMailer(true);
    }

    // 'set' Methods
    public function setFromEmail($email)
    {
        $this->fromEmail = $email;
    }

    public function setReplyToEmail($email)
    {
        $this->replyToEmail = $email;
    }

    // 'get' Methods
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    public function getReplyToEmail()
    {
        if ($this->replyToEmail == NULL)
        {
            return $this->getFromEmail();
        }
        else
        {
            return $this->replyToEmail;
        }
    }

    public function getToEmail()
    {
        return $this->toEmail;
    }

    public function getSubject()
    {
        return $this->subject;
    }


    /* public function prepareEmail()
    {

    } */
}

