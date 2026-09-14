@extends('layout')
@section('title','Join the IZOBO Movement | Owan East')
@section('content')
<main class="shell" style="padding:35px 0 80px">
  <div style="max-width:760px;margin:auto">
    <a href="/" style="font-size:13px;font-weight:800;color:var(--green)">← Back to campaign</a>
    <div class="card" style="padding:clamp(24px,5vw,52px);margin-top:18px">
      <p style="font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:var(--bright);font-weight:800;margin:0 0 10px">IZOBO • OWAN EAST</p>
      <h1 style="font:800 clamp(38px,7vw,68px)/.95 Manrope;margin:0 0 18px;letter-spacing:-.06em;color:var(--green)">Join the movement<span style="color:var(--red)">.</span></h1>
      <p class="muted" style="max-width:620px">Register your interest in supporting the IZOBO campaign. Choose your location below so the campaign team can understand where registered supporters are coming from.</p>

      @if(session('success'))<div class="notice" role="status">{{ session('success') }}</div>@endif
      @if($errors->any())<div class="notice" style="background:#fff1f1;color:#8d2026" role="alert">Please check the highlighted information and try again.</div>@endif

      <form method="POST" action="{{ route('join.store') }}" style="margin-top:28px">
        @csrf
        <div class="grid">
          <div class="field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" autocomplete="name" required>@error('full_name')<div class="error">{{ $message }}</div>@enderror</div>
          <div class="field"><label for="phone">Phone number</label><input id="phone" name="phone" value="{{ old('phone') }}" type="tel" autocomplete="tel" required>@error('phone')<div class="error">{{ $message }}</div>@enderror</div>
        </div>
        <div class="field"><label for="email">Email <span style="font-weight:400;color:var(--muted)">(optional)</span></label><input id="email" name="email" value="{{ old('email') }}" type="email" autocomplete="email">@error('email')<div class="error">{{ $message }}</div>@enderror</div>

        <div style="margin-top:30px;padding-top:25px;border-top:1px solid var(--line)"><p style="font-weight:800;margin:0">Your location</p><p class="muted" style="font-size:13px;margin:5px 0 0">Select each option. The next list updates automatically.</p></div>
        <div class="grid">
          <div class="field"><label for="state_id">State</label><select id="state_id" name="state_id" required><option value="">Loading states…</option></select></div>
          <div class="field"><label for="lga_id">LGA</label><select id="lga_id" name="lga_id" required disabled><option value="">Select LGA</option></select></div>
        </div>
        <div class="field"><label for="ward_id">Ward</label><select id="ward_id" name="ward_id" required disabled><option value="">Select Ward</option></select></div>

        <div class="field"><label for="interest">How would you like to participate?</label><select id="interest" name="interest" required><option value="">Select one</option>@foreach(['Volunteer','Campaign supporter','Community outreach','Digital/media support','Other'] as $interest)<option @selected(old('interest')===$interest)>{{ $interest }}</option>@endforeach</select></div>
        <label style="display:flex;gap:10px;align-items:flex-start;margin-top:20px;font-size:13px;line-height:1.5"><input type="checkbox" name="consent" value="1" required style="margin-top:3px"> I agree that the campaign team may use the information I submit for campaign coordination and communication.</label>
        @error('consent')<div class="error">{{ $message }}</div>@enderror
        <button class="btn" type="submit" style="width:100%;margin-top:24px">Register my interest</button>
      </form>
    </div>
  </div>
</main>
@endsection
@push('scripts')
<script>
const state = document.getElementById('state_id');
const lga = document.getElementById('lga_id');
const ward = document.getElementById('ward_id');

let locations = [];

function label(value) {
  return value.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function resetSelect(select, text) {
  select.innerHTML = '';
  const option = document.createElement('option');
  option.value = '';
  option.textContent = text;
  select.appendChild(option);
}

function populate(select, items, getValue, getLabel) {
  items.forEach(item => {
    const option = document.createElement('option');
    option.value = getValue(item);
    option.textContent = getLabel(item);
    select.appendChild(option);
  });
}

function populateLgas(stateKey, selectedLga = '') {
  const selectedState = locations.find(item => item.state === stateKey);
  resetSelect(lga, 'Select LGA');
  resetSelect(ward, 'Select Ward');
  ward.disabled = true;

  if (!selectedState) {
    lga.disabled = true;
    return;
  }

  populate(lga, selectedState.lgas, item => item.lga, item => label(item.lga));
  lga.disabled = false;

  if (selectedLga && selectedState.lgas.some(item => item.lga === selectedLga)) {
    lga.value = selectedLga;
    populateWards(selectedLga, @json(old('ward_id')));
  }
}

function populateWards(lgaKey, selectedWard = '') {
  const selectedState = locations.find(item => item.state === state.value);
  const selectedLga = selectedState?.lgas.find(item => item.lga === lgaKey);
  resetSelect(ward, 'Select Ward');

  if (!selectedLga) {
    ward.disabled = true;
    return;
  }

  populate(ward, selectedLga.wards, item => item, item => label(item));
  ward.disabled = false;

  if (selectedWard && selectedLga.wards.includes(selectedWard)) {
    ward.value = selectedWard;
  }
}

state.addEventListener('change', () => populateLgas(state.value));
lga.addEventListener('change', () => populateWards(lga.value));

async function loadLocations() {
  try {
    const response = await fetch('/data/nigeria-locations.json', {
      headers: { Accept: 'application/json' },
      cache: 'no-store'
    });

    if (!response.ok) throw new Error('Unable to load location data');

    locations = await response.json();

    resetSelect(state, 'Select State');
    populate(state, locations, item => item.state, item => label(item.state));
    state.disabled = false;

    const oldState = @json(old('state_id'));
    const oldLga = @json(old('lga_id'));
    const oldWard = @json(old('ward_id'));

    const defaultState = oldState || 'edo';
    const defaultLga = oldLga || 'owan-east';
    const defaultWard = oldWard || 'emai-1';

    if (locations.some(item => item.state === defaultState)) {
      state.value = defaultState;
      populateLgas(defaultState, defaultLga);
    }
  } catch (error) {
    console.error(error);
    resetSelect(state, 'Unable to load states');
    resetSelect(lga, 'Unable to load LGAs');
    resetSelect(ward, 'Unable to load wards');
    state.disabled = true;
    lga.disabled = true;
    ward.disabled = true;
  }
}

loadLocations();
</script>
@endpush
