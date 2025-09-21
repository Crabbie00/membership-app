@extends('layouts.app')

@section('content')
<h3>Register Member</h3>

<form method="post" enctype="multipart/form-data" action="{{ route('members.store') }}" class="grid">
  @csrf
  <label>Name<input name="name" value="{{ old('name') }}" required></label>
  <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
  <label>Phone<input name="phone" value="{{ old('phone') }}"></label>

  <label>Referral Code (optional)
    <input name="referral_code_input" value="{{ old('referral_code_input') }}" placeholder="Enter an existing member's code">
  </label>

  <fieldset>
    <legend>Addresses</legend>
    <div id="addresses">
      <div class="address">
        <label>Type
          <select name="addresses[0][address_type_id]" required>
            @foreach($addressTypes as $t)
              <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
          </select>
        </label>
        <label>Line 1<input name="addresses[0][line1]" required></label>
        <label>Line 2<input name="addresses[0][line2]"></label>
        <label>City<input name="addresses[0][city]" required></label>
        <label>State<input name="addresses[0][state]"></label>
        <label>Postal Code<input name="addresses[0][postal_code]"></label>
        <label>Country<input name="addresses[0][country]" value="MY"></label>
      </div>
    </div>
  </fieldset>

  <label>Profile Image (jpg/png, ≤2MB) <input type="file" name="profile_image" accept=".jpg,.jpeg,.png"></label>
  <label>Proof of Address (jpg/png/pdf, ≤4MB) <input type="file" name="proof_of_address" accept=".jpg,.jpeg,.png,.pdf"></label>

  <button type="submit">Create</button>
</form>

@include('members.partials.errors')
@endsection
