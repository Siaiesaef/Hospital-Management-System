<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Delete Appointment Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Appointment</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    if (isset($_POST["deleteAppointment"])){

        deleteAppointment(
            $_POST["appointmentID"],
            $conn
        );
    }

    $appointmentid=$_GET["appointmentID"];
    $appointment=viewAppointment($appointmentid, $conn);

    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Delete Appointment</h2>
        </div>
        <form method="post" onsubmit="return confirmDelete()">
            <input type="hidden" name="appointmentID" value="<?php echo $appointment[0]['appointmentID'];?>">

            <label for="patientID">Patient:</label>
            <input type="text" id="patientID" value="<?php echo $appointment[0]['PatientName']; ?>" readonly>
            <input type="hidden" name="patientID" value=" <?php echo $appointment[0]['patientID']; ?> ">

            <label for="staffID">Staff:</label>
            <input type="text" id="staffID" value="<?php echo $appointment[0]['StaffName']; ?>" readonly>
            <input type="hidden" name="staffID" value=" <?php echo $appointment[0]['staffID']; ?> ">

            <label for="testResultsID">Test Results:</label>
            <input type="text" id="testResultsID" value="<?php echo $appointment[0]['testResults']; ?>" readonly>
            <input type="hidden" name="testResultsID" value=" <?php echo $appointment[0]['testResultsID']; ?> ">

            <label for="aDate">Date:</label>
            <input type="date" id="aDate" name="aDate" value="<?php echo $appointment[0]['aDate'];?>" readonly>

            <label for="aTime">Time:</label>
            <input type="time" id="aTime" name="aTime" value="<?php echo $appointment[0]['aTime'];?>" readonly>

            <input type="submit" name="deleteAppointment" value="Delete Appointment">
        </form>

    </div>
<script>

    function confirmDelete(){
        return confirm("Are you sure you want to delete this record?");
    }

</script>

</body>
</html>