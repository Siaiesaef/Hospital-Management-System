<?php
require_once("functions.php");
startSessionIfNotStarted();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="View Doctor Records Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Doctor Records</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <?php
    include("adminNavBar.php");
    ?>
    <div class="main" role="main">
        <div>
            <h2 class="centered-header">Doctor Records</h2>
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
                FROM Staff s

                LEFT JOIN StaffType st
                ON st.staffTypeID=s.staffTypeID

                WHERE roleID=2 AND(
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    s.sDOB LIKE ? OR
                    st.stSpeciality LIKE ? 
                )
            ");
            $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
            $stmt->execute();
            $total_result = $stmt->get_result();
            $total_row = $total_result->fetch_assoc();

            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                s.staffID,
                g.gGender AS Gender,
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) 
                AS StaffName,
                st.stSpeciality,
                s.sDOB 

                FROM Staff s

                INNER JOIN Gender g 
                ON g.genderID=s.genderID

                LEFT JOIN StaffType st
                ON st.staffTypeID=s.staffTypeID
                
                WHERE roleID=2 AND(
                    CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) LIKE ? OR
                    s.sDOB LIKE ? OR
                    st.stSpeciality LIKE ?
                )

                ORDER BY staffID

                LIMIT ?
                OFFSET ?
            ");

            $stmt->bind_param("sssii", $searchParam, $searchParam, $searchParam, $records_per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
        } else{

            $stmt=$conn->prepare("SELECT 
                COUNT(*) AS total 
                FROM Staff 
                WHERE roleID=2
            ");

            $stmt->execute();
            $total_result=$stmt->get_result();
            $total_row = $total_result->fetch_assoc();
            $total_records = $total_row['total'];

            $stmt =$conn->prepare("SELECT 
                s.staffID,
                g.gGender AS Gender,
                CONCAT_WS(' ', s.sFirstName, s.sMiddleName, s.sLastName) AS StaffName,
                st.stSpeciality,
                s.sDOB 

                FROM Staff s

                INNER JOIN Gender g 
                ON g.genderID=s.genderID

                LEFT JOIN StaffType st
                ON st.staffTypeID=s.staffTypeID
                
                WHERE roleID=2
                ORDER BY staffID

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
            <label for='search'>Search by DOB, Speciality, First or Last Name:</label>
            <input type='text' name='search' placeholder='Search...' 
            value='" . htmlspecialchars($_GET['search'] ?? '') . "'>
            <button class='search-btn' type='submit'>Search</button>
        </form>";

        echo "<table>";
        echo "
        <thead>
            <tr> 
                <td>Doctor ID</td>
                <td>Gender</td>
                <td>Doctor Name</td>
                <td>Date of Birth</td>
                <td>Doctor Speciality</td>
                <td style='text-align: center' colspan='2'> Action </td>
            </tr>
        </thead>";

        while ($row = $result->fetch_assoc()){
            $staffid = $row['staffID'];
            $genderid = $row['Gender'];
            $staffname = $row['StaffName'];
            $sDOB = $row['sDOB'];
            $speciality = $row['stSpeciality'];
            echo "
            <tbody>
                <tr>
                    <td>$staffid</td>
                    <td>$genderid</td>
                    <td>$staffname</td>
                    <td>$sDOB</td>
                    <td>$speciality</td>
                    <td><a class='action-button' href='updateDoctor.php?staffID=$staffid'>Edit</a></td>
                    <td><a class='action-button' href='deleteDoctor.php?staffID=$staffid'>Delete</a></td>
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