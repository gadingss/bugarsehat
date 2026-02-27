@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid p-3 p-lg-6">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Edit Profile</h3>
                    <div class="card-toolbar">
                        @php
                            $profileIndexUrl = \Illuminate\Support\Facades\Route::has('profile.index') 
                                ? route('profile.index') 
                                : url('/profile');
                        @endphp
                        <a href="{{ $profileIndexUrl }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Upload -->
                        <div class="row mb-8">
                            <div class="col-12">
                                <label class="fw-semibold fs-6 mb-2">Foto Profile</label>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-100px symbol-circle me-5">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/avatars/' . $user->avatar) }}" alt="avatar" id="avatar-preview-img" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" />
                                        @else
                                            <div class="symbol-label fs-3 bg-light-primary text-primary d-flex justify-content-center align-items-center" style="width: 100px; height: 100px; border-radius: 50%;" id="avatar-preview">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(this)">
                                        <div class="form-text">Format yang diizinkan: JPG, PNG, GIF. Maksimal 2MB.</div>
                                        @error('avatar')
                                            <div class="text-danger fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row mb-6">
                            <div class="col-12 col-md-6">
                                <label class="required fw-semibold fs-6 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" class="form-control form-control-solid" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="required fw-semibold fs-6 mb-2">Email</label>
                                <input type="email" name="email" class="form-control form-control-solid" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-12 col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control form-control-solid" 
                                       value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control form-control-solid" 
                                       value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                @error('date_of_birth')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-12 col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Jenis Kelamin</label>
                                <select name="gender" class="form-select form-select-solid">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-12">
                                <label class="fw-semibold fs-6 mb-2">Alamat</label>
                                <textarea name="address" class="form-control form-control-solid" rows="3" 
                                          placeholder="Alamat lengkap">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="separator separator-dashed my-8"></div>
                        <h4 class="fw-bold mb-6">Kontak Darurat</h4>

                        <div class="row mb-6">
                            <div class="col-12 col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Nama Kontak Darurat</label>
                                <input type="text" name="emergency_contact" class="form-control form-control-solid" 
                                       value="{{ old('emergency_contact', $user->emergency_contact) }}" 
                                       placeholder="Nama keluarga/teman">
                                @error('emergency_contact')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Nomor Telepon Darurat</label>
                                <input type="text" name="emergency_phone" class="form-control form-control-solid" 
                                       value="{{ old('emergency_phone', $user->emergency_phone) }}" 
                                       placeholder="08xxxxxxxxxx">
                                @error('emergency_phone')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ $profileIndexUrl }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatar-preview-img');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
