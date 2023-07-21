<?php
session_start();
require_once '../credentials.php';

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
	$firstName = sanitizeInput($_POST['firstName']);
	$lastName = sanitizeInput($_POST['lastName']);
	$gender = sanitizeInput($_POST['gender']);
	$location = sanitizeInput($_POST['location']);
	$emailAddress = sanitizeInput($_POST['emailAddress']);
	$phoneNumber = sanitizeInput($_POST['phoneNumber']);
	$SSN = sanitizeInput($_POST['SSN']);
	$dateOfBirth = sanitizeInput($_POST['dateOfBirth']);
	$password = sanitizeInput($_POST['password']);

	// Encrypt the password
	$passwordHash = encryptPassword($password);

	// Create a new PDO connection to the database
	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Prepare the SQL statement
		$stmt = $pdo->prepare("INSERT INTO patient (firstName, lastName, gender, location, emailAddress, phoneNumber, SSN, dateOfBirth, passwordHash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

		// Bind the parameters with values
		$stmt->bindParam(1, $firstName);
		$stmt->bindParam(2, $lastName);
		$stmt->bindParam(3, $gender);
		$stmt->bindParam(4, $location);
		$stmt->bindParam(5, $emailAddress);
		$stmt->bindParam(6, $phoneNumber);
		$stmt->bindParam(7, $SSN);
		$stmt->bindParam(8, $dateOfBirth);
		$stmt->bindParam(9, $passwordHash);

		// Execute the statement
		$stmt->execute();

		// Display success message
		echo "Patient registered successfully.";

		// Close the database connection
		$pdo = null;
	} catch (PDOException $e) {
		// Display error message
		echo "Error: " . $e->getMessage();
	}
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Patient Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/form.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <h2 class="text-center">Patient Registration</h2>
    <div class="custom-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="SSN">Social Security Number:</label>
                <input type="text" name="SSN" id="SSN" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="dateOfBirth">Date of Birth:</label>
                <input type="date" name="dateOfBirth" id="dateOfBirth" class="form-control" required>
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

