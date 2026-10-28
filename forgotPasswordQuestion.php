<?php
include('functions.php');
startSessionIfNotStarted();
require_once('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Forgot Password Question page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Question</title>
    <link rel="stylesheet" href="login.css" />
</head>
<?php

function getUsernameAndForgotPassword($email, $conn){

    $stmt=$conn->prepare("SELECT 
        lEmail,
        lMemorableInfo
        FROM Login
        WHERE lEmail=?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if (isset($_POST['enterInfo'])){
    $email=$_POST['lEmail'];

    $user=getUsernameAndForgotPassword($email, $conn);
    if ($user && password_verify($_POST['memorableInfo'], $user['lMemorableInfo'])){
        $_SESSION['email']=$email;

        header("Location: forgotPassword.php");
        exit();
    } else {
        $error = "Incorrect email or memorable answer.";
    }
}

?>
<body>
    <div class="container">
        <h1 class="centered-header">Forgot Password/Username Question: </h1>
        <div class="login">
            <div class="main" role="main">
                <form method="post">
                    <label for="lEmail">Email: </label>
                    <input type="text" name="lEmail" placeholder="user@example.com" required>
                    <label for="memorableInfo">What's your mother's name? </label>
                    <input type="text" name="memorableInfo" placeholder="••••••••" required>
                    <input type="submit" name="enterInfo" value="Enter Information">

                    <?php if (!empty($error)): ?>
                        <p style="color:red;"><?php echo $error; ?></p>
                    <?php endif; ?>
                    <a href="logIn.php" class="left-link">Log In?</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>