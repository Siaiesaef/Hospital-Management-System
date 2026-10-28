<?php
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Hospital Management System</title>
  <meta charset="utf-8">
  <meta name="description" content="Doctor Navigation Bar">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bgColor">
  <div class="topnav">
      <a class="navbar-brand-top" href="doctorNavBar.php">Doctor Homepage</a><br />
      <div class="logout">
        <a class="navbar-brand-top LogOut" href="logOut.php">Log Out</a><br />
      </div>
  </div>

  <div class="header">
    <div class="navbar">
      <a class="navbar-brand" href="createNewPatientRecord.php">Create New Patient Records</a><br /> 
      <a class="navbar-brand" href="viewPatientRecords.php">View Patient Records</a><br />

      <a class="navbar-brand" href="createNewPrescriptions.php">Prescribe Prescriptions</a><br />
      <a class="navbar-brand" href="viewPrescriptions.php">View Prescriptions</a><br />

      <a class="navbar-brand" href="createNewTestResult.php">Create New Test Results</a><br />
      <a class="navbar-brand" href="viewTestResults.php">View Test Results</a><br />

      <a class="navbar-brand" href="createNewAppointment.php">Create New Appointment</a><br />
      <a class="navbar-brand" href="viewAnyAppointments.php">View Any Appointment</a><br />

      <a class="navbar-brand" href="timetable.php"> Doctor Timetable</a><br /><br /><br />
    </div>
  </div>
  <!-- <div class="main" role="main">
    <h1 class="centered-header">Welcome to the Doctor Homepage!👋</h1>
  </div> -->
</body>