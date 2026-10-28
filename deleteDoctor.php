<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Delete Doctor Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Doctor</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["deleteDoctor"])){

        deleteDoctor(
            $_POST["staffID"],
            $conn
        );
    }

    $staffid=$_GET["staffID"];
    $staff=viewStaff($staffid, $conn);
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete Dr. <?php echo $staff[0]['sLastName']; ?>'s Record</h2>
        </div>
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="staffID" value="<?php echo $staff[0]['staffID'];?>">

            <label for="gender">Gender:</label>
            <input type="text" id="gender" name="gender" value="<?php echo $staff[0]['gGender']; ?>"readonly>
            <input type="hidden" name="genderID" value="<?php echo $staff[0]['genderID']; ?>">

            <label for="Speciality">Doctor Speciality:</label>
            <input type="text" id="Speciality" name="Speciality" value="<?php echo $staff[0]['stSpeciality']; ?>"readonly>
            <input type="hidden" name="staffTypeID" value="<?php echo $staff[0]['staffTypeID']; ?>">

            <label for="sFirstName">First Name:</label>
            <input type="text" id="sFirstName" name="sFirstName" value="<?php echo $staff[0]['sFirstName'];?>" readonly>

            <label for="sMiddleName">Middle Name:</label>
            <input type="text" id="sMiddleName" name="sMiddleName" value="<?php echo $staff[0]['sMiddleName'];?>" readonly>

            <label for="sLastName">Last Name:</label>
            <input type="text" id="sLastName" name="sLastName" value="<?php echo $staff[0]['sLastName'];?>" readonly>

            <label for="sDOB">DOB:</label>
            <input type="date" id="sDOB" name="sDOB" value="<?php echo $staff[0]['sDOB'] ?>" readonly>

            <input type="submit" name="deleteDoctor" value="Delete Doctor">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>