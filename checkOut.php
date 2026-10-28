<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Check Out Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Out</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");

    $checkOut=null;

    if (isset($_POST["checkOut"])){

        checkOut(
            $_POST["appointmentID"],
            $checkOut,
            $conn
        );
    }

    $appointmentid=$_GET["appointmentID"];
    $appointment=viewAppointment($appointmentid, $conn);

    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Check Out</h2>
        </div>
        <form method="post">
            <input type="hidden" name="appointmentID" value="<?php echo $appointment[0]['appointmentID'];?>">

            <label for="patientID">Patient:</label>
            <input type="text" value="<?php echo $appointment[0]['PatientName']; ?>" readonly>
            <input type="hidden" name="patientID" value="<?php echo $appointment[0]['patientID']; ?>">

            <label for="testResultsID">Test Results:</label>
            <input type="text" value="<?php echo $appointment[0]['testResults']; ?>" readonly>
            <input type="hidden" name="testResultsID" value=" <?php echo $appointment[0]['testResultsID']; ?> ">

            <label for="aDate">Date:</label>
            <input type="date" name="aDate" value="<?php echo $appointment[0]['aDate'];?>" readonly>

            <label for="aTime">Time:</label>
            <input type="time" name="aTime" value="<?php echo $appointment[0]['aTime'];?>" readonly>

            <label for="aCheckedInTime">Checked in time:</label>
            <input type="text" name="aCheckedInTime" value="<?php echo $appointment[0]['aCheckedInTime'] ?>" readonly>

            <input type="submit" name="checkOut" value="Check Out">
        </form>

    </div>
</body>
</html>