<?php

// TESTING SESSION THING
session_start();


$servername = "localhost";
$username = "ayazhan";
$password = "";
$dbname = "ERMS";
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ERMS</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    
<?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form

    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];


    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT Username FROM User WHERE Username = '$myusername' and Password = '$mypassword'";

    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $_SESSION["session_user"] = $myusername;
        // $cookie_name = "user";
        // $cookie_value = $myusername;
        // setcookie($cookie_name, $cookie_value, time() + (86400), "/"); // 86400 = 1 day
        header("location: menu.php");
    }
    else{
        ?>
        <div class="container">
            <form id="contact" action="" method="post">
                <h3>Login to ERMS</h3>
                <h4 style="color:red;">Invalid UserId/Password! Please check and try again</h4>
                <h4>Emergency Notification Management System</h4>
                <fieldset>
                    <input placeholder="Username" name="username" type="text" tabindex="1" required autofocus>
                </fieldset>
                <fieldset>
                    <input placeholder="Password" type="text" name = "password" tabindex="2" required>
                </fieldset>
                <fieldset>
                    <button type="submit">Login</button>
                </fieldset>
            </form>
        </div>
        <?php
    }
}
else{
    ?>

    <div class="container">
        <form id="contact" action="" method="post">
            <h3>Login to ERMS</h3>
            <h4>Emergency Notification Management System</h4>
            <fieldset>
                <input placeholder="Username" name="username" type="text" tabindex="1" required autofocus>
            </fieldset>
            <fieldset>
                <input placeholder="Password" type="text" name = "password" tabindex="2" required>
            </fieldset>
            <fieldset>
                <button type="submit">Login</button>
            </fieldset>
        </form>
    </div>

    <?php
}
include 'footer.php';
?>