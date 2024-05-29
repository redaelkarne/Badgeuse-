<?php
    $servername = "192.168.0.12:3366";
    $username = "astus";
    $password = "123456";
    $dbname = "dick";
    date_default_timezone_set('Europe/Paris');
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
    
        // Si une ligne avec heure_fin vide est trouvée, calculer la différence
        if ($result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $heure = new DateTime($row2['heure']);
            $now = new DateTime();
            $difference = $now->diff($heure)->format('%H:%I:%S');
            $differenceInSeconds = $now->getTimestamp() - $heure->getTimestamp();

    // Convert Durepause to seconds
    list($hours, $minutes, $seconds) = explode(':', $row2['Durepause']);
    $durepauseInSeconds = $hours * 3600 + $minutes * 60 + $seconds;

    // Calculate remaining time for countdown
    $remainingTimeInSeconds = $durepauseInSeconds - $differenceInSeconds;

    
} else {
            header("Location: https://webclient.astus.fr/badg/form_pauses.php?identifiant=" . urlencode($identifiant) . "&nom_utilisateur=" . urlencode($nom_utilisateur));
            exit;
        }
    
        $stmt2->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Countdown Timer</title>
    <script>
        function startCountdown(duration) {
            var timer = duration, hours, minutes, seconds;
            setInterval(function () {
                hours = parseInt(timer / 3600, 10);
                minutes = parseInt((timer % 3600) / 60, 10);
                seconds = parseInt(timer % 60, 10);

                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                document.getElementById('countdown').textContent = hours + ":" + minutes + ":" + seconds;

                if (--timer < 0) {
                    
                    
                    clearInterval(countdownInterval); // Stop the countdown when it reaches 00:00:00
                    redirectUrl += '?identifiant=<?php echo urlencode($identifiant); ?>&motif=<?php echo urlencode($motif); ?>';
                    window.location.href = redirectUrl;
                    window.location.href = 'update_pause.php';
                 
                }
            }, 1000);
        }


    window.onload = function () {
        var countdownTime = <?php echo $remainingTimeInSeconds; ?>;
        startCountdown(countdownTime);
    };

    document.addEventListener('DOMContentLoaded', function() {
    var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    var endPauseButton = document.getElementById('endPause');
    var nomAgent = "<?php echo $nom_utilisateur; ?>"; 

    if (isMobile) {
        endPauseButton.disabled = true;
        
    } else {
        endPauseButton.addEventListener('click', function() {
            var url = 'update_pause.php?nom_agent=' + encodeURIComponent(nomAgent);
            window.location.href = url;
        });
    }

    var countdownTime = <?php echo $remainingTimeInSeconds; ?>;
    startCountdown(countdownTime);
});
    
</script>

</head>
<body>
   
<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh;">
    <h2>Temps restant dans votre pause</h2>
    <div id="countdown" style="text-align: center; font-size: 50px; font-weight: bold;"></div>
    <button id="endPause" style="display: block;">Fin de pause</button>
</div>


<?php echo $nom_utilisateur; ?>


</body>
</html>
