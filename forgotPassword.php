<?php
include("functions.php");
startSessionIfNotStarted();
require_once("db_connect.php");

if(!isset($_SESSION['email'])){
    header('Location: forgotPasswordQuestion.php');
    exit();
}

if(isset($_POST['newPasswordComplete'])){
    $newPassword=$_POST['newPassword'];
    $confirmPassword=$_POST['ConfirmNewPassword'];

    if($newPassword!=$confirmPassword){
        $error="Passwords do not match.";
    } else{

        $stmt=$conn->prepare("SELECT lUsername
            FROM Login
            WHERE lEmail=?
        ");
        $stmt->bind_param("s", $_SESSION['email']);
        $stmt->execute();

        $result=$stmt->get_result();
        $row=$result->fetch_assoc();
        $username=$row['lUsername'];

        $stmt->close();

        $hashedPassword=hashPassword($confirmPassword);

        $stmt=$conn->prepare("UPDATE Login
            SET lHashPassword=?
            WHERE lEmail=?
        ");
        $stmt->bind_param("ss", $hashedPassword, $_SESSION['email']);

        if($stmt->execute()){
            unset($_SESSION['email']);

            echo "<script>
                    alert('Password successfully updated! Your username is: $username.');
                    window.location.href='logIn.php';
                </script>";
        } else{
            $error="Error updating password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Forgot Password page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="login.css" />
</head>

<body>
    <div class="container" >
        <h1 class="centered-header">Re-Enter Password: </h1>
        <div class="login">
            <div class="main" role="main">
                <form method="post">
                    <!-- in php get the two passwords to match -->
                    <label for="newPassword">Enter New Password: </label>
                    <input type="password" name="newPassword" placeholder="New Password" required>
                    <label for="newPassword">Confirm New Password: </label>
                    <input type="password" name="ConfirmNewPassword" placeholder="Confirm New Password" required>
                    <?php if (!empty($error)): ?>
                        <p style="color: red;"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <input type="submit" name="newPasswordComplete" value="New Password">
                </form>
            </div>
        </div>
    </div>
</body>
</html>