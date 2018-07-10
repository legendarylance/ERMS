<?php
include 'header.php';
 
$i_date = $_POST['i_date'];
$i_descr = addslashes($_POST['i_descr']);

$i_lng = $_POST['i_lng'];
$i_lat = $_POST['i_lat'];

// // GOLD BABY!!!!!
$con=mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
  if (!mysqli_query($con,"INSERT INTO Incident 
		(Owner, Description, Incident_Date, Lng, Lat)
VALUES ('$user', '$i_descr', '$i_date', '$i_lng', '$i_lat')"))
  {
  // IF FAILS, REDIRECT BACK TO ADD RESOURE PAGE
    header("location: new_incident.php?error=1");
  }
        
mysqli_close($con);

	?>
 
<div class="container">  
  <form id="contact">
    <h3>Incident Added!</h3>
    <div style="width:50%; display:block; float:right; text-align:right;">
       <?php include 'display_user_info.php';?>
    </div>
    <div style="clear:both;"></div>
    <center>
    Your Incident has been added.<br />
    
    <h4>Thank you!</h4>
    <button name="cancel" type="button" onclick="window.location.href='menu.php'">Menu</button>
    </center>
  </form>
<?php
include 'footer.php';
?>