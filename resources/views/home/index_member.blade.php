    @extends('layouts.app')
    @section('css')
    <style>
    .member-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    .member-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .membership-status {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .promo-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    .promo-card:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }
    .quick-action {
        transition: all 0.3s ease;
        border-radius: 12px;
        cursor: pointer;
    }
    .quick-action:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .stats-ring {
        position: relative;
        display: inline-block;
    }
    .progress-ring {
        transform: rotate(-90deg);
    }
    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        transform-origin: 50% 50%;
    }
    </style>
    @endsection
    @section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
        
        <!-- Welcome Header -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="card membership-status text-white">
                    <div class="card-body py-8">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="text-white fw-bold mb-3">
                                    <i class="fas fa-dumbbell me-3"></i>Selamat Datang Di Bugar Sehat, {{ $data['user']->name }}!
                                </h1>
                                <p class="text-white opacity-75 fs-5 mb-0">
                                    Mari jaga kesehatan dan capai target fitness Anda hari ini
                                </p>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="text-white">
                                    <div class="fs-2x fw-bold">{{ \Carbon\Carbon::now()->format('d') }}</div>
                                    <div class="opacity-75">{{ \Carbon\Carbon::now()->format('M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Status & Quick Actions -->
        <div class="row g-5 g-xl-6 mb-6">
            
            <!-- Membership Status -->
            <div class="col-12 col-lg-8">
                @if($data['activeMembership'])
                <div class="card member-card bg-light-success">
                    <div class="card-body p-6">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center">
                                <div class="symbol symbol-100px">
                                    <span class="symbol-label bg-success text-white fs-1 fw-bold">
                                        {{ substr($data['activeMembership']->package->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="fw-bold text-success mb-2">{{ $data['activeMembership']->package->name }} Membership</h3>
                                <div class="text-gray-700 mb-2">
                                    <i class="fas fa-calendar me-2"></i>
                                    Berlaku hingga: {{ \Carbon\Carbon::parse($data['activeMembership']->end_date)->format('d F Y') }}
                                </div>
                                <div class="text-gray-700 mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    Sisa: {{ $data['activeMembership']->getRemainingDays() }} hari
                                </div>
                                @if(isset($data['activeMembership']->remaining_visits) && $data['activeMembership']->remaining_visits !== null)
                                <div class="text-gray-700">
                                    <i class="fas fa-ticket-alt me-2"></i>
                                    Sisa kunjungan: {{ $data['activeMembership']->remaining_visits == 999 ? 'Unlimited' : $data['activeMembership']->remaining_visits }}
                                </div>
                                @endif
                            </div>
                            <div class="col-md-3 text-center">
                                @if($data['activeMembership']->getRemainingDays() <= 7)
                                    <a href="{{ route('packet_membership') }}" class="btn btn-warning btn-lg">
                                        <i class="fas fa-refresh me-2"></i>Perpanjang
                                    </a>
                                @else
                                    <span class="badge badge-success fs-6 p-3">Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card member-card bg-light-warning">
                    <div class="card-body text-center py-8">
                        <i class="fas fa-exclamation-triangle fs-3x text-warning mb-4"></i>
                        <h3 class="fw-bold text-warning mb-3">Tidak Ada Membership Aktif</h3>
                        <p class="text-gray-700 fs-5 mb-5">Anda belum memiliki membership aktif. Pilih paket membership untuk mulai berlatih di gym kami.</p>
                        <a href="{{ route('packet_membership') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-plus me-2"></i>Pilih Paket Membership
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="col-12 col-lg-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card quick-action bg-light-primary h-100" id="checkin-action">
                            <div class="card-body text-center py-6">
                                <i class="fas fa-qrcode fs-2x text-primary mb-3"></i>
                                <div class="fw-bold text-primary">Check-in</div>
                                <div class="text-gray-600 fs-7">QR Code</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card quick-action bg-light-info h-100" id="products-action">
                            <div class="card-body text-center py-6">
                                <i class="fas fa-shopping-cart fs-2x text-info mb-3"></i>
                                <div class="fw-bold text-info">Produk</div>
                                <div class="text-gray-600 fs-7">Belanja</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card quick-action bg-light-success h-100" id="services-action">
                            <div class="card-body text-center py-6">
                                <i class="fas fa-dumbbell fs-2x text-success mb-3"></i>
                                <div class="fw-bold text-success">Layanan</div>
                                <div class="text-gray-600 fs-7">Personal Training</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card quick-action bg-light-warning h-100" id="history-action">
                            <div class="card-body text-center py-6">
                                <i class="fas fa-history fs-2x text-warning mb-3"></i>
                                <div class="fw-bold text-warning">Riwayat</div>
                                <div class="text-gray-600 fs-7">Aktivitas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership Warning -->
        @if($data['membershipWarning'])
        <div class="row mb-6">
            <div class="col-12">
                <div class="alert alert-{{ $data['membershipWarning']['type'] == 'expired' ? 'danger' : 'warning' }} d-flex align-items-center p-5">
                    <i class="fas fa-{{ $data['membershipWarning']['type'] == 'expired' ? 'times-circle' : 'exclamation-triangle' }} fs-2x me-4"></i>
                    <div>
                        <h4 class="mb-1">{{ $data['membershipWarning']['type'] == 'expired' ? 'Membership Berakhir' : 'Peringatan Membership' }}</h4>
                        <div>{{ $data['membershipWarning']['message'] }}</div>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('packet_membership') }}" class="btn btn-{{ $data['membershipWarning']['type'] == 'expired' ? 'danger' : 'warning' }}">
                            {{ $data['membershipWarning']['type'] == 'expired' ? 'Beli Sekarang' : 'Perpanjang' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Stats & Charts -->
        <div class="row g-5 g-xl-6 mb-6">
            
            <!-- Check-in Stats -->
            <div class="col-6 col-lg-3">
                <div class="card member-card text-center">
                    <div class="card-body py-6">
                        <div class="stats-ring mb-3">
                            <svg class="progress-ring" width="80" height="80">
                                <circle class="progress-ring-circle" stroke="#e5e7eb" stroke-width="8" fill="transparent" r="32" cx="40" cy="40"/>
                                <circle class="progress-ring-circle" stroke="#3b82f6" stroke-width="8" fill="transparent" r="32" cx="40" cy="40" 
                                        stroke-dasharray="201" stroke-dashoffset="{{ 201 - (201 * min($data['checkinStats']['today'], 3) / 3) }}"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <div class="fs-2x fw-bold text-primary">{{ $data['checkinStats']['today'] }}</div>
                            </div>
                        </div>
                        <div class="fw-bold text-gray-800">Hari Ini</div>
                        <div class="text-gray-600 fs-7">Check-in</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card member-card text-center">
                    <div class="card-body py-6">
                        <div class="fs-2x fw-bold text-success mb-2">{{ $data['checkinStats']['this_week'] }}</div>
                        <div class="fw-bold text-gray-800">Minggu Ini</div>
                        <div class="text-gray-600 fs-7">Check-in</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card member-card text-center">
                    <div class="card-body py-6">
                        <div class="fs-2x fw-bold text-info mb-2">{{ $data['checkinStats']['this_month'] }}</div>
                        <div class="fw-bold text-gray-800">Bulan Ini</div>
                        <div class="text-gray-600 fs-7">Check-in</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="card member-card text-center">
                    <div class="card-body py-6">
                        <div class="fs-2x fw-bold text-warning mb-2">{{ $data['checkinStats']['total'] }}</div>
                        <div class="fw-bold text-gray-800">Total</div>
                        <div class="text-gray-600 fs-7">Check-in</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-5 g-xl-6">
            
            <!-- Promo & Offers -->
            <div class="col-12 col-lg-6">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-fire me-2 text-danger"></i>Promo & Penawaran Spesial
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['activePromos']->count() > 0)
                            @foreach($data['activePromos'] as $promo)
                            <div class="promo-card card bg-light-success mb-4">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50px me-4">
                                            <span class="symbol-label bg-success text-white">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold text-success">{{ $promo->name }}</div>
                                            <div class="text-gray-700 fs-7">{{ $promo->description ?? 'Penawaran terbatas' }}</div>
                                            @if(isset($promo->price))
                                            <div class="text-success fw-bold">Rp {{ number_format($promo->price, 0, ',', '.') }}</div>
                                            @endif
                                        </div>
                                        <button class="btn btn-sm btn-success">Ambil</button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-gift fs-3x text-gray-400 mb-4"></i>
                                <div class="text-gray-600">Tidak ada promo saat ini</div>
                                <div class="text-gray-500 fs-7">Pantau terus untuk penawaran menarik!</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Activity Chart -->
            <div class="col-12 col-lg-6">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Aktivitas Check-in Bulanan
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyCheckinsChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="col-12 col-lg-6">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-receipt me-2"></i>Transaksi Terbaru
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['recentTransactions']->count() > 0)
                            @foreach($data['recentTransactions'] as $transaction)
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        <i class="fas fa-shopping-bag"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $transaction->product->name ?? 'Produk' }}</div>
                                    <div class="text-muted fs-7">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y H:i') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                                    <span class="badge badge-light-success">{{ ucfirst($transaction->status) }}</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-receipt fs-3x text-gray-400 mb-4"></i>
                                <div class="text-gray-600">Belum ada transaksi</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Assigned Trainers -->
            <div class="col-12 col-lg-6">
                <div class="card h-lg-100">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie me-2"></i>Personal Trainer Saya
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['assignedTrainers']->count() > 0)
                            @foreach($data['assignedTrainers'] as $trainer)
                            <div class="d-flex align-items-center mb-5 p-4 border border-dashed border-gray-300 rounded">
                                <div class="symbol symbol-50px me-4">
                                    @if($trainer->avatar)
                                        <img src="{{ Storage::url($trainer->avatar) }}" alt="{{ $trainer->name }}">
                                    @else
                                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                                            {{ substr($trainer->name, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-gray-800 fs-6">{{ $trainer->name }}</div>
                                    <div class="text-muted fs-7">Spesialis: Fitness & Bodybuilding</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-light-primary">Aktif</span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-user-alt-slash fs-3x text-gray-400 mb-4"></i>
                                <div class="text-gray-600">Belum ada trainer terhubung</div>
                                <p class="text-gray-500 fs-7 px-10 mt-2">Book layanan Personal Trainer untuk mulai berlatih dengan bantuan ahli.</p>
                                <a href="{{ route('services.index') }}" class="btn btn-sm btn-light-primary mt-2">
                                    <i class="fas fa-search me-1"></i>Cari Trainer
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Services (Personal Training) -->
            <div class="col-12 col-lg-4">
                <div class="card h-lg-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 mb-1"><i class="fas fa-calendar-alt me-2 text-primary"></i>Personal Training</span>
                            <span class="text-muted fw-semibold fs-7">Sesi mendatang bersama trainer</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['upcomingServices']->count() > 0)
                            @foreach($data['upcomingServices'] as $service)
                            <div class="d-flex align-items-center mb-6">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-primary text-primary">
                                        <i class="fas fa-user-clock fs-2"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-gray-800 fs-6">{{ $service->service->name ?? 'Layanan' }}</div>
                                    <div class="text-muted fs-7">
                                        <i class="fas fa-user-tie me-1 fs-8"></i>{{ $service->trainer->name ?? 'Trainer' }}
                                    </div>
                                    <div class="text-primary fs-7 fw-bold">{{ \Carbon\Carbon::parse($service->scheduled_date)->format('d M, H:i') }}</div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <span class="text-muted fs-7">Tidak ada jadwal PT mendatang</span>
                                <br>
                                <a href="{{ route('services.index') }}" class="btn btn-sm btn-link pt-2">Book Trainer</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Classes (Group Classes) -->
            <div class="col-12 col-lg-4">
                <div class="card h-lg-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 mb-1"><i class="fas fa-users me-2 text-info"></i>Jadwal Kelas</span>
                            <span class="text-muted fw-semibold fs-7">Kelas grup yang Anda ikuti</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['upcomingClasses']->count() > 0)
                            @foreach($data['upcomingClasses'] as $booking)
                            <div class="d-flex align-items-center mb-6">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-info text-info">
                                        <i class="fas fa-chalkboard-teacher fs-2"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-gray-800 fs-6">{{ $booking->schedule->package->name ?? 'Kelas' }}</div>
                                    <div class="text-info fs-7 fw-bold">{{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('d M, H:i') }}</div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <span class="text-muted fs-7">Belum ada booking kelas</span>
                                <br>
                                <a href="{{ route('member.schedule.index') }}" class="btn btn-sm btn-link pt-2">Cari Kelas</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Progress -->
            <div class="col-12 col-lg-4">
                <div class="card h-lg-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 mb-1"><i class="fas fa-chart-line me-2 text-success"></i>Progress Terakhir</span>
                            <span class="text-muted fw-semibold fs-7">Kemajuan latihan Anda</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($data['recentProgress']->count() > 0)
                            @foreach($data['recentProgress'] as $prog)
                            <div class="d-flex align-items-center mb-6">
                                <div class="symbol symbol-45px me-4">
                                    <span class="symbol-label bg-light-success text-success">
                                        <i class="fas fa-walking fs-2"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-gray-800 fs-6">{{ Str::limit($prog->progress_note, 40) }}</div>
                                    <div class="text-muted fs-7">{{ $prog->date->format('d M Y') }} - {{ $prog->trainer->name }}</div>
                                </div>
                            </div>
                            @endforeach
                            <a href="{{ route('member.progress.index') }}" class="btn btn-sm btn-light-success w-100">Lihat Semua Progress</a>
                        @else
                            <div class="text-center py-5">
                                <span class="text-muted fs-7">Belum ada catatan progress</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
    @endsection

    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Chart Check-in Bulanan
    const monthlyCheckinsData = @json($data['monthlyCheckins'] ?? []);
    const checkinsCtx = document.getElementById('monthlyCheckinsChart');
    if (checkinsCtx && monthlyCheckinsData) {
    new Chart(checkinsCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: Object.keys(monthlyCheckinsData),
            datasets: [{
                label: 'Check-in',
                data: Object.values(monthlyCheckinsData),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    }

    // Quick Actions Click Handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Check-in QR Code
        document.getElementById('checkin-action').addEventListener('click', function() {
            @if($data['activeMembership'])
                window.location.href = "{{ route('checkin.generate-qr') }}";
            @else
                alert('Anda perlu membership aktif untuk check-in');
            @endif
        });

        // Produk
        document.getElementById('products-action').addEventListener('click', function() {
            window.location.href = "{{ route('products.index') }}";
        });

        // Layanan
        document.getElementById('services-action').addEventListener('click', function() {
            window.location.href = "{{ route('services.index') }}";
        });

        // Riwayat
        document.getElementById('history-action').addEventListener('click', function() {
            window.location.href = "{{ route('membership.history') }}";
        });
    });

    // Auto refresh membership status setiap 5 menit
    setInterval(function() {
        @if($data['activeMembership'] && $data['activeMembership']->getRemainingDays() <= 1)
            location.reload();
        @endif
    }, 300000);
    </script>
    @endsection
