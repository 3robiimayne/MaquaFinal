// Mobile menu toggle
const mobileMenuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
const overlay = document.querySelector('.mobile-menu-overlay');

mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Hamburger icoon veranderen
    const icon = mobileMenuButton.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

// Sluiten bij klik op overlay of links
overlay.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    overlay.classList.remove('active');
    mobileMenuButton.querySelector('i').classList.replace('fa-times', 'fa-bars');
});

document.querySelectorAll('#mobile-menu a').forEach(link => {
    link.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
        mobileMenuButton.querySelector('i').classList.replace('fa-times', 'fa-bars');
    });
});

// Theme toggle
const themeToggleDesktop = document.getElementById('theme-toggle-desktop');
const themeToggleMobile = document.getElementById('theme-toggle-mobile');
const body = document.body;
const savedTheme = localStorage.getItem('theme');

if (savedTheme) {
    body.setAttribute('data-theme', savedTheme);
}

function toggleTheme() {
    if (body.getAttribute('data-theme') === 'orange') {
        body.removeAttribute('data-theme');
        localStorage.setItem('theme', 'default');
    } else {
        body.setAttribute('data-theme', 'orange');
        localStorage.setItem('theme', 'orange');
    }
}

themeToggleDesktop.addEventListener('click', toggleTheme);
themeToggleMobile.addEventListener('click', toggleTheme);

// Scroll to top button
const scrollToTopButton = document.getElementById('scrollToTop');

window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollToTopButton.classList.add('show');
    } else {
        scrollToTopButton.classList.remove('show');
    }
});

scrollToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// FAQ functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const item = question.parentElement;
        item.classList.toggle('active');
    });
});