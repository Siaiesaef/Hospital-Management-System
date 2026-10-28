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
    <meta name="description" content="View Test Results Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Test Results</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">Test Results</h1>
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

                FROM TestResults tr

                INNER JOIN Patient p 
                ON p.patientID=tr.patientID
                INNER JOIN Staff s 
                ON s.staffID=tr.staffID

                WHERE 
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ?
                    OR CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ?
                    OR tr.testResults LIKE ?
                    OR tr.dateTested LIKE ?
            ");

            $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();
            $total_records = $total_row['total'];

            $stmt = $conn->prepare("SELECT 
                tr.testResultsID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
                AS StaffName,
                tr.testResults, 
                tr.dateTested

                FROM TestResults tr

                INNER JOIN Patient p 
                ON p.patientID=tr.patientID
                INNER JOIN Staff s 
                ON s.staffID=tr.staffID

                WHERE 
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ?
                    OR CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ?
                    OR tr.testResults LIKE ?
                    OR tr.dateTested LIKE ?

                ORDER BY tr.testResultsID

                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("ssssii", $searchParam, $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
            $stmt->execute();
            $result=$stmt->get_result();
        } else{

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM TestResults
            ");

            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt = $conn->prepare("SELECT 
                tr.testResultsID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
                AS StaffName,
                tr.testResults, 
                tr.dateTested

                FROM TestResults tr

                INNER JOIN Patient p 
                ON p.patientID=tr.patientID
                INNER JOIN Staff s 
                ON s.staffID=tr.staffID

                ORDER BY tr.testResultsID

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
            <label for='search'>Search by Patient/Staff Name, Test Results or Date Tested:</label>
            <input type='text' name='search' placeholder='Search...' 
            value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <button class='search-btn' type='submit'>Search</button>
        </form>";

        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>Test Result ID</td>
                <td>Patient Name</td>
                <td>Staff Name</td>
                <td>Test Results</td>
                <td>Date Tested</td>
                <td style='text-align: center' colspan='2'> Action </td>
            </tr>
        </thead>";

        while ($row = $result->fetch_assoc()){
            $testresultid = $row['testResultsID'];
            $patientname = $row['PatientName'];
            $staffname = $row['StaffName'];
            $testresults = $row['testResults'];
            $datetested = $row['dateTested'];
            echo "
            <tbody>
                <tr>
                    <td>$testresultid</td>
                    <td>$patientname</td>
                    <td>$staffname</td>
                    <td>$testresults</td>
                    <td>$datetested</td>
                    <td><a class='action-button' href='updateTestResult.php?testResultID=$testresultid'>Edit</a></td>
                    <td><a class='action-button' href='deleteTestResult.php?testResultID=$testresultid'>Delete</a></td>
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