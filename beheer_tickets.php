<?php
session_start();

// Check if admin is logged in
$admin = $_SESSION["admin"] ?? 0;
if ($admin != 1) {
    header('Location: /Maqua/index.php');
    exit();
}

// Connect to the database
include("includes/dbconn.inc.php");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_ticket'])) {
        $ticketName = $_POST['ticket_name'];
        $prijs = $_POST['prijs'];
        $afbeelding = '';

        // Handle image upload
        if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/tickets/';
            $fileName = uniqid() . '_' . basename($_FILES['afbeelding']['name']);
            $uploadPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadPath)) {
                $afbeelding = $uploadPath;
            } else {
                $error = "Fout bij het uploaden van de afbeelding.";
            }
        }

        // Insert into database
        $stmt = mysqli_prepare($dbconn, "INSERT INTO tblTickets (ticket, prijs, afbeelding) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sds", $ticketName, $prijs, $afbeelding);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Ticket succesvol aangemaakt.";
        } else {
            $error = "Fout bij het aanmaken van het ticket: " . mysqli_error($dbconn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['delete_ticket'])) {
        $ticketID = $_POST['ticketID'];
        // Get image path to delete file
        $stmt = mysqli_prepare($dbconn, "SELECT afbeelding FROM tblTickets WHERE ticketID = ?");
        mysqli_stmt_bind_param($stmt, "i", $ticketID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $afbeelding);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Delete from database
        $stmt = mysqli_prepare($dbconn, "DELETE FROM tblTickets WHERE ticketID = ?");
        mysqli_stmt_bind_param($stmt, "i", $ticketID);
        if (mysqli_stmt_execute($stmt)) {
            // Delete image file if exists
            if ($afbeelding && file_exists($afbeelding)) {
                unlink($afbeelding);
            }
            $success = "Ticket succesvol verwijderd.";
        } else {
            $error = "Fout bij het verwijderen van het ticket: " . mysqli_error($dbconn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all tickets
$qrySelectTickets = "SELECT ticketID, ticket, prijs, afbeelding FROM tblTickets ORDER BY ticketID";
if ($stmtSelectTickets = mysqli_prepare($dbconn, $qrySelectTickets)) {
    mysqli_stmt_execute($stmtSelectTickets);
    mysqli_stmt_bind_result($stmtSelectTickets, $ticketID, $ticket, $prijs, $afbeelding);
    $tickets = [];
    while (mysqli_stmt_fetch($stmtSelectTickets)) {
        $tickets[] = [
            'ticketID' => $ticketID,
            'ticket' => $ticket,
            'prijs' => $prijs,
            'afbeelding' => $afbeelding
        ];
    }
    mysqli_stmt_close($stmtSelectTickets);
} else {
    $error = "Fout bij het ophalen van tickets: " . mysqli_error($dbconn);
}

mysqli_close($dbconn);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Beheer Tickets</title>
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
                <a href="klanten.php" class="text-white hover:text-primary">Terug naar Admin</a>
            </div>
        </div>
    </nav>

    <section class="min-h-screen pt-20 px-4 pb-10">
        <div class="max-w-5xl mx-auto bg-[rgba(255,255,255,0.05)] rounded-xl p-6 md:p-8 backdrop-blur-md shadow-xl">
            <h2 class="text-2xl md:text-3xl font-bold text-center mb-6">Beheer Tickets <span class="text-primary">MAQUA</span></h2>

            <?php if (isset($success)): ?>
                <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <!-- Create Ticket Form -->
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Nieuw Ticket Aanmaken</h3>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-300">Ticket Naam</label>
                        <input type="text" name="ticket_name" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-gray-300">Prijs (€)</label>
                        <input type="number" name="prijs" step="0.01" required class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-gray-300">Afbeelding</label>
                        <input type="file" name="afbeelding" accept="image/*" class="w-full bg-[rgba(255,255,255,0.1)] text-white border-none rounded-lg py-2 px-4">
                    </div>
                    <button type="submit" name="create_ticket" class="bg-primary text-white py-2 px-4 rounded-lg hover:bg-[#b50322] transition-colors">Ticket Aanmaken</button>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-gray-300 text-sm md:text-base min-w-[640px]">
                    <thead class="bg-[rgba(255,255,255,0.1)]">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Ticket ID</th>
                            <th class="py-3 px-4 font-semibold">Naam</th>
                            <th class="py-3 px-4 font-semibold">Prijs (€)</th>
                            <th class="py-3 px-4 font-semibold">Afbeelding</th>
                            <th class="py-3 px-4 font-semibold">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr class="border-b border-gray-700 hover:bg-[rgba(255,255,255,0.05)] transition-all">
                                <td class="py-3 px-4"><?php echo htmlspecialchars($ticket['ticketID']); ?></td>
                                <td class="py-3 px-4"><?php echo htmlspecialchars($ticket['ticket']); ?></td>
                                <td class="py-3 px-4"><?php echo number_format($ticket['prijs'], 2); ?></td>
                                <td class="py-3 px-4">
                                    <?php if ($ticket['afbeelding']): ?>
                                        <img src="<?php echo htmlspecialchars($ticket['afbeelding']); ?>" alt="Ticket" class="w-16 h-16 object-cover rounded">
                                    <?php else: ?>
                                        Geen afbeelding
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 flex space-x-2">
                                    <form method="POST" onsubmit="return confirm('Weet je zeker dat je dit ticket wilt verwijderen?');">
                                        <input type="hidden" name="ticketID" value="<?php echo $ticket['ticketID']; ?>">
                                        <button type="submit" name="delete_ticket" class="action-btn text-red-500 hover:text-primary transition-colors">
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