<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site avec Recherche Avancée</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #398FAA;
            --primary-hover: #2e7a91;
            --primary-dark: #1d5c6e;
            --text-dark: #333333;
            --text-light: #f8f9fa;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navigation Bar */
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: var(--shadow-sm);
        }

        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link:focus {
            color: white;
            transform: translateY(-2px);
        }

        .nav-link.active {
            color: white !important;
            font-weight: 500;
            position: relative;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: white;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        /* Search Section */
        .search-container {
            background-color: #f1f3f5;
            padding: 2.5rem;
            border-radius: 12px;
            margin-top: 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .search-container:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
        }

        /* Ad Carousel */
        .ad-carousel-container {
            position: relative;
            margin: 2rem auto;
            max-width: 100%;
            overflow: hidden;
        }

        .ad-track {
            display: flex;
            gap: 1.25rem;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 0 1rem;
        }

        .ad-slide {
            flex: 0 0 calc(25% - 1rem);
            min-width: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            position: relative;
            aspect-ratio: 16/9;
        }

        .ad-slide:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            z-index: 5;
        }

        .ad-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ad-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            padding: 1rem;
            font-size: 0.95rem;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            opacity: 0.8;
            z-index: 10;
        }

        .carousel-nav:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-nav.prev {
            left: 0;
        }

        .carousel-nav.next {
            right: 0;
        }

        .indicators {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .indicator {
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background: #ccc;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background: var(--primary-color);
            transform: scale(1.2);
        }

        /* Advanced Search */
        .advanced-search-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            padding: 1.25rem;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .ad-slide {
                flex: 0 0 calc(33.33% - 1rem);
            }
        }

        @media (max-width: 768px) {
            .ad-slide {
                flex: 0 0 calc(50% - 0.75rem);
            }

            .search-container {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .ad-slide {
                flex: 0 0 calc(100% - 1rem);
            }

            .ad-carousel-container {
                padding: 0;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <main class="container mt-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carousel Implementation
            const initCarousel = () => {
                const track = document.querySelector('.ad-track');
                const slides = document.querySelectorAll('.ad-slide');
                const prevBtn = document.querySelector('.carousel-nav.prev');
                const nextBtn = document.querySelector('.carousel-nav.next');
                const indicatorsContainer = document.querySelector('.indicators');

                if (!track || slides.length === 0) return;

                const slideCount = slides.length;
                let currentIndex = 0;
                const visibleSlides = 4;
                let autoSlideInterval;

                // Create indicators
                const createIndicators = () => {
                    indicatorsContainer.innerHTML = '';
                    for (let i = 0; i < slideCount; i++) {
                        const indicator = document.createElement('div');
                        indicator.classList.add('indicator');
                        if (i === 0) indicator.classList.add('active');
                        indicator.addEventListener('click', () => goToSlide(i));
                        indicatorsContainer.appendChild(indicator);
                    }
                };

                // Update carousel position
                const updateCarousel = () => {
                    const slideWidth = slides[0].offsetWidth + 20; // includes gap
                    track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;

                    // Update indicators
                    document.querySelectorAll('.indicator').forEach((ind, index) => {
                        ind.classList.toggle('active', index === currentIndex);
                    });
                };

                // Go to specific slide
                const goToSlide = (index) => {
                    currentIndex = index;
                    updateCarousel();
                };

                // Navigation handlers
                const handlePrev = () => {
                    currentIndex = Math.max(currentIndex - 1, 0);
                    updateCarousel();
                    resetAutoSlide();
                };

                const handleNext = () => {
                    currentIndex = Math.min(currentIndex + 1, slideCount - visibleSlides);
                    updateCarousel();
                    resetAutoSlide();
                };

                // Auto-rotation
                const startAutoSlide = () => {
                    autoSlideInterval = setInterval(() => {
                        if (currentIndex >= slideCount - visibleSlides) {
                            currentIndex = 0;
                        } else {
                            currentIndex++;
                        }
                        updateCarousel();
                    }, 5000);
                };

                const resetAutoSlide = () => {
                    clearInterval(autoSlideInterval);
                    startAutoSlide();
                };

                // Event listeners
                prevBtn.addEventListener('click', handlePrev);
                nextBtn.addEventListener('click', handleNext);

                // Pause on hover
                track.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
                track.addEventListener('mouseleave', startAutoSlide);

                // Initialize
                createIndicators();
                startAutoSlide();

                // Handle resize
                window.addEventListener('resize', updateCarousel);
            };

            // Advanced Search Toggle
            const initAdvancedSearch = () => {
                const advancedBtn = document.querySelector('.btn-advanced');
                const advancedSearch = document.getElementById('advancedSearch');
                const hideAdvanced = document.getElementById('hideAdvanced');

                if (!advancedBtn || !advancedSearch || !hideAdvanced) return;

                advancedBtn.addEventListener('click', function() {
                    advancedSearch.style.display = 'block';
                    window.scrollTo({
                        top: advancedSearch.offsetTop - 30,
                        behavior: 'smooth'
                    });
                });

                hideAdvanced.addEventListener('click', function() {
                    advancedSearch.style.display = 'none';
                });
            };

            // Initialize all components
            initCarousel();
            initAdvancedSearch();
        });
    </script>
</body>

</html>
