<?php
require_once 'db-config.php';

// Fetch hero section data
$hero_query = "SELECT * FROM hero_section WHERE id = 1";
$hero_result = mysqli_query($connect, $hero_query);
$hero_data = mysqli_fetch_assoc($hero_result);

// Set default values if no data exists
if (!$hero_data) {
    $hero_data = [
        'heading' => 'Experience Authentic Ethiopian Coffee',
        'subheading' => 'Discover the rich flavors of freshly brewed Habesha coffee and a variety of delicious dishes in the heart of Hawassa.',
        'hero_image' => '',
        'video_url' => ''
    ];
}


// Get current logo
$logoStmt = $connect->prepare("SELECT setting_value FROM settings WHERE setting_key = 'brand_logo'");
$logoStmt->execute();
$logoResult = $logoStmt->get_result();
$currentLogo = $logoResult->fetch_assoc()['setting_value'] ?? 'time-logo.png';

// Add cache-busting parameter
$logoUrl = 'assets/img/' . htmlspecialchars($currentLogo) . '?v=' . time();


?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Time Cafe</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo $logoUrl; ?>" rel="icon">
  <link href="<?php echo $logoUrl; ?>" rel="apple-touch-icon">

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
        
        <img src="<?php echo $logoUrl; ?>" alt="time logo">
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

   <!-- Hero Section -->
<section id="hero" class="hero section light-background">
    <div class="container">
        <div class="row gy-4 justify-content-center justify-content-lg-between">
            <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center">
                <h1 data-aos="fade-up">
                    <?php echo htmlspecialchars($hero_data['heading']); ?>
                </h1>
                <p data-aos="fade-up" data-aos-delay="100">
                    <?php echo nl2br(htmlspecialchars($hero_data['subheading'])); ?>
                </p>
                <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                    <a href="#book-a-table" class="btn-get-started">Book a Table</a>
                    <?php if (!empty($hero_data['video_url'])): ?>
                        <a href="./uploads/hero/<?php echo htmlspecialchars($hero_data['video_url']); ?>" 
                           class="glightbox btn-watch-video d-flex align-items-center">
                            <i class="bi bi-play-circle"></i><span>Watch Video</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-5 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                <?php if (!empty($hero_data['hero_image'])): ?>
                    <img src="./uploads/hero/<?php echo htmlspecialchars($hero_data['hero_image']); ?>" 
                         class="img-fluid animated" alt="Hero Image">
                <?php else: ?>
                    <img src="assets/img/timesandwich.png" class="img-fluid animated" alt="Default Hero Image">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!-- /Hero Section -->

<!-- Why Us Section -->
<section id="why-us" class="why-us section light-background">

  <div class="container">

    <div class="row gy-4">

      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
        <div class="why-box">
          <h3>Why Choose Time Café</h3>
          <p>
            At Time Café, we pride ourselves on serving the finest Ethiopian coffee, brewed to perfection. Paired with our warm ambiance and authentic Ethiopian dishes, we promise an unforgettable culinary experience.
          </p>
          <div class="text-center">
            <a href="#" class="more-btn"><span>Learn More</span> <i class="bi bi-chevron-right"></i></a>
          </div>
        </div>
      </div>
      <!-- End Why Box -->

      <div class="col-lg-8 d-flex align-items-stretch">
        <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">

          <div class="col-xl-4">
            <div class="icon-box d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-cup"></i>
              <h4>Authentic Ethiopian Coffee</h4>
              <p>Savor the unique and rich flavors of traditional Habesha coffee, prepared the Ethiopian way.</p>
            </div>
          </div>
          <!-- End Icon Box -->

          <div class="col-xl-4" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-box d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-emoji-smile"></i>
              <h4>Warm and Inviting Ambiance</h4>
              <p>Enjoy a cozy atmosphere perfect for meeting friends, family, or just relaxing.</p>
            </div>
          </div>
          <!-- End Icon Box -->

          <div class="col-xl-4" data-aos="fade-up" data-aos-delay="400">
            <div class="icon-box d-flex flex-column justify-content-center align-items-center">
              <i class="bi bi-restaurant"></i>
              <h4>Delicious Dishes</h4>
              <p>Explore our menu featuring authentic Ethiopian cuisine and a variety of international flavors.</p>
            </div>
          </div>
          <!-- End Icon Box -->

        </div>
      </div>

    </div>

  </div>

</section>
<!-- /Why Us Section -->


    <!-- Stats Section -->
    <section id="stats" class="stats section dark-background">

      <img src="assets/img/stats-bg.jpg" alt="" data-aos="fade-in">

      <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="500" data-purecounter-duration="1" class="purecounter"></span>
              <p>+ Customers</p>
            </div>
          </div><!-- End Stats Item -->


          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="24" data-purecounter-duration="1" class="purecounter"></span>
              <p>Hours Of Support</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="51" data-purecounter-duration="2" class="purecounter"></span>
              <p>+ Workers</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="50" data-purecounter-duration="1" class="purecounter"></span>
              <p>Defrent food types</p>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Stats Section -->

   
    <!-- Testimonials Section -->
<section id="testimonials" class="testimonials section light-background">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>TESTIMONIALS</h2>
    <p>What Our Guests <span class="description-title">Say About Us</span></p>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="swiper init-swiper">
      <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": {
            "delay": 5000
          },
          "slidesPerView": "auto",
          "pagination": {
            "el": ".swiper-pagination",
            "type": "bullets",
            "clickable": true
          }
        }
      </script>
      <div class="swiper-wrapper">

        <div class="swiper-slide">
          <div class="testimonial-item">
            <div class="row gy-4 justify-content-center">
              <div class="col-lg-6">
                <div class="testimonial-content">
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    <span>The Ethiopian coffee here is simply outstanding! The aroma and taste are unmatched. It feels like sipping a cup of Ethiopia itself. Can't wait to come back!</span>
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                  <h3>Grum Bedaso</h3>
                  <h4>Coffee Enthusiast</h4>
                  <div class="stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 text-center">
                <img src="assets/img/testimonials/testimonials-1.jpg" class="img-fluid testimonial-img" alt="Emily Brown">
              </div>
            </div>
          </div>
        </div><!-- End testimonial item -->

        <div class="swiper-slide">
          <div class="testimonial-item">
            <div class="row gy-4 justify-content-center">
              <div class="col-lg-6">
                <div class="testimonial-content">
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    <span>Time Cafe is a hidden gem! The ambiance is warm and welcoming, and their Ethiopian coffee is out of this world. Perfect spot for a relaxing afternoon.</span>
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                  <h3>Abenet Bkele</h3>
                  <h4>custemer</h4>
                  <div class="stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 text-center">
                <img src="assets/img/testimonials/testimonials-2.jpg" class="img-fluid testimonial-img" alt="Michael Carter">
              </div>
            </div>
          </div>
        </div><!-- End testimonial item -->

        <div class="swiper-slide">
          <div class="testimonial-item">
            <div class="row gy-4 justify-content-center">
              <div class="col-lg-6">
                <div class="testimonial-content">
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    <span>The food is just as amazing as the coffee! The staff is incredibly friendly and attentive. Time Cafe has quickly become my go-to spot for great food and coffee.</span>
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                  <h3>Bezawit Niguse</h3>
                  <h4>Food Blogger</h4>
                  <div class="stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 text-center">
                <img src="assets/img/testimonials/testimonials-3.jpg" class="img-fluid testimonial-img" alt="Sarah Thompson">
              </div>
            </div>
          </div>
        </div><!-- End testimonial item -->

        <div class="swiper-slide">
          <div class="testimonial-item">
            <div class="row gy-4 justify-content-center">
              <div class="col-lg-6">
                <div class="testimonial-content">
                  <p>
                    <i class="bi bi-quote quote-icon-left"></i>
                    <span>I was blown away by the rich flavor of their Ethiopian coffee. It's a must-try for any coffee lover. The staff's hospitality made the experience even better!</span>
                    <i class="bi bi-quote quote-icon-right"></i>
                  </p>
                  <h3>Yohans Feseha</h3>
                  <h4>Entrepreneur</h4>
                  <div class="stars">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                  </div>
                </div>
              </div>
              <div class="col-lg-2 text-center">
                <img src="assets/img/testimonials/testimonials-4.jpg" class="img-fluid testimonial-img" alt="James Anderson">
              </div>
            </div>
          </div>
        </div><!-- End testimonial item -->

      </div>
      <div class="swiper-pagination"></div>
    </div>

  </div>

</section><!-- /Testimonials Section -->



    <!-- Book A Table Section -->
    <section id="book-a-table" class="book-a-table section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Book A Table</h2>
        <p><span>Book Your</span> <span class="description-title">Stay With Us<br></span></p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row g-0" data-aos="fade-up" data-aos-delay="100">

          <div class="col-lg-4 reservation-img" style="background-image: url(assets/img/reservation.jpg);"></div>

          <div class="col-lg-8 d-flex align-items-center reservation-form-bg" data-aos="fade-up" data-aos-delay="200">
            <form action="forms/book-a-table.php" method="post" role="form" class="php-email-form">
              <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required="">
                </div>
                <div class="col-lg-4 col-md-6">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
                </div>
                <div class="col-lg-4 col-md-6">
                  <input type="text" class="form-control" name="phone" id="phone" placeholder="Your Phone" required="">
                </div>
                <div class="col-lg-4 col-md-6">
                  <input type="date" name="date" class="form-control" id="date" placeholder="Date" required="">
                </div>
                <div class="col-lg-4 col-md-6">
                  <input type="time" class="form-control" name="time" id="time" placeholder="Time" required="">
                </div>
                <div class="col-lg-4 col-md-6">
                  <input type="number" class="form-control" name="people" id="people" placeholder="# of people" required="">
                </div>
              </div>

              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Message"></textarea>
              </div>

              <div class="text-center mt-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your booking request was sent. We will call back or send an Email to confirm your reservation. Thank you!</div>
                <button type="submit">Book a Table</button>
              </div>
            </form>
          </div><!-- End Reservation Form -->

        </div>

      </div>

    </section><!-- /Book A Table Section -->

    

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
        Designed by <a href="https://yitage.github.io/yiteefreelancer.github.io/">4th years of computer science students at infolink university</a>
      </div>
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
  <script src="assets/js/translations.js"></script>
  <script src="assets/js/main.js"></script>

</body>

</html>