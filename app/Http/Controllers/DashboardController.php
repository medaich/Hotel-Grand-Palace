<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $total_rooms = Room::count();
        $available_rooms = Room::where('status', 'available')->count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $total_guests = Guest::count();
        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        
        $total_revenue = Booking::where('payment_status', 'paid')->sum('total_price') ?? 0;

        $search = $request->input('search');
        
        $recent_bookings_query = Booking::with(['guest', 'room'])->orderBy('created_at', 'desc');

        if ($search) {
            $recent_bookings_query->whereHas('guest', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('booking_ref', 'like', "%{$search}%");
        }

        $recent_bookings = $recent_bookings_query->take(10)->get();

        $today = Carbon::today();
        $todays_checkins = Booking::with(['guest', 'room'])->whereDate('check_in', $today)->get();
        $todays_checkouts = Booking::with(['guest', 'room'])->whereDate('check_out', $today)->get();

        return view('dashboard', compact(
            'total_rooms', 'available_rooms', 'occupied_rooms',
            'total_guests', 'total_bookings', 'pending_bookings',
            'total_revenue', 'search', 'recent_bookings',
            'todays_checkins', 'todays_checkouts'
        ));
    }
}
