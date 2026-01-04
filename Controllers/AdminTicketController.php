<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    public function index(Request $request): View
    {
        // NOTE: since you said admin only monitor,
        // you can add your own admin middleware/role check later.

        $tickets = Ticket::with(['buyer', 'seller'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->string('category')->toString());
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'filters' => [
                'status' => $request->string('status')->toString(),
                'category' => $request->string('category')->toString(),
            ],
        ]);
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'messages' => function ($query) {
                $query->with('sender')->oldest();
            },
            'buyer',
            'seller',
            'product',
        ]);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed']);

        return redirect()->route('admin.tickets.show', $ticket);
    }
}
