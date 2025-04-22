<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SalahSync - Prayer Times & Tracking</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mosque me-2"></i>SalahSync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="php/prayer-times.php">
                            <i class="fas fa-clock me-1"></i>Prayer Times
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="php/calendar.php">
                            <i class="fas fa-calendar-alt me-1"></i>Islamic Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="php/quran.php">
                            <i class="fas fa-book-open me-1"></i>Daily Verse
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tracking.php">
                            <i class="fas fa-chart-line me-1"></i>Prayer Tracking
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Parallax Effect -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title">
                        SalahSync
                    </h1>
                    <div class="arabic-text">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
                    <p class="hero-subtitle">Your comprehensive companion for prayer times, Quran verses, and spiritual growth</p>
                    <div class="hero-buttons">
                        <a href="php/prayer-times.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-clock me-2"></i>View Prayer Times
                        </a>
                        <a href="tracking.php" class="btn btn-outline-light btn-lg ms-2">
                            <i class="fas fa-chart-line me-2"></i>Track Prayers
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="hero-mosque-icon">
                        <i class="fas fa-mosque"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-overlay"></div>
    </div>

    <!-- Features Section -->
    <div class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Our Features</h2>
                    <p class="section-subtitle">Discover the tools to enhance your spiritual journey</p>
                </div>
            </div>
            <div class="row features-row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Prayer Times</h3>
                        <p>Get accurate prayer times based on your location. Stay informed about all five daily prayers.</p>
                        <a href="php/prayer-times.php" class="feature-link">
                            View Prayer Times <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Islamic Calendar</h3>
                        <p>Stay updated with the Islamic Hijri calendar and upcoming important religious events.</p>
                        <a href="php/calendar.php" class="feature-link">
                            View Calendar <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>Daily Quran Verse</h3>
                        <p>Receive daily inspiration with verses from the Holy Quran and their translations.</p>
                        <a href="php/quran.php" class="feature-link">
                            Read Today's Verse <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Prayer Tracking</h3>
                        <p>Monitor your daily prayers and track your spiritual progress over time.</p>
                        <a href="tracking.php" class="feature-link">
                            Track Prayers <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quran Verse Highlight -->
    <div class="verse-highlight-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="verse-card">
                        <div class="verse-icon">
                            <i class="fas fa-quran"></i>
                        </div>
                        <h3>Daily Inspiration</h3>
                        <div class="arabic-verse">وَإِذَا سَأَلَكَ عِبَادِي عَنِّي فَإِنِّي قَرِيبٌ ۖ أُجِيبُ دَعْوَةَ الدَّاعِ إِذَا دَعَانِ</div>
                        <p class="verse-translation">"And when My servants ask you concerning Me - indeed I am near. I respond to the invocation of the supplicant when he calls upon Me."</p>
                        <p class="verse-reference">Surah Al-Baqarah (2:186)</p>
                        <a href="php/quran.php" class="btn btn-outline-light">Read More Verses</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="cta-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2>Start Tracking Your Prayers Today</h2>
                    <p>Monitor your spiritual journey and build consistency in your daily prayers.</p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="tracking.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-line me-2"></i>Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4><i class="fas fa-mosque me-2"></i>SalahSync</h4>
                    <p>Your comprehensive companion for prayer times, Quran verses, and spiritual growth.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="php/prayer-times.php"><i class="fas fa-clock me-2"></i>Prayer Times</a></li>
                        <li><a href="php/calendar.php"><i class="fas fa-calendar-alt me-2"></i>Islamic Calendar</a></li>
                        <li><a href="php/quran.php"><i class="fas fa-book-open me-2"></i>Daily Verse</a></li>
                        <li><a href="tracking.php"><i class="fas fa-chart-line me-2"></i>Prayer Tracking</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope me-2"></i>Email: contact@salahsync.com</p>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6">
                        <p><i class="fas fa-copyright me-2"></i>2025 SalahSync. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p>Made with <i class="fas fa-heart text-danger"></i> by Hassan</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>