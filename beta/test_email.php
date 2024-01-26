<html>
<body>
<div id="checkin-form-container" class="form">
        <br>
        <form class="needs-validation" novalidate autocomplete="off" id="checkin" method="POST" action="send_email_new.php">
          <input type="hidden" id="form-function" name="form-function" value="checkin">
          <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter first name">
          </div>
          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter last name">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input maxlength="50" type="text" class="form-control" name="email" id="email" placeholder="Enter email address">
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input maxlength="50" type="text" class="form-control" name="subject" id="subject" placeholder="Enter email subject">
          </div>
          <input type="hidden" name="emailBody" id="emailBody" value="flightConfirmation">
          <button type="submit" class="btn btn-primary" id="submitBtn">Send Email</button>
        </form>
      </div>
</body>
</html>

