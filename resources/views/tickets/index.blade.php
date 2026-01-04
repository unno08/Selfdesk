{{-- tickets/index.blade.php --}}

<div class="ticket-page">

    <div class="ticket-topbar">
        <h2 class="ticket-title">Service Desk</h2>

        <a class="btn-ticket" href="{{ route('tickets.create') }}">
            + New Ticket
        </a>
    </div>

    <div class="ticket-grid">
        @forelse($tickets as $t)
            <div class="ticket-card">
                <div class="ticket-thumb">
                    {{-- guna initial/placeholder (kalau nak letak logo seller pun boleh nanti) --}}
                    <div class="ticket-badge">{{ strtoupper(substr(optional($t->seller)->name ?? 'S', 0, 1)) }}</div>
                </div>

                <div class="ticket-body">
                    <div class="ticket-subject">
                        {{ $t->subject ?? 'Ticket' }}
                    </div>

                    <div class="ticket-meta">
                        <span class="ticket-seller">{{ optional($t->seller)->name ?? 'Seller' }}</span>
                        <span class="dot">•</span>
                        <span class="ticket-category">{{ str_replace('_',' ', $t->category) }}</span>
                    </div>

                    <div class="ticket-status status-{{ $t->status }}">
                        {{ strtoupper($t->status) }}
                    </div>

                    <a class="btn-view" href="{{ route('tickets.show', $t) }}">
                        View Details →
                    </a>
                </div>
            </div>
        @empty
            <p>No tickets yet. Click “New Ticket” to start.</p>
        @endforelse
    </div>

    <div class="ticket-paging">
        {{ $tickets->links() }}
    </div>

</div>


{{-- CSS inline dulu (senang, nanti boleh pindah ke app.css) --}}
<style>
    .ticket-page{ padding:24px; max-width:1200px; margin:0 auto; }
    .ticket-topbar{ display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; }
    .ticket-title{ font-size:28px; font-weight:800; margin:0; }
    .btn-ticket{
        display:inline-flex; align-items:center; justify-content:center;
        padding:12px 18px; border-radius:999px;
        border:2px solid #2FA36B; color:#2FA36B; font-weight:700;
        text-decoration:none; background:#fff;
        transition: all .15s ease;
    }
    .btn-ticket:hover{ background:#2FA36B; color:#fff; }

    .ticket-grid{
        display:grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap:18px;
    }

    @media (max-width:1100px){
        .ticket-grid{ grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width:820px){
        .ticket-grid{ grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width:520px){
        .ticket-grid{ grid-template-columns: 1fr; }
    }

    .ticket-card{
        background:#fff;
        border-radius:18px;
        overflow:hidden;
        border:1px solid #eee;
        box-shadow: 0 8px 18px rgba(0,0,0,0.06);
        display:flex;
        flex-direction:column;
    }

    .ticket-thumb{
        height:170px;
        background: linear-gradient(135deg, #e7f6ee, #f6fbf8);
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .ticket-badge{
        width:82px; height:82px;
        border-radius:999px;
        border:3px solid #2FA36B;
        display:flex; align-items:center; justify-content:center;
        font-size:32px; font-weight:900; color:#2FA36B;
        background:#fff;
    }

    .ticket-body{ padding:14px 14px 16px; display:flex; flex-direction:column; gap:10px; }
    .ticket-subject{ font-size:16px; font-weight:800; color:#111; line-height:1.25; }
    .ticket-meta{ font-size:13px; color:#6b7280; display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
    .dot{ opacity:.6; }

    .ticket-status{
        font-size:12px; font-weight:800;
        width:fit-content;
        padding:6px 10px;
        border-radius:999px;
        border:1px solid #e5e7eb;
    }
    .status-open{ background:#e8fff2; color:#137a46; border-color:#bff2d5; }
    .status-pending{ background:#fff7e6; color:#8a5a00; border-color:#ffe0a6; }
    .status-closed{ background:#f3f4f6; color:#374151; border-color:#e5e7eb; }

    .btn-view{
        margin-top:6px;
        display:flex; align-items:center; justify-content:center;
        padding:12px 14px;
        border-radius:999px;
        border:2px solid #2FA36B;
        color:#2FA36B;
        font-weight:800;
        text-decoration:none;
        background:#fff;
        transition: all .15s ease;
    }
    .btn-view:hover{ background:#2FA36B; color:#fff; }
    .ticket-paging{ margin-top:20px; }
</style>
