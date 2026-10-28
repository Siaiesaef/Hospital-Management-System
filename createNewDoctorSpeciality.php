<?php 
include("functions.php");
startSessionIfNotStarted();
include_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Create New Doctor Speciality page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create New Doctor Speciality</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST['CreateDoctorSpeciality'])){
        addDoctorSpeciality($_POST['specialityName'],
            $conn
        );
        header('Location:viewDoctorSpecialities.php');
        exit();
    }
    ?>
    <div class="main" role="main">
        <h2 class="centered-header">Create New Doctor Speciality</h2>
    <!-- <div class="main"> -->
        <form method="post">

            <label for="specialityName">Speciality Name: </label><br />
            <input type="text" name="specialityName" placeholder="Speciality Name" required /><br />

            <button type="submit" name="CreateDoctorSpeciality">Create Doctor Speciality</button>

        </form>
    </div>

</body>
</html>