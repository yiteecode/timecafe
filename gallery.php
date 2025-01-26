<?php
require_once 'db-config.php';

// Get gallery images
try {
    $stmt = $connect->prepare("
        SELECT * FROM gallery 
        WHERE active = 1 
        ORDER BY sort_order DESC, created_at DESC
    ");
    $stmt->execute();
    $gallery_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error loading gallery: " . $e->getMessage());
    $gallery_items = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gallery - Time Cafe</title>
    
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    
    <!-- Template Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">
    <!-- Header -->
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
                <img src="assets/img/time-logo.png" alt="time logo">
                <h1 class="sitename">Time Cafe</h1>
                <span>.</span>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="index.php" data-i18n="nav.home">Home</a></li>
                    <li><a href="about.php" data-i18n="nav.about">About</a></li>
                    <li><a href="menu.php" data-i18n="nav.menu">Menu</a></li>
                    <li><a href="chefs.php" data-i18n="nav.chefs">Chefs</a></li>
                    <li><a href="gallery.php" class="active" data-i18n="nav.gallery">Gallery</a></li>
                    <li class="dropdown">
                        <a href="#" data-i18n="nav.services">
                            <span>Our Services</span>
                            <i class="bi bi-chevron-down toggle-dropdown"></i>
                        </a>
                        <ul>
                            <li><a href="coffee.html">Coffee</a></li>
                            <li class="dropdown">
                                <a href="restaurant.html">
                                    <span>Restaurant</span>
                                    <i class="bi bi-chevron-down toggle-dropdown"></i>
                                </a>
                                <ul>
                                    <li><a href="menu.php">Breakfast</a></li>
                                    <li><a href="menu.php">Lunch</a></li>
                                    <li><a href="menu.php">Dinner</a></li>
                                </ul>
                            </li>
                            <li><a href="reservation.html">Reservation</a></li>
                        </ul>
                    </li>
                    <li><a href="contact.php" data-i18n="nav.contact">Contact</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="index.php#book-a-table" data-i18n="nav.book">Book a Table</a>

            <div class="language-selector">
                <button type="button" class="lang-toggle">
                    <i class="bi bi-translate"></i>
                    <span class="current-lang">EN</span>
                </button>
                <ul class="lang-dropdown">
                    <li data-lang="en">English</li>
                    <li data-lang="am">አማርኛ</li>
                </ul>
            </div>
        </div>
    </header>

    <main>
        <!-- Gallery Section -->
        <section id="gallery" class="gallery">
            <div class="container-fluid">
                <div class="section-title">
                    <h2>Our Gallery</h2>
                    <p>Some photos from <span class="description-title">Time Cafe</span></p>
                </div>

                <div class="gallery-container">
                    <?php foreach ($gallery_items as $item): ?>
                        <div class="gallery-item" data-aos="fade-up">
                            <a href="uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                               class="glightbox" 
                               data-gallery="gallery-images"
                               data-title="<?php echo htmlspecialchars($item['title']); ?>"
                               data-description="<?php echo htmlspecialchars($item['description']); ?>">
                                <div class="gallery-image-wrapper">
                                    <img src="uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="img-fluid">
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                            <?php if (!empty($item['description'])): ?>
                                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="footer" class="footer dark-background">
        <div class="container">
            <div class="row gy-3">
                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-geo-alt icon"></i>
                    <div>
                        <h4 data-i18n="footer.address">Address</h4>
                        <p>Piyasa Sub City<br>Hawassa, Ethiopia</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-telephone icon"></i>
                    <div>
                        <h4 data-i18n="footer.contact">Contact</h4>
                        <p>
                            <strong>Phone:</strong> +251 955 995 524<br>
                            <strong>Email:</strong> yiteecode@gmail.com
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-clock icon"></i>
                    <div>
                        <h4 data-i18n="footer.hours">Opening Hours</h4>
                        <p>
                            <strong>Mon-Sun: </strong>7AM - 10PM<br>
                            <strong>Holidays: </strong>Open
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h4 data-i18n="footer.follow">Follow Us</h4>
                    <div class="social-links d-flex">
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Time Cafe</strong> <span>All Rights Reserved</span></p>
            <div class="credits">
                Designed by <a href="https://yitage.github.io/yiteefreelancer.github.io/">yiteecode</a>
            </div>
        </div>
    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/translations.js"></script>

    <script>
        // Initialize GLightbox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    </script>
</body>
</html>