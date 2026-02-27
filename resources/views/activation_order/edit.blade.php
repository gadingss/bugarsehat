@extends('layouts.app')

@section('title', 'Edit Membership')

@section('content')
<style>
    .custom-container {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
    .form-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .member-info {
        background: #fff3e0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>

<div class="container mt-4 custom-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Edit Membership</h3>
        <a href="{{ route('activation_order', ['tab' => $membership->type]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-section">
        <form method="POST" action="{{ route('activation_order.update', $membership->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Member</label>
                        <input type="text" class="form-control" value="{{ $membership->user->name }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="package_id" class="form-label">Paket Membership <span class="text-danger">*</span></label>
                        <select class="form-select @error('package_id') is-invalid @enderror" id="package_id" name="package_id" required>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price }}" data-duration="{{ $package->duration_days }}" 
                                    {{ $membership->package_id == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }} ({{ $package->duration_days }} hari - {{ $package->visit_limit }} kunjungan)
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                               id="start_date" name="start_date" 
                               value="{{ old('start_date', $membership->start_date) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                               id="end_date" name="end_date" 
                               value="{{ old('end_date', $membership->end_date) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="remaining_visits" class="form-label">Sisa Kunjungan <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('remaining_visits') is-invalid @enderror" 
                               id="remaining_visits" name="remaining_visits" 
                               value="{{ old('remaining_visits', $membership->remaining_visits) }}" min="0" required>
                        @error('remaining_visits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="inactive" {{ $membership->status == 'inactive' ? 'selected' : '' }}>Belum Aktif</option>
                            <option value="active" {{ $membership->status == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="cancelled" {{ $membership->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            <option value="expired" {{ $membership->status == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Mengubah paket akan memperbarui harga transaksi sesuai dengan paket yang dipilih.
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('activation_order', ['tab' => $membership->type]) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const packageSelect = document.getElementById('package_id');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function updateEndDate() {
            const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
            const startDate = new Date(startDateInput.value);
            
            if (selectedPackage.value && startDateInput.value) {
                const duration = parseInt(selectedPackage.dataset.duration);
                
                const endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + duration);
                
                endDateInput.value = endDate.toISOString().split('T')[0];
            }
        }

        packageSelect.addEventListener('change', updateEndDate);
        startDateInput.addEventListener('change', updateEndDate);
        
        // Initial call
        updateEndDate();
    });
</script>
@endsection
