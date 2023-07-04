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

// Query to get total number of assigned prescriptions
$totalRecordsQuery = "SELECT COUNT(*) AS totalRecords FROM prescription 
    INNER JOIN doctor_patient_assignment ON prescription.doctorPatientAssignmentId = doctor_patient_assignment.doctorPatientAssignmentId
    WHERE doctor_patient_assignment.doctorId = $doctorId";
$totalRecordsResult = $conn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['totalRecords'];

// Query to get assigned prescriptions with pagination
$prescriptionsQuery = "SELECT prescription.*, drug.tradename, drug.scientificName, drug.formula, drug.form, 
    patient.firstName AS patientFirstName, patient.lastName AS patientLastName
    FROM prescription 
    INNER JOIN doctor_patient_assignment ON prescription.doctorPatientAssignmentId = doctor_patient_assignment.doctorPatientAssignmentId
    INNER JOIN drug ON prescription.drugId = drug.drugId
    INNER JOIN patient ON doctor_patient_assignment.patientId = patient.patientId
    WHERE doctor_patient_assignment.doctorId = $doctorId
    ORDER BY prescription.prescriptionId ASC LIMIT $offset, $recordsPerPage";
$prescriptionsResult = $conn->query($prescriptionsQuery);

// Fetch assigned prescriptions
$assignedPrescriptions = [];
while ($row = $prescriptionsResult->fetch_assoc()) {
    $assignedPrescriptions[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Assigned Prescriptions | <?php echo $_SESSION['name']; ?></title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h2>Assigned Prescriptions</h2>
    <table>
        <thead>
            <tr>
                <th>Prescription ID</th>
                <th>Patient</th>
                <th>Drug Tradename</th>
                <th>Drug Scientific Name</th>
                <th>Drug Formula</th>
                <th>Drug Form</th>
                <th>Dosage</th>
                <th>Quantity</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assignedPrescriptions as $prescription) { ?>
                <tr>
                    <td><?php echo $prescription['prescriptionId']; ?></td>
                    <td><?php echo $prescription['patientFirstName'] . ' ' . $prescription['patientLastName']; ?></td>
                    <td><?php echo $prescription['tradename']; ?></td>
                    <td><?php echo $prescription['scientificName']; ?></td>
                    <td><?php echo $prescription['formula']; ?></td>
                    <td><?php echo $prescription['form']; ?></td>
                    <td><?php echo $prescription['dosage']; ?></td>
                    <td><?php echo $prescription['quantity']; ?></td>
                    <td><?php echo $prescription['startDate']; ?></td>
                    <td><?php echo $prescription['endDate']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div>
        <?php
        $totalPages = ceil($totalRecords / $recordsPerPage);

        if ($page > 1) { ?>
            <a href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo ($page - 1); ?>">Previous</a>
        <?php }

        for ($i = 1; $i <= $totalPages; $i++) { ?>
            <a href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php }

        if ($page < $totalPages) { ?>
            <a href="?doctorId=<?php echo $doctorId; ?>&page=<?php echo ($page + 1); ?>">Next</a>
        <?php } ?>
    </div>
</body>
</html>
