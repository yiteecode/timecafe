(function() {
  "use strict";


  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });

  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);



  /**
   * scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  // header hide and apear
  

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

  /**
   * Language Selector Implementation
   */
  function initLanguageSelector() {
    const langToggle = document.querySelector('.lang-toggle');
    const langDropdown = document.querySelector('.lang-dropdown');
    const currentLangSpan = document.querySelector('.current-lang');
    
    // Set initial language from localStorage or default to English
    let currentLang = localStorage.getItem('language') || 'en';
    updateLanguage(currentLang);
    
    // Toggle dropdown
    langToggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      langDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', () => {
      langDropdown.classList.remove('show');
    });
    
    // Handle language selection
    document.querySelectorAll('.lang-dropdown li').forEach(item => {
      item.addEventListener('click', (e) => {
        const newLang = e.target.dataset.lang;
        updateLanguage(newLang);
        langDropdown.classList.remove('show');
      });
    });
    
    function updateLanguage(lang) {
      localStorage.setItem('language', lang);
      currentLangSpan.textContent = lang.toUpperCase();
      document.documentElement.lang = lang;
      
      // Update all translatable elements
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.dataset.i18n;
        if (translations[lang] && translations[lang][key]) {
          element.textContent = translations[lang][key];
        }
      });
    }
  }

  // Initialize language selector
  window.addEventListener('DOMContentLoaded', initLanguageSelector);

  /**
   * Navigation Active State Implementation
   */
  function initActiveMenu() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('#navmenu a');
    
    // Remove all active classes first
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.parentElement.classList.contains('active')) {
        link.parentElement.classList.remove('active');
      }
    });
    
    // Find and set the active link
    navLinks.forEach(link => {
      const linkPath = link.getAttribute('href');
      
      // Handle home page
      if (currentPath === '/' || currentPath === '/index.html') {
        if (linkPath === 'index.html' || linkPath === '/') {
          link.classList.add('active');
          setParentActive(link);
        }
      }
      // Handle other pages
      else if (linkPath && currentPath.includes(linkPath) && linkPath !== '#') {
        link.classList.add('active');
        setParentActive(link);
      }
    });
    
    // Helper function to set parent dropdown as active
    function setParentActive(element) {
      const parentDropdown = element.closest('.dropdown');
      if (parentDropdown) {
        const parentLink = parentDropdown.querySelector('a');
        if (parentLink) {
          parentLink.classList.add('active');
        }
        parentDropdown.classList.add('active');
      }
    }
  }

  // Initialize active menu state
  document.addEventListener('DOMContentLoaded', initActiveMenu);

})();

function handleHeader() {
  const header = document.querySelector('.header');
  let lastScroll = 0;
  let scrollDirection = 'up';
  
  window.addEventListener('scroll', () => {
    const currentScroll = window.scrollY;
    
    // Determine scroll direction
    if (currentScroll > lastScroll && scrollDirection !== 'down') {
      scrollDirection = 'down';
      if (currentScroll > 1) { // Only hide after scrolling down 100px
        header.classList.add('hide');
      }
    } else if (currentScroll < lastScroll && scrollDirection !== 'up') {
      scrollDirection = 'up';
      header.classList.remove('hide');
    }
    
    lastScroll = currentScroll;
  });
}

// Initialize header functionality
document.addEventListener('DOMContentLoaded', handleHeader);