<?php
session_start();
require_once '../credentials.php';

// Check if administrator is logged in
if ($_SESSION['role'] !== 'administrator')
{
	header("Location: ../auth/login.php");
	exit;
}

// Function to sanitize user input
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to encrypt the password using password_hash()
function encryptPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $name = sanitizeInput($_POST['name']);
    $location = sanitizeInput($_POST['location']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $emailAddress = sanitizeInput($_POST['emailAddress']);
    $operator = sanitizeInput($_POST['operator']);
    $password = sanitizeInput($_POST['password']);

    // Encrypt the password
    $passwordHash = encryptPassword($password);

    // Create a new PDO connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO pharmaceutical (name, location, phoneNumber, emailAddress, operator, passwordHash) VALUES (?, ?, ?, ?, ?, ?)");

        // Bind the parameters with values
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $location);
        $stmt->bindParam(3, $phoneNumber);
        $stmt->bindParam(4, $emailAddress);
        $stmt->bindParam(5, $operator);
        $stmt->bindParam(6, $passwordHash);

        // Execute the statement
        $stmt->execute();

        // Display success message
        echo "Pharmaceutical registered successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html>

<head>
    <title>Pharmaceutical Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/form.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <h2 class="text-center">Pharmaceutical Registration</h2>
    <div class="custom-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="operator">Operator:</label>
                <input type="text" name="operator" id="operator" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <input type="submit" value="Register" class="btn btn-primary">
        </form>
    </div>
<?php echo $footer; ?>
</body>

</html>
