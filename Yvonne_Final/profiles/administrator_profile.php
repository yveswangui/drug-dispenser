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
</head>
<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h1>Welcome back, <?php echo $_SESSION['name']; ?>!</h1>
    <p>The Drug Dispenser system loves having you as an administrator.</p>
    
    <h2>Records</h2>
    <ul>
        <li><a href="patients.php">Patients</a></li>
        <li><a href="doctors.php">Doctors</a></li>
        <li><a href="pharmacies.php">Pharmacies</a></li>
        <li><a href="pharmaceuticals.php">Pharmaceuticals</a></li>
    </ul>

    <h2>Registrations</h2>
    <ul>
        <li><a href="../registration/patient_registration.php">Patient Registration</a></li>
        <li><a href="../registration/doctor_registration.php">Doctor Registration</a></li>
        <li><a href="../registration/pharmacy_registration.php">Pharmacy Registration</a></li>
        <li><a href="../registration/pharmaceutical_registration.php">Pharmaceutical Registration</a></li>
    </ul>
</body>
</html>
