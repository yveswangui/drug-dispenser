<?php
session_start();
require_once '../credentials.php';

// Check if patient is logged in
if ($_SESSION['role'] !== 'patient') {
    header("Location: ../auth/login.php");
    exit;
}

// Check if patientId is provided in the URL
if (!isset($_GET['patientId'])) {
    header("Location: patient_profile.php"); // Redirect to the patient profile page or display an error message
    exit;
}

// Get the patientId from the URL
$patientId = $_GET['patientId'];

// Function to sanitize user input
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to move the uploaded image to the desired directory
function moveUploadedFile($tempFilePath, $newFilePath)
{
    if (move_uploaded_file($tempFilePath, $newFilePath)) {
        return true;
    }
    return false;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $firstName = sanitizeInput($_POST['firstName']);
    $lastName = sanitizeInput($_POST['lastName']);
    $gender = sanitizeInput($_POST['gender']);
    $location = sanitizeInput($_POST['location']);
    $emailAddress = sanitizeInput($_POST['emailAddress']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $imageUrl = '';

    // Handle image upload
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];

        // Check if a file is selected
        if ($file['size'] > 0) {
            // Specify the directory to store the uploaded image
            $uploadDirectory = '../static/images/';

            // Generate a unique name for the image
            $imageName = uniqid() . '_' . basename($file['name']);
            $newFilePath = $uploadDirectory . $imageName;

            // Move the uploaded image to the desired directory
            if (moveUploadedFile($file['tmp_name'], $newFilePath)) {
                $imageUrl = $newFilePath;
            }
        }
    }

    // Create a new PDO connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("UPDATE patient SET firstName = ?, lastName = ?, gender = ?, location = ?, emailAddress = ?, phoneNumber = ?, imageUrl = ? WHERE patientId = ?");

        // Bind the parameters with values
        $stmt->bindParam(1, $firstName);
        $stmt->bindParam(2, $lastName);
        $stmt->bindParam(3, $gender);
        $stmt->bindParam(4, $location);
        $stmt->bindParam(5, $emailAddress);
        $stmt->bindParam(6, $phoneNumber);
        $stmt->bindParam(7, $imageUrl);
        $stmt->bindParam(8, $patientId);

        // Execute the statement
        $stmt->execute();

        // Display success message
        echo "Patient profile updated successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the patient data from the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM patient WHERE patientId = ?");

    // Bind the patientId parameter with the value
    $stmt->bindParam(1, $patientId);

    // Execute the statement
    $stmt->execute();

    // Fetch the patient data
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Edit Patient Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <h2 class="text-center">Edit Patient Profile</h2>
    <div class="custom-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?patientId=' . $patientId); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control" value="<?php echo $patientData['firstName']; ?>" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control" value="<?php echo $patientData['lastName']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($patientData['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($patientData['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($patientData['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" name="location" id="location" class="form-control" value="<?php echo $patientData['location']; ?>" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control" value="<?php echo $patientData['emailAddress']; ?>" required>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control" value="<?php echo $patientData['phoneNumber']; ?>" required>
            </div>

            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image" class="form-control-file">
            </div>

            <input type="submit" value="Update" class="btn btn-primary">
        </form>
    </div>
<?php echo $footer; ?>
</body>

</html>
