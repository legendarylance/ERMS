<?php
include 'header.php';

$d_res_id = $_GET['Resource_ID']; 
$d_res_return_date = 'NO_DATE'; 
$d_res_return_date_from_search = $_GET['Returned_Date'];

if(empty($d_res_id)) {
    $err_msg = "Resource_Id is empty. Cannot deploy the resource.";
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
        
        $is_res_available = strcasecmp("NOW", $d_res_return_date_from_search);
    
        if($is_res_available == 0) {
            $s_ins_query = "INSERT INTO Repair (Resource_ID, N_DAYS, Start_Date, Returned_Date)
                    VALUES(".$d_res_id.", 15, CURDATE(), DATE_ADD(CURDATE(), INTERVAL +15 DAY) )"; 
        } else {
            $s_ins_query = "INSERT INTO Repair (Resource_ID, N_DAYS, Start_Date, Returned_Date)
                    VALUES(".$d_res_id.", 15, DATE_ADD('$d_res_return_date_from_search', INTERVAL +1 DAY), DATE_ADD('$d_res_return_date_from_search', INTERVAL +15 DAY) )";  
        }
        
        // echo 'Executing the Query = '.$s_ins_query;    
        if (mysqli_query($con, $s_ins_query)) {
            if($is_res_available == 0) {
                // Update resource status only if it is not In Use.
                $s_upd_query = "UPDATE Resources SET Status = 'In Repair' WHERE Resource_ID=".$d_res_id;
                // echo 'Executing the Query = '.$s_upd_query;
                if(mysqli_query($con, $s_upd_query)) {
                    $msg = "Successfully saved the repair request for Resource_ID:".$d_res_id .']';
                } else {
                    $msg = "Error while updating the status for the Resource_ID:".$d_res_id;
                }
            } else {
                $msg = "Successfully saved the repair request for Resource_ID:".$d_res_id .']';
            }
        } else{
          $msg = "Error while inserting record in Resource_Assignment for the Resource_ID:".$d_res_id;  
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
