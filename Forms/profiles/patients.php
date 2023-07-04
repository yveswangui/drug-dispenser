<?php
session_start();
// Check whether logged in user is administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Patients | <?php echo $_SESSION['name']; ?></title>
    <style>
    	table {
	    width: 100%;
	}

        table, th, td {
            border-collapse: collapse;
	    border: 1px solid black;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: #000;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <a href = "../auth/logout.php">Log Out</a>
    <h1>Registered Patients</h1>

    <?php
    // Include database credentials
    require_once('../credentials.php');

    // Number of patients to display per page
    $patientsPerPage = 10;

    // Get the current page number from the query string
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

    // Calculate the offset for the database query
    $offset = ($currentPage - 1) * $patientsPerPage;

    // Establish database connection
    $conn = new mysqli($host, $username, $dbPassword, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve total number of patients
    $totalCountQuery = "SELECT COUNT(*) AS total FROM patient";
    $totalCountResult = $conn->query($totalCountQuery);
    $totalCountRow = $totalCountResult->fetch_assoc();
    $totalCount = $totalCountRow['total'];

    // Calculate the total number of pages
    $totalPages = ceil($totalCount / $patientsPerPage);

    // Retrieve patients for the current page
    $query = "SELECT * FROM patient LIMIT $patientsPerPage OFFSET $offset";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Location</th>
                <th>Email</th>
                <th>Phone</th>
              </tr>";

        // Display patient records
        while ($row = $result->fetch_assoc()) {
            $patientId = $row['patientId'];
            $firstName = $row['firstName'];
            $lastName = $row['lastName'];
            $gender = $row['gender'];
            $location = $row['location'];
            $emailAddress = $row['emailAddress'];
            $phoneNumber = $row['phoneNumber'];

            echo "<tr>";
            echo "<td>$patientId</td>";
            echo "<td><a href='patient_profile.php?patientId=$patientId'>$firstName $lastName</a></td>";
            echo "<td>$gender</td>";
            echo "<td><a href='https://www.google.com/maps?q=$location' target='_blank'>$location</a></td>";
            echo "<td><a href='mailto:$emailAddress'>$emailAddress</a></td>";
            echo "<td><a href='tel:$phoneNumber'>$phoneNumber</a></td>";
            echo "</tr>";
        }

        echo "</table>";

        // Pagination links
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $currentPage) ? 'class="active"' : '';
            echo "<a href='patients.php?page=$i' $activeClass>$i</a>";
        }
        echo "</div>";
    } else {
        echo "No patients found.";
    }

    // Close database connection
    $conn->close();
    ?>

</body>
</html>
