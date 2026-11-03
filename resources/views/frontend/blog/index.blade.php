<x-layouts.app>
    <x-slot:title>Blog & Career Preparation Articles - AptiMedha</x-slot:title>

    {{-- Blog Header --}}
    <div class="bg-white border-b border-gray-200/80 py-10 sm:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">
                <a href="{{ url('/') }}" class="hover:text-[#153B70] transition-colors">Home</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <span class="text-[#087F82]">Blog</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#153B70] tracking-tight">Placement Insights & Aptitude Guides</h1>
            <p class="mt-2 text-base text-[#243247]/70 max-w-2xl">
                Practical advice, exam patterns, speed tricks, and career strategies written by aptitude trainers and placement mentors.
            </p>
        </div>
    </div>

    {{-- Blog Listing --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-[#087F82]/50 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden group">
                    <div>
                        {{-- Article Image or Decorative Banner --}}
                        <div class="aspect-video bg-gradient-to-tr from-[#153B70] to-[#087F82] p-6 flex items-end justify-between relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
                            <span class="relative z-10 px-3 py-1 rounded-full text-xs font-bold bg-white text-[#153B70] shadow-xs">
                                {{ $post->category ?? 'Article' }}
                            </span>
                            <span class="relative z-10 text-xs font-semibold text-white/90">
                                {{ $post->read_time ?? '5 min' }}
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2 font-medium">
                                <span>{{ $post->date }}</span>
                                <span>&bull;</span>
                                <span>{{ $post->author }}</span>
                            </div>

                            <h2 class="text-lg font-bold text-[#243247] group-hover:text-[#153B70] transition-colors leading-snug">
                                <a href="{{ route('blog.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <p class="mt-3 text-xs sm:text-sm text-gray-500 leading-relaxed line-clamp-3">
                                {{ $post->excerpt }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 border-t border-gray-50 flex items-center justify-between mt-auto">
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="inline-flex items-center text-xs font-bold text-[#087F82] group-hover:text-[#066668] transition-colors">
                            Read Full Article &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-layouts.app>
