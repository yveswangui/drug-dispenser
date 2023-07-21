<?php
session_start();
require_once '../credentials.php';

// Check if administrator is logged in
if ($_SESSION['role'] !== 'administrator') {
    header("Location: ../auth/login.php");
    exit;
}

// Check if pharmaceuticalId is provided in the URL
if (!isset($_GET['pharmaceuticalId'])) {
    header("Location: pharmaceutical_profile.php"); // Redirect to the pharmaceutical profile page or display an error message
    exit;
}

// Get the pharmaceuticalId from the URL
$pharmaceuticalId = $_GET['pharmaceuticalId'];

// Function to sanitize user input
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $name = sanitizeInput($_POST['name']);
    $location = sanitizeInput($_POST['location']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $emailAddress = sanitizeInput($_POST['emailAddress']);
    $operator = sanitizeInput($_POST['operator']);

    // Create a new PDO connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("UPDATE pharmaceutical SET name = ?, location = ?, phoneNumber = ?, emailAddress = ?, operator = ? WHERE pharmaceuticalId = ?");

        // Bind the parameters with values
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $location);
        $stmt->bindParam(3, $phoneNumber);
        $stmt->bindParam(4, $emailAddress);
        $stmt->bindParam(5, $operator);
        $stmt->bindParam(6, $pharmaceuticalId);

        // Execute the statement
        $stmt->execute();

        // Display success message
        echo "Pharmaceutical profile updated successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the pharmaceutical data from the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM pharmaceutical WHERE pharmaceuticalId = ?");

    // Bind the pharmaceuticalId parameter with the value
    $stmt->bindParam(1, $pharmaceuticalId);

    // Execute the statement
    $stmt->execute();

    // Fetch the pharmaceutical data
    $pharmaceuticalData = $stmt->fetch(PDO::FETCH_ASSOC);

    // Close the database connection
    $pdo = null;
} catch (PDOException $e) {
    // Display error message
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Pharmaceutical Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <a href="../auth/logout.php">Log Out</a>
    <h2 class="text-center">Edit Pharmaceutical Profile</h2>
    <div class="container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmaceuticalId=' . $pharmaceuticalId); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $pharmaceuticalData['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo $pharmaceuticalData['location']; ?>" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" value="<?php echo $pharmaceuticalData['phoneNumber']; ?>" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control" value="<?php echo $pharmaceuticalData['emailAddress']; ?>" required>
            </div>

            <div class="form-group">
                <label for="operator">Operator:</label>
                <input type="text" name="operator" id="operator" class="form-control" value="<?php echo $pharmaceuticalData['operator']; ?>" required>
            </div>

            <input type="submit" value="Update" class="btn btn-primary">
        </form>
    </div>
</body>

</html>
