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
    <title>Pharmaceuticals | <?php echo $_SESSION['name']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/records.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        h1 {
            margin-bottom: 20px;
        }

        .card {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .card-text {
            margin-bottom: 5px;
        }

        .btn {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h1>Registered Pharmaceuticals</h1>

    <?php
    // Include database credentials
    require_once('../credentials.php');

    // Establish database connection
    $conn = new mysqli($host, $username, $dbPassword, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve pharmaceutical records
    $sql = "SELECT * FROM pharmaceutical";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display pharmaceutical records
        while ($row = $result->fetch_assoc()) {
            $pharmaceuticalId = $row['pharmaceuticalId'];
            $name = $row['name'];
            $location = $row['location'];
            $phoneNumber = $row['phoneNumber'];
            $emailAddress = $row['emailAddress'];
            $operator = $row['operator'];

            echo "<div class='card'>";
            echo "<div class='card-body'>";
            echo "<h3 class='card-title'>$name</h3>";
            echo "<p class='card-text'><i class='fas fa-map-marker-alt'></i> Location: <a href='https://www.google.com/maps?q=$location' target='_blank'>$location</a></p>";
            echo "<p class='card-text'><i class='fas fa-phone'></i> Phone: <a href='tel:$phoneNumber'>$phoneNumber</a></p>";
            echo "<p class='card-text'><i class='fas fa-envelope'></i> Email: <a href='mailto:$emailAddress'>$emailAddress</a></p>";
            echo "<p class='card-text'><i class='fas fa-user'></i> Operator: $operator</p>";
            echo "<p><a href='pharmaceutical_profile.php?pharmaceuticalId=$pharmaceuticalId' class='btn btn-primary'><i class='fas fa-eye'></i> View Profile</a></p>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No pharmaceuticals found.</p>";
    }

    // Close database connection
    $conn->close();
    ?>

</div>
<?php echo $footer; ?>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
