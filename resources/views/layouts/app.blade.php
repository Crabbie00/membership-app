<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Membership App</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body class="container">
  <nav>
    <ul><li><strong><a href="{{ route('members.index') }}">Members</a></strong></li></ul>
    <ul>
      <li><a href="{{ route('members.create') }}">Register Member</a></li>
      <li><a href="{{ route('members.export', request()->only('q','ref')) }}">Export CSV</a></li>
    </ul>
  </nav>
  @if(session('ok')) <article class="contrast">{{ session('ok') }}</article> @endif
  {{ $slot ?? '' }}
  @yield('content')
</body>
</html>
