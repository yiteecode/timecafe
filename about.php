<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Time Cafe</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/time-logo.png" rel="icon">
  <link href="assets/img/time-logo.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

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
          <!-- <li><a href="events.html" data-i18n="nav.events">Events</a></li> -->
          <li><a href="chefs.php" data-i18n="nav.chefs">Chefs</a></li>
          <li><a href="gallery.php" data-i18n="nav.gallery">Gallery</a></li>
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

      <a class="btn-getstarted" href="index.php#book-a-table">Book a Table</a>

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
  </header>

    <main class="main">

<?php
require_once 'db-config.php';

// Get about content
try {
    $stmt = $connect->prepare("SELECT * FROM about_section WHERE id = 1");
    $stmt->execute();
    $about = $stmt->get_result()->fetch_assoc();

    $stmt = $connect->prepare("
        SELECT * FROM about_features 
        WHERE active = 1 
        ORDER BY sort_order, created_at DESC
    ");
    $stmt->execute();
    $features = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error loading about page: " . $e->getMessage());
    $about = [];
    $features = [];
}
?>

<!-- About Section -->
<section id="about" class="about">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>About Us</h2>
            <?php if (!empty($about['subheading'])): ?>
                <p><?php echo htmlspecialchars($about['subheading']); ?></p>
            <?php endif; ?>
        </div>

        <div class="row gy-4">
            <?php if (!empty($about['image'])): ?>
                <div class="col-lg-7 position-relative about-img" 
                     style="background-image: url('uploads/about/<?php echo htmlspecialchars($about['image']); ?>');" 
                     data-aos="fade-up" data-aos-delay="150">
                    <?php if (!empty($about['video_url'])): ?>
                        <div class="play-btn">
                            <a href="<?php echo htmlspecialchars($about['video_url']); ?>" 
                               class="glightbox play-btn"></a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="col-lg-5 d-flex align-items-end" data-aos="fade-up" data-aos-delay="300">
                <div class="content ps-0 ps-lg-5">
                    <?php if (!empty($about['heading'])): ?>
                        <h3><?php echo htmlspecialchars($about['heading']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if (!empty($about['main_content'])): ?>
                        <p><?php echo nl2br(htmlspecialchars($about['main_content'])); ?></p>
                    <?php endif; ?>

                    <div class="position-relative mt-4">
                        <?php foreach ($features as $feature): ?>
                            <div class="feature-item" data-aos="fade-up" data-aos-delay="150">
                                <i class="bi bi-<?php echo htmlspecialchars($feature['icon']); ?>"></i>
                                <div>
                                    <h4><?php echo htmlspecialchars($feature['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($feature['description']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($about['mission']) || !empty($about['vision'])): ?>
                        <div class="row mt-5">
                            <?php if (!empty($about['mission'])): ?>
                                <div class="col-md-6">
                                    <div class="mission-vision-box">
                                        <h4>Our Mission</h4>
                                        <p><?php echo nl2br(htmlspecialchars($about['mission'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($about['vision'])): ?>
                                <div class="col-md-6">
                                    <div class="mission-vision-box">
                                        <h4>Our Vision</h4>
                                        <p><?php echo nl2br(htmlspecialchars($about['vision'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($about['video_url'])): ?>
    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="ratio ratio-16x9">
                        <iframe src="" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

    </main>
    <footer id="footer" class="footer dark-background">

      <div class="container">
        <div class="row gy-3">
          <div class="col-lg-3 col-md-6 d-flex">
            <i class="bi bi-geo-alt icon"></i>
            <div class="address">
              <h4>Address</h4>
              <p>Piyasa Sub City</p>
              <p>Hawassa, Ethiopia</p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6 d-flex">
            <i class="bi bi-telephone icon"></i>
            <div>
              <h4>Contact</h4>
              <p>
                <strong>Phone:</strong> <span>+251 955 995 524</span><br>
                <strong>Email:</strong> <span>yiteecode@gmail.com</span><br>
              </p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6 d-flex">
            <i class="bi bi-clock icon"></i>
            <div>
              <h4>Opening Hours</h4>
              <p>
                <strong>Mon-Sun:</strong> <span>7AM - 10PM</span><br>
                <strong>Holidays:</strong> <span>Open</span>
              </p>
            </div>
          </div>
    
          <div class="col-lg-3 col-md-6">
            <h4>Follow Us</h4>
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
          <!-- Keep the credits as per licensing -->
          Designed by <a href="https://yitage.github.io/yiteefreelancer.github.io/">yiteecode</a>
        </div>
      </div>
    
    </footer>
  
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
    <!-- Preloader -->
    <div id="preloader"></div>
  
    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
  
</body>
</html>