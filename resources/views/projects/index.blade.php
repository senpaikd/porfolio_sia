<x-layouts.app :settings="$settings" :seo="$seo">
    <section class="section-shell pt-36">
        <p class="eyebrow">Portfolio archive</p>
        <h1 class="display-title mt-3">Published <span class="spectral-text">projects.</span></h1>
        <p class="lead-copy">A complete gallery of published projects, ordered for relevance.</p>

        @if ($projects->isNotEmpty())
            <div class="mt-14 grid gap-5 md:grid-cols-2 xl:grid-cols-3">@foreach ($projects as $project)<x-site.project-card :project="$project" />@endforeach</div>
            <div class="mt-12">{{ $projects->onEachSide(1)->links() }}</div>
        @else
            <div class="mt-14"><x-site.empty-state title="No projects are published yet." message="Published projects will appear here automatically." /></div>
        @endif
    </section>
</x-layouts.app>
