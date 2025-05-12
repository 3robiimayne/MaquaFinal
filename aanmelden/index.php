<?php
// Sessies starten
session_start();

// Variabelen declaratie
$melding = "";

// Error reporting inschakelen voor debuggen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST["knop_Aanmelden"])) {
    // Connectie met de databank maken
    include("../includes/dbconn.inc.php");

    // Input opschonen
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Query om klantengegevens op te vragen - Gebruik 'adminkey' in plaats van 'admin'
    $qrySelectKlant = "SELECT klantID, naam, voornaam, email, wachtwoord, adminkey FROM tblKlanten WHERE email = ?";

    // Prepared statement
    if ($stmtSelectKlant = mysqli_prepare($dbconn, $qrySelectKlant)) {
        mysqli_stmt_bind_param($stmtSelectKlant, "s", $email);
        if (mysqli_stmt_execute($stmtSelectKlant) === false) {
            $melding = "Fout bij het uitvoeren van de query: " . mysqli_stmt_error($stmtSelectKlant);
        } else {
            mysqli_stmt_store_result($stmtSelectKlant);

            if (mysqli_stmt_num_rows($stmtSelectKlant) > 0) {
                // Gebruik 'adminkey' in bind_result
                mysqli_stmt_bind_result($stmtSelectKlant, $klantID, $naam, $voornaam, $email, $dbwachtwoord, $adminkey);
                mysqli_stmt_fetch($stmtSelectKlant);

                if (password_verify($password, $dbwachtwoord)) {
                    // Sessie variabelen instellen
                    $_SESSION["klantID"] = $klantID;
                    $_SESSION["naam"] = $naam;
                    $_SESSION["voornaam"] = $voornaam;
                    $_SESSION["email"] = $email;
                    $_SESSION["admin"] = $adminkey;

                    // Direct doorsturen naar index.php
                    header("Location: ../index.php");
                    exit;
                } else {
                    $melding = "E-mail en wachtwoord combinatie is foutief!";
                }
            } else {
                $melding = "E-mail adres was niet gevonden!";
            }
        }
        mysqli_stmt_close($stmtSelectKlant);
    } else {
        $melding = "Fout bij het voorbereiden van de query: " . mysqli_error($dbconn);
    }
    mysqli_close($dbconn);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Aanmelden</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Google Sign-In Script -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- Custom Tailwind Config -->
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
    <nav id="navbar" class="fixed top-0 left-0 w-full bg-[rgba(18,18,42,0.95)] backdrop-blur-xl z-50 py-4 shadow-lg">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="../index.php" class="flex items-center">
                    <span class="text-2xl font-bold font-['Playfair_Display'] text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1 animate-pulse"></span>
                </a>
                <!-- Desktop Icons -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#shop" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shop</span>
                    </a>
                    <?php if (isset($_SESSION["klantID"])): ?>
                        <!-- Logged In: Show Logout -->
                        <a href="/Maqua/index.php?logout=true" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Uitloggen</span>
                        </a>
                    <?php else: ?>
                        <!-- Logged Out: Show Login -->
                        <a href="index.php" class="flex items-center space-x-2 text-primary transition-colors">
                            <i class="fas fa-user"></i>
                            <span>Account</span>
                        </a>
                    <?php endif; ?>
                    <a href="#tickets" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#a00d26] transition-colors flex items-center space-x-2 hover-lift">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                    </a>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
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
                        <?php if (isset($_SESSION["klantID"])): ?>
                            <!-- Logged In: Show Logout -->
                            <a href="/Maqua/index.php?logout=true" class="flex items-center space-x-3 text-white hover:text-primary">
                                <i class="fas fa-sign-out-alt w-6"></i>
                                <span>Uitloggen</span>
                            </a>
                        <?php else: ?>
                            <!-- Logged Out: Show Login -->
                            <a href="/Maqua/registreren/" class="flex items-center space-x-3 text-primary">
                                <i class="fas fa-user w-6"></i>
                                <span>Account</span>
                            </a>
                        <?php endif; ?>
                        <a href="#tickets" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#a00d26] flex items-center justify-center space-x-2 hover-lift">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Tickets</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="min-h-screen flex items-center justify-center pt-20">
        <div class="container mx-auto px-4">
            <div class="max-w-lg mx-auto bg-[rgba(255,255,255,0.05)] rounded-xl p-8 backdrop-blur-md shadow-xl">
                <h2 class="text-3xl font-bold text-center mb-6">Aanmelden bij <span class="text-primary">MAQUA</span></h2>
                <p class="text-gray-300 text-center mb-8">Log in om toegang te krijgen tot je account.</p>

                <!-- Login Form -->
                <form id="loginForm" method="POST" action="" class="space-y-6">
                    <div class="form-group">
                        <label for="email" class="block text-sm font-medium mb-2">E-mailadres</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="jouw@email.com">
                            <i class="fas fa-envelope absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="block text-sm font-medium mb-2">Wachtwoord</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="••••••••">
                            <button type="button" class="toggle-password absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="knop_Aanmelden" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-[#b50322] transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Aanmelden</span>
                    </button>
                </form>

                <!-- Error Message -->
                <?php if (!empty($melding)): ?>
                    <p class="text-center text-red-500 mt-6"><?php echo $melding; ?></p>
                <?php endif; ?>

                <!-- Social Login Buttons -->
                <div class="mt-6 space-y-4">
                    <!-- Google Sign-In Button Container -->
                    <div id="google-signin-btn-container" class="flex justify-center"></div>
                </div>

                <!-- Register Link -->
                <p class="text-center text-gray-300 mt-6">Nog geen account? <a href="/registreren/" class="text-primary hover:underline">Registreer hier</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0D0D1A] pt-20 pb-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-16">
                <!-- Logo & About -->
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
                <!-- Quick Links -->
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
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">Contact<span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span></h3>
                    <ul class="space-y-4">
                        <li class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i><span class="text-gray-400">Museumplein 123, 3000 Antwerpia</span></li>
                        <li class="flex items-start"><i class="fas fa-phone-alt mt-1 mr-3 text-primary"></i><span class="text-gray-400">+32 123 456 789</span></li>
                        <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-3 text-primary"></i><span class="text-gray-400">info@maqua.be</span></li>
                    </ul>
                </div>
                <!-- Newsletter -->
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
            <!-- Bottom Footer -->
            <div class="pt-8 mt-8 border-t border-gray-800 text-center">
                <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript for Password Toggle, Mobile Menu, and Google Sign-In -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Redirect to HTTPS if on HTTP
    if (window.location.protocol === 'http:') {
        console.log('Redirecting to HTTPS...');
        window.location.href = 'https://' + window.location.host + window.location.pathname;
        return;
    }

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
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Google Sign-In Initialization
    function initializeGoogleSignIn() {
        if (typeof google === 'undefined' || !google.accounts || !google.accounts.id) {
            console.error('Google Sign-In library not loaded or incomplete');
            return;
        }

        try {
            // Explicitly set redirect_uri to HTTPS
            const redirectUri = 'https://elbouga.kunstkaai.online/aanmelden/';
            console.log('Setting redirect_uri to:', redirectUri);

            google.accounts.id.initialize({
                client_id: '1078845914507-g26tju80p4jsjefebnkljdpk9fiv64bd.apps.googleusercontent.com',
                callback: handleCredentialResponse,
                auto_select: false,
                ux_mode: 'popup',
                auto_prompt: false,
                redirect_uri: redirectUri // Use HTTPS URL
            });

            // Render Google Sign-In button
            const googleButtonContainer = document.getElementById('google-signin-btn-container');
            if (googleButtonContainer) {
                google.accounts.id.renderButton(
                    googleButtonContainer,
                    {
                        theme: 'outline',
                        size: 'large',
                        type: 'standard',
                        shape: 'rectangular',
                        text: 'signin_with',
                        logo_alignment: 'left',
                        width: '300'
                    }
                );
                console.log('Google Sign-In button rendered successfully');
            } else {
                console.error('Google Sign-In button container not found');
            }
        } catch (error) {
            console.error('Error initializing Google Sign-In:', error);
        }
    }

    // Handle Google Sign-In response
    function handleCredentialResponse(response) {
        console.log('Full response from Google:', response);
        if (!response.credential) {
            console.error('No ID token received:', response);
            alert('Login mislukt: Geen geldig ID-token ontvangen.');
            return;
        }
        console.log('Credential received:', response.credential);
        fetch('/aanmelden/handle-google-login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ credential: response.credential })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Login mislukt: ' + (data.error || 'Onbekende fout'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Er is een fout opgetreden tijdens het inloggen: ' + error.message);
        });
    }

    // Ensure Google script is loaded before initializing
    function checkGoogleLibrary() {
        if (typeof google !== 'undefined' && google.accounts && google.accounts.id) {
            console.log('Google Sign-In library loaded');
            initializeGoogleSignIn();
        } else {
            console.log('Waiting for Google Sign-In library...');
            setTimeout(checkGoogleLibrary, 100); // Retry every 100ms
        }
    }

    // Start checking for Google library
    checkGoogleLibrary();
}); // Close DOMContentLoaded event listener
</script>
</body>
</html>