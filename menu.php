<?php
include 'header.php';
?>

<?php include 'display_info.php'; // rename to get_user_info 
?>

    <div class="container">
        <form id="contact">
            <div style="float:left;width:100%;">
                <div style="font-weight:bold;float:left;">ERMS Main Menu</div>            
                <?php include 'display_user_info.php';?>
            </div>
            <hr/>
        <div style="clear:both;margin-top:5px;">
            <center>
                <a href="/new_resource.php">Add Resource</a><br />
                <a href="/new_incident.php">Add Emergency Incident</a><br />
                <a href="/search.php">Search Resources</a><br />
                <a href="/resource_status.php">Resource Status</a><br />
                <a href="/resource_report.php">Resource Report</a><br />
                <a href="/statistics.php">Statistics</a><br />
                <a href="/exit.php">Exit</a><br />
            </center>
        </div>
        </form>
        </div>
        <?php
    // }
// }
// else
// {
/*     ?>
//    <div class="container">
//         <form id="contact">
//             <h3>Something is Wrong... </h3>
//             <!-- Make sure this login page actually goes where it should... -->
//             <p>Please try to <a href="/login.php">log in</a> again</p>
//         </form>
//     </div>
//     <?php
// }*/
?>


<?php
include 'footer.php';
?>