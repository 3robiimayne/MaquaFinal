<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>404 - Pagina Niet Gevonden | MAQUA</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@800&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="/style.css">
    <style>
        :root {
            --shadow: hsl(0, 0%, 0%);
            --header: hsl(0, 78%, 38%); /* Matches #D90429 */
            --lit-header: hsl(0, 78%, 60%);
            --speed: 2s;
        }
        * {
            box-sizing: border-box;
            transform-style: preserve-3d;
        }
        @property --swing-x {
            initial-value: 0;
            inherits: false;
            syntax: '<integer>';
        }
        @property --swing-y {
            initial-value: 0;
            inherits: false;
            syntax: '<integer>';
        }
        section h1 {
            animation: swing var(--speed) infinite alternate ease-in-out;
            font-size: clamp(4rem, 30vmin, 15rem); /* Slightly smaller, still bold */
            font-family: 'Open Sans', sans-serif !important;
            margin: 0;
            margin-bottom: 1.5rem; /* More space for balance */
            letter-spacing: 1rem;
            transform: translate3d(0, 0, 0vmin);
            --x: calc(50% + (var(--swing-x) * 0.5) * 1%);
            background: radial-gradient(var(--lit-header), var(--header) 45%) var(--x) 100% / 200% 200%;
            -webkit-background-clip: text;
            color: transparent;
            position: relative;
        }
        section h1:after {
            animation: swing var(--speed) infinite alternate ease-in-out;
            content: "404";
            position: absolute;
            top: 0;
            left: 0;
            color: var(--shadow);
            filter: blur(1.5vmin);
            transform: scale(1.05) translate3d(0, 12%, -10vmin) translate(calc((var(--swing-x, 0) * 0.05) * 1%), calc((var(--swing-y) * 0.05) * 1%));
        }
        .cloak__wrapper {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            overflow: hidden;
            z-index: -1;
        }
        .cloak__container {
            height: 300vmax;
            width: 300vmax;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .cloak {
            animation: swing var(--speed) infinite alternate-reverse ease-in-out;
            height: 100%;
            width: 100%;
            transform-origin: 50% 30%;
            transform: rotate(calc(var(--swing-x) * -0.25deg));
            background: radial-gradient(40% 40% at 50% 42%, transparent, black 35%);
        }
        @keyframes swing {
            0% {
                --swing-x: -100;
                --swing-y: -100;
            }
            50% {
                --swing-y: 0;
            }
            100% {
                --swing-y: -100;
                --swing-x: 100;
            }
        }
        /* Button Styling */
        .btn-404 {
            background: linear-gradient(135deg, #D90429, #b50322); /* Gradient for depth */
            padding: 1rem 2.5rem;
            font-size: 1.125rem; /* Slightly larger text */
            border-radius: 9999px; /* Fully rounded */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); /* Subtle shadow */
            transition: all 0.3s ease;
        }
        .btn-404:hover {
            background: linear-gradient(135deg, #b50322, #D90429);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            transform: translateY(-2px); /* Slight lift on hover */
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A] text-white font-['Inter']">
    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 w-full bg-[rgba(26,26,46,0.8)] backdrop-blur-md z-50 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <a href="/index.php" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                </a>
                <button id="mobile-menu-button" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <div id="mobile-menu" class="hidden fixed top-[68px] left-0 w-full bg-dark md:hidden z-40 transition-all duration-300">
                <div class="flex flex-col p-6 space-y-6">
                    <a href="/index.php#nieuws" class="text-white text-lg hover:text-primary">Nieuws</a>
                    <a href="/index.php#over" class="text-white text-lg hover:text-primary">Over</a>
                    <a href="/index.php#locatie" class="text-white text-lg hover:text-primary">Locatie</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- 404 Content -->
    <section class="min-h-screen flex items-center justify-center pt-20 relative">
        <!-- Cloak Background -->
        <div class="cloak__wrapper">
            <div class="cloak__container">
                <div class="cloak"></div>
            </div>
        </div>
        <!-- Main Content -->
        <div class="container mx-auto px-4 text-center relative z-10">
            <h1>404</h1>
            <div class="info max-w-lg mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Pagina Niet Gevonden</h2>
                <p class="text-gray-300 mb-8 text-lg">Deze pagina bestaat niet.</p>
                <a href="/index.php" class="btn-404 text-white font-semibold flex items-center justify-center space-x-2 mx-auto w-fit">
                    <i class="fas fa-home"></i>
                    <span>Terug naar Home</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0D0D1A] py-10">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>