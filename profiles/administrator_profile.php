<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Administrator Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link rel = "stylesheet" href = "styles/styles.css">
    <style>
	body {
	    padding: 0;
margin: 0;
        }
        h1,
        h2 {
            margin-top: 30px;
        }

        ul {
            list-style: none;
            padding-left: 0;
        }

        li {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
    <p>We love having you as an administrator.</p>

    <h2>Records</h2>
    <ul class="list-group">
        <li class="list-group-item"><a href="patients.php">Patients</a></li>
        <li class="list-group-item"><a href="doctors.php">Doctors</a></li>
        <li class="list-group-item"><a href="pharmacies.php">Pharmacies</a></li>
        <li class="list-group-item"><a href="pharmaceuticals.php">Pharmaceuticals</a></li>
    </ul>

    <h2>Registrations</h2>
    <ul class="list-group">
        <li class="list-group-item"><a href="../registration/patient_registration.php">Patient Registration</a></li>
        <li class="list-group-item"><a href="../registration/doctor_registration.php">Doctor Registration</a></li>
        <li class="list-group-item"><a href="../registration/pharmacy_registration.php">Pharmacy Registration</a></li>
        <li class="list-group-item"><a href="../registration/pharmaceutical_registration.php">Pharmaceutical Registration</a></li>
    </ul>
</div>
<?php echo $footer; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>

