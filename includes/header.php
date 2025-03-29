<?php
session_start();

$role = $_SESSION['role'];
$name = $_SESSION['fullname'];
$branchid = $_SESSION['branch_id'];
$overalltotal = $_SESSION['overalltotal'];
?>
<!DOCTYPE html>
<html lang="en-us">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="../assets/image/neocash.ico">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/styles.css" rel="stylesheet">
  <title>Queueing System</title>
  <style>
    .navbar {
      background-color: rgb(234, 255, 0);
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .selected {
      border-bottom: 3px solid #0056b3;
      font-weight: bold;
      color: #0056b3;
      background-color: rgba(0, 123, 255, 0.1);
      padding-bottom: 2px;
    }

    .navbar-brand {
      color: #fff;
    }

    .navbar-nav .nav-link {
      color: #fff;
    }

    .strong {
      font-weight: bolder;
    }

    .white {
      color: white;
    }

    .dropdown1:hover .dropdownreport {
      display: block;
    }

    .dropdownreport {
      display: none;
      border: 1px solid #ccc;
      border-radius: 2px;
      position: absolute;
      background-color: white;
      min-width: 120px;
      z-index: 1;
    }

    .footer {
      height: 35px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg sticky-top bg-light navbar-light py-0 d-print-none">
    <a class="navbar-brand small" href="dashboard.php">
      <img src="../assets/image/Neologo.png" width="30" height="30" class="d-inline-block align-top" alt="">
      <strong style="font-family: Century Gothic;">NEOCASH</strong></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto" style="font-family: Raleway; font-size: 13.5px;">
        <li class="nav-item">
          <a class="nav-link text-dark" href="create_ticket.php"><i class="fa fa-plus text-success"
              aria-hidden="true"></i> Add Queue</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="records.php"><i class="fa fa-database text-primary" aria-hidden="true"
              selected></i> Records</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="noterecords.php"><i class="fa fa-clipboard text-success"
              aria-hidden="true"></i> Notes</a>
        </li>

        <?php if ($role == 'ADMINISTRATOR') { ?>
          <li class="nav-item">
            <a class="nav-link text-dark" href="summary.php"><i class="fa fa-bar-chart text-warning"
                aria-hidden="true"></i> Reports</a>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a class="nav-link text-dark" href="branchsummary.php"><i class="fa fa-bar-chart text-warning"
                aria-hidden="true"></i> Reports</a>
          </li>
        <?php } ?>
        <?php if (stripos($name, 'DEV') !== false) { ?>
          <li class="nav-item">
            <a class="nav-link text-dark" href="branchmanage.php"><i class="fa fa-user text-primary"
                aria-hidden="true"></i> Branch Management</a>
          </li>
        <?php } ?>
      </ul>
      <div class="d-flex align-items-center">
        <h6 class="mr-2 small"><b><?php echo date('l, F j, Y'); ?></b></h6>
        <h6 class="mr-2 small" id="time"></h6>
      </div>
      <h6 class="mr-2 small">Current User: <b><?php echo htmlspecialchars($name); ?></b></h6>
      <div class="dropdown">
        <img src="../assets/image/profile.png" style="width: 35px; height: 35px;" name="profile" class="dropdown-toggle"
          id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="dropdown-menu dropdown-menu-right profile" aria-labelledby="dropdownMenuButton"
          style="z-index: 1000;">
          <div style="display: flex; align-items: center; justify-content: center;">
            <img src="<?php if ($name == 'LJ Dev') {
              echo "../assets/image/mcdo.png";
            } else {
              echo "../assets/image/Neologo.png";
            } ?>" class="rounded-circle mt-3" alt="User Image" style="width: 70px; height: 70px;">
          </div>
          <h6 class="dropdown-item font-weight-bold text-center"><?= $name; ?></h6>
          <div id="totalrunning">
            <p class="small text-center">Total Running Collection</p>
            <p class="text-center"><strong><u><span id="profiletotal"></u></span></strong></p>
          </div>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-dark small" href="#" id="editprofile" data-toggle="modal"
            data-target="#editprofileModal"><i class="fa fa-edit" aria-hidden="true"></i> <b>Edit Profile</b></a>
          <a class="dropdown-item text-danger small" href="#" id="logoutButton"><i class="fa fa-sign-out"
              aria-hidden="true"></i> <b>Logout</b></a>
        </div>
      </div>
    </div>
    </div>
  </nav>


  <!-- <nav class="navbar fixed-bottom navbar-light bg-light footer">
  <a class="navbar-brand strong mr-auto" href="#" style="font-size: 0.7rem; font-family: Fahkwang, sans-serif;">&copy; Queueing System, All Rights Reserved 2025</a>
  <span class="text-muted" style="font-size: 0.7rem; font-family: Fahkwang, sans-serif;">Dev: LJ Siodora | Version <?php echo $_SESSION['version']; ?></span>
</nav> -->

  <!-- Modal -->
  <div class="modal fade" id="editprofileModal" tabindex="-1" role="dialog" aria-labelledby="editprofileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title text-dark" id="editprofileModalLabel"><i class="fa fa-edit" aria-hidden="true"></i>
            Edit Profile</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="../actions.php" id="editprofileform" method="post">
            <h6 class="text-dark">Username</h6>
            <input type="hidden" name="action" value="username">
            <div class="form-group">
              <label class="small" for="username">New Username</label>
              <input type="text" class="form-control form-control-sm" id="username" name="username">
            </div>
            <div class="form-group">
              <label class="small" for="confirmusername">Confirm New Username</label>
              <input type="text" class="form-control form-control-sm" id="confirmusername" name="confirmusername">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
            </div>
          </form>
          <hr>
          <form action="../actions.php" id="editpasswordform" method="post">
            <h6 class="text-dark">Password</h6>
            <input type="hidden" name="action" value="password">
            <div class="form-group">
              <label class="small" for="password">New Password</label>
              <input type="password" class="form-control form-control-sm" id="password" name="password">
            </div>
            <div class="form-group">
              <label class="small" for="confirmpassword">Confirm New Password</label>
              <input type="password" class="form-control form-control-sm" id="confirmpassword" name="confirmpassword">
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-sm btn-primary">Save changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone.min.js"></script>

  <script>

    $(document).ready(function () {
      loadoveralltotal();
      setInterval(function () { loadoveralltotal(); }, 10000);
      let idleTime = 0;
      const idleInterval = setInterval(timerIncrement, 60000);

      $(this).mousemove(resetIdleTime);
      $(this).keypress(resetIdleTime);

      function timerIncrement() {
        idleTime++;
        if (idleTime >= 10) {
          window.location.href = '../views/dashboard.php';
        }
      }

      function resetIdleTime() {
        idleTime = 0;
      }

      document.querySelectorAll('.nav-link').forEach(link => {
        if (link.href === window.location.href) {
          link.classList.add('selected');
        }
      });
    });

    $('.dropdown').on('show.bs.dropdown', function () {
      $('.profile').slideDown(200);
    });

    $('.dropdown').on('hide.bs.dropdown', function () {
      $('.profile').slideUp(200);
    });

    function loadoveralltotal() {
      $.ajax({
        url: '../load/dailyrunningcollection.php',
        type: 'GET',
        success: function (data) {
          $('#profiletotal').html("₱" + data.overalltotal);
          $('#c1running').html(data.total1);
          $('#c2running').html(data.total2);
          $('#c3running').html(data.total3);
          $('#c4running').html(data.total4);
        }
      });
    }

    $('#editprofileform').submit(function (event) {
      event.preventDefault();
      if ($('#username').val() !== $('#confirmusername').val()) {
        Swal.fire("Username does not match!", "Please enter the same username in both fields.", "error");
      } else {
        Swal.fire({
          title: "Save Changes?",
          text: "Are you sure you want to save changes to your profile?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, save it!'
        })
          .then((result) => {
            if (result.isConfirmed) {
              this.submit();
            }
          });
      }
    });

    $('#editpasswordform').submit(function (event) {
      event.preventDefault();
      if ($('#password').val() !== $('#confirmpassword').val()) {
        Swal.fire("Password does not match!", "Please enter the same password in both fields.", "error");
      } else {
        Swal.fire({
          title: "Save Changes?",
          text: "Are you sure you want to save changes to your password?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, save it!'
        })
          .then((result) => {
            if (result.isConfirmed) {
              this.submit();
            }
          });
      }
    });

    document.getElementById('logoutButton').addEventListener('click', function (event) {
      event.preventDefault();
      Swal.fire({
        title: "Are you sure?",
        text: "Once logged out, you will need to log in again.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, logout!'
      })
        .then((result) => {
          if (result.isConfirmed) {
            window.location.href = '../logout.php';
          }
        });
    });

    const timeElement = document.getElementById('time');
    const updateTimer = () => {
      timeElement.textContent = moment().format('h:mm:ss A');
      timeElement.style.fontWeight = 'bold';
      setTimeout(updateTimer, 1000);
    };
    updateTimer();


  </script>
</body>

</html>