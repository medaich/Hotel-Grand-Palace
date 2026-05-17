<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Room;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $maintenances = Maintenance::with(['room', 'reportedBy'])->orderBy('created_at', 'desc')->get();
        $rooms = Room::orderBy('room_number')->get();

        $view_maintenance = null;
        if ($request->filled('view')) {
            $view_maintenance = Maintenance::with(['room', 'reportedBy'])->find($request->view);
        }

        $edit_maintenance = null;
        if ($request->filled('edit')) {
            $edit_maintenance = Maintenance::find($request->edit);
        }

        return view('maintenance.index', compact('maintenances', 'rooms', 'view_maintenance', 'edit_maintenance'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issue' => 'required|string',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'status' => 'required|string|in:open,in_progress,resolved,cancelled',
        ]);

        $validated['reported_by'] = auth()->id();

        Maintenance::create($validated);

        // Optional: Update room status to maintenance if priority is urgent or high
        if (in_array($validated['priority'], ['high', 'urgent']) && $validated['status'] != 'resolved') {
            Room::where('id', $validated['room_id'])->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request created.');
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issue' => 'required|string',
            'priority' => 'required|string|in:low,normal,high,urgent',
            'status' => 'required|string|in:open,in_progress,resolved,cancelled',
        ]);

        if ($validated['status'] == 'resolved' && $maintenance->status != 'resolved') {
            $validated['resolved_at'] = now();
        }

        $maintenance->update($validated);

        return redirect()->route('maintenance.index')->with('success', 'Maintenance request updated.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenance.index')->with('success', 'Maintenance request deleted.');
    }
}
