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

    if (isset($_POST["deletePatient"])){

        deletePatient(
            $_POST["patientID"],
            $conn
        );
    }

    $patientid=$_GET["patientID"];
    $patient=viewPatient($patientid, $conn);
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete <?php echo $patient[0]['pLastName'] . ' ' . $patient[0]['pLastName']; ?>'s Record</h2>
        </div>
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="patientID" value="<?php echo $patient[0]['patientID'];?>">

            <label for="gender">Gender:</label>
            <input type="text" id="gender" name="gender" value="<?php echo $patient[0]['gGender']; ?>"readonly>
            <input type="hidden" name="genderID" value="<?php echo $patient[0]['genderID']; ?>">

            <label for="pFirstName">First Name:</label>
            <input type="text" id="pFirstName" name="pFirstName" value="<?php echo $patient[0]['pFirstName'];?>" readonly>

            <label for="pMiddleName">Middle Name:</label>
            <input type="text" id="pMiddleName" name="pMiddleName" value="<?php echo $patient[0]['pMiddleName'];?>" readonly>

            <label for="pLastName">Last Name:</label>
            <input type="text" id="pLastName" name="pLastName" value="<?php echo $patient[0]['pLastName'];?>" readonly>

            <label for="pDOB">DOB:</label>
            <input type="date" id="pDOB" name="pDOB" value="<?php echo $patient[0]['pDOB'] ?>" readonly>

            <label for="pAddress">Address:</label>
            <input type="text" id="pAddress" name="pAddress" value="<?php echo $patient[0]['pAddress'];?>" readonly>

            <label for="pCity">City:</label>
            <input type="text" id="pCity" name="pCity" value="<?php echo $patient[0]['pCity'];?>" readonly>

            <label for="pPostcode">Postcode:</label>
            <input type="text" id="pPostcode" name="pPostcode" value="<?php echo $patient[0]['pPostcode'];?>" readonly>

            <input type="submit" name="deletePatient" value="Delete Patient">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>