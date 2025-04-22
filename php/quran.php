<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Verse - SalahSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-mosque me-2"></i>SalahSync
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="prayer-times.php">
                            <i class="fas fa-clock me-1"></i>Prayer Times
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">
                            <i class="fas fa-calendar-alt me-1"></i>Islamic Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="quran.php">
                            <i class="fas fa-book-open me-1"></i>Daily Verse
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../tracking.php">
                            <i class="fas fa-chart-line me-1"></i>Prayer Tracking
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-book-open me-2"></i>Daily Quran Verse</h3>
            </div>
            <div class="card-body">
                <div class="verse-container">
                    <div class="arabic-text" id="arabic-verse">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="translation" id="verse-translation">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html> 