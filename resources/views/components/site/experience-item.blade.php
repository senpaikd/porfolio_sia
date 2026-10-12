@props(['experience'])

@php
    $descriptionLines = collect(preg_split('/\R/u', trim($experience->description ?? '')) ?: [])
        ->map(fn ($line) => trim($line))
        ->filter();
    $summary = $descriptionLines
        ->reject(fn ($line) => str_starts_with($line, '- '))
        ->implode(' ');
    $responsibilities = $descriptionLines
        ->filter(fn ($line) => str_starts_with($line, '- '))
        ->map(fn ($line) => str($line)->after('- ')->toString());
@endphp

<article class="timeline-item">
    <h4 class="font-semibold">{{ $experience->role }}</h4>
    <p style="color: var(--muted)">{{ $experience->company }}</p>
    <p class="timeline-date">
        <time datetime="{{ $experience->start_date->toDateString() }}">{{ $experience->start_date->format('M Y') }}</time>
        <span aria-hidden="true">–</span>
        @if ($experience->is_current)
            <span>Present</span>
        @elseif ($experience->end_date)
            <time datetime="{{ $experience->end_date->toDateString() }}">{{ $experience->end_date->format('M Y') }}</time>
        @else
            <span>Present</span>
        @endif
    </p>
    @if ($summary !== '')
        <p class="mt-3 text-sm leading-6">{{ $summary }}</p>
    @endif
    @if ($responsibilities->isNotEmpty())
        <ul class="timeline-responsibilities">
            @foreach ($responsibilities as $responsibility)
                <li>{{ $responsibility }}</li>
            @endforeach
        </ul>
    @endif
</article>
