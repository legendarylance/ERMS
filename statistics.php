<?php
include 'header.php';


$conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "
            SELECT
(sum(case when Resources.Status in ('In Use') then 1 else 0 END)/Count(*))*100 as Percent_In_Use,
(sum(case when Resources.Status in ('In Repair') then 1 else 0 END)/Count(*))*100 as Percent_In_Repair,
(sum(case when Resources.Status in ('Available') then 1 else 0 END)/Count(*))*100 as Percent_Available

FROM Resources

Where Resources.Owner = '$user'
            ";
            $result = $conn->query($sql);
            
            
            
          ?>  
<div class="container2">  
  <div id="contact">
    <h3>Statistics</h3>
    <h4>My resources in Use/Repair</h4>
    <table>
    <tbody>
        <!--THIS IS TABLE HEADER WITH NAMES-->
    	<tr>
        	<th>Percent In Use</th>
        	<th>Percent In Repair</th>
        	<th>Percent Available</th>   
        </tr>
        
        
        <!--THIS IS THE CONTENT, THE RESULT OF THE SEARCH QUERY-->
        <tr>
            
            <?php
            
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
            		// Pulling ESFs
            		echo '<td>'.$row["Percent_In_Use"].' %</td>';
            		echo '<td>'.$row["Percent_In_Repair"].' %</td>';
            		echo '<td>'.$row["Percent_Available"].' %</td>';
                }
            } else {
                echo '<option value="NoESF">No ESF, bummer</option>';
            }
            
            ?>            
        </tr>
        </tbody>
        </table>
        <!--NEXT COLUMN WITH DUMMY DATA, YOU CAN DELETE FROM HERE UP TO...-->
        
        <?php
        
        $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql2 = "
            
            SELECT
sum(case when (Incident_Date < DATE_SUB(CURDATE(),Interval 12 MONTH)) then 1 else 0 end) as OVER_12,
sum(case when (Incident_date > (DATE_SUB(CURDATE(),Interval 12 MONTH)) AND (Incident_Date < DATE_SUB(CURDATE(),Interval 6 MONTH)) )then 1 else 0 end) as 12_to_6,
sum(case when (Incident_date > (DATE_SUB(CURDATE(),Interval 6 MONTH)) AND (Incident_Date < DATE_SUB(CURDATE(),Interval 3 MONTH)) )then 1 else 0 end) as 6_to_3,
sum(case when (Incident_date > (DATE_SUB(CURDATE(),Interval 3 MONTH)))then 1 else 0 end) as LAST_3
From Incident

Where Owner = '$user'
            ";
            $result2 = $conn->query($sql2);
        
        
        ?>
        
    <h4>My Incidents Statistics</h4>
    <table>
    <tbody>
        <tr>
            <th>OVER 12 months</th>
            <th>12 to 6 months</th>
            <th>6 to 3 months</th>
            <th>Last 3 months</th> 
        </tr>
            <?php
            
            if ($result2->num_rows > 0) {
                // output data of each row
                while($row = $result2->fetch_assoc()) {
            		// Pulling ESFs
                    echo '<tr>';
                    // OVER_12, 12_to_6, 6_to_3, LAST_3
            		echo '<td>'.$row["OVER_12"].'</td>';
            		echo '<td>'.$row["12_to_6"].'</td>';
            		echo '<td>'.$row["6_to_3"].'</td>';
            		echo '<td>'.$row["LAST_3"].'</td>';
                    echo '</tr>';
                }
            } else {
                echo 'No Data, bummer';
            }
            
            ?>
        
        </tbody>
    </table>
    
        <button name="close" type="button" onclick="window.location.href='menu.php'">Close</button>
    </div>
</div>
<?php
include 'footer.php';
?>