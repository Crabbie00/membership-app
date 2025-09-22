@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp

<link rel="stylesheet" href="{{ asset('css/member-edit.css') }}"> {{-- optional external stylesheet --}}

<style>
/* ---- MEMBER EDIT PAGE (class-based) ---- */

/* page container */
.edit-container {
  max-width: 1200px;
  margin: 18px auto;
  padding: 20px;
  background: #2a3140;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(13, 38, 76, 0.04);
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* header */
.edit-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}
.edit-header h1 { font-size: 1.25rem; margin:0; }

/* layout: two columns */
.edit-body {
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 20px;
}

/* fallback for small screens */
@media (max-width: 820px) {
  .edit-body { grid-template-columns: 1fr; }
}

/* left form column */
.member-form { padding-right: 4px; }
.field-row { display:flex; gap:12px; margin-bottom:12px; }
.field { flex:1; display:flex; flex-direction:column; }
.field label { font-size:0.85rem; margin-bottom:6px; color:#fff; }
.field input[type="text"],
.field input[type="email"],
.field input[type="tel"],
.field select,
.field textarea {
  padding:10px 12px;
  border:1px solid #e6e6e6;
  border-radius:8px;
  font-size:0.95rem;
  background:#5d6b89;
}

/* addresses */
.addresses { margin-top:14px; display:flex; flex-direction:column; gap:12px; }
.address-card {
  gap:12px; align-items:flex-start;
  padding:12px; border:1px solid #f0f0f0; border-radius:8px; background:#2a3140;
}
.address-left { flex:1; }
.address-right { flex-shrink:0; display:flex; flex-direction:column; gap:8px; align-items:center; }

/* small helper text */
.helper { font-size:0.8rem; color:#666; margin-top:6px; }

/* right column (avatar + actions) */
.side-panel {
  position:relative;
  padding:12px;
  border:1px dashed #f0f0f0;
  border-radius:10px;
  text-align:center;
}
.avatar-box {
  width:160px; height:160px; margin:0 auto 10px; border-radius:50%; overflow:hidden; background:#dfe6ee;
  display:flex; align-items:center; justify-content:center; border:3px solid #eef4fb;
}
.avatar-box img { width:100%; height:100%; object-fit:cover; display:block; }

/* file input custom */
.input-file {
  display:block;
  margin:1px;
  width:100%;
  padding:8px 10px;
  border-radius:8px;
  border:1px solid #e6e6e6;
  background:#fafafa;
}
.small-btn {
  display:inline-block;
  padding:8px 12px;
  border-radius:8px;
  border:none;
  background:#f3f4f6;
  cursor:pointer;
  font-weight:600;
  color:#111827;
}
.small-btn.danger { background:#fee2e2; color:#b91c1c; }

/* primary action */
.actions { margin-top:16px; display:flex; gap:10px; justify-content:center; }
.btn-primary {
  display:inline-block; padding:10px 18px; border-radius:10px; background:#2563eb; color:#fff; text-decoration:none;
  font-weight:700; border:none; cursor:pointer;
}
.btn-ghost { background:transparent; padding:10px 16px; border-radius:8px; }

/* responsive address proof thumbnail */
.proof-thumb { height:240px; border-radius:6px; overflow:hidden; border:1px solid #e8e8e8; display:flex; align-items:center; justify-content:center; background:#fafafa; }
.proof-thumb img { width:100%; height:100%; object-fit:cover; display:block; }

/* small text link */
.link-muted { color:#2563eb; text-decoration:underline; font-size:0.9rem; }

/* error list */
.errors { background:#fff4f4; color:#7f1d1d; border:1px solid #fee2e2; padding:10px; border-radius:8px; margin-bottom:12px; }
</style>

<div class="edit-container">

  <div class="edit-header">
    <h1>Edit Member</h1>
    <div class="meta">
      <div style="font-size:0.85rem;color:#666">Member #{{ $member->id }}</div>
      <div style="font-size:0.75rem;color:#999">Created: {{ $member->created_at->format('d M Y') }}</div>
    </div>
  </div>

  @if ($errors->any())
    <div class="errors">
      <strong>There were some problems with your input:</strong>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
    @csrf @method('put')
    
    <div class="edit-body">
      {{-- LEFT: form fields --}}
      <div class="member-form">
        <div class="field-row">
          <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name',$member->name) }}" required>
          </div>
          <div class="field">
            <label for="phone">Phone</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone',$member->phone) }}">
          </div>
        </div>

        <div class="field-row">
          <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email',$member->email) }}" required>
          </div>
          <div class="field">
            <label for="referral_code">Referral code</label>
            <input id="referral_code" name="referral_code" type="text" value="{{ old('referral_code',$member->referral_code) }}" readonly>
          </div>
        </div>

        {{-- Addresses --}}
        <div class="addresses">
          @foreach($member->addresses as $i => $address)
            <div class="address-card">
              <div class="address-left">
                <div class="field-row">
                  <div class="field">
                    <label>Type</label>
                    <select name="addresses[{{ $i }}][address_type_id]">
                      @foreach($addressTypes as $t)
                        <option value="{{ $t->id }}" @selected($t->id == $address->address_type_id)>{{ $t->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="field">
                    <label>Postal Code</label>
                    <input type="text" name="addresses[{{ $i }}][postal_code]" value="{{ old("addresses.$i.postal_code",$address->postal_code) }}">
                  </div>
                </div>

                <div class="field">
                  <label>Line 1</label>
                  <input type="text" name="addresses[{{ $i }}][line1]" value="{{ old("addresses.$i.line1",$address->line1) }}">
                </div>
                <div class="field" style="margin-top:8px;">
                  <label>Line 2</label>
                  <input type="text" name="addresses[{{ $i }}][line2]" value="{{ old("addresses.$i.line2",$address->line2) }}">
                </div>
                <div class="field-row" style="margin-top:8px;">
                  <div class="field">
                    <label>City</label>
                    <input type="text" name="addresses[{{ $i }}][city]" value="{{ old("addresses.$i.city",$address->city) }}">
                  </div>
                  <div class="field">
                    <label>State</label>
                    <input type="text" name="addresses[{{ $i }}][state]" value="{{ old("addresses.$i.state",$address->state) }}">
                  </div>
                </div>

                {{-- hidden id to allow update --}}
                <input type="hidden" name="addresses[{{ $i }}][id]" value="{{ $address->id }}">
                <div class="helper">You can upload a proof image for this address on the right.</div>
              </div>

              <div class="address-right">
                {{-- Show existing proof --}}
                <div class="proof-thumb" id="proof-preview">
                  @if($address->proof)
                    <a href="{{ $address->proof->url }}" target="_blank" title="Open proof">
                      <img src="{{ $address->proof->url }}" alt="Proof">
                    </a>
                  @endif
                </div>
                {{-- file input --}}
                <input id="proof_of_address" class="input-file" type="file" name="proof_of_address" accept=".jpg,.jpeg,.png,.pdf" onchange="previewProof(event)">
                
              </div>
            </div>
          @endforeach
        </div>

      </div>

      {{-- RIGHT: avatar & actions --}}
      <aside class="side-panel">
        <div class="avatar-box" id="avatarPreview">
          @if($member->profileImage)
            <img src="{{ $member->profileImage->url }}" alt="Profile image">
          @else
            <div style="font-size:1.6rem;color:#475569;font-weight:600;">
              {{ Str::upper(substr($member->name,0,1) ?? 'U') }}
            </div>
          @endif
        </div>

        <label for="profile_image" style="display:block;margin-bottom:8px;font-weight:600;">Profile image</label>
        <input id="profile_image" class="input-file" type="file" name="profile_image" accept=".jpg,.jpeg,.png,.pdf" onchange="previewAvatar(event)">

        <div class="actions">
          <a href="{{ route('members.index') }}" class="btn-ghost">Back</a>
          <button type="submit" class="btn-primary" onclick="setTimeout(function() { this.form.submit(); }.bind(this), 100);">Save changes</button>
        </div>
      </aside>
    </div>
  </form>
</div>

{{-- JS: preview avatar + proof file thumbnails --}}
<script>
function previewAvatar(e){
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev){
    const box = document.getElementById('avatarPreview');
    box.innerHTML = '<img src="' + ev.target.result + '" alt="Avatar preview">';
  };
  reader.readAsDataURL(file);
}

function previewProof(e){
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = function(ev){
    const box = document.getElementById('proof-preview');
    box.innerHTML = '<img src="' + ev.target.result + '" alt="Proof preview">';
  };
  reader.readAsDataURL(file);
}
</script>

@include('members.partials.errors')
@endsection
