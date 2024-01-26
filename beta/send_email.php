<?php
// import PHPMailer classes into the global namespace
// these must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// load Composer's autoloader
require '../vendor/autoload.php';

// ****  DEFAULT VARIABLES - SET ME **** \\
$fromEmail = "johnny.5.flight.check-in@example.com";
$replyToEmail = "johnny.5.flight.check-in@example.com";


$firstName = $_POST["first_name"];
$lastName = $_POST["last_name"];
$fullName = $firstName." ".$lastName;
$toEmail = $_POST["email"];
$subject = "Your flight check-in";

// instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

$flightConfirmEmailBody = "<html><head><style>
.bold {font-weight: bold;}
.table {width: 50%; margin-bottom: 1rem; color: #212529;}
.table th,.table td {padding: 0.75rem; vertical-align: top; border-top: 1px solid #dee2e6;}
.table thead th {vertical-align: bottom; border-bottom: 2px solid #dee2e6;}
.table tbody + tbody {border-top: 2px solid #dee2e6;}
.table-sm th,.table-sm td {padding: 0.3rem;}
.table-bordered {border: 1px solid #dee2e6;}
.table-bordered th,.table-bordered td {border: 1px solid #dee2e6;}
.table-bordered thead th,.table-bordered thead td {border-bottom-width: 2px;}
.table-borderless th,.table-borderless td,.table-borderless thead th,.table-borderless tbody + tbody {border: 0;}
.table-striped tbody tr:nth-of-type(odd) {background-color: rgba(0, 0, 0, 0.05);}
.table-hover tbody tr:hover {color: #212529; background-color: rgba(0, 0, 0, 0.075);}
.table-primary,.table-primary > th,.table-primary > td {background-color: #b8daff;}
.table-primary th,.table-primary td,.table-primary thead th,.table-primary tbody + tbody {border-color: #7abaff;}
.table-hover .table-primary:hover {background-color: #9fcdff;}
.table-hover .table-primary:hover > td,.table-hover .table-primary:hover > th {background-color: #9fcdff;}
.table-secondary,.table-secondary > th,.table-secondary > td {background-color: #d6d8db;}
.table-secondary th,.table-secondary td,.table-secondary thead th,.table-secondary tbody + tbody {border-color: #b3b7bb;}
.table-hover .table-secondary:hover {background-color: #c8cbcf;}
.table-hover .table-secondary:hover > td,.table-hover .table-secondary:hover > th {background-color: #c8cbcf;}
.table-success,.table-success > th,.table-success > td {background-color: #c3e6cb;}
.table-success th,.table-success td,.table-success thead th,.table-success tbody + tbody {border-color: #8fd19e;}
.table-hover .table-success:hover {background-color: #b1dfbb;}
.table-hover .table-success:hover > td,.table-hover .table-success:hover > th {background-color: #b1dfbb;}
.table-info,.table-info > th,.table-info > td {background-color: #bee5eb;}
.table-info th,.table-info td,.table-info thead th,.table-info tbody + tbody {border-color: #86cfda;}
.table-hover .table-info:hover {background-color: #abdde5;}
.table-hover .table-info:hover > td,.table-hover .table-info:hover > th {background-color: #abdde5;}
.table-warning,.table-warning > th,.table-warning > td {background-color: #ffeeba;}
.table-warning th,.table-warning td,.table-warning thead th,.table-warning tbody + tbody {border-color: #ffdf7e;}
.table-hover .table-warning:hover {background-color: #ffe8a1;}
.table-hover .table-warning:hover > td,.table-hover .table-warning:hover > th {background-color: #ffe8a1;}
.table-danger,.table-danger > th,.table-danger > td {background-color: #f5c6cb;}
.table-danger th,.table-danger td,.table-danger thead th,.table-danger tbody + tbody {border-color: #ed969e;}
.table-hover .table-danger:hover {background-color: #f1b0b7;}
.table-hover .table-danger:hover > td,.table-hover .table-danger:hover > th {background-color: #f1b0b7;}
.table-light,.table-light > th,.table-light > td {background-color: #fdfdfe;}
.table-light th,.table-light td,.table-light thead th,.table-light tbody + tbody {border-color: #fbfcfc;}
.table-hover .table-light:hover {background-color: #ececf6;}
.table-hover .table-light:hover > td,.table-hover .table-light:hover > th {background-color: #ececf6;}
.table-dark,.table-dark > th,.table-dark > td {background-color: #c6c8ca;}
.table-dark th,.table-dark td,.table-dark thead th,.table-dark tbody + tbody {border-color: #95999c;}
.table-hover .table-dark:hover {background-color: #b9bbbe;}
.table-hover .table-dark:hover > td,.table-hover .table-dark:hover > th {background-color: #b9bbbe;}
.table-active,.table-active > th,.table-active > td {background-color: rgba(0, 0, 0, 0.075);}
.table-hover .table-active:hover {background-color: rgba(0, 0, 0, 0.075);}
.table-hover .table-active:hover > td,.table-hover .table-active:hover > th {background-color: rgba(0, 0, 0, 0.075);}
.table .thead-dark th {color: #fff; background-color: #343a40; border-color: #454d55;}
.table .thead-light th {color: #495057; background-color: #e9ecef; border-color: #dee2e6;}
.table-dark {color: #fff; background-color: #343a40;}
.table-dark th,.table-dark td,.table-dark thead th {border-color: #454d55;}
.table-dark.table-bordered {border: 0;}
.table-dark.table-striped tbody tr:nth-of-type(odd) {background-color: rgba(255, 255, 255, 0.05);}
.table-dark.table-hover tbody tr:hover {color: #fff; background-color: rgba(255, 255, 255, 0.075);}
</style></head><body>";

$flightConfirmEmailBody .= "<h2>Your flight has been scheduled. Please see the details below.</h2>";
$flightConfirmEmailBody .= "<table class=\"table\">";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">First Name:</td><td>".$_POST["first_name"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Last Name:</td><td>".$_POST["last_name"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Confirmation #:</td><td>".$_POST["confirmation_num"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Airline:</td><td>".$_POST["airline"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Flight Time:</td><td>".$_POST["flight_time"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Flight Date:</td><td>".$_POST["flight_date"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Reference #:</td><td>".$_POST["reference_num"]."</td></tr>";
$flightConfirmEmailBody .= "<tr><td class=\"bold\">Reference #:</td><td>".$_POST["emailBody"]."</td></tr>";
$flightConfirmEmailBody .= "</table><br>";
// $emailBody .= "<p><a href=\"#\">Modify your Reservation</a> | <a href=\"#\">Cancel your Reservation</a></p>";
$flightConfirmEmailBody .= "</body></html>";

// set PHPMailer to use the sendmail transport
$mail->isSendmail();
// set HTML Content-Type
$mail->isHTML();
// set who the message is to be sent from
$mail->setFrom($fromEmail, 'Flight Auto-Checkin');
// set an alternative reply-to address
$mail->addReplyTo($replyToEmail, 'Flight Auto-Checkin');
// set who the message is to be sent to
$mail->addAddress($toEmail, $fullName);
// set the subject line
$mail->Subject = $subject;
// read an HTML message body
$mail->msgHTML($flightConfirmEmailBody);
// send the message, check for errors
if (!$mail->send())
{
    echo "Mailer Error: " . $mail->ErrorInfo;
}
else
{
    echo "Message sent!";
}
?>
