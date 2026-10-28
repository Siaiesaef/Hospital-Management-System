<?php
require_once("functions.php");
startSessionIfNotStarted();

$patientID=$_SESSION['patientID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View My Medication Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Medication</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("patientNavBar.php");
    ?>
    <div class="main" role="main">
        <div>
            <h2 class="centered-header">My Medication</h2>
        </div>
        <?php
        require_once('db_connect.php');
        $db=dbConnection();
        //pagination variables
        $records_per_page=4;
        $current_page=isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($current_page<1) $current_page=1;

        $offset=($current_page-1)*$records_per_page;
        
        $total_query="SELECT COUNT(*) AS total FROM Prescription WHERE patientID=?";
        $total_stmt=$conn->prepare($total_query);
        $total_stmt->bind_param("i", $patientID);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_row = $total_result->fetch_assoc();

        $total_records = $total_row['total'];
        if($total_records==0){
            echo "<h2 class='centered-header'>You have no Medication!</h2>";
        }else{
            $total_pages = ceil($total_records / $records_per_page);

            $select_query = "SELECT 
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
                INNER JOIN Patient p ON p.patientID=pr.patientID
                INNER JOIN Staff s ON s.staffID=pr.staffID
                WHERE pr.patientID=?
                ORDER BY pr.prescriptionID
                LIMIT ?
                OFFSET ?";

            $stmt = $conn->prepare($select_query);
            $stmt->bind_param("iii", $patientID, $records_per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
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
                    <td>Action</td>
                </tr>
            </thead>";

            while ($row = $result->fetch_assoc()){
                $prescriptionid = $row['prescriptionID'];
                $patientname = $row['PatientName'];
                $staffname = $row['StaffName'];
                $dateprescribed = $row['datePrescribed'];
                $datecollected = $row['dateCollected'];
                $medication = $row['medication'];
                $dosage = $row['dosage'];
                $nextpickupdate = $row['nextPickUp'];
                $repeatprescription = $row['repeatPrescription'] ? 'Yes' : 'No';
                
                echo "<tbody>";
                echo "<tr>";
                        
                echo "<td>$prescriptionid</td>";
                echo "<td>$patientname</td>";
                echo "<td>$staffname</td>";
                echo "<td>$dateprescribed</td>";
                echo "<td>$datecollected</td>";
                echo "<td>$medication</td>";
                echo "<td>$dosage</td>";
                echo "<td>$nextpickupdate</td>";
                echo "<td>$repeatprescription</td>";

                if ($repeatprescription=='Yes')
                    {
                        echo "<td>
                                <a class='action-button' href='repeatPrescription.php?prescriptionID=$prescriptionid'>
                                    Repeat
                                </a>
                            </td>";
                    }

                echo "</tr>";
                echo "</tbody>";

            } 
            echo "</table>";

            //pagination controls
            echo"<div class='pagination'>";
            if ($current_page > 1) {
            $prev_page = $current_page - 1;
            echo "<a href='?page=$prev_page'>Previous</a>";
            }
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    echo "<strong>$i</strong>";
                } else {
                    echo "<a href='?page=$i'>$i</a>";
                }
            }
            if ($current_page < $total_pages) {
                $next_page = $current_page + 1;
                echo "<a href='?page=$next_page'>Next</a>";
            }
            echo "</div>";
        }
        
        $db->close();
        ?>
    </div>
</body>
</html>