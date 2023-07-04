<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is pharmaceutical, pharmacy or administrator
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'pharmacy' && 
	$_SESSION['role'] !== 'pharmaceutical')) {
    header('Location: ../auth/login.php');
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

// Create a new PDO connection to the database
try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Query the contract details
	$stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacyName, pharmaceutical.name AS pharmaceuticalName
		FROM contract
		INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
		INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
		WHERE contract.contractId = ?");
	$stmt->execute([$_GET['contractId']]);
	$contract = $stmt->fetch(PDO::FETCH_ASSOC);

	// Query the drugs related to the contract
	$stmt = $pdo->prepare("SELECT * FROM drug WHERE contractId = ?");
	$stmt->execute([$_GET['contractId']]);
	$drugs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Contract Details</title>
    <style>
	table {
	    border-collapse: collapse;
	    width: 100%;
	}

	th,
	td {
	    border: 1px solid black;
	    padding: 8px;
	}

	th {
	    background-color: #f2f2f2;
	}
    </style>
</head>

<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h2>Contract Details</h2>
    <table>
	<tr>
	    <th>Contract ID</th>
	    <td><?php echo $contract['contractId']; ?></td>
	</tr>
	<tr>
	    <th>Title</th>
	    <td><?php echo $contract['title']; ?></td>
	</tr>
	<tr>
	    <th>Start Date</th>
	    <td><?php echo $contract['startDate']; ?></td>
	</tr>
	<tr>
	    <th>End Date</th>
	    <td><?php echo $contract['endDate']; ?></td>
	</tr>
	<tr>
	    <th>Pharmacy</th>
	    <td>
		<a href="pharmacy_profile.php?pharmacyId=<?php echo $contract['pharmacyId']; ?>">
		    <?php echo $contract['pharmacyName']; ?>
		</a>
	    </td>
	</tr>
	<tr>
	    <th>Pharmaceutical</th>
	    <td>
		<a href="pharmaceutical_profile.php?pharmaceuticalId=<?php echo $contract['pharmaceuticalId']; ?>">
		    <?php echo $contract['pharmaceuticalName']; ?>
		</a>
	    </td>
	</tr>
    </table>

    <h3>Registered Drugs</h3>
    <table>
	<tr>
	    <th>Drug ID</th>
	    <th>Trade Name</th>
	    <th>Scientific Name</th>
	    <th>Formula</th>
	    <th>Form</th>
	    <th>Expiration Date</th>
	    <th>Manufacturing Date</th>
	</tr>
	<?php foreach ($drugs as $drug) : ?>
	    <tr>
		<td><?php echo $drug['drugId']; ?></td>
		<td><?php echo $drug['tradename']; ?></td>
		<td><?php echo $drug['scientificName']; ?></td>
		<td><?php echo $drug['formula']; ?></td>
		<td><?php echo $drug['form']; ?></td>
		<td><?php echo $drug['expirationDate']; ?></td>
		<td><?php echo $drug['manufacturingDate']; ?></td>
	    </tr>
	<?php endforeach; ?>
    </table>

    <h3>Register New Drug</h3>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?contractId=' . $_GET['contractId']); ?>" method="POST">
	<label for="tradeName">Trade Name:</label>
	<input type="text" name="tradeName" id="tradeName" required><br><br>
	<label for="scientificName">Scientific Name:</label>
	<input type="text" name="scientificName" id="scientificName" required><br><br>
	<label for="formula">Formula:</label>
	<input type="text" name="formula" id="formula" required><br><br>
	<label for="form">Form:</label>
	<input type="text" name="form" id="form" required><br><br>
	<label for="expirationDate">Expiration Date:</label>
	<input type="datetime-local" name="expirationDate" id="expirationDate" required><br><br>
	<label for="manufacturingDate">Manufacturing Date:</label>
	<input type="datetime-local" name="manufacturingDate" id="manufacturingDate" required><br><br>
	<input type="submit" value="Register Drug">
    </form>

<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$tradeName = sanitizeInput($_POST['tradeName']);
	$scientificName = sanitizeInput($_POST['scientificName']);
	$formula = sanitizeInput($_POST['formula']);
	$form = sanitizeInput($_POST['form']);
	$expirationDate = sanitizeInput($_POST['expirationDate']);
	$manufacturingDate = sanitizeInput($_POST['manufacturingDate']);

	// Insert the new drug into the database
	try {
		$stmt = $pdo->prepare("INSERT INTO drug (tradename, scientificName, formula, form, contractId, expirationDate, manufacturingDate)
			VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$tradeName, $scientificName, $formula, $form, $_GET['contractId'], $expirationDate, $manufacturingDate]);
		echo '<p>Drug registered successfully.</p>';
	} catch (PDOException $e) {
		echo 'Error registering the drug: ' . $e->getMessage();
	}
}

$pdo = null; // Close the database connection
?>
</body>

</html>
