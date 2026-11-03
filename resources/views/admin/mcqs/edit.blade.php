<x-layouts.admin>
    <x-slot:title>Edit MCQ - AptiMedha Admin</x-slot:title>
    <x-slot:header>Edit Question #1</x-slot:header>

    <div class="space-y-6 max-w-4xl" x-data="{ correctOption: 'C' }">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.mcqs.index') }}" class="text-xs font-bold text-[#153B70] hover:underline flex items-center">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Questions
            </a>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Published</span>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-xs p-6 sm:p-8">
            <form action="{{ route('admin.mcqs.update', 1) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Taxonomy Hierarchy --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-6 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Category</label>
                        <select name="category" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm focus:border-[#087F82] focus:outline-none">
                            <option value="Arithmetic Aptitude" selected>Arithmetic Aptitude</option>
                            <option value="Data Interpretation">Data Interpretation</option>
                            <option value="Logical Reasoning">Logical Reasoning</option>
                            <option value="Programming">Programming</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Subject</label>
                        <input type="text" name="subject" value="Commercial Arithmetic" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Topic</label>
                        <input type="text" name="topic" value="Percentages" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm">
                    </div>
                </div>

                {{-- Question Number & Section --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Question Number</label>
                        <input type="number" name="question_number" value="1" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Section</label>
                        <select name="section" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm">
                            <option value="1" selected>Section 1 (Questions 1 - 100)</option>
                            <option value="2">Section 2 (Questions 101 - 200)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs sm:text-sm">
                            <option value="published" selected>Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>

                {{-- Question Statement --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Question Statement</label>
                    <textarea name="question" rows="3" class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">What is 25% of 240?</textarea>
                </div>

                {{-- Options --}}
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                        Options & Answer Key
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'A' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="A" x-model="correctOption" class="h-4 w-4 text-emerald-600">
                            <span class="font-bold text-xs text-gray-500">A.</span>
                            <input type="text" name="option_a" value="40" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'B' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="B" x-model="correctOption" class="h-4 w-4 text-emerald-600">
                            <span class="font-bold text-xs text-gray-500">B.</span>
                            <input type="text" name="option_b" value="50" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'C' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="C" x-model="correctOption" class="h-4 w-4 text-emerald-600">
                            <span class="font-bold text-xs text-gray-500">C.</span>
                            <input type="text" name="option_c" value="60" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-xl border" :class="correctOption === 'D' ? 'border-emerald-500 bg-emerald-50/50' : 'border-gray-200'">
                            <input type="radio" name="correct_option" value="D" x-model="correctOption" class="h-4 w-4 text-emerald-600">
                            <span class="font-bold text-xs text-gray-500">D.</span>
                            <input type="text" name="option_d" value="80" class="w-full text-xs sm:text-sm border-0 bg-transparent p-0 focus:ring-0 focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- Explanation --}}
                <div class="pt-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Explanation</label>
                    <textarea name="explanation" rows="3" class="w-full p-3.5 rounded-xl border border-gray-300 text-sm focus:border-[#087F82] focus:outline-none">25% = 25/100 = 1/4. Therefore, 25% of 240 = (1/4) × 240 = 60.</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                        Delete Question
                    </button>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.mcqs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-600 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#153B70] hover:bg-[#0e2a52] text-white text-xs font-bold shadow-xs transition-colors">
                            Update Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
