<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAQUA - Veilingen & Biedingen</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Custom styles -->
    <script>
        // Configureer Tailwind
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
<body class="min-h-screen bg-gradient-to-b from-dark to-[#0F0F1A]">
    <button id="scrollToTop" class="hidden fixed bottom-6 right-6 bg-primary text-white p-3 rounded-full shadow-lg hover:bg-primary-dark transition">
        <i class="fas fa-chevron-up"></i>
    </button>
    <!-- Navbar -->
    <nav id="navbar" class="fixed top-0 left-0 w-full navbar-glass z-50 transition-all duration-300 py-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <a href="index.html" class="flex items-center">
                    <span class="text-2xl font-bold text-white">MAQ<span class="text-primary">UA</span></span>
                    <span class="w-2 h-2 rounded-full bg-primary ml-1"></span>
                </a>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.html#nieuws" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Nieuws</a>
                    <a href="index.html#over" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Over</a>
                    <a href="index.html#locatie" class="text-white hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-primary after:transition-all hover:after:w-full">Locatie</a>
                    <a href="bidding.html" class="text-primary hover:text-primary transition-colors relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-primary after:transition-all">Veilingen</a>
                </div>
                <!-- Desktop Icons -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#shop" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shop</span>
                    </a>
                    <a href="/aanmelden/aanmelden.html" class="flex items-center space-x-2 text-white hover:text-primary transition-colors">
                        <i class="fas fa-user"></i>
                        <span>Account</span>
                    </a>
                    <a href="tickets.html" class="btn-primary">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                    </a>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden flex items-center text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-[68px] left-0 w-full h-screen bg-dark md:hidden z-40">
            <div class="flex flex-col p-6 space-y-6">
                <a href="index.html#nieuws" class="text-white text-lg hover:text-primary">Nieuws</a>
                <a href="index.html#over" class="text-white text-lg hover:text-primary">Over</a>
                <a href="index.html#locatie" class="text-white text-lg hover:text-primary">Locatie</a>
                <a href="bidding.html" class="text-primary text-lg">Veilingen</a>
                <div class="border-t border-gray-700 pt-6 flex flex-col space-y-6">
                    <a href="#shop" class="flex items-center space-x-3 text-white hover:text-primary">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Shop</span>
                    </a>
                    <a href="/aanmelden/aanmelden.html" class="flex items-center space-x-3 text-white hover:text-primary">
                        <i class="fas fa-user w-6"></i>
                        <span>Account</span>
                    </a>
                    <a href="tickets.html" class="btn-primary mt-4 justify-center">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Tickets</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-[60vh] relative flex items-center justify-center overflow-hidden pt-20">
        <!-- Hero Background -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1551966775-a4ddc8df052b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'); filter: brightness(0.3);"></div>
        <!-- Hero Content -->
        <div class="container mx-auto px-4 pt-16 z-10 flex flex-col items-center">
            <div class="max-w-4xl mx-auto text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-4">Kunst & Antiek</h1>
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-primary mb-12">Veilingen</h2>
                <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">Ontdek exclusieve kunststukken en antiek uit verschillende tijdperken. Bied mee op unieke stukken en voeg ze toe aan uw collectie.</p>
                <div class="flex flex-wrap justify-center gap-4 mb-16">
                    <a href="#huidige-veilingen" class="btn-primary">
                        <span>Bekijk Huidige Veilingen</span>
                        <i class="fas fa-gavel"></i>
                    </a>
                    <a href="#aanmelden-veiling" class="btn-secondary">
                        <span>Aanmelden voor Veiling</span>
                        <i class="fas fa-user-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

        <!-- Upcoming Auctions -->
    <section class="py-20 bg-gradient-to-b from-[#0F0F1A] to-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Aankomende <span class="text-primary">Veilingen</span></h2>
                <div class="w-24 h-1 bg-primary mx-auto mb-6"></div>
                <p class="text-gray-300 max-w-3xl mx-auto">Bekijk onze geplande veilingen en markeer ze in uw agenda. Registreer u vooraf om deel te nemen aan deze exclusieve evenementen.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Auction Event 1 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg transition-transform hover:transform hover:scale-105">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Moderne Kunst Veiling" class="w-full h-64 object-cover">
                        <div class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full font-bold">
                            15 Juni 2025
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3">Moderne Kunst Veiling</h3>
                        <p class="text-gray-300 mb-4">Een exclusieve collectie moderne kunstwerken van opkomende en gevestigde kunstenaars uit de 21e eeuw.</p>
                        <div class="flex items-center mb-4">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            <span>14:00 - 18:00</span>
                        </div>
                        <div class="flex items-center mb-6">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            <span>Grote Zaal, MAQUA Museum</span>
                        </div>
                        <a href="#veiling-details-1" class="btn-primary w-full justify-center">
                            <span>Details & Registratie</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Auction Event 2 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg transition-transform hover:transform hover:scale-105">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1577083288073-40892c0860a4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antiek Meubilair" class="w-full h-64 object-cover">
                        <div class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full font-bold">
                            22 Juni 2025
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3">Antiek Meubilair</h3>
                        <p class="text-gray-300 mb-4">Zeldzame meubels uit de 18e en 19e eeuw, waaronder stukken uit koninklijke collecties en landhuizen.</p>
                        <div class="flex items-center mb-4">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            <span>11:00 - 16:00</span>
                        </div>
                        <div class="flex items-center mb-6">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            <span>Historische Vleugel, MAQUA Museum</span>
                        </div>
                        <a href="#veiling-details-2" class="btn-primary w-full justify-center">
                            <span>Details & Registratie</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Auction Event 3 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg transition-transform hover:transform hover:scale-105">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1572947650440-e8a97ef053b2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Historische Manuscripten" class="w-full h-64 object-cover">
                        <div class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full font-bold">
                            5 Juli 2025
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold mb-3">Historische Manuscripten</h3>
                        <p class="text-gray-300 mb-4">Zeldzame boeken, manuscripten en documenten die de geschiedenis van Antwerpia en omgeving documenteren.</p>
                        <div class="flex items-center mb-4">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            <span>13:00 - 17:00</span>
                        </div>
                        <div class="flex items-center mb-6">
                            <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                            <span>Bibliotheek Zaal, MAQUA Museum</span>
                        </div>
                        <a href="#veiling-details-3" class="btn-primary w-full justify-center">
                            <span>Details & Registratie</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#alle-veilingen" class="btn-secondary">
                    <span>Bekijk Alle Geplande Veilingen</span>
                    <i class="fas fa-calendar-alt ml-2"></i>
                </a>
            </div>
        </div>
    </section>

<!-- Current Auctions -->
<section id="huidige-veilingen" class="py-20 bg-gradient-to-b from-dark to-[#0F0F1A]">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">Huidige <span class="text-primary">Veilingen</span></h2>
            <div class="w-24 h-1 bg-primary mx-auto mb-6"></div>
            <p class="text-gray-300 max-w-3xl mx-auto">Bied nu mee op deze exclusieve items. Alle online biedingen sluiten op de aangegeven datum en tijd.</p>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Auction Item 1 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="A1245" data-start-bid="2500" data-current-bid="4250">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1579762593175-20226054cad0?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antiek Schilderij" class="w-full h-64 object-cover">
                <div class="absolute top-4 left-4 bg-accent text-dark px-3 py-1 rounded-full font-bold">Populair</div>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Landschap van Antwerpia</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #A1245</span>
                </div>
                <p class="text-gray-300 mb-4">19e-eeuws olieverfschilderij dat het historische landschap van Antwerpia toont, gesigneerd door J.F. Willems.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€2.500</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€4.250</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-12T18:00:00">2 dagen, 6 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-1" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>

        <!-- Auction Item 2 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="B2367" data-start-bid="3800" data-current-bid="5100">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1560072810-1cffb09faf0f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antieke Klok" class="w-full h-64 object-cover">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Staande Klok, ca. 1780</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #B2367</span>
                </div>
                <p class="text-gray-300 mb-4">Prachtig bewerkte staande klok uit de late 18e eeuw, met origineel uurwerk en handgeschilderde wijzerplaat.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€3.800</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€5.100</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-14T14:00:00">4 dagen, 12 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-2" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>

        <!-- Auction Item 3 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="C3489" data-start-bid="7500" data-current-bid="9200">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1551913902-c92207136625?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antieke Juwelen" class="w-full h-64 object-cover">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Art Deco Juwelen Set</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #C3489</span>
                </div>
                <p class="text-gray-300 mb-4">Complete set Art Deco juwelen uit de jaren 1920, bestaande uit een halsketting, armband en oorbellen met diamanten.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€7.500</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€9.200</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-11T20:00:00">1 dag, 8 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-3" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>

        <!-- Auction Item 4 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="D4512" data-start-bid="12000" data-current-bid="18500">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1584283367830-7875dd139a2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antiek Porselein" class="w-full h-64 object-cover">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Chinees Porselein Set</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #D4512</span>
                </div>
                <p class="text-gray-300 mb-4">Zeldzame collectie Ming-dynastie porselein, bestaande uit 12 borden, 6 kommen en een theepot met bijpassende kopjes.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€12.000</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€18.500</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-13T16:00:00">3 dagen, 4 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-4" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>

        <!-- Auction Item 5 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="E5678" data-start-bid="5000" data-current-bid="6750">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1564594985645-4427056e22e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Vintage Camera" class="w-full h-64 object-cover">
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Vintage Camera Collectie</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #E5678</span>
                </div>
                <p class="text-gray-300 mb-4">Verzameling van 15 vintage camera's uit de periode 1920-1960, inclusief zeldzame Leica en Hasselblad modellen.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€5.000</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€6.750</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-15T22:00:00">5 dagen, 10 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-5" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>

        <!-- Auction Item 6 -->
        <div class="bg-[rgba(255,255,255,0.05)] rounded-xl overflow-hidden shadow-lg auction-item" data-item-id="F6789" data-start-bid="8500" data-current-bid="14200">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1581974944026-5d6ed762f617?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Antieke Munten" class="w-full h-64 object-cover">
                <div class="absolute top-4 left-4 bg-accent text-dark px-3 py-1 rounded-full font-bold">Populair</div>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xl font-bold">Historische Muntencollectie</h3>
                    <span class="text-sm bg-[rgba(255,255,255,0.1)] px-2 py-1 rounded">Item #F6789</span>
                </div>
                <p class="text-gray-300 mb-4">Complete verzameling van zeldzame munten uit de geschiedenis van Antwerpia, van de Middeleeuwen tot de 19e eeuw.</p>
                <div class="mb-4 pb-4 border-b border-gray-700 bid-info">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Startbod:</span>
                        <span class="font-semibold start-bid">€8.500</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-400">Huidig bod:</span>
                        <span class="font-semibold text-primary current-bid">€14.200</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sluit over:</span>
                        <span class="font-semibold countdown" data-end-time="2025-03-12T15:00:00">2 dagen, 3 uur</span>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="#item-details-6" class="flex-1 bg-[rgba(255,255,255,0.1)] text-white py-2 rounded-lg font-semibold text-center hover:bg-[rgba(255,255,255,0.15)] transition-colors">Details</a>
                    <button class="flex-1 bg-primary text-white py-2 rounded-lg font-semibold text-center hover:bg-primary-dark transition-colors bid-button">Bied Nu</button>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-12">
        <a href="#alle-items" class="btn-secondary">
            <span>Bekijk Alle Veilingitems</span>
            <i class="fas fa-search ml-2"></i>
        </a>
    </div>
</div>
</section>

<!-- Bidding Popup -->
<div id="bidPopup" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
<div class="bg-dark p-6 rounded-xl w-full max-w-md">
    <h3 class="text-xl font-bold mb-4">Plaats uw bod</h3>
    <form id="bidForm">
        <div class="mb-4">
            <label class="block text-gray-300 mb-2">Item: <span id="bidItemName"></span></label>
            <label class="block text-gray-300 mb-2">Huidig bod: €<span id="bidCurrentAmount"></span></label>
        </div>
        <div class="mb-4">
            <input type="number" id="bidAmount" class="w-full custom-input py-2 px-3 rounded-lg" placeholder="Uw bod in €" step="50" required>
            <input type="hidden" id="bidItemId">
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-primary-dark">Bied</button>
            <button type="button" id="closeBidPopup" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700">Annuleer</button>
        </div>
    </form>
</div>
</div>

    <!-- How to Participate -->
    <section id="aanmelden-veiling" class="py-20 bg-gradient-to-b from-[#0F0F1A] to-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold mb-4">Hoe <span class="text-primary">Deelnemen</span></h2>
                <div class="w-24 h-1 bg-primary mx-auto mb-6"></div>
                <p class="text-gray-300 max-w-3xl mx-auto">Volg deze eenvoudige stappen om deel te nemen aan onze veilingen en uw kans te wagen op unieke kunst- en antiekstukken.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Step 1 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Registreer</h3>
                    <p class="text-gray-300">Maak een account aan en vul uw persoonlijke gegevens in. Verificatie is vereist voor deelname aan veilingen.</p>
                </div>

                <!-- Step 2 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Verken Items</h3>
                    <p class="text-gray-300">Bekijk de catalogus van beschikbare items, lees de beschrijvingen en bekijk de foto's om uw interesse te bepalen.</p>
                </div>

                <!-- Step 3 -->
                <div class="bg-[rgba(255,255,255,0.05)] rounded-xl p-8 text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Plaats Bod</h3>
                    <p class="text-gray-300">Plaats uw bod online of tijdens de live veiling. U ontvangt meldingen als u overboden wordt.</p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#registreer" class="btn-primary">
                    <span>Registreer Nu</span>
                    <i class="fas fa-user-plus ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#0D0D1A] pt-20 pb-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-16">
                <!-- Column 1: Logo & About -->
                <div>
                    <a href="#" class="flex items-center mb-6">
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
                
                <!-- Column 2: Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">
                        Snelle Links
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="index.html#nieuws" class="text-gray-400 hover:text-primary transition-colors">Nieuws</a></li>
                        <li><a href="index.html#over" class="text-gray-400 hover:text-primary transition-colors">Over Ons</a></li>
                        <li><a href="#collection" class="text-gray-400 hover:text-primary transition-colors">Collectie</a></li>
                        <li><a href="#events" class="text-gray-400 hover:text-primary transition-colors">Evenementen</a></li>
                        <li><a href="tickets.html" class="text-gray-400 hover:text-primary transition-colors">Tickets</a></li>
                        <li><a href="#vacatures" class="text-gray-400 hover:text-primary transition-colors">Werken bij MAQUA</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-6 relative inline-block">
                        Contact
                        <span class="absolute bottom-0 left-0 w-full h-0.5 bg-primary"></span>
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-400">Museumplein 123, 3000 Antwerpia</span>
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
                
                <!-- Column 4: Newsletter -->
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
            
            <!-- Bottom Footer -->
            <div class="pt-8 mt-8 border-t border-gray-800 text-center">
                <p class="text-gray-500 text-sm">© 2025 MAQUA Museum. Alle rechten voorbehouden.</p>
                <div class="flex justify-center mt-4 space-x-6">
                    <a href="#privacy" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Privacybeleid</a>
                    <a href="#terms" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Gebruiksvoorwaarden</a>
                    <a href="#cookies" class="text-gray-500 hover:text-gray-400 transition-colors text-sm">Cookiebeleid</a>
                </div>
            </div>
        </div>
    </footer>

 <!-- Scripts -->
<script>
// Mobile menu toggle
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        mobileMenuButton.innerHTML = isOpen 
            ? '<i class="fas fa-times text-xl"></i>' 
            : '<i class="fas fa-bars text-xl"></i>';
        console.log('Mobile menu toggled:', isOpen ? 'open' : 'closed');
    });
} else {
    console.error('Mobile menu elementen niet gevonden');
}

// Navbar scroll effect
const navbar = document.getElementById('navbar');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('py-2', 'shadow-lg');
            navbar.classList.remove('py-4');
        } else {
            navbar.classList.add('py-4');
            navbar.classList.remove('py-2', 'shadow-lg');
        }
    });
} else {
    console.error('Navbar niet gevonden');
}

// Scroll to top button
const scrollToTopBtn = document.getElementById('scrollToTop');
if (scrollToTopBtn) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.remove('hidden');
        } else {
            scrollToTopBtn.classList.add('hidden');
        }
    });
    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
} else {
    console.error('Scroll to top button niet gevonden');
}

// Voeg een bidding log toe
const auctionSection = document.querySelector('#huidige-veilingen .container');
if (auctionSection) {
    const bidLog = document.createElement('div');
    bidLog.id = 'bidLog';
    bidLog.className = 'mt-8 text-gray-300 text-sm';
    bidLog.innerHTML = '<h3 class="text-lg font-bold mb-2">Recente Biedingen:</h3>';
    auctionSection.appendChild(bidLog);
} else {
    console.error('Auction section niet gevonden');
}

// Bidding popup
const bidButtons = document.querySelectorAll('.bid-button');
const bidPopup = document.getElementById('bidPopup');
const bidForm = document.getElementById('bidForm');
const closeBidPopup = document.getElementById('closeBidPopup');
const bidItemName = document.getElementById('bidItemName');
const bidCurrentAmount = document.getElementById('bidCurrentAmount');
const bidAmountInput = document.getElementById('bidAmount');
const bidItemId = document.getElementById('bidItemId');

if (bidButtons.length && bidPopup && bidForm && closeBidPopup && bidItemName && bidCurrentAmount && bidAmountInput && bidItemId) {
    bidButtons.forEach(button => {
        button.addEventListener('click', () => {
            const auctionItem = button.closest('.auction-item');
            if (!auctionItem) {
                console.error('Auction item niet gevonden voor bid button');
                return;
            }
            const itemId = auctionItem.dataset.itemId;
            const currentBid = parseFloat(auctionItem.dataset.currentBid);
            const itemName = auctionItem.querySelector('h3').textContent;

            bidItemName.textContent = itemName;
            bidCurrentAmount.textContent = currentBid.toLocaleString('nl-NL');
            bidItemId.value = itemId;
            bidAmountInput.min = currentBid + 50;
            bidAmountInput.value = currentBid + 50;
            bidPopup.classList.remove('hidden');
            console.log('Bid popup geopend voor item:', itemId);
        });
    });

    closeBidPopup.addEventListener('click', () => {
        bidPopup.classList.add('hidden');
        bidForm.reset();
        console.log('Bid popup gesloten');
    });

    bidForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const itemId = bidItemId.value;
        const newBid = parseFloat(bidAmountInput.value);
        const auctionItem = document.querySelector(`.auction-item[data-item-id="${itemId}"]`);
        const currentBid = parseFloat(auctionItem.dataset.currentBid);

        if (newBid > currentBid) {
            auctionItem.dataset.currentBid = newBid;
            auctionItem.querySelector('.current-bid').textContent = `€${newBid.toLocaleString('nl-NL')}`;
            bidPopup.classList.add('hidden');
            bidForm.reset();
            addBidToLog('Jij', auctionItem.querySelector('h3').textContent, newBid);
            alert(`Uw bod van €${newBid.toLocaleString('nl-NL')} is succesvol geplaatst!`);
            console.log('Bod geplaatst:', newBid, 'op item', itemId);
        } else {
            alert('Uw bod moet hoger zijn dan het huidige bod!');
            console.log('Bod geweigerd: te laag', newBid, '<=', currentBid);
        }
    });
} else {
    console.error('Een of meer bidding elementen niet gevonden');
}

// Bot bidding
const botNames = ['Kunstliefhebber', 'AntiekVerzamelaar', 'BiedMeester', 'VeilingFanaat', 'Collectionneur', 'Kunstkenner'];

function randomBotBid() {
    const items = Array.from(document.querySelectorAll('.auction-item'));
    const activeItems = items.filter(item => {
        const endTime = new Date(item.querySelector('.countdown').dataset.endTime).getTime();
        return endTime > Date.now();
    });

    if (activeItems.length === 0) {
        console.log('Geen actieve veilingen meer voor bot bidding');
        return;
    }

    const randomItem = activeItems[Math.floor(Math.random() * activeItems.length)];
    const currentBid = parseFloat(randomItem.dataset.currentBid);
    const increment = Math.floor(Math.random() * 200) + 50;
    const newBid = currentBid + increment;

    randomItem.dataset.currentBid = newBid;
    const currentBidElement = randomItem.querySelector('.current-bid');
    if (currentBidElement) {
        currentBidElement.textContent = `€${newBid.toLocaleString('nl-NL')}`;
    } else {
        console.error('Current bid element niet gevonden voor bot bid:', randomItem);
    }

    const botName = botNames[Math.floor(Math.random() * botNames.length)];
    const itemName = randomItem.querySelector('h3').textContent;
    addBidToLog(botName, itemName, newBid);
    console.log(`${botName} biedt €${newBid.toLocaleString('nl-NL')} op "${itemName}"`);

    randomItem.classList.add('bg-[rgba(255,0,0,0.1)]');
    setTimeout(() => randomItem.classList.remove('bg-[rgba(255,0,0,0.1)]'), 1000);
}

function startBotBidding() {
    console.log('Bot bidding gestart');
    randomBotBid();
    setInterval(() => randomBotBid(), 5000);
}

// Biedingen loggen
function addBidToLog(bidder, itemName, amount) {
    const bidLog = document.getElementById('bidLog');
    if (bidLog) {
        const logEntry = document.createElement('p');
        logEntry.textContent = `${bidder} bood €${amount.toLocaleString('nl-NL')} op "${itemName}" - ${new Date().toLocaleTimeString()}`;
        bidLog.appendChild(logEntry);
        while (bidLog.children.length > 6) {
            bidLog.removeChild(bidLog.children[1]); // Houd header + max 5 entries
        }
    } else {
        console.error('Bid log niet gevonden');
    }
}

// Countdown timer
console.log('Huidige tijd:', new Date().toISOString(), 'Eindtijd:', countdown.dataset.endTime);
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(countdown => {
        const endTime = new Date(countdown.dataset.endTime).getTime();
        const now = Date.now();
        const distance = endTime - now;

        if (distance < 0) {
            countdown.textContent = 'Veiling Gesloten';
            const bidButton = countdown.closest('.auction-item').querySelector('.bid-button');
            if (bidButton) {
                bidButton.disabled = true;
                bidButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        countdown.textContent = `${days}d ${hours}u ${minutes}m`;
    });
}

// Lazy loading observer
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('[class*="bg-[rgba(255,255,255,0.05)"]').forEach(item => {
    observer.observe(item);
});

// Initialisatie
document.addEventListener('DOMContentLoaded', () => {
    console.log('Pagina geladen, initialisatie gestart');
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
    startBotBidding();
});
</script>
</body>
</html>