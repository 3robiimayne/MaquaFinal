<?php
session_start();

// Check if user is logged in
$klantID = $_SESSION["klantID"] ?? null;
if (!$klantID) {
    header('Location: login.php?redirect=tickets.php');
    exit();
}

// Connect to the database
include("includes/dbconn.inc.php");

// Maximum capacity per time slot
$maxCapacityPerSlot = 100;

// Available time slots
$timeSlots = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_reservation'])) {
    $ticketID = filter_input(INPUT_POST, 'ticketID', FILTER_VALIDATE_INT);
    $hoeveelheid = filter_input(INPUT_POST, 'hoeveelheid', FILTER_VALIDATE_INT);
    $datum = filter_input(INPUT_POST, 'datum', FILTER_SANITIZE_STRING);
    $tijd = filter_input(INPUT_POST, 'tijd', FILTER_SANITIZE_STRING);

    // Validate inputs
    if (!$ticketID || $hoeveelheid < 1 || !in_array($tijd, $timeSlots) || !DateTime::createFromFormat('Y-m-d', $datum)) {
        $error = "Ongeldige invoer. Controleer je selecties.";
    } else {
        $aankoopdatum = "$datum $tijd:00";
        
        // Check if date is in the future
        $selectedDateTime = new DateTime($aankoopdatum);
        $now = new DateTime();
        if ($selectedDateTime < $now) {
            $error = "Je kunt geen reserveringen maken voor een datum/tijd in het verleden.";
        } else {
            // Check availability
            $stmt = mysqli_prepare($dbconn, "SELECT SUM(hoeveelheid) as total FROM tblAankopen WHERE aankoopdatum = ?");
            mysqli_stmt_bind_param($stmt, "s", $aankoopdatum);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $totalReserved);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            $totalReserved = $totalReserved ?? 0;
            $available = $maxCapacityPerSlot - $totalReserved;

            if ($hoeveelheid > $available) {
                $error = "Niet genoeg tickets beschikbaar voor deze tijdslot. Nog $available beschikbaar.";
            } else {
                // Get ticket price
                $stmt = mysqli_prepare($dbconn, "SELECT prijs FROM tblTickets WHERE ticketID = ?");
                mysqli_stmt_bind_param($stmt, "i", $ticketID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $prijs);
                if (mysqli_stmt_fetch($stmt)) {
                    mysqli_stmt_close($stmt);

                    // Calculate total price
                    $totaalprijs = $prijs * $hoeveelheid;

                    // Insert  Insert reservation
                    $stmt = mysqli_prepare($dbconn, "INSERT INTO tblAankopen (klantID, artikelID, aankoopdatum, hoeveelheid, totaalprijs) VALUES (?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "iisid", $klantID, $ticketID, $aankoopdatum, $hoeveelheid, $totaalprijs);
                    if (mysqli_stmt_execute($stmt)) {
                        $aankoopID = mysqli_insert_id($dbconn);
                        mysqli_stmt_close($stmt);
                        mysqli_close($dbconn);
                        header("Location: ticket_confirm.php?aankoopID=$aankoopID");
                        exit();
                    } else {
                        $error = "Fout bij het maken van de reservering: " . mysqli_error($dbconn);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Ongeldig ticket geselecteerd.";
                    mysqli_stmt_close($stmt);
                }
            }
        }
    }
}

// Fetch tickets
$qrySelectTickets = "SELECT ticketID, ticket, prijs, afbeelding FROM tblTickets ORDER BY ticketID";
if ($stmt = mysqli_prepare($dbconn, $qrySelectTickets)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ticketID, $ticket, $prijs, $afbeelding);
    $tickets = [];
    while (mysqli_stmt_fetch($stmt)) {
        $tickets[] = [
            'ticketID' => $ticketID,
            'ticket' => $ticket,
            'prijs' => $prijs,
            'afbeelding' => $afbeelding
        ];
    }
    mysqli_stmt_close($stmt);
}

// Fetch availability for a selected date (default to today)
$selectedDate = $_POST['datum'] ?? date('Y-m-d');
$availability = [];
foreach ($timeSlots as $slot) {
    $aankoopdatum = "$selectedDate $slot:00";
    $stmt = mysqli_prepare($dbconn, "SELECT SUM(hoeveelheid) as total FROM tblAankopen WHERE aankoopdatum = ?");
    mysqli_stmt_bind_param($stmt, "s", $aankoopdatum);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $totalReserved);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    $availability[$slot] = $maxCapacityPerSlot - ($totalReserved ?? 0);
}

mysqli_close($dbconn);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Reserveer Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #D90429;
            --primary-dark: #b50322;
        }
        [data-theme="orange"] {
            --primary-color: #D89746;
            --primary-dark: #b37432;
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .hover\:bg-primary:hover { background-color: var(--primary-color); }
        .hover\:text-primary:hover { color: var(--primary-color); }
        .bg-primary-dark { background-color: var(--primary-dark); }
        .hover\:bg-primary-dark:hover { background-color: var(--primary-dark); }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
        }
        .btn-secondary {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-secondary:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        .btn-secondary:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
        }
        body {
            font-size: 16px;
            line-height: 1.6;
            font-family: 'Inter', sans-serif;
        }
        section {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }
        @media (max-width: 640px) {
            section {
                padding-top: 2.5rem;
                padding-bottom: 2.5rem;
            }
        }
        h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
        }
        h2 {
            font-size: clamp(2rem, 4vw, 2.5rem);
        }
        h2.text-primary {
            font-size: clamp(2rem, 4vw, 3rem);
        }
        nav a {
            transition: color 0.3s ease, transform 0.2s ease;
        }
        nav a:hover {
            transform: translateY(-2px);
        }
        .hover-lift {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        }
        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 8px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #1A1A2E; }
        ::-webkit-scrollbar-thumb { background: var(--primary-color); border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }
        #mobile-menu {
            width: 100%;
            max-width: 320px;
            background: linear-gradient(180deg, #1A1A2E 0%, #0F0F1A 100%);
            box-shadow: -4px 0 10px rgba(0, 0, 0, 0.3);
        }
        #mobile-menu .flex.flex-col {
            padding: 1rem;
            gap: 0.5rem;
        }
        #mobile-menu a, #mobile-menu button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: #fff;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #mobile-menu a:hover, #mobile-menu button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--primary-color);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'var(--primary-color)',
                        secondary: '#118AB2',
                        accent: '#FFD700',
                        dark: '#1A1A2E',
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A]">
    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4 relative">
            <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                    <a href="/Maqua/index.php" class="flex items-center">
                        <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                        <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                    </a>
                </div>
                <div class="hidden lg:flex absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 items-center space-x-8">
                    <a href="/Maqua/index.php#nieuws" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Nieuws</a>
                    <a href="/Maqua/index.php#over" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Over</a>
                    <a href="/Maqua/index.php#locatie" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Locatie</a>
                </div>
                <div class="hidden lg:flex items-center space-x-6 flex-shrink-0">
                    <a href="/Maqua/index.php#shop" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shop</span>
                    </a>
                    <?php if (isset($_SESSION["klantID"])): ?>
                        <a href="/Maqua/index.php?logout=true" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Uitloggen</span>
                        </a>
                    <?php else: ?>
                        <a href="/Maqua/aanmelden/" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                            <i class="fas fa-user"></i>
                            <span>Account</span>
                        </a>
                    <?php endif; ?>
                    <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                        <a href="/Maqua/klanten.php" class="flex items-center space-x-3 text-white hover:text-primary">
                            <i class="fas fa-user-shield w-6"></i>
                            <span>Admin</span>
                        </a>
                    <?php endif; ?>
                    <button id="theme-toggle-desktop" class="text-white hover:text-primary transition-colors p-2" title="Toggle Theme">
                        <i class="fas fa-adjust"></i>
                    </button>
                </div>
                <button id="mobile-menu-button" class="lg:hidden flex items-center text-white p-3" aria-label="Menu openen">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>
        <div id="mobile-menu" class="fixed top-0 right-0 w-full max-w-sm min-h-screen bg-dark z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl overflow-y-auto lg:hidden">
            <div class="flex justify-end p-4">
                <button id="mobile-menu-close" class="text-white text-xl hover:text-primary transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex flex-col p-4">
                <a href="/Maqua/index.php#nieuws" class="text-white hover:text-primary">Nieuws</a>
                <a href="/Maqua/index.php#over" class="text-white hover:text-primary">Over</a>
                <a href="/Maqua/index.php#locatie" class="text-white hover:text-primary">Locatie</a>
                <a href="/Maqua/index.php#shop" class="flex items-center gap-2 text-white hover:text-primary">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Shop</span>
                </a>
                <?php if (isset($_SESSION["klantID"])): ?>
                    <a href="/Maqua/index.php?logout=true" class="flex items-center gap-2 text-white hover:text-primary">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Uitloggen</span>
                    </a>
                <?php else: ?>
                    <a href="/Maqua/aanmelden/" class="flex items-center gap-2 text-white hover:text-primary">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                    </a>
                <?php endif; ?>
                <a href="/Maqua/tickets.php" class="bg-primary text-white hover:bg-primary-dark flex items-center gap-2">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Tickets</span>
                </a>
                <?php if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1): ?>
                    <a href="/Maqua/klanten.php" class="flex items-center gap-2 text-white hover:text-primary">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin</span>
                    </a>
                <?php endif; ?>
                <button id="theme-toggle-mobile" class="flex items-center gap-2 text-white hover:text-primary">
                    <i class="fas fa-adjust"></i>
                    <span>Toggle Theme</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen relative flex items-center justify-center overflow-hidden pt-20">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('kmska.webp'); filter: brightness(0.3);"></div>
        <div class="container mx-auto px-4 pt-16 z-10 flex flex-col items-center">
            <div class="max-w-4xl mx-auto text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4">Beleef <span class="text-primary">MAQUA</span></h1>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-primary mb-12">Reserveer je tickets</h2>
                <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto mb-10">
                    Reserveer je tickets voor een exclusieve reis door de wonderen van de onderwaterwereld.
                </p>
                <div class="w-40 h-1 bg-primary mx-auto rounded-full"></div>
            </div>
        </div>
    </section>

    <!-- Date and Time Picker (Large Screens) -->
    <section class="hidden lg:block bg-[rgba(255,255,255,0.05)] py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto bg-[rgba(255,255,255,0.05)] rounded-2xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold mb-6 text-center">Kies je Bezoek</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Datum</label>
                        <input type="text" id="date-picker" class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300" value="<?php echo htmlspecialchars($selectedDate); ?>">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Tijdslot</label>
                        <select id="time-slot" class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300">
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?php echo $slot; ?>" <?php echo $availability[$slot] <= 0 ? 'disabled' : ''; ?>>
                                    <?php echo $slot; ?> (<?php echo $availability[$slot]; ?> beschikbaar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button id="update-availability" class="mt-6 w-full bg-primary text-white py-3 rounded-lg hover:bg-primary-dark transition-colors duration-300 hover-lift">
                    Beschikbaarheid Bijwerken
                </button>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            <?php if (isset($success)): ?>
                <div class="mb-10 p-6 bg-green-500 bg-opacity-10 rounded-lg text-green-400 text-center fade-in flex items-center justify-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="mb-10 p-6 bg-red-500 bg-opacity-10 rounded-lg text-red-400 text-center fade-in flex items-center justify-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Ticket Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto mb-16">
                <?php foreach ($tickets as $index => $ticket): ?>
                    <div class="bg-[rgba(255,255,255,0.05)] rounded-xl p-6 flex flex-col justify-between h-full transition-transform hover:transform hover:scale-105 hover:shadow-xl <?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'bg-gradient-to-br from-primary to-primary-dark transform md:scale-110 shadow-2xl relative z-10' : ''; ?>">
                        <div>
                            <?php if ($ticket['ticket'] === 'Diepte-Explorer Ticket'): ?>
                                <div class="absolute top-0 right-6 transform -translate-y-1/2">
                                    <div class="bg-accent text-dark px-4 py-1 rounded-full font-bold text-sm">POPULAIR</div>
                                </div>
                            <?php endif; ?>
                            <div class="text-center mb-4">
                                <h3 class="text-xl md:text-2xl font-bold mb-2"><?php echo htmlspecialchars($ticket['ticket']); ?></h3>
                                <div class="<?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-primary'; ?> text-3xl md:text-4xl font-bold mb-2">€<?php echo number_format($ticket['prijs'], 2); ?></div>
                                <p class="<?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-gray-100' : 'text-gray-300'; ?> text-base">
                                    <?php
                                    if ($ticket['ticket'] === 'Standaard Ticket') {
                                        echo 'Toegang tot de kerncollectie van MAQUA';
                                    } elseif ($ticket['ticket'] === 'Diepte-Explorer Ticket') {
                                        echo 'De complete MAQUA-ervaring';
                                    } else {
                                        echo 'Voor 2 volwassenen en 2 kinderen';
                                    }
                                    ?>
                                </p>
                            </div>
                            <ul class="space-y-2 mb-6 text-base flex-grow">
                                <li class="flex items-center">
                                    <i class="fas fa-check <?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-primary'; ?> mr-2"></i>
                                    <span>Permanente tentoonstellingen</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check <?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-primary'; ?> mr-2"></i>
                                    <span>Holografische gidsen</span>
                                </li>
                                <li class="flex items-center <?php echo $ticket['ticket'] === 'Standaard Ticket' ? 'text-gray-500' : ($ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-white'); ?>">
                                    <i class="fas <?php echo $ticket['ticket'] === 'Standaard Ticket' ? 'fa-times' : 'fa-check'; ?> mr-2 <?php echo $ticket['ticket'] === 'Standaard Ticket' ? '' : ($ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-primary'); ?>"></i>
                                    <span><?php echo $ticket['ticket'] === 'Familieticket' ? 'Onderwaterzwemtocht' : 'Onderwatertoegang'; ?></span>
                                </li>
                                <li class="flex items-center <?php echo $ticket['ticket'] === 'Standaard Ticket' ? 'text-gray-500' : ($ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-white'); ?>">
                                    <i class="fas <?php echo $ticket['ticket'] === 'Standaard Ticket' ? 'fa-times' : 'fa-check'; ?> mr-2 <?php echo $ticket['ticket'] === 'Standaard Ticket' ? '' : ($ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'text-white' : 'text-primary'); ?>"></i>
                                    <span><?php echo $ticket['ticket'] === 'Familieticket' ? 'Kinderactiviteiten' : 'Tijdelijke exposities'; ?></span>
                                </li>
                            </ul>
                        </div>
                        <button onclick="openReservationForm(<?php echo $ticket['ticketID']; ?>, '<?php echo htmlspecialchars($ticket['ticket']); ?>')" class="block text-center <?php echo $ticket['ticket'] === 'Diepte-Explorer Ticket' ? 'bg-white text-primary hover:bg-gray-100' : 'bg-[rgba(255,255,255,0.1)] text-white hover:bg-primary'; ?> py-3 rounded-lg font-semibold transition-colors text-base mt-auto">
                            Selecteer
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Reservation Form -->
            <div id="reservation-form" class="hidden max-w-3xl mx-auto bg-[rgba(255,255,255,0.05)] rounded-2xl p-8 shadow-xl">
                <h3 class="text-2xl sm:text-3xl font-bold mb-6 text-center">Maak je Reservering</h3>
                <form method="POST" id="reservation-form-element" class="space-y-6">
                    <input type="hidden" name="ticketID" id="ticketID">
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Geselecteerd Ticket</label>
                        <input type="text" id="ticketName" readonly class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300">
                    </div>
                    <div class="lg:hidden">
                        <label class="block text-gray-300 mb-2 font-medium">Datum</label>
                        <input type="text" name="datum" id="mobile-date-picker" required class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300" value="<?php echo htmlspecialchars($selectedDate); ?>">
                    </div>
                    <div class="lg:hidden">
                        <label class="block text-gray-300 mb-2 font-medium">Tijdslot</label>
                        <select name="tijd" required class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300">
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?php echo $slot; ?>" <?php echo $availability[$slot] <= 0 ? 'disabled' : ''; ?>>
                                    <?php echo $slot; ?> (<?php echo $availability[$slot]; ?> beschikbaar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 font-medium">Aantal Personen</label>
                        <input type="number" name="hoeveelheid" min="1" max="<?php echo $availability[$tijd] ?? $maxCapacityPerSlot; ?>" required class="w-full py-3 px-4 rounded-lg bg-[rgba(255,255,255,0.1)] text-white border border-transparent focus:border-primary focus:outline-none transition-all duration-300">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeReservationForm()" class="bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors duration-300">Annuleren</button>
                        <button type="submit" name="create_reservation" id="submit-btn" class="bg-primary text-white py-3 px-6 rounded-lg hover:bg-primary-dark transition-colors duration-300 hover-lift flex items-center">
                            <i class="fas fa-ticket-alt mr-2"></i> Reservering Maken
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0D0D1A] pt-김n pb-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-16">
                <div>
                    <a href="/Maqua/index.php" class="flex items-center mb-6">
                        <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                        <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                    </a>
                    <p class="text-gray-400 mb-6">Het MAQUA Museum brengt de rijke geschiedenis van Antwerpia tot leven met meeslepende tentoonstellingen en interactieve ervaringen.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">
                        Snelle Links
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="/Maqua/index.php#nieuws" class="text-gray-400 hover:text-primary transition-colors">Nieuws</a></li>
                        <li><a href="/Maqua/index.php#over" class="text-gray-400 hover:text-primary transition-colors">Over Ons</a></li>
                        <li><a href="/Maqua/index.php#collection" class="text-gray-400 hover:text-primary transition-colors">Collectie</a></li>
                        <li><a href="/Maqua/index.php#events" class="text-gray-400 hover:text-primary transition-colors">Evenementen</a></li>
                        <li><a href="/Maqua/tickets.php" class="text-gray-400 hover:text-primary transition-colors">Tickets</a></li>
                        <li><a href="/Maqua/index.php#vacatures" class="text-gray-400 hover:text-primary transition-colors">Werken bij MAQUA</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">
                        Contact
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-400">Hanzestedenplaats 1, 2000 Antwerpia</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone-alt mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-400">+32 123 456 789</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-400">info@maqua-museum.be</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-400">Di-Zo: 10:00 - 18:00<br>Ma: Gesloten</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">
                        Blijf op de hoogte
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
                    </h3>
                    <p class="text-gray-400 mb-4">Meld je aan voor onze nieuwsbrief en blijf op de hoogte van nieuwe tentoonstellingen en evenementen.</p>
                    <form class="mb-4">
                        <div class="flex">
                            <input type="email" placeholder="Je e-mailadres" class="custom-input py-3 px-4 rounded-l-lg w-full focus:outline-none">
                            <button type="submit" class="bg-primary text-white py-3 px-4 rounded-r-lg hover:bg-primary-dark transition-colors">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <p class="text-gray-500 text-sm">Door je aan te melden ga je akkoord met onze privacyvoorwaarden.</p>
                </div>
            </div>
            <div class="pt-8 mt-8 border-t-2 border-gray-800 text-center">
                <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
                <div class="flex justify-center mt-4 space-x-6">
                    <a href="/Maqua/index.php#privacy" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Privacybeleid</a>
                    <a href="/Maqua/index.php#terms" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Gebruiksvoorwaarden</a>
                    <a href="/Maqua/index.php#cookies" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Cookiebeleid</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileLinks = document.querySelectorAll('#mobile-menu a');

        function toggleMobileMenu() {
            mobileMenu.classList.toggle('translate-x-full');
            mobileMenuOverlay.classList.toggle('opacity-0');
            mobileMenuOverlay.classList.toggle('pointer-events-none');
            document.body.classList.toggle('overflow-hidden');
        }

        mobileMenuButton.addEventListener('click', toggleMobileMenu);
        mobileMenuClose.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay.addEventListener('click', toggleMobileMenu);

        mobileLinks.forEach(link => {
            link.addEventListener('click', toggleMobileMenu);
        });

        // Theme Toggle
        const themeToggleDesktop = document.getElementById('theme-toggle-desktop');
        const themeToggleMobile = document.getElementById('theme-toggle-mobile');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme) {
            body.setAttribute('data-theme', savedTheme);
        }

        function toggleTheme() {
            body.getAttribute('data-theme') === 'orange' ?
                (body.removeAttribute('data-theme'), localStorage.setItem(' personally identifiable information', 'default')) :
                (body.setAttribute('data-theme', 'orange'), localStorage.setItem('theme', 'orange'));
        }

        themeToggleDesktop.addEventListener('click', toggleTheme);
        themeToggleMobile.addEventListener('click', toggleTheme);

        // Flatpickr for Date Picker
        flatpickr('#date-picker', {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            defaultDate: '<?php echo htmlspecialchars($selectedDate); ?>',
            onChange: function(selectedDates, dateStr) {
                updateAvailability(dateStr);
            },
            locale: {
                firstDayOfWeek: 1 // Start week on Monday
            }
        });

        flatpickr('#mobile-date-picker', {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            defaultDate: '<?php echo htmlspecialchars($selectedDate); ?>',
            onChange: function(selectedDates, dateStr) {
                updateAvailability(dateStr);
            },
            locale: {
                firstDayOfWeek: 1
            }
        });

        // Reservation Form
        function openReservationForm(ticketID, ticketName) {
            const form = document.getElementById('reservation-form');
            form.classList.remove('hidden');
            document.getElementById('ticketID').value = ticketID;
            document.getElementById('ticketName').value = ticketName;
            window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
        }

        function closeReservationForm() {
            const form = document.getElementById('reservation-form');
            form.classList.add('hidden');
        }

        // Update Availability
        function updateAvailability(date) {
            window.location.href = `tickets.php?datum=${date}`;
        }

        // Form Submission with Loading State
        document.getElementById('reservation-form-element').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner mr-2"></i> Verwerken...';
        });
    </script>
</body>
</html>