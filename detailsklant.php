<?php
session_start();

// Check if user is an admin
$admin = $_SESSION["admin"] ?? 0;
if ($admin != 1) {
    header('Location: index.php');
    exit();
}

// Get klantID from URL
$klantID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($klantID <= 0) {
    header('Location: /Maqua/admin.php#klanten');
    exit();
}

// Connect to database
include("includes/dbconn.inc.php");

// Fetch customer details
$qrySelectKlant = "SELECT klantID, naam, voornaam, email, wachtwoord, geboortedatum, geslacht, straat, huisnummer, postcode, stad, land, telefoonnummer, adminkey, google_id 
                   FROM tblKlanten 
                   WHERE klantID = ?";
if ($stmt = mysqli_prepare($dbconn, $qrySelectKlant)) {
    mysqli_stmt_bind_param($stmt, "i", $klantID);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $klantID, $naam, $voornaam, $email, $wachtwoord, $geboortedatum, $geslacht, $straat, $huisnummer, $postcode, $stad, $land, $telefoonnummer, $adminkey, $google_id);
    if (mysqli_stmt_fetch($stmt)) {
        $klant = [
            'klantID' => $klantID,
            'naam' => $naam,
            'voornaam' => $voornaam,
            'email' => $email,
            'wachtwoord' => $wachtwoord ? '********' : 'Niet ingesteld (Google login)',
            'geboortedatum' => $geboortedatum,
            'geslacht' => $geslacht,
            'straat' => $straat,
            'huisnummer' => $huisnummer,
            'postcode' => $postcode,
            'stad' => $stad,
            'land' => $land,
            'telefoonnummer' => $telefoonnummer,
            'adminkey' => $adminkey,
            'google_id' => $google_id
        ];
    } else {
        $error = "Klant niet gevonden.";
    }
    mysqli_stmt_close($stmt);
} else {
    $error = "Fout bij het ophalen van klantgegevens: " . mysqli_error($dbconn);
}

mysqli_close($dbconn);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Klant Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D90429',
                        secondary: '#118AB2',
                        accent: '#FFD700',
                        dark: '#1A1A2E',
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A] text-white font-['Inter']">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="/Maqua/" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/Maqua/admin.php#klanten" class="text-white hover:text-primary transition-colors">Terug naar Admin</a>
                    <a href="/Maqua/index.php?logout=true" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Uitloggen</span>
                    </a>
                </div>
                <button id="mobile-menu-button" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="min-h-screen flex items-center justify-center pt-20 px-4">
        <div class="max-w-2xl w-full bg-[rgba(255,255,255,0.05)] rounded-xl p-6 md:p-8 backdrop-blur-md shadow-xl">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">Klant Details <span class="text-primary">MAQUA</span></h2>
            <?php if (isset($error)): ?>
                <p class="text-center text-red-500 mb-6"><?php echo $error; ?></p>
            <?php elseif (isset($klant)): ?>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Klant ID:</span>
                        <span><?php echo htmlspecialchars($klant['klantID']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Naam:</span>
                        <span><?php echo htmlspecialchars($klant['naam']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Voornaam:</span>
                        <span><?php echo htmlspecialchars($klant['voornaam']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">E-mail:</span>
                        <span><?php echo htmlspecialchars($klant['email']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Wachtwoord:</span>
                        <span><?php echo htmlspecialchars($klant['wachtwoord']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Geboortedatum:</span>
                        <span><?php echo $klant['geboortedatum'] ? htmlspecialchars(date('d/m/Y', strtotime($klant['geboortedatum']))) : 'Niet ingesteld'; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Geslacht:</span>
                        <span><?php echo htmlspecialchars($klant['geslacht'] ?: 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Adres:</span>
                        <span><?php echo htmlspecialchars(($klant['straat'] && $klant['huisnummer']) ? "$klant[straat] $klant[huisnummer]" : 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Postcode:</span>
                        <span><?php echo htmlspecialchars($klant['postcode'] ?: 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Stad:</span>
                        <span><?php echo htmlspecialchars($klant['stad'] ?: 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Land:</span>
                        <span><?php echo htmlspecialchars($klant['land'] ?: 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Telefoonnummer:</span>
                        <span><?php echo htmlspecialchars($klant['telefoonnummer'] ?: 'Niet ingesteld'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Admin:</span>
                        <span><?php echo $klant['adminkey'] ? 'Ja' : 'Nee'; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-300">Google ID:</span>
                        <span><?php echo htmlspecialchars($klant['google_id'] ?: 'Niet gekoppeld'); ?></span>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <a href="klanten.php#klanten" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors">Terug naar Klanten</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer (optional, can be removed or kept) -->
    <footer class="bg-[#0D0D1A] pt-20 pb-10">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
        </div>
    </footer>
</body>
</html>