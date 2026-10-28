<?php
require_once("functions.php");
startSessionIfNotStarted();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Admin Sign Up Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up Page</title>
    <link rel="stylesheet" href="login.css" />
</head>

<?php
require_once("db_connect.php");
include("adminNavBar.php");

if (isset($_POST['SignUp'])){

   $result= handleSignUp($_POST,$conn);

   if ($result==true){
        echo "Success!";
   }else{
        $error = $result;
   }
}

?>

<body>
    <div class="login">
        <div class="main" role="main">
            <h1 class="centered-header">Sign Up Page: </h1>
            <form method="post">

                <div class="no-select-2">
                    <label for='roleID'>Role: </label><br />
                    <select id="roleID" name="roleID" required>
                        <option value="3">Admin</option>
                    </select><br />
                </div>

                <label for="FirstName">First Name: </label>
                <input type="text" name="FirstName" placeholder="First Name" required>

                <label for="MiddleName">Middle Name: </label>
                <input type="text" name="MiddleName" placeholder="Middle Name" required>

                <label for="LastName">Last Name: </label>
                <input type="text" name="LastName" placeholder="Last Name" required> 

                <label for="lEmail">Email: </label>
                <input type="email" name="lEmail" placeholder="example123@example.com" required>

                <label for="lPhoneNo">Phone Number: </label>
                <input type="text" name="lPhoneNo" placeholder="07123 456789" required>

                <label for="DOB">Date Of Birth: </label>
                <input type="date" id="DOB" name="DOB" required>

                <div class="no-select-2">
                    <label for="genderID">Gender: </label><br />
                    <?php dropDownGender();?><br />
                </div>

                <label for="lMemorableInfo">What's your mother's name? </label>
                <input type="text" name="lMemorableInfo" placeholder="••••••••" required>

                <label for="password">Password: </label>
                <input type="password" name="password" placeholder="••••••••" required>

                <label for="confirmPassword">Confirm Password: </label>
                <input type="password" name="confirmPassword" placeholder="••••••••" required>

                <?php if (!empty($error)): ?>
                    <p class="color: red; margin-bottom:10px;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <input type="submit" name="SignUp" value="Create Account">
            </form>
        </div>
    </div>
</body>
</html>