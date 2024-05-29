<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Paris');
// Retrieving database connection parameters


// Creating a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Checking if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieving the agent name from the POST request
$nom_agent = $_REQUEST['nom_agent'];
// Updating the database record
$current_time = date("Y-m-d H:i:s");
$query = "UPDATE enregistrements SET heure_fin = '$current_time' WHERE Agent = '$nom_agent' ORDER BY heure DESC LIMIT 1";

// Executing the query and checking for errors
if ($conn->query($query) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}
$query2 = "
    SELECT 
        ROUND(SUM(GREATEST(
            TIME_TO_SEC(TIMEDIFF(heure_fin, heure)) - TIME_TO_SEC(Durepause) - 60, 
            0
        )) / 60, 2) AS malus
    FROM 
        enregistrements
    WHERE
        Agent = '$nom_agent'

";
$result = $conn->query($query2);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $malus = $row['malus'];

    // Check if malus is positive
    if ($malus >= 0) {
        // Convert the malus to hours, minutes, and seconds
        $malus_in_seconds = $malus * 60;
        $malus_formatted = gmdate("H:i:s", $malus_in_seconds);

        // Update the malus in the database
        $query = "UPDATE enregistrements SET Malus = '$malus_formatted' WHERE Agent = '$nom_agent' AND heure_fin IS NOT NULL ORDER BY heure DESC LIMIT 1";
        if ($conn->query($query) === TRUE) {
            echo "Malus calculated and updated successfully";
        } else {
            echo "Error updating malus: " . $conn->error;
        }
    } else {
        echo "Malus is negative, no update performed";
    }
} else {
    echo "No data found for agent: " . $nom_agent;
}

// Closing the database connection
$conn->close();
echo "<script>
alert('Pause terminee');
window.location.href='https://webclient.astus.fr/badg/login-index.php';
</script>";
exit();
?>
