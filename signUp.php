<?php
require_once("functions.php");
startSessionIfNotStarted();
require_once("db_connect.php");

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    echo "form submitted";

   $error= handleSignUp($_POST, $conn);

   if (!empty($error)){
    echo "<p style='color: red;'>$error</p>";
   }
}

// reference: https://www.w3schools.com/jsref/event_onchange.asp
function dropDownRoleForSignUp(){
    echo "<select name='roleID' id='roleID' onchange='toggleFields()' required>";
    echo "<option value=''>Select Role</option>";
    echo "<option value='1'>Patient</option>";
    echo "<option value='2'>Doctor</option>";
    echo "</select><br />";
}  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Sign Up Page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="login.css" />

    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<?php
// require_once("functions.php");
// require_once("db_connect.php");

// if ($_SERVER["REQUEST_METHOD"]=="POST"){
//     echo "form submitted";
//     exit();

//    $error= handleSignUp($_POST, $conn);

//    if (!empty($error)){
//     echo "<p style='color: red;'>$error</p>";
//    }
// }

// // reference: https://www.w3schools.com/jsref/event_onchange.asp
// function dropDownRoleForSignUp(){
//     echo "<select name='roleID' id='roleID' onchange='toggleFields()' required>";
//     echo "<option value=''>Select Role</option>";
//     echo "<option value='1'>Patient</option>";
//     echo "<option value='2'>Doctor</option>";
//     echo "</select><br />";
// }  

?>

<body>
    <div class="container" >
        <h1 class="centered-header">Sign Up Page: </h1>
        <div class="login">
            <div class="main" role="main">
                <form method="post" action="">
                    <label for='roleID'>Select role to sign up as: </label><br />
                    <?php dropDownRoleForSignUp(); ?>

                    <label for="FirstName">First Name: </label>
                    <input type="text" name="FirstName" placeholder="First Name" required>

                    <label for="MiddleName">Middle Name: </label>
                    <input type="text" name="MiddleName" placeholder="Middle Name" >

                    <label for="LastName">Last Name: </label>
                    <input type="text" name="LastName" placeholder="Last Name" required> 

                    <label for="lEmail">Email: </label>
                    <input type="email" name="lEmail" placeholder="example123@example.com" required>

                    <label for="lPhoneNo">Phone Number: </label>
                    <input type="text" name="lPhoneNo" placeholder="07123 456789" required>

                    <label for="DOB">Date Of Birth: </label>
                    <input type="date" id="DOB" name="DOB" required>

                    <label for="genderID">Gender: </label><br />
                    <?php dropDownGender();?><br />

                    <div id="patientFields" style="display: none;"> 
                        <label for="pAddress">Address: </label>
                        <input type="text" name="pAddress" placeholder="123 Example Street" >

                        <label for="pCity">City: </label>
                        <input type="text" name="pCity" placeholder="City" >

                        <label for="pPostcode">Postcode: </label>
                        <input type="text" name="pPostcode" placeholder="A1 2BC" >
                    </div>

                    <div id="doctorFields" style="display: none;">
                        <label for="doctorSpecialityID">Doctor Speciality: </label><br />
                        <?php smartDropDownDoctorSpecialityID($conn) ?><br />

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

                    <a href="logIn.php" class="left-link">Log In Instead?</a>
                </form>
            </div>
        </div>
    </div>

<script>
$(document).ready(function(){
    $('#doctorSpecialityDropDown').select2({
        width: '100%',
        placeholder: "Search for a doctor speciality",
        allowClear: true
    });
});

function toggleFields() {
    let role=document.getElementById('roleID').value;

    document.getElementById("patientFields").style.display="none";
    document.getElementById("doctorFields").style.display="none";

    if (role=="1"){
        document.getElementById("patientFields").style.display="block";
    } else if (role=="2"){
        document.getElementById("doctorFields").style.display="block";
    }
}

</script>
</body>
</html>