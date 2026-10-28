<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Patient Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["updatePatient"])){

        updatePatient(
            $_POST["patientID"],
            $_POST["genderID"],
            $_POST["pFirstName"],
            $_POST["pMiddleName"],
            $_POST["pLastName"],
            $_POST["pDOB"],
            $_POST["pAddress"],
            $_POST["pCity"],
            $_POST["pPostcode"],
            $conn
        );
    }

    $patientid=$_GET["patientID"];
    $patient=viewPatient($patientid, $conn);

    $genderResult=$conn->query("SELECT genderID,gGender FROM Gender");
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Update <?php echo $patient[0]['pLastName'] . ' ' . $patient[0]['pLastName']; ?>'s Record</h2>
        </div>
        <form method="post">
            <input type="hidden" name="patientID" value="<?php echo $patient[0]['patientID'];?>">

            <div class="no-select-2">
                <label for="genderID">Gender:</label>
                <select id="genderID" name="genderID" required>
                    <?php 
                    while($row=$genderResult->fetch_assoc()){
                        $selected="";
                        if($row['genderID']==$staff[0]['genderID']){
                            $selected="selected";
                        }
                        echo "<option value='".$row['genderID']."' $selected>
                                ".$row['gGender']."
                            </option>";
                    }
                    ?>
                </select>
            </div>

            <label for="pFirstName">First Name:</label>
            <input type="text" id="pFirstName" name="pFirstName" value="<?php echo $patient[0]['pFirstName'];?>">

            <label for="pMiddleName">Middle Name:</label>
            <input type="text" id="pMiddleName" name="pMiddleName" value="<?php echo $patient[0]['pMiddleName'];?>">

            <label for="pLastName">Last Name:</label>
            <input type="text" id="pLastName" name="pLastName" value="<?php echo $patient[0]['pLastName'];?>">

            <label for="pDOB">DOB:</label>
            <input type="date" id="pDOB" name="pDOB" value="<?php echo $patient[0]['pDOB'] ?>">

            <label for="pAddress">Address:</label>
            <input type="text" id="pAddress" name="pAddress" value="<?php echo $patient[0]['pAddress'];?>">

            <label for="pCity">City:</label>
            <input type="text" id="pCity" name="pCity" value="<?php echo $patient[0]['pCity'];?>">

            <label for="pPostcode">Postcode:</label>
            <input type="text" id="pPostcode" name="pPostcode" value="<?php echo $patient[0]['pPostcode'];?>">

            <input type="submit" name="updatePatient" value="Update Patient">
        </form>

    </div>
</body>
</html>
