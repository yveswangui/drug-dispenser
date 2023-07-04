<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is pharmacy or administrator or pharmaceutical
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'pharmaceutical' 
	&& $_SESSION['role'] !== 'pharmacy')) {
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

	$contracts = [];

	if (isset($_GET['pharmacyId'])) {
		// Query contracts assigned to the pharmacy
		$stmt = $pdo->prepare("SELECT contract.*, pharmaceutical.name AS pharmaceuticalName FROM contract
			INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
			WHERE contract.pharmacyId = ?
			ORDER BY contract.contractId DESC");
		$stmt->execute([$_GET['pharmacyId']]);
		$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} elseif (isset($_GET['pharmaceuticalId'])) {
		// Query contracts assigned to the pharmaceutical
		$stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacyName FROM contract
			INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
			WHERE contract.pharmaceuticalId = ?
			ORDER BY contract.contractId DESC");
		$stmt->execute([$_GET['pharmaceuticalId']]);
		$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
} catch (PDOException $e) {
	die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Assigned Contracts | <?php echo $_SESSION['name']; ?></title>
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
    <h2>Assigned Contracts</h2>
    <table>
	<tr>
	    <th>Title</th>
	    <th>Start Date</th>
	    <th>End Date</th>
	    <th>Pharmacy/Pharmaceutical</th>
	</tr>
	<?php foreach ($contracts as $contract) : ?>
	    <tr>
		<td>
		    <a href="contract_profile.php?contractId=<?php echo $contract['contractId']; ?>">
			<?php echo $contract['title']; ?>
		    </a>
		</td>
		<td><?php echo $contract['startDate']; ?></td>
		<td><?php echo $contract['endDate']; ?></td>
		<td>
<?php
if (isset($_GET['pharmacyId'])) {
	echo $contract['pharmaceuticalName'];
} elseif (isset($_GET['pharmaceuticalId'])) {
	echo $contract['pharmacyName'];
}
?>
		</td>
	    </tr>
	<?php endforeach; ?>
    </table>

    <p>
<?php
if (isset($_GET['pharmacyId'])) {
	echo '<a href="pharmacy_profile.php?pharmacyId=' . $_GET['pharmacyId'] . '">Back to Pharmacy Profile</a>';
} elseif (isset($_GET['pharmaceuticalId'])) {
	echo '<a href="pharmaceutical_profile.php?pharmaceuticalId=' . $_GET['pharmaceuticalId'] . '">Back to Pharmaceutical Profile</a>';
}
?>
    </p>
</body>

</html>
