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

	// Check if the form is submitted for creating a contract
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pharmacyId'])) {
		$pharmacyId = $_POST['pharmacyId'];

		// Prepare the SQL statement to create a contract
		$stmt = $pdo->prepare("INSERT INTO contract (title, startDate, endDate, pharmacyId, pharmaceuticalId) VALUES (?, ?, ?, ?, ?)");
		$stmt->execute([sanitizeInput($_POST['title']), $_POST['startDate'], $_POST['endDate'], $pharmacyId, $_GET['pharmaceuticalId']]);
	}

	// Prepare the SQL statement to fetch pharmaceutical details
	$stmt = $pdo->prepare("SELECT * FROM pharmaceutical WHERE pharmaceuticalId = ?");
	$stmt->execute([$_GET['pharmaceuticalId']]);
	$pharmaceutical = $stmt->fetch(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch the latest contracts assigned to the pharmaceutical
	$stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacyName FROM contract
		INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
		WHERE contract.pharmaceuticalId = ?
		ORDER BY contract.contractId DESC
		LIMIT 10");
	$stmt->execute([$_GET['pharmaceuticalId']]);
	$latestContracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch all pharmacies
	$stmt = $pdo->prepare("SELECT * FROM pharmacy");
	$stmt->execute();
	$pharmacies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmaceutical Profile</title>
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
    <h1>Welcome back to <?php echo $pharmaceutical['name']; ?>!</h1>
    <h3>Pharmaceutical Details</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Contact Person</th>
                <th>Email Address</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $pharmaceutical['name']; ?></td>
                <td><?php echo $pharmaceutical['location']; ?></td>
                <td><?php echo $pharmaceutical['operator']; ?></td>
                <td><a href = "mailto:<?php echo $pharmaceutical['emailAddress']; ?>"><?php echo $pharmaceutical['emailAddress']; ?></a></td>
                <td><a href = "tel:<?php echo $pharmaceutical['phoneNumber']; ?>"><?php echo $pharmaceutical['phoneNumber']; ?></a></td>
            </tr>
        </tbody>
    </table>

    <h3>Create Contract</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmaceuticalId=' . $_GET['pharmaceuticalId']); ?>">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="startDate">Start Date:</label>
            <input type="date" name="startDate" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="endDate">End Date:</label>
            <input type="date" name="endDate" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="pharmacyId">Select Pharmacy:</label>
            <select name="pharmacyId" class="form-control" required>
                <?php foreach ($pharmacies as $pharmacy) : ?>
                    <option value="<?php echo $pharmacy['pharmacyId']; ?>">
                        <?php echo $pharmacy['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Contract</button>
    </form>

    <h3>Latest Contracts</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Pharmacy</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latestContracts as $contract) : ?>
                <tr>
                    <td>
                        <a href="contract_profile.php?contractId=<?php echo $contract['contractId']; ?>">
                            <?php echo $contract['pharmacyName']; ?>
                        </a>
                    </td>
                    <td class = "date"><?php echo $contract['startDate']; ?></td>
                    <td class = "date"><?php echo $contract['endDate']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="assigned_contracts.php?pharmaceuticalId=<?php echo $_GET['pharmaceuticalId']; ?>" class="btn btn-primary">View All Assigned Contracts</a></p>
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
