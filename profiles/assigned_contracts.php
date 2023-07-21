<?php
session_start();
require_once '../credentials.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Assigned Contracts | <?php echo $_SESSION['name']; ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel = "stylesheet" href = "styles/pagination.css">
<link rel = "stylesheet" href = "styles/styles.css">
</head>

<body>
    <?php include '../header_footer.php'; echo $navigation_bar;?>
<div class = "container">
    <h2>Assigned Contracts</h2>
    <table class="table table-striped table-hover">
        <thead class="thead">
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Pharmacy/Pharmaceutical</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Create a new PDO connection to the database
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $dbPassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $contracts = [];

                if (isset($_GET['pharmacyId'])) {
                    // Query contracts assigned to the pharmacy
                    $stmt = $pdo->prepare("SELECT contract.*, pharmaceutical.name AS pharmaceuticalName FROM contract
                        INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
                        WHERE contract.pharmacyId = ?
                        ORDER BY contract.contractId DESC");
                    $stmt->execute([$_GET['pharmacyId']]);
                    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } elseif (isset($_GET['pharmaceuticalId'])) {
                    // Query contracts assigned to the pharmaceutical
                    $stmt = $pdo->prepare("SELECT contract.*, pharmacy.name AS pharmacyName FROM contract
                        INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
                        WHERE contract.pharmaceuticalId = ?
                        ORDER BY contract.contractId DESC");
                    $stmt->execute([$_GET['pharmaceuticalId']]);
                    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                foreach ($contracts as $contract) {
                    echo '<tr>';
                    echo '<td><a href="contract_profile.php?contractId=' . $contract['contractId'] . '">' . $contract['title'] . '</a></td>';
                    echo '<td class = "date">' . $contract['startDate'] . '</td>';
                    echo '<td class = "date">' . $contract['endDate'] . '</td>';
                    echo '<td>';

                    if (isset($_GET['pharmacyId'])) {
                        echo $contract['pharmaceuticalName'];
                    } elseif (isset($_GET['pharmaceuticalId'])) {
                        echo $contract['pharmacyName'];
                    }

                    echo '</td>';
                    echo '</tr>';
                }
            } catch (PDOException $e) {
                die("Error connecting to the database: " . $e->getMessage());
            }
            ?>
        </tbody>
    </table>

    <p>
        <?php
        if (isset($_GET['pharmacyId'])) {
            echo '<a href="pharmacy_profile.php?pharmacyId=' . $_GET['pharmacyId'] . '">Back to Pharmacy Profile</a>';
        } elseif (isset($_GET['pharmaceuticalId'])) {
            echo '<a href="pharmaceutical_profile.php?pharmaceuticalId=' . $_GET['pharmaceuticalId'] . '">Back to Pharmaceutical Profile</a>';
        }
        ?>
    </p>
</div>
<?php echo $footer; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".date").each(function() {
                var date = $(this).text();
                var formattedDate = moment(date).format('ddd D MMMM, YYYY');
                $(this).text(formattedDate);
            });
        });
    </script>
</body>

</html>
