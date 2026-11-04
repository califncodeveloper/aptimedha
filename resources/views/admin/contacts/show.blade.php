<x-layouts.admin>
    <x-slot:title>Contact Message #{{ $contact->id }} - AptiMedha Admin</x-slot:title>
    <x-slot:header>Message from {{ $contact->name }}</x-slot:header>

    <div class="space-y-6 max-w-3xl">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.contacts.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to All Messages
            </a>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.contacts.mark-read', $contact->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                        Mark as {{ $contact->is_read ? 'Unread' : 'Read' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact->id) }}" onsubmit="return confirm('Permanently delete this contact message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Message Card --}}
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-5 border-b border-gray-100 text-xs">
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">Sender Name</span>
                    <p class="text-base font-bold text-[#153B70]">{{ $contact->name }}</p>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">Email Address</span>
                    <a href="mailto:{{ $contact->email }}" class="text-sm font-semibold text-[#087F82] hover:underline">
                        {{ $contact->email }}
                    </a>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">Received At</span>
                    <p class="text-gray-700 font-medium">{{ $contact->created_at->format('M d, Y h:i A') }} ({{ $contact->created_at->diffForHumans() }})</p>
                </div>
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1">IP Address</span>
                    <p class="font-mono text-gray-500">{{ $contact->ip_address ?: 'Unknown' }}</p>
                </div>
            </div>

            <div>
                <span class="font-bold text-gray-400 uppercase tracking-wider block text-xs mb-1">Subject</span>
                <h3 class="text-lg font-bold text-[#243247]">{{ $contact->subject }}</h3>
            </div>

            <div>
                <span class="font-bold text-gray-400 uppercase tracking-wider block text-xs mb-1">Message Body</span>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-sm text-[#243247] leading-relaxed whitespace-pre-line">
                    {{ $contact->message }}
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject) }}"
                   class="inline-flex items-center px-4 py-2 bg-[#087F82] text-white text-xs font-bold rounded-xl hover:bg-[#066668] transition-colors shadow-xs">
                    <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                    Reply via Email
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>

