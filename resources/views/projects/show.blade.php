<x-layouts.app :settings="$settings" :seo="$seo">
    <article class="pt-20">
        <header class="section-shell pb-10">
            <a href="{{ route('projects.index') }}" class="eyebrow inline-flex hover:underline">← Back to all projects</a>
            <div class="mt-10 grid gap-10 lg:grid-cols-[1.3fr_.7fr] lg:items-end">
                <div>
                    <p class="project-tag">{{ $project->status->label() }}</p>
                    <h1 class="display-title mt-3">{{ $project->title }}</h1>
                    <p class="lead-copy">{{ $project->short_description }}</p>
                </div>
                <div class="flex flex-wrap gap-3 lg:justify-end">
                    @if ($project->github_url)<a class="button-secondary" href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer">View repository</a>@endif
                    @if ($project->live_url)<a class="button-primary" href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer">Open live demo</a>@endif
                </div>
            </div>
        </header>

        @if ($project->main_image)
            <div class="content-wrap"><img src="{{ asset('storage/'.$project->main_image) }}" alt="{{ $project->title }} project preview" width="1440" height="960" class="aspect-[3/2] w-full rounded-[24px] border object-cover" style="border-color: var(--line)"></div>
        @endif

        <div class="section-shell grid gap-12 lg:grid-cols-[.35fr_.65fr]">
            <aside>
                <p class="eyebrow">Built with</p>
                <div class="mt-4 flex flex-wrap gap-2">@forelse ($project->technologies as $technology)<x-site.technology-badge :technology="$technology" />@empty<p class="text-sm" style="color: var(--muted)">No technologies listed.</p>@endforelse</div>
                @if ($project->completion_date)<p class="mt-8 font-mono text-xs uppercase tracking-wider" style="color: var(--muted)">Completed {{ $project->completion_date->format('F Y') }}</p>@endif
            </aside>
            <div class="prose-copy whitespace-pre-line">{{ $project->full_description }}</div>
        </div>

        @if ($project->images->isNotEmpty())
            <section class="section-shell pt-0" aria-labelledby="screenshots-heading">
                <h2 id="screenshots-heading" class="section-title mb-8">Project screenshots</h2>
                <div class="grid gap-6 md:grid-cols-2">@foreach ($project->images as $image)<img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt_text ?: $project->title.' screenshot' }}" loading="lazy" width="1200" height="800" class="rounded-[20px] border" style="border-color: var(--line)">@endforeach</div>
            </section>
        @endif
    </article>
</x-layouts.app>
