<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // User is not logged in, redirect to login page
    header("Location: auth/login.php");
    exit;
}

// User is logged in, check their role
if (isset($_SESSION['role'])) {
    $role = $_SESSION['role'];

    // Redirect the user based on their role
    switch ($role) {
        case 'administrator':
            header("Location: administrator_profile.php");
            break;
        case 'pharmacy':
            header("Location: pharmacy_profile.php?pharmacyId=" . $_SESSION['pharmacyId']);
            break;
        case 'patient':
            header("Location: patient_profile.php?patientId=" . $_SESSION['patientId']);
            break;
        case 'doctor':
            header("Location: doctor_profile.php?doctorId=" . $_SESSION['doctorId']);
            break;
        case 'pharmaceutical':
            header("Location: pharmaceutical_profile.php?pharmaceuticalId=" . $_SESSION['pharmaceuticalId']);
            break;
        default:
            // Unknown role, redirect to a default page or display an error message
            header("Location: default_page.php");
            break;
    }
    exit;
}
?>
