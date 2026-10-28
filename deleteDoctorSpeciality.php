<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Delete Doctor Specialities Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Doctor Specialities</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["deleteSpeciality"])){

        deleteDoctorSpeciality(
            $_POST["staffTypeID"],
            $conn
        );
    }

    $stafftypeid=$_GET["staffTypeID"];
    $stafftype=viewStaffType($stafftypeid, $conn);
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete <?php echo $stafftype[0]['stSpeciality']; ?></h2>
        </div>
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="staffTypeID" value="<?php echo $stafftype[0]['staffTypeID'];?>">

            <label for="speciality">Doctor Speciality:</label>
            <input type="text" id="speciality" name="speciality" value="<?php echo $stafftype[0]['stSpeciality'];?>" readonly>

            <input type="submit" name="deleteSpeciality" value="Delete Doctor Speciality">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>