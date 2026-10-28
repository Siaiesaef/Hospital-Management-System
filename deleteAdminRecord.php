<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Delete Admin Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Admin</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["deleteAdmin"])){

        deleteAdmin(
            $_POST["staffID"],
            $conn
        );
    }

    $staffid=$_GET["staffID"];
    $staff=viewStaff($staffid, $conn);

    $genderResult=$conn->query("SELECT genderID,gGender FROM Gender")
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete <?php echo $staff[0]['sFirstName'] . " ". $staff[0]['sLastName']; ?>'s Record</h2>
        </div>
        <!-- refernce: https://www.w3schools.com/jsref/event_onsubmit.asp -->
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="staffID" value="<?php echo $staff[0]['staffID'];?>">

            <label for="genderID">Gender:</label>
            <input type="text" id="genderID" value="<?php echo $staff[0]['gGender']; ?>" readonly>
            <input type="hidden" name="genderID" value=" <?php echo $staff[0]['genderID']; ?> ">

            <label for="sFirstName">First Name:</label>
            <input type="text" id="sFirstName" name="sFirstName" value="<?php echo $staff[0]['sFirstName'];?>" readonly>

            <label for="sMiddleName">Middle Name:</label>
            <input type="text" id="sMiddleName" name="sMiddleName" value="<?php echo $staff[0]['sMiddleName'];?>" readonly>

            <label for="sLastName">Last Name:</label>
            <input type="text" id="sLastName" name="sLastName" value="<?php echo $staff[0]['sLastName'];?>" readonly>

            <label for="sDOB">DOB:</label>
            <input type="text" id="sDOB" name="sDOB" value="<?php echo $staff[0]['sDOB'];?>" readonly>

            <input type="submit" name="deleteAdmin" value="Delete Admin Record">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>