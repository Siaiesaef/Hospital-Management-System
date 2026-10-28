<?php
require_once("functions.php");
startSessionIfNotStarted();
require_once("db_connect.php");
include("adminNavBar.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View Checked in Patients Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Checked in Patients</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">View Checked in Patients</h1>
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

                WHERE aCheckedInTime IS NOT null 
                    AND(
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ? OR
                    a.aDate LIKE ? OR
                    a.aTime LIKE ?
                )
            ");

            $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result = $stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            if ($total_records==0){
                echo "<h2 class='centered-header'>No Patients have been checked in today.</h2>";
            } else{
                $total_pages = ceil($total_records / $records_per_page);

                $stmt = $conn->prepare("SELECT 
                    a.appointmentID, 
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) 
                    AS PatientName, 
                    a.aDate, 
                    a.aTime,
                    a.aCheckedInTime 

                    FROM Appointment a
                    INNER JOIN Patient p ON p.patientID=a.patientID

                    WHERE a.aCheckedInTime IS NOT NULL
                        AND (
                        CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ? OR
                        a.aDate LIKE ? OR
                        a.aTime LIKE ?
                    )

                    ORDER BY a.aCheckedInTime DESC
                    LIMIT ? OFFSET ?");

                $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
                $stmt->execute();
                $result = $stmt->get_result();

                echo "
                <form method='get' class='search-form'>
                    <label for='search'>Search by Patient First or Last Name, Date or Time:</label>
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
                        <td>Appointment Date</td>
                        <td>Appointment Time</td>
                        <td>Checked In DateTime</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>";

                while ($row = $result->fetch_assoc()){
                    $appointmentid = $row['appointmentID'];
                    $patientname = $row['PatientName'];
                    $adate = $row['aDate'];
                    $atime = $row['aTime'];
                    $acheckedintime = $row['aCheckedInTime'];
                    echo "
                        <tr>
                            <td>$appointmentid</td>
                            <td>$patientname</td>
                            <td>$adate</td>
                            <td>$atime</td>
                            <td>$acheckedintime</td>
                            <td><a class='action-button' href='checkOut.php?appointmentID=$appointmentid'>Check Out</a></td>
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
                    
            }
            
        } else{

            $total_query="SELECT COUNT(*) AS total FROM Appointment WHERE aCheckedInTime IS NOT null";

            $total_result = $conn->query($total_query);
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];
            if ($total_records==0){
                echo "<h2 class='centered-header'>No Patients have been checked in today.</h2>";
            } else{
                $total_pages = ceil($total_records / $records_per_page);

                $select_query = "SELECT 
                    a.appointmentID, 
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) 
                    AS PatientName, 
                    a.aDate, 
                    a.aTime,
                    a.aCheckedInTime 

                    FROM Appointment a
                    INNER JOIN Patient p ON p.patientID=a.patientID

                    WHERE a.aCheckedInTime IS NOT NULL

                    ORDER BY a.aCheckedInTime DESC
                    LIMIT ? OFFSET ?";

                $stmt = $conn->prepare($select_query);
                $stmt->bind_param("ii", $records_per_page, $offset);
                $stmt->execute();
                $result = $stmt->get_result();
            }

            echo "
            <form method='get' class='search-form'>
                <label for='search'>Search by Patient First or Last Name, Date or Time:</label>
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
                    <td>Appointment Date</td>
                    <td>Appointment Time</td>
                    <td>Checked In DateTime</td>
                    <td>Action</td>
                </tr>
            </thead>
            <tbody>";

            while ($row = $result->fetch_assoc()){
                $appointmentid = $row['appointmentID'];
                $patientname = $row['PatientName'];
                $adate = $row['aDate'];
                $atime = $row['aTime'];
                $acheckedintime = $row['aCheckedInTime'];
                echo "
                    <tr>
                        <td>$appointmentid</td>
                        <td>$patientname</td>
                        <td>$adate</td>
                        <td>$atime</td>
                        <td>$acheckedintime</td>
                        <td><a class='action-button' href='checkOut.php?appointmentID=$appointmentid'>Check Out</a></td>
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
                
        }
        $conn->close();
        ?>
    </div>
</body>
</html>