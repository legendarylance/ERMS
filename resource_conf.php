<?php
include 'header.php';

include 'display_info.php';
$res_id = int;

$r_name = addslashes($_POST['r_name']);
$r_model = addslashes($_POST['r_model']);
$r_cap = $_POST['r_cap']; // this one will be ignored 
$r_lat = $_POST['r_lat'];
$r_lng = $_POST['r_lng'];
$r_dollar = $_POST['r_dollar'];
$r_prim_esf = $_POST['r_prim_esf'];
$r_add_esf = $_POST['r_add_esf'];
$r_per = $_POST['r_per'];

// Gets Capabilities as a string of arrays
$addCap = $_POST["addCap"];

$r_add_esf_string = implode(", ",$r_add_esf);
$arrlength = count($r_add_esf);

// GOLD BABY!!!!!
$con=mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  
  if (!mysqli_query($con,"INSERT INTO Resources 
( Owner , Resource_Name, Model, Cost , Unit , Lng , Lat ,Primary_ESF)
VALUES ('$user', '$r_name', '$r_model', '$r_dollar', '$r_per', '$r_lng', '$r_lat', '$r_prim_esf')"))
  {
  // IF FAILS, REDIRECT BACK TO ADD RESOURE PAGE
    header("location: new_resource.php?error=1");
  }
$res_id = mysqli_insert_id($con);

foreach ($addCap as $addCa) {
        $temp_cap = addslashes($addCa);
        mysqli_query($con,"INSERT INTO Resource_Capabilities(Resource_ID, Capabilities) VALUES ($res_id,'$temp_cap')");
        }
        
foreach ($r_add_esf as $add_esf) {
        mysqli_query($con,"INSERT INTO Additional_ESF(Resource_ID, ESF_ID) VALUES ($res_id,'$add_esf')");
        }
        
mysqli_close($con);

	?>
 

<div class="container">
<div id="contact">
    <h3>Resource Added!</h3>
    <div style="width:50%; display:block; float:right; text-align:right;">
       <?php include 'display_user_info.php';?>
    </div>
    <div style="clear:both;"></div>
    <center>
    Your Resource <span><?php echo $r_name ?></span> has been added.<br />
    
    <h4>Thank you!</h4>
    
    <div style="width:100%;">
    <div style="width:45%; display:block; position:relative; float:right;">
    	<fieldset>
        <button name="cancel" type="button" onclick="window.location.href='new_resource.php'">Add Another</button>
   		</fieldset>
    </div>
    <div style="width:45%; display:block; position:relative; float:left;">
    	<fieldset>
    	  <button name="cancel" type="button" onclick="window.location.href='menu.php'">Menu</button>
    	</fieldset>	
    </div>
    </div>
    <div style="clear:both;"></div>
    </center>
 </div>

<?php
include 'footer.php';
?>