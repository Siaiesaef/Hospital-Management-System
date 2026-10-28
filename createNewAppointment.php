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
    <meta name="description" content="Create New Appointment page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create New Appointment</title>
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

    if (isset($_POST['createNewAppointment'])){

        if ($_POST['aDate']>= date("Y-m-d")){

            $testResultID=$_POST['testResultsID'] ?? null;

            addAppointment($_POST['patientID'],
                $_POST['staffID'],
                $testResultID,
                $_POST['aDate'],
                $_POST['aTime'],
                $conn
            );

            header('Location: viewAnyAppointments.php');
            exit();
        } else {
            $error="Please select a validate date in the future!";
        }
    }
    ?>
    <div class="main" role="main">
        <h2 class="centered-header">Create New Appointment</h2>
        <form method="post">

            <?php if (!empty($error)): ?>
                <p class="color: red; margin-bottom:10px;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <label for="staffID">Select the Doctor: </label><br />
            <?php smartDropDownDoctorID($conn) ?><br />
            
            <label for="patientID">Select a Patient: </label><br />
            <?php smartDropDownPatientID($conn) ?><br />

            <Label for="testResultsID">Select a Test Result if exists: </label><br />
            <?php smartDropDownTestResultsIDNotRequired($conn) ?><br />
            
            <label for="aDate">Appointment Date: </label><br />
            <input type="date" id="aDate" name="aDate" min="<?php echo date('Y-m-d') ?>" required /><br />

            <label for="aTime">Appointment Time: </label><br />
            <input type="time" id="aTime" name="aTime" required /><br />

            <button type="submit" name="createNewAppointment">Create Appointment</button>
        </form>
    </div>

<script>
$(document).ready(function(){
    $('#patientDropDown').select2({
        placeholder: "Search for a patient",
        allowClear: true
    });

    $('#doctorDropDown').select2({
        placeholder: "Search for a doctor",
        allowClear: true
    });

    $('#testResultsDropDown').select2({
        dropDownAutoWidth: true,
        placeholder: "Search for a test result",
        allowClear: true
    });
});
</script>
</body>
</html>