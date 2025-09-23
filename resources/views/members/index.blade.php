@extends('layouts.app')

@section('content')
<h3>Members</h3>

<form method="get" class="grid">
  <input type="text" name="q" value="{{ $q }}" placeholder="Search name/email">
  <input type="text" name="ref" value="{{ $ref }}" placeholder="Search referral (code/referrer)">
  <button type="submit">Search</button>
</form>

<table>
  <thead>
    <tr>
      <th>User Id</th><th>Name</th><th>Email</th><th>Referral Code</th><th>Referrer</th><th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($members as $m)
    <tr>
      <td>{{ $m->id }}</td>
      <td>{{ $m->name }}</td>
      <td>{{ $m->email }}</td>
      <td>{{ $m->referral_code }}</td>
      <td>{{ optional($m->referrer)->email }}</td>
      <td>
        <a href="{{ route('members.show',$m) }}">View</a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

{{ $members->links('pagination::bootstrap-4') }}
@endsection
