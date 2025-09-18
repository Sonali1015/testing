<?php
session_start();
include("../config/db.php");

if (isset($_POST['search'])) {
    $location = strtolower(trim($_POST['location']));

    $sql = "SELECT * FROM hotels WHERE LOWER(location) LIKE ?";
    $param = "%" . $location . "%";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<div>
                <h3>{$row['name']}</h3>
                <p>Location: {$row['location']}</p>
                <p>Price: {$row['price']}</p>
                <img src='../assets/images/{$row['image']}' width='200'>
              </div>";
    }
}
?>

<form method="POST">
    <input type="text" name="location" placeholder="Enter location">
    <button type="submit" name="search">Search</button>
</form>