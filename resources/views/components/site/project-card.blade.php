@props(['project'])
<article class="glass-card project-card group" data-tilt-card>
    @if ($project->main_image)
        <img src="{{ asset('storage/'.$project->main_image) }}" alt="{{ $project->title }} project preview" width="960" height="640" loading="lazy" class="project-card-image">
        <div class="project-card-scrim" aria-hidden="true"></div>
    @endif
    <div class="mt-auto">
        <div class="project-tag">
            @if ($project->technologies->isNotEmpty())
                {{ $project->technologies->take(3)->pluck('name')->implode(' · ') }}
            @else
                {{ $project->status->label() }}
            @endif
        </div>
        <h3 class="text-xl font-semibold tracking-tight"><a class="after:absolute after:inset-0" href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></h3>
        <p class="mt-3 text-sm leading-6" style="color: var(--muted)">{{ $project->short_description }}</p>
        <div class="mt-5 flex items-center justify-between gap-3 font-mono text-[0.68rem] uppercase tracking-wider" style="color: var(--muted)">
            <span>{{ $project->status->label() }}</span>
            @if ($project->completion_date)<time datetime="{{ $project->completion_date->toDateString() }}">{{ $project->completion_date->format('Y') }}</time>@endif
        </div>
    </div>
</article>
