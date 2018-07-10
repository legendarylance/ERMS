<?php
include 'header.php';


//Resource Report ********************	
// SELECT 
// ESF.ESF_ID,
// ESF.Description,
// COUNT(Resources.Primary_ESF) as 'Total Resources',
// SUM(CASE WHEN Resources.Status = 'In Use' THEN 1 ELSE 0 END) as 'Resources In Use'

// FROM ESF LEFT OUTER JOIN Resources ON ESF.ESF_ID = Resources.Primary_ESF

// WHERE Resources.Owner = '$Username' 

// GROUP BY
// ESF.ESF_ID, ESF.Description

        
?>

<script>
     var totals=[0,0,0];
$(document).ready(function(){

    var $dataRows=$("#sum_table tr:not('.totalColumn, .titlerow')");
    
    $dataRows.each(function() {
        $(this).find('.rowDataSd').each(function(i){        
            totals[i]+=parseInt( $(this).html());
        });
    });
    $("#sum_table td.totalCol").each(function(i){  
        $(this).html(totals[i]);
    });

});
</script>

<div class="container2">  
  <div id="contact">
    <h3>Resource Report</h3>
    <h4>Resource Report by Primary Emergency Support Function</h4>
    
    <table class="results_tabel" id="sum_table">
    	<tr class="titlerow">
        	<th>ESF ID</th>
        	<th>Description</th>
        	<th>Total Resources</th>
        	<th>Resources in Use</th>      
        </tr>
        <tr>
            <?php
            // Create connection
            $user = $_SESSION["session_user"];
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT 
ESF.ESF_ID,
ESF.Description,
COUNT(Resources.Primary_ESF) as 'Total_Resources',
SUM(CASE WHEN Resources.Status = 'In Use' THEN 1 ELSE 0 END) as 'Resources_In_Use'
FROM ESF LEFT OUTER JOIN Resources ON ESF.ESF_ID = Resources.Primary_ESF
AND Resources.Owner = '$user'
GROUP BY
ESF.ESF_ID, ESF.Description";
        
        // SELECT ESF.ESF_ID, ESF.Description, COUNT(Resources.Primary_ESF) as 'Total_Resources',
        // SUM(CASE WHEN Resources.Status = 'In Use' THEN 1 ELSE 0 END) as 'Resources_In_Use'
        // FROM ESF LEFT OUTER JOIN Resources ON ESF.ESF_ID = Resources.Primary_ESF
        // WHERE Resources.Owner = '$user' 
        // GROUP BY
        // ESF.ESF_ID, ESF.Description
        // ";
        
        // SELECT MAX(Resource_ID) AS newResourceId  FROM Resources";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                //  not sure about this part
            echo '<tr>';
            echo '<td>'.$row["ESF_ID"].'</td>';
            echo '<td>'.$row["Description"].'</td>';
            echo '<td class="rowDataSd">'.$row["Total_Resources"].'</td>';
            echo '<td class="rowDataSd">'.$row["Resources_In_Use"].'</td>';
            echo '</tr>';
            }
        } else {
            $newResourceId = 1; // this might need to be deleted...
        }
            ?>
       <tr style="font-weight: 600;">
           <td></td>
           <td>TOTALS</td>
           <td class="totalCol"></td>
           <td class="totalCol"></td>
           </tr> 	     
       
    </table>
    
    <center>
        <button name="cancel" onclick="window.location.href='menu.php'">Cancel</button></center>
    </div>
</div>


<?php
include 'footer.php';
?>
