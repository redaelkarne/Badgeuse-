<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indiquer une pause</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Indiquer une pause</h1>
    <form id="loginForm" action="login.php" method="post">
        <label for="identifiant" style="text-align: center;">Log (4 chiffres) :</label><br>
        <input type="text" id="identifiant" name="identifiant" pattern="\d{4}" maxlength="4" required>
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" class="button-72">Valider</button>
        </div>
    </form>

    <script>
        // JavaScript functions for adding digits and clearing input
        function addDigit(digit) {
            var input = document.getElementById("identifiant");
            if (input.value.length < 4) {
                input.value += digit;
            }
        }

        function clearDigits() {
            var input = document.getElementById("identifiant");
            input.value = "";
        }
    </script>
</body>
</html>
