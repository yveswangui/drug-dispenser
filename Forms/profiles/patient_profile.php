<?php
session_start();
require_once '../credentials.php';

// Check whether logged in user is patient or administrator
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'administrator' && $_SESSION['role'] !== 'patient')) {
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

	// Check if the form is submitted for assigning a doctor
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctorId']) && isset($_POST['assignmentType'])) {
		$doctorId = $_POST['doctorId'];
		$assignmentType = $_POST['assignmentType'];

		// Prepare the SQL statement to assign the doctor to the patient
		$stmt = $pdo->prepare("INSERT INTO doctor_patient_assignment (doctorId, patientId, primaryAssignment) VALUES (?, ?, ?)");
		$stmt->execute([$doctorId, $_GET['patientId'], $assignmentType]);
	}

	// Prepare the SQL statement to fetch patient details
	$stmt = $pdo->prepare("SELECT * FROM patient WHERE patientId = ?");
	$stmt->execute([$_GET['patientId']]);
	$patient = $stmt->fetch(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch assigned doctors
	$stmt = $pdo->prepare("SELECT doctor.* FROM doctor
		INNER JOIN doctor_patient_assignment ON doctor.doctorId = doctor_patient_assignment.doctorId
		WHERE doctor_patient_assignment.patientId = ?");
	$stmt->execute([$_GET['patientId']]);
	$assignedDoctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch prescriptions
	$stmt = $pdo->prepare("SELECT prescription.*, drug.tradename, drug.form, doctor.* FROM prescription
		INNER JOIN drug ON prescription.drugId = drug.drugId 
		INNER JOIN doctor_patient_assignment ON prescription.doctorPatientAssignmentId = doctor_patient_assignment.doctorPatientAssignmentId
		INNER JOIN doctor ON doctor.doctorId = doctor_patient_assignment.doctorId 
		WHERE doctor_patient_assignment.patientId = ?");
	$stmt->execute([$_GET['patientId']]);
	$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Prepare the SQL statement to fetch unassigned doctors
	$stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctorId NOT IN (
		SELECT doctorId FROM doctor_patient_assignment WHERE patientId = ?
	)");
	$stmt->execute([$_GET['patientId']]);
	$unassignedDoctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Close the database connection
	$pdo = null;
} catch (PDOException $e) {
	// Display error message
	echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>
	Patient Profile | <?php echo $_SESSION['name'] ?>
    </title>
    <style>
	table {
	    border-collapse: collapse;
	    width: 100%;
	}

	table, th, td {
	    border: 1px solid black;
	    padding: 5px;
	}
    </style>
</head>

<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
    <h3>Patient Details</h3>
    <table>
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
    </table>

    <h3>Assign Doctor</h3>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?patientId=' . $_GET['patientId']); ?>">
	<label for="doctorId">Select Doctor:</label>
	<select name="doctorId" required>
	    <?php foreach ($unassignedDoctors as $doctor) : ?>
		<option value="<?php echo $doctor['doctorId']; ?>">
		    <?php echo $doctor['firstName'] . ' ' . $doctor['lastName']; ?>
		</option>
	    <?php endforeach; ?>
	</select>
	<label for="assignmentType">Assignment Type:</label>
	<select name="assignmentType" required>
	    <option value="0">Secondary</option>
	    <option value="1">Primary</option>
	</select>
	<input type="submit" value="Assign">
    </form>

    <h3>Assigned Doctors</h3>
    <table>
	<tr>
	    <th>First Name</th>
	    <th>Last Name</th>
	    <th>Gender</th>
	    <th>Phone Number</th>
	    <th>Hospital</th>
	    <th>Email Address</th>
	    <th>Specialization</th>
	</tr>
	<?php foreach ($assignedDoctors as $doctor) : ?>
	    <tr>
		<td><?php echo $doctor['firstName']; ?></td>
		<td><?php echo $doctor['lastName']; ?></td>
		<td><?php echo $doctor['gender']; ?></td>
		<td><?php echo $doctor['phoneNumber']; ?></td>
		<td><?php echo $doctor['hospital']; ?></td>
		<td><?php echo $doctor['emailAddress']; ?></td>
		<td><?php echo $doctor['specialization']; ?></td>
	    </tr>
	<?php endforeach; ?>
    </table>

    <h3>Prescriptions</h3>
    <table>
	<tr>
	    <th>Prescription ID</th>
	    <th>Doctor Name</th>
	    <th>Drug Name</th>
	    <th>Dosage</th>
	    <th>Quantity</th>
	    <th>Start Date</th>
	    <th>End Date</th>
	    <th>Form</th>
	</tr>
	<?php foreach ($prescriptions as $prescription) : ?>
	    <tr>
		<td><?php echo $prescription['prescriptionId']; ?></td>
		<td>
		<?php
		$doctorName = $prescription['firstName'] . ' ' . $prescription['lastName'];
		echo $doctorName;
		?>
		</td>
		<td><?php echo $prescription['tradename']; ?></td>
		<td><?php echo $prescription['dosage']; ?></td>
		<td><?php echo $prescription['quantity']; ?></td>
		<td><?php echo $prescription['startDate']; ?></td>
		<td><?php echo $prescription['endDate']; ?></td>
		<td><?php echo $prescription['form']; ?></td>
	    </tr>
	<?php endforeach; ?>
    </table>
</body>

</html>
