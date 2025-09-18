<?php
include("../config/db.php");

if (isset($_POST['cancel'])) {
    $id = $_POST['booking_id'];

    // Check if booking exists
    $stmt = $conn->prepare("SELECT status FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        if ($booking['status'] !== 'Cancelled') {
            // Update booking status
            $updateStmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
            $updateStmt->bind_param("i", $id);

            if ($updateStmt->execute()) {
                echo "<div style='color: green; font-weight: bold;'>Booking cancelled successfully! Redirecting...</div>";
                echo "<script>setTimeout(() => { window.location.href = 'myBookings.php'; }, 2000);</script>";
            } else {
                echo "<div style='color: red;'>Error updating booking: " . $conn->error . "</div>";
            }
        } else {
            echo "<div style='color: orange;'>This booking is already cancelled.</div>";
        }
    } else {
        echo "<div style='color: red;'>Booking not found.</div>";
    }

    $stmt->close();
    $conn->close();
}
