<?php
require_once("functions.php");
startSessionIfNotStarted();
$roleid=$_SESSION['role'];

require_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Create New Patient Record page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Patient Record</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    if ($roleid==3){
        include("adminNavBar.php");
    } else{
        include("doctorNavBar.php");
    }

    if (isset($_POST['createNewPatient'])){

        $roleid=1;

        addPatientRecord($_POST['pFirstName'],
            $_POST['pMiddleName'],  
            $_POST['pLastName'],
            $roleid,
            $_POST['pDOB'],
            $_POST['pAddress'],
            $_POST['pCity'],
            $_POST['pPostcode'],
            $_POST['genderID'],
            $conn
        );
        header('Location: viewPatientRecords.php');
        
    }
    ?>

    <div class="main" role="main">
        <div>
            <h2 class="centered-header">Create New Patient Record</h2>
        </div>
        <form method="post">
            <label for="pFirstName">First Name: </label><br />
            <input type="text" name="pFirstName" placeholder="First Name" required /><br />

            <label for="pMiddleName">Middle Name: </label><br />
            <input type="text" name="pMiddleName" placeholder="Middle Name" required /><br />

            <label for="pLastName">Last Name: </label><br />
            <input type="text" name="pLastName" placeholder="Last Name" required /><br />

            <label for="pDOB">Date Of Birth </label><br />
            <input type="date" id="pDOB" name="pDOB" required /><br />

            <div class="no-select-2">
                <label for="genderID">Gender: </label><br />
                <?php dropDownGender();?><br />
            </div>

            <label for="pAddress">First line of address: </label><br />
            <input type="text" name="pAddress" placeholder="123 Example Road" required /><br />

            <label for="pCity">City: </label><br />
            <input type="text" name="pCity" placeholder="City" required /><br />

            <label for="pPostcode">Postcode: </label><br />
            <input type="text" name="pPostcode" placeholder="AB1 2CD" required /><br />

            <button type="submit" name="createNewPatient">Create Patient</button>
        </form>
    </div>
</body>
</html>
        