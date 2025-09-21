@extends('layouts.app')

@section('content')
<h3>Member #{{ $member->id }}</h3>
<p><strong>Name:</strong> {{ $member->name }}</p>
<p><strong>Email:</strong> {{ $member->email }}</p>
<p><strong>Phone:</strong> {{ $member->phone }}</p>
<p><strong>Referral Code:</strong> {{ $member->referral_code }}</p>
<p><strong>Referrer:</strong> {{ optional($member->referrer)->email ?? '—' }}</p>

<h4>Profile / Documents</h4>
<ul>
  @foreach($member->documents as $doc)
    <li>{{ strtoupper($doc->type) }} — <a href="{{ $doc->url }}" target="_blank">{{ $doc->original_name ?? basename($doc->file_path) }}</a></li>
  @endforeach
</ul>

<h4>Addresses</h4>
@foreach($member->addresses as $a)
  <article>
    <header><strong>{{ $a->type->name }}</strong></header>
    <p>{{ $a->line1 }} {{ $a->line2 }}<br>{{ $a->city }}, {{ $a->state }} {{ $a->postal_code }}<br>{{ $a->country }}</p>
    <ul>
      @foreach($a->documents as $doc)
        <li>{{ strtoupper($doc->type) }} — <a href="{{ $doc->url }}" target="_blank">{{ $doc->original_name ?? basename($doc->file_path) }}</a></li>
      @endforeach
    </ul>
  </article>
@endforeach

<p>
  <a href="{{ route('members.edit',$member) }}">Edit</a> |
  <form method="post" action="{{ route('members.destroy',$member) }}" style="display:inline">
    @csrf @method('delete')
    <button onclick="return confirm('Delete member?')">Delete</button>
  </form>
</p>
@endsection
