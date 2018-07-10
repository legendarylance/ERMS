<?php
include 'header.php';

$s_keyword = $_POST['search_keyword'];
$s_prim_esf = $_POST['s_prim_esf'];
$s_loc = $_POST['search_location'];
$s_incident = $_POST['select_incident'];

$s_key_on = !empty($s_keyword);
$s_prim_esf_on = !empty($s_prim_esf);
$s_loc_on = !empty($s_loc);
$s_incident_on = !empty($s_incident);
$i_Lat=0.0;$i_Lng=0.0;
$s_incident_name ="";$s_query = "";

// Create connection
$user = $_SESSION["session_user"];
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get Lat and Long for Incident if both $s_incident_on and $s_loc_on are true!
if($s_incident_on) {
   // Get the Longitude and lattitude of the incident.
   $s_inc_q = "SELECT Description, Lng, Lat FROM Incident WHERE Incident_ID =" . $s_incident;
   
   $result = $conn->query($s_inc_q);
   if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
        	$i_Lat = $row["Lat"];
        	$i_Lng = $row["Lng"];
        }
        // echo 'Long='.$i_Lng.' for the incident'. $s_incident.' selected ';
    } else {
        // echo 'Cannot get the Lng and Lat of the incident id:'.$s_incident.' selected ';
    }
}

if(!$s_key_on & !$s_prim_esf_on) { // Cases (0,0,*,*)
    if($s_incident_on) {
        if($s_loc_on) {
            // echo 'case : (0,0,1,1)';
            // DONE
            $s_query = "
            SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, 
                (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                WHEN User.Type in ('2') then Municipal.Name
                WHEN User.type in ('3') then Government.Name
                WHEN User.type in ('4') then Company.Name
                else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                else 'NOW' END) AS Returned_Date, Resources.Distance
                FROM (
                    SELECT
                      Resource_ID, Owner, Resource_Name, Status, Cost, Unit, (
                        6371 * acos (
                          cos ( radians(".$i_Lat.") )
                          * cos( radians( Lat ) )
                          * cos( radians( Lng ) - radians(".$i_Lng.") )
                          + sin ( radians(".$i_Lat.") )
                          * sin( radians( Lat ) )
                        )
                      ) AS Distance FROM Resources
                HAVING distance < $s_loc
            	) Resources

            LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
            LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
            Left Join User ON Resources.Owner = User.Username
            Left JOIN Individual ON Resources.Owner = Individual.Username
            Left Join Municipal ON Resources.Owner = Municipal.Username
            Left Join Government ON Resources.Owner = Government.Username
            Left Join Company ON Resources.Owner = Company.Username
	
            GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
            Resources.Distance
            
            ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        } else {
            // echo 'case : (0,0,0,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, 
                (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                WHEN User.Type in ('2') then Municipal.Name
                WHEN User.type in ('3') then Government.Name
                WHEN User.type in ('4') then Company.Name
                else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                else 'NOW' END) AS Returned_Date, Resources.Distance
                    FROM (
                    SELECT Resource_ID, Owner, Resource_Name, Cost, Status, Unit, 
                      (6371 * acos (
                          cos ( radians(".$i_Lat.") )
                          * cos( radians(Lat) )
                          * cos( radians(Lng) - radians(".$i_Lng.") )
                          + sin ( radians(".$i_Lat.") )
                          * sin( radians(Lat) )
                        )
                      ) AS Distance FROM Resources
                	) Resources
	
                LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                Left Join User ON Resources.Owner = User.Username
                Left JOIN Individual ON Resources.Owner = Individual.Username
                Left Join Municipal ON Resources.Owner = Municipal.Username
                Left Join Government ON Resources.Owner = Government.Username
                Left Join Company ON Resources.Owner = Company.Username
            
                GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                Resources.Distance
                
                ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        }
    } else {
        // echo 'case : (0,0,0,0)';
        // DONE
        $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, 
                    (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name)
                    WHEN User.Type in ('2') then Municipal.Name
                    WHEN User.type in ('3') then Government.Name
                    WHEN User.type in ('4') then Company.Name
                    else NULL END) as Owner,
                    Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                    (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                    WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                    else 'NOW' END) AS Returned_Date
                    
                    FROM Resources
                    LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                    LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                    Left Join User ON Resources.Owner = User.Username
                    Left JOIN Individual ON Resources.Owner = Individual.Username
                    Left Join Municipal ON Resources.Owner = Municipal.Username
                    Left Join Government ON Resources.Owner = Government.Username
                    Left Join Company ON Resources.Owner = Company.Username
                    
                    GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status
                    
                    ORDER BY Resources.Resource_Name ASC";
    }
} else if(!$s_key_on & $s_prim_esf_on) { // Cases (0,1,*,*)
    if($s_incident_on) {
        if($s_loc_on) {
            // echo 'case : (0,1,1,1)';
            // DONE
            $s_query = "
            
            SELECT 
                Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                WHEN User.Type in ('2') then Municipal.Name
                WHEN User.type in ('3') then Government.Name
                WHEN User.type in ('4') then Company.Name
                else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                else 'NOW' END) AS Returned_Date, Resources.Distance
                FROM (
                    SELECT
                      Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Primary_ESF, (
                        6371 * acos (
                          cos ( radians(".$i_Lat.") )
                          * cos( radians( Lat ) )
                          * cos( radians( Lng ) - radians(".$i_Lng.") )
                          + sin ( radians(".$i_Lat.") )
                          * sin( radians( Lat ) )
                        )
                      ) AS Distance FROM Resources
                	HAVING  Distance < $s_loc
                	) Resources
    
                LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
                LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                Left Join User ON Resources.Owner = User.Username
                Left JOIN Individual ON Resources.Owner = Individual.Username
                Left Join Municipal ON Resources.Owner = Municipal.Username
                Left Join Government ON Resources.Owner = Government.Username
                Left Join Company ON Resources.Owner = Company.Username

                WHERE Resources.Primary_ESF in ('".$s_prim_esf."') OR Additional_ESF.ESF_ID in ('".$s_prim_esf."')

                GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, Resources.Distance
                
                ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        } else {
            // echo 'case : (0,1,0,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                WHEN User.Type in ('2') then Municipal.Name
                WHEN User.type in ('3') then Government.Name
                WHEN User.type in ('4') then Company.Name
                else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                else 'NOW' END) AS Returned_Date, Resources.Distance
                    FROM (
                    SELECT
                      Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Primary_ESF, (
                        6371 * acos (
                          cos ( radians(".$i_Lat.") )
                          * cos( radians( Lat ) )
                          * cos( radians( Lng ) - radians(".$i_Lng.") )
                          + sin ( radians(".$i_Lat.") )
                          * sin( radians( Lat ) )
                        )
                      ) AS Distance FROM Resources
                	) Resources
                LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
                LEFT OUTER JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                LEFT OUTER JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                LEFT OUTER JOIN User ON Resources.Owner = User.Username
                LEFT OUTER JOIN Individual ON Resources.Owner = Individual.Username
                LEFT OUTER JOIN Municipal ON Resources.Owner = Municipal.Username
                LEFT OUTER JOIN Government ON Resources.Owner = Government.Username
                LEFT OUTER JOIN Company ON Resources.Owner = Company.Username
                
                WHERE Resources.Primary_ESF in ('".$s_prim_esf."') or Additional_ESF.ESF_ID in ('".$s_prim_esf."')
                	
                GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                Resources.Distance
                
                ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        }
    } else {
        // echo 'case : (0,1,0,0)';
        // DONE
        $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
            WHEN User.Type in ('2') then Municipal.Name
            WHEN User.type in ('3') then Government.Name
            WHEN User.type in ('4') then Company.Name
            else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
            (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
            WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
            else 'NOW' END) AS Returned_Date
            
            FROM Resources
            LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
            LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
            LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
            Left Join User ON Resources.Owner = User.Username
            Left JOIN Individual ON Resources.Owner = Individual.Username
            Left Join Municipal ON Resources.Owner = Municipal.Username
            Left Join Government ON Resources.Owner = Government.Username
            Left Join Company ON Resources.Owner = Company.Username
            WHERE Resources.Primary_ESF in ('$s_prim_esf') or Additional_ESF.ESF_ID in ('$s_prim_esf')
            
            GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status
            
            ORDER BY Resources.Resource_Name ASC";
        
        
    }
} else if($s_key_on & !$s_prim_esf_on) { // Cases (1,0,*,*)
    if($s_incident_on) {
        if($s_loc_on) {
            // echo 'case : (1,0,1,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                WHEN User.Type in ('2') then Municipal.Name
                WHEN User.type in ('3') then Government.Name
                WHEN User.type in ('4') then Company.Name
                else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                else 'NOW' END) AS Returned_Date, Resources.Distance
                
                FROM (
                    SELECT
                      Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Model, (
                        6371 * acos (
                          cos ( radians(".$i_Lat.") )
                          * cos( radians( Lat ) )
                          * cos( radians( Lng ) - radians(".$i_Lng.") )
                          + sin ( radians(".$i_Lat.") )
                          * sin( radians( Lat ) )
                        )
                      ) AS Distance From Resources
                	HAVING  Distance < $s_loc
                	) Resources
                
                
                LEFT OUTER JOIN Resource_Capabilities ON  Resources.Resource_ID = Resource_Capabilities.Resource_ID
                LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                Left Join User ON Resources.Owner = User.Username
                Left JOIN Individual ON Resources.Owner = Individual.Username
                Left Join Municipal ON Resources.Owner = Municipal.Username
                Left Join Government ON Resources.Owner = Government.Username
                Left Join Company ON Resources.Owner = Company.Username
                Left Join Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID

                WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') 
                        OR Resources.Model like ('%".$s_keyword."%'))
                
                GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, Resources.Distance
                
                ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        } else {
            // echo 'case : (1,0,0,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                        WHEN User.Type in ('2') then Municipal.Name
                        WHEN User.type in ('3') then Government.Name
                        WHEN User.type in ('4') then Company.Name
                        else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                        (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                        WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                        else 'NOW' END) AS Returned_Date, Resources.Distance
                            FROM (
                            SELECT
                              Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Model, (
                                6371 * acos (
                                  cos ( radians(".$i_Lat.") )
                                  * cos( radians( Lat ) )
                                  * cos( radians( Lng ) - radians(".$i_Lng.") )
                                  + sin ( radians(".$i_Lat.") )
                                  * sin( radians( Lat ) )
                                )
                              ) AS Distance FROM Resources
                        	) Resources
                        
                        LEFT OUTER JOIN Resource_Capabilities ON  Resources.Resource_ID = Resource_Capabilities.Resource_ID
                        LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                        LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                        Left Join User ON Resources.Owner = User.Username
                        Left JOIN Individual ON Resources.Owner = Individual.Username
                        Left Join Municipal ON Resources.Owner = Municipal.Username
                        Left Join Government ON Resources.Owner = Government.Username
                        Left Join Company ON Resources.Owner = Company.Username
                        
                        WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') 
                                OR Resources.Model like ('%".$s_keyword."%'))
                        	
                        GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                        Resources.Distance
                        
                        ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        }
    } else {
        // echo 'case : (1,0,0,0)';
        // DONE
        $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                    WHEN User.Type in ('2') then Municipal.Name
                    WHEN User.type in ('3') then Government.Name
                    WHEN User.type in ('4') then Company.Name
                    else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                    (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                    WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                    else 'NOW' END) AS Returned_Date
                
                    FROM Resources
                    LEFT OUTER JOIN Resource_Capabilities ON  Resources.Resource_ID = Resource_Capabilities.Resource_ID
                    LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                    LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                    Left Join User ON Resources.Owner = User.Username
                    Left JOIN Individual ON Resources.Owner = Individual.Username
                    Left Join Municipal ON Resources.Owner = Municipal.Username
                    Left Join Government ON Resources.Owner = Government.Username
                    Left Join Company ON Resources.Owner = Company.Username
                    
                    WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') 
                            OR Resources.Model like ('%".$s_keyword."%'))
                    
                    GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status
                    
                    ORDER BY Resources.Resource_Name ASC";
    }
} else if($s_key_on & $s_prim_esf_on) { // Cases (1,1,*,*)
    if($s_incident_on) {
        if($s_loc_on) {
            // echo 'case : (1,1,1,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status,
                        (CASE WHEN User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                            WHEN User.Type in ('2') then Municipal.Name
                            WHEN User.type in ('3') then Government.Name
                            WHEN User.type in ('4') then Company.Name
                            ELSE NULL END) as Owner, 
                        (CASE when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                            WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                            ELSE 'NOW' END) AS Returned_Date, Resources.Distance
                            FROM (
                                SELECT
                                  Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Model, Primary_ESF, (
                                    6371 * acos (
                                      cos ( radians(".$i_Lat.") )
                                      * cos( radians( Lat ) )
                                      * cos( radians( Lng ) - radians(".$i_Lng.") )
                                      + sin ( radians(".$i_Lat.") )
                                      * sin( radians( Lat ) )
                                    )
                                  ) AS Distance
                            From Resources
                            	HAVING  Distance < $s_loc
                            	) Resources	
                            
                            
                            LEFT OUTER JOIN Resource_Capabilities ON Resources.Resource_ID = Resource_Capabilities.Resource_ID
                            LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
                            LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                            Left Join User ON Resources.Owner = User.Username
                            Left JOIN Individual ON Resources.Owner = Individual.Username
                            Left Join Municipal ON Resources.Owner = Municipal.Username
                            Left Join Government ON Resources.Owner = Government.Username
                            Left Join Company ON Resources.Owner = Company.Username
                            Left Join Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID

                            WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') 
                            OR Resources.Model like ('%".$s_keyword."%')) 
                            AND (Resources.Primary_ESF in ('".$s_prim_esf."') OR Additional_ESF.ESF_ID in ('".$s_prim_esf."'))
                            
                            GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, Resources.Distance
                            
                            ORDER BY Resources.Distance ASC, Resources.Resource_Name ASC";
        } else {
            // echo 'case : (1,1,0,1)';
            // DONE
            $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                        WHEN User.Type in ('2') then Municipal.Name
                        WHEN User.type in ('3') then Government.Name
                        WHEN User.type in ('4') then Company.Name
                        else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                        (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                        WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                        else 'NOW' END) AS Returned_Date, Resources.Distance
                            FROM (
                            SELECT
                              Resource_ID, Owner, Resource_Name, Status, Cost, Unit, Model, Primary_ESF, (
                                6371 * acos (
                                  cos ( radians(".$i_Lat.") )
                                  * cos( radians( Lat ) )
                                  * cos( radians( Lng ) - radians(".$i_Lng.") )
                                  + sin ( radians(".$i_Lat.") )
                                  * sin( radians( Lat ) )
                                )
                              ) AS Distance
                        FROM Resources
                        	) Resources
                        
                        
                        LEFT OUTER JOIN Resource_Capabilities ON  Resources.Resource_ID = Resource_Capabilities.Resource_ID
                        LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
                        LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                        Left Join User ON Resources.Owner = User.Username
                        Left JOIN Individual ON Resources.Owner = Individual.Username
                        Left Join Municipal ON Resources.Owner = Municipal.Username
                        Left Join Government ON Resources.Owner = Government.Username
                        Left Join Company ON Resources.Owner = Company.Username
                        
                        LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                        
                        WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') OR Resources.Model like ('%".$s_keyword."%'))
                                AND (Resources.Primary_ESF in ('".$s_prim_esf."') or Additional_ESF.ESF_ID in ('".$s_prim_esf."'))
                        	
                        GROUP BY
                        Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                        Resources.Distance
                        
                        
                        ORDER BY
                        Resources.Distance ASC, Resources.Resource_Name ASC";
        }
    } else {
        // echo 'case : (1,1,0,0)';
        // DONE
        $s_query = "SELECT Resources.Resource_ID, Resources.Owner as ownerUsername, 
                    (case when User.Type in ('1') then CONCAT(Individual.First_Name,' ',Individual.Last_Name) 
                    WHEN User.Type in ('2') then Municipal.Name
                    WHEN User.type in ('3') then Government.Name
                    WHEN User.type in ('4') then Company.Name
                    else NULL END) as Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status, 
                    (case when Resources.Status in ('In Use') then MAX(Resource_Assignment.Returned_Date) 
                    WHEN Resources.Status in ('In Repair') then MAX(Repair.Returned_Date)
                    else 'NOW' END) AS Returned_Date
                    
                    FROM Resources
                    LEFT OUTER JOIN Resource_Capabilities ON  Resources.Resource_ID = Resource_Capabilities.Resource_ID
                    LEFT OUTER JOIN Additional_ESF ON Resources.Resource_ID = Additional_ESF.Resource_ID
                    LEFT JOIN Resource_Assignment ON Resources.Resource_ID = Resource_Assignment.Resource_ID
                    LEFT JOIN Repair ON Resources.Resource_ID = Repair.Resource_ID
                    Left Join User ON Resources.Owner = User.Username
                    Left JOIN Individual ON Resources.Owner = Individual.Username
                    Left Join Municipal ON Resources.Owner = Municipal.Username
                    Left Join Government ON Resources.Owner = Government.Username
                    Left Join Company ON Resources.Owner = Company.Username

                    WHERE (Resources.Resource_Name like ('%".$s_keyword."%') OR Resource_Capabilities.Capabilities like ('%".$s_keyword."%') 
                            OR Resources.Model like ('%".$s_keyword."%'))
                            AND (Resources.Primary_ESF in ('".$s_prim_esf."') or Additional_ESF.ESF_ID in ('".$s_prim_esf."'))
                    
                    GROUP BY Resources.Resource_ID, Owner, Resources.Resource_Name, Resources.Cost, Resources.Unit, Resources.Status
                    
                    ORDER BY Resources.Resource_Name ASC";
    }
}

?>

<?php 
    if(empty($s_query)) {
        $err_msg = "Query is TBD.";
        include 'error.php';
    } else {
        // echo 'Query=' . $s_query;
?>
    <div class="search_results_container">  
      <div id="contact">
        <h3>Search Results</h3>
        <?php 
            if(!empty($s_incident_name)) { ?>
                <h4>Search Results for incident: <span style="font-weight:600;">Flash Floods in Fulton County (102)</span></h4>
        <?php } ?>
        <div style="height: auto; max-height:500px;overflow-y: auto;">
            <table>
                <tbody>
                    <!--THIS IS TABLE HEADER WITH NAMES-->
                	<tr>
                    	<th>ID</th>
                    	<th>Name</th>
                    	<th>Owner</th>
                    	<th>Cost</th>
                    	<th>Status</th>
                    	<th>Next Available</th>
                    	<th>Distance</th>
                    	<th>Action</th>            
                    </tr>
                     <?php 
                        // EXECUTE THE SEARCH QUERY.
                        $s_results = $conn->query($s_query);
                         if ($s_results->num_rows > 0) {
                            while($row = $s_results->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>'.$row["Resource_ID"].'</td>';
                                echo '<td>'.$row["Resource_Name"].'</td>';
                                echo '<td>'.$row["Owner"].'</td>';
                                echo '<td>'.$row["Cost"].'</td>';
                                echo '<td>'.$row["Status"].'</td>';
                                echo '<td>'.$row["Returned_Date"].'</td>';
                                echo '<td>'.$row["Distance"].' km </td>';
                                
                                $is_user_res_owner = strcasecmp($user, $row["ownerUsername"]);
                                $is_res_in_repair = strcasecmp("In Repair", $row["Status"]);
                                
                                if($is_user_res_owner == 0 & $s_incident_on & strcasecmp("Available", $row["Status"]) == 0) {
                                    echo '<td><div><button style="float:left;width:auto;" 
                                    onclick="window.location.href=\'deploy_resource.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$s_incident.'&action=insert\'">Deploy</button>'
                                    .'<button style="float:right;width:auto;" onclick="window.location.href=\'repair_resource.php?Resource_ID='.$row["Resource_ID"].'&Returned_Date='.$row["Returned_Date"].'\'">Repair</button></div></td>';    
                                } else if($is_user_res_owner == 0 & $is_res_in_repair != 0) {
                                    echo '<td><button onclick="window.location.href=\'repair_resource.php?Resource_ID='.$row["Resource_ID"].'&Returned_Date='.$row["Returned_Date"].'\'">Repair</button></td>'; // ADD RETURN DATE HERE AS &Returned_Date='$row['Returned_Date']
                                } else if($is_user_res_owner != 0 & $is_res_in_repair != 0 & $s_incident_on) {
                                    echo '<td><button onclick="window.location.href=\'request_resource.php?Resource_ID='.$row["Resource_ID"].'&Incident_ID='.$s_incident.'\'">Request</button></td>';    
                                } else {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                            }
                         } else {
                            echo '<tr><td colspan="8">No Results found.</td></tr>';
                         }
                     ?>
                </tbody>
            </table>
        </div>
        <center>
            <button name="close" style="width:100px;" onclick="window.location.href='menu.php'">Close</button>
            <button name="seach_again" type="button" style="width:auto;" onclick="window.location.href='search.php'">Search again</button>
        </center>
        </div>
    </div>
<?php }?>

<?php
include 'footer.php';
?>