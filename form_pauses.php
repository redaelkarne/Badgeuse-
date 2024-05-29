<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indiquer une pause</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
// Retrieving variables from the URL
date_default_timezone_set('Europe/Paris');
$identifiant = $_GET['identifiant'];
$utilisateur = $_GET['nom_utilisateur'];
?>
    <h1>Indiquer une pause - <?php echo $identifiant; ?> - <?php echo $utilisateur; ?></h1>
    <form id="pauseForm" action="enregistrer_pause.php" method="post">
        <label for="identifiant" style="text-align: center;"></label><br>
        <input type="hidden" id="identifiant" name="identifiant" value="<?php echo $identifiant; ?>">
        <div style="text-align: center; margin-bottom: 20px;">
   

        <br>
        <label for="motif" style="text-align: center;">Motif de la pause :</label><br>
        <input type="hidden" id="motif" name="motif"> <!-- Champs cach� pour le motif -->
        <div style="text-align: center; margin-bottom: 20px;">
    <button type="button" class="button-72" onclick="setMotifAndSubmit('WC')">WC</button>
</div>

<div style="text-align: center; margin-bottom: 20px;">
    <button type="button" class="button-72" onclick="setMotifAndSubmit('PAUSE 10')">10</button>
    <button type="button" class="button-72" onclick="setMotifAndSubmit('PAUSE 15')">15</button>
</div>

<div style="text-align: center;">
    <button type="button" class="button-72" onclick="setMotifAndSubmit('PAUSE 45')">45</button>
    <button type="button" class="button-72" onclick="setMotifAndSubmit('PAUSE 1H')">1H</button>
</div>


        <br><br>
        <input type="submit" id="submitBtn" value="Enregistrer la pause" style="display: none;">
    </form>

    

    <script>
        function addDigit(digit) {
            var input = document.getElementById("identifiant");
            input.value += digit;
        }

        function clearDigits() {
            var input = document.getElementById("identifiant");
            input.value = "";
        }

        function setMotifAndSubmit(motif) {
            var motifInput = document.getElementById("motif");
            motifInput.value = motif;
            document.getElementById("submitBtn").click(); 
        }

                // Fonction pour indiquer la fin de la pause
                function endPause() {
            var motifInput = document.getElementById("motif");
            var motif = motifInput.value; 
            motifInput.value = "OUT PAUSE"; 
            document.getElementById("submitBtn").click(); 
        }

    </script>
</body>
</html>
