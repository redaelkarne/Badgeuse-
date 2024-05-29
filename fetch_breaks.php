<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Paris');
// Database connection parameters


// Creating a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Query to fetch breaks and Durepause
$query = "SELECT Agent, heure, heure_fin, Durepause, flux 
          FROM enregistrements 
          ORDER BY heure DESC";
$result = $conn->query($query);

// Checking if the query was successful
if ($result->num_rows > 0) {
    // Fetching results and constructing the table rows
    while($row = $result->fetch_assoc()) {
        $agent = htmlspecialchars($row['Agent']);
        $heure = htmlspecialchars($row['heure']);
        $heure_fin = htmlspecialchars($row['heure_fin']);
        $durepause = htmlspecialchars($row['Durepause']);
        $flux = htmlspecialchars($row['flux']);

        // Output datetime strings for debugging
       // Output database values for debugging
// Output database values for debugging


// Convert datetime strings to Unix timestamps for debugging
$start_timestamp = strtotime($heure);
$end_timestamp = ($heure_fin !== '') ? strtotime($heure_fin) : null;


// Calculate the time difference in minutes
$duree_pause = ($end_timestamp !== null) ? round(($end_timestamp - $start_timestamp) / 60) : null;


        // If heure_fin is NULL, show as "In Progress"
        if (is_null($heure_fin)) {
            $heure_fin = "<span class='in-progress'>In Progress</span>";
        }

        // Determine if "Signaler" should be displayed
        

// Convert $durepause to minutes
list($hours, $minutes, $seconds) = explode(':', $durepause);
$durepause_minutes = ($hours * 60) + $minutes + ($seconds / 60);



// Determine if "Signaler" should be displayed
$signaler = "";
if ($duree_pause !== null) {
    if ($duree_pause > $durepause_minutes) {
        $signaler = "<button class='signaler' data-agent='{$agent}' data-duree='{$duree_pause}' data-durepause='{$durepause}'>Signaler</button>";
       // echo "duree_pause: " . $duree_pause . "<br>";
        //echo "durepause_minutes: " . $durepause_minutes . "<br>";
    }
}


        echo "<tr>
                <td data-label='Agent'>{$agent}</td>
                <td data-label='Start Time'>{$heure}</td>
                <td data-label='End Time'>{$heure_fin}</td>
                <td data-label='Duration'>{$duree_pause} minutes</td>
                <td data-label='Allowed Duration'>{$flux} minutes</td>
                <td data-label='Action'>{$signaler}</td>
                
                
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No breaks found</td></tr>";
}

// Closing the database connection
$conn->close();

?>
