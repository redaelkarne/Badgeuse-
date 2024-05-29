<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Europe/Paris');
// V�rifier si le formulaire a �t� soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // R�cup�rer les donn�es du formulaire
    $identifiant = $_POST["identifiant"];
    $motif = $_POST["motif"];
    
    // Connexion � la base de donn�es (� remplacer par vos informations de connexion)

    $conn = new mysqli($servername, $username, $password, $dbname);

    // V�rifier la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // R�cup�rer le nom de l'agent � partir de la table utilisateurs
    $sql_nom_agent = "SELECT `Nom utilisateur` FROM utilisateurs WHERE `log` = ?";
    $stmt = $conn->prepare($sql_nom_agent);
    $stmt->bind_param("s", $identifiant);
    $stmt->execute();
    $result_nom_agent = $stmt->get_result();
    
    if ($result_nom_agent->num_rows > 0) {
        $row = $result_nom_agent->fetch_assoc();
        $nom_agent = $row['Nom utilisateur'];

        // Enregistrer l'action dans la table enregistrements
        $heure = date("Y-m-d H:i:s");
        if ($motif !== "OUT PAUSE") {
            $flux = "$motif";
        } else {
            $flux = $motif;
        }
        if ($motif == 'PAUSE 10') {
            $durepause = '00:10:00';
        }else if ($motif == 'PAUSE 15'){
            $durepause = '00:15:00';
        }else if ($motif == 'PAUSE 45'){
            $durepause = '00:45:00';
        }else if ($motif == 'PAUSE 1H'){
            $durepause = '01:00:00';
        }else{
            $durepause = '00:00:00';
        }
            
        

        $sql_enregistrer_action = "INSERT INTO enregistrements (Agent, heure, flux , Durepause) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_enregistrer_action);
        $stmt->bind_param("ssss", $nom_agent, $heure, $flux, $durepause);
        $stmt->execute();

        // Affichage de succ�s
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indiquer une pause</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Pause (' . $motif . ') enregistree avec succes</h1>';
        
// Trigger the timer based on the motif
$temps_minuteur = 0;
switch ($motif) {
    case 'PAUSE 10':
        $temps_minuteur = 10 * 60; // 10 minutes
        break;
    case 'PAUSE 15':
        $temps_minuteur = 15 * 60; // 15 minutes
        break;
    case 'PAUSE 45':
        $temps_minuteur = 45 * 60; // 45 minutes
        break;
    case 'PAUSE 1H':
        $temps_minuteur = 60 * 60; // 60 minutes
        break;
    default:
        // Do nothing for other motifs
        break;
}
if ($temps_minuteur == 0) {
echo '<button id="endPause">Fin de pause</button>';
echo '<script>
var nomAgent = \'' . urlencode($nom_agent) . '\';

document.getElementById("endPause").addEventListener("click", function() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_pause.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert("La pause est terminee.");
            window.location.href = document.referrer; // Redirect to the previous page
        } else {
            alert("Erreur lors de la fin de la pause.");
        }
    };
    xhr.send("nom_agent=" + nomAgent);
});
</script>';} 

elseif ($temps_minuteur > 0) {
    // Display the timer
    echo '<div id="minuteur" style="text-align: center; font-size: 50px; font-weight: bold;"></div>';
    echo '<button id="endPause">Fin de pause</button>';
    echo '<script>
        var tempsRestant = ' . $temps_minuteur . ';
        var minuteur = setInterval(function() {
            var minutes = Math.floor(tempsRestant / 60);
            var secondes = tempsRestant % 60;
            document.getElementById("minuteur").innerText = minutes + "m " + secondes + "s";
            tempsRestant--;

            // Add a beep when 60 seconds are left
            if (tempsRestant === 60) {
                var beep = new Audio("beep.mp3");
                beep.play();
            }
            if (tempsRestant === 1) {
                var alerte = new Audio("alerte.mp3");
                alerte.play();
            }

            if (tempsRestant < 0) {
                clearInterval(minuteur);
                alert("La pause est terminée.");
            }
        }, 1000);

        document.getElementById("endPause").addEventListener("click", function() {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_pause.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert("La pause est terminee.");
                    window.location.href = document.referrer; // Redirect to the previous page
                } else {
                    alert("Erreur lors de la fin de la pause.");
                }
            };
            xhr.send("nom_agent=' . urlencode($nom_agent) . '");
        });
    </script>';
}

echo '</div>
</body>
</html>';
} else {
    echo "Identifiant invalide.";
}

$conn->close();
}
?>
