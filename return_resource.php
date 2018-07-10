<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID']; 
$d_inc_id = $_GET['Incident_ID'];
$d_return_dt = $_GET['Returned_Date'];

$msg='Yes!! Resource_ID=['. $d_res_id .'] must be returned';

if(empty($d_res_id) || empty($d_inc_id) || empty($d_return_dt)) {
    $err_msg = "Resource_Id or Incident_ID Or Returned Date is empty. Cannot return the Resource.";
    include 'error.php';
} else {
    // Create connection
    $user = $_SESSION["session_user"];
    try {
        $con = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if (mysqli_connect_errno()) {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    
        // Update the Resource_Assignment
        $s_upd_assignment_query = "UPDATE Resource_Assignment 
                                SET Request_Status = 'RETURNED',
                                Returned_Date = CURDATE()
                                WHERE Incident_ID='$d_inc_id' AND Request_Status = 'Approved' AND Returned_Date='$d_return_dt' AND Resource_ID=".$d_res_id;
        // echo 'Executing the $s_upd_assignment_query = '.$s_upd_assignment_query;
        if (mysqli_query($con, $s_upd_assignment_query)) {
            // Update resource status.
            $s_upd_query = "UPDATE Resources SET Status = 'Available' WHERE Resource_ID=".$d_res_id;
            // echo 'Executing the Query = '.$s_upd_query;
            if(mysqli_query($con, $s_upd_query)) {
                $msg = "Successfully returned the Resource_ID:".$d_res_id .']';
            } else {
                $msg = "Error while updating the status for the Resource_ID:".$d_res_id;
            }
        } else{
          $msg = "Error while updating status in Resource_Assignment for the Resource_ID:".$d_res_id." and the Incident_ID: $d_inc_id";  
        }
        // close the connection.
        mysqli_close($con);
    } catch(Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}
?>
<div class="container">  
    <div id="contact">
        <center>
            <h4><?php echo $msg ?></h4>
            <button style="width:100px;" name="close" onclick="window.location.href='menu.php'">Close</button>
        </center>
    </div>
</div>
<?php
include 'footer.php';
?>
