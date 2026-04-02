
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileLinks = document.querySelectorAll('.mobile-link');

    if (hamburger && mobileMenu) {
        // Redundant listeners removed. Toggling is handled by toggleMobileMenu in index.php.

        mobileLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const targetId = href.substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        const navHeight = document.querySelector('nav').offsetHeight;
                        window.scrollTo({
                            top: targetElement.offsetTop - navHeight,
                            behavior: 'smooth'
                        });
                    }
                } else if (href.includes('#')) {
                    const parts = href.split('#');
                    if (window.location.pathname.endsWith(parts[0]) || parts[0] === 'index.php') {
                        e.preventDefault();
                        const targetId = parts[1];
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            const navHeight = document.querySelector('nav').offsetHeight;
                            window.scrollTo({
                                top: targetElement.offsetTop - navHeight,
                                behavior: 'smooth'
                            });
                        }
                    }
                }
                hamburger.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });

        // Event listener click dokumen redundant dihapus. Click overlay sudah menangani penutupan mobile menu di index.php
    }
});

window.addEventListener('scroll', () => {
    let current = '';
    const sections = document.querySelectorAll('section[id]');

    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= (sectionTop - 120)) {
            current = section.getAttribute('id');
        }
    });

    const navItems = document.querySelectorAll('.nav-center a, .mobile-link');
    navItems.forEach(a => {
        a.classList.remove('active');
        const href = a.getAttribute('href');
        if (href === `#${current}` || (href.includes('#') && href.split('#')[1] === current)) {
            a.classList.add('active');
        }
    });
});