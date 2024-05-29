<?php
session_start();



$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifiant = $_POST['identifiant'];

    // Requête pour récupérer le nom de l'utilisateur
    $query1 = "SELECT `Nom Utilisateur` FROM utilisateurs WHERE log = ?";
    $stmt1 = $conn->prepare($query1);
    if ($stmt1 === false) {
        die('Erreur de préparation de la requête : ' . $conn->error);
    }
    $stmt1->bind_param("s", $identifiant);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    // Vérifiez si l'utilisateur existe et obtenez le nom de l'utilisateur
    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $nom_utilisateur = $row1['Nom Utilisateur'];

        // Ajouter nom_agent et heure à la session
        $_SESSION['nom_agent'] = $nom_utilisateur;
        $_SESSION['heure'] = date("Y-m-d H:i:s");
    } else {
        header("Location: https://webclient.astus.fr/badg/form_pauses.php?identifiant=" . urlencode($identifiant));
        $stmt1->close();
        exit;
    }
    $stmt1->close();

    // Requête pour vérifier s'il y a une entrée avec heure_fin vide
    $query2 = "SELECT heure, heure_fin, flux , Durepause FROM enregistrements WHERE Agent = ? AND heure_fin IS NULL";
    $stmt2 = $conn->prepare($query2);
    if ($stmt2 === false) {
        die('Erreur de préparation de la requête : ' . $conn->error);
    }
    $stmt2->bind_param("s", $nom_utilisateur); 
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    // Si une ligne avec heure_fin vide est trouvée, rediriger vers timer.php
    if ($result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        // Convert the database time to a DateTime object
        $pauseStartTime = new DateTime($row2['heure']);

        // Convert Durepause to seconds
        list($hours, $minutes, $seconds) = explode(':', $row2['Durepause']);
        $durepauseInSeconds = $hours * 3600 + $minutes * 60 + $seconds;

        // Calculate the elapsed time since the pause started
        $now = new DateTime();
        $differenceInSeconds = $now->getTimestamp() - $pauseStartTime->getTimestamp();

        // Calculate the remaining time for the pause
        $remainingTimeInSeconds = $durepauseInSeconds - $differenceInSeconds;

        // Make sure the remaining time is not negative
        $remainingTimeInSeconds = max(0, $remainingTimeInSeconds);

        // Redirect to timer.php
        header("Location: timer.php?remainingTime=" . $remainingTimeInSeconds);
        exit;
    }

    $stmt2->close();
}
?>
