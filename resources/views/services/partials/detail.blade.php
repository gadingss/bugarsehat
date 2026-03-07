<div class="row">
    <div class="col-md-5">
        <div class="symbol symbol-100 symbol-lg-160 symbol-fixed position-relative mb-5 w-100">
            <img src="{{ $service->image ?? asset('metronic/assets/media/stock/600x400/img-2.jpg') }}" alt="image"
                class="w-100 rounded shadow-sm" style="height: 200px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('metronic/assets/media/stock/600x400/img-2.jpg') }}';" />
        </div>
    </div>
    <div class="col-md-7">
        <div class="mb-5">
            <h3 class="text-dark fw-bold mb-1">{{ $service->name }}</h3>
            <span class="badge badge-light-primary fw-bold">{{ $service->category }}</span>
        </div>
        <div class="fs-6 text-gray-600 mb-5">
            {{ $service->description }}
        </div>
        <div class="d-flex flex-stack flex-wrap mb-5">
            <div class="me-2">
                <div class="fs-2 fw-bold text-gray-800">
                    @if($service->price == 0)
                        <span class="text-success">GRATIS</span>
                    @else
                        Rp {{ number_format($service->price, 0, ',', '.') }}
                    @endif
                </div>
                <div class="fs-7 text-gray-400">Harga Layanan</div>
            </div>
            @if($service->duration_minutes)
                <div class="me-2 text-end">
                    <div class="fs-2 fw-bold text-gray-800">{{ $service->getFormattedDuration() }}</div>
                    <div class="fs-7 text-gray-400">Estimasi Durasi</div>
                </div>
            @endif
        </div>
    </div>
</div>

    <div class="separator separator-dashed my-5"></div>
    <div class="mb-5">
        <div class="d-flex flex-stack mb-3">
            <div>
                <h3 class="fw-bold text-dark mb-1">Daftar Sesi Latihan</h3>
                <div class="text-muted fs-7">Rencana topik pada setiap pertemuan layanan ini.</div>
            </div>
            @if(Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner'))
            <button type="button" class="btn btn-sm btn-light-primary" onclick="addSessionTemplate({{ $service->id }})">
                <i class="fas fa-plus me-1"></i> Tambah Sesi
            </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_table_templates">
                <thead>
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px text-center">Sesi</th>
                        <th class="min-w-150px">Topik Latihan / Rencana Materi</th>
                        @if(Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner'))
                        <th class="text-end min-w-70px">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-600">
                    @forelse($service->sessionTemplates as $template)
                        <tr>
                            <td class="text-center">
                                <span class="badge badge-outline badge-secondary">{{ $template->session_number }}</span>
                            </td>
                            <td>
                                @if(Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner'))
                                    <input type="text" class="form-control form-control-sm form-control-solid"
                                        value="{{ $template->topic }}"
                                        onchange="updateSessionTemplate({{ $template->id }}, this.value)"
                                        placeholder="Contoh: Pemanasan & Teknik Dasar">
                                @else
                                    <span class="text-dark fw-semibold d-block fs-6">{{ $template->topic ?: 'Menunggu topik dari Staff' }}</span>
                                @endif
                            </td>
                            @if(Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner'))
                            <td class="text-end">
                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                    onclick="deleteSessionTemplate({{ $template->id }}, this, {{ $service->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr id="empty-template-row">
                            <td colspan="{{ Auth::user()->hasRole('User:Staff') || Auth::user()->hasRole('User:Owner') ? '3' : '2' }}" class="text-center text-muted py-10">
                                <i class="fas fa-tasks fs-3x text-gray-200 mb-3 d-block"></i>
                                Belum ada rincian sesi untuk layanan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>