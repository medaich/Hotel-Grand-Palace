<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Booking;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::with('booking')->orderBy('created_at', 'desc')->get();
        $bookings = Booking::with('guest', 'room')->whereIn('status', ['confirmed', 'checked_in'])->get();

        $view_service = null;
        if ($request->filled('view')) {
            $view_service = Service::with('booking.guest')->find($request->view);
        }

        $edit_service = null;
        if ($request->filled('edit')) {
            $edit_service = Service::find($request->edit);
        }

        return view('services.index', compact('services', 'bookings', 'view_service', 'edit_service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'service_name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['total'] = $validated['quantity'] * $validated['unit_price'];
        $validated['added_by'] = auth()->id();

        Service::create($validated);

        return redirect()->route('services.index')->with('success', 'Service added successfully.');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'service_name' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $validated['total'] = $validated['quantity'] * $validated['unit_price'];

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
