<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head>
        <base href="{{ config('app.url') }}" />
        <title>{{ config('app.name', 'Laravel') }} {{ $config['title'] ? '[' . $config['title'] . ']' : '' }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<meta charset="utf-8" />
		<meta name="description" content="Sistem Informasi Gym dan Yoga" />
		<meta name="keywords" content="GYM, YOGA" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="SIM-PKP" />
		<meta property="og:url" content="{{config('app.url')}}" />
		<meta property="og:site_name" content="Sistem Informasi Gym dan Yoga" />
		<link rel="canonical" href="{{config('app.url')}}" />
		<link rel="shortcut icon" href="{{ asset('') }}/favicon.ico" />

		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->

		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('metronic') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{ asset('metronic') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
        <style>
            .image-bg{
                background-image: url({{asset('metronic')}}/assets/media/misc/auth-bg.png);
            }
            .w-xl-100px{
                width: 150px !important;
            }
            .font-h2{
                color: #fff;
                font-size: 2rem;
            }
            #showHide{
                cursor: pointer;
            }
			.pass-type{
				position: absolute;
				right: 0px;
				top: 0px;
				width: 40px;
				text-align: center;
				height: 100%;
				font-size: 24px;
			}
			.icon-pass{
				font-size: 16px !important;
			}
			.row-relative{
				position: relative;
			}
			.shadow-wall{
				position: relative;
			}
			.shadow-wall::after{
				content: "";
				width: 100%;
				height: 100%;
				background: rgba(0,0,0,0.2);
				position: absolute;
			}
			.layer-up{
				z-index: 2;
			}
			.rounded-100{
				border-radius: 100px;
			}
			.logo-in-login{
				margin-left: -30px;
				border: 4px solid #e4e6ef;
				border-radius: 50%;
				margin-bottom: 3px;
				height: 36px !important;
			}
        </style>
        @yield('css')
		<!--end::Global Stylesheets Bundle-->
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank app-blank">
        <!--begin::Theme mode setup on page load-->
		<script>
            var defaultThemeMode = "light";
            var themeMode; 
            if ( document.documentElement ) { 
                if ( document.documentElement.hasAttribute("data-theme-mode")) {
                     themeMode = document.documentElement.getAttribute("data-theme-mode");
                } else { 
                    if ( localStorage.getItem("data-theme") !== null ) { 
                        themeMode = localStorage.getItem("data-theme"); 
                    } else { 
                        themeMode = defaultThemeMode; 
                    } 
                } 
                if (themeMode === "system") { 
                    themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; 
                } 
                document.documentElement.setAttribute("data-theme", themeMode); 
            }
        </script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-2 order-lg-1 image-bg shadow-wall" >
                    <!--begin::Content-->
					<div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
						<!--begin::Logo-->
						<a href="{{route('home')}}" class="mb-0 mb-lg-12 font-h2 fs-2qx text-gray-800 fw-bolder text-center layer-up">
                            <!-- BUGAR SEHAT </br> -->
							<img alt="Logo" src="{{ asset('metronic/assets/media/misc/auth-screens.png') }}" class="h-350px h-lg-380px rounded-100" />
							
						</a>
						<!--end::Logo-->
						<!--begin::Text-->
						<div class="d-none d-lg-block text-white fs-base text-center"></div>
						<!--end::Text-->
					</div>
					<!--end::Content-->
                </div>
                <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-1 order-lg-2">
                    <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                        @yield('content')
                    </div>
                    <div class="d-flex flex-center flex-wrap px-5">
                        <!--begin::Links-->
						<!-- target="_blank" -->
						{{--<div class="d-flex fw-semibold text-primary fs-base">
							<a href="javascript:;" class="px-5">Terms</a>
							<a href="javascript:;" class="px-5">Info</a>
							<a href="javascript:;" class="px-5">Contact Us</a>
						</div>--}}
						<!--end::Links-->
                    </div>
                </div>
            </div>
        </div>
        <!--end::Root-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('metronic') }}/assets/plugins/global/plugins.bundle.js"></script>
		<script src="{{ asset('metronic') }}/assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Custom Javascript(used for this page only)-->
		<!-- <script src="assets/js/custom/authentication/sign-in/general.js"></script> -->
		<script src="{{ asset('plugins') }}/jquery.form.min.js"></script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
        @yield('script')
		<script>
			let table;
			$(function() {
				'use strict';
				let defaultError = "Proses tidak berhasil.";
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$(document).on('click', '#showHide', function() {
					let e=$(this),
						target=e.parent().find("[name='password']");
					if(target.attr('type')=='password'){
						e.html('<i class="bi bi-eye-slash text-primary icon-pass">');
						target.attr('type','text');
					}else{
						e.html('</i><i class="bi bi-eye text-primary icon-pass"></i>');
						target.attr('type','password');
					}
				});
			});
		</script>
    </body>
	<!--end::Body-->
</html>