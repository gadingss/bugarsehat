<div class="row">
    <div class="col-md-5">
        <div class="symbol symbol-100 symbol-lg-160 symbol-fixed position-relative mb-3 w-100">
            @if($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->name }}"
                    class="w-100 rounded shadow-sm" style="height: 220px; object-fit: cover;"
                    onerror="this.onerror=null; this.src='{{ asset('metronic/assets/media/stock/600x400/img-2.jpg') }}';" />
            @else
                <div class="w-100 rounded bg-light-primary d-flex align-items-center justify-content-center" style="height: 220px;">
                    <div class="text-center text-gray-400">
                        <i class="ki-duotone ki-picture fs-3x mb-2"><span class="path1"></span><span class="path2"></span></i>
                        <p class="mb-0 fs-7">Belum ada gambar</p>
                    </div>
                </div>
            @endif
        </div>

        @if(Auth::user()->hasRole('User:Owner') || Auth::user()->hasRole('User:Staff'))
        <div class="card border-dashed border-primary bg-light-primary mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="ki-duotone ki-picture fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                    {{ $service->image ? 'Ganti Gambar Layanan' : 'Upload Gambar Layanan' }}
                </h6>
                <form action="{{ route('services.update-image', $service->id) }}" method="POST" enctype="multipart/form-data" id="service-image-form">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="image" accept="image/*" class="form-control"
                            onchange="previewServiceImage(this)">
                        <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Maks 3MB.</small>
                    </div>
                    <div id="service-image-preview-new" class="mb-3 d-none">
                        <p class="text-muted mb-1 fw-semibold">Preview:</p>
                        <img src="" id="service-new-img" class="img-fluid rounded border" style="max-height: 150px; object-fit: cover;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="ki-duotone ki-cloud-upload fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                        Simpan Gambar
                    </button>
                </form>
            </div>
        </div>
        @endif
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