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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
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
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h1>Welcome to <?php echo $pharmacy['name']; ?>!</h1>
    <h3>Pharmacy Details</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Operator</th>
                <th>Email Address</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $pharmacy['name']; ?></td>
                <td><?php echo $pharmacy['location']; ?></td>
                <td><?php echo $pharmacy['operator']; ?></td>
              <td><a href = "mailto:<?php echo $pharmacy['emailAddress']; ?>"><?php echo $pharmacy['emailAddress']; ?></a></td>
                <td><a href = "tel:<?php echo $pharmacy['phoneNumber']; ?>"><?php echo $pharmacy['phoneNumber']; ?></a></td>
            </tr>
        </tbody>
    </table>

    <h3>Latest Contracts</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Pharmaceutical</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latestContracts as $contract) : ?>
                <tr>
                    <td>
                        <a href="contract_profile.php?contractId=<?php echo $contract['contractId']; ?>">
                            <?php echo $contract['pharmaceuticalName']; ?>
                        </a>
                    </td>
                    <td class = "date"><?php echo $contract['startDate']; ?></td>
                    <td class = "date"><?php echo $contract['endDate']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="assigned_contracts.php?pharmacyId=<?php echo $_GET['pharmacyId']; ?>" class="btn btn-primary">View All Assigned Contracts</a></p>
</div>
<?php echo $footer; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".date").each(function() {
                var date = $(this).text();
                var formattedDate = moment(date).format('ddd D MMMM, YYYY');
                $(this).text(formattedDate);
            });
        });
    </script>
</body>

</html>

