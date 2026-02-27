@extends('layouts.app')

@section('title', 'Pengajuan Membership Baru')

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
</style>

<div class="container mt-4 custom-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Pengajuan Membership Baru</h3>
        <a href="{{ route('activation_order', ['tab' => 'application']) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-section">
        <form method="POST" action="{{ route('activation_order.application.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Member <span class="text-danger">*</span></label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                            <option value="">-- Pilih Member --</option>
                            
                            {{-- PERBAIKAN 1: Logika @php dihapus, kita gunakan variabel $members dari controller --}}
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} - {{ $member->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="package_id" class="form-label">Pilih Paket <span class="text-danger">*</span></label>
                        <select class="form-select @error('package_id') is-invalid @enderror" id="package_id" name="package_id" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price }}" data-duration="{{ $package->duration_days }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    
                                    {{-- PERBAIKAN 2: Mengganti 'visit_limit' menjadi 'max_visits' --}}
                                    {{ $package->name }} - Rp {{ number_format($package->price, 0, ',', '.') }} ({{ $package->duration_days }} hari - {{ $package->max_visits }} kunjungan)
                                
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
                               value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="end_date" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Pembayaran</label>
                        <input type="text" class="form-control" id="total_amount" readonly>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Buat Pengajuan
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
        const totalAmountInput = document.getElementById('total_amount');

        function updateEndDateAndAmount() {
            const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
            const startDate = new Date(startDateInput.value);
            
            if (selectedPackage.value && startDateInput.value) {
                const duration = parseInt(selectedPackage.dataset.duration);
                const price = parseInt(selectedPackage.dataset.price);
                
                const endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + duration);
                
                endDateInput.value = endDate.toISOString().split('T')[0];
                totalAmountInput.value = 'Rp ' + price.toLocaleString('id-ID');
            }
        }

        packageSelect.addEventListener('change', updateEndDateAndAmount);
        startDateInput.addEventListener('change', updateEndDateAndAmount);
        
        // Initial call
        updateEndDateAndAmount();
    });
</script>
@endsection