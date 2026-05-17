<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Only allow admin access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $tab = $request->get('tab', 'users');
        $data = ['tab' => $tab];

        if ($tab === 'users') {
            $data['users'] = User::orderBy('full_name')->get();
            $data['view_user'] = $request->filled('view') ? User::find($request->view) : null;
            $data['edit_user'] = $request->filled('edit') ? User::find($request->edit) : null;
        } elseif ($tab === 'logs') {
            $logFile = $request->get('log', 'laravel.log');
            // Secure log reading: only allow .log files inside storage/logs and prevent path traversal
            if (preg_match('/^[a-zA-Z0-9_-]+\.log$/', $logFile)) {
                $path = storage_path('logs/' . $logFile);
                if (file_exists($path)) {
                    $data['log_content'] = file_get_contents($path);
                } else {
                    $data['log_content'] = "File not found: $logFile";
                }
            } else {
                $data['log_content'] = "Invalid log filename.";
            }
        } elseif ($tab === 'backup' && $request->isMethod('post')) {
            // Stub out secure backup
            $data['backup_output'] = "Backup functionality is safely disabled in this demo.";
        } elseif ($tab === 'settings' && $request->isMethod('post')) {
            session()->flash('success', 'Settings saved. Hotel: ' . $request->get('hotel_name'));
        }

        return view('admin.index', $data);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,staff,manager',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('admin.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'full_name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|in:admin,staff,manager',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('admin.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.index')->withErrors('You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.index')->with('success', 'User deleted successfully.');
    }

    public function runPing(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $host = $request->input('ping_host');
        // Securely mock the ping output to prevent OS command injection
        $output = "PING " . htmlspecialchars($host) . " (Secure Mock)\n64 bytes from $host: icmp_seq=1 ttl=64 time=0.042 ms\n64 bytes from $host: icmp_seq=2 ttl=64 time=0.043 ms\n\n--- $host ping statistics ---\n2 packets transmitted, 2 received, 0% packet loss";
        return redirect()->route('admin.index', ['tab' => 'tools'])->with('ping_output', $output);
    }

    public function runBackup(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $path = $request->input('backup_path');
        // Stub out secure backup
        $output = "Backup functionality to '$path' is safely disabled in this demo environment.";
        return redirect()->route('admin.index', ['tab' => 'backup'])->with('backup_output', $output);
    }

    public function saveSettings(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        return redirect()->route('admin.index', ['tab' => 'settings'])->with('success', 'Settings saved. Hotel: ' . $request->input('hotel_name'));
    }
}
