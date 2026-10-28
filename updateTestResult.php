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

        updateTestResults(
            $_POST["appointmentID"],
            $_POST["patientID"],
            $_POST["staffID"],
            $_POST["testResultsID"],
            $_POST["aDate"],
            $_POST["aTime"],
            $conn
        );
        if (!empty($_POST["appointmentID"])){
            linkTestResultToAppointment(
                $_POST['testResultsID'],
                $_POST['appointmentID'],
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
            <h2 class="centered-header">Update Test Result</h2>
        </div>
        <form method="post">

            <input type="hidden" name="testResultsID" value="<?php echo $testResults[0]['testResultsID']; ?>">

            <label>Patient:</label>
            <?php smartDropDownPatientIDForUpdate($conn, $testResults[0]['patientID']); ?>

            <label>Doctor:</label>
            <?php smartDropDownDoctorIDForUpdate($conn, $testResults[0]['staffID']); ?>

            <label>Change appointment:</label>
            <?php smartDropDownAppointmentIDForUpdate($conn, $testResult[0]['appointmentID']) ?>

            <label for="testResults">Test Result:</label>
            <input type="text" id="testResults" name="testResults" value="<?php echo $testResults[0]['testResults']; ?>">

            <label for="dateTested">Date Tested:</label>
            <input type="date" id="dateTested" name="dateTested" value="<?php echo $testResults[0]['dateTested'];?>">

            <input type="submit" name="updateTestResult" value="Update Test Result">
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

        $('#appointmentDropDown').select2({
            placeholder: "Select appointment",
            allowClear: true
        });
    });
</script>