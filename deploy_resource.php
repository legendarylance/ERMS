<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID'];
$d_inc_id = $_GET['Incident_ID'];
$d_action = $_GET['action'];
$d_flag = true;

if(empty($d_res_id) || empty($d_inc_id)) {
    $err_msg = "Resource_Id or Incident_ID is empty. Cannot deploy the resource.";
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
    
        // INSERT into Resource_Assignment log    
        if(strcasecmp("insert", $d_action) == 0) {
            $s_ins_query = "INSERT INTO Resource_Assignment (Resource_ID, Incident_ID, Request_Status, Start_Date, Returned_Date)
                    VALUES(".$d_res_id.",".$d_inc_id.", 'Approved', CURDATE(), DATE_ADD(CURDATE(), INTERVAL +2 DAY) )";    
                    
                    
            // See if the Resource has been assigned to the Incident before
            $s_query = "SELECT Resource_ID, Incident_ID FROM Resource_Assignment WHERE Resource_ID=$d_res_id AND Incident_ID=$d_inc_id";
            
            $s_results = $con->query($s_query);
            if ($s_results->num_rows > 0) { 
                $msg = "The Resource_ID [$d_res_id] has been returned by the Incident:$d_inc_id before. Cannot be requested again!";  
                $d_flag = false;
            }
        } else {
            $s_ins_query = "UPDATE Resource_Assignment 
                            SET Request_Status = 'Approved'
                            WHERE Resource_ID = $d_res_id AND Incident_ID =$d_inc_id";
        }
        
        if($d_flag) {
            // echo 'Executing the Query = '.$s_ins_query;    
            if (mysqli_query($con, $s_ins_query)) {
                // Update resource status.
                $s_upd_query = "UPDATE Resources SET Status = 'In Use' WHERE Resource_ID=".$d_res_id;
                // echo 'Executing the Query = '.$s_upd_query;
                if(mysqli_query($con, $s_upd_query)) {
                    $msg = "Successfully deployed the Resource_ID:".$d_res_id .'] to Incident_ID=['.$d_inc_id.']';
                } else {
                    $msg = "Error while updating the status for the Resource_ID:".$d_res_id;
                }
            } else{
              $msg = "Error while inserting record in Resource_Assignment for the Resource_ID:".$d_res_id;  
            }
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
            <button style="width:100px;" name="close" type="button" onclick="window.location.href='ERMS/menu.php'">Close</button>
        </center>
    </div>
</div>
<?php
include 'footer.php';
?>
