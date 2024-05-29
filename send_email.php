<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agent = $_POST['agent'];
    $duree = $_POST['duree'];
    $durepause = $_POST['durepause'];
    date_default_timezone_set('Europe/Paris');
    // Your email address
    $to = "test";
    $subject = "Alerte Pause : $agent";
    $message = "$agent a depasseé le temps de sa pause.\r\n";
    $message .= "Duree autorisée: $durepause.";
    $message .= "Pause prise: $duree minutes.\r\n";
    $headers = "From: test\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    

    $mail_sent = mail($to, $subject, $message, $headers);

    if ($mail_sent) {
        echo "Email envoyé.";
    } else {
        error_log("Mail function returned false. Agent: $agent, Duration: $duree, Allowed Duration: $durepause");
        
        // Output debugging information
        echo "Debug info: <br>";
        echo "To: $to<br>";
        echo "Subject: $subject<br>";
        echo "Message: $message<br>";
        echo "Headers: $headers<br>";
        
        // Output the last error
        $error = error_get_last();
        echo "Error type: " . $error['type'] . "<br>";
        echo "Error message: " . $error['message'] . "<br>";
        echo "Error file: " . $error['file'] . "<br>";
        echo "Error line: " . $error['line'] . "<br>";
        
        echo "Failed to send email.";
    }
}
?>
