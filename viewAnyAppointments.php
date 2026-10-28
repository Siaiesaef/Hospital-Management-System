<?php
require_once("functions.php");
startSessionIfNotStarted();

$roleid=$_SESSION['role'];

if ($roleid==3){
    include("adminNavBar.php");
} else{
    include("doctorNavBar.php");
}
require_once("db_connect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View Any Appointments Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Any Appointments</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">View Any Appointments</h1>
        </div>
        <?php

        $search=$_GET['search'] ?? '';
        $searchParam= "%" . $search . "%";

        //pagination variables
        $records_per_page=4;
        $current_page=isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($current_page<1) $current_page=1;

        $offset=($current_page-1)*$records_per_page;

        if (!empty($search)){

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM Appointment a

                INNER JOIN Patient p ON p.patientID=a.patientID
                INNER JOIN Staff s ON s.staffID=a.staffID
                LEFT JOIN TestResults tr ON tr.testResultsID=a.testResultsID

                WHERE 
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)  LIKE ? OR
                    tr.testResults LIKE ? OR
                    a.aDate LIKE ? OR
                    a.aTime LIKE ?
            ");

            $stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result = $stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                a.appointmentID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) 
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) 
                AS StaffName,
                tr.testResults, 
                a.aDate, 
                a.aTime

                FROM Appointment a

                INNER JOIN Patient p ON p.patientID=a.patientID
                INNER JOIN Staff s ON s.staffID=a.staffID
                LEFT JOIN TestResults tr ON tr.testResultsID=a.testResultsID

                WHERE 
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)  LIKE ? OR
                    tr.testResults LIKE ? OR
                    a.aDate LIKE ? OR
                    a.aTime LIKE ?

                ORDER BY a.appointmentID
                
                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("sssssii", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
        } else{

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM Appointment
            ");

            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];
            

            $stmt = $conn->prepare("SELECT 
                a.appointmentID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) 
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) 
                AS StaffName,
                tr.testResults, 
                a.aDate, 
                a.aTime
                FROM Appointment a
                INNER JOIN Patient p ON p.patientID=a.patientID
                INNER JOIN Staff s ON s.staffID=a.staffID
                LEFT JOIN TestResults tr ON tr.testResultsID=a.testResultsID
                ORDER BY a.appointmentID
                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("ii", $records_per_page, $offset);
            $stmt->execute();
            $result=$stmt->get_result();
        }

        $total_pages = ceil($total_records / $records_per_page);

        echo "
        <form method='get' class='search-form'>
            <label for='search'>Search by Patient/Staff First or Last Name, Test Results, Date or Time:</label>
            <input type='text' name='search' placeholder='Search...' 
            value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <button class='search-btn' type='submit'>Search</button>
        </form>";

        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>Appointment ID</td>
                <td>Patient Name</td>
                <td>Staff Name</td>
                <td>Test Results</td>
                <td>Appointment Date</td>
                <td>Appointment Time</td>
                <td style='text-align: center' colspan='2'> Action </td>
            </tr>
        </thead>
        <tbody>";

        while ($row = $result->fetch_assoc()){
            $appointmentid = $row['appointmentID'];
            $patientname = $row['PatientName'];
            $staffname = $row['StaffName'];
            $testresults = $row['testResults'];
            $adate = $row['aDate'];
            $atime = $row['aTime'];
            echo "
                <tr>
                    <td>$appointmentid</td>
                    <td>$patientname</td>
                    <td>$staffname</td>
                    <td>$testresults</td>
                    <td>$adate</td>
                    <td>$atime</td>
                    <td><a class='action-button' href='updateAppointment.php?appointmentID=$appointmentid'>Edit</a></td>
                    <td><a class='action-button' href='deleteAppointment.php?appointmentID=$appointmentid'>Delete</a></td>
                </tr>";
            } 
            echo "</tbody></table>";

            //pagination controls
            echo"<div class='pagination'>";
            if ($current_page > 1) {
            $prev_page = $current_page - 1;
            echo "<a href='?page=$prev_page&search=" . urlencode($search) . "'>Previous</a>";
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    echo "<strong>$i</strong>";
                } else {
                    echo "<a href='?page=$i&search=" . urlencode($search) . "'>$i</a>";
                }
            }
            if ($current_page < $total_pages) {
                $next_page = $current_page + 1;
                echo "<a href='?page=$next_page&search=" . urlencode($search) . "'>Next</a>";
            }
            echo "</div>";
            $conn->close();
        ?>
    </div>
</body>
</html>