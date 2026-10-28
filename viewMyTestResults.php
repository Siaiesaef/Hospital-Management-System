<?php
require_once("functions.php");
startSessionIfNotStarted();

$patientID=$_SESSION['patientID'];
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
    <?php
    include("patientNavBar.php");
    ?>
    <div class="main" role="main">
        <div>
            <h1 class="centered-header">My Test Results</h1>
        </div>
        <?php
        require_once('db_connect.php');
        //pagination variables
        $records_per_page=4;
        $current_page=isset($_GET['page']) ? intval($_GET['page']) : 1;
        if ($current_page<1) $current_page=1;

        $offset=($current_page-1)*$records_per_page;
        
        $total_query="SELECT COUNT(*) AS total FROM TestResults WHERE patientID=?";
        $total_stmt=$conn->prepare($total_query);
        $total_stmt->bind_param("i", $patientID);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_row = $total_result->fetch_assoc();

        $total_records = $total_row['total'];
        if ($total_records==0){
            echo "<h2 class='centered-header'>You have no test results!</h2>";
        } else{
            $total_pages = ceil($total_records / $records_per_page);

            $select_query = "SELECT 
                tr.testResultsID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName)
                AS StaffName,
                tr.testResults, 
                tr.dateTested
                FROM TestResults tr
                INNER JOIN Patient p ON p.patientID=tr.patientID
                INNER JOIN Staff s ON s.staffID=tr.staffID
                WHERE tr.patientID=?
                ORDER BY tr.testResultsID
                LIMIT ? OFFSET ?";

            $stmt = $conn->prepare($select_query);
            $stmt->bind_param("iii", $patientID, $records_per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            echo "<table>";
            echo "
            <thead>
                <tr> 
                    <td>Test Result ID</td>
                    <td>Patient Name</td>
                    <td>Staff Name</td>
                    <td>Test Results</td>
                    <td>Date Tested</td>
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
                    </tr>
                </tbody>";
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

        $conn->close();
        ?>
    </div>
</body>
</html>