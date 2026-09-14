<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\Registration;
use App\Models\State;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JoinController extends Controller
{
    public function create()
    {
        return view('join');
    }

    public function lgas(State $state)
    {
        return response()->json($state->lgas()->orderBy('name')->get(['id','name']));
    }

    public function wards(Lga $lga)
    {
        return response()->json($lga->wards()->orderBy('name')->get(['id','name']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => ['required','string','min:2','max:120'],
            'phone' => ['required','string','min:7','max:30'],
            'email' => ['nullable','email','max:190'],
            'state_id' => ['required','string','max:100'],
            'lga_id' => ['required','string','max:100'],
            'ward_id' => ['required','string','max:100'],
            'interest' => ['required','in:Volunteer,Campaign supporter,Community outreach,Digital/media support,Other'],
            'consent' => ['accepted'],
        ]);

        $path = public_path('data/nigeria-locations.json');
        abort_unless(is_file($path), 500, 'Location data is unavailable.');

        $locations = json_decode(file_get_contents($path), true);
        abort_unless(is_array($locations), 500, 'Location data is invalid.');

        $stateKey = $data['state_id'];
        $lgaKey = $data['lga_id'];
        $wardKey = $data['ward_id'];

        $selectedState = collect($locations)->firstWhere('state', $stateKey);
        $selectedLga = collect($selectedState['lgas'] ?? [])->firstWhere('lga', $lgaKey);
        $validWard = in_array($wardKey, $selectedLga['wards'] ?? [], true);

        abort_unless($selectedState && $selectedLga && $validWard, 422, 'Please select a valid state, LGA and ward combination.');

        $state = State::whereRaw('LOWER(name) = ?', [strtolower(str_replace('-', ' ', $stateKey))])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($this->humanizeLocationKey($stateKey))])
            ->first();

        if (!$state) {
            $state = State::get()->first(fn ($item) => Str::slug($item->name) === $stateKey);
        }

        $lga = $state?->lgas()->get()->first(fn ($item) => Str::slug($item->name) === $lgaKey);
        $ward = $lga?->wards()->get()->first(fn ($item) => Str::slug($item->name) === $wardKey);

        abort_unless($state && $lga && $ward, 422, 'The selected location is not available in the registration database.');

        $data['state_id'] = $state->id;
        $data['lga_id'] = $lga->id;
        $data['ward_id'] = $ward->id;

        $validLocation = DB::table('lgas')->where('id', $data['lga_id'])->where('state_id', $data['state_id'])->exists()
            && DB::table('wards')->where('id', $data['ward_id'])->where('lga_id', $data['lga_id'])->exists();

        abort_unless($validLocation, 422, 'Please select a valid state, LGA and ward combination.');

        Registration::create($data);

        return back()->with('success', 'Thank you. Your registration has been received.');
    }

    private function humanizeLocationKey(string $key): string
    {
        return str_replace('-', ' ', $key);
    }
}
