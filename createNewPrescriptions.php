<?php 
include("functions.php");
startSessionIfNotStarted();
$roleid=$_SESSION['role'];

include_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Prescribe Medication page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Prescribe Medication</title>
    <link rel="stylesheet" href="style.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    if ($roleid==3){
        include("adminNavBar.php");
    } else{
        include("doctorNavBar.php");
    }

    if (isset($_POST['PrescribeMedication'])){
        addPrescription($_POST['patientID'],
            $_POST['staffID'], 
            $_POST['datePrescribed'],
            $_POST['dateCollected'] ?? null,
            $_POST['medication'],
            $_POST['dosage'],
            $_POST['repeatPrescription'],
            $conn
        );
        header('Location: viewPrescriptions.php');
        exit();
    }
    ?>
    <div class="main" role="main">
        <h2 class="centered-header">Prescribe Medication</h2>
    <!-- <div class="main"> -->
        <form method="post">

            <label for="staffID">Select the Doctor: </label><br />
            <?php smartDropDownDoctorID($conn) ?><br />

            <label for="patientID">Select a Patient: </label><br />
            <?php smartDropDownPatientID($conn) ?><br />

            <label for="datePrescribed">Date Prescribed: </label><br />
            <input type="date" name="datePrescribed" placeholder="Date Prescribed" required /><br />

            <label for="dateCollected">Date Collected: </label><br />
            <input type="date" name="dateCollected" placeholder="Date Collected" /><br />

            <label for="medication">Medication: </label><br />
            <input type="text" name="medication" placeholder="Medication" required /><br />

            <label for="dosage">Dosage: </label><br />
            <input type="text" name="dosage" placeholder="Dosage" required /><br />

            <div class="no-select-2">
                <label for="repeatPrescription">Allow Repeats:</label><br />
                <select id="repeatPrescription" name="repeatPrescription" required>
                    <option value="">Select if repeatable</option>
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select><br />
            </div>

            <button type="submit" name="PrescribeMedication">Prescribe Medication</button>

        </form>
    </div>

<script>
$(document).ready(function(){
    $('#patientDropDown').select2({
        width: '100%',
        dropDownAutoWidth: true,
        placeholder: "Search for a patient",
        allowClear: true
    });
});

$(document).ready(function(){
    $('#doctorDropDown').select2({
        width: '100%',
        placeholder: "Search for a doctor",
        allowClear: true
    });
});
</script>
</body>
</html>