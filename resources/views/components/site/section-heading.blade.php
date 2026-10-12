@props(['eyebrow', 'title', 'description' => null])
<div class="section-heading"><p class="eyebrow">{{ $eyebrow }}</p><h2 class="section-title">{{ $title }}</h2>@if ($description)<p class="lead-copy">{{ $description }}</p>@endif</div>
