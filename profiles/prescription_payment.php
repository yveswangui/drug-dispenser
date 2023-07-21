<?php
require_once '../credentials.php';

// Check if prescriptionId is provided in the URL
if (!isset($_GET['prescriptionId'])) {
    // Redirect or display an error message
    header("Location: prescriptions.php");
    exit;
}

// Get the prescriptionId from the URL
$prescriptionId = $_GET['prescriptionId'];

// Function to sanitize user input
function sanitizeInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Function to format currency
function formatCurrency($amount)
{
    return number_format($amount, 2);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $paymentDate = date('Y-m-d H:i:s');
    $amount = sanitizeInput($_POST['amount']);
    $method = sanitizeInput($_POST['method']);
    $description = sanitizeInput($_POST['description']);

    // Create a new PDO connection to the database
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement
        $stmt = $pdo->prepare("INSERT INTO payment (paymentDate, amount, method, description, prescriptionId) VALUES (?, ?, ?, ?, ?)");

        // Bind the parameters with values
        $stmt->bindParam(1, $paymentDate);
        $stmt->bindParam(2, $amount);
        $stmt->bindParam(3, $method);
        $stmt->bindParam(4, $description);
        $stmt->bindParam(5, $prescriptionId);

        // Execute the statement
        $stmt->execute();

        // Display success message or perform any other necessary actions
        echo "Payment made successfully.";

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Display error message
        echo "Error: " . $e->getMessage();
    }
}

// Fetch the prescription and payment data from the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the SQL statement to fetch prescription data
    $prescriptionStmt = $pdo->prepare("SELECT * FROM prescription WHERE prescriptionId = ?");

    // Bind the prescriptionId parameter with the value
    $prescriptionStmt->bindParam(1, $prescriptionId);

    // Execute the prescription statement
    $prescriptionStmt->execute();

    // Fetch the prescription data
    $prescriptionData = $prescriptionStmt->fetch(PDO::FETCH_ASSOC);

    // Get the patientId associated with the prescription
    $patientId = $prescriptionData['doctorPatientAssignmentId'];
    
    // Prepare the SQL statement to fetch patient data
    $patientStmt = $pdo->prepare("SELECT * FROM patient WHERE patientId = ?");
    
    // Bind the patientId parameter with the value
    $patientStmt->bindParam(1, $patientId);
    
    // Execute the patient statement
    $patientStmt->execute();
    
    // Fetch the patient data
    $patientData = $patientStmt->fetch(PDO::FETCH_ASSOC);

    // Prepare the SQL statement to fetch payment data
    $paymentStmt = $pdo->prepare("SELECT * FROM payment WHERE prescriptionId = ?");

    // Bind the prescriptionId parameter with the value
    $paymentStmt->bindParam(1, $prescriptionId);

    // Execute the payment statement
    $paymentStmt->execute();

    // Fetch all the payments associated with the prescription
    $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Make Payment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/payment.css">
</head>

<body>
    <h2 class="text-center">Make Payment</h2>
    <div class="container">
        <h4>Prescription Information</h4>
        <table class="table">
            <tbody>
                <tr>
                    <th>Prescription ID:</th>
                    <td><?php echo $prescriptionData['prescriptionId']; ?></td>
                </tr>
                <tr>
                    <th>Patient Name:</th>
                    <td><?php echo $patientData['firstName'] . ' ' . $patientData['lastName']; ?></td>
                </tr>
                <tr>
                    <th>Doctor:</th>
                    <td><?php echo $prescriptionData['doctorId']; ?></td>
                </tr>
                <tr>
                    <th>Pharmacy:</th>
                    <td><?php echo $prescriptionData['pharmacyId']; ?></td>
                </tr>
                <tr>
                    <th>Drug:</th>
                    <td><?php echo $prescriptionData['drugId']; ?></td>
                </tr>
                <tr>
                    <th>Start Date:</th>
                    <td><?php echo $prescriptionData['startDate']; ?></td>
                </tr>
                <tr>
                    <th>End Date:</th>
                    <td><?php echo $prescriptionData['endDate']; ?></td>
                </tr>
            </tbody>
        </table>

        <h4>Payments</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment) : ?>
                    <tr>
                        <td><?php echo $payment['paymentDate']; ?></td>
                        <td><?php echo formatCurrency($payment['amount']); ?></td>
                        <td><?php echo $payment['method']; ?></td>
                        <td><?php echo $payment['description']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Make a Payment</h4>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?prescriptionId=' . $prescriptionId); ?>">
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="text" name="amount" id="amount" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="method">Payment Method:</label>
                <input type="text" name="method" id="method" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" name="description" id="description" class="form-control" required>
            </div>

            <input type="submit" value="Submit" class="btn btn-primary">
        </form>
    </div>
</body>

</html>
