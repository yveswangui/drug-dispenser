<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is doctor or administrator
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'doctor')) {
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

	// Check if the form is submitted for assigning a patient or prescription
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Assigning a patient
		if (isset($_POST['patientId']) && isset($_POST['assignmentType'])) {
			$patientId = $_POST['patientId'];
			$assignmentType = $_POST['assignmentType'];

			// Prepare the SQL statement to assign the patient to the doctor
			$stmt = $pdo->prepare("INSERT INTO doctor_patient_assignment (doctorId, patientId, primaryAssignment) VALUES (?, ?, ?)");
			$stmt->execute([$_GET['doctorId'], $patientId, $assignmentType]);
		}
		// Assigning a prescription
		elseif (isset($_POST['patientId']) && isset($_POST['drugId']) && isset($_POST['dosage'])
			&& isset($_POST['quantity']) && isset($_POST['startDate']) && isset($_POST['endDate'])) {
			$patientId = $_POST['patientId'];
			$drugId = $_POST['drugId'];
			$dosage = sanitizeInput($_POST['dosage']);
			$quantity = sanitizeInput($_POST['quantity']);
			$startDate = sanitizeInput($_POST['startDate']);
			$endDate = sanitizeInput($_POST['endDate']);

			// Prepare the SQL statement to fetch doctorPatientAssignmentId
			$stmt = $pdo->prepare("SELECT doctorPatientAssignmentId FROM doctor_patient_assignment WHERE doctorId = ? AND patientId = ?");
			$stmt->execute([$_GET['doctorId'], $patientId]);
			$doctorPatientAssignmentId = $stmt->fetch(PDO::FETCH_ASSOC)['doctorPatientAssignmentId'];

			// Prepare the SQL statement to assign the prescription to the patient
			$stmt = $pdo->prepare("INSERT INTO prescription (doctorPatientAssignmentId, drugId, dosage, quantity, startDate, endDate) VALUES (?, ?, ?, ?, ?, ?)");
			$stmt->execute([$doctorPatientAssignmentId, $drugId, $dosage, $quantity, $startDate, $endDate]);
		}
	}

	// Prepare the SQL statement to fetch doctor details
	$stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctorId = ?");
	$stmt->execute([$_GET['doctorId']]);
	$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch assigned patients
	$stmt = $pdo->prepare("SELECT patient.*, doctor_patient_assignment.primaryAssignment FROM patient
		INNER JOIN doctor_patient_assignment ON patient.patientId = doctor_patient_assignment.patientId
		WHERE doctor_patient_assignment.doctorId = ?
		ORDER BY doctor_patient_assignment.doctorPatientAssignmentId DESC
		LIMIT 10");
	$stmt->execute([$_GET['doctorId']]);
	$assignedPatients = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch unassigned patients
	$stmt = $pdo->prepare("SELECT * FROM patient WHERE patientId NOT IN (
		SELECT patientId FROM doctor_patient_assignment WHERE doctorId = ?
	)");
	$stmt->execute([$_GET['doctorId']]);
	$unassignedPatients = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch prescriptions assigned by the doctor
	$stmt = $pdo->prepare("SELECT prescription.*, patient.firstName, patient.lastName, drug.* FROM prescription
		INNER JOIN doctor_patient_assignment ON prescription.doctorPatientAssignmentId = doctor_patient_assignment.doctorPatientAssignmentId
		INNER JOIN patient ON doctor_patient_assignment.patientId = patient.patientId INNER JOIN drug ON drug.drugId = prescription.drugId
		WHERE doctor_patient_assignment.doctorId = ?");
	$stmt->execute([$_GET['doctorId']]);
	$assignedPrescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Fetch records of all drugs
	$stmt = $pdo->prepare("SELECT * FROM drug");
	$stmt->execute();
	$drugs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
	die("Error connecting to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
	.container {
	    max-width: 800px;
	    margin: 0 auto;
	    padding: 20px;
	}

	.navbar {
	    background-color: #fff;
	    border-bottom: 1px solid #dee2e6;
	    padding: 10px;
	}

	h1,
	h3 {
	    margin-top: 20px;
	    margin-bottom: 10px;
	    font-weight: bold;
	}

	table {
	    width: 100%;
	    background-color: #fff;
	    border: 1px solid #dee2e6;
	    border-collapse: collapse;
	    margin-bottom: 20px;
	}

	th,
	td {
	    padding: 10px;
	    text-align: center;
	    border: 1px solid #dee2e6;
	}

	.form-row {
	    margin-bottom: 20px;
	}
    </style>
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
 <div class="container">
    <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
    <h3>Doctor Details</h3>
    <table class="table table-bordered">
	<thead>
	    <tr>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Gender</th>
		<th>Phone Number</th>
		<th>Hospital</th>
		<th>Email Address</th>
		<th>Specialization</th>
	    </tr>
	</thead>
	<tbody>
	    <tr>
		<td><?php echo $doctor['firstName']; ?></td>
		<td><?php echo $doctor['lastName']; ?></td>
		<td><?php echo $doctor['gender']; ?></td>
		<td><?php echo $doctor['phoneNumber']; ?></td>
		<td><?php echo $doctor['hospital']; ?></td>
		<td><?php echo $doctor['emailAddress']; ?></td>
		<td><?php echo $doctor['specialization']; ?></td>
	    </tr>
	</tbody>
    </table>

    <h3>Assign Patient</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?doctorId=' . $_GET['doctorId']); ?>">
	<div class="form-group">
	    <label for="patientId">Select Patient:</label>
	    <select name="patientId" class="form-control" required>
		<?php foreach ($unassignedPatients as $patient) : ?>
		    <option value="<?php echo $patient['patientId']; ?>">
			<?php echo $patient['firstName'] . ' ' . $patient['lastName']; ?>
		    </option>
		<?php endforeach; ?>
	    </select>
	</div>
	<div class="form-group">
	    <label for="assignmentType">Assignment Type:</label>
	    <select name="assignmentType" class="form-control" required>
		<option value="0">Secondary</option>
		<option value="1">Primary</option>
	    </select>
	</div>
	<button type="submit" class="btn btn-primary">Assign</button>
    </form>

    <h3>Assigned Patients</h3>
    <table class="table table-bordered">
	<thead>
	    <tr>
		<th>Patient Name</th>
		<th>Assignment Type</th>
	    </tr>
	</thead>
	<tbody>
	    <?php foreach ($assignedPatients as $patient) : ?>
		<tr>
		    <td><?php echo $patient['firstName'] . ' ' . $patient['lastName']; ?></td>
		    <td><?php echo $patient['primaryAssignment'] ? 'Primary' : 'Secondary'; ?></td>
		</tr>
	    <?php endforeach; ?>
	</tbody>
    </table>

    <p><a href="assigned_patients.php?doctorId=<?php echo $_GET['doctorId']; ?>" class="btn btn-primary">View All Assigned Patients</a></p>

    <h3>Assign Prescription</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?doctorId=' . $_GET['doctorId']); ?>">
	<div class="form-group">
	    <label for="patientId">Select Patient:</label>
	    <select name="patientId" class="form-control" required>
		<?php foreach ($assignedPatients as $patient) : ?>
		    <option value="<?php echo $patient['patientId']; ?>">
			<?php echo $patient['firstName'] . ' ' . $patient['lastName']; ?>
		    </option>
		<?php endforeach; ?>
	    </select>
	</div>
	<div class="form-group">
	    <label for="drugId">Select Drug:</label>
	    <select name="drugId" class="form-control" required>
		<?php foreach ($drugs as $drug) : ?>
		    <option value="<?php echo $drug['drugId']; ?>">
			<?php echo $drug['tradename']; ?>
		    </option>
		<?php endforeach; ?>
	    </select>
	</div>
	<div class="form-group">
	    <label for="dosage">Dosage:</label>
	    <input type="text" name="dosage" class="form-control" required>
	</div>
	<div class="form-group">
	    <label for="quantity">Quantity:</label>
	    <input type="text" name="quantity" class="form-control" required>
	</div>
	<div class="form-group">
	    <label for="startDate">Start Date:</label>
	    <input type="date" name="startDate" class="form-control" required>
	</div>
	<div class="form-group">
	    <label for="endDate">End Date:</label>
	    <input type="date" name="endDate" class="form-control" required>
	</div>
	<button type="submit" class="btn btn-primary">Assign</button>
    </form>

    <h3>Assigned Prescriptions</h3>
    <table class="table table-bordered">
	<thead>
	    <tr>
		<th>Patient Name</th>
		<th>Drug</th>
		<th>Dosage</th>
		<th>Quantity</th>
		<th>Start Date</th>
		<th>End Date</th>
	    </tr>
	</thead>
	<tbody>
	    <?php foreach ($assignedPrescriptions as $prescription) : ?>
		<tr>
		    <td><?php echo $prescription['firstName'] . ' ' . $prescription['lastName']; ?></td>
		    <td><?php echo $prescription['tradename']; ?></td>
		    <td><?php echo $prescription['dosage']; ?></td>
		    <td><?php echo $prescription['quantity']; ?></td>
		    <td class = "date"><?php echo $prescription['startDate']; ?></td>
		    <td class = "date"><?php echo $prescription['endDate']; ?></td>
		</tr>
	    <?php endforeach; ?>
	</tbody>
    </table>
    <p><a href="assigned_prescriptions.php?doctorId=<?php echo $_GET['doctorId']; ?>" class="btn btn-primary">View All Assigned Prescriptions</a></p>
</div>
<?php echo $footer; ?>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
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
