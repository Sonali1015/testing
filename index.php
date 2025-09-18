<?php
ob_start();
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link rel="apple-touch-icon" sizes="180x180" href="image/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/favicon/favicon-16x16.png">
    <link rel="manifest" href="image/favicon/site.webmanifest">
    <link rel="mask-icon" href="image/favicon/safari-pinned-tab.svg" color="#5bbad5">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.2.5/css/select.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css">

    <?php

    require 'lib/phpPasswordHashing/passwordLib.php';
    require 'app/DB.php';
    require 'app/Util.php';
    require 'app/dao/CustomerDAO.php';
    require 'app/dao/BookingDetailDAO.php';
    require 'app/models/RequirementEnum.php';
    require 'app/models/Customer.php';
    require 'app/models/Booking.php';
    require 'app/models/Reservation.php';
    require 'app/handlers/CustomerHandler.php';
    require 'app/handlers/BookingDetailHandler.php';

    $username = $cHandler = $bdHandler = $cBookings = null;
    $isSessionExists = false;
    $isAdmin = [];
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];

        $cHandler = new CustomerHandler();
        $cHandler = $cHandler->getCustomerObj($_SESSION["accountEmail"]);
        $cAdmin = new Customer();
        $cAdmin->setEmail($cHandler->getEmail());

        $bdHandler = new BookingDetailHandler();
        $cBookings = $bdHandler->getCustomerBookings($cHandler);
        $isSessionExists = true;
        $isAdmin = $_SESSION["authenticated"];
    }
    if (isset($_SESSION["isAdmin"]) && isset($_SESSION["username"])) {
        $isSessionExists = true;
        $username = $_SESSION["username"];
        $isAdmin = $_SESSION["isAdmin"];
    }

    // if (isset($_COOKIE['is_admin'])) {
    //     echo $_COOKIE['is_admin'];
    //     var_dump($isAdmin);
    // }

    ?>
    <title>hotel</title>
    <?php //echo '<title>Home isAdmin=' . $isAdmin . ' $isSessionExists=' . $isSessionExists . '</title>'?>
</head>
<body>

<header>
    <div class="bg-dark collapse" id="navbarHeader" style="">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-md-7 py-4">
                    <h4 class="text-white">About</h4>
                    <p class="text-muted">Add some information about hotel booking.</p>
                </div>
                <div class="col-sm-4 offset-md-1 py-4 text-right">
                    <?php if ($isSessionExists) { ?>
                    <h4 class="text-white"><?php echo $username; ?></h4>
                    <ul class="list-unstyled">
                        <?php if ($isAdmin[1] == "true" && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "true") { ?>
                        <li><a href="admin.php" class="text-white">Manage customer reservation(s)<i class="far fa-address-book ml-2"></i></a></li>
                        <?php } else { ?>
                        <li><a href="#" class="text-white my-reservations">View my bookings<i class="far fa-address-book ml-2"></i></a></li>
                        <li>
                            <a href="#" class="text-white" data-toggle="modal" data-target="#myProfileModal">Update profile<i class="fas fa-user ml-2"></i></a>
                        </li>
                        <?php } ?>
                        <li><a href="#" id="sign-out-link" class="text-white">Sign out<i class="fas fa-sign-out-alt ml-2"></i></a></li>
                    </ul>
                    <?php } else { ?>
                   <h4>
            <a class="text-white" href="sign-in.php">Sign in</a> 
            <span class="text-white">or</span>
            <a href="register.php" class="text-white">Register</a>
        </h4>
                    <!-- <p class="text-muted">Log in so you can take advantage with our hotel room prices.</p> -->
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <!-- <i class="fas fa-h-square mr-2"></i> -->
                <strong>LuxStay</strong>
            </a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>
    <div class="container my-5" id="my-reservations-div">
    <h4 class="reservations-title mb-4">Reservations</h4>
    <div class="row">
        <?php if (!empty($cBookings) && $bdHandler->getExecutionFeedback() == 1) { ?>
            <?php foreach ($cBookings as $k => $v) { ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <span class="reservation-number">#<?php echo ($k + 1); ?></span>
                            <span class="reservation-status <?php echo strtolower($v["status"]); ?>">
                                <?php echo $v["status"]; ?>
                            </span>
                        </div>
                        <div class="reservation-body">
                            <p><strong>Room type:</strong> <?php echo $v["type"]; ?></p>
                            <p><strong>Start:</strong> <?php echo $v["start"]; ?> <strong>End:</strong> <?php echo $v["end"]; ?></p>
                            <p><strong>Requirements:</strong> <?php echo $v["requirement"]; ?></p>
                            <p><strong>Adults:</strong> <?php echo $v["adults"]; ?> <strong>Children:</strong> <?php echo $v["children"]; ?></p>
                            <p><strong>Requests:</strong> <?php echo $v["requests"]; ?></p>
                            <p class="reservation-timestamp"><?php echo $v["timestamp"]; ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="col-12">
                <p class="text-muted">No reservations found.</p>
            </div>
        <?php } ?>
    </div>
</div>

</div>

</header>

<main role="main">

   <section class="jumbotron jumbotron-custom d-flex align-items-center">
    <div class="container text-left pl-5">
        <h1 class="display-2 font-weight-bold text-white">Find Your Perfect Stay</h1>
       <p class="lead text-white mb-5">Discover amazing hotels and book your next vacation with ease.</p>

        <?php if ($isSessionExists) { ?>
        <a href="#" class="btn btn-success btn-lg" data-toggle="modal" data-target=".book-now-modal-lg">
            Book now <i class="fas fa-angle-double-right ml-1"></i>
        </a>
        <?php } else { ?>
        <a href="#" class="btn btn-success btn-lg bg-dark" style="background-color: #343a40;" data-toggle="modal" data-target=".sign-in-to-book-modal">
            Book now <i class="fas fa-angle-double-right ml-1"></i>
        </a>
        <?php } ?>
    </div>
</section>



    <div class="container">
        <div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
            <h1 class="display-4">Why Choose Us</h1>
            <p class="lead">Quickly build an effective pricing table for your potential customers with this Bootstrap example. It's built with default Bootstrap components and utilities with little customization.</p>
            <a href="#" class="btn btn-success btn-lg bg-dark" style="background-color: #343a40;" >
            Read More 
        </a>
        </div>
    </div>

    <div class="album py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Deluxe Room -->
            <div class="col-md-4">
                <div class="card room-card mb-4">
                    <img class="card-img-top" src="image/deluxe.jpg" alt="Deluxe Room">
                    <div class="card-body">
                        <h5 class="room-title">Deluxe Room</h5>
                        <p class="room-desc">The ultimate sanctuary to recharge the senses, the beautifully-appointed 24sqm Deluxe Room exudes sheer sophistication and elegance. .</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="btn-group">
                                <?php if ($isSessionExists) { ?>
                                <button type="button" class="btn btn-primary btn-sm" data-rtype="Deluxe" data-toggle="modal" data-target=".book-now-modal-lg">Book</button>
                                <?php } else { ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target=".sign-in-to-book-modal">Book</button>
                                <?php } ?>
                            </div>
                            <small class="text-muted">$250 / night</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Double Room -->
            <div class="col-md-4">
                <div class="card room-card mb-4">
                    <img class="card-img-top" src="image/golden_sands.jpg" alt="Double Room">
                    <div class="card-body">
                        <h5 class="room-title">Double Room</h5>
                        <p class="room-desc">A standard twin room with two single beds, fully air-conditioned, and equipped with top-notch facilities. Perfect for couples or friends looking for comfort and security.</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <?php if ($isSessionExists) { ?>
                            <button type="button" class="btn btn-primary btn-sm" data-rtype="Double" data-toggle="modal" data-target=".book-now-modal-lg">Book</button>
                            <?php } else { ?>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target=".sign-in-to-book-modal">Book</button>
                            <?php } ?>
                            <small class="text-muted">$180 / night</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Single Room -->
            <div class="col-md-4">
                <div class="card room-card mb-4">
                    <img class="card-img-top" src="image/ocean_view.jpg" alt="Single Room">
                    <div class="card-body">
                        <h5 class="room-title">Single Room</h5>
                        <p class="room-desc">A cozy single room with en suite bathroom, free WiFi, minibar, and flat-screen TV. Ideal for solo travelers looking for comfort and convenience.</p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <?php if ($isSessionExists) { ?>
                            <button type="button" class="btn btn-primary btn-sm" data-rtype="Single" data-toggle="modal" data-target=".book-now-modal-lg">Book</button>
                            <?php } else { ?>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target=".sign-in-to-book-modal">Book</button>
                            <?php } ?>
                            <small class="text-muted">$130 / night</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


    <?php if(isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal fade book-now-modal-lg" tabindex="-1" role="dialog" aria-labelledby="bookNowModalLarge" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="reservationModalBody">
                    <?php if ($isSessionExists == 1 && $isAdmin[1] == "false") { ?>
                        <form role="form" autocomplete="off" method="post" id="multiStepRsvnForm">
                            <div class="rsvnTab">
                                <?php if ($isSessionExists) { ?>
                                    <input type="number" name="cid" value="<?php echo $cHandler->getId() ?>" hidden>
                                <?php } ?>
                                <div class="form-group row">
                                    <label for="startDate" class="col-sm-3 col-form-label">Check-in
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"
                                                   name="startDate"  min="<?php echo Util::dateToday('0'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="endDate" class="col-sm-3 col-form-label">Check-out
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                            <input type="date" class="form-control"  min="<?php echo Util::dateToday('1'); ?>" name="endDate" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomType">Room type
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="roomType">
                                            <option value="<?php echo \models\RequirementEnum::DELUXE; ?>">Deluxe room</option>
                                            <option value="<?php echo \models\RequirementEnum::DOUBLE; ?>">Double room</option>
                                            <option value="<?php echo \models\RequirementEnum::SINGLE; ?>">Single room</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="roomRequirement">Room requirements</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="roomRequirement">
                                            <option value="no preference" selected>No preference</option>
                                            <option value="non smoking">Non smoking</option>
                                            <option value="smoking">Smoking</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="adults">Adults
                                        <span class="red-asterisk"> *</span>
                                    </label>
                                    <div class="col-sm-9">
                                        <select required class="custom-select mr-sm-2"  name="adults">
                                            <option selected value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="children">Children</label>
                                    <div class="col-sm-9">
                                        <select class="custom-select mr-sm-2"  name="children">
                                            <option selected value="0">-</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label" for="specialRequests">Special requirements</label>
                                    <div class="col-sm-9">
                                        <textarea rows="3" maxlength="500"  name="specialRequests" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <button type="button" class="btn btn-info" style="margin-left: 0.8em;" data-container="body" data-toggle="popover"
                                            data-placement="right" data-content="Check-in time starts at 3 PM. If a late check-in is planned, please contact our support department.">
                                        Check-in policies
                                    </button>
                                </div>
                            </div>

                            <div class="rsvnTab">
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="bookedDate">Booked Date</label>
                                    <div class="col-sm-9 bookedDateTxt">
                                        July 13, 2019
                                    </div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="roomPrice">Room Price</label>
                                    <div class="col-sm-9 roomPriceTxt">235.75</div>
                                </div>
                                <div class="form-group row align-items-center">
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights"><span class="numNightsTxt">3</span> nights </label>
                                    <div class="col-sm-9">
                                        $<span class="roomPricePerNightTxt">69.63</span> avg. / night
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold" for="numNights">From - to</label>
                                    <div class="col-sm-9 fromToTxt">
                                        Mon. July 4 to Wed. July 6
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Taxes </label>
                                    <div class="col-sm-9">
                                        $<span class="taxesTxt">0</span>
                                    </div>
                                    <label class="col-sm-3 col-form-label font-weight-bold">Total </label>
                                    <div class="col-sm-9">
                                        $<span class="totalTxt">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <div style="text-align:center;margin-top:40px;">
                                <span class="step"></span>
                                <span class="step"></span>
                            </div>

                        </form>
                        <div style="overflow:auto;">
                            <div style="float:right;">
                                <button type="button" class="btn btn-success" id="rsvnPrevBtn" onclick="rsvnNextPrev(-1)">Previous</button>
                                <button type="button" class="btn btn-success" id="rsvnNextBtn" onclick="rsvnNextPrev(1)" readySubmit="false">Next</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <p>Booking is reserved for customers.</p>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal sign-in-to-book-modal" tabindex="-1" role="dialog" aria-labelledby="signInToBookModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="exampleModalLongTitle">Sign in required</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-white">You have to <a href="sign-in.php" class="text-primary">sign in</a> in order to reserve a room.</h5>
            </div>
        </div>
    </div>
</div>

    </div>

    <?php if(($isSessionExists == 1 && $isAdmin[1] == "false") && isset($_COOKIE['is_admin']) && $_COOKIE['is_admin'] == "false") : ?>
    <div class="modal" id="myProfileModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card border-0">
                        <div class="card-body p-0">
                            <?php if ($isSessionExists) { ?>
                            <form class="form" role="form" autocomplete="off" id="update-profile-form" method="post">
                                <input type="number" id="customerId" hidden
                                       name="customerId" value="<?php echo $cHandler->getId(); ?>" >
                                <div class="form-group">
                                    <label for="updateFullName">Full Name</label>
                                    <input type="text" class="form-control" id="updateFullName"
                                           name="updateFullName" value="<?php echo $cHandler->getFullName(); ?>" >
                                </div>
                                <div class="form-group">
                                    <label for="updatePhoneNumber">Phone Number</label>
                                    <input type="text" class="form-control" id="updatePhoneNumber"
                                           name="updatePhoneNumber" value="<?php echo $cHandler->getPhone(); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="updateEmail">Email</label>
                                    <input type="email" class="form-control" id="updateEmail"
                                           name="updateEmail" value="<?php echo $cHandler->getEmail(); ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="updatePassword">New Password</label>
                                    <input type="password" class="form-control" id="updatePassword"
                                           name="updatePassword"
                                           title="At least 4 characters with letters and numbers">
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary btn-md float-right"
                                           name="updateProfileSubmitBtn" value="Update">
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<footer class="footer bg-dark text-center text-white py-3">
    <p>&copy; 2025 LuxStay. All rights reserved.</p>
</footer>

<script src="js/utilityFunctions.js"></script>

<script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"
        integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+"
        crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.5/js/dataTables.select.min.js"></script>
<script src="js/animatejscx.js"></script>
<script src="js/form-submission.js"></script>
<script>
    $(document).ready(function () {
      let reservationDiv = $("#my-reservations-div");
      reservationDiv.hide();
      $(".my-reservations").click(function () {
        reservationDiv.slideToggle("slow");
      });
      $('#myReservationsTbl').DataTable();

      // dynamically entered room type value on show modal
      $('.book-now-modal-lg').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let roomType = button.data('rtype');
        let modal = $(this);
        modal.find('.modal-body select[name=roomType]').val(roomType);
      });

      // check-in policies popover
      $('[data-toggle="popover"]').popover();

    });
</script>
<script src="js/multiStepsRsvn.js"></script>
</body>
</html>