<?php
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
</head>

<body>
    <h2>Patient Registration</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	<label for="firstName">First Name:</label>
	<input type="text" name="firstName" required><br><br>

	<label for="lastName">Last Name:</label>
	<input type="text" name="lastName" required><br><br>

	<label for="gender">Gender:</label>
	<select name="gender" required>
	    <option value="">Select Gender</option>
	    <option value="Male">Male</option>
	    <option value="Female">Female</option>
	    <option value="Other">Other</option>
	</select><br><br>

	<label for="location">Location:</label>
	<input type="text" name="location" required><br><br>

	<label for="emailAddress">Email Address:</label>
	<input type="email" name="emailAddress" required><br><br>

	<label for="phoneNumber">Phone Number:</label>
	<input type="tel" name="phoneNumber" required><br><br>

	<label for="SSN">Social Security Number:</label>
	<input type="text" name="SSN" required><br><br>

	<label for="dateOfBirth">Date of Birth:</label>
	<input type="date" name="dateOfBirth" required><br><br>

	<label for="password">Password:</label>
	<input type="password" name="password" required><br><br>

	<input type="submit" value="Register">
    </form>
</body>

</html>
