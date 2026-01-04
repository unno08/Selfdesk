<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Resolve user id.
     * - kalau login: guna user id sebenar
     * - kalau tak login & DEMO_NO_LOGIN=true: guna demo buyer id (1)
     * - selain tu: abort 401
     */
    private function resolveUserId(Request $request): int
    {
        return $request->user()?->id
            ?? (env('DEMO_NO_LOGIN') ? 1 : abort(401));
    }

    // ✅ Buyer - list semua ticket dia (boleh view tanpa login jika DEMO_NO_LOGIN=true)
    public function index(Request $request): View
    {
        $userId = $this->resolveUserId($request);

        $tickets = Ticket::query()
            ->with(['seller'])
            ->withCount([
                'messages as unread_count' => function ($q) use ($userId) {
                    $q->where(function ($qq) {
                            $qq->whereNull('tickets.buyer_last_read_at')
                               ->orWhereColumn('ticket_messages.created_at', '>', 'tickets.buyer_last_read_at');
                        })
                        ->where(function ($qq) use ($userId) {
                            $qq->whereNull('ticket_messages.sender_id') // system
                               ->orWhere('ticket_messages.sender_id', '!=', $userId);
                        });
                }
            ])
            ->where('buyer_id', $userId)
            ->latest()
            ->paginate(12);

        return view('tickets.index', compact('tickets'));
    }

    // ✅ Buyer - form buka ticket baru
    public function create(Request $request): View
    {
        // create ticket tak patut guest (walaupun demo)
        if (!$request->user()) {
            abort(401, 'Please login to create a ticket.');
        }

        $sellers = User::where('id', '!=', $request->user()->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('tickets.create', [
            'sellers'    => $sellers,
            'seller_id'  => $request->query('seller_id'),
            'order_id'   => $request->query('order_id'),
            'product_id' => $request->query('product_id'),
            'category'   => $request->query('category'),
            'subject'    => $request->query('subject'),
        ]);
    }

    // ✅ create ticket + auto create first message
    public function store(Request $request): RedirectResponse
    {
        if (!$request->user()) {
            abort(401, 'Please login to create a ticket.');
        }

        $data = $request->validate([
            'seller_id'  => ['required', 'integer', 'exists:users,id'],
            'subject'    => ['required', 'string', 'max:255'],
            'category'   => ['required', 'in:plant_care,product_details,delivery,order_support'],
            'message'    => ['required', 'string'],
            'order_id'   => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $ticket = Ticket::create([
            'buyer_id'   => $request->user()->id,
            'seller_id'  => $data['seller_id'],
            'product_id' => $data['product_id'] ?? null,
            'order_id'   => $data['order_id'] ?? null,
            'subject'    => $data['subject'],
            'category'   => $data['category'],
            'status'     => 'open',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => null, // SYSTEM
            'message'   => '📌 Ticket opened by buyer.',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'message'   => $data['message'],
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    // ✅ chat page (boleh view tanpa login jika DEMO_NO_LOGIN=true)
    public function show(Request $request, Ticket $ticket): View
    {
        $this->ensureParticipant($request, $ticket);

        // update last read hanya kalau user login betul-betul
        $userId = $request->user()?->id;
        if ($userId) {
            if ((int) $ticket->buyer_id === (int) $userId) {
                $ticket->update(['buyer_last_read_at' => now()]);
            } elseif ((int) $ticket->seller_id === (int) $userId) {
                $ticket->update(['seller_last_read_at' => now()]);
            }
        }

        $ticket->load(['messages.sender', 'buyer', 'seller']);

        $me = $request->user(); // boleh null (demo mode)

return view('tickets.show', compact('ticket', 'me'));

    }

    // ✅ reply message
    public function storeMessage(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$request->user()) {
            abort(401, 'Please login to reply.');
        }

        $this->ensureParticipant($request, $ticket);

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'message'   => $data['message'],
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$request->user()) {
            abort(401, 'Please login to close a ticket.');
        }

        $this->ensureParticipant($request, $ticket);

        $ticket->update([
            'status' => 'closed',
        ]);

        return redirect()->route('tickets.show', $ticket);
    }

    // ✅ only buyer or seller boleh access chat
    private function ensureParticipant(Request $request, Ticket $ticket): void
    {
        $userId = $this->resolveUserId($request);

        $isBuyer  = (int) $ticket->buyer_id === (int) $userId;
        $isSeller = (int) $ticket->seller_id === (int) $userId;

        if (!$isBuyer && !$isSeller) {
            abort(403);
        }
    }
}
