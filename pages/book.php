<?php
session_start();
include("../config/db.php");

if (isset($_POST['book'])) {
    $user_id = $_SESSION['user_id'];
    $hotel_id = $_POST['hotel_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    $sql = "INSERT INTO bookings (user_id, hotel_id, check_in, check_out) 
            VALUES ('$user_id', '$hotel_id', '$check_in', '$check_out')";
    if ($conn->query($sql) === TRUE) {
        echo "Booking confirmed! <a href='myBookings.php'>View Bookings</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}
