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
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

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
    .blue-text {
      color: #00559e;
    }

    .yellow-text {
      color: #fe851a;
    }

    .text-center {
      text-align: center;
    }

    .text-left {
      text-align: left;
    }

    .header .logo img {
      max-height: 58px !important;
      border: 3px solid #00559e;
      border-radius: 50%;
    }

    .bg-card>a {
      color: white !important;
    }

    .bg-card {
      background: #00559e;
      color: white;
      border-radius: 20px 0px;
      padding: 4px;
    }

    .description tr {
      vertical-align: initial;
    }

    /* Modern Membership Design */
    #hero-membership {
      padding: 80px 0;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .membership-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 24px;
      padding: 40px 30px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .membership-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 20px 40px rgba(0, 85, 158, 0.15);
      border-color: #00559e;
    }

    .membership-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, #00559e, #fe851a);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .membership-card:hover::before {
      opacity: 1;
    }

    .membership-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      display: inline-block;
      transition: transform 0.3s ease;
    }

    .membership-card:hover .membership-icon {
      transform: scale(1.2) rotate(5deg);
    }

    .membership-title {
      font-size: 1.5rem;
      font-weight: 800;
      color: #00559e;
      margin-bottom: 15px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .membership-price {
      font-size: 2.5rem;
      font-weight: 900;
      color: #333;
      margin-bottom: 25px;
      display: flex;
      align-items: baseline;
      justify-content: center;
    }

    .membership-price span {
      font-size: 1rem;
      color: #777;
      margin-left: 5px;
      font-weight: 500;
    }

    .membership-features {
      list-style: none;
      padding: 0;
      margin: 0 0 30px 0;
      text-align: left;
      flex-grow: 1;
    }

    .membership-features li {
      padding: 12px 0;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      color: #555;
      display: flex;
      align-items: center;
      font-weight: 500;
    }

    .membership-features li:last-child {
      border-bottom: none;
    }

    .membership-features li i {
      color: #00559e;
      margin-right: 12px;
      font-size: 1.2rem;
    }

    .membership-badge {
      position: absolute;
      top: 20px;
      right: -35px;
      background: #fe851a;
      color: white;
      padding: 5px 40px;
      transform: rotate(45deg);
      font-size: 0.8rem;
      font-weight: 700;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .btn-membership {
      background: #00559e;
      color: white;
      border-radius: 50px;
      padding: 15px 30px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: none;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0, 85, 158, 0.3);
    }

    .btn-membership:hover {
      background: #fe851a;
      color: white;
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(254, 133, 26, 0.4);
    }

    .section-title span {
      font-size: 1.1rem;
      color: #666;
      max-width: 600px;
      margin: 10px auto;
      display: block;
    }
  </style>
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="{{ Route::has('landing_preview') ? route('landing_preview') : url('/') }}"
        class="logo d-flex align-items-center me-auto">
        <img src="{{ asset('metronic/assets/media/misc/logo-only.png') }}" alt="">
        <h1 class="sitename"><b><span class="blue-text">Bugar</span><span class="yellow-text">Sehat</span></b></h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero-home" class="active goto-hero">Beranda</a></li>
          <li><a href="#hero-membership" class="goto-hero">Membership</a></li>
          <li><a href="#hero-product-services" class="goto-hero">Produk & Layanan</a></li>
          <li><a href="#footer" class="goto-hero">Kontak</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="{{route('login')}}">Masuk</a>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero-home" class="hero section dark-background">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        @php
          $class = "active";
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
            $class = "";
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
        <p>💪 Pilihan Paket Membership Gym & Yoga<br></p>
        <span>Pilih paket yang sesuai dengan gaya hidup dan tujuan kebugaran Anda</span>
      </div><!-- End Section Title -->
      <div class="container">

        <div class="row gy-4">
          @php
            $aos = 100;
          @endphp
          @foreach($data['membership_packet'] as $membership_packet)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{$aos}}">
              <div class="membership-card">
                @if(str_contains(strtolower($membership_packet['name']), 'premium') || str_contains(strtolower($membership_packet['name']), 'gold'))
                  <div class="membership-badge">Popular</div>
                @endif

                <div class="membership-icon">
                  @if(str_contains(strtolower($membership_packet['name']), 'basic'))
                    <i class="bi bi-lightning"></i>
                  @elseif(str_contains(strtolower($membership_packet['name']), 'silver'))
                    <i class="bi bi-shield-shaded"></i>
                  @elseif(str_contains(strtolower($membership_packet['name']), 'gold'))
                    <i class="bi bi-trophy"></i>
                  @else
                    <i class="bi bi-gem"></i>
                  @endif
                </div>

                <h3 class="membership-title">{{$membership_packet['name']}}</h3>

                <div class="membership-price">
                  Rp {{ (int)$membership_packet['price'] }}K
                  <span>/ {{ $membership_packet['duration_days'] }} hari</span>
                </div>

                <ul class="membership-features">
                  <li><i class="bi bi-check2-circle"></i> {{ $membership_packet['max_visits'] }} Kunjungan Maksimal</li>
                  <li><i class="bi bi-check2-circle"></i> Durasi {{ $membership_packet['duration_days'] }} Hari</li>
                  <li><i class="bi bi-check2-circle"></i> {{ $membership_packet['usage'] }}</li>
                  @if($membership_packet['description'])
                    <li><i class="bi bi-info-circle"></i> {{ Str::limit($membership_packet['description'], 50) }}</li>
                  @endif
                </ul>

                <a href="{{ url('/packet-membership') }}" class="btn btn-membership w-100">Pilih Paket</a>
              </div>
            </div><!-- End Membership Card -->
            @php
              $aos = $aos + 100;
            @endphp
          @endforeach

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Products & Services Section -->
    <section id="hero-product-services" class="services section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <p>Produk & Layanan Kami<br></p>
        <span>Temukan produk premium dan layanan profesional kami untuk mendukung kebugaran Anda.</span>
      </div><!-- End Section Title -->

      <div class="container">

        <!-- Products Subheading -->
        <h3 class="mb-4 text-center" data-aos="fade-up">Produk Unggulan</h3>
        <div class="row gy-4 mb-5">
          @foreach($data['products'] as $product)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="card h-100 shadow-sm border-0">
                @if($product->image)
                  <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}"
                    style="height: 200px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='{{ asset('metronic/assets/media/stock/600x400/img-26.jpg') }}';">
                @else
                  <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                    style="height: 200px;">
                    <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                  </div>
                @endif
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bold">
                    <a href="{{ route('login') }}"
                      class="text-dark text-decoration-none stretched-link">{{ $product->name }}</a>
                  </h5>
                  <p class="card-text text-muted mb-4">{{ Str::limit($product->description, 100) }}</p>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-bold" style="color: var(--accent-color);">Rp
                      {{ number_format($product->getCurrentPrice(), 0, ',', '.') }}</span>
                    <span class="badge bg-light text-dark border">{{ $product->category ?? 'Produk' }}</span>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Services Subheading -->
        <h3 class="mb-4 text-center mt-5" data-aos="fade-up">Layanan Profesional</h3>
        <div class="row gy-4">
          @foreach($data['services'] as $service)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
              <div class="card h-100 shadow-sm border-0">
                @if($service->image)
                  <img src="{{ asset('storage/' . $service->image) }}" class="card-img-top" alt="{{ $service->name }}"
                    style="height: 200px; object-fit: cover;"
                    onerror="this.onerror=null;this.src='{{ asset('metronic/assets/media/stock/600x400/img-1.jpg') }}';">
                @else
                  <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                    style="height: 200px;">
                    <i class="bi bi-person-badge text-muted" style="font-size: 4rem;"></i>
                  </div>
                @endif
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title fw-bold">
                    <a href="{{ route('login') }}"
                      class="text-dark text-decoration-none stretched-link">{{ $service->name }}</a>
                  </h5>
                  <p class="card-text text-muted mb-4">{{ Str::limit($service->description, 100) }}</p>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-bold" style="color: var(--accent-color);">Rp
                      {{ number_format($service->price, 0, ',', '.') }}</span>
                    <span class="text-muted small">/ sesi</span>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

      </div>

    </section><!-- /Products & Services Section -->

  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-6 col-md-6 footer-about">
          <a href="{{route('landing_preview')}}" class="logo d-flex align-items-center">
            <span class="sitename">{{ config('app.name', 'Laravel') }}</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Hubungi kami untuk informasi lebih lanjut.</p>
            <p>Kami siap membantu perjalanan kebugaran Anda.</p>
            <p class="mt-3"><strong>Telepon:</strong> <span>+62 7777 77777 77</span></p>
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
          <h4>Buletin Kami</h4>
          <p>Berlangganan buletin kami untuk menerima berita terbaru tentang produk dan layanan khusus kami!</p>
          <form action="{{route('landing_preview')}}" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Langganan"></div>
            <div class="loading">Memuat</div>
            <div class="error-message"></div>
            <div class="sent-message">Permintaan berlangganan Anda telah terkirim. Terima kasih!</div>
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
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a
          href="https://themewagon.com/">ThemeWagon</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

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
    });

    const myCarouselElement = document.querySelector('#hero-carousel');
    if (myCarouselElement) {
      const carousel = new bootstrap.Carousel(myCarouselElement, {
        interval: 5000,
        ride: 'carousel',
        pause: false
      });
      carousel.cycle();
    }
  </script>

</body>

</html>