<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID'];
$d_inc_id = $_GET['Incident_ID'];

//$msg='Yes!! Requests for the Resource_ID=['. $d_res_id .'] and Incident_ID=['. $d_inc_id .'] must be cancelled.';

if(empty($d_res_id) || empty($d_inc_id)) {
    $err_msg = "Resource_Id or Incident_ID is empty. Cannot perform any action.";
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
                                SET Request_Status = 'Rejected' 
                                WHERE Incident_ID='$d_inc_id' AND Request_Status = 'Pending' AND Resource_ID=".$d_res_id;
                    
        //echo 'Executing the Query = '.$s_ins_query;    
        if (mysqli_query($con, $s_upd_assignment_query)) {
            $msg = "Successfully rejected the request for Resource_ID:".$d_res_id ." and the Incident: ".$d_inc_id;
        } else{
          $msg = "Error while rejecting the request for Resource_ID:".$d_res_id." and the Incident:".$d_inc_id;
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
