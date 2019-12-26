<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- REDIRECT TO NON-BETA VERSION -->
    <meta http-equiv = "refresh" content = "0; url = ../checkin.php" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>Flight Check-in</title>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script>
      (function()
      {
        'use strict';
        window.addEventListener('load', function()
        {
          // Fetch all the forms we want to apply custom Bootstrap validation styles to
          var forms = document.getElementsByClassName('needs-validation');
          // Loop over them and prevent submission
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
        // START -- If checkin was successful, display a modal (see <script> block and form below)
        if ((isset($_POST['confirm-checkin'])) && ($_POST['confirm-checkin'] == "true"))
        {
    ?>
    <script>
      $(document).ready(function()
      {
        $('#confirmationModal').modal('show');
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
          var data_string = 'email=' + email + '&first_name=' + first_name + '&last_name=' + last_name + '&confirmation_num=' + confirmation_num;
          data_string += '&airline=' + airline + '&flight_time=' + flight_time + '&flight_date=' + flight_date + '&reference_num=' + reference_num;
          data_string += '&subject=Your flight check-in&emailBodyType=flightConfirmation';
          $.ajax(
          {
            type: "POST",
            url: "send_email_beta.php",
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
                <form autocomplete="off" id="confirmation" method="post" action="">
                  <input type="hidden" id="form-function" name="form-function" value="confirm-checkin">
                  <div class="form-group">
                    <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Enter email address">
                  </div>
                  <button type="submit" class="btn btn-primary" id="submitEmailBtn">Email this confirmation</button>
                </form>
              </div>
            </div>
            <div id="inProgress" style="text-align:center; display:none;"><h3><i class="fa fa-spinner w3-spin" style="font-size:36px"></i></h3></div>
            <div id="confirmSentSuccess" style="color:red; text-align:center; display:none;"><h3>Confirmation sent!</h3></div>
            <div id="confirmSentError" style="color:red; text-align:center; display:none;"><h3>An error occurred -- please email jdstone@jdstone1.com for support.</h3></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <?php
        // END -- If checkin was successful, display a modal (see <script> block and form below)
        }
        else if ((isset($_POST['confirm-checkin'])) && ($_POST['confirm-checkin'] == "true"))
        {

        }
        else if ((isset($_GET['confirm'])) && ($_GET['confirm'] == "modify"))
        {
            echo "<script>\n";
            echo "      $(document).ready(function(){\n";
            echo "        $('#scheduled').hide();\n";
            echo "        $('#form-container').show();\n";
            echo "      });\n";
            echo "    </script>\n";
            echo "<h1>Something went wrong with scheduling your flight.</h1n";
        }
        else if ((isset($_GET['confirm'])) && ($_GET['confirm'] == "delete"))
        {
            echo "TEST";
        }
        if ((isset($_GET['scheduled'])) && ($_GET['scheduled'] == "true"))
        {
            echo "<script>\n";
            echo "      $(document).ready(function(){\n";
            echo "        $('#scheduled').show();\n";
            echo "        $('#form-container').hide();\n";
            echo "      });\n";
            echo "    </script>\n";
            echo "<h1>Your flight has been scheduled!</h1>\n";
        }
        else if ((isset($_GET['scheduled'])) && ($_GET['scheduled'] == "false"))
        {
            echo "<script>\n";
            echo "      $(document).ready(function(){\n";
            echo "        $('#scheduled').hide();\n";
            echo "        $('#form-container').show();\n";
            echo "      });\n";
            echo "    </script>\n";
            echo "<h1>Something went wrong with scheduling your flight.</h1n";
        }
    ?>
    <div class="container">
      <div class="w3-bar w3-border-top w3-border-bottom w3-border-left w3-border-right w3-light-grey">
        <button class="w3-bar-item w3-button tablink w3-dark-grey" onclick="openForm(event,'checkin-form-container')">Check-in to flight</button>
        <!-- <button class="w3-bar-item w3-button tablink" onclick="openForm(event,'modify-form-container')">Edit flight</button> -->
        <!-- <button class="w3-bar-item w3-button tablink" onclick="openForm(event,'delete-form-container')">Delete flight</button> -->
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
            <label for="inputConfirmationNum">Confirmation #</label>
            <input maxlength="6" type="text" class="form-control" name="inputConfirmationNum" id="inputConfirmationNum" placeholder="Enter confirmation number">
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
          <button type="submit" class="btn btn-primary" id="submitBtn">Check-in to flight</button>
        </form>
      </div>

      <div id="modify-form-container" class="form" style="display:none">
        <br>
        <form class="needs-validation" novalidate autocomplete="off" id="modify" method="POST" action="schedule.php">
          <input type="hidden" id="form-function" name="form-function" value="modify">
          <div class="form-group">
            <label for="inputRefNum">Reference Number</label>
            <input type="text" class="form-control" name="inputRefNum" id="inputRefNum" placeholder="Enter reference #">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <button type="submit" class="btn btn-primary" id="submitBtn">View & Edit flight</button>
        </form>
      </div>

      <!-- <div id="delete-form-container" class="form" style="display:none">
        <br>
        <form class="needs-validation" novalidate autocomplete="off" id="delete" method="POST" action="schedule.php">
          <input type="hidden" id="form-function" name="form-function" value="delete">
          <div class="form-group">
            <label for="inputRefNum">Reference Number</label>
            <input type="text" class="form-control" name="inputRefNum" id="inputRefNum" placeholder="Enter reference #">
            <div class="valid-feedback">
              Looks good!
            </div>
          </div>
          <button type="submit" class="btn btn-primary" id="submitBtn">View & Delete flight</button>
        </form>
      </div> -->
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