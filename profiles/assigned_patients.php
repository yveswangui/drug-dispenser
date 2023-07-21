<?php
session_start();
require_once('../credentials.php');

// Check whether logged in user is doctor or administrator
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'doctor')) {
    header('Location: ../auth/login.php');
    exit;
}

$conn = new mysqli($host, $username, $dbPassword, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$recordsPerPage = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

// Get doctorId from URL parameter
if (!isset($_GET['doctorId'])) {
    die("Missing doctorId parameter");
}
$doctorId = $_GET['doctorId'];

// Query to get total number of assigned patients
$totalRecordsQuery = "SELECT COUNT(*) AS totalRecords FROM doctor_patient_assignment WHERE doctorId = $doctorId";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['totalRecords'];

// Query to get assigned patients with pagination
$patientsQuery = "SELECT patient.* FROM patient 
    INNER JOIN doctor_patient_assignment ON patient.patientId = doctor_patient_assignment.patientId
    WHERE doctor_patient_assignment.doctorId = $doctorId
    ORDER BY patient.patientId ASC LIMIT $offset, $recordsPerPage";
$patientsResult = $conn->query($patientsQuery);

// Fetch assigned patients
$assignedPatients = [];
while ($row = $patientsResult->fetch_assoc()) {
    $assignedPatients[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assigned Patients | <?php echo $_SESSION['name']; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel = "stylesheet" href = "styles/pagination.css">
<link rel = "stylesheet" href = "styles/styles.css">
</head>
<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h2>Assigned Patients</h2>
    <table class="table table-striped table-hover">
        <thead class="thead">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Gender</th>
                <th>Location</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>SSN</th>
                <th>Date of Birth</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignedPatients as $patient) { ?>
                <tr>
                    <td><?php echo $patient['firstName']; ?></td>
                    <td><?php echo $patient['lastName']; ?></td>
                    <td><?php echo $patient['gender']; ?></td>
                    <td><?php echo $patient['location']; ?></td>
                    <td><?php echo $patient['emailAddress']; ?></td>
                    <td><?php echo $patient['phoneNumber']; ?></td>
                    <td><?php echo $patient['SSN']; ?></td>
                    <td><?php echo $patient['dateOfBirth']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <br>

    <?php
    // Pagination
    $totalPages = ceil($totalRecords / $recordsPerPage);
    ?>
    <div class="pagination">
        <?php if ($page > 1) { ?>
            <a href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo ($page - 1); ?>">Previous</a>
        <?php } ?>

        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <a class="<?php echo ($i == $page) ? 'current-page' : ''; ?>" href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php } ?>

        <?php if ($page < $totalPages) { ?>
            <a href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo ($page + 1); ?>">Next</a>
        <?php } ?>
    </div>

</div>
<?php echo $footer; ?>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>

