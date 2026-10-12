@if (session('contact_success'))<div role="status" class="mb-6 rounded-lg border border-emerald-300 bg-emerald-50 p-4 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">{{ session('contact_success') }}</div>@endif
<form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
    @csrf
    <div class="hidden" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div><label class="field-label" for="name">Name</label><input class="field-input" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" maxlength="100">@error('name')<p class="field-error">{{ $message }}</p>@enderror</div>
        <div><label class="field-label" for="email">Email</label><input class="field-input" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" maxlength="254">@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
    </div>
    <div><label class="field-label" for="subject">Subject</label><input class="field-input" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="150">@error('subject')<p class="field-error">{{ $message }}</p>@enderror</div>
    <div><label class="field-label" for="message">Message</label><textarea class="field-input min-h-40" id="message" name="message" required minlength="20" maxlength="5000">{{ old('message') }}</textarea>@error('message')<p class="field-error">{{ $message }}</p>@enderror</div>
    <button type="submit" class="button-primary">Send message</button>
</form>
