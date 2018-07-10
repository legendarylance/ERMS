<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID']; 
$d_res_repair_start_date = $_GET['Start_Date']; 

$msg='Yes!! Resource_ID=['. $d_res_id .'] must be cancelled from repair.';

if(empty($d_res_id)) {
    $err_msg = "Resource_Id is empty. Cannot cancel the Repair schedule.";
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
    
        // Update the Repair
        $s_upd_repair_query = "DELETE FROM Repair WHERE Resource_ID=".$d_res_id;
                    
        //echo 'Executing the Query = '.$s_ins_query;    
        if (mysqli_query($con, $s_upd_repair_query)) {
            // Update resource status.
            $msg = "Successfully cancelled the repair request for Resource_ID:".$d_res_id .'';
        } else{
          $msg = "Error while cancelled the repair request for the Resource_ID:".$d_res_id;  
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
