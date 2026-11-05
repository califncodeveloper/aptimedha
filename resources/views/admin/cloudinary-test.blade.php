<x-layouts.admin>
    <x-slot:title>Cloudinary Storage Test</x-slot:title>
    <x-slot:header>Cloudinary Core Service Verification</x-slot:header>

    <div class="space-y-6 max-w-5xl">
        {{-- Notice / Safety Banner --}}
        <div class="p-4 bg-blue-50/70 border border-blue-200/80 rounded-xl text-xs sm:text-sm text-[#153B70] flex items-start space-x-3">
            <i data-lucide="info" class="w-5 h-5 flex-shrink-0 text-[#087F82] mt-0.5"></i>
            <div>
                <p class="font-semibold text-[#153B70]">Core Service Integration & Test Harness</p>
                <p class="text-gray-600 mt-0.5">
                    This admin utility tests the official Cloudinary PHP SDK integration, HTTPS delivery, folder structure compliance, and asset deletion. Existing local storage remains untouched. No dummy records are written to production tables.
                </p>
            </div>
        </div>

        {{-- Configuration Status Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-4">
                <div>
                    <h2 class="text-lg font-bold text-[#153B70]">Configuration Status</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Live configuration from config/services.php</p>
                </div>
                <div class="flex items-center space-x-2">
                    @if($isConfigured && $pingSuccess)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                            Connected & Authenticated
                        </span>
                    @elseif($isConfigured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                            Configured (Ping Failed)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                            Not Configured
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="p-4 bg-[#F7F9FC] rounded-xl border border-gray-200/80">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Cloud Name</p>
                    <p class="text-base font-bold text-[#153B70] mt-1 font-mono">{{ $cloudName }}</p>
                </div>
                <div class="p-4 bg-[#F7F9FC] rounded-xl border border-gray-200/80">
                    <p class="text-xs font-semibold text-gray-500 uppercase">API Key</p>
                    <p class="text-base font-bold text-[#153B70] mt-1 font-mono">{{ $maskedApiKey }}</p>
                </div>
                <div class="p-4 bg-[#F7F9FC] rounded-xl border border-gray-200/80">
                    <p class="text-xs font-semibold text-gray-500 uppercase">API Secret</p>
                    <p class="text-base font-bold text-gray-600 mt-1 font-mono">[REDACTED]</p>
                </div>
            </div>

            {{-- One-Click Automated Verification Sequence --}}
            <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-[#243247]">One-Click Live Verification</p>
                    <p class="text-xs text-gray-500">Executes credentials check, test upload, HTTPS verification, and asset deletion.</p>
                </div>
                <form action="{{ route('admin.cloudinary-test.verify-live') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-[#087F82] hover:bg-[#076d70] text-white text-sm font-semibold shadow-xs transition-colors">
                        <i data-lucide="play-circle" class="w-4 h-4 mr-2"></i>
                        Run Automated Verification
                    </button>
                </form>
            </div>
        </div>

        {{-- Automated Verification Results (if present) --}}
        @if(session('automated_report'))
            @php $rep = session('automated_report'); @endphp
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-xs p-6">
                <div class="flex items-center space-x-2 text-emerald-700 font-bold mb-4">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>Automated Verification Checklist Results</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-lg border {{ ($rep['credentials_loaded'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">1. Credentials Loaded</p>
                        <p class="mt-0.5">{{ ($rep['credentials_loaded'] ?? false) ? '✔ Successful' : '✖ Failed' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['api_ping'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">2. Cloudinary API Ping</p>
                        <p class="mt-0.5">{{ ($rep['api_ping'] ?? false) ? '✔ Authenticated' : '✖ Failed' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['upload_success'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">3. Test Image Upload</p>
                        <p class="mt-0.5">{{ ($rep['upload_success'] ?? false) ? '✔ Uploaded' : '✖ Failed' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['secure_url_generated'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">4. Secure HTTPS URL</p>
                        <p class="mt-0.5">{{ ($rep['secure_url_generated'] ?? false) ? '✔ Generated (HTTPS)' : '✖ Missing' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['public_id_returned'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">5. Public ID Returned</p>
                        <p class="mt-0.5">{{ ($rep['public_id_returned'] ?? false) ? '✔ Returned' : '✖ Missing' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['https_accessible'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
                        <p class="font-semibold">6. HTTPS Delivery Check</p>
                        <p class="mt-0.5">{{ ($rep['https_accessible'] ?? false) ? '✔ Reachable' : '⚠ Direct head check bypassed/pending' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border {{ ($rep['deletion_success'] ?? false) ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-rose-50 border-rose-200 text-rose-800' }}">
                        <p class="font-semibold">7. Test Asset Deletion</p>
                        <p class="mt-0.5">{{ ($rep['deletion_success'] ?? false) ? '✔ Cleaned Up (Result: ' . ($rep['deletion_result'] ?? 'ok') . ')' : '✖ Failed' }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Manual Upload Test Result (if present) --}}
        @if(session('upload_result'))
            @php $res = session('upload_result'); @endphp
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
                <h3 class="text-base font-bold text-[#153B70] mb-4">Latest Test Upload Result</h3>
                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    <div class="w-32 h-32 rounded-xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center flex-shrink-0">
                        @if(!empty($res['secure_url']))
                            <img src="{{ $res['secure_url'] }}" alt="Test preview" class="w-full h-full object-cover">
                        @else
                            <i data-lucide="image" class="w-8 h-8 text-gray-400"></i>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2 text-xs sm:text-sm">
                        <div>
                            <span class="font-semibold text-gray-500">Public ID:</span>
                            <span class="font-mono text-[#153B70] font-bold break-all">{{ $res['public_id'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-500">Secure URL:</span>
                            <a href="{{ $res['secure_url'] ?? '#' }}" target="_blank" class="font-mono text-[#087F82] hover:underline break-all inline-flex items-center">
                                {{ $res['secure_url'] ?? 'N/A' }}
                                <i data-lucide="external-link" class="w-3.5 h-3.5 ml-1 inline"></i>
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-4 text-xs text-gray-600 pt-1">
                            <span>Format: <strong>{{ $res['format'] ?? 'N/A' }}</strong></span>
                            <span>Bytes: <strong>{{ number_format($res['bytes'] ?? 0) }}</strong></span>
                            <span>Dimensions: <strong>{{ ($res['width'] ?? 0) . 'x' . ($res['height'] ?? 0) }}</strong></span>
                        </div>

                        {{-- Quick Delete Action --}}
                        <div class="pt-2">
                            <form action="{{ route('admin.cloudinary-test.delete') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this test asset from Cloudinary?');">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="public_id" value="{{ $res['public_id'] }}">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1.5"></i>
                                    Delete Test Asset Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Manual Upload Test Form --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
            <h3 class="text-base font-bold text-[#153B70] mb-1">Manual Test Upload</h3>
            <p class="text-xs text-gray-500 mb-6">Select an image to test Cloudinary upload, folder organization, and HTTPS link generation.</p>

            <form action="{{ route('admin.cloudinary-test.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label for="image" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Image File (Max 2MB, jpeg/png/webp)
                    </label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" required
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#153B70] file:text-white hover:file:bg-[#102d55] file:cursor-pointer border border-gray-200 rounded-xl p-2 bg-[#F7F9FC]">
                    @error('image')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="folder" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Target Folder
                    </label>
                    <select name="folder" id="folder" class="block w-full rounded-xl border-gray-200 bg-white text-sm focus:border-[#087F82] focus:ring-[#087F82] p-2.5">
                        @foreach($folders as $folder)
                            <option value="{{ $folder }}">{{ $folder }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#102d55] text-white text-sm font-semibold shadow-xs transition-colors">
                        <i data-lucide="upload-cloud" class="w-4 h-4 mr-2"></i>
                        Upload to Cloudinary
                    </button>
                </div>
            </form>
        </div>

        {{-- Manual Asset Deletion Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
            <h3 class="text-base font-bold text-[#153B70] mb-1">Manual Asset Deletion</h3>
            <p class="text-xs text-gray-500 mb-4">Delete any asset from Cloudinary by its public ID.</p>

            <form action="{{ route('admin.cloudinary-test.delete') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                @method('DELETE')
                <div class="flex-1">
                    <input type="text" name="public_id" placeholder="e.g. aptimedha/mcq/questions/xyz123" required
                           class="w-full rounded-xl border-gray-200 text-sm focus:border-[#087F82] focus:ring-[#087F82] p-2.5">
                    @error('public_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-black text-white text-sm font-semibold transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Delete Asset
                </button>
            </form>
        </div>
    </div>
</x-layouts.admin>

