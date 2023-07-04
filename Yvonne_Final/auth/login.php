<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once '../credentials.php';

	$email = $_POST['email'];
	$password = $_POST['password'];
	$role = $_POST['role'];

	// Database connection
	$db = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	switch ($role) {
	case 'patient':
		$query = $db->prepare("SELECT * FROM patient WHERE emailAddress = :email");
		$query->bindValue(':email', $email);
		break;
	case 'doctor':
		$query = $db->prepare("SELECT * FROM doctor WHERE emailAddress = :email");
		$query->bindValue(':email', $email);
		break;
	case 'pharmacy':
		$query = $db->prepare("SELECT * FROM pharmacy WHERE emailAddress = :email");
		$query->bindValue(':email', $email);
		break;
	case 'pharmaceutical':
		$query = $db->prepare("SELECT * FROM pharmaceutical WHERE emailAddress = :email");
		$query->bindValue(':email', $email);
		break;
	case 'administrator':
		$userName = $username . '@gmail.com';
		if ($password === $dbPassword && $email === $userName) {
			$_SESSION['role'] = $role;
			$_SESSION['name'] = 'Administrator';
			header('Location: ../profiles/administrator_profile.php');
			exit;
		} else {
			// Invalid administrator credentials
			echo 'Invalid administrator credentials.';
			header('Location: login.php');
			exit;
		}
	default:
		echo 'Invalid role.';
		header('Location: login.php');
		exit;
	}

	$query->execute();
	$user = $query->fetch(PDO::FETCH_ASSOC);

	if ($user) {
		// Verify the password
		if (password_verify($password, $user['passwordHash'])) {
			// Set the session variables based on the role
			$_SESSION['role'] = $role;
			switch ($role) {
			case 'patient':
				$_SESSION['patientId'] = $user['patientId'];
				$_SESSION['name'] = $user['firstName'] . ' ' . $user['lastName'];
				header('Location: ../profiles/patient_profile.php?patientId=' . $user['patientId']);
				break;
			case 'doctor':
				$_SESSION['doctorId'] = $user['doctorId'];
				$_SESSION['name'] = $user['firstName'] . ' ' . $user['lastName'];
				header('Location: ../profiles/doctor_profile.php?doctorId=' . $user['doctorId']);
				break;
			case 'pharmacy':
				$_SESSION['pharmacyId'] = $user['pharmacyId'];
				$_SESSION['name'] = $user['name'];
				header('Location: ../profiles/pharmacy_profile.php?pharmacyId=' . $user['pharmacyId']);
				break;
			case 'pharmaceutical':
				$_SESSION['pharmaceuticalId'] = $user['pharmaceuticalId'];
				$_SESSION['name'] = $user['name'];
				header('Location: ../profiles/pharmaceutical_profile.php?pharmaceuticalId=' . $user['pharmaceuticalId']);
				break;
			}
			exit;
		} else {
			// Invalid password
			echo 'Invalid password.';
			header('Location: login.php');
			exit;
		}
	} else {
		// User not found
		echo 'User not found.';
		header('Location: login.php');
		exit;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="login.php">
	<label for="email">Email:</label>
	<input type="email" id="email" name="email" required><br><br>

	<label for="password">Password:</label>
	<input type="password" id="password" name="password" required><br><br>

	<label for="role">Role:</label>
	<select id="role" name="role" required>
	    <option value="patient">Patient</option>
	    <option value="doctor">Doctor</option>
	    <option value="pharmacy">Pharmacy</option>
	    <option value="pharmaceutical">Pharmaceutical</option>
	    <option value="administrator">Administrator</option>
	</select><br><br>

	<input type="submit" value="Login">
    </form>
</body>
</html>
