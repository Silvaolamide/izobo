<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Models\Registration;
use App\Models\State;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JoinController extends Controller
{
    public function create()
    {
        $states = State::orderBy('name')->get(['id','name']);
        $defaultState = State::where('name', 'Edo State')->first();
        $defaultLga = $defaultState?->lgas()->where('name', 'Owan East')->first();
        $defaultWard = $defaultLga?->wards()->where('name', 'EMAI 1')->first();

        return view('join', compact('states', 'defaultState', 'defaultLga', 'defaultWard'));
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
            'state_id' => ['required','exists:states,id'],
            'lga_id' => ['required','exists:lgas,id'],
            'ward_id' => ['required','exists:wards,id'],
            'interest' => ['required','in:Volunteer,Campaign supporter,Community outreach,Digital/media support,Other'],
            'consent' => ['accepted'],
        ]);

        $validLocation = DB::table('lgas')->where('id', $data['lga_id'])->where('state_id', $data['state_id'])->exists()
            && DB::table('wards')->where('id', $data['ward_id'])->where('lga_id', $data['lga_id'])->exists();

        abort_unless($validLocation, 422, 'Please select a valid state, LGA and ward combination.');

        Registration::create($data);

        return back()->with('success', 'Thank you. Your registration has been received.');
    }
}
