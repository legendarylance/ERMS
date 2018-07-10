<?php
include 'header.php';

// Create connection
$user = $_SESSION["session_user"];
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<div class="container2">  
  <div id="contact">
    <h3>Resource Status</h3>
    <h4 style="font-weight: bold;">Resources in Use</h4>
    
    <table>
    	<tr>
        	<th>ID</th>
        	<th>Resource Name</th>
        	<th>Incident</th>
        	<th>Owner</th>
        	<th>Start Date</th>
        	<th>Return by</th>
        	<th>Action</th>            
        </tr>
            <?php 
                $s_query = "SELECT Resources.Resource_ID, Resources.Resource_Name, Incident.Description, Incident.Incident_ID, Resource_Assignment.Start_Date, 
                            Resource_Assignment.Returned_Date, (CASE 
                                WHEN User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                                WHEN User.Type in ('2') then Municipal.Name
                                WHEN User.type in ('3') then Government.Name
                                WHEN User.type in ('4') then Company.Name
                                else NULL END
                            ) AS Owner
	
                            FROM Resources
                            LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                            LEFT JOIN Incident ON Resource_Assignment.Incident_ID = Incident.Incident_ID
                            LEFT JOIN User ON Resources.Owner = User.Username
                            LEFT JOIN Individual ON Resources.Owner = Individual.Username
                            LEFT JOIN Municipal ON Resources.Owner = Municipal.Username
                            LEFT JOIN Government ON Resources.Owner = Government.Username
                            LEFT JOIN Company ON Resources.Owner = Company.Username
                            WHERE 
                                Resources.Status in ('In Use') AND Incident.Owner = '$user'";
                            
                // EXECUTE THE SEARCH QUERY.
                
                $s_results = $conn->query($s_query);
                 if ($s_results->num_rows > 0) {
                    while($row = $s_results->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>'.$row["Resource_ID"].'</td>';
                        echo '<td>'.$row["Resource_Name"].'</td>';
                        echo '<td>'.$row["Description"].'</td>';
                        echo '<td>'.$row["Owner"].'</td>';
                        echo '<td>'.$row["Start_Date"].'</td>';
                        echo '<td>'.$row["Returned_Date"].'</td>';
                        echo '<td><button onclick="window.location.href=\'return_resource.php?Resource_ID='.$row["Resource_ID"].
                                        '&Incident_ID='.$row["Incident_ID"].'&Returned_Date='.$row["Returned_Date"].'\'">Return</button></td>';
                        echo '</tr>';
                    }
                 } else {
                     echo '<tr><td colspan="7">No Results found.</td></tr>';
                 }
            ?>
    </table>
    
    <h4 style="font-weight: bold;">Resources Requested by Me</h4>    
    <table>
    	<tr>
        	<th>ID</th>
        	<th>Resource Name</th>
        	<th>Incident</th>
        	<th>Owner</th>
        	<th>Return by</th>
        	<th>Action</th>            
        </tr>
        <?php 
            $s_query = "SELECT Resources.Resource_ID, Resources.Resource_Name, Incident.Description, Incident.Incident_ID, Resource_Assignment.Returned_Date,
                        (CASE 
                            WHEN User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                            WHEN User.Type in ('2') then Municipal.Name
                            WHEN User.type in ('3') then Government.Name
                            WHEN User.type in ('4') then Company.Name
                            ELSE NULL END
                        ) as Owner 	
                        	
                        FROM Resources
                        LEFT OUTER JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                        LEFT OUTER JOIN Incident ON Resource_Assignment.Incident_ID = Incident.Incident_ID
                        LEFT OUTER JOIN User ON Resources.Owner = User.Username
                        LEFT OUTER JOIN Individual ON Resources.Owner = Individual.Username
                        LEFT OUTER JOIN Municipal ON Resources.Owner = Municipal.Username
                        LEFT OUTER JOIN Government ON Resources.Owner = Government.Username
                        LEFT OUTER JOIN Company ON Resources.Owner = Company.Username
                        
                        WHERE Resource_Assignment.Request_Status in ('Pending') AND Incident.Owner = '$user'
                        GROUP BY Resources.Resource_ID, Resources.Resource_Name, Incident.Description, Owner, Resource_Assignment.Returned_Date";
            
            //echo 'EXECUTE THE SEARCH QUERY=' . $s_query;
            
            // EXECUTE THE SEARCH QUERY.
            $s_results = $conn->query($s_query);
             if ($s_results->num_rows > 0) {
                while($row = $s_results->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>'.$row["Resource_ID"].'</td>';
                    echo '<td>'.$row["Resource_Name"].'</td>';
                    echo '<td>'.$row["Description"].'</td>';
                    echo '<td>'.$row["Owner"].'</td>';
                    echo '<td>'.$row["Returned_Date"].'</td>';
                    echo '<td><button onclick="window.location.href=\'cancel_request.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$row["Incident_ID"].'\'">Cancel</button></td>';
                    echo '</tr>';
                }
             } else {
                 echo '<tr><td colspan="6">No Results found.</td></tr>';
             }
        ?>
    </table>
    
    
    <h4 style="font-weight: bold;">Resources Requests received by Me</h4>    
    <table>
        <!--THIS IS TABLE HEADER WITH NAMES-->
    	<tr>
        	<th>ID</th>
        	<th>Resource Name</th>
        	<th>Incident</th>
        	<th>Requested_By</th>
        	<th>Return by</th>
        	<th>Action</th>            
        </tr>
        <?php 
            $s_query = "SELECT Resources.Resource_ID, Resources.Resource_Name, Resources.Status, Incident.Description, Incident.Incident_ID,
                        Resource_Assignment.Returned_Date, (CASE 
                         WHEN User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                         WHEN User.Type in ('2') then Municipal.Name
                         WHEN User.type in ('3') then Government.Name
                         WHEN User.type in ('4') then Company.Name
                         else NULL END
                        ) as Requested_By
                        
                        FROM Resources
                        LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                        LEFT JOIN Incident ON Resource_Assignment.Incident_ID = Incident.Incident_ID
                        LEFT JOIN User ON Incident.Owner = User.Username
                        Left JOIN Individual ON Incident.Owner = Individual.Username
                        LEFT JOIN Municipal ON Incident.Owner = Municipal.Username
                        LEFT JOIN Government ON Incident.Owner = Government.Username
                        LEFT JOIN Company ON Incident.Owner = Company.Username
                        
                        WHERE Resource_Assignment.Request_Status in ('Pending') AND Resources.Owner = '$user'
                        GROUP BY Resources.Resource_ID, Resources.Resource_Name, Incident.Description, Requested_By, Resource_Assignment.Returned_Date";
            
            //echo 'EXECUTE THE SEARCH QUERY=' . $s_query;
            
            // EXECUTE THE SEARCH QUERY.
            $s_results = $conn->query($s_query);
             if ($s_results->num_rows > 0) {
                while($row = $s_results->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>'.$row["Resource_ID"].'</td>';
                    echo '<td>'.$row["Resource_Name"].'</td>';
                    echo '<td>'.$row["Description"].'</td>';
                    echo '<td>'.$row["Requested_By"].'</td>';
                    echo '<td>'.$row["Returned_Date"].'</td>';
                    
                    
                    $is_user_res_owner = strcasecmp($user, $row["ownerUsername"]);
                    $is_res_in_repair = strcasecmp("In Repair", $row["Status"]);
                    
                    if(strcasecmp("Available", $row["Status"]) == 0) {
                        echo '<td><div><button style="float:left;width:auto;" 
                        onclick="window.location.href=\'deploy_resource.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$row["Incident_ID"].'&action=approve\'">Deploy</button>'
                        .'<button style="float:right;width:auto;" onclick="window.location.href=\'reject_request.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$row["Incident_ID"].'\'">Reject</button></div></td>';    
                    } else if(strcasecmp("In Use", $row["Status"]) == 0) {
                        echo '<td><button onclick="window.location.href=\'reject_request.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$row["Incident_ID"].'\'">Reject</button></td>';
                    } else {
                        echo '<td></td>';
                    }
                                
                    echo '</tr>';
                }
             } else {
                 echo '<tr><td colspan="6">No Results found.</td></tr>';
             }
        ?>
    </table>
    
    <h4 style="font-weight: bold;">Repairs Scheduled/In-progress</h4>    
    <table>
    	<tr>
        <!--THIS IS TABLE HEADER WITH NAMES-->
        	<th>ID</th>
        	<th>Resource Name</th>
        	<th>Start on</th>
        	<th>Ready by</th>
        	<th>Action</th>            
        </tr>
        <?php 
            $s_query3 = "SELECT Repair.Resource_ID, Resources.Resource_Name, Repair.Start_Date, Repair.Returned_Date, Resources.Status
                        FROM Repair
                        LEFT JOIN Resources ON Repair.Resource_ID = Resources.Resource_ID
                        WHERE Resources.Owner = '$user' AND (Resources.Status in ('In Repair') OR Repair.Start_Date >= CURDATE())";
                        
            //echo 'EXECUTE THE SEARCH QUERY=' . $s_query3;
            // EXECUTE THE SEARCH QUERY.
            $s_results3 = $conn->query($s_query3);
             if ($s_results3->num_rows > 0) {
                while($row = $s_results3->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>'.$row["Resource_ID"].'</td>';
                    echo '<td>'.$row["Resource_Name"].'</td>';
                    echo '<td>'.$row["Start_Date"].'</td>';
                    echo '<td>'.$row["Returned_Date"].'</td>';
                    if(strcasecmp("In Repair", $row["Status"]) == 0) {
                        echo '<td></td>';  
                    } else {
                        echo '<td><button onclick="window.location.href=\'cancel_repair.php?Resource_ID='.$row["Resource_ID"].'&Start_Date='.$row["Start_Date"].'\'">Cancel</button></td>';
                    }
                    
                    echo '</tr>';
                }
             } else {
                 echo '<tr><td colspan="5">No Results found.</td></tr>';
             }
        ?>
    </table>
    
    <center><button style="width:100px;" name="close" onclick="window.location.href='menu.php'">Close</button></center>
    </div>
</div>

<?php
include 'footer.php';
?>