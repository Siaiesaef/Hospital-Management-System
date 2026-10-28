<?php
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Hospital Management System</title>
  <meta charset="utf-8">
  <meta name="description" content="Patient Navigation Bar">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Navbar</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bgColor">

  <div class="topnav">
    <a class="navbar-brand-top" href="patientNavBar.php">Patient Homepage</a>
    <a class="navbar-brand-top LogOut" href="logOut.php">Log Out</a><br />
  </div>

  <div class="header">
    <div class="navbar"> 
      <a class="navbar-brand" href="viewMyRecords.php">View My Patient Record</a><br />

      <a class="navbar-brand" href="viewMyMedications.php">View My Medications</a><br />

      <a class="navbar-brand" href="viewMyTestResults.php">View My Test Results</a><br />

      <a class="navbar-brand" href="viewMyAppointments.php">View My Appointments</a><br />
    </div>
  </div>
  <!-- <div class="main" role="main">
    <h1 class="centered-header">Welcome to the Patient Homepage!👋</h1>
  </div> -->
</body>
</html>