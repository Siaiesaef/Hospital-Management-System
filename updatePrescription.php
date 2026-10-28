<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Prescription Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Prescription</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    include("doctorNavBar.php");

    if (isset($_POST["updatePrescription"])){

        updatePrescription(
            $_POST["staffID"],
            $_POST["patientID"],
            $_POST["datePrescribed"],
            $_POST["dateCollected"],
            $_POST["medication"],
            $_POST["repeatPrescription"],
            $_POST["nextPickUp"],
            $_POST["prescriptionID"],
            $conn
        );
    }

    $prescriptionid=$_GET["prescriptionID"] ?? $_POST["prescriptionID"] ?? null;
    $prescription=viewPrescription($prescriptionid, $conn);
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Update Prescription</h2>
        </div>
        <form method="post">

            <input type="hidden" name="prescriptionID" value="<?php echo $prescription[0]['prescriptionID']; ?>">

            <label>Patient:</label>
            <?php smartDropDownPatientIDForUpdate($conn, $prescription[0]['patientID']); ?>

            <label>Doctor:</label>
            <?php smartDropDownDoctorIDForUpdate($conn, $prescription[0]['staffID']); ?>

            <label for="datePrescribed">Date Prescribed: </label><br />
            <input type="date" id="datePrescribed" name="datePrescribed" value="<?php echo $prescription[0]['datePrescribed']; ?>">

            <label for="dateCollected">Date Collected: </label><br />
            <input type="date" id="dateCollected" name="dateCollected" value="<?php echo $prescription[0]['dateCollected']; ?>">

            <label for="medication">Medication: </label><br />
            <input type="text" id="medication" name="medication" value="<?php echo $prescription[0]['medication']; ?>">

            <label for="dosage">Dosage: </label><br />
            <input type="text" id="dosage" name="dosage" value="<?php echo $prescription[0]['dosage'] ?>">

            <div class="no-select-2">
                <label for="repeatPrescription">Allow Repeats:</label><br />
                <select id="repeatPrescription" name="repeatPrescription" required>
                    <option value="1"
                        <?php if($prescription[0]['repeatPrescription']==1) echo "selected"; ?>>
                        Yes
                    </option>

                    <option value="0"
                        <?php if($prescription[0]['repeatPrescription']==0) echo "selected"; ?>>
                        No 
                    </option>

                </select><br />
            </div>

            <?php 
            if (!empty($prescription[0]['nextPickUp'])):
            ?>
                <label for="nextPickUp"> Next Pick-Up Date:</label>
                <input type="date" id="nextPickUp" name="nextPickUp" value="<?php echo $prescription[0]['nextPickUp'] ?>">
            <?php endif; ?>

            <input type="submit" name="updatePrescription" value="Update Prescription">
        </form>

    </div>
</body>
</html>
<script>
    $(document).ready(function(){
        $('#patientDropDown').select2({
            placeholder: "Select Patient",
            allowClear: true
        });

        $('#doctorDropDown').select2({
            placeholder: "Select Doctor",
            allowClear: true
        });
    });
</script>