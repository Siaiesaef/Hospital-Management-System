<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Doctor Specialities Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Doctor Specialities</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["updateSpeciality"])){

        updateDoctorSpeciality(
            $_POST["speciality"],
            $_POST["staffTypeID"],
            $conn
        );
    }

    $stafftypeid=$_GET["staffTypeID"];
    $stafftype=viewStaffType($stafftypeid, $conn);
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Update <?php echo $stafftype[0]['stSpeciality']; ?></h2>
        </div>
        <form method="post">
            <!-- <label for="staffTypeID">ID:</label><br /> -->
            <input type="hidden" name="staffTypeID" value="<?php echo $stafftype[0]['staffTypeID'];?>">

            <label for="speciality">Doctor Speciality:</label>
            <input type="text" id="speciality" name="speciality" value="<?php echo $stafftype[0]['stSpeciality'];?>">

            <input type="submit" name="updateSpeciality" value="Update Doctor Speciality">
        </form>

    </div>
</body>
</html>
