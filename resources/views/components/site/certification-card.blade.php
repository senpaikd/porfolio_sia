@props(['certification'])
<article class="glass-card flex h-full flex-col">
    <p class="eyebrow">{{ $certification->issuer }}</p>
    <h3 class="mt-4 text-xl font-semibold tracking-tight">{{ $certification->title }}</h3>
    <div class="mt-auto pt-7">
        @if ($certification->completion_date)
            <time class="font-mono text-xs uppercase tracking-wider" style="color: var(--muted)" datetime="{{ $certification->completion_date->toDateString() }}">
                Completed {{ $certification->completion_date->format('M Y') }}
            </time>
        @endif
        @if ($certification->certificate_path)
            <a class="mt-4 inline-flex font-semibold" style="color: var(--cyan)" href="{{ asset('storage/'.$certification->certificate_path) }}" target="_blank" rel="noopener noreferrer">
                View certificate <span class="ml-2" aria-hidden="true">↗</span>
            </a>
        @endif
    </div>
</article>
