<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
      integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Flight Check-in</title>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </head>
  <body>
    <div class="container">
      <div class="w3-bar w3-border-top w3-border-bottom w3-border-left w3-border-right w3-light-grey">
        <button class="w3-bar-item w3-button tablink w3-dark-grey" onclick="openForm(event,'checkin-form-container')">Check-in to flight</button>
      </div>

      <div id="checkin-form-container" class="form">
        <br>
        <form class="needs-validation" novalidate autocomplete="off" id="checkin" method="POST" action="schedule.php">
          <input type="hidden" id="form-function" name="form-function" value="checkin">
          <div class="form-group">
            <label for="inputFirstName">First Name</label>
            <input type="text" class="form-control" name="inputFirstName" id="inputFirstName" placeholder="Enter first name">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="inputLastName">Last Name</label>
            <input type="text" class="form-control" name="inputLastName" id="inputLastName" placeholder="Enter last name">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail">Email</label> <small>(confirmation email and email updates)</small>
            <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Enter email address">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="inputConfirmationNum">Confirmation #</label>
            <input maxlength="6" type="text" class="form-control" name="inputConfirmationNum" id="inputConfirmationNum"
              placeholder="Enter confirmation number">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="formControlSelectAirline">Airline</label>
            <select class="form-control" name="formControlSelectAirline" id="formControlSelectAirline">
              <option>Please select an airline</option>
              <option>Southwest</option>
            </select>
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="inputFlightTime">Flight Departure Time</label>
            <input type="time" class="form-control" name="inputFlightTime" id="inputFlightTime">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <div class="form-group">
            <label for="inputFlightDate">Flight Departure Date</label>
            <input type="date" class="form-control" name="inputFlightDate" id="inputFlightDate">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <button type="submit" class="btn btn-primary" id="submitBtn">Check-in to flight</button>
          <p id="show_message" style="display: none">Form data sent.</p>
          <div id="dialog" style="display: none">
        </form>
      </div>
    </div>
    <script>
      function openForm(evt, formName)
      {
        var i, x, tablinks;
        x = document.getElementsByClassName("form");
        for (i = 0; i < x.length; i++)
        {
          x[i].style.display = "none";  
        }
        tablinks = document.getElementsByClassName("tablink");
        for (i = 0; i < tablinks.length; i++)
        {
          tablinks[i].className = tablinks[i].className.replace(" w3-dark-grey", "");
        }
        document.getElementById(formName).style.display = "block";  
        evt.currentTarget.className += " w3-dark-grey";
      }
    </script>
  </body>
</html>