<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DownloadLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Dashboard Overview — semua data dinamis dari database.
     */
    public function index()
    {
        // ── Metric Cards ────────────────────────────────────────
        $totalUsers         = User::count();
        $downloadsToday     = DownloadLog::whereDate('created_at', Carbon::today())->count();
        $downloadsThisMonth = DownloadLog::whereMonth('created_at', Carbon::now()->month)
                                         ->whereYear('created_at', Carbon::now()->year)
                                         ->count();

        // Rasio keberhasilan
        $totalDownloads   = DownloadLog::count();
        $successDownloads = DownloadLog::where('status', 'sukses')->count();
        $successRate      = $totalDownloads > 0
            ? round(($successDownloads / $totalDownloads) * 100, 1)
            : 0;

        // ── Platform Distribution ───────────────────────────────
        $platformStats = DownloadLog::selectRaw('platform_name, COUNT(*) as total')
            ->groupBy('platform_name')
            ->orderByDesc('total')
            ->get();

        // ── Recent Activity (5 terbaru) ─────────────────────────
        $recentActivities = DownloadLog::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'downloadsToday',
            'downloadsThisMonth',
            'successRate',
            'platformStats',
            'recentActivities'
        ));
    }

    /**
     * User Management — daftar pengguna dengan pagination.
     */
    public function users()
    {
        $users = User::withCount('downloadLogs')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    /**
     * Toggle role user antara 'admin' dan 'user'.
     */
    public function toggleRole(User $user)
    {
        // Jangan biarkan admin mengganti role dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role diri sendiri.');
        }

        $user->role = $user->role === 'admin' ? 'user' : 'admin';
        $user->save();

        return back()->with('success', "Role {$user->name} berhasil diubah menjadi {$user->role}.");
    }

    /**
     * Toggle status aktif/suspend user.
     */
    public function toggleStatus(User $user)
    {
        // Jangan biarkan admin men-suspend dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat men-suspend diri sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'di-suspend';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /**
     * Hapus user dari database.
     */
    public function destroyUser(User $user)
    {
        // Jangan biarkan admin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun {$name} berhasil dihapus.");
    }

    /**
     * Toggle maintenance mode on/off.
     */
    public function toggleMaintenance()
    {
        $current = Setting::isMaintenanceMode();
        Setting::set('maintenance_mode', $current ? '0' : '1');

        $status = $current ? 'dinonaktifkan' : 'diaktifkan';

        return response()->json([
            'success' => true,
            'maintenance' => !$current,
            'message' => "Maintenance mode berhasil {$status}.",
        ]);
    }
}
