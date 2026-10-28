<?php 
include("functions.php");
startSessionIfNotStarted();
$roleid=$_SESSION['role'];

require_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Create New Test Result page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create New Test Result</title>
    <link rel="stylesheet" href="style.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <?php
    if ($roleid==3){
        include("adminNavBar.php");
    } else{
        include("doctorNavBar.php");
    }

    if (isset($_POST['createNewTestResult'])){

        // var_dump($_POST);
        // exit();
        $appointmentID = $_POST['appointmentID'];

        $stmt=$conn->prepare("SELECT
            patientID,
            staffID
            FROM Appointment
            WHERE appointmentID=?
        ");

        $stmt->bind_param("i", $appointmentID);
        $stmt->execute();

        $result=$stmt->get_result();
        $appointment=$result->fetch_assoc();

        $patientID=$appointment['patientID'];
        $staffID=$appointment['staffID'];

        $testResultID=addTestResult(
            $patientID,
            $staffID,
            $_POST['testResults'],
            $_POST['dateTested'],
            $conn
        );
        if ($appointmentID!= null){
            linkTestResultToAppointment(
                $testResultID,
                $appointmentID,
                $conn
            );
        }
        
        header('Location: viewTestResults.php');
        exit();
    }
    ?>
    <div class="main" role="main">
        <h2 class="centered-header">Create New Test Result</h2>
    <!-- <div class="main"> -->
        <form method="post">
            <label for="appointmentID">Select an Appointment: </label><br />
            <?php smartDropDownAppointmentID($conn) ?><br />
            
            <label for="testResults">Test Results: </label><br />
            <input type="text" name="testResults" placeholder="Test Results" required /><br />

            <label for="dateTested">Date Tested: </label><br />
            <input type="date" id="dateTested" name="dateTested" required /><br />

            <button type="submit" name="createNewTestResult">Create Test Result</button>
            <a class="left-link-appointment" href="createNewAppointment.php">Create Appointment?</a>
        </form>
    </div>

<script>
$(document).ready(function(){
    $('#appointmentDropDown').select2({
        width: '100%',
        placeholder: "Search for an appointment",
        allowClear: true
    });
});
</script>
</body>
</html>