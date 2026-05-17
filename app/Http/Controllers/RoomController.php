<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('type')) {
            $query->where('room_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        $rooms = $query->orderBy('room_number')->get();

        $view_room = null;
        if ($request->filled('view')) {
            $view_room = Room::find($request->view);
        }

        $edit_room = null;
        if ($request->filled('edit')) {
            $edit_room = Room::find($request->edit);
        }

        return view('rooms.index', compact('rooms', 'view_room', 'edit_room'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms',
            'room_type' => 'required|string|max:50',
            'floor' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'price_per_night' => 'required|numeric',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'room_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('room_image')) {
            // Laravel automatically generates a safe, random filename and stores it safely.
            $path = $request->file('room_image')->store('rooms', 'public');
            $validated['image_path'] = '/storage/' . $path;
        }
        
        unset($validated['room_image']);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', "Room {$validated['room_number']} added successfully.");
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $room->id,
            'room_type' => 'required|string|max:50',
            'floor' => 'nullable|integer',
            'capacity' => 'nullable|integer',
            'price_per_night' => 'required|numeric',
            'description' => 'nullable|string',
            'amenities' => 'nullable|string',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index', ['view' => $room->id])->with('success', 'Room updated successfully');
    }

    public function updateStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        $room->update($validated);
        return redirect()->back()->with('success', 'Room status updated');
    }

    public function destroy(Room $room)
    {
        // Authorization check could be added here
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully');
    }
}
