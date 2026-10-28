<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View My Patient Record Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Patient Record</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("patientNavBar.php");

    if (isset($_POST['repeatPrescription'])){

        repeatPrescription(
            $_POST['prescriptionID'],
            $conn
        );
        header("Location: viewMyMedications.php");
    }

    $prescriptionid=$_GET['prescriptionID'] ?? null;
    $prescription=viewPrescription($prescriptionid, $conn);
    ?>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">Repeat Prescription?</h1>
        </div>
        <form method="post">
            <input type="hidden" name="prescriptionID" value="<?php echo $prescription[0]['prescriptionID']; ?>">

            <label for="PatientName">Patient:</label>
            <input type="text" id="PatientName" name="PatientName" value="<?php echo $prescription[0]['PatientName']; ?>" readonly>
            <input type="hidden" name="patientID" value="<?php echo $prescription[0]['patientID']; ?>">

            <label for="StaffName">Doctor:</label>
            <input type="text" id="StaffName" name="StaffName" value="<?php echo $prescription[0]['StaffName']; ?>" readonly>
            <input type="hidden" name="staffID" value="<?php echo $prescription[0]['staffID']; ?>" readonly>

            <label for="datePrescribed">Date Prescribed:</label>
            <input type="date" id="datePrescribed" name="datePrescribed" value="<?php echo $prescription[0]['datePrescribed']; ?>" readonly>

            <label for="medication">Medication:</label>
            <input type="text" id="medication" name="medication" value="<?php echo $prescription[0]['medication']; ?>" readonly>

            <input type="submit" name="repeatPrescription" value="Repeat Prescription">
        </form>
    </div>
</body>
</html>