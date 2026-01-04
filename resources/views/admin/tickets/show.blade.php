@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $ticket->subject }}</h1>
            <div class="text-muted small">
                Buyer: {{ $ticket->buyer?->name ?? 'N/A' }} | Seller: {{ $ticket->seller?->name ?? 'N/A' }}
            </div>
        </div>
        @if($ticket->status !== 'closed')
            <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-danger" type="submit">Close Ticket</button>
            </form>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            @forelse($ticket->messages as $message)
                <div class="mb-3">
                    <div class="fw-semibold">{{ $message->sender?->name ?? 'User' }}</div>
                    <div>{{ $message->message }}</div>
                    <div class="text-muted small">{{ $message->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-muted">No messages yet.</p>
            @endforelse
        </div>
    </div>
@endsection