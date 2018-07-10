<?php
include 'header.php';

$conn = new mysqli($servername, $username, $password, $dbname);

?>

<div class="container">
    <form id="contact" action="search_results.php" method="post">
        <h3>Search Resources</h3><br />
        <fieldset>
            <input placeholder="Keyword" name="search_keyword" type="text" tabindex="1">
        </fieldset>
        <label>ESF</label>
        <fieldset>
            <select name="s_prim_esf">
                <option value="">--Select--</option>
                <?php              
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
            <input placeholder="Location (within km from incident)" type="number" tabindex="2" id="search_location" name="search_location">
        </fieldset>
        </fieldset>
        <label>Incident</label>
        <fieldset>
            <select id="select_incident" name="select_incident">
                <option value="">--Select--</option>
                <?php              
                $sql = " SELECT Incident_ID, Description FROM Incident ORDER BY Incident_Date DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                		// Pulling ESFs
                		echo '<option value="'.$row["Incident_ID"].'">'.'#'.$row["Incident_ID"].' '.$row["Description"].'</option>';
                    }
                } else {
                    echo '<option value="NoESF">No Incidents, good :)</option>';
                }
                ?>
            </select>
        </fieldset>
        
        <div style="width:100%;">
            <button style="width:45%; float:right;" name="save" type="submit" id="contact-submit" data-submit="...Searching">Search</button>
            <button style="width:45%; float:left;"  type="button" onclick="window.location.href='menu.php'" name="cancel">Cancel</button>
        </div>
        
        <div style="clear:both;"></div>
    </form>
</div>

<?php
include 'footer.php';
?>


<script type="text/javascript">

document.getElementById('search_location').addEventListener('blur', function(event) {
    console.log("event.target.value" +event.target.value);
    if (event.target.value) {
      document.getElementById("select_incident").setAttribute("required", "required");
      document.getElementById("select_incident").setAttribute("autofocus", "");
    } else {
        document.getElementById("select_incident").removeAttribute("required");
        document.getElementById("select_incident").removeAttribute("autofocus");
    }
}, false);
    
</script>
