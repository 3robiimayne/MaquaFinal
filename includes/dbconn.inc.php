<?php
define("SERVERNAME", "localhost");
define("USERNAME", "ismail_dbMaqua");
define("PASSWORD", "isi12345");
define("DATABASE", "ismail_dbMaqua");

// Maak de verbinding
$dbconn = mysqli_connect(SERVERNAME, USERNAME, PASSWORD, DATABASE);

// Controleer of de verbinding gelukt is
if (!$dbconn) {
    // Foutmelding als de verbinding niet is gelukt
    die("Verbinding met de database is mislukt: " . mysqli_connect_error());
} else {
    // Optioneel: echo om te bevestigen dat de verbinding gelukt is (voor debugging)
    // echo "Verbonden met de database!";
}
?>
