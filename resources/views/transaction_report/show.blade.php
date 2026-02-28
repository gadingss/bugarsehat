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
                            <span class="card-label fw-bolder fs-3 mb-1">Detail Transaksi</span>
                            <span class="text-muted mt-1 fw-bold fs-7">Informasi lengkap transaksi</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('transaction_report') }}" class="btn btn-light-primary">
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none">
                                        <path d="M9.60001 11H21C21.6 11 22 11.4 22 12C22 12.6 21.6 13 21 13H9.60001V11Z"
                                            fill="currentColor" />
                                        <path opacity="0.3" d="M9.6 20V4L2.3 11.3C1.9 11.7 1.9 12.3 2.3 12.7L9.6 20Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                Kembali
                            </a>
                        </div>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Row-->
                        <div class="row mb-7">
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">ID Transaksi</span>
                                        <span
                                            class="text-dark fw-bolder fs-6">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                                <!--end::Details-->
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">Tanggal Transaksi</span>
                                        <span
                                            class="text-dark fw-bolder fs-6">{{ $transaction->transaction_date->format('d F Y H:i') }}</span>
                                    </div>
                                </div>
                                <!--end::Details-->
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">Status</span>
                                        @if($transaction->status == 'pending')
                                            <span class="badge badge-light-warning fs-7">Pending</span>
                                        @elseif($transaction->status == 'validated')
                                            <span class="badge badge-light-success fs-7">Validated</span>
                                        @elseif($transaction->status == 'cancelled')
                                            <span class="badge badge-light-danger fs-7">Cancelled</span>
                                        @endif
                                    </div>
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-lg-6">
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">Member</span>
                                        <span class="text-dark fw-bolder fs-6">{{ $transaction->user->name }}</span>
                                        <span
                                            class="text-muted fw-semibold d-block fs-7">{{ $transaction->user->email }}</span>
                                    </div>
                                </div>
                                <!--end::Details-->
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">Produk</span>
                                        <span class="text-dark fw-bolder fs-6">{{ $transaction->item->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <!--end::Details-->
                                <!--begin::Details-->
                                <div class="d-flex align-items-center mb-5">
                                    <div class="flex-grow-1">
                                        <span class="text-muted fw-bold d-block">Validator</span>
                                        <span
                                            class="text-dark fw-bolder fs-6">{{ $transaction->validator ? $transaction->validator->name : '-' }}</span>
                                    </div>
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Row-->

                        <!--begin::Separator-->
                        <div class="separator separator-dashed my-10"></div>
                        <!--end::Separator-->

                        <!--begin::Order details-->
                        <div class="mb-0">
                            <h4 class="text-dark fw-bolder mb-7">Detail Order</h4>
                            <!--begin::Item-->
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <span class="text-muted fw-semibold fs-6">Jumlah</span>
                                <span class="text-dark fw-bolder fs-6">{{ $transaction->quantity }} item</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <span class="text-muted fw-semibold fs-6">Harga Satuan</span>
                                <span class="text-dark fw-bolder fs-6">
                                    Rp
                                    @if($transaction->quantity != 0)
                                        {{ number_format($transaction->amount / $transaction->quantity, 0, ',', '.') }}
                                    @else
                                        0 {{-- atau bisa diganti dengan "Tidak tersedia" --}}
                                    @endif
                                </span>
                            </div>

                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex justify-content-between align-items-center mb-5">
                                <span class="text-muted fw-semibold fs-6">Subtotal</span>
                                <span class="text-dark fw-bolder fs-6">Rp
                                    {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex justify-content-between align-items-center border-top pt-5">
                                <span class="text-dark fw-bolder fs-4">Total</span>
                                <span class="text-dark fw-bolder fs-4">Rp
                                    {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Order details-->
                    </div>
                    <!--end::Card body-->
                    <!--begin::Card footer-->
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            @can('update', $transaction)
                                <a href="{{ route('transaction_report.edit', $transaction) }}" class="btn btn-primary me-3">
                                    <span class="svg-icon svg-icon-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none">
                                            <path opacity="0.3"
                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                fill="currentColor" />
                                            <path
                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    Edit
                                </a>
                            @endcan

                            @if($transaction->status == 'pending' && auth()->user()->can('validate', $transaction))
                                <form action="{{ route('transaction_report.validate', $transaction) }}" method="POST"
                                    class="me-3">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <path
                                                    d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        Validate
                                    </button>
                                </form>
                            @endif

                            @if($transaction->status != 'cancelled' && auth()->user()->can('cancel', $transaction))
                                <form action="{{ route('transaction_report.cancel', $transaction) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to cancel this transaction?')">
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none">
                                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10"
                                                    fill="currentColor" />
                                                <rect x="7" y="15.3137" width="12" height="2" rx="1"
                                                    transform="rotate(-45 7 15.3137)" fill="currentColor" />
                                                <rect x="8.41422" y="7" width="12" height="2" rx="1"
                                                    transform="rotate(45 8.41422 7)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <!--end::Card footer-->
                </div>
                <!--end::Card-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Post-->
    </div>
    <!--end::Content-->
@endsection