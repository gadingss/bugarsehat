<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" 
        class="app-sidebar-wrapper hover-scroll-overlay-y my-5" 
        data-kt-scroll="true" 
        data-kt-scroll-activate="true" 
        data-kt-scroll-height="auto" 
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" 
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" 
        data-kt-scroll-offset="10px" 
        data-kt-scroll-save-state="true">

        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3" 
            id="kt_app_sidebar_menu" 
            data-kt-menu="true" 
            data-kt-menu-expand="false">

            @if(isset($config['menu']) && is_array($config['menu']))
                @foreach($config['menu'] as $item)
                    @if($item['type'] == 'menu')
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{ $item['class'] ?? '' }}" href="{{ $item['url'] }}">
                                <span class="menu-icon">
                                    {!! $item['icon'] !!}
                                </span>
                                <span class="menu-title">{{ $item['name'] }}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->

                    @elseif($item['type'] == 'label')
                        <!--begin:Menu label-->
                        <div class="menu-item pt-5">
                            <div class="menu-content">
                                <span class="menu-heading fw-bold text-uppercase fs-7">{{ $item['name'] }}</span>
                            </div>
                        </div>
                        <!--end:Menu label-->

                    @elseif($item['type'] == 'submenu')
                        @php
                            $urlSub = collect($item['sub'])->pluck('url')->toArray();
                        @endphp

                        <!--begin:Menu submenu-->
                        <div data-kt-menu-trigger="click" 
                             class="menu-item menu-accordion {{ in_array(url()->current(), $urlSub) ? 'show' : '' }}">
                            <span class="menu-link">
                                <span class="menu-icon">
                                    {!! $item['icon'] !!}
                                </span>
                                <span class="menu-title">{{ $item['name'] }}</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion">
                                @foreach($item['sub'] as $itemsub)
                                    <div class="menu-item">
                                        <a class="menu-link {{ $itemsub['class'] ?? '' }}" href="{{ $itemsub['url'] }}">
                                            <span class="menu-bullet">
                                                <span class="bullet bullet-dot"></span>
                                            </span>
                                            <span class="menu-title">{{ $itemsub['name'] }}</span>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!--end:Menu submenu-->
                    @endif
                @endforeach
            @endif

        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>

<!--begin::Footer-->
<div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
    @role('User:Member')
    <a href="{{ route('checkin.generate-qr') }}" 
        class="btn btn-flex flex-center btn-custom btn-primary overflow-hidden text-nowrap px-0 h-40px w-100" 
        data-bs-toggle="tooltip" 
        data-bs-trigger="hover"
        data-bs-dismiss-="click" 
        title="Show My QR Code">
        <span class="btn-label">QR Code</span>
        <span class="svg-icon svg-icon-2 ms-2">
            <!-- ICON -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd"
                      d="M2 4.63158C2 3.1782 3.1782 2 4.63158 2H13.47C14.0155 2 14.278 2.66919 13.8778 3.04006L12.4556 4.35821C11.9009 4.87228 11.1726 5.15789 10.4163 5.15789H7.1579C6.05333 5.15789 5.15789 6.05333 5.15789 7.1579V16.8421C5.15789 17.9467 6.05333 18.8421 7.1579 18.8421H16.8421C17.9467 18.8421 18.8421 17.9467 18.8421 16.8421V13.7518C18.8421 12.927 19.1817 12.1387 19.7809 11.572L20.9878 10.4308C21.3703 10.0691 22 10.3403 22 10.8668V19.3684C22 20.8218 20.8218 22 19.3684 22H4.63158C3.1782 22 2 20.8218 2 19.3684V4.63158Z"
                      fill="currentColor"/>
                <path d="M10.9256 11.1882C10.5351 10.7977 10.5351 10.1645 10.9256 9.77397L18.0669 2.6327C18.8479 1.85165 20.1143 1.85165 20.8953 2.6327L21.3665 3.10391C22.1476 3.88496 22.1476 5.15129 21.3665 5.93234L14.2252 13.0736C13.8347 13.4641 13.2016 13.4641 12.811 13.0736L10.9256 11.1882Z"
                      fill="currentColor"/>
                <path d="M8.82343 12.0064L8.08852 14.3348C7.8655 15.0414 8.46151 15.7366 9.19388 15.6242L11.8974 15.2092C12.4642 15.1222 12.6916 14.4278 12.2861 14.0223L9.98595 11.7221C9.61452 11.3507 8.98154 11.5055 8.82343 12.0064Z"
                      fill="currentColor"/>
            </svg>
        </span>
    </a>
    @endrole
    
    @role('User:Staff')
    <a href="{{ route('checkin.qr-scan') }}" 
        class="btn btn-flex flex-center btn-custom btn-success overflow-hidden text-nowrap px-0 h-40px w-100" 
        data-bs-toggle="tooltip" 
        data-bs-trigger="hover"
        data-bs-dismiss-="click" 
        title="Scan Member QR Code">
        <span class="btn-label">QR Code Scan</span>
        <span class="svg-icon svg-icon-2 ms-2">
            <!-- ICON -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" d="M21 2H3C2.4 2 2 2.4 2 3V21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21V3C22 2.4 21.6 2 21 2Z" fill="currentColor"/>
                <path d="M10 6H14V18H10V6Z" fill="currentColor"/>
                <path d="M6 10H10V14H6V10Z" fill="currentColor"/>
                <path d="M14 10H18V14H14V10Z" fill="currentColor"/>
            </svg>
        </span>
    </a>
    @endrole
    
    @role('User:Owner')
    <a href="{{ route('checkin.history') }}" 
        class="btn btn-flex flex-center btn-custom btn-info overflow-hidden text-nowrap px-0 h-40px w-100" 
        data-bs-toggle="tooltip" 
        data-bs-trigger="hover"
        data-bs-dismiss-="click" 
        title="View Check-in History">
        <span class="btn-label">Code Qr History</span>
        <span class="svg-icon svg-icon-2 ms-2">
            <!-- ICON -->
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.3" d="M3 13H21V11H3V13ZM3 17H21V15H3V17ZM3 9H21V7H3V9ZM3 5H21V3H3V5Z" fill="currentColor"/>
                <path d="M21 21H3C2.4 21 2 20.6 2 20V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V20C22 20.6 21.6 21 21 21Z" fill="currentColor"/>
            </svg>
        </span>
    </a>
    @endrole
</div>
<!--end::Footer-->
