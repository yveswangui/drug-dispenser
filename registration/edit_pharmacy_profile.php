<?php
session_start();
require_once '../credentials.php';

// Check if administrator is logged in
if ($_SESSION['role'] !== 'administrator') {
    header("Location: ../auth/login.php");
    exit;
}

// Check if pharmacyId is provided in the URL
if (!isset($_GET['pharmacyId'])) {
    header("Location: pharmacy_profile.php"); // Redirect to the pharmacy profile page or display an error message
    exit;
}

// Get the pharmacyId from the URL
$pharmacyId = $_GET['pharmacyId'];

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
        $stmt = $pdo->prepare("UPDATE pharmacy SET name = ?, location = ?, phoneNumber = ?, emailAddress = ?, operator = ? WHERE pharmacyId = ?");

        // Bind the parameters with values
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $location);
        $stmt->bindParam(3, $phoneNumber);
        $stmt->bindParam(4, $emailAddress);
        $stmt->bindParam(5, $operator);
        $stmt->bindParam(6, $pharmacyId);

        // Execute the statement
        $stmt->execute();

        // Display success message
        echo "Pharmacy profile updated successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the pharmacy data from the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM pharmacy WHERE pharmacyId = ?");

    // Bind the pharmacyId parameter with the value
    $stmt->bindParam(1, $pharmacyId);

    // Execute the statement
    $stmt->execute();

    // Fetch the pharmacy data
    $pharmacyData = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Edit Pharmacy Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <h2 class="text-center">Edit Pharmacy Registration</h2>
    <div class="custom-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmacyId=' . $pharmacyId); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $pharmacyData['name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo $pharmacyData['location']; ?>" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" value="<?php echo $pharmacyData['phoneNumber']; ?>" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control" value="<?php echo $pharmacyData['emailAddress']; ?>" required>
            </div>

            <div class="form-group">
                <label for="operator">Operator:</label>
                <input type="text" name="operator" id="operator" class="form-control" value="<?php echo $pharmacyData['operator']; ?>" required>
            </div>

            <input type="submit" value="Update" class="btn btn-primary">
        </form>
    </div>
<?php echo $footer; ?>
</body>

</html>
