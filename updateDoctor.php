<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Doctor Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Doctor</title>
    <link rel="stylesheet" href="style.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["updateDoctor"])){

        updateDoctor(
            $_POST["staffID"],
            $_POST["genderID"],
            $_POST["staffTypeID"],
            $_POST["sFirstName"],
            $_POST["sMiddleName"],
            $_POST["sLastName"],
            $_POST["sDOB"],
            $conn
        );
    }

    $staffid=$_GET["staffID"];
    $staff=viewStaff($staffid, $conn);

    $genderResult=$conn->query("SELECT genderID,gGender FROM Gender");
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Update Dr. <?php echo $staff[0]['sLastName']; ?>'s Record</h2>
        </div>
        <form method="post">
            <input type="hidden" name="staffID" value="<?php echo $staff[0]['staffID'];?>">

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

            <label>Doctor Speciality:</label>
            <?php smartDropDownSpecialityIDForUpdate($conn, $staff[0]['staffTypeID']); ?>

            <label for="sFirstName">First Name:</label>
            <input type="text" id="sFirstName" name="sFirstName" value="<?php echo $staff[0]['sFirstName'];?>">

            <label for="sMiddleName">Middle Name:</label>
            <input type="text" id="sMiddleName" name="sMiddleName" value="<?php echo $staff[0]['sMiddleName'];?>">

            <label for="sLastName">Last Name:</label>
            <input type="text" id="sLastName" name="sLastName" value="<?php echo $staff[0]['sLastName'];?>">

            <label for="sDOB">DOB:</label>
            <input type="date" id="sDOB" name="sDOB" value="<?php echo $staff[0]['sDOB'] ?>">

            <input type="submit" name="updateDoctor" value="Update Doctor">
        </form>

    </div>
</body>
</html>
<script>
    $(document).ready(function(){
        $('#specialityDropDown').select2({
            placeholder: "Select Speciality",
            allowClear: true
        });
    });
</script>
