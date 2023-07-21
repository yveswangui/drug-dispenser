<?php
// commons.php

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['role']);

// Function to generate the profile link based on user role
function getProfileLink()
{
	$role = $_SESSION['role'];
	if ($role === 'administrator') {
		return '../profiles/administrator_profile.php';
	} elseif ($role === 'pharmacy' || $role === 'patient' || $role === 'doctor' || $role === 'pharmaceutical') {
		$profileId = $_SESSION[$role . 'Id'];
		return '../profiles/' . $role . '_profile.php?' . $role . 'Id=' . $profileId;
	} else {
		return '#';
	}
}

if ($isLoggedIn) {
	$profileLink = getProfileLink();
}

$navigation_bar = <<<_HTML
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
	<a class="navbar-brand" href="../static_pages/homepage.php">
	<img src="../static_pages/static/images/logo.png" class = "rounded-circle" alt="Company Logo" width="50">
	</a>

	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
	aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarNav">
	<ul class="navbar-nav mx-auto">
	<li class="nav-item">
	<a class="nav-link" href="../static_pages/homepage.php">Homepage</a>
	</li>
	<li class="nav-item">
	<a class="nav-link" href="../static_pages/about_us.php">About Us</a>
	</li>
	<li class="nav-item">
	<a class="nav-link" href="../static_pages/contact_us.php">Contacts</a>
	</li>
	</ul>

	<ul class="navbar-nav">
	_HTML;

if ($isLoggedIn) {
	$navigation_bar .= <<<_HTML
		<li class="nav-item">
		<a class="nav-link" href="{$profileLink}">Profile</a>
		</li>
		<li class="nav-item">
		<a class="nav-link" href="../auth/logout.php">Sign Out</a>
		</li>
		_HTML;

} else {
	$navigation_bar .= <<<_HTML
		<li class="nav-item">
		<a class="nav-link" href="../auth/login.php">Sign In</a>
		</li>
		<li class="nav-item">
		<a class="nav-link" href="../registration/patient_registration.php">Sign Up</a>
		</li>
		_HTML;
}
$navigation_bar .= <<<_HTML
	</ul>
	</div>
	</div>
	</nav>
	_HTML;

// Footer variable
$footer = <<<_HTML
	<footer style = "padding: 100px 40px;" class="bg-dark text-light text-center py-4">
	<div class="container" style = "padding-top: 50px; padding-bottom: 30px;">
	<div class="row">
	<div class="col-md-4">
	<h5>Dawa Bora Enterprises</h5>
	<p>Ole Sangale Keri Link Road, Madaraka, Nairobi</p>
	<p>Phone: 123-456-7890</p>
	</div>
	<div class="col-md-4">
	<h5>Quick Links</h5>
	<ul class="list-unstyled">
	<li><a href="../static_pages/homepage.php">Home</a></li>
	<li><a href="../static_pages/homepage.php">About</a></li>
	<li><a href="../static_pages/homepage.php">Contact</a></li>
	</ul>
	</div>
	<div class="col-md-4">
	<h5>Follow Us</h5>
	<ul class="list-inline">
	<ul class="list-inline">
	<li class="list-inline-item"><a href="#"><i class="fab fa-facebook"></i></a></li>
	<li class="list-inline-item"><a href="#"><i class="fab fa-twitter"></i></a></li>
	<li class="list-inline-item"><a href="#"><i class="fab fa-instagram"></i></a></li>
	</ul>
	</ul>
	</div>
	</div>
	</div>
	</footer>
	_HTML;
?>
