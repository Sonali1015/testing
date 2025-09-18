<?php
session_start();
include("../config/db.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='../index.php'>login</a> first.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare and execute query securely
$stmt = $conn->prepare("
    SELECT b.id, h.name, b.check_in, b.check_out, b.status 
    FROM bookings b 
    JOIN hotels h ON b.hotel_id = h.id 
    WHERE b.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .booking {
            padding: 15px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .booking h3 {
            margin: 0 0 10px 0;
        }

        .booking button {
            padding: 5px 10px;
            background-color: #f44336;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }

        .booking button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>

<body>
    <h2>My Bookings</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>You have no bookings yet.</p>
    <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="booking">
                <h3><?= htmlspecialchars($row['name']) ?> | <?= htmlspecialchars($row['status']) ?></h3>
                <p>Check-in: <?= htmlspecialchars($row['check_in']) ?> | Check-out: <?= htmlspecialchars($row['check_out']) ?></p>
                <?php if ($row['status'] === 'Booked'): ?>
                    <form method="POST" action="cancel.php">
                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="cancel">Cancel</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</body>

</html>