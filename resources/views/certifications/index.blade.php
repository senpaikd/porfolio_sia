<x-layouts.app :settings="$settings" :seo="$seo">
    <section class="section-shell pt-36">
        <p class="eyebrow">Credentials</p>
        <h1 class="display-title mt-3">Professional <span class="spectral-text">certifications.</span></h1>
        <p class="lead-copy">Verified learning achievements and ongoing professional development.</p>

        @if ($certifications->isNotEmpty())
            <div class="mt-14 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($certifications as $certification)
                    <x-site.certification-card :certification="$certification" />
                @endforeach
            </div>
            <div class="mt-12">{{ $certifications->onEachSide(1)->links() }}</div>
        @else
            <div class="mt-14"><x-site.empty-state title="No certifications are published yet." message="Published certificates will appear here automatically." /></div>
        @endif
    </section>
</x-layouts.app>
