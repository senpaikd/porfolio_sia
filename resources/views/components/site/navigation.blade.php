@props(['settings'])
<header class="site-nav">
    <nav aria-label="Primary navigation" class="content-wrap flex h-[62px] items-center justify-end">
        <button id="menu-toggle" type="button" class="theme-button md:hidden" aria-expanded="false" aria-controls="primary-menu">Menu</button>
        <div id="primary-menu" class="nav-panel absolute left-0 top-[62px] hidden w-full flex-col gap-5 border-b px-6 py-6 text-sm font-medium md:static md:flex md:w-auto md:flex-row md:items-center md:gap-6 md:border-0 md:bg-transparent md:p-0 md:backdrop-blur-none">
            <a class="nav-link" href="{{ route('home') }}">Home</a>
            <a class="nav-link" href="{{ route('home') }}#about">About</a>
            <a class="nav-link" href="{{ route('projects.index') }}">Projects</a>
            <a class="nav-link" href="{{ route('certifications.index') }}">Certifications</a>
            <a class="nav-link" href="{{ route('home') }}#experience">Experience</a>
            <a class="nav-link" href="{{ route('home') }}#contact">Contact</a>
            <button id="theme-toggle" type="button" class="theme-button" aria-label="Toggle color theme">Theme</button>
        </div>
    </nav>
</header>
