<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'room']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->where('check_in', $request->date);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('guest', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('booking_ref', 'like', "%{$search}%");
        }

        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        $bookings = $query->orderBy($sort, $order)->get();
        
        $guests = Guest::orderBy('first_name')->get();
        $rooms = Room::where('status', 'available')->orWhere('status', 'occupied')->orderBy('room_number')->get();

        $view_booking = null;
        if ($request->filled('view')) {
            $view_booking = Booking::with(['guest', 'room'])->find($request->view);
        }

        $edit_booking = null;
        if ($request->filled('edit')) {
            $edit_booking = Booking::with(['guest', 'room'])->find($request->edit);
        }

        return view('bookings.index', compact('bookings', 'guests', 'rooms', 'view_booking', 'edit_booking', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'special_requests' => 'nullable|string',
        ]);

        $validated['booking_ref'] = 'BK-' . date('Y') . '-' . rand(1000, 9999);
        $validated['status'] = 'pending';
        $validated['payment_status'] = 'unpaid';
        $validated['created_by'] = auth()->id();

        Booking::create($validated);
        
        Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);

        return redirect()->route('bookings.index')->with('success', "Booking created: <strong>{$validated['booking_ref']}</strong>");
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,checked_in,checked_out,cancelled',
            'payment_status' => 'required|string|in:unpaid,partial,paid,refunded',
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.index', ['updated' => 1]);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index', ['deleted' => 1]);
    }
}
