@props(['action' => null])

@php
    $siteKey = config('services.turnstile.site_key');
@endphp

<div class="turnstile-wrapper my-3">
    @if (!empty($siteKey))
        <div class="cf-turnstile min-h-[65px] flex items-center"
             data-sitekey="{{ $siteKey }}"
             @if($action) data-action="{{ $action }}" @endif
             data-theme="light"></div>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @else
        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span>Cloudflare Turnstile CAPTCHA (configure <code class="font-mono font-bold">TURNSTILE_SITE_KEY</code>).</span>
        </div>
    @endif

    @error('cf-turnstile-response')
        <p class="text-xs text-rose-500 mt-1.5 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span>{{ $message }}</span>
        </p>
    @enderror
</div>

