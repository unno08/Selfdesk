@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $product->name }}</h1>
            <div class="text-muted small">Sold by {{ $product->seller?->name ?? 'Seller' }}</div>
        </div>
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="subject" value="Inquiry about {{ $product->name }}">
            <input type="hidden" name="category" value="product_details">
            <button class="btn btn-success" type="submit">Chat with Seller</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            {{ $product->description ?? 'No description available.' }}
        </div>
    </div>
@endsection