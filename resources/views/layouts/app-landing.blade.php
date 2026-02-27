<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  
  <title>
    {{ config('app.name', 'Laravel') }}
    {{ config('app.title') ? ' [' . config('app.title') . ']' : '' }}
  </title>
  
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('favicon.ico') }}" rel="icon">
  <link href="{{ asset('favicon.ico') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('sailor/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('sailor/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('sailor/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('sailor/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('sailor/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('sailor/assets/css/main.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Sailor
  * Template URL: https://bootstrapmade.com/sailor-free-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style>
    .blue-text{
        color: #00559e;
    }
    .yellow-text{
        color: #fe851a;
    }
    .text-center{
      text-align: center;
    }
    .text-left{
      text-align: left;
    }
    .header .logo img {
      max-height: 58px !important;
      border: 3px solid #00559e;
      border-radius: 50%;
    }
    .bg-card>a{
      color: white !important;
    }
    .bg-card{
      background: #00559e;
      color: white;
      border-radius: 20px 0px;
      padding: 4px;
    }
    .description{
      margin-bottom: 40px;
    }
    .description tr{
      vertical-align: initial;
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

    <a href="{{ Route::has('landing_preview') ? route('landing_preview') : url('/') }}" class="logo d-flex align-items-center me-auto">
    <img src="{{ asset('metronic/assets/media/misc/logo-only.png') }}" alt="">
    <h1 class="sitename"><b><span class="blue-text">Bugar</span><span class="yellow-text">Sehat</span></b></h1>
</a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero-home" class="active goto-hero">Home</a></li>
          <li><a href="#hero-membership" class="goto-hero">Membership</a></li>
          <li><a href="#hero-product-services" class="goto-hero">Produk & Layanan</a></li>
          <li><a href="#hero-contact" class="goto-hero">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{route('login')}}">Sign In</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero-home" class="hero section dark-background">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        @php
          $class="active";
        @endphp
        @foreach($data['slide'] as $slides)
          <div class="carousel-item {{$class}}">
            <img src="{{ $slides['pathfile'] }}" alt="">
            <div class="carousel-container">
              <h2>{{ $slides['title'] }}<br></h2>
              <p>{{ $slides['desc'] }}</p>
            </div>
          </div><!-- End Carousel Item -->
          @php
            $class="";
          @endphp
        @endforeach
        
        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>

        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>

        <ol class="carousel-indicators"></ol>

      </div>

    </section><!-- /Hero Section -->
    
    <section id="hero-membership" class="services section text-center">
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <p>💪 Flexible Gym & Yoga Membership Plans<br></p>
        <span>Choose the plan that fits your lifestyle and fitness goals</span>
      </div><!-- End Section Title -->
      <div class="container">

        <div class="row gy-4">
          @php
            $aos=100;
          @endphp
          @foreach($data['membership_packet'] as $membership_packet)
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{$aos}}">
              <div class="service-item d-flex position-relative h-100 p-3">
                <!-- <i class="bi bi-briefcase icon flex-shrink-0"></i> -->
                <div>
                  <h4 class="title mb-2"><a href="{{route('login')}}" class="stretched-link">👍 {{$membership_packet['name']}} Plan</a></h4>
                  <div class="mb-2 bg-card">{{$membership_packet['name_label']}}</div>
                  <div class="description">
                    <table class="text-left">
                      <tbody>
                        <tr>
                          <td style="width:100px">💰 Price</td>
                          <td>:</td>
                          <td>Rp. {{$membership_packet['price']}},00</td>
                        </tr>
                        <tr>
                          <td>⏱️ Duration</td>
                          <td>:</td>
                          <td>{{$membership_packet['duration_days']}} Days</td>
                        </tr>
                        <tr>
                          <td>🚪 Max Visits</td>
                          <td>:</td>
                          <td>{{$membership_packet['max_visits']}}</td>
                        </tr>
                        <tr>
                          <td>📝 Desc</td>
                          <td>:</td>
                          <td>{{$membership_packet['description']}}</td>
                        </tr>
                        <tr>
                          <td>✅ Ideal for</td>
                          <td>:</td>
                          <td>{{$membership_packet['usage']}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                   <!-- <a href="{{route('login')}}" class="read-more btn"><span>Read More</span><i class="bi bi-arrow-right"></i></a> -->
                </div>
              </div>
            </div><!-- End Service Item -->
            @php
              $aos=$aos+100;
            @endphp
          @endforeach
          
        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- About Section -->
    <section id="hero-product-services" class="about section text-center">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <p>Our Products & Services<br></p>
        <span>Everything you need for your fitness journey</span>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore
              magna aliqua.
            </p>
            <ul>
              <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo consequat.</span></li>
              <li><i class="bi bi-check2-circle"></i> <span>Duis aute irure dolor in reprehenderit in voluptate velit.</span></li>
              <li><i class="bi bi-check2-circle"></i> <span>Ullamco laboris nisi ut aliquip ex ea commodo</span></li>
            </ul>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <p>Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. </p>
            <a href="about.html" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->
    
    <!-- Services Section -->
    <section id="hero-contact" class="services section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-briefcase icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Lorem Ipsum</a></h4>
                <p class="description">Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-card-checklist icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Dolor Sitema</a></h4>
                <p class="description">Minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat tarad limino ata</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-bar-chart icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Sed ut perspiciatis</a></h4>
                <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-binoculars icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Magni Dolores</a></h4>
                <p class="description">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-brightness-high icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Nemo Enim</a></h4>
                <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque</p>
              </div>
            </div>
          </div><!-- End Service Item -->

          <div class="col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="service-item d-flex position-relative h-100">
              <i class="bi bi-calendar4-week icon flex-shrink-0"></i>
              <div>
                <h4 class="title"><a href="#" class="stretched-link">Eiusmod Tempor</a></h4>
                <p class="description">Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi</p>
              </div>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-6 col-md-6 footer-about">
          <a href="{{route('landing_preview')}}" class="logo d-flex align-items-center">
            <span class="sitename">{{ config('app.name', 'Laravel') }}</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Lorem ipsum dolor sit.</p>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing.</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+62 7777 77777 77</span></p>
            <p><strong>Email:</strong> <span>bugarsehat@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href="javascript:;"><i class="bi bi-twitter-x"></i></a>
            <a href="javascript:;"><i class="bi bi-facebook"></i></a>
            <a href="javascript:;"><i class="bi bi-instagram"></i></a>
            <a href="javascript:;"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-6 col-md-12 footer-newsletter">
          <h4>Our Newsletter</h4>
          <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form action="{{route('landing_preview')}}" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Sailor</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href=“https://themewagon.com>ThemeWagon
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('sailor/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/waypoints/noframework.waypoints.js') }}"></script>
  <script src="{{ asset('sailor/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('sailor/assets/js/main.js') }}"></script>
  <script>
    document.querySelectorAll('.goto-hero').forEach(item => {
      item.addEventListener('click', e => {
        e.preventDefault();
        const href = e.currentTarget.getAttribute('href');
        document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
      })
    })
  </script>

</body>

</html>