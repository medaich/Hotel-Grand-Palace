<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%")
                  ->orWhere('nationality', 'like', "%{$search}%");
        }
        
        $guests = $query->orderBy('id', 'desc')->get();

        $view_guest = null;
        if ($request->filled('view')) {
            $view_guest = Guest::with(['bookings.room'])->find($request->view);
        }

        $edit_guest = null;
        if ($request->filled('edit')) {
            $edit_guest = Guest::find($request->edit);
        }

        return view('guests.index', compact('guests', 'view_guest', 'edit_guest', 'request'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $guest = Guest::create($validated);

        return redirect()->route('guests.index')->with('success', "Guest <strong>{$guest->first_name} {$guest->last_name}</strong> registered.");
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $guest->update($validated);

        return redirect()->route('guests.index', ['view' => $guest->id])->with('success', 'Guest updated successfully.');
    }

    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('guests.index', ['deleted' => 1]);
    }
}
