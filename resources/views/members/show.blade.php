@extends('layouts.app')

@section('content')

<style>
/* container */
.member-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin: 18px 0;
}

/* avatar */
.avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  display: inline-block;
  border: 3px solid #e6e6e6;
  background-color: #d8d8d8; /* placeholder color */
}

/* avatar img fits */
.avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* addresses grid: responsive cards */
.addresses {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 18px;
  margin-top: 20px;
}

/* single address card */
.address-card {
  display: flex;
  gap: 14px;
  align-items: flex-start;
  padding: 14px;
  border: 1px solid #eee;
  border-radius: 10px;
  background: #5d6b89;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}

/* address text area */
.address-card .addr-text {
  flex: 1 1 auto;
  min-width: 0; /* allows text to truncate/flex correctly */
}

/* address title */
.addr-title {
  font-weight: 600;
  margin-bottom: 6px;
}

/* proof thumbnail */
.proof-thumb {
  width: 120px;
  height: 120px;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #ddd;
  flex-shrink: 0;
  background: #f6f6f6;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* proof image fit */
.proof-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* small meta row */
.meta-row {
  color: #666;
  font-size: 0.9rem;
  margin-top: 8px;
}

/* buttons row */
.card-actions {
  margin-top: 10px;
  display:flex;
  gap:8px;
}

.btn-primary {
  display: inline-block;
  padding: 10px 24px;
  background-color: #2563eb;
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.btn-primary:hover {
  background-color: #1d4ed8;
}

/* ensure good spacing on small screens */
@media (max-width: 420px) {
  .proof-thumb { width: 90px; height: 90px; }
  .avatar { width: 120px; height: 120px; }
}
</style>

{{-- Member header (centered avatar + info) --}}
<div class="member-header">
  <h2>{{ $member->name }}</h2>
  <div class="avatar" role="img" aria-label="Profile picture of {{ $member->name }}">
    @if($member->profileImage)
      <img src="{{ $member->profileImage->url }}" alt="Profile picture of {{ $member->name }}">
    @else
      {{-- optional initials inside placeholder --}}
      <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#666;font-weight:600;">
        {{ Str::upper(substr($member->name,0,1) ?? 'U') }}
      </div>
    @endif
  </div>

  <div style="text-align:center;">
    <div>{{ $member->email }}</div>
    <div class="meta-row">{{ $member->phone ?? '—' }}</div>
  </div>
</div>

<hr style="margin: 24px 0; border: none; border-top: 1px solid #f0f0f0;">

<h3>Addresses</h3>

<div class="addresses">
  @forelse($member->addresses as $address)
    <div class="address-card">
      {{-- address text --}}
      <div class="addr-text">
        <div class="addr-title">{{ $address->type->name ?? 'Address' }}</div>
        <div>
          {{ $address->line1 }}<br>
          @if($address->line2) {{ $address->line2 }}<br> @endif
          {{ $address->city }} @if($address->state), {{ $address->state }}@endif
          @if($address->postal_code) — {{ $address->postal_code }}@endif<br>
          <small class="meta-row">{{ $address->country }}</small>
        </div>

      </div>

      {{-- proof thumbnail (clickable to open full file) --}}
      <div>
        @if($address->proof)
          <a href="{{ $address->proof->url }}" target="_blank" class="proof-thumb" title="Open proof of address">
            <img src="{{ $address->proof->url }}" alt="Proof of address">
          </a>
        @else
          <div class="proof-thumb">
            <span style="color:#999;font-size:0.9rem;">No proof</span>
          </div>
        @endif
      </div>
    </div>
  @empty
    <p>No addresses found for this member.</p>
  @endforelse
</div>

{{-- Centered Edit button at bottom --}}
<div style="margin-top: 40px; text-align: center;">
    <a href="{{ route('members.edit', $member) }}" class="btn-primary">
        ✏️ Edit Member
    </a>
</div>

@include('members.partials.errors')
@endsection