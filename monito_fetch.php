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
          WHERE heure_fin is NULL
          ORDER BY heure DESC";
$result = $conn->query($query);

// Initialize an array to store break information
$breaks = array();

// Checking if the query was successful
if ($result->num_rows > 0) {
    // Fetching results and constructing the array
    while($row = $result->fetch_assoc()) {
        $break = array();
        $break['Agent'] = htmlspecialchars($row['Agent']);
        $break['heure'] = htmlspecialchars($row['heure']);
        $break['heure_fin'] = htmlspecialchars($row['heure_fin']);
        $break['Durepause'] = htmlspecialchars($row['Durepause']);
        $break['flux'] = htmlspecialchars($row['flux']);

        // Add the break to the breaks array
        $breaks[] = $break;
    }
}

// Closing the database connection
$conn->close();

// Output the breaks array as JSON
echo json_encode($breaks);
?>
