@extends('components.layouts.app')

@section('title', 'Member Transactions')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Transaction History for {{ $member->name }}</h4>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                            <td>{{ $transaction->reference_number }}</td>
                                            <td>
                                                <span class="badge badge-{{ $transaction->type === 'deposit' ? 'success' : ($transaction->type === 'withdrawal' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td>KES {{ number_format($transaction->amount, 2) }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $transactions->links() }}
                    @else
                        <p class="text-muted">No transactions found for this member.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 