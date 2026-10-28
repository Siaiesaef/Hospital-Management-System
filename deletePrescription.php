<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Delete Patient Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Patient</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["deletePrescription"])){

        deletePrescription(
            $_POST["prescriptionID"],
            $conn
        );
    }

    $prescriptionid=$_GET["prescriptionID"];
    $prescription=viewPrescription($prescriptionid, $conn);

    if (($prescription[0]['repeatPrescription'])==1){
        $repeatValue= "Yes";
    } else if(($prescription[0]['repeatPrescription'])==0){
        $repeatValue= "No";
    }
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete Prescription</h2>
        </div>
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="prescriptionID" value="<?php echo $prescription[0]['prescriptionID'];?>">

            <label for="StaffName">Staff Name:</label>
            <input type="text" id="StaffName" name="StaffName" value="<?php echo $prescription[0]['StaffName']; ?>"readonly>

            <label for="PatientName">Patient Name:</label>
            <input type="text" id="PatientName" name="PatientName" value="<?php echo $prescription[0]['PatientName'];?>" readonly>

            <label for="datePrescribed">Date Prescribed:</label>
            <input type="date" id="datePrescribed" name="datePrescribed" value="<?php echo $prescription[0]['datePrescribed'];?>" readonly>

            <label for="dateCollected">Date Collected:</label>
            <input type="date" id="dateCollected" name="dateCollected" value="<?php echo $prescription[0]['dateCollected'];?>" readonly>

            <label for="medication">Medication:</label>
            <input type="text" id="medication" name="medication" value="<?php echo $prescription[0]['medication'] ?>" readonly>

            <label for="dosage">Dosage:</label>
            <input type="text" id="dosage" name="dosage" value="<?php echo $prescription[0]['dosage'];?>" readonly>

            <label for="repeatPrescription">Allow Repeats:</label>
            <input type="text" id="repeatPrescription" name="repeatPrescription" value="<?php echo $repeatValue; ?>" readonly>

            <?php 
            if (!empty($prescription[0]['nextPickUp'])):
            ?>
                <label for="nextPickUp"> Next Pick-Up Date:</label>
                <input type="date" id="nextPickUp" name="nextPickUp" value="<?php echo $prescription[0]['nextPickUp'] ?>" readonly>
            <?php endif; ?>

            <input type="submit" name="deletePrescription" value="Delete Prescription">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>