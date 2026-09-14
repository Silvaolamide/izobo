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
          <div class="field"><label for="state_id">State</label><select id="state_id" name="state_id" required>@foreach($states as $state)<option value="{{ $state->id }}" @selected(old('state_id',$defaultState?->id)==$state->id)>{{ $state->name }}</option>@endforeach</select></div>
          <div class="field"><label for="lga_id">LGA</label><select id="lga_id" name="lga_id" required><option value="{{ $defaultLga?->id }}">{{ $defaultLga?->name ?? 'Select LGA' }}</option></select></div>
        </div>
        <div class="field"><label for="ward_id">Ward</label><select id="ward_id" name="ward_id" required><option value="{{ $defaultWard?->id }}">{{ $defaultWard?->name ?? 'Select Ward' }}</option></select></div>

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
const state=document.getElementById('state_id'), lga=document.getElementById('lga_id'), ward=document.getElementById('ward_id');
async function load(url, select, placeholder){select.innerHTML=`<option value="">${placeholder}</option>`;select.disabled=true;try{const r=await fetch(url,{headers:{Accept:'application/json'}});const items=await r.json();items.forEach(x=>{const o=document.createElement('option');o.value=x.id;o.textContent=x.name;select.appendChild(o)});select.disabled=false}catch(e){select.innerHTML='<option value="">Unable to load options</option>'}}
state.addEventListener('change',()=>load(`/locations/${state.value}/lgas`,lga,'Select LGA').then(()=>{ward.innerHTML='<option value="">Select Ward</option>';ward.disabled=true}));
lga.addEventListener('change',()=>load(`/locations/${lga.value}/wards`,ward,'Select Ward'));
</script>
@endpush
