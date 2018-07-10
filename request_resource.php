<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID'];
$d_inc_id = $_GET['Incident_ID'];

$msg='Yes!! Resource_ID=['. $d_res_id .'] is requested by the Incident_ID='.$d_inc_id;

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
        
        // See if the Resource has been assigned to the Incident before
        $s_query = "SELECT Resource_ID, Incident_ID, Request_Status FROM Resource_Assignment WHERE Resource_ID=$d_res_id AND Incident_ID=$d_inc_id";
        
        $s_results = $con->query($s_query);
        if ($s_results->num_rows <= 0) {
            // INSERT into Resource_Assignment log    
            $s_ins_query = "INSERT INTO Resource_Assignment (Resource_ID, Incident_ID, Request_Status, Start_Date, Returned_Date)
                        VALUES(".$d_res_id.",".$d_inc_id.", 'Pending', CURDATE(), DATE_ADD(CURDATE(), INTERVAL +2 DAY) )";
            // echo 'Executing the Query = '.$s_ins_query;    
            if (mysqli_query($con, $s_ins_query)) {
                $msg = "Request for the Resource_ID[".$d_res_id."] is submitted successfully." ;
            } else{
              $msg = "Error while inserting record in Resource_Assignment for the Resource_ID:".$d_res_id;  
            }
         } else {
            if ($s_results->num_rows == 1) {
                while($row = $s_results->fetch_assoc()) {
                    $res_status = $row["Request_Status"];
                }
            }
            if(strcasecmp("Rejected", $res_status) == 0) {
                $s_upd_query = "UPDATE Resource_Assignment
                                SET Request_Status='Pending',
                                    Start_Date=CURDATE(),
                                    Returned_Date=DATE_ADD(CURDATE(), INTERVAL +2 DAY)
                                WHERE Resource_ID=$d_res_id AND Incident_ID=$d_inc_id";
                // echo 'Executing the Query = '.$s_upd_query;    
                if (mysqli_query($con, $s_upd_query)) {
                    $msg = "Request for the Resource_ID[".$d_res_id."] is submitted successfully." ;
                } else{
                  $msg = "Error while updating the record in Resource_Assignment for the Resource_ID:".$d_res_id;  
                }
            } else {
                $msg = "The Resource_ID [$d_res_id] has been returned by the Incident:$d_inc_id before. Cannot be requested again!";    
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
            <button style="width:100px;" name="close" onclick="window.location.href='search.php'">Close</button>
        </center>
    </div>
</div>
<?php
include 'footer.php';
?>