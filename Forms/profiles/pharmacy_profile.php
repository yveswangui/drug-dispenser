<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is pharmaceutical, pharmacy or administrator
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'pharmacy' && 
	$_SESSION['role'] !== 'pharmaceutical')) {
    header('Location: ../auth/login.php');
    exit;
}

// Create a new PDO connection to the database
try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Prepare the SQL statement to fetch pharmacy details
	$stmt = $pdo->prepare("SELECT * FROM pharmacy WHERE pharmacyId = ?");
	$stmt->execute([$_GET['pharmacyId']]);
	$pharmacy = $stmt->fetch(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch the latest contracts assigned to the pharmacy
	$stmt = $pdo->prepare("SELECT contract.*, pharmaceutical.name AS pharmaceuticalName FROM contract
		INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
		WHERE contract.pharmacyId = ?
		ORDER BY contract.contractId DESC
		LIMIT 10");
	$stmt->execute([$_GET['pharmacyId']]);
	$latestContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
<title>Pharmacy Profile | <?php echo $_SESSION['name'] ?></title>
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
    <h1>Welcome to <?php echo $_SESSION['name']; ?>!</h1>
    <h3>Pharmacy Details</h3>
    <table>
	<tr>
	    <th>Name</th>
	    <th>Location</th>
	    <th>Operator</th>
	    <th>Email Address</th>
	    <th>Phone Number</th>
	</tr>
	<tr>
	    <td><?php echo $pharmacy['name']; ?></td>
	    <td><?php echo $pharmacy['location']; ?></td>
	    <td><?php echo $pharmacy['operator']; ?></td>
	    <td><?php echo $pharmacy['emailAddress']; ?></td>
	    <td><?php echo $pharmacy['phoneNumber']; ?></td>
	</tr>
    </table>

    <h3>Latest Contracts</h3>
    <table>
	<tr>
	    <th>Pharmaceutical</th>
	    <th>Start Date</th>
	    <th>End Date</th>
	</tr>
	<?php foreach ($latestContracts as $contract) : ?>
	    <tr>
		<td>
		    <a href="contract_profile.php?contractId=<?php echo $contract['contractId']; ?>">
			<?php echo $contract['pharmaceuticalName']; ?>
		    </a>
		</td>
		<td><?php echo $contract['startDate']; ?></td>
		<td><?php echo $contract['endDate']; ?></td>
	    </tr>
	<?php endforeach; ?>
    </table>

    <p><a href="assigned_contracts.php?pharmacyId=<?php echo $_GET['pharmacyId']; ?>">View All Assigned Contracts</a></p>

</body>

</html>
