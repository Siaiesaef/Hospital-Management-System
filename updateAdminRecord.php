<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Admin Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["updateAdmin"])){

        updateAdmin(
            $_POST["staffID"],
            $_POST["genderID"],
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
            <h2 class="centered-header">Update <?php echo $staff[0]['sFirstName'] . ' ' . $staff[0]['sLastName']; ?>'s Record</h2>
        </div>
        <form method="post">

            <input type="hidden" name="staffID" value=" <?php echo $staff[0]['staffID']; ?>">

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

            <label for="sFirstName">First Name:</label>
            <input type="text" id="sFirstName" name="sFirstName" value="<?php echo $staff[0]['sFirstName']; ?>">

            <label for="sMiddleName">Middle Name:</label>
            <input type="text" id="sMiddleName" name="sMiddleName" value="<?php echo $staff[0]['sMiddleName'];?>">

            <label for="sLastName">Last Name:</label>
            <input type="text" id="sLastName" name="sLastName" value="<?php echo $staff[0]['sLastName'];?>"><br />

            <label for="sDOB">DOB:</label></br />
            <input type="date" id="sDOB" name="sDOB" value="<?php echo $staff[0]['sDOB'] ?>"><br />


            <input type="submit" name="updateAdmin" value="Update Admin Record">
        </form>

    </div>
</body>
</html>