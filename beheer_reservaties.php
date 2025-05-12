<?php
session_start();

// Check if user is logged in
$klantID = $_SESSION["klantID"] ?? null;
if (!$klantID) {
    header('Location: /Maqua/index.php?login_required=true');
    exit();
}

// Connect to the database
include("includes/dbconn.inc.php");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_reservation'])) {
        $ticketID = $_POST['ticketID'];
        $datum = $_POST['datum'];
        $tijd = $_POST['tijd'];
        $hoeveelheid = $_POST['hoeveelheid'];

        // Get ticket price
        $stmt = mysqli_prepare($dbconn, "SELECT prijs FROM tblTickets WHERE ticketID = ?");
        mysqli_stmt_bind_param($stmt, "i", $ticketID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $prijs);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Calculate total price
        $totaalprijs = $prijs * $hoeveelheid;

        // Insert into tblAankopen
        $aankoopdatum = date('Y-m-d H:i:s', strtotime("$datum $tijd"));
        $stmt = mysqli_prepare($dbconn, "INSERT INTO tblAankopen (klantID, artikelID, aankoopdatum, hoeveelheid, totaalprijs) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iisid", $klantID, $ticketID, $aankoopdatum, $hoeveelheid, $totaalprijs);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Reservering succesvol aangemaakt.";
        } else {
            $error = "Fout bij het aanmaken van de reservering: " . mysqli_error($dbconn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['cancel_reservation'])) {
        $aankoopID = $_POST['aankoopID'];
        $stmt = mysqli_prepare($dbconn, "DELETE FROM tblAankopen WHERE aankoopID = ? AND klantID = ?");
        mysqli_stmt_bind_param($stmt, "ii", $aankoopID, $klantID);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Reservering succesvol geannuleerd.";
        } else {
            $error = "Fout bij het annuleren van de reservering: " . mysqli_error($dbconn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all tickets for dropdown
$qrySelectTickets = "SELECT ticketID, ticket, prijs FROM tblTickets";
if ($stmtSelectTickets = mysqli_prepare($dbconn, $qrySelectTickets)) {
    mysqli_stmt_execute($stmtSelectTickets);
    mysqli_stmt_bind_result($stmtSelectTickets, $ticketID, $ticket, $prijs);
    $tickets = [];
    while (mysqli_stmt_fetch($stmtSelectTickets)) {
        $tickets[] = [
            'ticketID' => $ticketID,
            'ticket' => $ticket,
            'prijs' => $prijs
        ];
    }
    mysqli_stmt_close($stmtSelectTickets);
}

// Fetch user's reservations
$qrySelectReservations = "SELECT a.aankoopID, a.klantID, a.artikelID, a.aankoopdatum, a.hoeveelheid, a.totaalprijs, t.ticket 
                         FROM tblAankopen a 
                         JOIN tblTickets t ON a.artikelID = t.ticketID 
                         WHERE a.klantID = ? 
                         ORDER BY a.aankoopdatum DESC";
if ($stmtSelectReservations = mysqli_prepare($dbconn, $qrySelectReservations)) {
    mysqli_stmt_bind_param($stmtSelectReservations, "i", $klantID);
    mysqli_stmt_execute($stmtSelectReservations);
    mysqli_stmt_bind_result($stmtSelectReservations, $aankoopID, $klantID, $artikelID, $aankoopdatum, $hoeveelheid, $totaalprijs, $ticket);
    $reservations = [];
    while (mysqli_stmt_fetch($stmtSelectReservations)) {
        $reservations[] = [
            'aankoopID' => $aankoopID,
            'klantID' => $klantID,
            'artikelID' => $artikelID,
            'aankoopdatum' => $aankoopdatum,
            'hoeveelheid' => $hoeveelheid,
            'totaalprijs' => $totaalprijs,
            'ticket' => $ticket
        ];
    }
    mysqli_stmt_close($stmtSelectReservations);
}

mysqli_close($dbconn);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Reserveringen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#D90429',
                        secondary: '#118AB2',
                        dark: '#1A1A2E',
                    }
                }
            }
        };
    </script>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .action-btn { min-width: 2.5rem; min-height: 2.5rem; display: inline-flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A] text-white font-['Inter']">
    <nav class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="/Maqua/" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                </a>
                <a href="/Maqua/" class="text-white hover:text-primary">Terug naar Home</a>
            </div>
        </div>
    </nav>

    <section class="min-h-screen pt-20 px-4 pb-10">
        <div class="max-w-5xl mx-auto bg-[rgba(255,255,255,0.05)] rounded-xl p-6 md:p-8 backdrop-blur-md shadow-xl">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">Reserveringen <span class="text-primary">MAQUA</span></h2>

            <?php if (isset($success)): ?>
                <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <!-- Create Reservation Form -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Nieuwe Reservering</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-300">Ticket</label>
                        <select name="ticketID" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                            <?php foreach ($tickets as $ticket): ?>
                                <option value="<?php echo $ticket['ticketID']; ?>">
                                    <?php echo htmlspecialchars($ticket['ticket']) . ' (€' . number_format($ticket['prijs'], 2) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-300">Datum</label>
                        <input type="date" name="datum" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label class="block text-gray-300">Tijd</label>
                        <input type="time" name="tijd" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-gray-300">Aantal Personen</label>
                        <input type="number" name="hoeveelheid" min="1" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button type="submit" name="create_reservation" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors">Reservering Maken</button>
                </form>
            </div>

            <!-- Reservations Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-300 text-sm md:text-base min-w-[640px]">
                    <thead class="bg-[rgba(255,255,255,0.1)]">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Reservering ID</th>
                            <th class="py-3 px-4 font-semibold">Ticket</th>
                            <th class="py-3 px-4 font-semibold">Datum & Tijd</th>
                            <th class="py-3 px-4 font-semibold">Aantal</th>
                            <th class="py-3 px-4 font-semibold">Totaal (€)</th>
                            <th class="py-3 px-4 font-semibold">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr class="border-b border-gray-700 hover:bg-[rgba(255,255,255,0.05)] transition-all">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($reservation['aankoopID']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($reservation['ticket']); ?></td>
                                <td class="py-3 px-4"><?php echo date('d/m/Y H:i', strtotime($reservation['aankoopdatum'])); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($reservation['hoeveelheid']); ?></td>
                                <td class="py-3 px-4"><?php echo number_format($reservation['totaalprijs'], 2); ?></td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze reservering wilt annuleren?');">
                                        <input type="hidden" name="aankoopID" value="<?php echo $reservation['aankoopID']; ?>">
                                        <button type="submit" name="cancel_reservation" class="action-btn text-red-500 hover:text-primary transition-colors">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html>