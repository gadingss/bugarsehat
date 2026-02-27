@extends('layouts.app')

@section('title', 'Product Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h2 class="fw-bold">Product Transactions</h2>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th>No</th>
                                    <th>Product</th>
                                    <th>Member</th>
                                    <th>Quantity</th>
                                    <th>Total Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-50px symbol-circle me-3">
                                                <div class="symbol-label bg-light-primary">
                                                    <i class="fas fa-box text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->product->name }}</div>
                                                <div class="text-muted fs-7">{{ $transaction->product->category }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-35px symbol-circle me-3">
                                                <div class="symbol-label bg-light-success">
                                                    <i class="fas fa-user text-success"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $transaction->user->name }}</div>
                                                <div class="text-muted fs-7">{{ $transaction->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $transaction->quantity }} unit</td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->created_at ? $transaction->created_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @if($transaction->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($transaction->status == 'validated')
                                            <span class="badge badge-success">Validated</span>
                                        @elseif($transaction->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $transaction->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-gray-500">No product transactions found</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
