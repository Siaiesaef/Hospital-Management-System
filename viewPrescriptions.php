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
    <meta name="description" content="View Prescriptions Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescriptions</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">Prescriptions</h1>
        </div>
        <?php
        require_once('db_connect.php');
        
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

                FROM Prescription pr
                
                INNER JOIN Patient p 
                ON p.patientID=pr.patientID
                INNER JOIN Staff s 
                ON s.staffID=pr.staffID

                WHERE CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ? OR
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    pr.medication LIKE ? 
            ");

            $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt = $conn->prepare("SELECT 
                pr.prescriptionID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
                AS StaffName,
                pr.datePrescribed,
                pr.dateCollected, 
                pr.medication, 
                pr.dosage,
                pr.nextPickUp,
                pr.repeatPrescription

                FROM Prescription pr

                INNER JOIN Patient p 
                ON p.patientID=pr.patientID
                INNER JOIN Staff s 
                ON s.staffID=pr.staffID

                WHERE CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ? OR
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    pr.medication LIKE ?

                ORDER BY pr.prescriptionID

                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
            $stmt->execute();
            $result=$stmt->get_result();
        } else {

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM Prescription
            ");

            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                pr.prescriptionID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
                AS StaffName,
                pr.datePrescribed,
                pr.dateCollected, 
                pr.medication, 
                pr.dosage,
                pr.nextPickUp,
                pr.repeatPrescription

                FROM Prescription pr

                INNER JOIN Patient p 
                ON p.patientID=pr.patientID
                INNER JOIN Staff s 
                ON s.staffID=pr.staffID

                ORDER BY pr.prescriptionID

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
            <label for='search'>Search by Prescription, Patient or Staff Name:</label>
            <input type='text' name='search' placeholder='Search...' 
            value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <button class='search-btn' type='submit'>Search</button>
        </form>";

        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>Prescription ID</td>
                <td>Patient Name</td>
                <td>Staff Name</td>
                <td>Date Prescribed</td>
                <td>Date Collected</td>
                <td>Medication</td>
                <td>Dosage</td>
                <td>Next Pick-up</td>
                <td>Repeat Prescription</td>
                <td style='text-align: center' colspan='2'> Action </td>
            </tr>
        </thead>";

        while ($row = $result->fetch_assoc()){
            $prescriptionid = $row['prescriptionID'];
            $patientname = $row['PatientName'];
            $staffname = $row['StaffName'];
            $dateprescribed = $row['datePrescribed'];
            $dateCollected=$row['dateCollected'];
            $medication = $row['medication'];
            $dosage = $row['dosage'];
            $nextpickupdate = $row['nextPickUp'];
            $repeatprescription = $row['repeatPrescription'] ? 'Yes' : 'No';
            echo "
            <tbody>
                <tr>
                    <td>$prescriptionid</td>
                    <td>$patientname</td>
                    <td>$staffname</td>
                    <td>$dateprescribed</td>
                    <td>$dateCollected</td>
                    <td>$medication</td>
                    <td>$dosage</td>
                    <td>$nextpickupdate</td>
                    <td>$repeatprescription</td>
                    <td><a class='action-button' href='updatePrescription.php?prescriptionID=$prescriptionid'>Edit</a></td>
                    <td><a class='action-button' href='deletePrescription.php?prescriptionID=$prescriptionid'>Delete</a></td>
                </tr>
            </tbody>";
            } 
            echo "</table>";

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