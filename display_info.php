<?php

$user = $_SESSION["session_user"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// get the user id from login screen and save in cookies maybe...
$sql_type = "SELECT Type FROM User WHERE Username='$user'";
$result_type = $conn->query($sql_type);
if ($result_type->num_rows == 1) {
    while($row = $result_type->fetch_assoc()) {
        $user_type = $row["Type"];
    }
}
else
{
    $user_type = 2; // setting default value, need to do something here for error case
}

// from user and
switch ($user_type) {
    // change this setting to get intersect
    // brute force, don't judge me :P
    case 1:
        $sql = "SELECT First_Name, Last_Name, Job_Title FROM Individual WHERE Username='$user'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $firtPar = $row["First_Name"] ." ". $row["Last_Name"];
                $secondPar = "Job Title: " . $row["Job_Title"];
            }

        }
        break;
    case 2:
        $sql = "SELECT Name, Population_Size FROM Municipal WHERE Username='$user'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $firtPar = $row["Name"];
                $secondPar = "Population: " . $row["Population_Size"];
            }

        }
        break;
    case 3:
        $sql = "SELECT Name, Jurisdiction FROM Government WHERE Username='$user'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $firtPar = $row["Name"];
                $secondPar = "Jurisdiction: " . $row["Jurisdiction"];
            }

        }
        break;
    case 4:
        $sql = "SELECT Name, Headquarters_Location FROM Company WHERE Username='$user'";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $firtPar = $row["Name"];
                $secondPar = "Headquarters: " . $row["Headquarters_Location"];
            }

        }
        break;
    default:
        $firtPar = "Unknown";
        $secondPar = "Unknown";
}

?>



<?php 

// OLD CODE


// // Dealing with Cookies
// if(!isset($_COOKIE['user'])) {
//     header("location: login.php");
// } else {
//     $user = $_COOKIE['user'];
// }
?>