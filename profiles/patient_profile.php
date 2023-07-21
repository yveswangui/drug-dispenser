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
    <meta charset="utf-8">
    <title>Patient Profile | <?php echo $_SESSION['name'] ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel = "stylesheet" href = "styles/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

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

        .list-group-item {
            background-color: #fff;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <div class="container">
        <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
        <h3>Patient Details</h3>
        <table class="table">
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
                <td>
                    <span id="dateOfBirth"><?php echo $patient['dateOfBirth']; ?></span>
                </td>
            </tr>
        </table>

        <h3>Assign Doctor</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?patientId=' . $_GET['patientId']); ?>">
            <div class="form-row">
                <div class="col-8">
                    <label for="doctorId">Select Doctor:</label>
                    <select name="doctorId" class="form-control" required>
                        <?php foreach ($unassignedDoctors as $doctor) : ?>
                            <option value="<?php echo $doctor['doctorId']; ?>">
                                <?php echo $doctor['firstName'] . ' ' . $doctor['lastName']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-4">
                    <label for="assignmentType">Assignment Type:</label>
                    <select name="assignmentType" class="form-control" required>
                        <option value="0">Secondary</option>
                        <option value="1">Primary</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Assign</button>
        </form>

        <h3>Assigned Doctors</h3>
        <?php if (count($assignedDoctors) > 0) : ?>
            <div class="list-group">
                <?php foreach ($assignedDoctors as $doctor) : ?>
                    <div class="list-group-item">
                        <h5 class="mb-1"><?php echo $doctor['firstName'] . ' ' . $doctor['lastName']; ?></h5>
                        <p class="mb-1"><i class="fas fa-venus-mars"></i> Gender: <?php echo $doctor['gender']; ?></p>
                        <p class="mb-1"><i class="fas fa-phone"></i> Phone Number: <?php echo $doctor['phoneNumber']; ?></p>
                        <p class="mb-1"><i class="fas fa-hospital"></i> Hospital: <?php echo $doctor['hospital']; ?></p>
                        <p class="mb-1"><i class="fas fa-envelope"></i> Email Address: <?php echo $doctor['emailAddress']; ?></p>
                        <p class="mb-1"><i class="fas fa-user-md"></i> Specialization: <?php echo $doctor['specialization']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No assigned doctors.</p>
        <?php endif; ?>

        <h3>Prescriptions</h3>
        <?php if (count($prescriptions) > 0) : ?>
            <table class="table">
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
        <td><?php echo $prescription['firstName'] . ' ' . $prescription['lastName']; ?></td>
        <td><?php echo $prescription['tradename']; ?></td>
        <td><?php echo $prescription['dosage']; ?></td>
        <td><?php echo $prescription['quantity']; ?></td>
        <td>
            <span class="date"><?php echo $prescription['startDate']; ?></span>
        </td>
        <td>
            <span class="date"><?php echo $prescription['endDate']; ?></span>
        </td>
        <td><?php echo $prescription['form']; ?></td>
    </tr>
<?php endforeach; ?>

            </table>
        <?php else : ?>
            <p>No prescriptions found.</p>
        <?php endif; ?>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var dateOfBirth = document.getElementById("dateOfBirth").textContent;
                var formattedDateOfBirth = moment(dateOfBirth).format('ddd D MMMM, YYYY');
                document.getElementById("dateOfBirth").textContent = formattedDateOfBirth;

        var dateElements = document.querySelectorAll(".date");
        dateElements.forEach(function(element) {
            var date = element.textContent;
            var formattedDate = moment(date).format('ddd D MMMM, YYYY');
            element.textContent = formattedDate;
        });
    });
</script>

        </script>
    </div>
<?php echo $footer; ?>
</body>

</html>


