<?php
include_once("db_connect.php");

// reference: https://www.php.net/manual/en/function.password-hash.php
function hashPassword($plainPassword)
{
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

function dropDownGender()
{
    echo '<select id= "genderID" name="genderID" required>';
    echo '<option value="">Select your gender</option>';
    echo '<option value="1">Female</option>';
    echo '<option value="2">Male</option>';
    echo '<option value="3">Other</option>';
    echo '</select><br />';
}


function createPatientUser($pFirstName, $pLastName)
{
    $letter1=strtoupper($pFirstName[0]);
    $letter2=strtoupper($pLastName[0]);
    return $letter1 . $letter2;
}

function dropDownRole(){
    echo "<select name='roleID' id='roleID' required>";
    echo "<option value=''>Select Role</option>";
    echo "<option value='1'>Patient</option>";
    echo "<option value='2'>Doctor</option>";
    echo "</select><br />";
}  

// reference: https://select2.org/
function smartDropDownDoctorSpecialityID($conn){
    echo"<select name='staffTypeID' id='doctorSpecialityDropDown'>
            <option value=''>Select Speciality</option>";
    $sql="SELECT * FROM StaffType 
        ORDER BY stSpeciality";

    $result=$conn->query($sql);

    while ($row=$result->fetch_assoc()) {
        echo "<option value='" . $row['staffTypeID'] . "'>" . $row['stSpeciality'] . "</option>";
    }
    echo "</select><br />";

}

function smartDropDownTestResultsID($conn){
    echo"<select name='testResults' id='testResultsDropDown' required>
            <option value=''>Select Test Result</option>";
    $sql="SELECT 
        tr.*,
        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
        AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
        AS StaffName

        FROM TestResults tr

        INNER JOIN Patient p
        ON p.patientID=tr.PatientID
        INNER JOIN Staff s 
        ON s.staffID=tr.staffID 

        ORDER BY testResults";

    $result=$conn->query($sql);

    while ($row=$result->fetch_assoc()) {
        echo "<option value='" . $row['testResultsID'] . "'>
                {$row['testResults']} - {$row['PatientName']} ( Doctor {$row['StaffName']})
            </option>";
    }
    echo "</select><br />";

}

function smartDropDownTestResultsIDNotRequired($conn){
    echo"<select name='testResults' id='testResultsDropDown'>
            <option value=''>Select Test Result</option>";
    $sql="SELECT 
        tr.*,
        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
        AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
        AS StaffName

        FROM TestResults tr

        INNER JOIN Patient p
        ON p.patientID=tr.PatientID
        INNER JOIN Staff s 
        ON s.staffID=tr.staffID 

        ORDER BY testResults";

    $result=$conn->query($sql);

    while ($row=$result->fetch_assoc()) {
        echo "<option value='" . $row['testResultsID'] . "'>
                {$row['testResults']} - {$row['PatientName']} ( Doctor {$row['StaffName']})
            </option>";
    }
    echo "</select><br />";

}

function addLoginUser($patientID, $StaffID, $lUsername, $lHashPassword, $roleID, $lMemorableInfo, $lEmail, $lPhoneNo, $conn){
    $stmt=$conn->prepare("INSERT INTO Login(patientID, staffID, lUsername, lHashPassword, roleID, lMemorableInfo, lEmail, lPhoneNo)
    VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param('iississs', $patientID, $StaffID, $lUsername, $lHashPassword, $roleID, $lMemorableInfo, $lEmail, $lPhoneNo);
    $success=$stmt->execute();

    if (!$success) {
        die("Error inserting login user: " . $stmt->error);
    }

    $stmt->close();

    return $success;
}

function usernameExists($lUsername, $conn){
    // include_once("db_connect.php");
    $query="SELECT
        * 
        FROM Login 
        WHERE lUsername=?";

    $stmt=$conn->prepare($query);
    $stmt->bind_param('s', $lUsername);
    $stmt->execute();
    $result=$stmt->get_result();
    $user=$result->fetch_assoc();

    $stmt->close();

    return $user ? true : false;
}


function handleSignUp($postdata, $conn){
    global $error, $username;

    // var_dump($postdata);
    // exit();

    // require_once("db_connect.php");

    if (empty($postdata['roleID'])){
        return "Please select a role";
    }

    if (strlen($postdata['password']) < 8) {
        return "Password must be at least 8 characters long.";

    } 
    
    if ($postdata['password'] !== $postdata['confirmPassword']) {
        return "Passwords do not match.";

    }

    // if ($postdata['roleID']==="1"){ 
    //     if (
    //         empty($postdata['pAddress']) ||
    //         empty($postdata['pCity']) ||
    //         empty($postdata['pPostcode'])
    //     ){
    //         return "All patient address fields are required.";

    //     }
    // }

    // if (!empty($error)){
    //     return $error;
    // }

    $conn->begin_transaction();

    if ($postdata['roleID']==="2") {
        $roleID = 2; // Doctor role ID
        $roleLetter = "D";

        $success=addStaffRecord(
            $postdata['FirstName'], 
            $postdata['MiddleName'],
            $postdata['LastName'], 
            $roleID, 
            $postdata['genderID'],
            $postdata['staffTypeID'],
            $postdata['DOB'],
            $conn 
        );
        $newRecordID = $conn->insert_id;

        if (!$success) {
            $conn->rollback();
            return "Error creating doctor record." . $conn->error;
        }

    } elseif ($postdata['roleID']==="1"){ 
        $roleID = 1; // Patient role ID
        $roleLetter = "P";

        $success=addPatientRecord(
            $postdata['FirstName'],
            $postdata['MiddleName'],
            $postdata['LastName'],
            $roleID,
            $postdata['DOB'],
            $postdata['pAddress'],
            $postdata['pCity'],
            $postdata['pPostcode'],
            $postdata['genderID'],
            $conn
        );
        $newRecordID = $conn->insert_id;

        if (!$success) {
            $conn->rollback();
            return "Error creating patient record." . $conn->error;
        }

    } elseif ($postdata['roleID']==="3"){
        $roleID = 3; // Admin role ID
        $roleLetter = "A";
        $stafftypeid=null;

        $success=addStaffRecord(
            $postdata['FirstName'], 
            $postdata['MiddleName'],
            $postdata['LastName'], 
            $roleID, 
            $postdata['genderID'],
            $stafftypeid,
            $postdata['DOB'],
            $conn 
        );
        $newRecordID = $conn->insert_id;

        if (!$success) {
            $conn->rollback();
            return "Error creating admin record." . $conn->error;
        }
    } else {
        $conn->rollback();
        return "Invalid role selection.";
    }
    
    $FirstName = $postdata['FirstName'];
    $LastName = $postdata['LastName'];

    $password = hashPassword($postdata['password']);
    $memorableInfo=hashPassword($postdata['lMemorableInfo']);

    $usernameBase = strtolower($FirstName . $LastName . $roleLetter);
    $username = $usernameBase;
    $counter = 1;
    
    while (usernameExists($username, $conn)) {
        $username = $usernameBase . $counter;
        $counter++;
    }

    if ($postdata['roleID']=="1"){
        $patientID = $newRecordID;
        $staffID = null;
    } else {
        $patientID = null;
        $staffID = $newRecordID;
    }

    $success= addLoginUser(
        $patientID,
        $staffID,
        $username, 
        $password, 
        $roleID, 
        $memorableInfo,
        $postdata['lEmail'], 
        $postdata['lPhoneNo'],
        $conn
    );

    if (!$success) {
        $conn->rollback();
        return "Error creating account. Please try again." . $conn->error;
    } 

    $conn->commit();

    echo "<script>alert('Account created successfully! Your username is: " . htmlspecialchars($username) . "'); window.location.href='logIn.php';</script>";
    exit();
}

function startSessionIfNotStarted() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function smartDropDownPatientID($conn){
    echo"<select name='patientID' id='patientDropDown' required>";
    echo"<option value=''>Patients</option>";

    $stmt=$conn->prepare("SELECT 
    patientID, pFirstName, 
    pLastName, pDOB
    FROM Patient
    ORDER BY pFirstName, pLastName"
    );
    $stmt->execute();
    $result=$stmt->get_result();
    while($row=$result->fetch_assoc()){
        echo"<option value='{$row['patientID']}'>
                {$row['pFirstName']} {$row['pLastName']} (DOB: {$row['pDOB']})
            </option>";
    }
    echo"</select>";
    $stmt->close();
}

function smartDropDownDoctorID($conn){
    echo"<select name='staffID' id='doctorDropDown' required>";
    echo"<option value=''>Doctors</option>";

    $stmt=$conn->prepare("SELECT 
        staffID, sFirstName, 
        sLastName, sDOB
        FROM Staff
        WHERE RoleID=?
        ORDER BY sFirstName, sLastName"
    );

    $doctorRoleID=2;
    $stmt->bind_param("i", $doctorRoleID);

    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        echo"<option value='{$row['staffID']}'>
                {$row['sFirstName']} {$row['sLastName']} (DOB: {$row['sDOB']})
            </option>";
    }
    echo"</select>";
    $stmt->close();
}


function smartDropDownAppointmentID($conn){
    echo"<select name='appointmentID' id='appointmentDropDown' required>";
    echo"<option value=''>Appointments</option>";

    $stmt=$conn->prepare("SELECT 
        a.appointmentID,
        a.patientID,
        a.staffID,
        a.aDate,
        a.aTime,
        CONCAT_WS(' ', p.pFirstName, p.pLastName) AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sLastName) AS StaffName

        FROM Appointment a

        INNER JOIN Patient p 
        ON p.patientID=a.patientID

        INNER JOIN Staff s
        ON s.staffID=a.staffID

        WHERE a.testResultsID IS NULL

        ORDER BY a.appointmentID"
    );

    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){

        echo"<option value='{$row['appointmentID']}'>
                Appt {$row['appointmentID']} -
                {$row['PatientName']} 
                with Dr. {$row['StaffName']} 
                (Date: {$row['aDate']}, Time: {$row['aTime']})
            </option>";
    }
    echo"</select>";
    $stmt->close();
}


function smartDropDownAppointmentIDNotRequired($conn){
    echo"<select name='appointmentID' id='appointmentDropDown'>";
    echo"<option value=''>Appointments</option>";

    $stmt=$conn->prepare("SELECT 
        a.appointmentID,
        a.patientID,
        a.staffID,
        a.aDate,
        a.aTime,
        CONCAT_WS(' ', p.pFirstName, p.pLastName) AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sLastName) AS StaffName

        FROM Appointment a

        INNER JOIN Patient p 
        ON p.patientID=a.patientID

        INNER JOIN Staff s
        ON s.staffID=a.staffID

        WHERE a.testResultsID IS NULL

        ORDER BY a.appointmentID"
    );

    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){

        echo"<option value='{$row['appointmentID']}'>
                Appt {$row['appointmentID']} -
                {$row['PatientName']} 
                with Dr. {$row['StaffName']} 
                (Date: {$row['aDate']}, Time: {$row['aTime']})
            </option>";
    }
    echo"</select>";
    $stmt->close();
}

function smartDropDownAppointmentIDForUpdate($conn, $selectedID=null){
    echo"<select name='appointmentID' id='appointmentDropDown' required>";
    echo"<option value=''>Appointments</option>";

    $stmt=$conn->prepare("SELECT 
        a.appointmentID,
        a.patientID,
        a.staffID,
        a.aDate,
        a.aTime,
        CONCAT_WS(' ', p.pFirstName, p.pLastName) AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sLastName) AS StaffName

        FROM Appointment a

        INNER JOIN Patient p 
        ON p.patientID=a.patientID

        INNER JOIN Staff s
        ON s.staffID=a.staffID

        WHERE a.testResultsID IS NULL
            OR a.apointmentID=?

        ORDER BY a.appointmentID"
    );

    $stmt->bind_param("i", $selectedID);
    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        $selected=($row['appointmentID']==$selectedID) ? "selected" : "";

        echo"<option value='{$row['appointmentID']}' $selected>
                Appt {$row['appointmentID']} -
                {$row['PatientName']} 
                with Dr. {$row['StaffName']} 
                (Date: {$row['aDate']}, Time: {$row['aTime']})
            </option>";
    }
    echo"</select>";
    $stmt->close();
}

function addPrescription($patientID, $staffID, $datePrescribed, $dateCollected, $medication, $dosage, $repeat, $conn){
    $stmt=$conn->prepare("INSERT INTO Prescription(patientID, staffID, datePrescribed, dateCollected, medication, dosage, repeatPrescription)
    VALUES (?,?,?,?,?,?,?)");

    $stmt->bind_param('iissssi', $patientID, $staffID, $datePrescribed, $dateCollected, $medication, $dosage, $repeat);
    $success=$stmt->execute();

    $stmt->close();

    return $success;
}

function addTestResult($patientID, $staffID, $testResults, $dateTested, $conn){
    $stmt=$conn->prepare("INSERT INTO TestResults(patientID, staffID, testResults, dateTested)
    VALUES (?,?,?,?)");

    $stmt->bind_param('iiss', $patientID, $staffID, $testResults, $dateTested);
    $success=$stmt->execute();

    $stmt->close();

    return $conn->insert_id; 
}

function addAppointment($patientID, $staffID, $testResultsID, $aDate, $aTime, $conn){
    $stmt=$conn->prepare("INSERT INTO Appointment(patientID, staffID, testResultsID, aDate, aTime)
    VALUES (?,?,?,?,?)");

    $stmt->bind_param('iiiss', $patientID, $staffID, $testResultsID, $aDate, $aTime);
    $success=$stmt->execute();

    $stmt->close();

    return $success;
}

function linkTestResultToAppointment($testResultsID, $appointmentID, $conn){
    $stmt=$conn->prepare(
        "UPDATE Appointment 
        SET testResultsID=? 
        WHERE appointmentID=?
        ");
    $stmt->bind_param('ii', $testResultsID, $appointmentID);
    $success=$stmt->execute();

    return $success;
}

function addPatientRecord($pFirstName, $pMiddleName, $pLastName, $roleID, $pDOB, $pAddress, $pCity, $pPostcode, $genderID, $conn){
     
    $stmt=$conn->prepare("INSERT INTO Patient(pFirstName, pMiddleName, pLastName, roleID, pDOB, pAddress, pCity, pPostcode, genderID)
    VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssissssi', $pFirstName, $pMiddleName, $pLastName, $roleID, $pDOB, $pAddress, $pCity, $pPostcode, $genderID);
    $success=$stmt->execute();

    $stmt->close();

    return $success;
}

function addStaffRecord($sFirstName, $sMiddleName, $sLastName, $roleID, $genderID, $staffTypeID, $sDOB, $conn){
     
    $stmt=$conn->prepare("INSERT INTO Staff(sFirstName, sMiddleName, sLastName, roleID, genderID, staffTypeID, sDOB)
    VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('sssiiis', $sFirstName, $sMiddleName, $sLastName, $roleID, $genderID, $staffTypeID, $sDOB);
    $success=$stmt->execute();

    $stmt->close();
    
    return $success;
}

function addDoctorSpeciality($stSpeciality, $conn){
    $stmt=$conn->prepare("INSERT INTO StaffType(stSpeciality)
    VALUES (?)");
    $stmt->bind_param('s', $stSpeciality);
    $success=$stmt->execute();

    $stmt->close();

    return $success;
}

function smartDropDownPatientIDForUpdate($conn, $selectedID=null){
    echo "<select name='patientID' id='patientDropDown' required>";
    echo "<option value=''>Patients</option>";

    $stmt=$conn->prepare("SELECT
        patientID,
        pFirstName,
        pLastName,
        pDOB
        FROM Patient
        ORDER BY pFirstName, pLastName
    ");
    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        $selected=($row['patientID']==$selectedID) ? "selected" : "";

        echo "<option value='{$row['patientID']}' $selected>
                {$row['pFirstName']} {$row['pLastName']} (DOB: {$row['pDOB']})
            </option>";
    }
    echo "</select>";
}

function smartDropDownDoctorIDForUpdate($conn, $selectedID=null){
    echo "<select name='staffID' id='doctorDropDown' required>";
    echo "<option value=''>Doctors</option>";

    $stmt=$conn->prepare("SELECT
        staffID,
        sFirstName,
        sLastName,
        sDOB
        FROM Staff
        WHERE roleID=2
        ORDER BY sFirstName, sLastName
    ");
    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        $selected=($row['staffID']==$selectedID) ? "selected" : "";

        echo "<option value='{$row['staffID']}' $selected>
                {$row['sFirstName']} {$row['sLastName']} (DOB: {$row['sDOB']})
            </option>";
    }
    echo "</select>";
}

function smartDropDownSpecialityIDForUpdate($conn, $selectedID=null){
    echo "<select name='staffTypeID' id='specialityDropDown' required>";
    echo "<option value=''>Specialities</option>";

    $stmt=$conn->prepare("SELECT
        stSpeciality,
        staffTypeID
        FROM StaffType
        ORDER BY stSpeciality
    ");
    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        $selected=($row['staffTypeID']==$selectedID) ? "selected" : "";

        echo "<option value='{$row['staffTypeID']}' $selected>
                {$row['stSpeciality']} 
            </option>";
    }
    echo "</select>";
}

function smartDropDownTestResultsIDForUpdate($conn, $selectedID=null){
    echo "<select name='testResultsID' id='testResultsDropDown' required>";
    echo "<option value=''>Test Results</option>";

    $stmt=$conn->prepare("SELECT
        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
        AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
        AS StaffName,
        tr.testResults,
        tr.testResultsID 

        FROM TestResults tr

        INNER JOIN Patient p
        ON p.patientID=tr.patientID
        INNER JOIN Staff s
        ON s.staffID=tr.staffID

        ORDER BY tr.testResults
    ");
    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        $selected=($row['testResultsID']==$selectedID) ? "selected" : "";

        echo "<option value='{$row['testResultsID']}' $selected>
                {(Test Result: {$row['testResults']}) - ({$row['PatientName']}) 
                \n (Doctor {$row['StaffName']})}
            </option>";
    }
    echo "</select>";
}

function smartDropDownAppointmentsToCheckIn($conn){
    echo "<select name='appointmentID' id='appointmentDropDown' required>";
    echo "<option value=''>Select Appointment</option>";

    $stmt=$conn->prepare("SELECT
        a.appointmentID,
        a.aDate,
        a.aTime,
        CONCAT_WS(' ', p.pFirstName, p.pLastName)
        AS PatientName
        
        FROM Appointment a
        INNER JOIN Patient p
        ON p.patientID=a.patientID
        
        WHERE a.aCheckedInTime IS NULL
        AND a.aDate=CURDATE()
        
        ORDER BY a.aTime
    ");

    $stmt->execute();
    $result=$stmt->get_result();

    while($row=$result->fetch_assoc()){
        echo "<option value='{$row['appointmentID']}'>
                {$row['PatientName']} - {$row['aDate']} {$row['aTime']}
            </option>";
    }
    echo "</select><br />";
}

function updateDoctorSpeciality($stSpeciality, $staffTypeID, $conn){
    $sql="UPDATE StaffType 
        SET stSpeciality=? 
        WHERE staffTypeID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("si", $stSpeciality, $staffTypeID);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }

}

function updateTestResults($stSpeciality, $staffTypeID, $conn){
    $sql="UPDATE StaffType 
        SET stSpeciality=? 
        WHERE staffTypeID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("si", $stSpeciality, $staffTypeID);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }

}

function updateAdmin($staffid, $genderid, $firstname, $middlename, $lastname, $dob,  $conn){
    $sql="UPDATE Staff 
        SET genderID=?,
        sFirstName=?,
        sMiddleName=?,
        sLastName=?,
        sDOB=?
        WHERE staffID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("issssi", $genderid, $firstname, $middlename, $lastname, $dob, $staffid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewAdminRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewAdminRecords.php';
        </script>";
    }

}

function updateDoctor($staffid, $genderid, $stafftypeid, $firstname, $middlename, $lastname, $dob, $conn){
    $sql="UPDATE Staff 
        SET genderID=?,
        staffTypeID=?,
        sFirstName=?,
        sMiddleName=?,
        sLastName=?,
        sDOB=?
        WHERE staffTypeID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("iissssi", $genderid, $stafftypeid, $firstname, $middlename, $lastname, $dob, $staffid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewDoctorRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewDoctorRecords.php';
        </script>";
    }

}

function updatePatient($patientid, $genderid, $firstname, $middlename, $lastname, $dob, $address, $city, $postcode, $conn){
    $sql="UPDATE Patient 
        SET genderID=?,
        pFirstName=?,
        pMiddleName=?,
        pLastName=?,
        pDOB=?,
        pAddress=?,
        pCity=?,
        pPostcode=?
        WHERE patientID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("isssssssi", $genderid, $firstname, $middlename, $lastname, $dob, $address, $city, $postcode, $patientid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewPatientRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewPatientRecords.php';
        </script>";
    }

}

function updatePatientForPatient($patientid, $genderid, $firstname, $middlename, $lastname, $dob, $address, $city, $postcode, $conn){
    $sql="UPDATE Patient 
        SET genderID=?,
        pFirstName=?,
        pMiddleName=?,
        pLastName=?,
        pDOB=?,
        pAddress=?,
        pCity=?,
        pPostcode=?
        WHERE patientID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("isssssssi", $genderid, $firstname, $middlename, $lastname, $dob, $address, $city, $postcode, $patientid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
        </script>";
    }

}

function updateAppointment($appointmentid, $patientid, $staffid, $testResultsID, $adate, $atime,  $conn){
    $sql="UPDATE Appointment 
        SET 
        patientID=?,
        staffID=?,
        testResultsID=?,
        aDate=?,
        aTime=?
        WHERE appointmentID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("iiissi", $patientid, $staffid, $testResultsID, $adate, $atime, $appointmentid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewAnyAppointments.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewAnyAppointments.php';
        </script>";
    }

}

function checkOut($appointmentid, $checkOut=null,  $conn){
    $sql="UPDATE Appointment 
        SET 
        aCheckedInTime=?
        WHERE appointmentID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("si", $checkOut, $appointmentid);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewCheckedInPatients.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewCheckedInPatients.php';
        </script>";
    }

}

function updatePrescription($staffid, $patientid, $datePrescribed, $dateCollected, $medication, $repeatPrescription, $nextPickUp, $prescriptionID,  $conn){
    if(empty($nextPickUp)){
        $nextPickUp=null;
    }
    $sql="UPDATE Prescription 
        SET 
        staffID=?,
        patientID=?,
        datePrescribed=?,
        dateCollected=?,
        medication=?,
        repeatPrescription=?,
        nextPickUp=?
        WHERE prescriptionID=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("iisssisi", $staffid, $patientid, $datePrescribed, $dateCollected, $medication, $repeatPrescription, $nextPickUp, $prescriptionID);
    
    if ($stmt->execute()){
        echo "<script>
            alert('Successfully updating the record!');
            window.location.href='viewPrescriptions.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in updating the record . $conn->lastErrorMsg()');
            window.location.href='viewPrescriptions.php';
        </script>";
    }

}

function viewStaffType($stafftypeid,$conn){
    $stmt=$conn->prepare("SELECT * 
        FROM StaffType 
        WHERE staffTypeID=?
        ORDER BY staffTypeID DESC");

    $stmt->bind_param("i", $stafftypeid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function viewTestResult($testresultsid,$conn){
    $stmt=$conn->prepare("SELECT 
        tr.*,
        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
        AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
        AS StaffName

        FROM TestResults tr

        INNER JOIN Patient p 
        ON p.patientID=tr.patientID
        INNER JOIN Staff s
        ON s.staffID=tr.staffID

        WHERE testResultsID=? 
        ORDER BY testResultsID DESC");

    $stmt->bind_param("i", $testresultsid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function viewPrescription($prescriptionid,$conn){
    $stmt=$conn->prepare("SELECT 
        pr.*,
        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
        AS PatientName,
        CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
        AS StaffName

        FROM Prescription pr

        INNER JOIN Patient p 
        ON p.patientID=pr.patientID
        INNER JOIN Staff s
        ON s.staffID=pr.staffID

        WHERE pr.prescriptionID=?
        ORDER BY pr.prescriptionID DESC");

    $stmt->bind_param("i", $prescriptionid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function viewStaff($staffid,$conn){
    $stmt=$conn->prepare("SELECT 
        s.* ,
        g.gGender,
        st.stSpeciality
        FROM Staff s

        INNER JOIN Gender g
        ON s.genderID=g.genderID
        LEFT JOIN StaffType st
        ON st.staffTypeID=s.staffTypeID

        WHERE s.staffID=?
        ORDER BY s.staffID DESC");

    $stmt->bind_param("i", $staffid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function viewPatient($patientid,$conn){
    $stmt=$conn->prepare("SELECT 
        p.* ,
        g.gGender
        FROM Patient p

        INNER JOIN Gender g
        ON p.genderID=g.genderID

        WHERE patientID=?
        ORDER BY patientID DESC");

    $stmt->bind_param("i", $patientid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function viewAppointment($appointmentid,$conn){
    $stmt=$conn->prepare("SELECT 
        a.* ,
        CONCAT_WS(' ', s.sFirstName, s.sLastName) AS StaffName,
        CONCAT_WS(' ', p.pFirstName, p.pLastName) AS PatientName,
        tr.testResults,
        a.aDate,
        a.aTime,
        a.aCheckedInTime 
        FROM Appointment a
        INNER JOIN Staff s
        ON s.staffID=a.staffID
        INNER JOIN Patient p
        ON p.patientID=a.patientID
        LEFT JOIN TestResults tr
        ON tr.testResultsID=a.testResultsID 
        WHERE appointmentID=?
        ORDER BY appointmentID DESC");

    $stmt->bind_param("i", $appointmentid);

    $stmt->execute();
    $result=$stmt->get_result();

    $detail=[];

    while($row=$result->fetch_assoc()){
        $detail[]=$row;
    }
    $stmt->close();
    return $detail;
}

function deleteDoctorSpeciality($stafftypeid, $conn){
    $stmt=$conn->prepare("DELETE FROM
    StaffType
    WHERE staffTypeID=?");

    $stmt->bind_param("i", $stafftypeid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewDoctorSpecialities.php';
        </script>";
    }
}

function deletePrescription($prescriptionid, $conn){
    $stmt=$conn->prepare("DELETE FROM
    Prescription
    WHERE prescriptionID=?");

    $stmt->bind_param("i", $prescriptionid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewPrescriptions.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewPrescriptions.php';
        </script>";
    }
}

function deleteAdmin($staffid, $conn){
    $stmt=$conn->prepare("DELETE FROM
    Staff
    WHERE staffID=?");

    $stmt->bind_param("i", $staffid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewAdminRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewAdminRecords.php';
        </script>";
    }
}

function deleteDoctor($staffid, $conn){
    $stmt=$conn->prepare("DELETE FROM
    Staff
    WHERE staffID=?");

    $stmt->bind_param("i", $staffid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewDoctorRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewDoctorRecords.php';
        </script>";
    }
}

function deleteAppointment($appointmentid, $conn){
    $stmt=$conn->prepare("DELETE FROM
        Appointment
        WHERE appointmentID=?");

    $stmt->bind_param("i", $appointmentid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewAnyAppointments.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewAnyAppointments.php';
        </script>";
    }
}

function deletePatient($patientid, $conn){
    $stmt=$conn->prepare("DELETE FROM
    Patient
    WHERE patientID=?");

    $stmt->bind_param("i", $patientid);

    if ($stmt->execute()){
        echo "<script>
            alert('Successfully deleting the record!');
            window.location.href='viewPatientRecords.php';
        </script>";
    }else{
        echo "<script>
            alert('Error in deleting the record . $conn->error');
            window.location.href='viewPatientRecords.php';
        </script>";
    }
}

function repeatPrescription($prescriptionID, $conn){
    //get original prescription
    $stmt=$conn->prepare("SELECT
        patientID,
        staffID,
        medication,
        dosage, 
        dateCollected, 
        repeatPrescription
        FROM Prescription 
        WHERE prescriptionID=?
    ");

    $stmt->bind_param("i", $prescriptionID);
    $stmt->execute();
    $result=$stmt->get_result();
    $original=$result->fetch_assoc();
    $stmt->close();

    //check if repeats allowed
    if (!$original || $original['repeatPrescription']==0){
        echo "<script>alert('Repeat not allowed for this prescription'); 
                window.location.href='viewMyMedications.php'
            </script>";
        return;
    }
    
    //prevent repeating early
    if (!empty($original['dateCollected']) && strtotime($original['dateCollected']) > strtotime("-30 days")){
        echo "<script>alert('Too early to request repeat');
                window.location.href='viewMyMedications.php'
            </script>";
        return;
    }

    $stmt = $conn->prepare("INSERT INTO Prescription
        (patientID,
        staffID,
        datePrescribed,
        medication,
        dosage,
        repeatPrescription)

        VALUES
        (?,?,CURDATE(),?,?,?)
    ");

    $repeat=1; //to keep it repeatable
    $stmt->bind_param("iissi", 
        $original['patientID'], 
        $original['staffID'], 
        $original['medication'], 
        $original['dosage'], 
        $repeat
    );

    $stmt->execute();
    $stmt->close();

    $stmt=$conn->prepare("UPDATE
        Prescription
        
        SET nextPickUp=DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        WHERE prescriptionID=?
    ");

    $stmt->bind_param("i", $prescriptionID);
    $stmt->execute();
    $stmt->close();

    echo "<script>
            alert('Repeat prescription requested successfully!)';
            window.location.href='viewMyMedications.php';
        </script>";

}

?>