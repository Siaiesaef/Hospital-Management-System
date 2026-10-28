<?php
require_once("functions.php");
startSessionIfNotStarted();

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 3; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View Patient Records Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Records</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    if ($isAdmin) {
        include("adminNavBar.php");
    } else {
        include("doctorNavBar.php");
    }
    ?>
    <div class="main" role="main">
        <div>
        <h2 class="centered-header">Patient Records</h2>
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
                FROM Patient
                WHERE  
                    CONCAT_WS(' ', pFirstName, pMiddleName, pLastName) LIKE ? OR
                    pDOB LIKE ? OR
                    pCity LIKE ? OR 
                    pPostcode LIKE ?  
            ");

            $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();
            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                p.patientID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                p.pDOB, 
                g.gGender AS Gender, 
                p.pAddress, 
                p.pCity,
                p.pPostcode

                FROM Patient p

                INNER JOIN Gender g 
                ON g.genderID=p.GenderID

                WHERE 
                    CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName) LIKE ? OR
                    p.pDOB LIKE ? OR
                    p.pCity LIKE ? OR 
                    p.pPostcode LIKE ?

                ORDER BY p.patientID

                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("ssssii", $searchParam, $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
            $stmt->execute();
            $result=$stmt->get_result();

        } else{

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM Patient
            ");
            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                p.patientID, 
                CONCAT_WS(' ', p.pFirstName, p.pMiddleName, p.pLastName)
                AS PatientName, 
                p.pDOB, 
                g.gGender AS Gender, 
                p.pAddress, 
                p.pCity,
                p.pPostcode

                FROM Patient p

                INNER JOIN Gender g 
                ON g.genderID=p.GenderID

                ORDER BY p.patientID

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
            <label for='search'>Search by DOB, City, Postcode, First or Last Name:</label>
            <input type='text' name='search' placeholder='Search...' 
            value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <button class='search-btn' type='submit'>Search</button>
        </form>";

        echo "<table>";

        echo "<thead>";
        echo "<tr>";

        echo "<td>Patient ID</td>";
        echo "<td>Patient Name</td>";
        echo "<td>Date Of Birth</td>";
        echo "<td>Gender</td>";
        echo "<td>Address</td>";
        echo "<td>City</td>";
        echo "<td>Postcode</td>";

        if ($isAdmin) {
            echo "<td style='text-align: center' colspan='2'> Action </td>";
        }

        echo "</tr>";
        echo "</thead>";

        while ($row = $result->fetch_assoc()){
            $patientid = $row['patientID'];
            $patientname = $row['PatientName'];
            $dob = $row['pDOB'];
            $gender = $row['Gender'];
            $address = $row['pAddress'];
            $city = $row['pCity'];
            $postcode = $row['pPostcode'];

            echo "<tbody>";
            echo "<tr>";

            echo "<td>$patientid</td>";
            echo "<td>$patientname</td>";
            echo "<td>$dob</td>";
            echo "<td>$gender</td>";
            echo "<td>$address</td>";
            echo "<td>$city</td>";
            echo "<td>$postcode</td>";
            if ($isAdmin){
                echo "<td>
                        <a class='action-button' href='updatePatient.php?patientID=$patientid'>
                            Edit
                        </a>
                    </td>";

                echo "<td>
                        <a class='action-button' href='deletePatient.php?patientID=$patientid'>
                            Delete
                        </a>
                    </td>";
            }
            echo"</tr>";
            echo"</tbody>";
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