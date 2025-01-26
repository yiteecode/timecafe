<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Time cafe</title>
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
    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p><span>Need Help?</span> <span class="description-title">Contact Us</span></p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="mb-5">
          <iframe style="width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m12!1m8!1m3!1d31677.34373324661!2d38.4777096!3d7.0482475!3m2!1i1024!2i768!4f13.1!2m1!1stime%20cafe%20hawassa%20google%20map%20adresse%20%3Fq!5e0!3m2!1sen!2set!4v1736834458341!5m2!1sen!2set" frameborder="0" allowfullscreen=""></iframe>
        </div><!-- End Google Maps -->

        <div class="row gy-4">

          <div class="col-md-6">
            <div class="info-item d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
              <i class="icon bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Address</h3>
                <p>Piyassa, Hawassa, Ethiopia</p>
              </div>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item d-flex align-items-center" data-aos="fade-up" data-aos-delay="300">
              <i class="icon bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>+251 559 955 24</p>
              </div>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item d-flex align-items-center" data-aos="fade-up" data-aos-delay="400">
              <i class="icon bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email Us</h3>
                <p>info@timecafe.com</p>
              </div>
            </div>
          </div><!-- End Info Item -->

          <div class="col-md-6">
            <div class="info-item d-flex align-items-center" data-aos="fade-up" data-aos-delay="500">
              <i class="icon bi bi-clock flex-shrink-0"></i>
              <div>
                <h3>Opening Hours<br></h3>
                <p><strong>Mon-Sat:</strong> 11AM - 23PM; <strong>Sunday:</strong> Open</p>
              </div>
            </div>
          </div><!-- End Info Item -->

        </div>

        <form id="contactForm" action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="600">
  <div class="row gy-4">
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" placeholder="Your Name" required>
    </div>
    <div class="col-md-6">
      <input type="email" class="form-control" name="email" placeholder="Your Email" required>
    </div>
    <div class="col-md-12">
      <input type="text" class="form-control" name="subject" placeholder="Subject" required>
    </div>
    <div class="col-md-12">
      <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
    </div>
    <div class="col-md-12 text-center">
      <button type="submit">Send Message</button>
    </div>
  </div>
</form>

<!-- Add a popup card -->
<div id="responseMessage" class="popup-card" style="display: none;">
  <div class="popup-content">
    <span class="close">&times;</span>
    <p id="popupText"></p>
  </div>
</div>

    </section><!-- /Contact Section -->
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
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  
    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>
<!-- The following js code is for popup display for contact page -->
<script>
  const form = document.getElementById('contactForm');
const responseMessage = document.getElementById('responseMessage');
const popupText = document.getElementById('popupText');
const closeBtn = document.querySelector('.popup-card .close');

form.addEventListener('submit', async (e) => {
  e.preventDefault(); // Prevent default form submission

  const formData = new FormData(form);

  try {
    const response = await fetch('forms/contact.php', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();
    popupText.textContent = result.message; // Update the popup message
    responseMessage.style.display = 'block'; // Show the popup

    if (result.status === 'success') {
      form.reset(); // Clear the form
    }
  } catch (error) {
    popupText.textContent = 'An unexpected error occurred. Please try again.';
    responseMessage.style.display = 'block'; // Show the popup on error
  }
});

// Close the popup when the close button is clicked
closeBtn.addEventListener('click', () => {
  responseMessage.style.display = 'none';
});

// Close the popup when clicking outside the popup
window.addEventListener('click', (e) => {
  if (e.target === responseMessage) {
    responseMessage.style.display = 'none';
  }
});

</script>

  
</body>
    </html>