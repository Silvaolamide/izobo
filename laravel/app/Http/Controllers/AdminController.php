<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Registration;
use App\Models\State;
use App\Models\Lga;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function loginForm() { return view('admin.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required','email'], 'password' => ['required','string']]);
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return back()->withErrors(['email' => 'The email or password is incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('izobo_admin_id', $admin->id);
        return redirect()->route('admin.dashboard');
    }

    public function dashboard()
    {
        $total = Registration::count();
        $today = Registration::whereDate('created_at', today())->count();
        $week = Registration::where('created_at', '>=', now()->subDays(7))->count();
        $month = Registration::where('created_at', '>=', now()->subDays(30))->count();

        $byLga = Registration::query()->join('lgas','registrations.lga_id','=','lgas.id')
            ->select('lgas.name', DB::raw('COUNT(*) as total'))->groupBy('lgas.id','lgas.name')->orderByDesc('total')->get();
        $byWard = Registration::query()->join('wards','registrations.ward_id','=','wards.id')
            ->select('wards.name', DB::raw('COUNT(*) as total'))->groupBy('wards.id','wards.name')->orderByDesc('total')->get();
        $byInterest = Registration::query()->select('interest', DB::raw('COUNT(*) as total'))
            ->groupBy('interest')->orderByDesc('total')->get();
        $daily = Registration::query()->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at','>=',now()->subDays(13))->groupBy('day')->orderBy('day')->get();

        return view('admin.dashboard', compact('total','today','week','month','byLga','byWard','byInterest','daily'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('izobo_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
