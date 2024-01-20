<?php
// SPDX-License-Identifier: GPL-3.0-or-later

// ****  DEFAULT VALUES - SET ME **** \\
define("SUPPORT_EMAIL", "johnny.appleseed@example.com");
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" integrity="sha384-3egDN28gONS81uTCjUvY2t7oN3kDAKncL7Rn2Jj94Ihi+PycXffr0EQuJ95luUT8" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <title>Flight Check-in</title>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.0.min.js" integrity="sha384-JUMjoW8OzDJw4oFpWIB2Bu/c6768ObEthBMVSiIx4ruBIEdyNSUQAjJNFqT5pnJ6" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
      (function()
      {
        'use strict';
        window.addEventListener('load', function()
        {
          // fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');
          // loop over them and prevent submission
          var validation = Array.prototype.filter.call(forms, function(form)
          {
            form.addEventListener('submit', function(event)
            {
              if (form.checkValidity() === false)
              {
                event.preventDefault();
                event.stopPropagation();
              }
              form.classList.add('was-validated');
            }, false);
          });
        }, false);
      })();
    </script>
  </head>
  <body>
<?php
if ((isset($_POST['flightCheckinScheduled'])) && ($_POST['flightCheckinScheduled'] == "true"))
{
?>
    <script>
      $(document).ready(function()
      {
        $('#confirmationModal').modal('show');

        // replace (reload) [the original] checkin.php page so old form data does not exist
        $('#confirmationModal').on('hidden.bs.modal', function ()
        {
          setTimeout(function(){ window.location.replace("<?php echo $_SERVER['PHP_SELF'] ?>"); }, 0);
        });
      });

      $(function()
      {
        $('#confirmation').on('submit', function (e)
        {
          $('#inProgress').show();
          e.preventDefault();
          var first_name = "<?php echo $_POST['firstName']; ?>";
          var last_name = "<?php echo $_POST['lastName']; ?>";
          var confirmation_num = "<?php echo $_POST['confirmationNum']; ?>";
          var airline = "<?php echo $_POST['airline']; ?>";
          var flight_time = "<?php echo $_POST['flightTime']; ?>";
          var flight_date = "<?php echo $_POST['flightDate']; ?>";
          var reference_num = "<?php echo $_POST['referenceNum']; ?>";
          var email = $("#inputEmail").val();
          var data_string = 'email=' + encodeURIComponent(email) + '&first_name=' + first_name + '&last_name=' + last_name + '&confirmation_num=' + confirmation_num;
          data_string += '&airline=' + airline + '&flight_time=' + flight_time + '&flight_date=' + flight_date + '&reference_num=' + reference_num;
          $.ajax
          ({
            type: "POST",
            url: "send_email.php",
            data: data_string,
            success: function()
            {
              $('#inProgress').hide();
              $("#confirmSentSuccess").fadeIn(400);
            },
            error: function()
            {
              $('#inProgress').hide();
              $("#confirmSentError").fadeIn(400);
            }
          });
          return false;
        });
      });
    </script>
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Automatic Flight Check-in Confirmation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container">
              <table class="table">
                <tr>
                  <th>First Name</th>
                  <td><?php echo $_POST['firstName']; ?></td>
                </tr>
                <tr>
                  <th>Last Name</th>
                  <td><?php echo $_POST['lastName']; ?></td>
                </tr>
                <tr>
                  <th>Confirmation #</th>
                  <td><?php echo $_POST['confirmationNum']; ?></td>
                </tr>
                <tr>
                  <th>Airline</th>
                  <td><?php echo $_POST['airline']; ?></td>
                </tr>
                <tr>
                  <th>Flight Time</th>
                  <td><?php echo $_POST['flightTime']; ?></td>
                </tr>
                <tr>
                  <th>Flight Date</th>
                  <td><?php echo $_POST['flightDate']; ?></td>
                </tr>
                <tr>
                  <th>Reference #</th>
                  <td><?php echo $_POST['referenceNum']; ?></td>
                </tr>
              </table>
              <div id="checkin-form-container" class="text-center form">
                <form autocomplete="off" name="confirmation" id="confirmation" method="post" action="">
                  <input type="hidden" name="formFunction" id="formFunction" value="confirm-checkin">
                  <div class="form-group">
                    <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Enter email address">
                  </div>
                  <button type="submit" class="btn btn-primary" name="submitEmailBtn" id="submitEmailBtn">Email this confirmation</button>
                </form>
              </div>
            </div>
            <div id="inProgress" style="text-align:center; display:none;"><h3><i class="fa fa-spinner w3-spin" style="font-size:36px"></i></h3></div>
            <div id="confirmSentSuccess" style="color:green; text-align:center; display:none;"><h3>Confirmation sent!</h3></div>
            <div id="confirmSentError" style="color:red; text-align:center; display:none;"><h3>An error occurred -- please contact
              <a href="mailto:<?php echo SUPPORT_EMAIL ?>" class="alert-link">support</a></h3></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

<?php
}
else if ((isset($_GET['flightCheckinScheduled'])) && ($_GET['flightCheckinScheduled'] == "false"))
{
?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <div class="text-center">ERROR! Something went wrong. Your flight was <u>not</u> scheduled.<br>
        Please contact <a href="mailto:<?php echo SUPPORT_EMAIL ?>" class="alert-link">support</a> or try again.</div>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
<?php
}
?>
    <div class="container">
      <div class="w3-bar w3-border-top w3-border-bottom w3-border-left w3-border-right w3-light-grey">
        <button class="w3-bar-item w3-button tablink w3-dark-grey" onclick="openForm(event,'checkin-form-container')">Check in to flight</button>
      </div>

      <div id="checkin-form-container" class="form">
        <br>
        <form class="needs-validation" novalidate autocomplete="off" name="checkin" id="checkin" method="POST" action="schedule.php">
          <input type="hidden" name="formFunction" id="formFunction" value="checkin">
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
          <button type="submit" class="btn btn-primary" name="submitBtn" id="submitBtn">Check in to flight</button>
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

