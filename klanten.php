<?php 
session_start();

// Sessievariabelen ophalen
$klantID = $_SESSION["klantID"] ?? null;
$naam = $_SESSION["naam"] ?? '';
$voornaam = $_SESSION["voornaam"] ?? '';
$admin = $_SESSION["admin"] ?? 0;

// Testen of er een admin is aangemeld
if ($admin != 1) {
    header('Location: /Maqua/index.php');
    exit();
}

// Connectie maken met de database
include("includes/dbconn.inc.php");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_klant'])) {
        $deleteId = $_POST['klantID'];
        $stmt = mysqli_prepare($dbconn, "DELETE FROM tblKlanten WHERE klantID = ?");
        mysqli_stmt_bind_param($stmt, "i", $deleteId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['edit_klant'])) {
        $editId = $_POST['klantID'];
        $newNaam = $_POST['naam'];
        $newVoornaam = $_POST['voornaam'];
        $newGeboortedatum = $_POST['geboortedatum'];
        $stmt = mysqli_prepare($dbconn, "UPDATE tblKlanten SET naam = ?, voornaam = ?, geboortedatum = ? WHERE klantID = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $newNaam, $newVoornaam, $newGeboortedatum, $editId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['toggle_admin'])) {
        $toggleId = $_POST['klantID'];
        $newAdminKey = $_POST['adminkey'] == 1 ? 0 : 1; // Toggle 0 to 1 or 1 to 0
        $stmt = mysqli_prepare($dbconn, "UPDATE tblKlanten SET adminkey = ? WHERE klantID = ?");
        mysqli_stmt_bind_param($stmt, "ii", $newAdminKey, $toggleId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // Refresh page after action
    header("Location: klanten.php#klanten");
    exit();
}

// Query maken om alle klanten op te halen
$qrySelectKlanten = "SELECT klantID, naam, voornaam, geboortedatum, adminkey 
                     FROM tblKlanten 
                     ORDER BY naam, voornaam";
if ($stmtSelectKlanten = mysqli_prepare($dbconn, $qrySelectKlanten)) {
    mysqli_stmt_execute($stmtSelectKlanten);
    mysqli_stmt_bind_result($stmtSelectKlanten, $klantID, $naam, $voornaam, $geboortedatum, $adminkey);
    mysqli_stmt_store_result($stmtSelectKlanten);
    $aantalKlanten = mysqli_stmt_num_rows($stmtSelectKlanten);
    $klanten = [];
    while (mysqli_stmt_fetch($stmtSelectKlanten)) {
        $klanten[] = [
            'klantID' => $klantID,
            'naam' => $naam,
            'voornaam' => $voornaam,
            'geboortedatum' => $geboortedatum,
            'adminkey' => $adminkey
        ];
    }
    mysqli_stmt_close($stmtSelectKlanten);
} else {
    $errorKlanten = "Fout bij het ophalen van klanten: " . mysqli_error($dbconn);
}


mysqli_close($dbconn);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Admin Control Panel</title>
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
    <style>
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .fade-in { animation: fadeIn 0.3s ease-out forwards; }
        html, body { overflow-x: hidden; }
        .action-btn { min-width: 2.5rem; min-height: 2.5rem; display: inline-flex; align-items: center; justify-content: center; }
        .nav-btn { position: relative; }
        .nav-btn .tooltip { 
            visibility: hidden; 
            width: 120px; 
            background-color: rgba(26, 26, 46, 0.9); 
            color: #fff; 
            text-align: center; 
            border-radius: 4px; 
            padding: 5px 0; 
            position: absolute; 
            z-index: 1; 
            top: 100%; 
            left: 50%; 
            transform: translateX(-50%); 
            opacity: 0; 
            transition: opacity 0.2s; 
        }
        .nav-btn:hover .tooltip { visibility: visible; opacity: 1; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 100; }
        .modal-content { background: #fff; color: #000; margin: 15% auto; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A] text-white font-['Inter']">
    <!-- Navbar (unchanged) -->
    <nav class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="/Maqua/" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <div class="flex space-x-4">
                        <button class="nav-btn text-white hover:text-primary transition-colors active" data-section="klanten">
                            <i class="fas fa-users text-xl"></i>
                            <span class="tooltip">Klanten</span>
                        </button>
                        <button class="nav-btn text-white hover:text-primary transition-colors" data-section="tickets">
                            <i class="fas fa-ticket-alt text-xl"></i>
                            <span class="tooltip">Tickets</span>
                        </button>
                        <button class="nav-btn text-white hover:text-primary transition-colors" data-section="reserveringen">
                            <i class="fas fa-calendar-check text-xl"></i>
                            <span class="tooltip">Reserveringen</span>
                        </button>
                    </div>
                    <span class="text-gray-300">Welkom, <?php echo htmlspecialchars($voornaam . ' ' . $naam); ?></span>
                    <a href="index.php?logout=true" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Uitloggen</span>
                    </a>
                </div>
                <button id="sidebar-toggle" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar (unchanged) -->
    <aside id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-[rgba(26,26,46,0.9)] transform -translate-x-full transition-transform duration-300 z-40 flex flex-col py-6 space-y-6 md:hidden">
        <div class="flex items-center justify-between px-4 mb-6">
            <span class="text-xl font-bold text-white">Admin Menu</span>
            <button id="sidebar-close" class="text-white"><i class="fas fa-times text-xl"></i></button>
        </div>
        <button class="nav-btn flex items-center w-full px-4 py-3 text-white hover:bg-[rgba(255,255,255,0.1)] transition-colors active" data-section="klanten">
            <i class="fas fa-users text-xl mr-4"></i>
            <span>Klanten</span>
        </button>
        <button class="nav-btn flex items-center w-full px-4 py-3 text-white hover:bg-[rgba(255,255,255,0.1)] transition-colors" data-section="tickets">
            <i class="fas fa-ticket-alt text-xl mr-4"></i>
            <span>Tickets</span>
        </button>
        <button class="nav-btn flex items-center w-full px-4 py-3 text-white hover:bg-[rgba(255,255,255,0.1)] transition-colors" data-section="reserveringen">
            <i class="fas fa-calendar-check text-xl mr-4"></i>
            <span>Reserveringen</span>
        </button>
    </aside>

    <!-- Overlay for mobile sidebar (unchanged) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden md:hidden z-30"></div>

    <!-- Main Content -->
    <section class="min-h-screen pt-20 px-4 pb-10">
        <div class="max-w-5xl mx-auto bg-[rgba(255,255,255,0.05)] rounded-xl p-6 md:p-8 backdrop-blur-md shadow-xl">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">Admin Control Panel <span class="text-primary">MAQUA</span></h2>

            <!-- Klanten Section -->
            <div id="klanten" class="section-content fade-in">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                    <p class="text-gray-300 mb-4 md:mb-0">Totaal aantal klanten: <span class="text-primary font-bold"><?php echo $aantalKlanten; ?></span></p>
                    <div class="flex space-x-4">
                        <input type="text" id="klanten-search" class="bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Zoek klant...">
                    </div>
                </div>
                <?php if (isset($errorKlanten)): ?>
                    <p class="text-center text-red-500 mb-6"><?php echo $errorKlanten; ?></p>
                <?php elseif (!empty($klanten)): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-gray-300 text-sm md:text-base min-w-[640px]">
                            <thead class="bg-[rgba(255,255,255,0.1)]">
                                <tr>
                                    <th class="py-3 px-4 font-semibold">Klant ID</th>
                                    <th class="py-3 px-4 font-semibold">Naam</th>
                                    <th class="py-3 px-4 font-semibold">Voornaam</th>
                                    <th class="py-3 px-4 font-semibold">Geboortedatum</th>
                                    <th class="py-3 px-4 font-semibold">Admin</th>
                                    <th class="py-3 px-4 font-semibold">Acties</th>
                                </tr>
                            </thead>
<tbody id="klanten-table">
    <?php foreach ($klanten as $klant): ?>
        <tr class="border-b border-gray-700 hover:bg-[rgba(255,255,255,0.05)] transition-all">
            <td class="py-3 px-4"><?php echo htmlspecialchars($klant['klantID']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($klant['naam']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars($klant['voornaam']); ?></td>
            <td class="py-3 px-4"><?php echo htmlspecialchars(date('d/m/Y', strtotime($klant['geboortedatum']))); ?></td>
            <td class="py-3 px-4"><?php echo $klant['adminkey'] ? '<span class="text-primary">Ja</span>' : 'Nee'; ?></td>
            <td class="py-3 px-4 flex space-x-2">
                <!-- Info Button linking to detailsklant.php -->
                <a href="detailsklant.php?id=<?php echo $klant['klantID']; ?>" class="action-btn text-blue-400 hover:text-primary transition-colors" title="Details bekijken">
                    <i class="fas fa-info-circle text-lg"></i>
                </a>
                <!-- Delete Button -->
                <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze klant wilt verwijderen?');">
                    <input type="hidden" name="klantID" value="<?php echo $klant['klantID']; ?>">
                    <button type="submit" name="delete_klant" class="action-btn text-red-500 hover:text-primary transition-colors">
                        <i class="fas fa-trash-alt text-lg"></i>
                    </button>
                </form>
                <!-- Toggle Admin Button -->
                <form method="POST">
                    <input type="hidden" name="klantID" value="<?php echo $klant['klantID']; ?>">
                    <input type="hidden" name="adminkey" value="<?php echo $klant['adminkey']; ?>">
                    <button type="submit" name="toggle_admin" class="action-btn text-<?php echo $klant['adminkey'] ? 'yellow-500' : 'green-500'; ?> hover:text-primary transition-colors">
                        <i class="fas fa-<?php echo $klant['adminkey'] ? 'user-times' : 'user-plus'; ?> text-lg"></i>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-400">Geen klanten gevonden in de database.</p>
                <?php endif; ?>
            </div>

<!-- Tickets Section -->
<div id="tickets" class="section-content hidden">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <p class="text-gray-300 mb-4 md:mb-0">Beheer hier de beschikbare tickets.</p>
        <div class="flex space-x-4">
            <input type="text" id="tickets-search" class="bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Zoek ticket...">
            <a href="beheer_tickets.php" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span class="hidden md:inline">Beheer Tickets</span>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-gray-300 text-sm md:text-base min-w-[640px]">
            <thead class="bg-[rgba(255,255,255,0.1)]">
                <tr>
                    <th class="py-3 px-4 font-semibold">Ticket ID</th>
                    <th class="py-3 px-4 font-semibold">Naam</th>
                    <th class="py-3 px-4 font-semibold">Prijs (€)</th>
                    <th class="py-3 px-4 font-semibold">Afbeelding</th>
                </tr>
            </thead>
            <tbody id="tickets-table">
                <?php
                // Fetch tickets from database
                include("includes/dbconn.inc.php");
                $qrySelectTickets = "SELECT ticketID, ticket, prijs, afbeelding FROM tblTickets ORDER BY ticketID";
                if ($stmtSelectTickets = mysqli_prepare($dbconn, $qrySelectTickets)) {
                    mysqli_stmt_execute($stmtSelectTickets);
                    mysqli_stmt_bind_result($stmtSelectTickets, $ticketID, $ticket, $prijs, $afbeelding);
                    while (mysqli_stmt_fetch($stmtSelectTickets)) {
                        echo '<tr class="border-b border-gray-700 hover:bg-[rgba(255,255,255,0.05)] transition-all">';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($ticketID) . '</td>';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($ticket) . '</td>';
                        echo '<td class="py-3 px-4">' . number_format($prijs, 2) . '</td>';
                        echo '<td class="py-3 px-4">';
                        if ($afbeelding) {
                            echo '<img src="' . htmlspecialchars($afbeelding) . '" alt="Ticket" class="w-16 h-16 object-cover rounded">';
                        } else {
                            echo 'Geen afbeelding';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                    mysqli_stmt_close($stmtSelectTickets);
                }
                mysqli_close($dbconn);
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reserveringen Section -->
<div id="reserveringen" class="section-content hidden">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <p class="text-gray-300 mb-4 md:mb-0">Beheer hier de museumreserveringen.</p>
        <div class="flex space-x-4">
            <input type="text" id="reserveringen-search" class="bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Zoek reservering...">
            <a href="beheer_reservaties.php" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span class="hidden md:inline">Nieuwe Reservering</span>
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-gray-300 text-sm md:text-base min-w-[640px]">
            <thead class="bg-[rgba(255,255,255,0.1)]">
                <tr>
                    <th class="py-3 px-4 font-semibold">Reservering ID</th>
                    <th class="py-3 px-4 font-semibold">Klant</th>
                    <th class="py-3 px-4 font-semibold">Ticket</th>
                    <th class="py-3 px-4 font-semibold">Datum & Tijd</th>
                    <th class="py-3 px-4 font-semibold">Aantal</th>
                    <th class="py-3 px-4 font-semibold">Totaal (€)</th>
                </tr>
            </thead>
            <tbody id="reserveringen-table">
                <?php
                // Fetch reservations from database
                include("includes/dbconn.inc.php");
                $qrySelectReservations = "SELECT a.aankoopID, a.klantID, a.artikelID, a.aankoopdatum, a.hoeveelheid, a.totaalprijs, t.ticket, k.naam, k.voornaam 
                                         FROM tblAankopen a 
                                         JOIN tblTickets t ON a.artikelID = t.ticketID 
                                         JOIN tblKlanten k ON a.klantID = k.klantID 
                                         ORDER BY a.aankoopdatum DESC";
                if ($stmtSelectReservations = mysqli_prepare($dbconn, $qrySelectReservations)) {
                    mysqli_stmt_execute($stmtSelectReservations);
                    mysqli_stmt_bind_result($stmtSelectReservations, $aankoopID, $klantID, $artikelID, $aankoopdatum, $hoeveelheid, $totaalprijs, $ticket, $naam, $voornaam);
                    while (mysqli_stmt_fetch($stmtSelectReservations)) {
                        echo '<tr class="border-b border-gray-700 hover:bg-[rgba(255,255,255,0.05)] transition-all">';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($aankoopID) . '</td>';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($voornaam . ' ' . $naam) . '</td>';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($ticket) . '</td>';
                        echo '<td class="py-3 px-4">' . date('d/m/Y H:i', strtotime($aankoopdatum)) . '</td>';
                        echo '<td class="py-3 px-4">' . htmlspecialchars($hoeveelheid) . '</td>';
                        echo '<td class="py-3 px-4">' . number_format($totaalprijs, 2) . '</td>';
                        echo '</tr>';
                    }
                    mysqli_stmt_close($stmtSelectReservations);
                }
                mysqli_close($dbconn);
                ?>
            </tbody>
        </table>
    </div>
</div>
        </div>
    </section>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2 class="text-xl font-bold mb-4">Klant Wijzigen</h2>
            <form method="POST">
                <input type="hidden" name="klantID" id="editKlantID">
                <div class="mb-4">
                    <label class="block text-gray-700">Naam</label>
                    <input type="text" name="naam" id="editNaam" class="w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Voornaam</label>
                    <input type="text" name="voornaam" id="editVoornaam" class="w-full p-2 border rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Geboortedatum</label>
                    <input type="date" name="geboortedatum" id="editGeboortedatum" class="w-full p-2 border rounded">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 text-black py-2 px-4 rounded">Annuleren</button>
                    <button type="submit" name="edit_klant" class="bg-primary text-white py-2 px-4 rounded hover:bg-[#b50322]">Opslaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer (unchanged) -->
    <footer class="bg-[#0D0D1A] pt-20 pb-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-16">
                <div>
                    <a href="/Maqua/" class="flex items-center mb-6">
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
    // Sidebar Toggle for Mobile
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    });

    sidebarClose.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    });

    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    });

    // Navigation
    const navButtons = document.querySelectorAll('.nav-btn');
    const sectionContents = document.querySelectorAll('.section-content');

    navButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            navButtons.forEach(btn => btn.classList.remove('active', 'text-primary'));
            button.classList.add('active', 'text-primary');

            sectionContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('fade-in');
            });
            const sectionId = button.getAttribute('data-section');
            const targetSection = document.getElementById(sectionId);
            targetSection.classList.remove('hidden');
            setTimeout(() => targetSection.classList.add('fade-in'), 10);

            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });
    });

    window.addEventListener('load', () => {
        const hash = window.location.hash.replace('#', '');
        const targetButton = hash ? document.querySelector(`.nav-btn[data-section="${hash}"]`) : navButtons[0];
        if (targetButton) targetButton.click();
    });

    // Search Functionality
    function filterTable(searchInputId, tableId) {
        const searchInput = document.getElementById(searchInputId);
        const table = document.getElementById(tableId);
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('input', () => {
            const filter = searchInput.value.toLowerCase();
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j] && cells[j].textContent.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
    }

    filterTable('klanten-search', 'klanten-table');
    filterTable('tickets-search', 'tickets-table');
    filterTable('reserveringen-search', 'reserveringen-table');
</script>
</body>
</html>