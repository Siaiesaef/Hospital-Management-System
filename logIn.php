<?php
require_once("functions.php");
startSessionIfNotStarted();
require_once("db_connect.php");

$error="";

if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $username=$_POST['lUsername'];
    $password=$_POST['Password'];

    $stmt=$conn->prepare("SELECT 
        l.*,
        p.patientID,
        p.pFirstName,
        p.pLastName,
        s.staffID,
        s.sFirstName,
        s.sLastName 

        FROM login  l

        LEFT JOIN Patient p 
        ON l.patientID=p.patientID

        LEFT JOIN Staff s
        ON l.staffID=s.staffID

        WHERE lUsername=?");

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result=$stmt->get_result();
    $login=$result->fetch_assoc();

    if ($login && password_verify($password, $login['lHashPassword'])){
        $_SESSION['role']=$login['roleID'];
        $_SESSION['userID']=$login['loginID'];
        $_SESSION['username']=$login['lUsername'];
        if ($login['roleID']=="1"){
            $_SESSION['patientID']=$login['patientID'];
            $_SESSION['patientName']=$login['pFirstName'];

            header("Location: patientNavBar.php");

        } elseif($login['roleID']=="2"){

            $_SESSION['staffID']=$login['staffID'];
            $_SESSION['staffName']=$login['sFirstName'] . " " . $login['sLastName'];
            $_SESSION['sLastName']=$login['sLastName'];

            header("Location: doctorNavBar.php");

        } elseif($login['roleID']=="3"){
            $_SESSION['staffID']=$login['staffID'];
            $_SESSION['staffName']=$login['sFirstName'];

            header("Location: adminNavBar.php");

        }

        exit();
    } else{
        $error="Invalid Login Credentials"; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Log In Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="container">
        <h1 class="centered-header">Log In Page: </h1>
        <div class="login">
            <div class="main" role="main">
                <form method="post">
                    <?php if (!empty($error)): ?>
                        <p class="color: red; margin-bottom:10px;">
                            <?php echo $error; ?>
                        </p>
                    <?php endif; ?>

                    <label for="lUsername">Username: </label>
                    <input type="text" name="lUsername" placeholder="Username" required>
                    <label for="Password">Password: </label>
                    <input type="password" name="Password" placeholder="Password" required>
                    <input type="submit" name="logIn" value="Log In">
                    <a href="signUp.php" class="left-link">Sign Up?</a>
                    <a href="forgotPasswordQuestion.php" class="right-link">Forgot Password?</a>
                </form>
            </div>
        <div>
    </div>
</body>
</html>