<?php
include 'header.php';

$error_mesage = "";

if ($_GET["error"]=="1"){
    $error_mesage = "<h2 style='color:red;'>Could not add New Resource...</h2>";
}
?>
<script type="text/javascript">
var counter = 1;
var limit = 10;
function addInput(divName){
     if (counter == limit)  {
          alert("You have reached the limit of adding " + counter + " inputs");
     }
     else {
          var newdiv = document.createElement('div');
          newdiv.innerHTML = "<input type='text' name='addCap[]' placeholder='Additional Cap'>";
          document.getElementById(divName).appendChild(newdiv);
          counter++;
     }
}
</script>
<div class="container">
<form id="contact" action="resource_conf.php" method="post">
    <h3>Add Resouce</h3>
    <?php echo $error_mesage; ?>
    <?php
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT MAX(Resource_ID) AS newResourceId  FROM Resources";
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            while($row = $result->fetch_assoc()) {
                $newResourceId = $row["newResourceId"] + 1;
            }
        } else {
            $newResourceId = 1;
        }
        
        include 'display_info.php';
    ?>
    <div>
        <label>Resource ID: </span><span class="fontBold"><?php echo  $newResourceId?></span>
    </div>
    
    <div>
        <label>Owner: </label><span class="fontBold"><?php echo  $firtPar?></span>
    </div>
    
    <fieldset>
      <input placeholder="Resource name" type="text" tabindex="1" name="r_name" required autofocus>
    </fieldset>
    <fieldset style="margin-top:-5px;">    
    <label>Primary ESF</label>
    	<select name="r_prim_esf">
            <?php
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM `ESF`";
            $result = $conn->query($sql);
            
            
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
            		// Pulling ESFs
            		echo '<option value="'.$row["ESF_ID"].'">'.'#'.$row["ESF_ID"].' '.$row["Description"].'</option>';
                }
            } else {
                echo '<option value="NoESF">No ESF, bummer</option>';
            }
            ?>
        </select>
    </fieldset>
    <fieldset style="margin-top:-15px;">    
    <label>Additional ESF</label>
    <select multiple name="r_add_esf[]">
        <?php
        // BRUTE FORCE,  BUT WORKS...
        $sql = "SELECT * FROM `ESF`";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
        		// Pulling ESFs
        		echo '<option value="'.$row["ESF_ID"].'">'.'#'.$row["ESF_ID"].' '.$row["Description"].'</option>';
            }
        } else {
            echo '<option value="NoESF">No ESF, bummer</option>';
        }
        ?>
        </select>
    </fieldset>
    
    <fieldset>
    <input placeholder="Model" type="text" tabindex="2" name="r_model" >
    </fieldset>
    
    
    <fieldset>
    <div id="dynamicInput"><input type="text" name="addCap[]" placeholder="Capabilities"></div>
    <button onClick="addInput('dynamicInput');" type="button">Add</button>
    </fieldset>
    
    <label>Home Location</label>
    <div style="widows:100%;">
    <div style="widows:45%; display:block; position:relative; float:left;">
    	<fieldset>
      		<input placeholder="Latitude" type="number" step="any" tabindex="1" required autofocus required name="r_lat" min="-90" max="90" >
    	</fieldset>
    </div>
    <div style="widows:45%; display:block; position:relative; float:right;">
    	<fieldset>
      		<input placeholder="Longitude" type="number" step="any" tabindex="1" required autofocus required name="r_lng"  min="-180" max="180" >
    	</fieldset>
    </div>
    </div>
    <div style="clear:both;"></div>
    
    
    <label>Cost</label>
    <div style="width:100%;">
    <div style="width:45%; display:block; position:relative; float:left;">
    	<fieldset>
      		<input placeholder="$" type="number" tabindex="1" required autofocus required name="r_dollar">
    	</fieldset>
    </div>
    <div style="width:45%; display:block; position:relative; float:right;">
    	<fieldset>
      	<select name="r_per">
        
        <?php
            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT * FROM `Cost`";
            $result = $conn->query($sql);
            
            
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
            		// Pulling Cost Options (ranges for Days, Weeks, etc.)
            		echo '<option value="'.$row["Unit"].'">'.$row["Unit"].'</option>';
                }
            } else {
                echo '<option value="Nooptions">No Options, bummer</option>';
            }
        ?>
        </select>
    	</fieldset>
    </div>
    </div>
    <div style="clear:both;"></div>
    
    <div style="width:100%;">
        <button style="width:45%; float:right;" name="save" type="submit" id="contact-submit" data-submit="...Sending">Save</button>
        <button style="width:45%; float:left;" name="cancel" type="button" onclick="window.location.href='menu.php'">Cancel</button>
    </div>
    
    <div style="clear:both;"></div>
  </form>
  <?php
include 'footer.php';
?>