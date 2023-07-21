<?php
session_start();
require_once '../credentials.php';

// Check if administrator is logged in
if ($_SESSION['role'] !== 'administrator') {
    header("Location: ../auth/login.php");
    exit;
}

// Check if doctorId is provided in the URL
if (!isset($_GET['doctorId'])) {
    header("Location: doctor_profile.php"); // Redirect to the doctor profile page or display an error message
    exit;
}

// Get the doctorId from the URL
$doctorId = $_GET['doctorId'];

// Function to sanitize user input
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to encrypt the password using password_hash()
function encryptPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
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
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $hospital = sanitizeInput($_POST['hospital']);
    $emailAddress = sanitizeInput($_POST['emailAddress']);
    $specialization = sanitizeInput($_POST['specialization']);
    $SSN = sanitizeInput($_POST['SSN']);
    $password = sanitizeInput($_POST['password']);
    $imageUrl = '';

    // Handle image upload
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];

        // Check if a file is selected
        if ($file['size'] > 0) {
            // Specify the directory to store the uploaded image
            $uploadDirectory = '../static/images/';

            // Generate a unique name for the image
            $imageName = 'doctor_' . $doctorId . '_' . basename($file['name']);
            $newFilePath = $uploadDirectory . $imageName;

            // Move the uploaded image to the desired directory
            if (moveUploadedFile($file['tmp_name'], $newFilePath)) {
                $imageUrl = $imageName;
            }
        }
    }

    // Create a new PDO connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("UPDATE doctor SET firstName = ?, lastName = ?, gender = ?, phoneNumber = ?, hospital = ?, emailAddress = ?, specialization = ?, SSN = ?, imageUrl = ? WHERE doctorId = ?");

        // Bind the parameters with values
        $stmt->bindParam(1, $firstName);
        $stmt->bindParam(2, $lastName);
        $stmt->bindParam(3, $gender);
        $stmt->bindParam(4, $phoneNumber);
        $stmt->bindParam(5, $hospital);
        $stmt->bindParam(6, $emailAddress);
        $stmt->bindParam(7, $specialization);
        $stmt->bindParam(8, $SSN);
        $stmt->bindParam(9, $imageUrl);
        $stmt->bindParam(10, $doctorId);

        // Execute the statement
        $stmt->execute();

        // Display success message
        echo "Doctor profile updated successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the doctor data from the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM doctor WHERE doctorId = ?");

    // Bind the doctorId parameter with the value
    $stmt->bindParam(1, $doctorId);

    // Execute the statement
    $stmt->execute();

    // Fetch the doctor data
    $doctorData = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Edit Doctor Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
    <h2 class="text-center">Edit Doctor Profile</h2>
    <div class="custom-container">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?doctorId=' . $doctorId); ?>"
            enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" name="firstName" id="firstName" class="form-control"
                    value="<?php echo $doctorData['firstName']; ?>" required>
            </div>

            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" name="lastName" id="lastName" class="form-control"
                    value="<?php echo $doctorData['lastName']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($doctorData['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($doctorData['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($doctorData['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" name="phoneNumber" id="phoneNumber" class="form-control"
                    value="<?php echo $doctorData['phoneNumber']; ?>" required>
            </div>

            <div class="form-group">
                <label for="hospital">Hospital:</label>
                <input type="text" name="hospital" id="hospital" class="form-control"
                    value="<?php echo $doctorData['hospital']; ?>" required>
            </div>

            <div class="form-group">
                <label for="emailAddress">Email Address:</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control"
                    value="<?php echo $doctorData['emailAddress']; ?>" required>
            </div>

            <div class="form-group">
                <label for="specialization">Specialization:</label>
                <input type="text" name="specialization" id="specialization" class="form-control"
                    value="<?php echo $doctorData['specialization']; ?>" required>
            </div>

            <div class="form-group">
                <label for="SSN">Social Security Number:</label>
                <input type="text" name="SSN" id="SSN" class="form-control" value="<?php echo $doctorData['SSN']; ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image" accept="image/*">
            </div>

            <input type="submit" value="Update" class="btn btn-primary">
        </form>
<?php echo $footer; ?>
</div>
</body>

</html>
