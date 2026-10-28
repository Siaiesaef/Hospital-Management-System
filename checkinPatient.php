<?php 
include("functions.php");
startSessionIfNotStarted();
require_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Check in Patient page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Check in Patient</title>
    <link rel="stylesheet" href="style.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST['checkIn'])){
        $appointmentID=$_POST['appointmentID'];

        $stmt=$conn->prepare("UPDATE
            Appointment
            SET aCheckedInTime = NOW()
            WHERE appointmentID=?
        ");

        $stmt->bind_param("i", $appointmentID);

        if($stmt->execute()){
            echo "<script>
                    alert('Patient checked in successfully!');
                    window.location.href='ViewCheckedinPatients.php';
                </script>";
        
        } else{
            echo "<script> alert('Error Checking in! $conn->lastErrorMsg()'); </script>";
        }
    }

    ?>

    <div class="main" role="main">
        <h1 class="centered-header">Check In Patient</h1>
        <form method="post">
            <label>Select the Patient:</label>
            <?php smartDropDownPatientID($conn); ?>

            <label>Select Corresponding Appointment:</label>
            <?php smartDropDownAppointmentsToCheckIn($conn); ?>

            <input type="submit" name="checkIn" value="Check In Patient">

        </form>
    </div>

<script>
$(document).ready(function(){
    $('#patientDropDown').select2({
        placeholder: "Search for a patient",
        allowClear: true
    });

    $('#appointmentDropDown').select2({
        placeholder: "Search for an appointment",
        allowClear: true
    });
});
</script>  
</body>
</html>