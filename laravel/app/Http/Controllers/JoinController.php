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

        $json = json_decode(file_get_contents($path), true);
        abort_unless(is_array($json) && isset($json['data']) && is_array($json['data']), 500, 'Location data is invalid.');

        $stateKey = $data['state_id'];
        $lgaKey = $data['lga_id'];
        $wardKey = $data['ward_id'];

        $selectedState = collect($json['data'])->firstWhere('id', $stateKey);
        $selectedLga = collect($selectedState['lga'] ?? [])->firstWhere('id', $lgaKey);
        $selectedWard = collect($selectedLga['ward'] ?? [])->firstWhere('id', $wardKey);

        abort_unless($selectedState && $selectedLga && $selectedWard, 422, 'Please select a valid state, LGA and ward combination.');

        $stateName = $selectedState['name']['en'] ?? $selectedState['name']['local'] ?? '';
        $lgaName = $selectedLga['name']['en'] ?? $selectedLga['name']['local'] ?? '';
        $wardName = $selectedWard['name']['en'] ?? $selectedWard['name']['local'] ?? '';

        $state = State::get()->first(function ($item) use ($stateName) {
            $name = Str::lower(trim($item->name));
            $target = Str::lower(trim($stateName));
            return $name === $target || $name === $target . ' state' || Str::slug($item->name) === Str::slug($stateName);
        });

        $lga = $state?->lgas()->get()->first(fn ($item) => Str::slug($item->name) === Str::slug($lgaName));
        $ward = $lga?->wards()->get()->first(fn ($item) => Str::slug($item->name) === Str::slug($wardName));

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
}
