<?php
session_start();

// Controleer of gebruiker via Google is ingelogd
if (!isset($_SESSION['google_login']) || !isset($_SESSION['klantID'])) {
    header("Location: ../aanmelden/");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST["knop_complete_profile"])) {
    echo "Stap 1: Formulier ontvangen!<br>";

    include("../includes/dbconn.inc.php");
    
    if (!$dbconn) {
        die("Fout: Databaseverbinding mislukt - " . mysqli_connect_error());
    } else {
        echo "Stap 2: Verbinding met database succesvol!<br>";
    }

    $email = $_SESSION['email'];
    $naam = trim($_POST["naam"] ?? '');
    $voornaam = trim($_POST["voornaam"] ?? '');
    $geboortedatum = trim($_POST["geboortedatum"] ?? '');
    $geslacht = trim($_POST["geslacht"] ?? '');
    $straat = trim($_POST["straat"] ?? '');
    $huisnummer = trim($_POST["huisnummer"] ?? '');
    $busnummer = trim($_POST["busnummer"] ?? '');
    $postcode = trim($_POST["postcode"] ?? '');
    $stad = trim($_POST["stad"] ?? '');
    $land = trim($_POST["land"] ?? '');
    $telefoonnummer = trim($_POST["telefoonnummer"] ?? '');
    $wachtwoord = trim($_POST["wachtwoord"] ?? '');
    $wachtwoord_confirm = trim($_POST["wachtwoord-confirm"] ?? '');

    if (empty($naam) || empty($voornaam) || empty($geboortedatum) || empty($geslacht) || 
        empty($straat) || empty($huisnummer) || empty($postcode) || empty($stad) || 
        empty($land) || empty($telefoonnummer) || empty($wachtwoord)) {
        die("Fout: Vereiste velden ontbreken!<br>");
    } elseif ($wachtwoord !== $wachtwoord_confirm) {
        die("Fout: Wachtwoorden komen niet overeen!<br>");
    }

    $hashed_wachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);

    echo "<pre>";
    var_dump([
        'naam' => $naam, 'voornaam' => $voornaam, 'geboortedatum' => $geboortedatum, 
        'geslacht' => $geslacht, 'straat' => $straat, 'huisnummer' => $huisnummer, 
        'busnummer' => $busnummer, 'postcode' => $postcode, 'stad' => $stad, 
        'land' => $land, 'telefoonnummer' => $telefoonnummer, 'email' => $email, 
        'wachtwoord' => $hashed_wachtwoord
    ]);
    echo "</pre>";

    $qryUpdateKlant = "UPDATE tblKlanten SET 
        naam = ?, voornaam = ?, geboortedatum = ?, geslacht = ?, 
        straat = ?, huisnummer = ?, busnummer = ?, postcode = ?, 
        stad = ?, land = ?, telefoonnummer = ?, wachtwoord = ?
        WHERE klantID = ? AND email = ?";

    if ($stmUpdateKlant = mysqli_prepare($dbconn, $qryUpdateKlant)) {
        mysqli_stmt_bind_param(
            $stmUpdateKlant, "ssssssssssssis", 
            $naam, $voornaam, $geboortedatum, $geslacht, 
            $straat, $huisnummer, $busnummer, $postcode, 
            $stad, $land, $telefoonnummer, $hashed_wachtwoord,
            $_SESSION['klantID'], $email
        );

        echo "Stap 3: Query correct voorbereid!<br>";

        if (mysqli_stmt_execute($stmUpdateKlant)) {
            echo "Stap 4: Profiel update succesvol!<br>";
            $_SESSION['naam'] = $naam;
            $_SESSION['voornaam'] = $voornaam;
            header("Location: ../index.php");
            exit();
        } else {
            die("Fout bij updaten profiel: " . mysqli_stmt_error($stmUpdateKlant));
        }
        mysqli_stmt_close($stmUpdateKlant);
    } else {
        die("Fout bij voorbereiden van query: " . mysqli_error($dbconn));
    }
    mysqli_close($dbconn);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Profiel Voltooien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <nav id="navbar" class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                </a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#nieuws" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Nieuws</a>
                    <a href="#over" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Over</a>
                    <a href="#locatie" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Locatie</a>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#shop" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shop</span>
                    </a>
                    <a href="../index.php?logout=true" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Uitloggen</span>
                    </a>
                    <a href="#tickets" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors flex items-center space-x-2">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                    </a>
                </div>
                <button id="mobile-menu-button" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <div id="mobile-menu" class="hidden fixed top-[68px] left-0 w-full h-screen bg-dark md:hidden z-40">
                <div class="flex flex-col p-6 space-y-6">
                    <a href="#nieuws" class="text-white text-lg hover:text-primary">Nieuws</a>
                    <a href="#over" class="text-white text-lg hover:text-primary">Over</a>
                    <a href="#locatie" class="text-white text-lg hover:text-primary">Locatie</a>
                    <div class="border-t border-gray-700 pt-6 flex flex-col space-y-6">
                        <a href="#shop" class="flex items-center space-x-3 text-white hover:text-primary">
                            <i class="fas fa-shopping-cart w-6"></i>
                            <span>Shop</span>
                        </a>
                        <a href="../index.php?logout=true" class="flex items-center space-x-3 text-white hover:text-primary">
                            <i class="fas fa-sign-out-alt w-6"></i>
                            <span>Uitloggen</span>
                        </a>
                        <a href="#tickets" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] flex items-center justify-center space-x-2">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Tickets</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Completion Section -->
    <section class="min-h-screen flex items-center justify-center pt-20">
        <div class="container mx-auto px-4">
            <div class="max-w-lg mx-auto bg-[rgba(255,255,255,0.05)] rounded-xl p-8 backdrop-blur-md shadow-xl">
                <h2 class="text-3xl font-bold text-center mb-6">Profiel Voltooien <span class="text-primary">MAQUA</span></h2>
                <p class="text-gray-300 text-center mb-8">Vul je gegevens aan om je registratie te voltooien.</p>

                <!-- Progress Bar -->
                <div class="flex justify-between mb-8">
                    <div class="flex flex-col items-center">
                        <span class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center font-bold">1</span>
                        <span class="text-sm mt-2 text-gray-300">Basis Info</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="w-8 h-8 bg-gray-500 text-white rounded-full flex items-center justify-center font-bold">2</span>
                        <span class="text-sm mt-2 text-gray-300">Contact</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="w-8 h-8 bg-gray-500 text-white rounded-full flex items-center justify-center font-bold">3</span>
                        <span class="text-sm mt-2 text-gray-300">Account</span>
                    </div>
                </div>

                <!-- Registration Form -->
                <form id="registerForm" method="POST" action="" class="space-y-6">
                    <!-- Step 1: Basic Info -->
                    <div class="form-step" data-step="1">
                        <div class="space-y-6">
                            <div class="form-group">
                                <label for="voornaam" class="block text-sm font-medium mb-2">Voornaam</label>
                                <input type="text" id="voornaam" name="voornaam" value="<?php echo htmlspecialchars($_SESSION['voornaam'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Voornaam">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige voornaam in</span>
                            </div>
                            <div class="form-group">
                                <label for="naam" class="block text-sm font-medium mb-2">Achternaam</label>
                                <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($_SESSION['naam'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Achternaam">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige achternaam in</span>
                            </div>
                            <div class="form-group">
                                <label for="land" class="block text-sm font-medium mb-2">Land</label>
                                <button type="button" id="land-btn" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 text-left focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center justify-between">
                                    <span id="land-selected"><?php echo isset($_SESSION['land']) ? ($_SESSION['land'] == 'BE' ? 'België' : 'Nederland') : 'Selecteer een land'; ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <input type="hidden" id="land" name="land" value="<?php echo htmlspecialchars($_SESSION['land'] ?? ''); ?>">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Selecteer een geldig land</span>
                            </div>
                            <div class="form-group">
                                <label for="geboortedatum" class="block text-sm font-medium mb-2">Geboortedatum</label>
                                <button type="button" id="date-btn" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 text-left focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center justify-between">
                                    <span id="date-selected"><?php echo isset($_SESSION['geboortedatum']) ? date('d/m/Y', strtotime($_SESSION['geboortedatum'])) : 'Kies je geboortedatum'; ?></span>
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                                <input type="hidden" id="geboortedatum" name="geboortedatum" value="<?php echo htmlspecialchars($_SESSION['geboortedatum'] ?? ''); ?>">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige geboortedatum in</span>
                            </div>
                            <div class="form-group">
                                <label class="block text-sm font-medium mb-2">Geslacht</label>
                                <div class="flex space-x-4">
                                    <button type="button" class="gender-btn flex-1 py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all <?php echo (isset($_SESSION['geslacht']) && $_SESSION['geslacht'] == 'man') ? 'bg-primary text-white' : ''; ?>" data-value="man">
                                        <i class="fas fa-mars mr-2"></i>Man
                                    </button>
                                    <button type="button" class="gender-btn flex-1 py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all <?php echo (isset($_SESSION['geslacht']) && $_SESSION['geslacht'] == 'vrouw') ? 'bg-primary text-white' : ''; ?>" data-value="vrouw">
                                        <i class="fas fa-venus mr-2"></i>Vrouw
                                    </button>
                                </div>
                                <input type="hidden" id="geslacht" name="geslacht" value="<?php echo htmlspecialchars($_SESSION['geslacht'] ?? ''); ?>">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Selecteer een geslacht</span>
                            </div>
                        </div>
                        <button type="button" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-[#b50322] transition-colors next-step mt-6" onclick="validateStep1()">Volgende</button>
                    </div>

                    <!-- Step 2: Contact Info -->
                    <div class="form-step hidden" data-step="2">
                        <div class="space-y-6">
                            <div class="form-group">
                                <label for="telefoonnummer" class="block text-sm font-medium mb-2">Telefoonnummer</label>
                                <div class="flex">
                                    <button type="button" id="phone-prefix-btn" class="bg-[rgba(255,255,255,0.1)] text-white border-none rounded-l-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center">
                                        <img id="phone-prefix-flag" src="https://flagcdn.com/24x18/be.png" alt="België" class="mr-2 w-6 h-4">
                                        <span id="phone-prefix-selected">+32</span>
                                        <i class="fas fa-chevron-down ml-2"></i>
                                    </button>
                                    <input type="tel" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($_SESSION['telefoonnummer'] ?? ''); ?>" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white border-none rounded-r-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="123 456 789">
                                </div>
                                <input type="hidden" id="phone-prefix" name="phone-prefix" value="+32">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldig telefoonnummer in</span>
                            </div>
                            <div class="form-group">
                                <label for="straat" class="block text-sm font-medium mb-2">Straat</label>
                                <input type="text" id="straat" name="straat" value="<?php echo htmlspecialchars($_SESSION['straat'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Straatnaam">
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige straatnaam in</span>
                            </div>
                            <div class="flex space-x-4">
                                <div class="form-group flex-1">
                                    <label for="huisnummer" class="block text-sm font-medium mb-2">Huisnummer</label>
                                    <input type="text" id="huisnummer" name="huisnummer" value="<?php echo htmlspecialchars($_SESSION['huisnummer'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="123">
                                    <span class="error-text text-red-500 text-sm hidden mt-1"></span>
                                </div>
                                <div class="form-group flex-1">
                                    <label for="busnummer" class="block text-sm font-medium mb-2">Busnummer</label>
                                    <input type="text" id="busnummer" name="busnummer" value="<?php echo htmlspecialchars($_SESSION['busnummer'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Optioneel">
                                    <span class="error-text text-red-500 text-sm hidden mt-1"></span>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <div class="form-group flex-1">
                                    <label for="postcode" class="block text-sm font-medium mb-2">Postcode</label>
                                    <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($_SESSION['postcode'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="3000">
                                    <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige postcode in</span>
                                </div>
                                <div class="form-group flex-1">
                                    <label for="stad" class="block text-sm font-medium mb-2">Stad</label>
                                    <input type="text" id="stad" name="stad" value="<?php echo htmlspecialchars($_SESSION['stad'] ?? ''); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="Antwerpia">
                                    <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldige stad in</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-4 mt-6">
                            <button type="button" class="w-full bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors prev-step">Vorige</button>
                            <button type="button" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-[#b50322] transition-colors next-step" onclick="validateStep2()">Volgende</button>
                        </div>
                    </div>

                    <!-- Step 3: Account Info -->
                    <div class="form-step hidden" data-step="3">
                        <div class="space-y-6">
                            <div class="form-group">
                                <label for="email" class="block text-sm font-medium mb-2">E-mailadres</label>
                                <div class="relative">
                                    <input type="email" id="email" name="email" readonly value="<?php echo htmlspecialchars($_SESSION['email']); ?>" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="jouw@email.com">
                                    <i class="fas fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldig e-mailadres in</span>
                            </div>
                            <div class="form-group">
                                <label for="wachtwoord" class="block text-sm font-medium mb-2">Wachtwoord</label>
                                <div class="relative">
                                    <input type="password" id="wachtwoord" name="wachtwoord" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="••••••••">
                                    <button type="button" class="toggle-password absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                                <span class="error-text text-red-500 text-sm hidden mt-1">Voer een geldig wachtwoord in</span>
                            </div>
                            <div class="form-group">
                                <label for="wachtwoord-confirm" class="block text-sm font-medium mb-2">Bevestig wachtwoord</label>
                                <div class="relative">
                                    <input type="password" id="wachtwoord-confirm" name="wachtwoord-confirm" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="••••••••">
                                    <button type="button" class="toggle-password absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                                <span class="error-text text-red-500 text-sm hidden mt-1">Wachtwoorden moeten overeenkomen</span>
                            </div>
                            <div class="form-group">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="reg-terms" name="terms" class="form-checkbox text-primary h-5 w-5">
                                    <span class="text-gray-300 text-sm">Ik ga akkoord met de <a href="#" class="text-primary hover:underline">algemene voorwaarden</a> en <a href="#" class="text-primary hover:underline">privacyverklaring</a></span>
                                </label>
                                <span class="error-text text-red-500 text-sm hidden mt-1">Accepteer de voorwaarden</span>
                            </div>
                        </div>
                        <div class="flex space-x-4 mt-6">
                            <button type="button" class="w-full bg-gray-500 text-white py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors prev-step">Vorige</button>
                            <button type="submit" name="knop_complete_profile" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-[#b50322] transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-user-plus"></i>
                                <span>Profiel Voltooien</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Land Popup -->
    <div id="land-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-dark rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
            <h3 class="text-lg font-bold mb-4">Kies een land</h3>
            <div class="space-y-2">
                <button class="country-option w-full text-left py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all" data-value="BE">
                    <img src="https://flagcdn.com/24x18/be.png" alt="België" class="inline-block mr-2 w-6 h-4"> België
                </button>
                <button class="country-option w-full text-left py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all" data-value="NL">
                    <img src="https://flagcdn.com/24x18/nl.png" alt="Nederland" class="inline-block mr-2 w-6 h-4"> Nederland
                </button>
            </div>
            <button id="close-land-popup" class="mt-4 w-full bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition-all">Sluiten</button>
        </div>
    </div>

    <!-- Phone Prefix Popup -->
    <div id="phone-prefix-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-dark rounded-lg p-6 w-full max-w-md max-h-[80vh] overflow-y-auto">
            <h3 class="text-lg font-bold mb-4">Kies een landcode</h3>
            <div class="space-y-2">
                <button class="prefix-option w-full text-left py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all" data-prefix="+32" data-country="BE">
                    <img src="https://flagcdn.com/24x18/be.png" alt="België" class="inline-block mr-2 w-6 h-4"> België (+32)
                </button>
                <button class="prefix-option w-full text-left py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all" data-prefix="+31" data-country="NL">
                    <img src="https://flagcdn.com/24x18/nl.png" alt="Nederland" class="inline-block mr-2 w-6 h-4"> Nederland (+31)
                </button>
                <button class="prefix-option w-full text-left py-3 px-4 bg-[rgba(255,255,255,0.1)] rounded-lg hover:bg-[rgba(255,255,255,0.2)] transition-all" data-prefix="+33" data-country="FR">
                    <img src="https://flagcdn.com/24x18/fr.png" alt="Frankrijk" class="inline-block mr-2 w-6 h-4"> Frankrijk (+33)
                </button>
            </div>
            <button id="close-phone-prefix-popup" class="mt-4 w-full bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition-all">Sluiten</button>
        </div>
    </div>

   <!-- Date Popup -->
<div id="date-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-dark rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4">Kies je geboortedatum</h3>
        <div class="grid grid-cols-3 gap-4">
            <!-- Day Dropdown -->
            <div class="relative">
                <button type="button" id="day-btn" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 text-left focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center justify-between">
                    <span id="day-selected">Dag</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="day-dropdown" class="absolute w-full bg-dark rounded-lg mt-1 max-h-48 overflow-y-auto hidden z-50">
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <button type="button" class="day-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="<?php echo sprintf('%02d', $i); ?>">
                            <?php echo $i; ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Month Dropdown -->
            <div class="relative">
                <button type="button" id="month-btn" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 text-left focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center justify-between">
                    <span id="month-selected">Maand</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="month-dropdown" class="absolute w-full bg-dark rounded-lg mt-1 max-h-48 overflow-y-auto hidden z-50">
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="01">Januari</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="02">Februari</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="03">Maart</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="04">April</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="05">Mei</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="06">Juni</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="07">Juli</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="08">Augustus</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="09">September</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="10">Oktober</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="11">November</button>
                    <button type="button" class="month-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="12">December</button>
                </div>
            </div>

            <!-- Year Dropdown -->
            <div class="relative">
                <button type="button" id="year-btn" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 text-left focus:outline-none focus:ring-2 focus:ring-primary transition-all flex items-center justify-between">
                    <span id="year-selected">Jaar</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="year-dropdown" class="absolute w-full bg-dark rounded-lg mt-1 max-h-48 overflow-y-auto hidden z-50">
                    <?php for ($i = date('Y'); $i >= date('Y') - 100; $i--): ?>
                        <button type="button" class="year-option w-full text-left py-2 px-4 hover:bg-primary hover:text-white transition-all" data-value="<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <button id="save-date" class="mt-4 w-full bg-primary text-white py-2 rounded-lg hover:bg-[#b50322] transition-all">Opslaan</button>
        <button id="close-date-popup" class="mt-2 w-full bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition-all">Sluiten</button>
    </div>
</div>

    <!-- Footer -->
    <footer class="bg-[#0D0D1A] pt-20 pb-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-16">
                <div>
                    <a href="index.php" class="flex items-center mb-6">
                        <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                        <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                    </a>
                    <p class="text-gray-400 mb-6">Het MAQUA Museum brengt de rijke geschiedenis van Antwerpia tot leven met meeslepende tentoonstellingen en interactieve ervaringen.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">Snelle Links<span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span></h3>
                    <ul class="space-y-3">
                        <li><a href="#nieuws" class="text-gray-400 hover:text-primary transition-colors">Nieuws</a></li>
                        <li><a href="#over" class="text-gray-400 hover:text-primary transition-colors">Over Ons</a></li>
                        <li><a href="#collection" class="text-gray-400 hover:text-primary transition-colors">Collectie</a></li>
                        <li><a href="#events" class="text-gray-400 hover:text-primary transition-colors">Evenementen</a></li>
                        <li><a href="#tickets" class="text-gray-400 hover:text-primary transition-colors">Tickets</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">Contact<span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span></h3>
                    <ul class="space-y-4">
                        <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i><span class="text-gray-400">Museumplein 123, 3000 Antwerpia</span></li>
                        <li class="flex items-start"><i class="fas fa-phone-alt mt-1 mr-3 text-primary"></i><span class="text-gray-400">+32 123 456 789</span></li>
                        <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-3 text-primary"></i><span class="text-gray-400">info@maqua.be</span></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">Blijf op de hoogte<span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span></h3>
                    <p class="text-gray-400 mb-4">Meld je aan voor onze nieuwsbrief.</p>
                    <form class="mb-4">
                        <div class="flex">
                            <input type="email" placeholder="Je e-mailadres" class="py-3 px-4 rounded-l-lg w-full bg-[rgba(255,255,255,0.1)] text-white border-none focus:outline-none">
                            <button type="submit" class="bg-primary text-white py-3 px-4 rounded-r-lg hover:bg-[#b50322] transition-colors"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="pt-8 mt-8 border-t border-gray-800 text-center">
                <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
<script>
// Form Step Navigation
const formSteps = document.querySelectorAll('.form-step');
const nextButtons = document.querySelectorAll('.next-step');
const prevButtons = document.querySelectorAll('.prev-step');
const progressSteps = document.querySelectorAll('.flex.flex-col.items-center span:first-child');
let currentStep = 1;

// Reset error states
function resetErrors(step) {
    const inputs = formSteps[step - 1].querySelectorAll('input, select, button');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
        const errorText = input.parentElement.querySelector('.error-text') || 
                         input.parentElement.parentElement.querySelector('.error-text');
        if (errorText) errorText.classList.add('hidden');
    });
}

// Validate Step 1
function validateStep1() {
    resetErrors(1);
    let isValid = true;

    const voornaam = document.getElementById('voornaam');
    const naam = document.getElementById('naam');
    const land = document.getElementById('land');
    const geboortedatum = document.getElementById('geboortedatum');
    const geslacht = document.getElementById('geslacht');

    if (!voornaam.value.trim()) {
        voornaam.classList.add('border-red-500');
        voornaam.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!naam.value.trim()) {
        naam.classList.add('border-red-500');
        naam.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!land.value) {
        document.getElementById('land-btn').classList.add('border-red-500');
        land.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!geboortedatum.value) {
        document.getElementById('date-btn').classList.add('border-red-500');
        geboortedatum.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!geslacht.value) {
        const geslachtContainer = document.querySelector('.form-group .flex.space-x-4');
        geslachtContainer.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }

    if (isValid) {
        formSteps[currentStep - 1].classList.add('hidden');
        currentStep++;
        formSteps[currentStep - 1].classList.remove('hidden');
        updateProgress();
    }
}

// Validate Step 2
function validateStep2() {
    resetErrors(2);
    let isValid = true;

    const telefoonnummer = document.getElementById('telefoonnummer');
    const straat = document.getElementById('straat');
    const postcode = document.getElementById('postcode');
    const stad = document.getElementById('stad');

    if (!telefoonnummer.value.trim()) {
        telefoonnummer.classList.add('border-red-500');
        telefoonnummer.parentElement.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!straat.value.trim()) {
        straat.classList.add('border-red-500');
        straat.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!postcode.value.trim()) {
        postcode.classList.add('border-red-500');
        postcode.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }
    if (!stad.value.trim()) {
        stad.classList.add('border-red-500');
        stad.nextElementSibling.classList.remove('hidden');
        isValid = false;
    }

    if (isValid) {
        formSteps[currentStep - 1].classList.add('hidden');
        currentStep++;
        formSteps[currentStep - 1].classList.remove('hidden');
        updateProgress();
    }
}

prevButtons.forEach(button => {
    button.addEventListener('click', () => {
        if (currentStep > 1) {
            resetErrors(currentStep);
            formSteps[currentStep - 1].classList.add('hidden');
            currentStep--;
            formSteps[currentStep - 1].classList.remove('hidden');
            updateProgress();
        }
    });
});

function updateProgress() {
    progressSteps.forEach((step, index) => {
        if (index < currentStep) {
            step.classList.remove('bg-gray-500');
            step.classList.add('bg-primary');
        } else {
            step.classList.remove('bg-primary');
            step.classList.add('bg-gray-500');
        }
    });
}

// Land Popup
const landBtn = document.getElementById('land-btn');
const landPopup = document.getElementById('land-popup');
const landOptions = document.querySelectorAll('.country-option');
const landSelected = document.getElementById('land-selected');
const landInput = document.getElementById('land');
const closeLandPopup = document.getElementById('close-land-popup');

landBtn.addEventListener('click', () => {
    landPopup.classList.remove('hidden');
});

landOptions.forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        landSelected.textContent = option.textContent.trim();
        landInput.value = value;
        landPopup.classList.add('hidden');
    });
});

closeLandPopup.addEventListener('click', () => {
    landPopup.classList.add('hidden');
});

// Phone Prefix Popup
const phonePrefixBtn = document.getElementById('phone-prefix-btn');
const phonePrefixPopup = document.getElementById('phone-prefix-popup');
const prefixOptions = document.querySelectorAll('.prefix-option');
const phonePrefixSelected = document.getElementById('phone-prefix-selected');
const phonePrefixFlag = document.getElementById('phone-prefix-flag');
const phonePrefixInput = document.getElementById('phone-prefix');
const closePhonePrefixPopup = document.getElementById('close-phone-prefix-popup');

phonePrefixBtn.addEventListener('click', () => {
    phonePrefixPopup.classList.remove('hidden');
});

prefixOptions.forEach(option => {
    option.addEventListener('click', () => {
        const prefix = option.getAttribute('data-prefix');
        const country = option.getAttribute('data-country');
        phonePrefixSelected.textContent = prefix;
        phonePrefixFlag.src = `https://flagcdn.com/24x18/${country.toLowerCase()}.png`;
        phonePrefixFlag.alt = option.textContent.trim();
        phonePrefixInput.value = prefix;
        phonePrefixPopup.classList.add('hidden');
    });
});

closePhonePrefixPopup.addEventListener('click', () => {
    phonePrefixPopup.classList.add('hidden');
});

// Gender Selection
const genderButtons = document.querySelectorAll('.gender-btn');
const geslachtInput = document.getElementById('geslacht');

genderButtons.forEach(button => {
    button.addEventListener('click', () => {
        genderButtons.forEach(btn => btn.classList.remove('bg-primary', 'text-white'));
        button.classList.add('bg-primary', 'text-white');
        geslachtInput.value = button.getAttribute('data-value');
    });
});

// Date Popup with Custom Dropdowns
const dateBtn = document.getElementById('date-btn');
const datePopup = document.getElementById('date-popup');
const dateSelected = document.getElementById('date-selected');
const geboortedatumInput = document.getElementById('geboortedatum');
const dayBtn = document.getElementById('day-btn');
const monthBtn = document.getElementById('month-btn');
const yearBtn = document.getElementById('year-btn');
const dayDropdown = document.getElementById('day-dropdown');
const monthDropdown = document.getElementById('month-dropdown');
const yearDropdown = document.getElementById('year-dropdown');
const daySelected = document.getElementById('day-selected');
const monthSelected = document.getElementById('month-selected');
const yearSelected = document.getElementById('year-selected');
const saveDate = document.getElementById('save-date');
const closeDatePopup = document.getElementById('close-date-popup');

// Initialize date dropdowns with current values if they exist
if (geboortedatumInput.value) {
    const [year, month, day] = geboortedatumInput.value.split('-');
    daySelected.textContent = day;
    const monthText = document.querySelector(`.month-option[data-value="${month}"]`).textContent;
    monthSelected.textContent = monthText;
    yearSelected.textContent = year;
}

dateBtn.addEventListener('click', () => {
    datePopup.classList.remove('hidden');
});

// Toggle Day Dropdown
dayBtn.addEventListener('click', () => {
    dayDropdown.classList.toggle('hidden');
    monthDropdown.classList.add('hidden');
    yearDropdown.classList.add('hidden');
});

// Toggle Month Dropdown
monthBtn.addEventListener('click', () => {
    monthDropdown.classList.toggle('hidden');
    dayDropdown.classList.add('hidden');
    yearDropdown.classList.add('hidden');
});

// Toggle Year Dropdown
yearBtn.addEventListener('click', () => {
    yearDropdown.classList.toggle('hidden');
    dayDropdown.classList.add('hidden');
    monthDropdown.classList.add('hidden');
});

// Handle Day Selection
document.querySelectorAll('.day-option').forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        daySelected.textContent = value;
        dayDropdown.classList.add('hidden');
    });
});

// Handle Month Selection
document.querySelectorAll('.month-option').forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        monthSelected.textContent = option.textContent;
        monthDropdown.classList.add('hidden');
    });
});

// Handle Year Selection
document.querySelectorAll('.year-option').forEach(option => {
    option.addEventListener('click', () => {
        const value = option.getAttribute('data-value');
        yearSelected.textContent = value;
        yearDropdown.classList.add('hidden');
    });
});

// Save Date
saveDate.addEventListener('click', () => {
    const day = daySelected.textContent;
    const month = Array.from(document.querySelectorAll('.month-option')).find(option => option.textContent === monthSelected.textContent)?.getAttribute('data-value');
    const year = yearSelected.textContent;
    if (day !== 'Dag' && month && year !== 'Jaar') {
        const dateStr = `${year}-${month}-${day}`;
        dateSelected.textContent = `${day}/${month}/${year}`;
        geboortedatumInput.value = dateStr;
        datePopup.classList.add('hidden');
    } else {
        alert('Selecteer een geldige dag, maand en jaar.');
    }
});

closeDatePopup.addEventListener('click', () => {
    datePopup.classList.add('hidden');
});

// Password Toggle
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', () => {
        const input = button.previousElementSibling;
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

// Mobile Menu Toggle
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
});
</script>
</body>
</html>