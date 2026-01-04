@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4">Seller Inbox</h1>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>Buyer</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Updated</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                        </td>
                        <td>{{ $ticket->buyer?->name ?? 'N/A' }}</td>
                        <td>{{ $ticket->product?->name ?? 'General' }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span></td>
                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">No tickets yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $tickets->links() }}
    </div>
@endsection