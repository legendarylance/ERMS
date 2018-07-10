<?php
include 'header.php';

$error_mesage = "";

if ($_GET["error"]=="1"){
    $error_mesage = "<h2 style='color:red;'>Could not add New Incident...</h2>";
}


// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT MAX(Incident_ID) AS newIncidentId FROM Incident";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $newIncidentId = $row["newIncidentId"] + 1;
            }
        } else {
            $newIncidentId = 1;
        }

?>

<div class="container">  
  <form id="contact" action="incident_conf.php" method="post">
    <h3>Add Incident</h3>
    <?php echo $error_mesage; ?>
    <h4>Incident ID: <?php echo $newIncidentId; ?></h4>
    <fieldset>
      Date:<br /><input placeholder="Date" type="date" tabindex="1" name="i_date" required autofocus>
    </fieldset>
    <fieldset>
      <textarea placeholder="Description" tabindex="2" name="i_descr" required></textarea>
    </fieldset>
    <fieldset style="margin-top:-15px;">
    <label>Location</label>
    <div style="width:100%;">
    <div style="width:45%; display:block; position:relative; float:left;">
    	<fieldset>
      		<input placeholder="Latitude" type="number" step="any" tabindex="1" name="i_lat"  min="-90" max="90" required autofocus>
    	</fieldset>
    </div>
    <div style="width:45%; display:block; position:relative; float:right;">
    	<fieldset>
      		<input placeholder="Longitude" type="number" step="any" tabindex="1" name="i_lng" min="-180" max="180"  required autofocus>
    	</fieldset>
    </div>
    </div>
    <div style="clear:both;"></div>
    <div style="width:100%;">
    <div style="width:45%; display:block; position:relative; float:right;">
    	<fieldset>
      		<button type="submit" id="contact-submit">Save</button>
   		</fieldset>   	
        
    </div>
    <div style="width:45%; display:block; position:relative; float:left;">
    	<fieldset>
        <button name="cancel" type="button" onclick="window.location.href='menu.php'">Cancel</button>
    	</fieldset>	
    </div>
    </div>
    <div style="clear:both;"></div>
    </fieldset>
  </form>

  <?php
include 'footer.php';
?>