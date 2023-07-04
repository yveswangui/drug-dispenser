<?php
session_start();
// Check whether logged in user is administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pharmacies | <?php echo $_SESSION['name']; ?></title>
</head>
<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h1>Registered Pharmacies</h1>

    <?php
    // Include database credentials
    require_once('../credentials.php');

    // Establish database connection
    $conn = new mysqli($host, $username, $dbPassword, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve pharmacy records
    $sql = "SELECT * FROM pharmacy";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display pharmacy records
        while ($row = $result->fetch_assoc()) {
            $pharmacyId = $row['pharmacyId'];
            $name = $row['name'];
            $location = $row['location'];
            $phoneNumber = $row['phoneNumber'];
            $emailAddress = $row['emailAddress'];
            $operator = $row['operator'];

            echo "<h3>$name</h3>";
            echo "<p>Location: <a href='https://www.google.com/maps?q=$location' target='_blank'>$location</a></p>";
            echo "<p>Phone: <a href='tel:$phoneNumber'>$phoneNumber</a></p>";
            echo "<p>Email: <a href='mailto:$emailAddress'>$emailAddress</a></p>";
            echo "<p>Operator: $operator</p>";
            echo "<p><a href='pharmacy_profile.php?pharmacyId=$pharmacyId'>View Profile</a></p>";
            echo "<hr>";
        }
    } else {
        echo "No pharmacies found.";
    }

    // Close database connection
    $conn->close();
    ?>

</body>
</html>
