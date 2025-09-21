@extends('layouts.app')

@section('content')
<h3>Edit Member</h3>

<form method="post" enctype="multipart/form-data" action="{{ route('members.update',$member) }}" class="grid">
  @csrf @method('put')

  <label>Name<input name="name" value="{{ old('name',$member->name) }}" required></label>
  <label>Email<input type="email" name="email" value="{{ old('email',$member->email) }}" required></label>
  <label>Phone<input name="phone" value="{{ old('phone',$member->phone) }}"></label>

  <fieldset>
    <legend>Addresses</legend>
    @foreach($member->addresses as $i => $a)
      <input type="hidden" name="addresses[{{ $i }}][id]" value="{{ $a->id }}">
      <label>Type
        <select name="addresses[{{ $i }}][address_type_id]" required>
          @foreach($addressTypes as $t)
            <option value="{{ $t->id }}" @selected($a->address_type_id==$t->id)>{{ $t->name }}</option>
          @endforeach
        </select>
      </label>
      <label>Line 1<input name="addresses[{{ $i }}][line1]" value="{{ $a->line1 }}" required></label>
      <label>Line 2<input name="addresses[{{ $i }}][line2]" value="{{ $a->line2 }}"></label>
      <label>City<input name="addresses[{{ $i }}][city]" value="{{ $a->city }}" required></label>
      <label>State<input name="addresses[{{ $i }}][state]" value="{{ $a->state }}"></label>
      <label>Postal Code<input name="addresses[{{ $i }}][postal_code]" value="{{ $a->postal_code }}"></label>
      <label>Country<input name="addresses[{{ $i }}][country]" value="{{ $a->country }}"></label>
      <hr>
    @endforeach
  </fieldset>

  <label>New Profile Image (optional) <input type="file" name="profile_image" accept=".jpg,.jpeg,.png"></label>
  <label>New Proof of Address (optional) <input type="file" name="proof_of_address" accept=".jpg,.jpeg,.png,.pdf"></label>

  <button type="submit">Save</button>
</form>

@include('members.partials.errors')
@endsection
