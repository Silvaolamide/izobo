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

      @if(session('registration_complete'))
        <div style="margin-top:30px;padding:32px;border-radius:18px;background:#f0f8f2;border:1px solid #cde5d2">
          <div style="font-size:42px;line-height:1;margin-bottom:18px">✓</div>
          <h2 style="font:800 30px/1.1 Manrope;margin:0 0 12px;color:var(--green)">You're already registered.</h2>
          <p class="muted" style="margin:0;max-width:580px">Thank you for registering your interest in supporting the IZOBO campaign. Your registration has been received by the campaign team.</p>
          @if(!empty($whatsappGroupLink))
            <a href="{{ $whatsappGroupLink }}" target="_blank" rel="noopener noreferrer" class="btn" style="display:inline-flex;justify-content:center;align-items:center;text-decoration:none;margin-top:24px">Join the IZOBO WhatsApp Group</a>
          @endif
        </div>
      @else
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
          <div class="field"><label for="interest">How would you like to participate?</label><select id="interest" name="interest" required><option value="">Select one</option>@foreach(['Volunteer','Campaign supporter','Community outreach','Digital/media support','Ward Coordinator','Polling Unit Canverser','Other'] as $interest)<option @selected(old('interest')===$interest)>{{ $interest }}</option>@endforeach</select></div>
          <label style="display:flex;gap:10px;align-items:flex-start;margin-top:20px;font-size:13px;line-height:1.5"><input type="checkbox" name="consent" value="1" required style="margin-top:3px"> I agree that the campaign team may use the information I submit for campaign coordination and communication.</label>
          @error('consent')<div class="error">{{ $message }}</div>@enderror
          <button class="btn" type="submit" style="width:100%;margin-top:24px">Register my interest</button>
        </form>
      @endif
    </div>
  </div>
</main>
@endsection
@push('scripts')
<script>
const state = document.getElementById('state_id');
const lga = document.getElementById('lga_id');
const ward = document.getElementById('ward_id');
let states = [];
function resetSelect(select, text) { select.innerHTML = ''; const option = document.createElement('option'); option.value = ''; option.textContent = text; select.appendChild(option); }
function populate(select, items, getValue, getLabel) { items.forEach(item => { const option = document.createElement('option'); option.value = getValue(item); option.textContent = getLabel(item); select.appendChild(option); }); }
function nameOf(item) { return item?.name?.en || item?.name?.local || ''; }
function populateLgas(stateId, selectedLga = '', selectedWard = '') { const selectedState = states.find(item => item.id === stateId); resetSelect(lga, 'Select LGA'); resetSelect(ward, 'Select Ward'); ward.disabled = true; if (!selectedState) { lga.disabled = true; return; } const lgas = selectedState.lga || []; populate(lga, lgas, item => item.id, item => nameOf(item)); lga.disabled = false; if (selectedLga && lgas.some(item => item.id === selectedLga)) { lga.value = selectedLga; populateWards(selectedLga, selectedWard); } }
function populateWards(lgaId, selectedWard = '') { const selectedState = states.find(item => item.id === state.value); const selectedLga = selectedState?.lga?.find(item => item.id === lgaId); resetSelect(ward, 'Select Ward'); if (!selectedLga) { ward.disabled = true; return; } const wards = selectedLga.ward || []; populate(ward, wards, item => item.id, item => nameOf(item)); ward.disabled = false; if (selectedWard && wards.some(item => item.id === selectedWard)) ward.value = selectedWard; }
state.addEventListener('change', () => populateLgas(state.value));
lga.addEventListener('change', () => populateWards(lga.value));
async function loadLocations() { try { const response = await fetch('/data/nigeria-locations.json', {headers:{Accept:'application/json'},cache:'no-store'}); if (!response.ok) throw new Error('Unable to load location data'); const json = await response.json(); states = Array.isArray(json) ? json : json.data; if (!Array.isArray(states) || !states.length) throw new Error('Location data has an invalid structure'); resetSelect(state, 'Select State'); populate(state, states, item => item.id, item => nameOf(item)); state.disabled = false; const oldState = @json(old('state_id')); const oldLga = @json(old('lga_id')); const oldWard = @json(old('ward_id')); let defaultState = oldState, defaultLga = oldLga, defaultWard = oldWard; if (!defaultState) { const edo = states.find(item => nameOf(item).toLowerCase() === 'edo'); defaultState = edo?.id || ''; if (edo) { const owanEast = (edo.lga || []).find(item => nameOf(item).toLowerCase() === 'owan east'); defaultLga = owanEast?.id || ''; const emai = (owanEast?.ward || []).find(item => nameOf(item).toLowerCase() === 'emai 1'); defaultWard = emai?.id || ''; } } if (states.some(item => item.id === defaultState)) { state.value = defaultState; populateLgas(defaultState, defaultLga, defaultWard); } } catch (error) { console.error(error); resetSelect(state, 'Unable to load states'); resetSelect(lga, 'Unable to load LGAs'); resetSelect(ward, 'Unable to load wards'); state.disabled = true; lga.disabled = true; ward.disabled = true; } }
loadLocations();
</script>
@endpush
