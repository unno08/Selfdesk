<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerTicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAnySeller', Ticket::class);

        $tickets = Ticket::with(['buyer'])
    ->where('seller_id', $request->user()->id)
    ->latest()
    ->paginate(15);


        return view('seller.tickets.index', [
            'tickets' => $tickets,
        ]);
    }
}