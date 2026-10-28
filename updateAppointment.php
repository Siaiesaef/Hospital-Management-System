<?php
require_once("db_connect.php");
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Update Appointment Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Appointment</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    include("doctorNavBar.php");

    function updateTestResultForUpdate($testResultsID, $text, $conn){
        $stmt=$conn->prepare("UPDATE
            TestResults
            SET testResults=?
            WHERE testResultsID=?
        ");
        $stmt->bind_param("si", $text, $testResultsID);
        $stmt->execute();
    }

    if (isset($_POST["updateAppointment"])){

        updateAppointment(
            $_POST["appointmentID"],
            $_POST["patientID"],
            $_POST["staffID"],
            $_POST["testResultsID"],
            $_POST["aDate"],
            $_POST["aTime"],
            $conn
        );
        if (!empty($_POST["testResultsID"])){
            updateTestResultForUpdate(
                $_POST['testResultsID'],
                $_POST['testResults'],
                $conn
            );
        }
    }

    $appointmentid=$_GET["appointmentID"] ?? $_POST["appointmentID"] ?? null;
    $appointment=viewAppointment($appointmentid, $conn);
    // var_dump($appointmentid);
    // var_dump($appointment);

    // if(!$appointment || count($appointment)===0){
    //     echo "Error: Appointment not found.";
    //     exit();
    // }
    ?>
     <div class="main" role="main">
        <div>
            <h2 class="centered-header">Update Appointment</h2>
        </div>
        <form method="post">

            <input type="hidden" name="appointmentID" value="<?php echo $appointment[0]['appointmentID']; ?>">

            <label>Patient:</label>
            <?php smartDropDownPatientIDForUpdate($conn, $appointment[0]['patientID']); ?>

            <label>Doctor:</label>
            <?php smartDropDownDoctorIDForUpdate($conn, $appointment[0]['staffID']); ?>

            <?php 
            if (!empty($appointment[0]['testResultsID'])): ?>
                <label for="testResults">Test Results:</label>
                <input type="text" id="testResults" name="testResults" value="<?php echo $appointment[0]['testResults']; ?>">
                <input type="hidden" name="testResultsID" value="<?php echo $appointment[0]['testResultsID']; ?>">
            <?php else: ?>
                <label>Select Test Result:</label>
                <?php smartDropDownTestResultsIDForUpdate( $conn, $appointment[0]['testResultsID']); ?>
            <?php endif; ?>


            <label for="aDate">Appointment date:</label>
            <input type="date" id="aDate" name="aDate" value="<?php echo $appointment[0]['aDate']; ?>">

            <label for="aTime">Appointment Time:</label>
            <input type="time" id="aTime" name="aTime" value="<?php echo $appointment[0]['aTime'];?>">

            <input type="submit" name="updateAppointment" value="Update Appointment">
        </form>

    </div>
</body>
</html>
<script>
    $(document).ready(function(){
        $('#patientDropDown').select2({
            placeholder: "Select Patient",
            allowClear: true
        });

        $('#doctorDropDown').select2({
            placeholder: "Select Doctor",
            allowClear: true
        });

        $('#testResultsDropDown').select2({
            placeholder: "Select Test Results",
            allowClear: true
        });
    });
</script>