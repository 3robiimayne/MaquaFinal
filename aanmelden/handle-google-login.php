<?php
session_start();
include("../includes/dbconn.inc.php");

header('Content-Type: application/json');
$logFile = 'debug.log';

// Log start of request
file_put_contents($logFile, "Request started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);
file_put_contents($logFile, "Raw input: " . $rawData . "\n", FILE_APPEND);

if (empty($data['credential'])) {
    file_put_contents($logFile, "No credential provided\n", FILE_APPEND);
    echo json_encode(['success' => false, 'error' => 'No credential provided']);
    exit;
}

try {
    $token = $data['credential'];
    $verification_url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($token);
    
    $response = @file_get_contents($verification_url);
    if ($response === false) {
        $error = error_get_last();
        throw new Exception('Failed to verify token with Google: ' . ($error['message'] ?? 'Unknown error'));
    }
    $payload = json_decode($response, true);
    file_put_contents($logFile, "Token response: " . $response . "\n", FILE_APPEND);

    if (!$payload || !isset($payload['email']) || $payload['aud'] !== '1078845914507-g26tju80p4jsjefebnkljdpk9fiv64bd.apps.googleusercontent.com') {
        throw new Exception('Invalid token or audience mismatch');
    }

    $email = $payload['email'];
    $name = $payload['name'] ?? 'Unknown';
    $googleId = $payload['sub'];
    $voornaam = $payload['given_name'] ?? '';

    file_put_contents($logFile, "Processing email: $email, Google ID: $googleId\n", FILE_APPEND);

    $stmt = mysqli_prepare($dbconn, "SELECT klantID, naam, voornaam, email, wachtwoord, geboortedatum, geslacht, straat, huisnummer, postcode, stad, land, telefoonnummer, adminkey FROM tblKlanten WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Failed to prepare SELECT statement: ' . mysqli_error($dbconn));
    }
    mysqli_stmt_bind_param($stmt, "s", $email);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('SELECT query failed: ' . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_store_result($stmt);

    $numRows = mysqli_stmt_num_rows($stmt);
    file_put_contents($logFile, "Number of rows found for email $email: $numRows\n", FILE_APPEND);

    if ($numRows > 0) {
        mysqli_stmt_bind_result($stmt, $klantID, $naam, $voornaam, $email, $wachtwoord, $geboortedatum, $geslacht, $straat, $huisnummer, $postcode, $stad, $land, $telefoonnummer, $adminkey);
        mysqli_stmt_fetch($stmt);

        $profileComplete = !empty($wachtwoord) && 
                          !empty($naam) && 
                          !empty($voornaam) && 
                          !empty($geboortedatum) && 
                          !empty($geslacht) && 
                          !empty($straat) && 
                          !empty($huisnummer) && 
                          !empty($postcode) && 
                          !empty($stad) && 
                          !empty($land) && 
                          !empty($telefoonnummer);

        $_SESSION['klantID'] = $klantID;
        $_SESSION['email'] = $email;
        $_SESSION['naam'] = $naam;
        $_SESSION['voornaam'] = $voornaam;
        $_SESSION['admin'] = $adminkey ?? 0;
        $_SESSION['google_login'] = true;

        $redirect = $profileComplete ? '/index.php' : '../registreren/complete-profile.php';
        file_put_contents($logFile, "User exists, klantID: $klantID, redirecting to: $redirect\n", FILE_APPEND);
        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        $naam = $payload['family_name'] ?? $name;
        $stmt = mysqli_prepare($dbconn, "INSERT INTO tblKlanten (naam, voornaam, email, google_id, adminkey) VALUES (?, ?, ?, ?, 0)");
        if (!$stmt) {
            throw new Exception('Failed to prepare INSERT statement: ' . mysqli_error($dbconn));
        }
        mysqli_stmt_bind_param($stmt, "ssss", $naam, $voornaam, $email, $googleId);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('INSERT query failed: ' . mysqli_stmt_error($stmt));
        }
        $klantID = mysqli_insert_id($dbconn);

        $_SESSION['klantID'] = $klantID;
        $_SESSION['email'] = $email;
        $_SESSION['naam'] = $naam;
        $_SESSION['voornaam'] = $voornaam;
        $_SESSION['admin'] = 0;
        $_SESSION['google_login'] = true;

        $redirect = '../registreren/complete-profile.php';
        file_put_contents($logFile, "New user created, klantID: $klantID, redirecting to: $redirect\n", FILE_APPEND);
        echo json_encode(['success' => true, 'redirect' => $redirect]);
    }
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    $errorMsg = 'Google login error: ' . $e->getMessage();
    error_log($errorMsg);
    file_put_contents($logFile, "Exception: " . $errorMsg . "\n", FILE_APPEND);
    http_response_code(500); // Explicitly set 500 for errors
    echo json_encode(['success' => false, 'error' => 'Authentication failed: ' . $e->getMessage()]);
}

mysqli_close($dbconn);
file_put_contents($logFile, "Request completed at " . date('Y-m-d H:i:s') . "\n\n", FILE_APPEND);
?>