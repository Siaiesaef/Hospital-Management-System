<?php
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Hospital Management System</title>
  <meta charset="utf-8">
  <meta name="description" content="Admin Navigation Bar">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bgColor">
  <div class="topnav">
    <a class="navbar-brand-top" href="adminNavBar.php">Admin Homepage</a><br />
    <a class="navbar-brand-top LogOut" href="logOut.php">Log Out</a><br />
  </div>

  <div class="header">
    <div class="navbar"> 
      <a class="navbar-brand">Admin Tools</a><br />
      <a class="navbar-brand" href="createNewPatientRecord.php">Create New Patient</a><br />
      <a class="navbar-brand" href="viewPatientRecords.php">View All Patients</a><br />

      <a class="navbar-brand" href="viewDoctorRecords.php">View All Doctors</a><br />

      <a class="navbar-brand" href="createNewDoctorSpeciality.php">Create New Doctor Speciality</a><br />
      <a class="navbar-brand" href="viewDoctorSpecialities.php">View All Doctor Specialities</a><br />

      <a class="navbar-brand" href="SignUpAdmin.php">Sign Up a New Admin</a><br />
      <a class="navbar-brand" href="viewAdminRecords.php">View All Admins</a><br />

      <a class="navbar-brand" href="checkinPatient.php">Check In a Patient</a><br />
      <a class="navbar-brand" href="viewCheckedinPatients.php">View All Checked in Patients</a><br />

      <a class="navbar-brand"></a><br />
      <a class="navbar-brand">Doctor Tools</a><br />

      <a class="navbar-brand" href="createNewPatientRecord.php">Create New Patient Records</a><br /> 
      <a class="navbar-brand" href="viewPatientRecords.php">View Patient Records</a><br />

      <a class="navbar-brand" href="createNewPrescriptions.php">Prescribe Prescriptions</a><br />
      <a class="navbar-brand" href="viewPrescriptions.php">View Prescriptions</a><br />

      <a class="navbar-brand" href="createNewTestResult.php">Create New Test Results</a><br />
      <a class="navbar-brand" href="viewTestResults.php">View Test Results</a><br />

      <a class="navbar-brand" href="createNewAppointment.php">Create New Appointment</a><br />
      <a class="navbar-brand" href="viewAnyAppointments.php">View Any Appointment</a><br />

    </div>
  </div>
  <!-- <div class="main" role="main">
    <h1 class="centered-header">Welcome to the Admin Homepage!👋</h1>
  </div> -->
</body>