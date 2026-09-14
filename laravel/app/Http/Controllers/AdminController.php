<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CampaignSetting;
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

        $whatsappGroupLink = CampaignSetting::getValue('whatsapp_group_link', '');

        return view('admin.dashboard', compact('total','today','week','month','byLga','byWard','byInterest','daily','whatsappGroupLink'));
    }

    public function registrations(Request $request)
    {
        $query = Registration::query()->with(['state', 'lga', 'ward']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->input('state_id'));
        }

        if ($request->filled('lga_id')) {
            $query->where('lga_id', $request->input('lga_id'));
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->input('ward_id'));
        }

        if ($request->filled('interest')) {
            $query->where('interest', $request->input('interest'));
        }

        $registrations = $query->latest('created_at')->paginate(25)->withQueryString();
        $states = State::query()->orderBy('name')->get(['id', 'name']);
        $lgas = Lga::query()->orderBy('name')->get(['id', 'name', 'state_id']);
        $wards = Ward::query()->orderBy('name')->get(['id', 'name', 'lga_id']);

        return view('admin.registrations', compact('registrations', 'states', 'lgas', 'wards'));
    }

    public function exportRegistrations(Request $request)
    {
        $query = Registration::query()->with(['state', 'lga', 'ward']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        foreach (['state_id', 'lga_id', 'ward_id', 'interest'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $registrations = $query->latest('created_at')->get();
        $filename = 'izobo-registrations-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($registrations) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Full Name', 'Phone', 'Email', 'State', 'LGA', 'Ward',
                'Interest', 'Consent', 'Registered At',
            ]);

            foreach ($registrations as $registration) {
                fputcsv($handle, [
                    $registration->id,
                    $registration->full_name,
                    $registration->phone,
                    $registration->email,
                    $registration->state?->name,
                    $registration->lga?->name,
                    $registration->ward?->name,
                    $registration->interest,
                    $registration->consent ? 'Yes' : 'No',
                    optional($registration->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'whatsapp_group_link' => [
                'nullable',
                'url',
                'max:2048',
                'regex:/^https:\/\/(chat\.whatsapp\.com\/|wa\.me\/)/i',
            ],
        ], [
            'whatsapp_group_link.regex' => 'Please enter a valid WhatsApp group invitation link.',
        ]);

        CampaignSetting::setValue('whatsapp_group_link', trim($data['whatsapp_group_link'] ?? ''));

        return redirect()->route('admin.dashboard')->with('settings_saved', 'Campaign settings updated successfully.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('izobo_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
