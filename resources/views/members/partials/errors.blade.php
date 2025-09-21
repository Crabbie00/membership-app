@if ($errors->any())
<article class="contrast">
  <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</article>
@endif
