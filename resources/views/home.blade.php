@php
    $capabilities = $skills->map(fn ($item) => ['name' => $item->name, 'category' => $item->category ?: 'Skill'])
        ->concat($technologies->map(fn ($item) => ['name' => $item->name, 'category' => $item->category ?: 'Technology']))
        ->unique(fn ($item) => str($item['name'])->after('Placeholder: ')->lower()->toString())->values();
    $marqueeItems = $capabilities->pluck('name');
    if ($marqueeItems->isEmpty()) $marqueeItems = collect(['Placeholder: Add skills in the admin dashboard']);
@endphp

<x-layouts.app :settings="$settings" :seo="$seo">
    <header class="hero-shell" id="top">
        <div class="content-wrap">
            <p class="eyebrow">Portfolio / Selected work</p>
            <h1 class="display-title">Hi, I’m<br><span class="spectral-text">{{ $settings->owner_name }}.</span></h1>
            <p class="hero-role">I build as a <strong data-typewriter="{{ $settings->professional_title }}">{{ $settings->professional_title }}</strong><span class="type-caret" aria-hidden="true"></span></p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#projects" class="button-primary">View projects</a>
                <a href="#contact" class="button-secondary">Start a conversation</a>
            </div>
            <div class="mt-10 max-w-md border-l-2 pl-5 text-sm" style="border-color: var(--cyan); color: var(--muted)">
                <p class="font-mono text-[0.68rem] uppercase tracking-wider">Current availability</p>
                <p class="mt-1" style="color: var(--text)">{{ $settings->availability ?: 'Placeholder: Add your availability in the dashboard.' }}</p>
            </div>
        </div>
        <a href="#about" class="scroll-indicator" aria-label="Scroll to about section"></a>
    </header>

    <div class="marquee" aria-label="Technology highlights">
        <div class="marquee-track" aria-hidden="true">
            @foreach ([1, 2] as $copy)
                @foreach ($marqueeItems as $item)<span>{{ $item }}</span>@endforeach
            @endforeach
        </div>
    </div>

    <section id="about" class="section-shell reveal">
        <x-site.section-heading eyebrow="About" title="Thoughtful software, clearly presented." />
        <p class="prose-copy max-w-2xl whitespace-pre-line">{{ $settings->biography ?: 'Placeholder: Add your biography in the admin dashboard.' }}</p>

        <div class="mt-11 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($capabilities->take(8) as $capability)
                <article class="glass-card" data-tilt-card>
                    <span class="skill-mark" aria-hidden="true">{{ mb_strtoupper(mb_substr($capability['name'], 0, 2)) }}</span>
                    <h3 class="mt-8 text-lg font-semibold">{{ $capability['name'] }}</h3>
                    <p class="mt-1 text-sm" style="color: var(--muted)">{{ $capability['category'] }}</p>
                </article>
            @empty
                <div class="sm:col-span-2 lg:col-span-4"><x-site.empty-state title="Skills coming soon" message="Add skills and technologies from the admin dashboard." /></div>
            @endforelse
        </div>
    </section>

    <section id="projects" class="section-shell reveal">
        <x-site.section-heading eyebrow="Selected work" title="Featured projects" description="A focused selection of published work. Move your pointer across a card to explore its depth." />
        @if ($featuredProjects->isNotEmpty())
            <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">@foreach ($featuredProjects as $project)<x-site.project-card :project="$project" />@endforeach</div>
        @else
            <x-site.empty-state title="No featured projects are published yet." message="Publish and feature a project from the admin dashboard to show it here." />
        @endif
        <a href="{{ route('projects.index') }}" class="mt-8 inline-flex font-semibold" style="color: var(--cyan)">Browse the complete project gallery <span aria-hidden="true" class="ml-2">→</span></a>
    </section>

    <section id="experience" class="section-shell reveal">
        <x-site.section-heading eyebrow="Background" title="Experience and education" />
        <div class="grid gap-12 lg:grid-cols-2">
            <div>
                <h3 class="mb-6 text-lg font-semibold">Experience</h3>
                @forelse ($experiences as $item)
                    <x-site.experience-item :experience="$item" />
                @empty
                    <p style="color: var(--muted)">No published experience records yet.</p>
                @endforelse
            </div>
            <div>
                <h3 class="mb-6 text-lg font-semibold">Education</h3>
                @forelse ($education as $item)
                    <article class="timeline-item"><h4 class="font-semibold">{{ $item->qualification }}</h4><p style="color: var(--muted)">{{ $item->institution }}</p><p class="mt-2 text-sm leading-6">{{ $item->description }}</p></article>
                @empty
                    <p style="color: var(--muted)">No published education records yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section id="contact" class="section-shell reveal">
        <div class="grid gap-12 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
            <div>
                <x-site.section-heading eyebrow="Contact" title="Have a project in mind?" description="Use the form or contact me through the links below." />
                @if ($settings->email)<a class="font-mono text-sm" style="color: var(--cyan)" href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>@endif
            </div>
            <div class="contact-card"><x-site.contact-form /></div>
        </div>
    </section>
</x-layouts.app>
