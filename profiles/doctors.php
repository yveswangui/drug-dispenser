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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel = "stylesheet" href = "styles/pagination.css">
<link rel = "stylesheet" href = "styles/styles.css">
</head>
<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h1>Doctors</h1>
    <table class="table table-hover table-striped">
        <thead class="thead">
            <tr>
                <th>Doctor ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Phone Number</th>
                <th>Hospital</th>
            </tr>
        </thead>
        <tbody>
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
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?php echo $currentPage - 1; ?>" class="btn btn-primary">&laquo; Previous</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a <?php if ($i === $currentPage) echo 'class="btn btn-primary active"'; ?> href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?php echo $currentPage + 1; ?>" class="btn btn-primary">Next &raquo;</a>
        <?php endif; ?>
    </div>

</div>
<?php echo $footer; ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
