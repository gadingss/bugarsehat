@extends('layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Edit Transaksi</span>
                        <span class="text-muted mt-1 fw-bold fs-7">Perbarui informasi transaksi</span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('transaction_report.show', $transaction) }}" class="btn btn-light-primary">
                            <span class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M9.60001 11H21C21.6 11 22 11.4 22 12C22 12.6 21.6 13 21 13H9.60001V11Z" fill="currentColor"/>
                                    <path opacity="0.3" d="M9.6 20V4L2.3 11.3C1.9 11.7 1.9 12.3 2.3 12.7L9.6 20Z" fill="currentColor"/>
                                </svg>
                            </span>
                            Kembali
                        </a>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Form-->
                <form action="{{ route('transaction_report.update', $transaction) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Row-->
                        <div class="row">
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Member</label>
                                    <select name="user_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih member" required>
                                        <option value="">Pilih member...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $transaction->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} - {{ $user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Produk</label>
                                    <select name="product_id" class="form-select form-select-solid" data-control="select2" data-placeholder="Pilih produk" required>
                                        <option value="">Pilih produk...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ old('product_id', $transaction->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                            <!--end::Col-->
                            <div class="col-lg-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Total Harga</label>
                                    <input type="number" name="amount" class="form-control form-control-solid" placeholder="0" value="{{ old('amount', $transaction->amount) }}" min="0" step="0.01" required>
                                    @error('amount')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                            <!--end::Col-->
                            <!--end::Col-->
                            <div class="col-lg-6">
                                <!--begin::Input group-->
                                <div class="fv-row mb-7">
                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                    <select name="status" class="form-select form-select-solid" required>
                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!--end::Input group-->
                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="text-danger fs-7">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-lg-6">
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <label class="required fs-6 fw-semibold mb-2">Status</label>
                                                    <select name="status" class="form-select form-select-solid" required>
                                                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="validated" {{ old('status', $transaction->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                    @error('status')
                                                    @enderror
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Card body-->
                                    
                                    <!--begin::Actions-->
                                    <div class="card-footer d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">Simpan Perubahan</span>
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Post-->
                </div>
                <!--end::Content-->
            @endsection
