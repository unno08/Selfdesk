{{-- resources/views/tickets/create.blade.php --}}

<div class="ticket-page">

    <div class="ticket-topbar">
        <h2 class="ticket-title">Open New Ticket</h2>

        {{-- BACK BUTTON --}}
        <a class="btn-ticket" href="{{ route('tickets.index') }}">
            ← Back
        </a>
    </div>

    <div class="ticket-form-card">
        <form method="POST" action="{{ route('tickets.store') }}">
    @csrf

    <label>Seller</label>
    <select name="seller_id" required>
        @foreach($sellers as $s)
            <option value="{{ $s->id }}" @selected(($seller_id ?? old('seller_id')) == $s->id)>
                {{ $s->name }} ({{ $s->email }})
            </option>
        @endforeach
    </select>

    <input type="hidden" name="order_id" value="{{ $order_id ?? old('order_id') }}">
    <input type="hidden" name="product_id" value="{{ $product_id ?? old('product_id') }}">

    <label>Subject</label>
    <input type="text" name="subject" required value="{{ $subject ?? old('subject') }}"
           placeholder="Contoh: Masalah order pokok">

    <label>Category</label>
    <select name="category" required>
        @php $cat = $category ?? old('category'); @endphp
        <option value="product_details" @selected($cat=='product_details')>Product Details</option>
        <option value="plant_care" @selected($cat=='plant_care')>Plant Care</option>
        <option value="delivery" @selected($cat=='delivery')>Delivery</option>
        <option value="order_support" @selected($cat=='order_support')>Order Support</option>
    </select>

    <label>Message</label>
    <textarea name="message" rows="5" required>{{ old('message') }}</textarea>

    <button type="submit">Submit Ticket</button>
</form>
