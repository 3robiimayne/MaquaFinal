<?php
session_start();
include 'includes/dbconn.inc.php';

$aankoopID = filter_input(INPUT_GET, 'aankoopID', FILTER_VALIDATE_INT);
if (!$aankoopID || !isset($_SESSION['klantID'])) {
    header('Location: tickets.php');
    exit();
}

$stmt = mysqli_prepare($dbconn, "SELECT a.aankoopdatum, a.hoeveelheid, a.totaalprijs, t.ticket FROM tblAankopen a JOIN tblTickets t ON a.artikelID = t.ticketID WHERE a.aankoopID = ? AND a.klantID = ?");
mysqli_stmt_bind_param($stmt, "ii", $aankoopID, $_SESSION['klantID']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $aankoopdatum, $hoeveelheid, $totaalprijs, $ticket);
$reservation = mysqli_stmt_fetch($stmt) ? [
    'aankoopdatum' => $aankoopdatum,
    'hoeveelheid' => $hoeveelheid,
    'totaalprijs' => $totaalprijs,
    'ticket' => $ticket
] : null;
mysqli_stmt_close($stmt);
mysqli_close($dbconn);

if (!$reservation) {
    header('Location: tickets.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Reservering Bevestigd</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #D90429;
            --primary-dark: #b50322;
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
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-[#1A1A2E] to-[#0F0F1A] text-white">
    <div class="container mx-auto px-4 py-16 text-center">
        <h1 class="text-4xl font-bold mb-6">Reservering Bevestigd!</h1>
        <p class="text-lg mb-8">Je reservering voor <strong><?php echo htmlspecialchars($reservation['ticket']); ?></strong> is succesvol.</p>
        <div class="bg-[rgba(255,255,255,0.05)] p-6 rounded-lg max-w-md mx-auto">
            <p><strong>Datum/Tijd:</strong> <?php echo date('d-m-Y H:i', strtotime($reservation['aankoopdatum'])); ?></p>
            <p><strong>Aantal Personen:</strong> <?php echo $reservation['hoeveelheid']; ?></p>
            <p><strong>Totaalprijs:</strong> €<?php echo number_format($reservation['totaalprijs'], 2); ?></p>
        </div>
        <a href="/Maqua/tickets.php" class="btn-primary mt-8 inline-flex items-center">
            <span>Terug naar Tickets</span>
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</body>
</html>