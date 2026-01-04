@extends('layouts.app')

@section('content')
@php
    $myId = auth()->id() ?? (env('DEMO_NO_LOGIN') ? 1 : null);
@endphp


<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="h5 mb-1">{{ $ticket->subject }}</h1>
        <div class="text-muted small">
            Category: {{ ucwords(str_replace('_', ' ', $ticket->category)) }}
            <span class="mx-2">•</span>
            Status: <span class="fw-semibold">{{ ucfirst($ticket->status) }}</span>
        </div>
    </div>

    <a class="btn btn-outline-secondary btn-sm" href="{{ route('tickets.index') }}">Back</a>
</div>

<style>
    .chat-wrap{
        background:#f6f7fb;
        border:1px solid rgba(0,0,0,.08);
        border-radius:16px;
        overflow:hidden;
    }
    .chat-header{
        background:#ffffff;
        border-bottom:1px solid rgba(0,0,0,.08);
        padding:14px 16px;
        display:flex;
        gap:12px;
        align-items:center;
        justify-content:space-between;
    }
    .badge-soft{
        font-size:12px;
        padding:6px 10px;
        border-radius:999px;
        background:rgba(13,110,253,.12);
        color:#0d6efd;
        border:1px solid rgba(13,110,253,.25);
    }
    .chat-body{
        padding:16px;
        max-height:460px;
        overflow:auto;
    }
    .msg-row{
        display:flex;
        margin-bottom:12px;
    }
    .msg-row.me{ justify-content:flex-end; }
    .msg-row.other{ justify-content:flex-start; }

    .bubble{
        max-width:78%;
        padding:10px 12px;
        border-radius:16px;
        border:1px solid rgba(0,0,0,.08);
        background:#fff;
        box-shadow:0 1px 0 rgba(0,0,0,.03);
    }
    .bubble.me{
        background:#0d6efd;
        color:#fff;
        border-color:rgba(13,110,253,.35);
        border-bottom-right-radius:6px;
    }
    .bubble.other{
        background:#ffffff;
        border-bottom-left-radius:6px;
    }
    .meta{
        display:flex;
        gap:10px;
        align-items:center;
        margin-top:6px;
        font-size:12px;
        opacity:.8;
    }
    .name{
        font-weight:600;
        font-size:12px;
        opacity:.9;
        margin-bottom:4px;
    }
    .chat-footer{
        background:#ffffff;
        border-top:1px solid rgba(0,0,0,.08);
        padding:12px;
    }
    .composer{
        display:flex;
        gap:10px;
        align-items:flex-end;
    }
    .composer textarea{
        resize:none;
        border-radius:12px;
    }
</style>

@php
    // Demo mode support: kalau tak login, fallback to buyer_id = 1
    $myId = auth()->id() ?? (env('DEMO_NO_LOGIN') ? 1 : null);
@endphp


<div class="chat-wrap">
    <div class="chat-header">
    <div class="d-flex flex-column">
        <div class="fw-semibold">Service Desk Chat</div>
        <div class="text-muted small">
            Buyer: {{ $ticket->buyer?->name ?? 'Buyer' }} • Seller: {{ $ticket->seller?->name ?? 'Seller' }}
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge-soft">{{ ucfirst($ticket->status) }}</span>

        @if($ticket->status !== 'closed')
            <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    Close Ticket
                </button>
            </form>
        @endif
    </div>
</div>


    <div class="chat-body" id="chatBody">
        @forelse($ticket->messages as $message)
            @php
    $isMe = $myId !== null && ((int) $message->sender_id === (int) $myId);
@endphp

@if($isMe)
    {{-- message from me --}}
@else
    {{-- message from others --}}
@endif


            <div class="msg-row {{ $isMe ? 'me' : 'other' }}">
                <div class="bubble {{ $isMe ? 'me' : 'other' }}">
                    <div class="name">
                        {{ $isMe ? 'You' : ($message->sender?->name ?? 'User') }}
                    </div>

                    <div style="white-space: pre-wrap;">{{ $message->message }}</div>

                    <div class="meta">
                        <span>{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-muted">No messages yet. Start the conversation.</div>
        @endforelse
    </div>

    @if($ticket->status === 'closed')
    <div class="alert alert-warning text-center mb-0">
        🔒 Ticket closed. This conversation is read-only.
    </div>
@else
    <div class="chat-footer">
        <form method="POST" action="{{ route('tickets.messages.store', $ticket) }}">
            @csrf
            <div class="composer">
                <div class="flex-grow-1">
                    <textarea class="form-control" name="message" rows="2" required
                        placeholder="Type your message...">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>
@endif
