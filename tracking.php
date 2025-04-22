<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prayer Tracking - SalahSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                        <a class="nav-link active" href="tracking.php">
                            <i class="fas fa-chart-line me-1"></i>Prayer Tracking
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-chart-line me-2"></i>Prayer Tracking</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Track your daily prayers and monitor your spiritual progress over time.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Display -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Gregorian Date</h5>
                        <p class="h4" id="gregorian-date">Loading...</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Hijri Date</h5>
                        <p class="h4" id="hijri-date">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-day me-2"></i>Daily Progress</h5>
                    </div>
                    <div class="card-body" id="daily-stats">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-week me-2"></i>Weekly Progress</h5>
                    </div>
                    <div class="card-body" id="weekly-stats">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-alt me-2"></i>Monthly Progress</h5>
                    </div>
                    <div class="card-body" id="monthly-stats">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prayer Cards -->
        <div class="row prayer-cards-container">
            <div class="col-12 mb-4">
                <h4><i class="fas fa-mosque me-2"></i>Today's Prayers</h4>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card prayer-card" data-prayer-id="fajr">
                    <div class="card-header bg-dawn text-white">
                        <h5><i class="fas fa-sun me-2"></i>Fajr</h5>
                    </div>
                    <div class="card-body">
                        <p>Dawn Prayer</p>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-warning btn-pending active">
                                <i class="fas fa-hourglass-half me-1"></i> Pending
                            </button>
                            <button type="button" class="btn btn-outline-success btn-completed">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card prayer-card" data-prayer-id="dhuhr">
                    <div class="card-header bg-noon text-white">
                        <h5><i class="fas fa-sun me-2"></i>Dhuhr</h5>
                    </div>
                    <div class="card-body">
                        <p>Noon Prayer</p>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-warning btn-pending active">
                                <i class="fas fa-hourglass-half me-1"></i> Pending
                            </button>
                            <button type="button" class="btn btn-outline-success btn-completed">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card prayer-card" data-prayer-id="asr">
                    <div class="card-header bg-afternoon text-white">
                        <h5><i class="fas fa-sun me-2"></i>Asr</h5>
                    </div>
                    <div class="card-body">
                        <p>Afternoon Prayer</p>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-warning btn-pending active">
                                <i class="fas fa-hourglass-half me-1"></i> Pending
                            </button>
                            <button type="button" class="btn btn-outline-success btn-completed">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card prayer-card" data-prayer-id="maghrib">
                    <div class="card-header bg-sunset text-white">
                        <h5><i class="fas fa-moon me-2"></i>Maghrib</h5>
                    </div>
                    <div class="card-body">
                        <p>Sunset Prayer</p>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-warning btn-pending active">
                                <i class="fas fa-hourglass-half me-1"></i> Pending
                            </button>
                            <button type="button" class="btn btn-outline-success btn-completed">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card prayer-card" data-prayer-id="isha">
                    <div class="card-header bg-night text-white">
                        <h5><i class="fas fa-moon me-2"></i>Isha</h5>
                    </div>
                    <div class="card-body">
                        <p>Night Prayer</p>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-warning btn-pending active">
                                <i class="fas fa-hourglass-half me-1"></i> Pending
                            </button>
                            <button type="button" class="btn btn-outline-success btn-completed">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Instructions -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-info-circle me-2"></i>How to Use</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li class="mb-2">Click <strong>Completed</strong> after you perform each prayer.</li>
                            <li class="mb-2">Your progress is automatically calculated and saved.</li>
                            <li class="mb-2">View your daily, weekly, and monthly prayer statistics.</li>
                            <li class="mb-2">If you miss a prayer, it will remain in the <strong>Pending</strong> state.</li>
                        </ol>
                        <div class="alert alert-success">
                            <i class="fas fa-lightbulb me-2"></i> <strong>Tip:</strong> Use this tracking system to build consistency in your prayer habits and monitor your spiritual journey.
                        </div>
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


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Main JS -->
    <script src="js/main.js"></script>
</body>
</html>