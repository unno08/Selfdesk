@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4">Admin Ticket Monitor</h1>
    </div>

    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                @foreach(['open', 'pending', 'closed'] as $status)
                    <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All</option>
                @foreach(['plant_care', 'product_details', 'delivery', 'order_support'] as $category)
                    <option value="{{ $category }}" @selected($filters['category'] === $category)>
                        {{ ucwords(str_replace('_', ' ', $category)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                <tr>
                    <th>Subject</th>
                    <th>Buyer</th>
                    <th>Seller</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Updated</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('admin.tickets.show', $ticket) }}">{{ $ticket->subject }}</a></td>
                        <td>{{ $ticket->buyer?->name ?? 'N/A' }}</td>
                        <td>{{ $ticket->seller?->name ?? 'N/A' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $ticket->category)) }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span></td>
                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted">No tickets found.</td>
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