<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header('Location: ../auth/login.php');
    exit;
}

// Pagination configuration
$resultsPerPage = 10;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($currentPage - 1) * $resultsPerPage;

// Database connection
$db = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch doctors count for pagination
$countQuery = $db->query("SELECT COUNT(*) FROM doctor");
$totalResults = $countQuery->fetchColumn();
$totalPages = ceil($totalResults / $resultsPerPage);

// Fetch doctors with pagination
$doctorsQuery = $db->query("SELECT * FROM doctor LIMIT $offset, $resultsPerPage");
$doctors = $doctorsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctors | <?php echo $_SESSION['name']; ?></title>
    <style>
    	table {
	    width: 100%;
	}

        table, th, td {
            border-collapse: collapse;
	    border: 1px solid black;
        }

	.pagination {
	    margin-top: 20px;
	}

	.pagination a {
	    padding: 8px 16px;
	    text-decoration: none;
	    color: #000;
	    background-color: #f2f2f2;
	    border: 1px solid #ddd;
	}

	.pagination a:hover {
	    background-color: #ddd;
	}

	.pagination .active {
	    background-color: #4CAF50;
	    color: white;
	}
    </style>
</head>
<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h1>Doctors</h1>
    <table>
	<tr>
	    <th>Doctor ID</th>
	    <th>First Name</th>
	    <th>Last Name</th>
	    <th>Gender</th>
	    <th>Phone Number</th>
	    <th>Hospital</th>
	</tr>
	<?php foreach ($doctors as $doctor): ?>
	    <tr>
		<td><?php echo $doctor['doctorId']; ?></td>
		<td><a href="doctor_profile.php?doctorId=<?php echo $doctor['doctorId']; ?>"><?php echo $doctor['firstName']; ?></a></td>
		<td><?php echo $doctor['lastName']; ?></td>
		<td><?php echo $doctor['gender']; ?></td>
		<td><a href="tel:<?php echo $doctor['phoneNumber']; ?>"><?php echo $doctor['phoneNumber']; ?></a></td>
		<td><a href="mailto:<?php echo $doctor['emailAddress']; ?>"><?php echo $doctor['hospital']; ?></a></td>
	    </tr>
	<?php endforeach; ?>
    </table>

    <div class="pagination">
	<?php if ($currentPage > 1): ?>
	    <a href="?page=<?php echo $currentPage - 1; ?>">&laquo; Previous</a>
	<?php endif; ?>
	<?php for ($i = 1; $i <= $totalPages; $i++): ?>
	    <a <?php if ($i === $currentPage) echo 'class="active"'; ?> href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
	<?php endfor; ?>
	<?php if ($currentPage < $totalPages): ?>
	    <a href="?page=<?php echo $currentPage + 1; ?>">Next &raquo;</a>
	<?php endif; ?>
    </div>
</body>
</html>
